<?php

namespace App\Console\Commands;

use App\Domain\Orders\Models\Order;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Enums\UserRole;
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
        
        // Get admins using enum
        $adminRole = Role::where('name', UserRole::ADMIN->value)->first();
        if ($adminRole) {
            $admins = User::where('role_id', $adminRole->id)->get();
        } else {
            // Fallback to legacy role field
            $admins = User::whereNotNull('role')->where('role', 1)->get();
        }

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
