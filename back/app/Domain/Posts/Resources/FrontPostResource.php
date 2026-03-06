<?php

namespace App\Domain\Posts\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontPostResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Post $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => mb_strimwidth(strip_tags($this->body), 0, 100, '...'),
            'image' => $this->image ? '/storage/blog/images/' . $this->image : '/images/gold-on-scale.png',
            'date' => $this->created_at->format('m/d/Y'),
            'path_prefix' => $this->path_prefix,
        ];
    }
}

