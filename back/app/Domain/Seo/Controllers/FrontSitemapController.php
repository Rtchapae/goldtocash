<?php

namespace App\Domain\Seo\Controllers;

use App\Domain\Posts\Repositories\FrontPostRepositoryInterface;
use App\Domain\LandingPages\Repositories\LandingPageRepositoryInterface;
use Illuminate\Http\JsonResponse;

class FrontSitemapController
{
    public function __construct(
        private readonly FrontPostRepositoryInterface $postRepository,
        private readonly LandingPageRepositoryInterface $landingPageRepository,
    ) {
    }

    public function getUrls(): JsonResponse
    {
        return response()->json([
            'base_url' => rtrim(config('app.url'), '/'),
            'gold_info' => $this->postRepository->getPublicSlugsForSitemap(null),
            'sell' => $this->postRepository->getPublicSlugsForSitemap('/sell'),
            'sell_gold' => $this->postRepository->getPublicSlugsForSitemap('/sell-gold'),
            'landing_pages' => $this->landingPageRepository->getActivePaths(),
        ]);
    }
}
