<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Models\History;
use App\Domain\Orders\Models\Order;
use App\Domain\Users\Models\User;
use Carbon\Carbon;

class OrderHistoryService
{
    public function createStatusChangeHistory(
        Order $order,
        int $oldStatus,
        int $newStatus,
        string $message,
        array $additionalMeta = []
    ): History {
        $meta = array_merge([
            [
                'key' => 'status',
                'old' => $oldStatus,
                'new' => $newStatus,
            ],
        ], $additionalMeta);

        return History::create([
            'model_type' => Order::class,
            'model_id' => $order->id,
            'user_id' => $order->user_id,
            'user_type' => User::class,
            'message' => $message,
            'meta' => json_encode($meta),
            'performed_at' => Carbon::now(),
        ]);
    }

    public function createAutomatedStatusHistory(
        Order $order,
        int $oldStatus,
        int $newStatus,
        string $reason,
        array $additionalMeta = []
    ): History {
        return $this->createStatusChangeHistory(
            $order,
            $oldStatus,
            $newStatus,
            "Automated status update: {$reason}",
            $additionalMeta
        );
    }
}


