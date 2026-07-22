<?php

namespace App\Domain\LandingPages\Repositories;

use App\Domain\LandingPages\Models\LandingPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentLandingPageRepository implements LandingPageRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = LandingPage::query();

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term);
            $query->where(function ($q) use ($escaped) {
                $q->where('title', 'like', '%'.$escaped.'%')
                    ->orWhere('path', 'like', '%'.$escaped.'%');
            });
        }

        $orderBy = $filters['order-by'] ?? 'created_at';
        $orderDir = $filters['order-dir'] ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findById(int $id): ?LandingPage
    {
        return LandingPage::query()->find($id);
    }

    public function findByPath(string $path): ?LandingPage
    {
        return LandingPage::query()->where('path', $path)->first();
    }

    public function findActiveByPath(string $path): ?LandingPage
    {
        return LandingPage::query()
            ->where('path', $path)
            ->where('active', true)
            ->first();
    }

    public function create(array $data): LandingPage
    {
        return LandingPage::query()->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $page = $this->findById($id);
        if (! $page) {
            return false;
        }

        return $page->update($data);
    }

    public function delete(int $id): bool
    {
        $page = $this->findById($id);
        if (! $page) {
            return false;
        }

        return (bool) $page->delete();
    }

    public function getActivePaths(): array
    {
        return LandingPage::query()
            ->where('active', true)
            ->orderBy('path')
            ->pluck('path')
            ->all();
    }
}
