<?php

namespace App\Domain\Posts\Repositories;

use App\Domain\Posts\Models\Post;
use App\Domain\Admin\Enums\PeriodFilter;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPostRepository implements AdminPostRepositoryInterface, FrontPostRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Post::query();

        if (!empty($filters['period'])) {
            $this->applyPeriodFilter($query, $filters['period'], $filters['from'] ?? null, $filters['to'] ?? null);
        }

        if (!empty($filters['order-by'])) {
            $orderDir = $filters['order-dir'] ?? 'asc';
            $query->orderBy($filters['order-by'], $orderDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findById(int $id): ?Post
    {
        return Post::query()->find($id);
    }

    public function create(array $data): Post
    {
        return Post::query()->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $post = $this->findById($id);
        if (!$post) {
            return false;
        }

        return $post->update($data);
    }

    public function delete(int $id): bool
    {
        $post = $this->findById($id);
        if (!$post) {
            return false;
        }

        return $post->delete();
    }

    private function applyPeriodFilter($query, string $period, ?string $from = null, ?string $to = null): void
    {
        $periodEnum = PeriodFilter::tryFrom($period) ?? PeriodFilter::default();

        match ($periodEnum) {
            PeriodFilter::ALL => null,
            PeriodFilter::TODAY => $query->whereDate('created_at', Carbon::today()),
            PeriodFilter::YESTERDAY => $query->whereDate('created_at', Carbon::yesterday()),
            PeriodFilter::CURRENT_MONTH => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
            PeriodFilter::LAST_MONTH => $query->where('created_at', '>=', Carbon::now()->startOfMonth()->subMonth())
                ->where('created_at', '<', Carbon::now()->startOfMonth()),
            PeriodFilter::CUSTOM => $this->applyCustomPeriodFilter($query, $from, $to),
            default => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
        };
    }

    private function applyCustomPeriodFilter($query, ?string $from, ?string $to): void
    {
        if ($from) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }
    }

    public function getPublicPosts(?string $pathPrefix, int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        $query = Post::query()
            ->where('active', true)
            ->orderBy('id', 'desc');

        if ($pathPrefix === null) {
            $query->whereNull('path_prefix');
        } else {
            $query->where('path_prefix', $pathPrefix);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getPublicSlugsForSitemap(?string $pathPrefix): array
    {
        $query = Post::query()
            ->where('active', true)
            ->select('slug');

        if ($pathPrefix === null) {
            $query->whereNull('path_prefix');
        } else {
            $query->where('path_prefix', $pathPrefix);
        }

        return $query->pluck('slug')->all();
    }

    public function findBySlug(string $slug, ?string $pathPrefix = null): ?Post
    {
        $query = Post::query()
            ->where('slug', $slug)
            ->where('active', true);

        if ($pathPrefix === null) {
            $query->whereNull('path_prefix');
        } else {
            $query->where('path_prefix', $pathPrefix);
        }

        return $query->first();
    }
}

