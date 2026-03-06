<?php

namespace App\Console\Commands;

use App\Domain\Users\Models\User;
use App\Jobs\SendNewMessageEmailJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class TestMailDispatchCommand extends Command
{
    protected $signature = 'mail:test-dispatch
                            {email? : Email to send to (user from DB, or any address with --sync)}
                            {--sync : Run in sync mode; allows sending to any email even if not in DB}';

/**
 * Dispatch a test email job and process it (queue or sync) to verify mail sending.
 *
 * Examples:
 *   php artisan mail:test-dispatch
 *   php artisan mail:test-dispatch user@example.com
 *   php artisan mail:test-dispatch user@example.com --sync
 *
 * From Tinker (dispatch only, then run "php artisan queue:work --once"):
 *   App\Jobs\SendNewMessageEmailJob::dispatch(App\Domain\Users\Models\User::first());
 */

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = $email
            ? User::where('email', $email)->first()
            : User::query()->whereNotNull('email')->where('email', '!=', '')->first();

        if (!$user) {
            if ($this->option('sync') && $email) {
                // Allow sending to any email in sync mode
            } else {
                $this->error($email
                    ? "User with email \"{$email}\" not found. Use --sync to send to this address anyway."
                    : 'No user with email found in database.');
                $this->line('Usage: php artisan mail:test-dispatch [email] [--sync]');
                return self::FAILURE;
            }
        }

        if ($this->option('sync')) {
            $recipient = $user ?? (object) [
                'email' => $email,
                'first_name' => 'Test',
                'name' => 'Test User',
            ];
            $this->info('Sending test email synchronously to: ' . $recipient->email);
            try {
                Mail::send('emails.new_message', [
                    'user' => $recipient,
                ], function ($message) use ($recipient) {
                    $userName = $recipient->first_name ?? $recipient->name ?? 'User';
                    $message->to($recipient->email)
                        ->subject($userName . ', New Message From Gold To Cash! (Test)');
                });
                $this->info('Mail sent successfully. Driver: ' . config('mail.default'));
            } catch (\Throwable $e) {
                $this->error('Mail failed: ' . $e->getMessage());
                $this->line($e->getTraceAsString());
                return self::FAILURE;
            }
            return self::SUCCESS;
        }

        $this->info('Dispatching SendNewMessageEmailJob for user: ' . $user->email);
        SendNewMessageEmailJob::dispatch($user);

        $this->info('Running queue worker for 1 job...');
        $exitCode = Artisan::call('queue:work', [
            '--once' => true,
            '--queue' => 'default',
        ], $this->getOutput());

        $this->line('Driver: ' . config('mail.default') . ' | Logs: storage/logs/laravel.log');
        $this->line('To see errors in console run: php artisan mail:test-dispatch ' . $user->email . ' --sync');
        return $exitCode === 0 ? self::SUCCESS : self::FAILURE;
    }
}
