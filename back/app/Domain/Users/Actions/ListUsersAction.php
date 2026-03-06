<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersAction
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function execute(
        int $perPage = 15,
        ?string $search = null,
        ?int $excludeUserId = null,
    ): LengthAwarePaginator {
        return $this->users->paginateForAdmin($perPage, $search, $excludeUserId);
    }
}


