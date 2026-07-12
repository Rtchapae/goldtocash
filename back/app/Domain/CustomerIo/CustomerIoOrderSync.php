<?php

namespace App\Domain\CustomerIo;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Models\Order;
use App\Domain\Sms\Services\SmsUtils;
use App\Domain\Users\Models\TraceEvent;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Log;

class CustomerIoOrderSync
{
    public function __construct(
        private readonly CustomerIoService $cio,
    ) {
    }

    public function syncKitRequest(User $user, Order $order): void
    {
        $this->sync($user, $order, OrderStatus::KIT_REQUESTED, TraceEvent::EVENT_KIT_REQUEST);
    }

    public function syncStatusChange(User $user, Order $order, OrderStatus $status): void
    {
        $this->sync($user, $order, $status, $status->value());
    }

    private function sync(User $user, Order $order, OrderStatus $status, string $eventName): void
    {
        if (! $this->cio->serviceIsEnabled()) {
            return;
        }

        $email = $user->email;
        if (! $email) {
            return;
        }

        try {
            $phoneDigits = SmsUtils::digits10($user->phone ?? '');

            $this->cio->identify($email, [
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'phone' => $phoneDigits !== '' ? '+1' . $phoneDigits : ($user->phone ?? ''),
                'address' => $user->address ?? '',
                'city' => $user->city ?? '',
                'state' => $user->state ?? '',
                'zip' => $user->zip ?? '',
                'created_at' => $user->created_at?->getTimestamp() ?? now()->getTimestamp(),
                'order_id' => $order->id,
                'order_status' => $status->value(),
                'order_amount' => $order->amount,
                'order_created_at' => $order->created_at?->getTimestamp() ?? now()->getTimestamp(),
            ]);

            $this->cio->trackEvent($email, $eventName);

            Log::info('Customer.io order sync ok', [
                'email' => $email,
                'order_id' => $order->id,
                'event' => $eventName,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Customer.io sync failed', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'event' => $eventName,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
