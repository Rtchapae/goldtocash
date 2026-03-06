<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Actions\QueryTraceEventsAction;
use App\Domain\Admin\Actions\QueryTraceConversionsAction;
use App\Domain\Admin\Actions\QueryCampaignStatsAction;
use App\Domain\Admin\Actions\GetTraceFilterOptionsAction;
use App\Domain\Admin\Requests\QueryTraceEventsRequest;
use App\Domain\Admin\Requests\QueryTraceConversionsRequest;
use App\Domain\Admin\Requests\QueryCampaignStatsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class TraceEventController extends Controller
{
    public function __construct(
        private readonly QueryTraceEventsAction $queryTraceEventsAction,
        private readonly QueryTraceConversionsAction $queryTraceConversionsAction,
        private readonly QueryCampaignStatsAction $queryCampaignStatsAction,
        private readonly GetTraceFilterOptionsAction $getTraceFilterOptionsAction,
    ) {
    }

    public function query(QueryTraceEventsRequest $request): JsonResponse
    {
        $result = $this->queryTraceEventsAction->execute(
            eventType: $request->eventType(),
            source: $request->source(),
            campaign: $request->campaign(),
            medium: $request->medium(),
            term: $request->term(),
            content: $request->content(),
            dateFrom: $request->dateFrom(),
            dateTo: $request->dateTo(),
        );

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    }

    public function queryConversions(QueryTraceConversionsRequest $request): JsonResponse
    {
        $result = $this->queryTraceConversionsAction->execute(
            source: $request->source(),
            campaign: $request->campaign(),
            medium: $request->medium(),
            term: $request->term(),
            content: $request->content(),
            dateFrom: $request->dateFrom(),
            dateTo: $request->dateTo(),
        );

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    }

    public function queryCampaignStats(QueryCampaignStatsRequest $request): JsonResponse
    {
        $result = $this->queryCampaignStatsAction->execute($request);

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    }

    public function getFilterOptions(): JsonResponse
    {
        $options = $this->getTraceFilterOptionsAction->execute();

        return response()->json([
            'status' => true,
            'data' => $options,
        ]);
    }
}

