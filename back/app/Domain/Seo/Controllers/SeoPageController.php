<?php

namespace App\Domain\Seo\Controllers;

use App\Domain\Seo\Actions\CreateSeoPageAction;
use App\Domain\Seo\Actions\DeleteSeoPageAction;
use App\Domain\Seo\Actions\ListSeoPagesAction;
use App\Domain\Seo\Actions\UpdateSeoPageAction;
use App\Domain\Seo\Requests\CreateSeoPageRequest;
use App\Domain\Seo\Requests\ListSeoPagesRequest;
use App\Domain\Seo\Requests\UpdateSeoPageRequest;
use App\Domain\Seo\Resources\SeoPageResource;
use App\Domain\Seo\Resources\SeoPageResourceCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SeoPageController extends Controller
{
    public function __construct(
        private readonly ListSeoPagesAction $listSeoPagesAction,
        private readonly CreateSeoPageAction $createSeoPageAction,
        private readonly UpdateSeoPageAction $updateSeoPageAction,
        private readonly DeleteSeoPageAction $deleteSeoPageAction,
    ) {
    }

    public function index(ListSeoPagesRequest $request): JsonResponse
    {
        $paginator = $this->listSeoPagesAction->execute(
            perPage: $request->perPage(),
            page: $request->page(),
            search: $request->search(),
        );

        return response()->json(new SeoPageResourceCollection($paginator));
    }

    public function store(CreateSeoPageRequest $request): JsonResponse
    {
        try {
            $seoPage = $this->createSeoPageAction->execute($request->validated());
            return response()->json(new SeoPageResource($seoPage), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create SEO page',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $seoPage = app(\App\Domain\Seo\Repositories\SeoPageRepositoryInterface::class)->findById($id);

        if (!$seoPage) {
            return response()->json([
                'message' => 'SEO page not found',
            ], 404);
        }

        return response()->json(new SeoPageResource($seoPage));
    }

    public function update(UpdateSeoPageRequest $request, int $id): JsonResponse
    {
        try {
            $seoPage = $this->updateSeoPageAction->execute($id, $request->validated());
            return response()->json(new SeoPageResource($seoPage));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update SEO page',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteSeoPageAction->execute($id);
            return response()->json([
                'message' => 'SEO page deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete SEO page',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
