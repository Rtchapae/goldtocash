<?php

namespace App\Domain\Reviews\Controllers;

use App\Domain\Reviews\Services\TrustpilotService;
use Illuminate\Http\JsonResponse;

class TrustpilotController
{
    public function __construct(
        private TrustpilotService $trustpilotService
    ) {}

    public function getReviews(): JsonResponse
    {
        $payload = $this->trustpilotService->getReviewsApiPayload();

        return response()->json([
            'data' => [
                'reviews' => $payload['reviews'],
                'total_count' => $payload['total_count'],
            ],
        ]);
    }
}
