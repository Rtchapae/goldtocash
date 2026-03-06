<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\Trace;
use App\Domain\Users\DTOs\TraceSourceDto;
use App\Domain\Orders\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class EloquentTraceRepository implements TraceRepositoryInterface
{
    public function getQueryBuilder(): Builder
    {
        return Trace::query();
    }

    public function getFilterOptions(?int $orderStatus = null): array
    {
        $query = Trace::query()
            ->select('traces.source')
            ->join('users', 'users.id', '=', 'traces.user_id')
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->whereNotNull('traces.source')
            ->distinct();

        if ($orderStatus !== null) {
            $query->where('orders.status', $orderStatus);
        }

        $traces = $query->get();

        $sources = [];
        $utmCampaigns = [];
        $utmMediums = [];

        foreach ($traces as $trace) {
            if (empty($trace->source)) {
                continue;
            }

            $sourceData = is_array($trace->source) ? $trace->source : [];

            if (empty($sourceData)) {
                continue;
            }

            if (isset($sourceData['utm_source'])) {
                $sources[$sourceData['utm_source']] = true;
            }

            if (isset($sourceData['utm_campaign'])) {
                $utmCampaigns[$sourceData['utm_campaign']] = true;
            }

            if (isset($sourceData['utm_medium'])) {
                $utmMediums[$sourceData['utm_medium']] = true;
            }
        }

        return [
            'sources' => array_keys($sources),
            'utm_campaigns' => array_keys($utmCampaigns),
            'utm_mediums' => array_keys($utmMediums),
        ];
    }

    public function countBetween(Carbon $after, Carbon $before, TraceSourceDto $traceSource): int
    {
        try {
            $query = Trace::query()
                ->where('created_at', '>=', $after)
                ->where('created_at', '<=', $before);

            $traceSourceData = $traceSource->getHashMapRepresentation();

            if (count($traceSourceData)) {
                foreach ($traceSourceData as $key => $val) {
                    $query->whereRaw("JSON_EXTRACT(`source`, '$.$key') = ?", [$val]);
                }
            }

            return $query->count();
        } catch (\Throwable $t) {
            return 0;
        }
    }

    public function getSourceCampaignsBetween(Carbon $from, Carbon $to, string $source): array
    {
        try {
            return Trace::query()
                ->whereRaw("JSON_EXTRACT(`source`, '$.utm_source') = ?", [$source])
                ->where('created_at', '>=', $from)
                ->where('created_at', '<=', $to)
                ->whereRaw("JSON_EXTRACT(`source`, '$.utm_campaign') IS NOT NULL")
                ->selectRaw("JSON_EXTRACT(`source`, '$.utm_campaign') as campaign")
                ->groupBy('campaign')
                ->pluck('campaign')
                ->toArray();
        } catch (\Throwable $t) {
            return [];
        }
    }
}

