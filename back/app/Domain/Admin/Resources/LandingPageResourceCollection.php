<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LandingPageResourceCollection extends ResourceCollection
{
    public function __construct(
        $resource,
        private readonly LengthAwarePaginator $paginator,
    ) {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page' => $this->paginator->currentPage(),
                'last_page' => $this->paginator->lastPage(),
                'per_page' => $this->paginator->perPage(),
                'total' => $this->paginator->total(),
            ],
        ];
    }
}
