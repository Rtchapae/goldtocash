<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Requests\CreateLandingPageRequest;
use App\Domain\Admin\Requests\ListLandingPagesRequest;
use App\Domain\Admin\Requests\UpdateLandingPageRequest;
use App\Domain\Admin\Resources\LandingPageResource;
use App\Domain\Admin\Resources\LandingPageResourceCollection;
use App\Domain\LandingPages\Repositories\LandingPageRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    public function __construct(
        private LandingPageRepositoryInterface $repository,
    ) {
    }

    public function index(ListLandingPagesRequest $request): JsonResponse
    {
        $filters = [
            'page' => $request->page(),
            'per_page' => $request->perPage(),
            'search' => $request->search(),
            'order-by' => $request->orderBy(),
            'order-dir' => $request->orderDir(),
        ];

        $results = $this->repository->search($filters);

        return response()->json(new LandingPageResourceCollection(
            LandingPageResource::collection($results->items()),
            $results,
        ));
    }

    public function show(int $id): JsonResponse
    {
        $page = $this->repository->findById($id);
        if (! $page) {
            return response()->json(['message' => 'Landing page not found'], 404);
        }

        return response()->json(new LandingPageResource($page));
    }

    public function store(CreateLandingPageRequest $request): JsonResponse
    {
        $page = $this->repository->create([
            'user_id' => Auth::guard('admin')->id(),
            'title' => $request->input('title'),
            'path' => $request->input('path'),
            'blocks' => $request->input('blocks'),
            'seo_title' => $request->input('seo_title'),
            'seo_description' => $request->input('seo_description'),
            'active' => $request->boolean('active', true),
        ]);

        return response()->json(new LandingPageResource($page), 201);
    }

    public function update(UpdateLandingPageRequest $request, int $id): JsonResponse
    {
        $page = $this->repository->findById($id);
        if (! $page) {
            return response()->json(['message' => 'Landing page not found'], 404);
        }

        $updated = $this->repository->update($id, [
            'title' => $request->input('title'),
            'path' => $request->input('path'),
            'blocks' => $request->input('blocks'),
            'seo_title' => $request->input('seo_title'),
            'seo_description' => $request->input('seo_description'),
            'active' => $request->boolean('active', true),
        ]);

        if (! $updated) {
            return response()->json(['message' => 'Failed to update landing page'], 500);
        }

        return response()->json(new LandingPageResource($this->repository->findById($id)));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (! $deleted) {
            return response()->json(['message' => 'Landing page not found'], 404);
        }

        return response()->json(['message' => 'Landing page deleted successfully']);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:5120'],
        ]);

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('landing-pages/images', $image, $imageName);

            return response()->json([
                'location' => '/storage/landing-pages/images/'.$imageName,
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}
