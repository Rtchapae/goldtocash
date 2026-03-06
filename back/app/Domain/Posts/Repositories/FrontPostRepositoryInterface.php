<?php

namespace App\Domain\Posts\Repositories;

use App\Domain\Posts\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FrontPostRepositoryInterface
{
    public function getPublicPosts(?string $pathPrefix, int $page = 1, int $perPage = 15): LengthAwarePaginator;
    public function getPublicSlugsForSitemap(?string $pathPrefix): array;
    public function findBySlug(string $slug, ?string $pathPrefix = null): ?Post;
}

