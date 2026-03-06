<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Orders\Services\OrderStatusUpdateService;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Domain\Orders\Models\Order;
use Illuminate\Validation\ValidationException;

class UpdateOrderAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly OrderStatusUpdateService $orderStatusUpdateService,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function execute(int $orderId, array $data): Order
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            throw ValidationException::withMessages([
                'order' => ['Order not found'],
            ]);
        }

        if (array_key_exists('amount', $data)) {
            $order->amount = $data['amount'] !== '' ? $data['amount'] : null;
        }

        if (array_key_exists('status', $data)) {
            $statusValue = (int) $data['status'];
            $statusEnum = OrderStatus::from($statusValue);
            $this->orderStatusUpdateService->updateOrderStatus($order, $statusEnum, 'Admin manual update');
        }

        $user = $order->user;
        if ($user) {
            $userData = array_filter([
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip' => $data['zip'] ?? null,
            ], static fn ($value) => $value !== null);

            if (!empty($userData)) {
                $this->userRepository->update($user, $userData);
            }
        }

        $order->save();

        return $order->fresh();
    }
}

