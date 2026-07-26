<?php

namespace App\Domain\Pages\Controllers;

use App\Domain\Pages\Models\BuilderPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class FrontBuilderPageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = BuilderPage::query()
            ->where('slug', $slug)
            ->where('published', true)
            ->first();

        if (! $page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'seo_title' => $page->seo_title,
                'seo_description' => $page->seo_description,
                'blocks' => $page->blocks ?? [],
            ],
        ]);
    }
}
