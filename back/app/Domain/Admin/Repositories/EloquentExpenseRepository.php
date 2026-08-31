<?php

namespace App\Domain\Admin\Repositories;

use App\Domain\Users\Models\User;
use App\Support\AdminUserSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentExpenseRepository implements ExpenseRepositoryInterface
{
    public function getExpenses(
        int $perPage,
        int $page,
        int $excludeUserId,
        ?string $orderBy = null,
        string $orderDir = 'desc',
        ?string $search = null,
    ): LengthAwarePaginator {
        $query = $this->baseExpensesQuery($excludeUserId, $search);

        if ($orderBy) {
            $query->orderBy($orderBy, $orderDir);
        } else {
            $query->orderBy('order_created_at', 'desc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getExpensesForExport(int $excludeUserId, callable $callback, ?string $search = null): void
    {
        $this->baseExpensesQuery($excludeUserId, $search, forExport: true)
            ->orderBy('order_created_at', 'desc')
            ->chunk(500, $callback);
    }

    private function baseExpensesQuery(int $excludeUserId, ?string $search, bool $forExport = false): Builder
    {
        $select = $forExport
            ? [
                'users.last_name',
                'users.first_name',
                'orders.id as order_id',
                'orders.status as order_status',
                'orders.amount as order_amount',
                'orders.shipping as order_shipping',
                'orders.created_at as order_created_at',
                'orders.updated_at as order_updated_at',
            ]
            : [
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.name',
                'users.email',
                'users.phone',
                'orders.id as order_id',
                'orders.status as order_status',
                'orders.amount as order_amount',
                'orders.shipping as order_shipping',
                'orders.created_at as order_created_at',
                'orders.updated_at as order_updated_at',
            ];

        $query = User::query()
            ->select($select)
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->where('users.id', '!=', $excludeUserId);

        AdminUserSearch::apply($query, $search);

        return $query;
    }
}

