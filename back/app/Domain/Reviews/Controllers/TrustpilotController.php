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
        $reviews = $this->trustpilotService->getFormattedReviewsForFrontend();

        return response()->json([
            'data' => [
                'reviews' => $reviews
            ]
        ]);
    }
}
