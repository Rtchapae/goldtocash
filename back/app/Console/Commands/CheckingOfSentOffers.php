<?php

namespace App\Console\Commands;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\History;
use App\Domain\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckingOfSentOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:check_sent_offer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Checking of sent offers every 1 minutes. If the order is found, count the time 72 hours, after which we change the status to accepted.";

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
        $orders = Order::where('status', 4)->get();

        foreach ($orders as $order) {
            $user = User::find($order->user_id);
            if (! $user) {
                continue;
            }

            if ($order->updated_at->addDays(3) < Carbon::now()) {
                $order->update(['status' => 5]);

                Mail::send('emails.new_offer_for_user_accepted', compact('user', 'order'), function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject($user->first_name . ", Offer Accepted!");
                });

                History::create([
                    'model_type' => Order::class,
                    'model_id' => $order->id,
                    'user_id' => $user->id,
                    'user_type' => User::class,
                    'message' => 'Auto Updating Order after 72 hours',
                    'meta' => [
                        'key' => 'status',
                        'old' => 4,
                        'new' => 5,
                    ],
                    'performed_at' => Carbon::now(),
                ]);
            } elseif ($order->updated_at->addDays(2) < Carbon::now()
                && $order->updated_at->copy()->addDays(2)->addMinutes(1) >= Carbon::now()) {
                Mail::send('emails.new_offer_for_user_warning', compact('user', 'order'), function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject($user->first_name . ", We are still waiting for your response!");
                });
            }
        }

        return Command::SUCCESS;
    }
}
