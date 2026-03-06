<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Actions\GetCountersAction;
use App\Domain\Admin\Actions\GetDashboardAnalyticsAction;
use App\Domain\Admin\Requests\CountersRequest;
use App\Domain\Admin\Requests\GetDashboardAnalyticsRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CountersController extends Controller
{
    public function __construct(
        private readonly GetCountersAction $getCountersAction,
        private readonly GetDashboardAnalyticsAction $getDashboardAnalyticsAction,
    ) {
    }

    public function index(CountersRequest $request): JsonResponse
    {
        $counters = $this->getCountersAction->execute(
            $request->period(),
            $request->from(),
            $request->to()
        );

        return response()->json([
            'status' => true,
            'counters' => $counters,
        ]);
    }

    public function analytics(GetDashboardAnalyticsRequest $request): JsonResponse
    {
        $analytics = $this->getDashboardAnalyticsAction->execute(
            $request->period(),
            $request->from(),
            $request->to()
        );

        return response()->json([
            'status' => true,
            'data' => $analytics,
        ]);
    }
}

