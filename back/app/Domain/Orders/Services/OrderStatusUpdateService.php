<?php

namespace App\Domain\Orders\Services;

use App\Domain\Admin\Models\ModelHistory;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdateService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function updateOrderStatus(Order $order, OrderStatus $newStatus, string $reason = ''): bool
    {
        $oldStatus = $order->status;

        if ($oldStatus === $newStatus->value) {
            Log::debug('Order status unchanged', [
                'order_id' => $order->id,
                'status' => $oldStatus,
            ]);
            return false;
        }

        $updated = $this->orderRepository->updateStatus($order, $newStatus->value);

        if ($updated) {
            Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus->value,
                'reason' => $reason,
            ]);

            try {
                $user = Auth::user();
                ModelHistory::create([
                    'model_type' => 'App\Models\Order',
                    'model_id' => $order->id,
                    'user_id' => $user ? $user->id : null,
                    'user_type' => $user ? get_class($user) : 'system',
                    'message' => "Order status changed from {$oldStatus} to {$newStatus->value}",
                    'meta' => [
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus->value,
                        'reason' => $reason,
                    ],
                    'performed_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create order status history', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $updated;
    }

    public function shouldAutoAcceptOffer(Order $order): bool
    {
        if ($order->status !== OrderStatus::OFFER_SENT->value) {
            return false;
        }

        $threeDaysAgo = $order->updated_at->copy()->addDays(3);
        return $threeDaysAgo->isPast();
    }

    public function shouldSendWarningEmail(Order $order): bool
    {
        if ($order->status !== OrderStatus::OFFER_SENT->value) {
            return false;
        }

        $twoDaysAgo = $order->updated_at->copy()->addDays(2);
        $twoDaysAgoPlusOneMinute = $twoDaysAgo->copy()->addMinute();

        return $twoDaysAgo->isPast() && $twoDaysAgoPlusOneMinute->isFuture();
    }
}


