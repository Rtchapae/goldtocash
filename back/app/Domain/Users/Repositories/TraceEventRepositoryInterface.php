<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\DTOs\TraceSourceDto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface TraceEventRepositoryInterface
{
    public function getEventsGroupedByDate(
        Carbon $dateFrom,
        Carbon $dateTo,
        array $filters = []
    ): Collection;

    public function getAvailableEventTypes(): array;

    public function countByTypeBetween(string $type, Carbon $after, Carbon $before, TraceSourceDto $traceSource): int;
}

