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

class SendNewMessageEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
    ) {
    }

    public function handle(): void
    {
        try {
            $userName = $this->user->first_name ?? $this->user->name ?? 'User';

            Mail::send('emails.new_message', [
                'user' => $this->user,
            ], function ($message) use ($userName) {
                $message->to($this->user->email)
                    ->subject($userName . ", New Message From Gold To Cash!");
            });

        } catch (\Exception $e) {
            Log::error('SendNewMessageEmailJob failed', [
                'user_id' => $this->user->id,
                'to' => $this->user->email ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

