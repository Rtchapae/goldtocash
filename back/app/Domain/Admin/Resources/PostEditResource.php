<?php

namespace App\Domain\Admin\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class PostEditResource extends JsonResource
{
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
            'image' => $this->image ? asset('storage/blog/images/' . $this->image) : null,
        ];
    }
}

