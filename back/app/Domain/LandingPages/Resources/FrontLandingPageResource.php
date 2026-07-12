<?php

namespace App\Domain\LandingPages\Resources;

use App\Domain\LandingPages\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontLandingPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var LandingPage $page */
        $page = $this->resource;

        return [
            'id' => $page->id,
            'title' => $page->title,
            'path' => $page->path,
            'blocks' => $page->blocks,
            'seo_title' => $page->seo_title,
            'seo_description' => $page->seo_description,
        ];
    }
}
