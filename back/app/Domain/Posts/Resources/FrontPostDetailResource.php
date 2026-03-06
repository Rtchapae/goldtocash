<?php

namespace App\Domain\Posts\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontPostDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Post $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'image' => $this->image ? '/storage/blog/images/' . $this->image : null,
            'date' => $this->created_at->format('m/d/Y'),
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'path_prefix' => $this->path_prefix,
        ];
    }
}

