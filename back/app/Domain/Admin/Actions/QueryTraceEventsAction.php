<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Users\Repositories\TraceEventRepositoryInterface;
use Carbon\Carbon;

class QueryTraceEventsAction
{
    public function __construct(
        private readonly TraceEventRepositoryInterface $traceEventRepository,
    ) {
    }

    public function execute(
        ?string $eventType = null,
        ?string $source = null,
        ?string $campaign = null,
        ?string $medium = null,
        ?string $term = null,
        ?string $content = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
    ): array {
        $dateRange = $this->determineDateRange($dateFrom, $dateTo);
        $dateFromCarbon = $dateRange['from'];
        $dateToCarbon = $dateRange['to'];

        $filters = [];
        if ($eventType) {
            $filters['eventType'] = $eventType;
        }
        if ($source) {
            $filters['source'] = $source;
        }
        if ($campaign) {
            $filters['campaign'] = $campaign;
        }
        if ($medium) {
            $filters['medium'] = $medium;
        }
        if ($term) {
            $filters['term'] = $term;
        }
        if ($content) {
            $filters['content'] = $content;
        }

        $events = $this->traceEventRepository->getEventsGroupedByDate(
            $dateFromCarbon,
            $dateToCarbon,
            $filters
        );

        $availableEventTypes = $this->traceEventRepository->getAvailableEventTypes();

        $appliedFilters = [];
        if ($eventType) {
            $appliedFilters['eventType'] = $eventType;
        }
        if ($source) {
            $appliedFilters['source'] = $source;
        }
        if ($campaign) {
            $appliedFilters['campaign'] = $campaign;
        }
        if ($medium) {
            $appliedFilters['medium'] = $medium;
        }
        if ($term) {
            $appliedFilters['term'] = $term;
        }
        if ($content) {
            $appliedFilters['content'] = $content;
        }
        if ($dateFrom) {
            $appliedFilters['dateFrom'] = $dateFrom;
        }
        if ($dateTo) {
            $appliedFilters['dateTo'] = $dateTo;
        }

        return [
            'events' => $events->toArray(),
            'availableEventTypes' => $availableEventTypes,
            'appliedFilters' => $appliedFilters,
        ];
    }

    private function determineDateRange(?string $dateFrom, ?string $dateTo): array
    {
        $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
        $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : null;

        if ($from && $to) {
            return ['from' => $from, 'to' => $to];
        } elseif ($from) {
            return ['from' => $from, 'to' => $from->clone()->addDays(7)->endOfDay()];
        } elseif ($to) {
            return ['from' => $to->clone()->subDays(7)->startOfDay(), 'to' => $to];
        }

        return [
            'from' => Carbon::now()->subDays(7)->startOfDay(),
            'to' => Carbon::now()->endOfDay(),
        ];
    }
}

