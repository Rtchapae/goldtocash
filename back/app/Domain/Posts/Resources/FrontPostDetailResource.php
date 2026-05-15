<?php

namespace App\Domain\Posts\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontPostDetailResource extends JsonResource
{
    /** Same default as list cards (`FrontPostResource`) when no featured file. */
    private function publicImageUrl(): string
    {
        /** @var Post $this */
        if (! $this->image) {
            return '/images/gold-on-scale.png';
        }

        $v = (string) ($this->updated_at?->getTimestamp() ?? $this->id);

        return '/storage/blog/images/'.$this->image.'?v='.$v;
    }

    public function toArray($request): array
    {
        /** @var Post $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'image' => $this->publicImageUrl(),
            'date' => $this->created_at->format('m/d/Y'),
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'path_prefix' => $this->path_prefix,
        ];
    }
}

