<?php

namespace App\Console\Commands;

use App\Domain\CustomerIo\CustomerIoOrderSync;
use App\Domain\Orders\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReplayCustomerIoKitEventsCommand extends Command
{
    protected $signature = 'cio:replay-kit-events {--since=2026-06-10} {--force} {--dry-run}';
    protected $description = 'Replay kit-request Customer.io events for orders that never synced (or all with --force)';

    public function handle(CustomerIoOrderSync $sync): int
    {
        $since = $this->option('since');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $query = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->leftJoin('customerio_leads', 'customerio_leads.email', '=', 'users.email')
            ->where('orders.created_at', '>=', $since);

        if (! $force) {
            $query->where(function ($q) {
                $q->whereNull('customerio_leads.email')
                    ->orWhereRaw('customerio_leads.last_transmission_at < orders.created_at');
            });
        }

        $rows = $query
            ->orderBy('orders.id')
            ->get([
                'orders.id as order_id',
                'orders.user_id',
                'users.email',
                'orders.created_at',
            ]);

        if ($rows->isEmpty()) {
            $this->info('No orders need CIO replay.');
            return Command::SUCCESS;
        }

        $this->info(($dryRun ? '[dry-run] ' : '') . 'Replaying kit-request for ' . $rows->count() . ' order(s)...');

        foreach ($rows as $row) {
            $this->line("  #{$row->order_id} {$row->email} ({$row->created_at})");

            if ($dryRun) {
                continue;
            }

            $order = Order::with('user')->find($row->order_id);
            if (! $order?->user) {
                $this->warn("    skipped: missing user");
                continue;
            }

            $sync->syncKitRequest($order->user, $order);
        }

        return Command::SUCCESS;
    }
}
