<?php

namespace App\Domain\Admin\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class PostEditResource extends JsonResource
{
    /**
     * Relative storage URL (same shape as FrontPostResource). Do not use asset(): ApiMiddleware
     * sets app.url from the request Host, so behind Vite proxy asset() can point at :5173 and break images.
     */
    private function adminImageUrl(): string
    {
        /** @var Post $this */
        $v = (string) ($this->updated_at?->getTimestamp() ?? $this->id);

        return '/storage/blog/images/'.$this->image.'?v='.$v;
    }

    public function toArray($request): array
    {
        /** @var Post $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'path_prefix' => $this->path_prefix,
            'active' => $this->active,
            'image' => $this->image ? $this->adminImageUrl() : null,
        ];
    }
}

