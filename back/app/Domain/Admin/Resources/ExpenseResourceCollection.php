<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseResourceCollection extends ResourceCollection
{
    private $pagination;

    public function __construct($resource, $pagination)
    {
        parent::__construct($resource);
        $this->pagination = $pagination;
    }

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page' => $this->pagination->currentPage(),
                'last_page' => $this->pagination->lastPage(),
                'per_page' => $this->pagination->perPage(),
                'total' => $this->pagination->total(),
            ],
        ];
    }
}

