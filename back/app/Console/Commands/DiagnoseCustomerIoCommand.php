<?php

namespace App\Console\Commands;

use App\Domain\CustomerIo\CustomerIoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseCustomerIoCommand extends Command
{
    protected $signature = 'diagnose:customerio {--test-email=}';
    protected $description = 'Diagnose Customer.io integration: config, recent leads, optional test event';

    public function handle(CustomerIoService $cio): int
    {
        $this->info('Customer.io enabled: ' . ($cio->serviceIsEnabled() ? 'yes' : 'no'));
        $this->line('site_id length: ' . strlen((string) config('services.customer_dot_io.site_id')));
        $this->line('api_key length: ' . strlen((string) config('services.customer_dot_io.api_key')));

        $leads = DB::table('customerio_leads')
            ->orderByDesc('last_transmission_at')
            ->limit(10)
            ->get(['email', 'last_transmission_at']);

        $this->info('Recent CIO transmissions (' . $leads->count() . '):');
        foreach ($leads as $lead) {
            $this->line("  {$lead->email} @ {$lead->last_transmission_at}");
        }

        $orders = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.created_at', '>=', now()->subDays(3))
            ->orderByDesc('orders.id')
            ->limit(10)
            ->get(['orders.id', 'orders.created_at', 'users.email']);

        $this->info('Recent orders (3 days):');
        foreach ($orders as $order) {
            $this->line("  #{$order->id} {$order->email} @ {$order->created_at}");
        }

        $missing = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->leftJoin('customerio_leads', 'customerio_leads.email', '=', 'users.email')
            ->where('orders.created_at', '>=', now()->subDays(3))
            ->where(function ($q) {
                $q->whereNull('customerio_leads.email')
                    ->orWhereRaw('customerio_leads.last_transmission_at < orders.created_at');
            })
            ->orderByDesc('orders.id')
            ->limit(10)
            ->get(['orders.id', 'orders.created_at', 'users.email', 'customerio_leads.last_transmission_at']);

        $this->info('Orders missing CIO sync (3 days):');
        foreach ($missing as $row) {
            $this->line("  #{$row->id} {$row->email} order@{$row->created_at} cio@".($row->last_transmission_at ?? 'never'));
        }

        $testEmail = $this->option('test-email');
        if ($testEmail) {
            $this->info("Sending test identify + kit-request to {$testEmail}...");
            $ok1 = $cio->identify($testEmail, ['first_name' => 'Diag', 'last_name' => 'Test']);
            $ok2 = $cio->trackEvent($testEmail, 'kit-request');
            $this->line('identify: ' . ($ok1 ? 'ok' : 'failed'));
            $this->line('trackEvent: ' . ($ok2 ? 'ok' : 'failed'));
        }

        return Command::SUCCESS;
    }
}
