<?php

namespace App\Domain\Admin\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ExpenseRepositoryInterface
{
    public function getExpenses(
        int $perPage,
        int $page,
        int $excludeUserId,
        ?string $orderBy = null,
        string $orderDir = 'desc'
    ): LengthAwarePaginator;

    public function getExpensesForExport(int $excludeUserId, callable $callback): void;
}

