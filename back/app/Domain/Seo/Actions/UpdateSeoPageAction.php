<?php

namespace App\Domain\Seo\Actions;

use App\Domain\Seo\Repositories\SeoPageRepositoryInterface;
use App\Domain\Seo\Models\SeoPage;
use Illuminate\Validation\ValidationException;

class UpdateSeoPageAction
{
    public function __construct(
        private readonly SeoPageRepositoryInterface $seoPageRepository,
    ) {
    }

    public function execute(int $id, array $data): SeoPage
    {
        $seoPage = $this->seoPageRepository->findById($id);

        if (!$seoPage) {
            throw ValidationException::withMessages([
                'id' => ['SEO page not found'],
            ]);
        }

        $existingRoute = $this->seoPageRepository->findByRouteName($data['route_name']);
        if ($existingRoute && $existingRoute->id !== $id) {
            throw ValidationException::withMessages([
                'route_name' => ['Route name already exists'],
            ]);
        }

        $existingUrl = $this->seoPageRepository->findByUrl($data['page_url']);
        if ($existingUrl && $existingUrl->id !== $id) {
            throw ValidationException::withMessages([
                'page_url' => ['Page URL already exists'],
            ]);
        }

        $oldRouteName = $seoPage->route_name;
        $oldPageUrl = $seoPage->page_url;

        $updatedSeoPage = $this->seoPageRepository->update($seoPage, $data);

        $getAction = app(GetSeoPageAction::class);
        $getAction->clearCache($oldRouteName, $oldPageUrl);
        $getAction->clearCache($updatedSeoPage->route_name, $updatedSeoPage->page_url);

        return $updatedSeoPage;
    }
}
