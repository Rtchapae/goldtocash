<?php

namespace App\Domain\Reviews\Services;

use App\Domain\Reviews\Repositories\TrustpilotRepository;
use Illuminate\Database\Eloquent\Collection;

class TrustpilotService
{
    public function __construct(
        private TrustpilotRepository $trustpilotRepository
    ) {}

    public function getPublishedReviews(): Collection
    {
        return $this->trustpilotRepository->getPublishedReviews();
    }

    public function getFormattedReviewsForFrontend(): array
    {
        return $this->trustpilotRepository
            ->getPublishedReviews()
            ->map(function ($review) {
                return [
                    'name' => $review->author_name,
                    'date' => $review->date_published->format('M j, Y'),
                    'title' => $review->title,
                    'text' => $review->review,
                ];
            })
            ->toArray();
    }
}
