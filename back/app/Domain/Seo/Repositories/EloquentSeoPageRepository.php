<?php

namespace App\Domain\Seo\Repositories;

use App\Domain\Seo\Models\SeoPage;
use Illuminate\Database\Eloquent\Collection;

class EloquentSeoPageRepository implements SeoPageRepositoryInterface
{
    public function findById(int $id): ?SeoPage
    {
        return SeoPage::find($id);
    }

    public function findByRouteName(string $routeName): ?SeoPage
    {
        return SeoPage::where('route_name', $routeName)
            ->where('is_active', true)
            ->first();
    }

    public function findByUrl(string $url): ?SeoPage
    {
        return SeoPage::where('page_url', $url)
            ->where('is_active', true)
            ->first();
    }

    public function getAllActive(): Collection
    {
        return SeoPage::where('is_active', true)
            ->orderBy('page_title')
            ->get();
    }

    public function getAll(): Collection
    {
        return SeoPage::orderBy('page_title')->get();
    }

    public function create(array $data): SeoPage
    {
        return SeoPage::create($data);
    }

    public function update(SeoPage $seoPage, array $data): SeoPage
    {
        $seoPage->update($data);
        return $seoPage->fresh();
    }

    public function delete(SeoPage $seoPage): bool
    {
        return $seoPage->delete();
    }
}
