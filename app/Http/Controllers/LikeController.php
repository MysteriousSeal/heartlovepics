<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, string $slug, LikeService $likes): JsonResponse
    {
        $image = Image::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        abort_unless($image->isVisibleTo($request->user()), 404);

        return response()->json($likes->toggle($image, $request));
    }
}