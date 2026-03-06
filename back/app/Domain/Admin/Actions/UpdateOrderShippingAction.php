<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;

class UpdateOrderShippingAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function execute(int $orderId, float $shipping): Order
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $this->orderRepository->updateShipping($order, $shipping);

        return $order->fresh();
    }
}

