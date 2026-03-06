<?php

namespace App\Console\Commands;

use App\Domain\CustomerIo\CustomerIoService;
use App\Domain\CustomerIo\Models\CustomerIoLead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FlushStaleCustomerDotIoLeads extends Command
{
    protected $signature = 'flush:stale-customerio-leads {--date=}';
    protected $description = 'Will delete from customer.io any leads that have had no transmissions after a cutoff point.';

    private Carbon $cutoff;

    private int $flushCount = 0;

    public function __construct(
        private readonly CustomerIoService $cio
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->cio->serviceIsEnabled()) {
            Log::info('cannot flush stale leads because service is disabled', [
                'signature' => self::class,
                'serviceSlug' => CustomerIoService::SERVICE_SLUG,
            ]);
            return Command::SUCCESS;
        }

        $start = microtime(true);
        $this->initCutoff();

        do {
            $loadedLeads = CustomerIoLead::query()
                ->where('last_transmission_at', '<=', $this->cutoff)
                ->limit(1000)
                ->get();

            if ($loadedLeads->isEmpty()) {
                break;
            }

            foreach ($loadedLeads as $lead) {
                $this->cio->delete($lead->email);
                $this->flushCount++;
            }
        } while (true);

        Log::info('flushed stale customerio leads', [
            'signature' => self::class,
            'durationInSeconds' => (float) number_format(microtime(true) - $start, 1),
            'flushCount' => $this->flushCount,
        ]);

        return Command::SUCCESS;
    }

    private function initCutoff(): void
    {
        if ($this->option('date')) {
            $this->cutoff = Carbon::parse($this->option('date'));
            $confirmed = $this->confirm('Are you sure you want to override the default cutoff date with: ' . $this->cutoff->format('Y-m-d H:i:s'));
            if (! $confirmed) {
                $this->cutoff = now()->subMonths(6);
            }
            return;
        }

        $this->cutoff = now()->subMonths(6);
    }
}
