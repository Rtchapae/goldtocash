<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\Session;
use Illuminate\Database\Eloquent\Builder;

class EloquentSessionRepository implements SessionRepositoryInterface
{
    public function getQueryBuilder(): Builder
    {
        return Session::query();
    }

    public function countUniqueVisitors(Builder $query): int
    {
        return (clone $query)->count();
    }

    public function sumVisitors(Builder $query): int
    {
        return (int) ((clone $query)->sum('times') ?? 0);
    }

    public function getVisitorsByMonth(): array
    {
        return Session::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->selectRaw('SUM(times) as sums')
            ->where('created_at', '>=', now()->subMonths(13)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'sums' => (int) ($item->sums ?? 0),
                ];
            })
            ->toArray();
    }
}

