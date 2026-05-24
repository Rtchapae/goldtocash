<?php

namespace App\Console\Commands;

use App\Domain\Seo\Data\DefaultSeoPages;
use App\Domain\Seo\Models\SeoPage;
use Illuminate\Console\Command;

class SyncDefaultSeoPagesCommand extends Command
{
    protected $signature = 'seo:sync-default-pages';

    protected $description = 'Create or update default SEO page records for all static front routes';

    public function handle(): int
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach (DefaultSeoPages::all() as $pageData) {
            $existing = SeoPage::where('route_name', $pageData['route_name'])
                ->orWhere('page_url', $pageData['page_url'])
                ->first();

            if ($existing) {
                $existing->update($pageData);
                $updated++;
            } else {
                try {
                    SeoPage::create($pageData);
                    $created++;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    $this->warn("Skipped {$pageData['route_name']} ({$pageData['page_url']}): duplicate constraint");
                    $skipped++;
                }
            }
        }

        $this->info("SEO pages synced: {$created} created, {$updated} updated, {$skipped} skipped.");

        return self::SUCCESS;
    }
}
