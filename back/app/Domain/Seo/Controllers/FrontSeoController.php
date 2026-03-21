<?php

namespace App\Domain\Seo\Controllers;

use App\Domain\Seo\Actions\GetSeoPageAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FrontSeoController
{
    public function __construct(
        private readonly GetSeoPageAction $getSeoPageAction,
    ) {
    }

    public function getSeoData(Request $request): JsonResponse
    {
        $routeName = $request->query('route_name');
        $pageUrl = $request->query('page_url', '/');

        $seoPage = null;

        if ($routeName) {
            $seoPage = $this->getSeoPageAction->execute(routeName: $routeName);
        }

        if (!$seoPage && $pageUrl) {
            $seoPage = $this->getSeoPageAction->execute(pageUrl: $pageUrl);
        }

        if ($seoPage) {
            return response()->json([
                'title' => $seoPage->meta_title ?: $seoPage->page_title,
                'description' => $seoPage->meta_description,
                'keywords' => $seoPage->meta_keywords,
            ]);
        }

        return response()->json(null);
    }
}
