<?php

namespace App\Domain\Seo\Actions;

use App\Domain\Seo\Repositories\SeoPageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListSeoPagesAction
{
    public function __construct(
        private readonly SeoPageRepositoryInterface $seoPageRepository,
    ) {
    }

    public function execute(
        int $perPage = 15,
        int $page = 1,
        ?string $search = null,
    ): LengthAwarePaginator {
        $query = \App\Domain\Seo\Models\SeoPage::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('page_title', 'LIKE', '%' . $search . '%')
                  ->orWhere('route_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('page_url', 'LIKE', '%' . $search . '%')
                  ->orWhere('meta_title', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy('page_title')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
