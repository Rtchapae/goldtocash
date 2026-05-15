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
        string $orderDir = 'desc',
        ?string $search = null,
    ): LengthAwarePaginator;

    public function getExpensesForExport(int $excludeUserId, callable $callback, ?string $search = null): void;
}

