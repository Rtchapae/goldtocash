<?php

namespace App\Jobs;

use App\Domain\Orders\Models\Order;
use App\Domain\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendKitRequestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Order $order,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send('emails.new_order', [
                'user' => $this->user,
                'order' => $this->order,
            ], function ($message) {
                $message->to($this->user->email)
                    ->subject('Your Free Appraisal Kit Request');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send kit request email: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'order_id' => $this->order->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}



