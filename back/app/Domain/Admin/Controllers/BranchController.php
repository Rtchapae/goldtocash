<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Users\Repositories\BranchRepositoryInterface;
use App\Domain\Admin\Resources\BranchResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepository,
    ) {
    }

    public function index(): JsonResponse
    {
        $branches = $this->branchRepository->getAllOrderedByDisplayName();

        return response()->json([
            'data' => BranchResource::collection($branches),
        ]);
    }
}

