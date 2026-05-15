<?php

namespace App\Domain\Admin\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class PostEditResource extends JsonResource
{
    private function adminImageUrl(): string
    {
        /** @var Post $this */
        $base = asset('storage/blog/images/'.$this->image);
        $v = (string) ($this->updated_at?->getTimestamp() ?? $this->id);
        $sep = str_contains($base, '?') ? '&' : '?';

        return $base.$sep.'v='.$v;
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

