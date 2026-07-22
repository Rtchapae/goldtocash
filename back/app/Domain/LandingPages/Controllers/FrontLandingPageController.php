<?php

namespace App\Domain\LandingPages\Controllers;

use App\Domain\LandingPages\Repositories\LandingPageRepositoryInterface;
use App\Domain\LandingPages\Resources\FrontLandingPageResource;
use App\Domain\LandingPages\Support\LandingPagePathNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FrontLandingPageController extends Controller
{
    public function __construct(
        private LandingPageRepositoryInterface $repository,
    ) {
    }

    public function showByPath(Request $request): JsonResponse
    {
        $path = LandingPagePathNormalizer::normalize($request->query('path', ''));
        if ($path === '/') {
            return response()->json(['message' => 'Landing page not found'], 404);
        }

        $page = $this->repository->findActiveByPath($path);
        if (! $page) {
            return response()->json(['message' => 'Landing page not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new FrontLandingPageResource($page),
        ]);
    }

    public function paths(): JsonResponse
    {
        return response()->json([
            'paths' => $this->repository->getActivePaths(),
        ]);
    }
}
