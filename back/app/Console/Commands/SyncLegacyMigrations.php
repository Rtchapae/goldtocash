<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncLegacyMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:sync-legacy 
                            {--dry-run : Show what would be inserted without actually inserting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark new migrations as executed since tables already exist from old project';

    /**
     * List of new migrations that should be marked as executed
     * Format: ['migration_name', batch_number]
     */
    private array $newMigrations = [
        // Batch 1 - Core Laravel tables
        ['0001_01_01_000000_create_users_table', 1],
        ['0001_01_01_000001_create_cache_table', 1],
        ['0001_01_01_000002_create_jobs_table', 1],
        ['2025_01_01_000000_create_legacy_tables', 1], // No-op migration
        
        // Batch 1 - Legacy tables that were created early
        ['2025_01_01_000100_create_statuses_table', 1],
        ['2025_01_01_000110_create_customerio_leads_table', 1],
        ['2025_01_01_000120_create_services_table', 1],
        ['2025_01_01_000130_create_model_histories_table', 1],
        ['2025_01_01_000140_create_threads_table', 1],
        ['2025_01_01_000150_create_messages_table', 1],
        ['2025_01_01_000160_create_participants_table', 1],
        ['2025_01_01_000170_create_files_table', 1],
        ['2025_01_01_000180_create_orders_table', 1],
        ['2025_01_01_000190_create_mail_in_reminder_sms_table', 1],
        ['2025_01_01_000200_create_nick_tmp_table', 1],
        ['2025_01_01_000210_create_password_resets_personal_access_tokens_posts_replies_tables', 1],
        ['2025_01_01_000220_create_sent_messages_tables', 1],
        ['2025_01_01_000230_create_sms_tables', 1],
        ['2025_01_01_000240_create_traces_tables', 1],
        ['2025_01_01_000250_create_trustpilot_table', 1],
        ['2025_01_01_000260_create_user_notes_and_verification_codes_tables', 1],
        ['2025_01_20_000000_create_model_histories_table', 1], // Duplicate, but keep for compatibility
        ['2025_12_01_000001_add_is_admin_to_users_table', 1],
        
        // Batch 2 - New migrations that need to be run
        ['2026_01_02_060151_create_roles_table', 2],
        ['2026_01_02_060153_create_branches_table', 2],
        ['2026_01_02_060154_add_role_id_and_branch_id_to_users_table', 2],
        ['2026_01_02_060941_add_order_type_and_branch_id_to_orders_table', 2],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $inserted = 0;
        $skipped = 0;
        $errors = 0;

        // Get the highest batch number
        $maxBatch = DB::table('migrations')->max('batch') ?? 0;

        foreach ($this->newMigrations as [$migration, $batch]) {
            try {
                // Check if migration already exists
                $exists = DB::table('migrations')
                    ->where('migration', $migration)
                    ->exists();

                if ($exists) {
                    $this->line("Skipped: {$migration} (already exists)");
                    $skipped++;
                    continue;
                }

                // Use provided batch or increment from max
                $finalBatch = $batch ?? ($maxBatch + 1);

                if (!$dryRun) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $finalBatch,
                    ]);
                }

                $this->info("✓ {$migration} -> batch {$finalBatch}");
                $inserted++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to insert {$migration}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Inserted: {$inserted}");
        $this->line("  Skipped: {$skipped}");
        $this->line("  Errors: {$errors}");

        if ($dryRun) {
            $this->newLine();
            $this->info('Run without --dry-run to actually insert the records');
        } else {
            $this->newLine();
            $this->info('New migrations have been marked as executed.');
            $this->info('You can now run: php artisan migrate');
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
