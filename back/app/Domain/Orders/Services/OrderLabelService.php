<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;

class OrderLabelService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function getPrintLabelUrl(int $orderId, Authenticatable $user): array
    {
        $userId = $user->getAuthIdentifier();
        $order = $this->orderRepository->findByIdAndUserId($orderId, $userId);

        if (!$order) {
            return [
                'status' => false,
                'error' => 'Order not found',
            ];
        }

        return [
            'status' => true,
            'order_id' => $orderId,
            'order' => $order,
        ];
    }

    public function getPrintLabelUrlPublic(int $orderId): array
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            return [
                'status' => false,
                'error' => 'Order not found',
            ];
        }

        return [
            'status' => true,
            'order_id' => $orderId,
            'order' => $order,
        ];
    }
}

