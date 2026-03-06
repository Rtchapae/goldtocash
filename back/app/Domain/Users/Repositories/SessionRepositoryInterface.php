<?php

namespace App\Domain\Users\Repositories;

use Illuminate\Database\Eloquent\Builder;

interface SessionRepositoryInterface
{
    public function getQueryBuilder(): Builder;

    public function countUniqueVisitors(Builder $query): int;

    public function sumVisitors(Builder $query): int;

    public function getVisitorsByMonth(): array;
}

