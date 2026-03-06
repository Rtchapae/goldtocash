<?php

namespace App\Domain\Orders\Repositories;

use App\Domain\Users\Models\User;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Enums\OrderStatus;
use App\Jobs\SendKitRequestEmailJob;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findByIdAndUserId(int $orderId, int $userId): ?Order
    {
        return Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public function findById(int $orderId): ?Order
    {
        return Order::with(['user', 'branch'])->find($orderId);
    }

    public function getUserOrders(int $userId): array
    {
        $orders = Order::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $baseUrl = config('app.frontend_url', config('app.url')) . '/api/v1';

        return $orders->map(function ($order) use ($baseUrl) {
            $documents = [];

            $letterPath = storage_path("app/docs/clients/{$order->user_id}/{$order->id}/letter.pdf");
            if (file_exists($letterPath)) {
                $documents[] = [
                    'id' => "order-{$order->id}-letter",
                    'title' => "Order #{$order->id} Appraisal Kit",
                    'type' => 'shipping',
                    'viewUrl' => "{$baseUrl}/front/user/orders/{$order->id}/letter",
                    'created_at' => $order->created_at->toISOString(),
                ];
            }

            return [
                'id' => $order->id,
                'created_at' => $order->created_at,
                'amount' => $order->amount,
                'status' => $order->status,
                'documents' => $documents,
            ];
        })->toArray();
    }

    public function getSubmissionUrl(Order $order): ?string
    {
        return $order->submission_url;
    }

    public function sendKitRequestEmail(User $user, Order $order): void
    {
        SendKitRequestEmailJob::dispatch($user, $order);
    }

    public function getOrdersForStatusUpdate(int $afterId = 0, int $limit = 30): Collection
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfDay();

        return Order::query()
            ->select('id', 'user_id', 'status', 'description')
            ->where('status', '<', OrderStatus::ITEMS_RECEIVED->value)
            ->where('created_at', '>=', $threeMonthsAgo)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function getOrdersByStatus(int $status): Collection
    {
        return Order::where('status', $status)
            ->with('user')
            ->get();
    }

    public function updateStatus(Order $order, int $newStatus): bool
    {
        $order->status = $newStatus;
        return $order->save();
    }

    public function getTrackingNumber(Order $order): ?string
    {
        if (empty($order->description)) {
            return null;
        }

        $description = json_decode($order->description, true);
        return $description['output']['transactionShipments'][0]['masterTrackingNumber'] ?? null;
    }

    public function getQueryBuilder(): Builder
    {
        return Order::query()
            ->select([
                'orders.id',
                'orders.user_id',
                'orders.amount',
                'orders.status',
                'orders.order_type',
                'orders.branch_id',
                'orders.created_at',
                'orders.updated_at',
                'orders.send_label',
                'orders.submission_url',
            ])
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->with(['user.traces', 'branch']);
    }

    public function userHasOtherOrders(int $userId, int $excludeOrderId): bool
    {
        return Order::query()
            ->where('user_id', $userId)
            ->where('id', '!=', $excludeOrderId)
            ->exists();
    }

    public function updateShipping(Order $order, float $shipping): bool
    {
        $order->shipping = $shipping;
        return $order->save();
    }

    public function getOrdersByMonth(?string $orderType = null, ?int $branchId = null): array
    {
        $query = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->selectRaw('COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(13)->startOfMonth());

        if ($orderType) {
            $query->where('order_type', $orderType);
        }

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return $query->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total' => (int) $item->total,
                ];
            })
            ->toArray();
    }

    public function getPaidAmountByMonth(?string $orderType = null, ?int $branchId = null): array
    {
        $query = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->selectRaw('SUM(amount) as total')
            ->where('status', OrderStatus::PAID->value)
            ->where('created_at', '>=', Carbon::now()->subMonths(13)->startOfMonth());

        if ($orderType) {
            $query->where('order_type', $orderType);
        }

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return $query->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total' => (float) ($item->total ?? 0),
                ];
            })
            ->toArray();
    }
}

