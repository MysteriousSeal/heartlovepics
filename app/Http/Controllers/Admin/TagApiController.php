<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagApiController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::query()
            ->with('images:id')
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'image_ids' => $tag->images->pluck('id'),
            ]);

        return response()->json($tags);
    }

    public function destroy(string $name): JsonResponse
    {
        $tag = Tag::query()->where('name', trim($name))->firstOrFail();

        $postCount = $tag->images()->count();
        $tag->images()->detach();
        $tag->delete();

        return response()->json([
            'deleted' => $tag->name,
            'detached_from_posts' => $postCount,
        ]);
    }
}
