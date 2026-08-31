<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Admin list/counter period filters in app timezone (America/Los_Angeles).
 * Avoids MySQL YEAR()/MONTH() on TIMESTAMP (UTC session) pulling prior-evening
 * local dates into "current month" (e.g. Jul 31 LA shown under August filter).
 */
final class AdminPeriodQuery
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $query
     */
    public static function apply($query, string $period, ?string $from, ?string $to, string $column = 'orders.created_at'): void
    {
        if ($period === 'all' || $period === '') {
            return;
        }

        $tz = (string) config('app.timezone', 'America/Los_Angeles');
        $now = Carbon::now($tz);

        match ($period) {
            'today' => self::whereBetweenInclusive(
                $query,
                $column,
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ),
            'yesterday' => self::whereBetweenInclusive(
                $query,
                $column,
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
            ),
            'currentmonth' => self::whereBetweenInclusive(
                $query,
                $column,
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ),
            'lastmonth' => self::whereBetweenInclusive(
                $query,
                $column,
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ),
            'custom' => self::applyCustom($query, $column, $from, $to, $tz),
            default => null,
        };
    }

    private static function applyCustom($query, string $column, ?string $from, ?string $to, string $tz): void
    {
        if ($from) {
            $fromDate = Carbon::parse($from, $tz)->startOfDay();
            $query->where($column, '>=', self::toDb($fromDate));
        }
        if ($to) {
            $toDate = Carbon::parse($to, $tz)->endOfDay();
            $query->where($column, '<=', self::toDb($toDate));
        }
    }

    private static function whereBetweenInclusive($query, string $column, Carbon $from, Carbon $to): void
    {
        $query->where($column, '>=', self::toDb($from))
            ->where($column, '<=', self::toDb($to));
    }

    /**
     * Bind wall-clock in app TZ. DB TIMESTAMP is read/written as the same
     * naive strings the admin UI already displays (see OrderResource).
     */
    private static function toDb(Carbon $date): string
    {
        return $date->copy()->timezone((string) config('app.timezone', 'America/Los_Angeles'))
            ->format('Y-m-d H:i:s');
    }
}
