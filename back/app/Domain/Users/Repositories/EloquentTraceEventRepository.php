<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\TraceEvent;
use App\Domain\Users\DTOs\TraceSourceDto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EloquentTraceEventRepository implements TraceEventRepositoryInterface
{
    public function getEventsGroupedByDate(
        Carbon $dateFrom,
        Carbon $dateTo,
        array $filters = []
    ): Collection {
        $query = TraceEvent::query()
            ->selectRaw('trace_events.name as name, DATE_FORMAT(trace_events.created_at, "%Y-%m-%d") as formatted_date, count(*) as cnt')
            ->where('trace_events.created_at', '>=', $dateFrom)
            ->where('trace_events.created_at', '<=', $dateTo)
            ->groupBy('formatted_date', 'trace_events.name');

        if (!empty($filters['eventType'])) {
            $query->where('trace_events.name', $filters['eventType']);
        }

        if (!empty($filters['source']) || !empty($filters['campaign']) || !empty($filters['medium']) || !empty($filters['term']) || !empty($filters['content'])) {
            $query->join('traces', 'traces.hash', '=', 'trace_events.trace_hash');

            if (!empty($filters['source'])) {
                $query->whereRaw("JSON_EXTRACT(`traces`.`source`, '$.utm_source') = ?", [$filters['source']]);
            }

            if (!empty($filters['campaign'])) {
                $query->whereRaw("JSON_EXTRACT(`traces`.`source`, '$.utm_campaign') = ?", [$filters['campaign']]);
            }

            if (!empty($filters['medium'])) {
                $query->whereRaw("JSON_EXTRACT(`traces`.`source`, '$.utm_medium') = ?", [$filters['medium']]);
            }

            if (!empty($filters['term'])) {
                $query->whereRaw("JSON_EXTRACT(`traces`.`source`, '$.utm_term') = ?", [$filters['term']]);
            }

            if (!empty($filters['content'])) {
                $query->whereRaw("JSON_EXTRACT(`traces`.`source`, '$.utm_content') = ?", [$filters['content']]);
            }
        }

        return $query->get();
    }

    public function getAvailableEventTypes(): array
    {
        return TraceEvent::query()
            ->select('name')
            ->distinct()
            ->whereNotNull('name')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    public function countByTypeBetween(string $type, Carbon $after, Carbon $before, TraceSourceDto $traceSource): int
    {
        try {
            $query = TraceEvent::query()
                ->where('name', $type)
                ->where('trace_events.created_at', '>=', $after)
                ->where('trace_events.created_at', '<=', $before);

            $traceSourceData = $traceSource->getHashMapRepresentation();

            if (count($traceSourceData)) {
                $query->join('traces', 'traces.hash', '=', 'trace_events.trace_hash');

                foreach ($traceSourceData as $key => $val) {
                    $query->whereRaw("JSON_EXTRACT(`traces`.`source`, '$.$key') = ?", [$val]);
                }
            }

            return $query->count();
        } catch (\Throwable $t) {
            return 0;
        }
    }
}

