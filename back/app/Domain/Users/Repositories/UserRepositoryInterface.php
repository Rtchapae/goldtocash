<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginateForAdmin(
        int $perPage = 15,
        ?string $search = null,
        ?int $excludeUserId = null,
    ): LengthAwarePaginator;

    public function findByEmail(string $email): ?User;

    public function findByEmailOrPhone(string $email, string $phone): ?User;

    public function create(array $data): User;

    public function sendPasswordEmail(User $user, string $password): void;

    public function update(User $user, array $data): User;

    public function getAdmins(): \Illuminate\Support\Collection;

    public function findById(int $id): ?User;

    public function findByIdWith(int $id, array $relations = []): ?User;

    public function delete(User $user): bool;

    public function listAdminUsers(
        int $perPage = 15,
        int $page = 1,
        ?string $search = null,
        ?string $role = null
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}


