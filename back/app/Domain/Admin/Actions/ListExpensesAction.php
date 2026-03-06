<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Repositories\ExpenseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListExpensesAction
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
    ) {
    }

    public function execute(
        int $perPage,
        int $page,
        ?string $orderBy = null,
        string $orderDir = 'desc',
    ): LengthAwarePaginator {
        $adminUser = Auth::guard('admin')->user();
        
        if (!$adminUser) {
            abort(401, 'Unauthorized');
        }

        return $this->expenseRepository->getExpenses(
            perPage: $perPage,
            page: $page,
            excludeUserId: $adminUser->id,
            orderBy: $orderBy,
            orderDir: $orderDir
        );
    }
}

