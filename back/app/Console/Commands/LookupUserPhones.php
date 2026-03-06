<?php

namespace App\Console\Commands;

use App\Domain\Sms\Models\SmsLookup;
use App\Domain\Sms\Repositories\SmsLookupRepositoryInterface;
use App\Domain\Sms\Services\PhoneLookupService;
use App\Domain\Users\Models\User;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command to lookup phone numbers for recently registered users
 *
 * REVIEW NOTES:
 * - Performs phone lookup to determine if phone is mobile
 * - Processes users created within specified time range
 * - Skips phones that already have non-assumed lookup results
 * - Stores lookup results in database for future use
 * - Used to optimize SMS sending by avoiding non-mobile numbers
 */
class LookupUserPhones extends Command
{
    protected $signature = 'lookup-user-phones {--from=} {--to=}';

    protected $description = 'Perform and store phone lookup (is_mobile) for users created within the time range';

    private Carbon $from;
    private Carbon $to;
    private array $phones = [];

    public function __construct(
        private readonly PhoneLookupService $phoneLookupService,
        private readonly SmsLookupRepositoryInterface $smsLookupRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(): int
    {
        try {
            $this->loadDateRange();
            $this->loadPhonesOfRecentUsers();
            $this->iteratePhones();

            $this->info('Phone lookup completed successfully');
            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('Failed to lookup user phones', [
                'command' => self::class,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Failed to lookup user phones: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Load and validate date range from options
     *
     * REVIEW: Parses --from and --to options.
     * If --from is not provided, defaults to 2 hours before --to.
     * Validates that from < to.
     *
     * @throws Exception
     */
    private function loadDateRange(): void
    {
        $to = $this->option('to');
        if (!$to) {
            throw new Exception("--to option is required");
        }
        $this->to = Carbon::parse($to);

        $from = $this->option('from');
        if (!$from) {
            $this->from = $this->to->clone()->subHours(2);
        } else {
            $this->from = Carbon::parse($from);
        }

        if ($this->from >= $this->to) {
            throw new Exception("cannot use 'from' that comes after 'to'");
        }

        $this->info("Date range: {$this->from->format('Y-m-d H:i:s')} to {$this->to->format('Y-m-d H:i:s')}");
    }

    /**
     * Load phone numbers of users created in date range
     *
     * REVIEW: Queries users created between from and to dates.
     * Extracts phone numbers, filtering out null/empty values.
     */
    private function loadPhonesOfRecentUsers(): void
    {
        Log::info('loading phones', [
            'signature' => 'Commands.LookupUserPhones',
            'from' => $this->from->format('Y-m-d H:i:s'),
            'to' => $this->to->format('Y-m-d H:i:s'),
        ]);

        // REVIEW: Direct model access is acceptable here for querying users by date range
        // as this is a command that operates on bulk data, not business logic
        $this->phones = User::query()
            ->where('created_at', '>=', $this->from)
            ->where('created_at', '<=', $this->to)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->pluck('phone')
            ->unique()
            ->values()
            ->toArray();

        $this->info("Found " . count($this->phones) . " unique phone number(s) to process");
    }

    /**
     * Iterate through phones and perform lookup
     */
    private function iteratePhones(): void
    {
        $processed = 0;
        $skipped = 0;

        foreach ($this->phones as $phone) {
            if ($this->handlePhone($phone)) {
                $processed++;
            } else {
                $skipped++;
            }
        }

        $this->info("Processed: {$processed}, Skipped: {$skipped}");
    }

    /**
     * Handle lookup for a single phone number
     *
     * REVIEW: Checks if non-assumed lookup already exists.
     * If not, performs lookup via service and stores result.
     *
     * @param string $phone
     * @return bool True if lookup was performed, false if skipped
     */
    private function handlePhone(string $phone): bool
    {
        $existingLookup = $this->smsLookupRepository->findNonAssumedByPhone($phone);

        if ($existingLookup) {
            Log::info('skipping lookup', [
                'signature' => 'Commands.LookupUserPhones',
                'phone' => $phone,
                'reason' => 'non-assumed lookup already exists',
            ]);
            return false;
        }

        $newLookupDto = $this->phoneLookupService->lookupPhone($phone);
        SmsLookup::storeDto($newLookupDto);

        Log::info('performed lookup for recent user', [
            'signature' => 'Commands.LookupUserPhones',
            'phone' => $phone,
            'is_mobile' => $newLookupDto->getIsMobile(),
            'is_assumed' => $newLookupDto->getIsAssumed(),
        ]);

        return true;
    }
}
