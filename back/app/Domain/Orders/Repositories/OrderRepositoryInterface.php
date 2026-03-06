<?php

namespace App\Domain\Orders\Repositories;

use App\Domain\Users\Models\User;
use App\Domain\Orders\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function findByIdAndUserId(int $orderId, int $userId): ?Order;

    public function findById(int $orderId): ?Order;

    public function getUserOrders(int $userId): array;

    public function getSubmissionUrl(Order $order): ?string;

    public function sendKitRequestEmail(User $user, Order $order): void;

    public function getOrdersForStatusUpdate(int $afterId = 0, int $limit = 30): Collection;

    public function getOrdersByStatus(int $status): Collection;

    public function updateStatus(Order $order, int $newStatus): bool;

    public function getTrackingNumber(Order $order): ?string;

    public function getQueryBuilder(): Builder;

    public function userHasOtherOrders(int $userId, int $excludeOrderId): bool;

    public function updateShipping(Order $order, float $shipping): bool;

    public function getOrdersByMonth(?string $orderType = null, ?int $branchId = null): array;

    public function getPaidAmountByMonth(?string $orderType = null, ?int $branchId = null): array;
}

