<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\Trace;
use App\Domain\Orders\Enums\OrderStatus;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

use App\Domain\Users\DTOs\TraceSourceDto;
use Carbon\Carbon;

interface TraceRepositoryInterface
{
    public function getQueryBuilder(): Builder;

    public function getFilterOptions(?int $orderStatus = null): array;

    public function countBetween(Carbon $after, Carbon $before, TraceSourceDto $traceSource): int;

    public function getSourceCampaignsBetween(Carbon $from, Carbon $to, string $source): array;
}

