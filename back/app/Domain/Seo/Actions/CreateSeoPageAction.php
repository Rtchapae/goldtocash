<?php

namespace App\Domain\Seo\Actions;

use App\Domain\Seo\Repositories\SeoPageRepositoryInterface;
use App\Domain\Seo\Models\SeoPage;
use Illuminate\Validation\ValidationException;

class CreateSeoPageAction
{
    public function __construct(
        private readonly SeoPageRepositoryInterface $seoPageRepository,
    ) {
    }

    public function execute(array $data): SeoPage
    {
        if ($this->seoPageRepository->findByRouteName($data['route_name'])) {
            throw ValidationException::withMessages([
                'route_name' => ['Route name already exists'],
            ]);
        }

        if ($this->seoPageRepository->findByUrl($data['page_url'])) {
            throw ValidationException::withMessages([
                'page_url' => ['Page URL already exists'],
            ]);
        }

        $seoPage = $this->seoPageRepository->create($data);

        $getAction = app(GetSeoPageAction::class);
        $getAction->clearCache($seoPage->route_name, $seoPage->page_url);

        return $seoPage;
    }
}
