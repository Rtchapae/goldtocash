<?php

namespace App\Domain\LandingPages\Repositories;

use App\Domain\LandingPages\Models\LandingPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LandingPageRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;

    public function findById(int $id): ?LandingPage;

    public function findByPath(string $path): ?LandingPage;

    public function findActiveByPath(string $path): ?LandingPage;

    public function create(array $data): LandingPage;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /** @return array<int, string> */
    public function getActivePaths(): array;
}
