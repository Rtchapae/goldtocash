<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Requests\ListPostsRequest;
use App\Domain\Admin\Requests\CreatePostRequest;
use App\Domain\Admin\Requests\UpdatePostRequest;
use App\Domain\Admin\Resources\PostResource;
use App\Domain\Admin\Resources\PostResourceCollection;
use App\Domain\Admin\Resources\PostEditResource;
use App\Domain\Posts\Repositories\AdminPostRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct(
        private AdminPostRepositoryInterface $postRepository
    ) {
    }

    public function index(ListPostsRequest $request): JsonResponse
    {
        $filters = [
            'page' => $request->page(),
            'per_page' => $request->perPage(),
            'period' => $request->period(),
            'from' => $request->from(),
            'to' => $request->to(),
            'order-by' => $request->orderBy(),
            'order-dir' => $request->orderDir(),
        ];

        $results = $this->postRepository->search($filters);

        return response()->json(new PostResourceCollection(
            PostResource::collection($results->items()),
            $results
        ));
    }

    public function show(int $id): JsonResponse
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json(new PostEditResource($post));
    }

    public function store(CreatePostRequest $request): JsonResponse
    {
        $data = [
            'user_id' => Auth::guard('admin')->id(),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'slug' => Str::slug($request->input('title')),
            'seo_title' => $request->input('seo_title'),
            'seo_description' => $request->input('seo_description'),
            'path_prefix' => $request->input('path_prefix'),
            'active' => $request->boolean('active', true),
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/blog/images', $imageName);
            $data['image'] = $imageName;
        }

        $post = $this->postRepository->create($data);

        return response()->json(new PostResource($post), 201);
    }

    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $data = [
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'slug' => Str::slug($request->input('title')),
            'seo_title' => $request->input('seo_title'),
            'seo_description' => $request->input('seo_description'),
            'path_prefix' => $request->input('path_prefix'),
            'active' => $request->boolean('active', true),
        ];

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::delete('public/blog/images/' . $post->image);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/blog/images', $imageName);
            $data['image'] = $imageName;
        }

        $updated = $this->postRepository->update($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update post'], 500);
        }

        $post = $this->postRepository->findById($id);

        return response()->json(new PostResource($post));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->postRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:5120'],
        ]);

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            Storage::disk('public')->putFileAs('blog/images', $image, $imageName);

            $url = Storage::disk('public')->url('blog/images/' . $imageName);

            if (!str_starts_with($url, 'http')) {
                $url = asset('storage/blog/images/' . $imageName);
            }

            return response()->json([
                'location' => $url
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}

