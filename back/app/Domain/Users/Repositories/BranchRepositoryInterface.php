<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\Branch;
use Illuminate\Support\Collection;

interface BranchRepositoryInterface
{
    public function getAllOrderedByDisplayName(): Collection;
}

