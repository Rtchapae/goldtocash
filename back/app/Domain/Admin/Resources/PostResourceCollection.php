<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostResourceCollection extends ResourceCollection
{
    protected $paginator;

    public function __construct($resource, LengthAwarePaginator $paginator = null)
    {
        parent::__construct($resource);
        $this->paginator = $paginator;
    }

    public function toArray($request): array
    {
        $data = [
            'data' => $this->collection,
        ];

        if ($this->paginator) {
            $data['meta'] = [
                'current_page' => $this->paginator->currentPage(),
                'per_page' => $this->paginator->perPage(),
                'total' => $this->paginator->total(),
                'last_page' => $this->paginator->lastPage(),
            ];
        }

        return $data;
    }
}

