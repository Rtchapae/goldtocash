<?php

namespace App\Domain\Seo\Actions;

use App\Domain\Seo\Repositories\SeoPageRepositoryInterface;
use Illuminate\Validation\ValidationException;

class DeleteSeoPageAction
{
    public function __construct(
        private readonly SeoPageRepositoryInterface $seoPageRepository,
    ) {
    }

    public function execute(int $id): bool
    {
        $seoPage = $this->seoPageRepository->findById($id);

        if (!$seoPage) {
            throw ValidationException::withMessages([
                'id' => ['SEO page not found'],
            ]);
        }

        $routeName = $seoPage->route_name;
        $pageUrl = $seoPage->page_url;

        $result = $this->seoPageRepository->delete($seoPage);

        if ($result) {
            $getAction = app(GetSeoPageAction::class);
            $getAction->clearCache($routeName, $pageUrl);
        }

        return $result;
    }
}
