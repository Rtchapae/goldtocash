<?php

namespace App\Jobs;

use App\Domain\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPasswordEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $password,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send('emails.register', [
                'user' => $this->user,
                'password' => $this->password,
            ], function ($message) {
                $message->to($this->user->email)
                    ->subject('[IMPORTANT] Account information inside');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send password email: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}



