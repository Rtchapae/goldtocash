<?php

namespace App\Console\Commands;

use App\Domain\Users\Models\TraceEvent;
use App\Models\Order;
use App\Models\SentMessage;
use App\Models\SmsLookup;
use App\Models\SmsOptout;
use App\Models\Status;
use App\Models\User;
use App\Services\Sms\Providers\Twilio\Twilio;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PurgeOldTraceEvents extends Command
{
    protected $signature = 'purge:old-trace-events';
    protected $description = 'Will remove from db, trace events of a whitelisted type, older than some age.';

    private Carbon $cutoff;

    private array $eventTypesToRemove;

    public function handle(): int
    {
        $this->initConfig();

        do {
            $startOfDelete = microtime(true);

            $deletedCount = TraceEvent::query()
                ->where('created_at', '<=', $this->cutoff)
                ->whereIn('name', $this->eventTypesToRemove)
                ->delete();

            Log::info('deleted qualified trace events', [
                'signature' => 'Commands.PurgeOldTraceEvents',
                'deletedCount' => $deletedCount,
                'cutoff' => $this->cutoff->format('Y-m-d H:i:s'),
                'durationInSeconds' => (float) number_format(microtime(true) - $startOfDelete),
            ]);
        } while ($deletedCount > 0);

        return 0;
    }

    public function initConfig(): void
    {
        $this->cutoff = now()->subMonths(6);

        $this->eventTypesToRemove = [
            TraceEvent::EVENT_PAGE_LOAD,
        ];
    }
}
