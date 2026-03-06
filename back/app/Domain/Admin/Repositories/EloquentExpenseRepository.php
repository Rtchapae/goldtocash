<?php

namespace App\Domain\Admin\Repositories;

use App\Domain\Users\Models\User;
use App\Domain\Orders\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentExpenseRepository implements ExpenseRepositoryInterface
{
    public function getExpenses(
        int $perPage,
        int $page,
        int $excludeUserId,
        ?string $orderBy = null,
        string $orderDir = 'desc'
    ): LengthAwarePaginator {
        $query = User::query()
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.name',
                'users.email',
                'orders.id as order_id',
                'orders.status as order_status',
                'orders.amount as order_amount',
                'orders.shipping as order_shipping',
                'orders.created_at as order_created_at',
                'orders.updated_at as order_updated_at',
            )
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->where('users.id', '!=', $excludeUserId);

        if ($orderBy) {
            $query->orderBy($orderBy, $orderDir);
        } else {
            $query->orderBy('order_created_at', 'desc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getExpensesForExport(int $excludeUserId, callable $callback): void
    {
        User::query()
            ->select(
                'users.last_name',
                'users.first_name',
                'orders.id as order_id',
                'orders.status as order_status',
                'orders.amount as order_amount',
                'orders.shipping as order_shipping',
                'orders.created_at as order_created_at',
                'orders.updated_at as order_updated_at',
            )
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->where('users.id', '!=', $excludeUserId)
            ->orderBy('order_created_at', 'desc')
            ->chunk(500, $callback);
    }
}

