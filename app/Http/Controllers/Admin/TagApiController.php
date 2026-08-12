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
}
