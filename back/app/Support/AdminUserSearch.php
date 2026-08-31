<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

final class AdminUserSearch
{
    public static function apply(Builder $query, ?string $raw, string $table = 'users'): void
    {
        if ($raw === null || $raw === '') {
            return;
        }

        $pattern = LikeSearch::wrap($raw);

        $query->where(function ($q) use ($pattern, $table): void {
            $q->where("{$table}.name", 'LIKE', $pattern)
                ->orWhere("{$table}.first_name", 'LIKE', $pattern)
                ->orWhere("{$table}.last_name", 'LIKE', $pattern)
                ->orWhere("{$table}.email", 'LIKE', $pattern)
                ->orWhere("{$table}.phone", 'LIKE', $pattern);
        });
    }
}
