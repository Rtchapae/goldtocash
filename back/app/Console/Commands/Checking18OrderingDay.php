<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Checking18OrderingDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:checking_18_ordering_day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Checking 18 Ordering Day";

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

        $current_time = Carbon::now();

        foreach($orders as $order) {

            if ( $order->updated_at->addDays(18) < $current_time 
                 && $order->updated_at->addDays(18)->addMinutes(1) >= $current_time ) {

                    $user = $order->user;
                    Mail::send('emails.checking_18_ordering_day', compact('user', 'order'), function ($message) use ($order, $user) {
                        $message->to($user->email)
                        ->subject("Your order #{$order->id} is still open for payout.");
                    });

            }
    
        }

        //return Command::SUCCESS;
    }
}