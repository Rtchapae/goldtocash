<?php

namespace App\Domain\Reviews\Repositories;

use App\Domain\Reviews\Models\Trustpilot;
use Illuminate\Database\Eloquent\Collection;

class TrustpilotRepository
{
    public function getHighRatedReviews(int $limit = 20): Collection
    {
        return Trustpilot::query()
            ->where('rating', '>=', 4)
            ->orderBy('date_published', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPublishedReviews(): Collection
    {
        return $this->getHighRatedReviews(20);
    }
}
