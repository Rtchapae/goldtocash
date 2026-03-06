<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

class CheckingOrderingDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:checking_ordering_day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Checking Ordering Day";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = DB::select('
            SELECT
                id, user_id, created_at, updated_at, status
            FROM orders as t
            WHERE NOT EXISTS (
                SELECT * 
                FROM orders as t2
                WHERE t2.user_id = t.user_id AND t2.created_at > t.created_at
            ) AND t.status = 0
            ORDER BY user_id
        ');

        $current_time = Carbon::now()->tz(Config('app.timezone'));
        //addDays
        foreach($orders as $order) {

            $user = User::find($order->user_id);
            $site_phone = Config('fedex.parcel_options.recipient_phone');
            $site_email = Config('fedex.parcel_options.recipient_email');
            $order_date = Carbon::parse($order->created_at);

            $schedule = Carbon::parse($order->created_at);
            if ( $schedule->addDays(4) < $current_time && $schedule->addMinutes(1) >= $current_time ) {
                Mail::send('emails.checking_4_ordering_day', compact('user', 'order', 'site_phone', 'site_email'), function ($message) use ($user) {
                    $message->to($user->email)
                    ->subject("100% Satisfaction Guaranteed!");
                });

                \Log::debug('schedule 4 '.json_encode($current_time));
            }

            $schedule = Carbon::parse($order->created_at);
            if ( $schedule->addDays(6) < $current_time && $schedule->addMinutes(1) >= $current_time ) {
                Mail::send('emails.checking_6_ordering_day', compact('user', 'order', 'site_phone', 'site_email'), function ($message) use ($user) {
                    $message->to($user->email)
                    ->subject("Special Offer Just For You!");
                });

               \Log::debug('schedule 6 '.json_encode($current_time));
            }

            $schedule = Carbon::parse($order->created_at);
            if ( $schedule->addDays(10) < $current_time && $schedule->addMinutes(1) >= $current_time ) {
               Mail::send('emails.checking_10_ordering_day', compact('user', 'order', 'site_phone', 'site_email'), function ($message) use ($user) {
                   $message->to($user->email)
                   ->subject("Gold is up. Take advantage of it Today!");
               });

               \Log::debug('schedule 10 '.json_encode($current_time));
            }

            $schedule = Carbon::parse($order->created_at);
            if ( $schedule->addDays(18) < $current_time && $schedule->addMinutes(1) >= $current_time ) {
               Mail::send('emails.checking_18_ordering_day', compact('user', 'order', 'site_phone', 'site_email'), function ($message) use ($order, $user) {
                   $message->to($user->email)
                   ->subject("Your order #{$order->id} is still open for payout.");
               });

               \Log::debug('schedule 18 '.json_encode($current_time));
            }

            $schedule = Carbon::parse($order->created_at);
            if ( $schedule->addDays(25) < $current_time && $schedule->addMinutes(1) >= $current_time ) {
               Mail::send('emails.checking_25_ordering_day', compact('user', 'order', 'site_phone', 'site_email'), function ($message) use ($user) {
                   $message->to($user->email)
                   ->subject("Important information about your 10% Bonus");
               });

               \Log::debug('schedule 25 '.json_encode($current_time));
            }

            $schedule = Carbon::parse($order->created_at);
            if ( $schedule->addDays(30) < $current_time && $schedule->addMinutes(1) >= $current_time ) {
               Mail::send('emails.checking_30_ordering_day', compact('user', 'order', 'site_phone', 'site_email'), function ($message) use ($order, $user) {
                   $message->to($user->email)
                   ->subject("Important information about your order #{$order->id}");
               });

               \Log::debug('schedule 30 '.json_encode($current_time));
            }
        }

        //return Command::SUCCESS;
    }
}
