<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Pages\Models\BuilderPage;
use App\Domain\Pages\Services\DocxPageImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class BuilderPageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = BuilderPage::query()->orderByDesc('updated_at');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $page = BuilderPage::query()->find($id);
        if (! $page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json(['data' => $page]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePage($request);
        $data['created_by'] = Auth::guard('admin')->id();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['title']));

        $page = BuilderPage::query()->create($data);

        return response()->json(['data' => $page], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $page = BuilderPage::query()->find($id);
        if (! $page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        $data = $this->validatePage($request, $page->id);
        if (! empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $page->id);
        }

        $page->fill($data);
        $page->save();

        return response()->json(['data' => $page->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $page = BuilderPage::query()->find($id);
        if (! $page) {
            return response()->json(['message' => 'Page not found'], 404);
        }
        $page->delete();

        return response()->json(['success' => true]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $file = $request->file('image');
        $path = $file->store('pages', 'public');

        return response()->json([
            'data' => [
                'url' => '/storage/' . $path,
            ],
        ]);
    }

    public function importDocx(Request $request, DocxPageImporter $importer): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:25600'],
        ]);

        $ext = strtolower($request->file('file')->getClientOriginalExtension() ?: '');
        if (! in_array($ext, ['doc', 'docx'], true)) {
            return response()->json(['message' => 'Please upload a .doc or .docx file.'], 422);
        }

        try {
            $imported = $importer->import($request->file('file'));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Failed to import Word document.',
            ], 422);
        }

        $slugBase = Str::slug($imported['title']) ?: 'page-' . time();

        return response()->json([
            'data' => [
                'title' => $imported['title'],
                'slug' => $this->uniqueSlug($slugBase),
                'seo_description' => $imported['seo_description'] ?? null,
                'blocks' => $imported['blocks'],
            ],
        ]);
    }

    private function validatePage(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'blocks' => ['nullable', 'array'],
            'published' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('blocks', $validated) && is_string($request->input('blocks'))) {
            $decoded = json_decode($request->input('blocks'), true);
            $validated['blocks'] = is_array($decoded) ? $decoded : [];
        }

        $validated['published'] = $request->boolean('published', false);

        return $validated;
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'page';
        $candidate = $base;
        $i = 2;
        while (
            BuilderPage::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}
