<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Users\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAdminUsersAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(
        int $perPage = 15,
        int $page = 1,
        ?string $search = null,
        ?string $role = null,
    ): LengthAwarePaginator {
        return $this->userRepository->listAdminUsers($perPage, $page, $search, $role);
    }
}

