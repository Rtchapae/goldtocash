<?php

namespace App\Console\Commands;

use App\Models\MailInReminderSms;
use App\Models\Order;
use App\Models\SentMessage;
use App\Models\SmsLookup;
use App\Models\SmsOptout;
use App\Models\Status;
use App\Models\User;
use App\Services\Sms\Providers\Twilio\Twilio;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OrderActionReminderSmsFlow extends Command
{
    /**
     * NOTE:
     * This approach is suboptimal, but quick to write. It would be better to track, in a dedicated table,
     * which users have been reminded, at what day offsets, and for which orders. That would result in
     * more resilient functionality, should the reminder flow fail at runtime.
     */

    protected $signature = 'order:action_reminder_sms_flow';
    protected $description = 'Will look for orders with no updates, and send the user a message prompting them for action.';

    private Carbon $bootTime;

    public function handle(): int
    {
        $this->bootTime = now()->subMinute()->startOfMinute();

        foreach ([0,1,5,6,7,8,9,10] as $dayOffset) {
            $this->handleDayOffset($dayOffset);
        }

        return 0;
    }

    private function handleDayOffset(int $dayOffset): void
    {
        $start = microtime(true);

        $orders = $this->loadOrders($dayOffset);
        if (! $orders->count()) {
            return;
        }

        $users = $this->loadUserInfoFromOrders($orders);
        if (! count($users)) {
            return;
        }

        $message = $this->messageForDayOffset($dayOffset);
        if (! $message) {
            Log::info('could not get message for the day offset', [
                'signature' => 'Commands.OrderActionReminderSmsFlow',
                'dayOffset' => $dayOffset,
            ]);
            return;
        }

        $smsProvider = new Twilio();

        // Send to each user
        $users->each(function (object $user) use (&$smsProvider, &$message) {
            $message = str_replace('{name}', $user->first_name, $message);
            $dto = $smsProvider->send(env('SMS_TWILIO_FROM'), $user->phone, $message);
            SentMessage::storeDto($dto);
        });

        Log::info('finished sms order action reminder flow for day offset', [
            'signature' => 'Commands.OrderActionReminderSmsFlow',
            'durationInSeconds' => (float) number_format(microtime(true) - $start, 1),
            'dayOffset' => $dayOffset,
        ]);
    }

    /**
     * Load orders where status is "Kit Requested" and it was created in the current minute $days days ago.
     */
    private function loadOrders(int $days): Collection
    {
        $bootTimeWithDayOffset = $this->bootTime->clone()->subDays($days);

        $orders = Order::query()
            ->where('status', Status::query()->first()->getAttribute('id'))
            ->where('created_at', '>=', $bootTimeWithDayOffset)
            ->where('created_at', '<=', $bootTimeWithDayOffset->clone()->endOfMinute())
            ->get();

        Log::info('loaded orders', [
            'signature' => 'Commands.OrderActionReminderSmsFlow',
            'orderCount' => $orders->count(),
            'dayOffset' => $days,
            'time' => $bootTimeWithDayOffset,
        ]);

        return $orders;
    }

    private function loadUserInfoFromOrders(Collection &$orders): Collection
    {
        $users = User::query()
            ->whereIn('id', $orders->pluck('user_id')->toArray())
            ->selectRaw('phone, first_name')
            ->groupBy('phone', 'first_name')
            ->get();

        $initialUserCount = $users->count();

        Log::info('loaded users of each order', [
            'signature' => 'Commands.OrderActionReminderSmsFlow',
            'userCount' => count($users),
            'orderCount' => $orders->count(),
        ]);

        // Filter out phones that have opted out
        $phonesWithOptout = SmsOptout::query()
            ->select('phone')
            ->whereIn('phone', $users->pluck('phone'))
            ->get()->pluck('phone')->toArray();
        $users = $users->filter(function (object $user) use (&$phonesWithOptout) {
            $hasOptedOut = in_array($user->phone, $phonesWithOptout);
            if ($hasOptedOut) {
                Log::info('skipping lead because they have opted out', [
                    'signature' => 'Commands.OrderActionReminderSmsFlow',
                    'phone' => $user->phone,
                ]);
                return false;
            }
            return true;
        });

        // Filter out phones that have not been confirmed mobile
        $confirmedMobilePhones = SmsLookup::query()
            ->select('phone')
            ->whereIn('phone', $users->pluck('phone'))
            ->where('is_mobile', true)
            ->get()->pluck('phone')->toArray();
        $users = $users->filter(function (object $user) use (&$confirmedMobilePhones) {
            $isMobile = in_array($user->phone, $confirmedMobilePhones);
            if (! $isMobile) {
                Log::info('skipping lead because their phone is not confirmed mobile', [
                    'signature' => 'Commands.OrderActionReminderSmsFlow',
                    'phone' => $user->phone,
                ]);
                return false;
            }
            return true;
        });

        Log::info('finished filtering users', [
            'signature' => 'Commands.OrderActionReminderSmsFlow',
            'initialUserCount' => $initialUserCount,
            'userCount' => $users->count(),
        ]);

        return $users;
    }

    private function messageForDayOffset(int $dayOffset): string
    {
        $closing = function (string $phrase = 'Best Regards'): string {
            return "\n\n$phrase,\nConstantine";
        };

        // Format site phone as kabob (xxx-xxx-xxxx)
        $sitePhone = Config('fedex.parcel_options.recipient_phone');
        preg_match('/(\d{3})(\d{3})(\d{4})/', $sitePhone, $matches);
        $kabobSitePhone = $matches[1] . '-' . $matches[2] . '-' . $matches[3];

        $messages = MailInReminderSms::query()
            ->where('is_enabled', true)
            ->get()
            ->pluck('message', 'days_after_request')
            ->toArray();

        $message = $messages[$dayOffset] ?? null;
        if (! $message) {
            return '';
        }

        $message = str_replace('{site_phone}', $kabobSitePhone, $message);

        return $message;
    }
}
