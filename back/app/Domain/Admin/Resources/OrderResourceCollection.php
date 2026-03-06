<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderResourceCollection extends ResourceCollection
{
    protected $paginator;
    protected $filters;

    public function __construct($resource, LengthAwarePaginator $paginator = null, array $filters = [])
    {
        parent::__construct($resource);
        $this->paginator = $paginator;
        $this->filters = $filters;
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

        if (!empty($this->filters)) {
            $data['filters'] = $this->filters;
        }

        return $data;
    }
}

