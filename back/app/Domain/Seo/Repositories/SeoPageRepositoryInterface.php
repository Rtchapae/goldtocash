<?php

namespace App\Domain\Seo\Repositories;

use App\Domain\Seo\Models\SeoPage;

interface SeoPageRepositoryInterface
{
    public function findById(int $id): ?SeoPage;

    public function findByRouteName(string $routeName): ?SeoPage;

    public function findByUrl(string $url): ?SeoPage;

    public function getAllActive(): \Illuminate\Database\Eloquent\Collection;

    public function getAll(): \Illuminate\Database\Eloquent\Collection;

    public function create(array $data): SeoPage;

    public function update(SeoPage $seoPage, array $data): SeoPage;

    public function delete(SeoPage $seoPage): bool;
}
