<?php

namespace App\Domain\Posts\Controllers;

use App\Domain\Posts\Repositories\FrontPostRepositoryInterface;
use App\Domain\Posts\Requests\ListFrontPostsRequest;
use App\Domain\Posts\Requests\ShowFrontPostRequest;
use App\Domain\Posts\Resources\FrontPostDetailResource;
use App\Domain\Posts\Resources\FrontPostResource;
use App\Domain\Posts\Resources\FrontPostResourceCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class FrontPostController extends Controller
{
    public function __construct(
        private readonly FrontPostRepositoryInterface $postRepository,
    ) {
    }

    public function index(ListFrontPostsRequest $request): JsonResponse
    {
        $paginator = $this->postRepository->getPublicPosts(
            $request->pathPrefix(),
            $request->page(),
            $request->perPage()
        );

        return response()->json(new FrontPostResourceCollection(
            FrontPostResource::collection($paginator->items()),
            $paginator
        ));
    }

    public function show(ShowFrontPostRequest $request, string $slug): JsonResponse
    {
        $post = $this->postRepository->findBySlug($slug, $request->pathPrefix());

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => new FrontPostDetailResource($post),
        ]);
    }
}

