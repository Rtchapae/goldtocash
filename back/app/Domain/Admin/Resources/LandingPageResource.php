<?php

namespace App\Domain\Admin\Resources;

use App\Domain\LandingPages\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var LandingPage $page */
        $page = $this->resource;
        $baseUrl = rtrim(config('app.front_url') ?? config('app.url'), '/');

        return [
            'id' => $page->id,
            'title' => $page->title,
            'path' => $page->path,
            'url' => $baseUrl.$page->path,
            'blocks' => $page->blocks,
            'seo_title' => $page->seo_title,
            'seo_description' => $page->seo_description,
            'active' => $page->active,
            'created_at' => $page->created_at?->toIso8601String(),
            'updated_at' => $page->updated_at?->toIso8601String(),
        ];
    }
}
