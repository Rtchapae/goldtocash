<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class CheckingOfAcceptedOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:check_accepted_offer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Checking of accepted offers every 1 minutes.";

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
        $orders = Order::where('status', 5)->with('user')->get();
        $admins = User::whereNotNull('role')->get();

        foreach($orders as $order) {

            if ( $order->updated_at->addDays(3) < Carbon::now() 
                && $order->updated_at->addDays(3)->addMinutes(1) >= Carbon::now() ) {
                    $user = $order->user;
                    foreach($admins as $admin) {
                        Mail::send('emails.new_offer_for_admin_accepted', compact('user', 'order'), function ($message) use ($request, $user) {
                            $message->to($admin->email)
                                ->subject("Order #{$order->id} Accepted Offer");
                        });
                    }
            }
    
        }

        //return Command::SUCCESS;
    }
}