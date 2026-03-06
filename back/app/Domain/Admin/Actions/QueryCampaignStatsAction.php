<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Requests\QueryCampaignStatsRequest;
use App\Domain\Users\DTOs\TraceSourceDto;
use App\Domain\Users\Models\TraceEvent;
use App\Domain\Users\Repositories\TraceEventRepositoryInterface;
use App\Domain\Users\Repositories\TraceRepositoryInterface;
use Carbon\Carbon;

class QueryCampaignStatsAction
{
    public function __construct(
        private readonly TraceRepositoryInterface $traceRepository,
        private readonly TraceEventRepositoryInterface $traceEventRepository,
    ) {
    }

    public function execute(QueryCampaignStatsRequest $request): array
    {
        $dateFrom = Carbon::parse($request->dateFrom())->startOfDay();
        $dateTo = Carbon::parse($request->dateTo())->endOfDay();
        $source = $request->source();

        // Get campaigns for this source
        $campaigns = $this->traceRepository->getSourceCampaignsBetween($dateFrom, $dateTo, $source);

        if (empty($campaigns)) {
            return [
                'campaignStats' => [],
                'appliedFilters' => [
                    'source' => $source,
                    'dateFrom' => $dateFrom->toDateString(),
                    'dateTo' => $dateTo->toDateString(),
                ],
            ];
        }

        $campaignStats = [];

        foreach ($campaigns as $campaign) {
            $campaignSpecificTraceSource = TraceSourceDto::createFromArray([
                'source' => $source,
                'campaign' => $campaign,
            ]);

            $traceCount = $this->traceRepository->countBetween($dateFrom, $dateTo, $campaignSpecificTraceSource);
            $kitReqCount = $this->traceEventRepository->countByTypeBetween(
                TraceEvent::EVENT_KIT_REQUEST,
                $dateFrom,
                $dateTo,
                $campaignSpecificTraceSource
            );

            $campaignStats[] = [
                'campaign' => $campaign,
                'traceCount' => $traceCount,
                'kitReqCount' => $kitReqCount,
            ];
        }

        return [
            'campaignStats' => $campaignStats,
            'appliedFilters' => [
                'source' => $source,
                'dateFrom' => $dateFrom->toDateString(),
                'dateTo' => $dateTo->toDateString(),
            ],
        ];
    }
}

