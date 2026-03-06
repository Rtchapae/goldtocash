<?php

namespace App\Domain\Posts\Repositories;

use App\Domain\Posts\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminPostRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;
    public function findById(int $id): ?Post;
    public function create(array $data): Post;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

