<?php

namespace App\Domain\Seo\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SeoPageResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => SeoPageResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
            ],
        ];
    }
}
