<?php

namespace App\Domain\Admin\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Post $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'seo_description' => $this->seo_description,
            'created_at' => $this->created_at?->toDateTimeString(),
            'active' => $this->active,
            'url' => $this->getUrl(),
        ];
    }

    private function getUrl(): ?string
    {
        if (!$this->slug) {
            return null;
        }

        $baseUrl = config('app.front_url') ?? config('app.url');
        $prefix = $this->path_prefix ?: '/sell-gold';
        
        return rtrim($baseUrl, '/') . $prefix . '/' . $this->slug;
    }
}

