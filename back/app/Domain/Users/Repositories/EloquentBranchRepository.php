<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\Branch;
use Illuminate\Support\Collection;

class EloquentBranchRepository implements BranchRepositoryInterface
{
    public function getAllOrderedByDisplayName(): Collection
    {
        return Branch::orderBy('display_name')->get();
    }
}

