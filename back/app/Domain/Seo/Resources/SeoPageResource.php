<?php

namespace App\Domain\Seo\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeoPageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'route_name' => $this->route_name,
            'page_url' => $this->page_url,
            'page_title' => $this->page_title,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords ?? [],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
