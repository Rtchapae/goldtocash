<?php

namespace App\Domain\Sms\Services;

use App\Domain\Orders\Models\Order;
use App\Domain\Sms\Models\SentMessage;
use App\Domain\Sms\Models\SmsOptout;
use App\Domain\Sms\Services\Providers\TwilioProvider;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KitWelcomeSmsService
{
    public function sendIfConfigured(User $user, Order $order): void
    {
        if (! SmsUtils::isSmsConfigured()) {
            return;
        }

        $phone = SmsUtils::digits10($user->phone ?? '');
        if (! SmsUtils::isValidPhone($phone)) {
            Log::info('Kit welcome SMS skipped: invalid phone', [
                'user_id' => $user->id,
                'order_id' => $order->id,
            ]);

            return;
        }

        if (SmsOptout::query()->where('phone', $phone)->exists()) {
            Log::info('Kit welcome SMS skipped: opted out', [
                'user_id' => $user->id,
                'phone' => $phone,
            ]);

            return;
        }

        $message = $this->resolveDayZeroMessage($user);
        if ($message === '') {
            return;
        }

        try {
            $from = config('services.twilio.from') ?? env('SMS_TWILIO_FROM');
            $provider = new TwilioProvider();
            $dto = $provider->send($from, $phone, $message);

            SentMessage::storeDto($dto);

            if ($dto->getStatus() === 'failed') {
                Log::warning('Kit welcome SMS failed', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'phone' => $phone,
                    'details' => $dto->getDetails(),
                ]);
            } else {
                Log::info('Kit welcome SMS sent', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'phone' => $phone,
                    'status' => $dto->getStatus(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Kit welcome SMS exception', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveDayZeroMessage(User $user): string
    {
        $template = DB::table('mail_in_reminder_sms')
            ->where('days_after_request', 0)
            ->where('is_enabled', true)
            ->value('message');

        if (! is_string($template) || trim($template) === '') {
            $template = 'Hi {name}, thanks for requesting your free appraisal kit from Gold to Cash! Questions? Call {site_phone}.';
        }

        $sitePhone = (string) config('fedex.parcel_options.recipient_phone', '564-237-7332');
        if (preg_match('/(\d{3})(\d{3})(\d{4})/', preg_replace('/\D/', '', $sitePhone), $matches)) {
            $sitePhone = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }

        $name = trim((string) ($user->first_name ?? $user->name ?? 'there'));

        return str_replace(
            ['{name}', '{site_phone}'],
            [$name, $sitePhone],
            $template
        );
    }
}
