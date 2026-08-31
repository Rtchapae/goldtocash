<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('orders:check-sent-offers')->everyMinute();
        $schedule->command('orders:check-accepted-offers')->everyMinute();

        // redundant since implementing customer.io
        // $schedule->command('order:checking_ordering_day')->everyMinute();

        // $schedule->command('order:action_reminder_sms_flow')->everyMinute();

        // Disabled for now since TP account is not being paid for
        // $schedule->command('order:checking_trustpilot')->hourly();

        $schedule->command('purge:old-trace-events')->hourly();

        $schedule->command('orders:update-in-transit-statuses')->everyThreeHours();

        $schedule->command('flush:stale-customerio-leads')->everyFiveMinutes();

        $schedule->command('update-gold-price')->everyThreeHours();
    }

    /**
     * Expose schedule registration for bootstrap/app.php (Laravel 12 style).
     */
    public function registerSchedule(Schedule $schedule): void
    {
        $this->schedule($schedule);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
