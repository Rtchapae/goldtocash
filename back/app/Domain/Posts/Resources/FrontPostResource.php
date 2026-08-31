<?php

namespace App\Domain\Posts\Resources;

use App\Domain\Posts\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontPostResource extends JsonResource
{
    /** Cache-friendly URL bust when the file or post row changes (admin replaced image). */
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
            'excerpt' => mb_strimwidth(strip_tags($this->body), 0, 100, '...'),
            'image' => $this->publicImageUrl(),
            'date' => $this->created_at->format('m/d/Y'),
            'path_prefix' => $this->path_prefix,
        ];
    }
}

