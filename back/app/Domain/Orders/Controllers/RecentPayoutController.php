<?php

namespace App\Domain\Orders\Controllers;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;

class RecentPayoutController
{
    public function getRandomRecentPayout(): JsonResponse
    {
        $order = Order::query()
            ->whereIn('status', [OrderStatus::OFFER_ACCEPTED, OrderStatus::PAID])
            ->where('amount', '>', 200)
            ->where('created_at', '>=', now()->subDays(60))
            ->inRandomOrder()
            ->first();

        if (!$order) {
            return response()->json([], 404);
        }

        $user = $order->user;

        if (!$user) {
            return response()->json([], 404);
        }

        return response()->json([
            'data' => [
                'payout' => [
                    'firstName' => ucfirst($user->first_name),
                    'amount' => $order->amount,
                ]
            ],
        ]);
    }
}
