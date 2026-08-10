<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\BookmarkService;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function toggle(Request $request, string $slug, BookmarkService $bookmarks, CollectionService $collections): JsonResponse
    {
        $image = Image::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        abort_unless($image->isVisibleTo($request->user()), 404);

        $result = $bookmarks->toggle($image, $request);

        $user = $request->user();

        $result['collectionPickerHtml'] = view('partials.collection-picker-menu', [
            'image' => $image,
            'bookmarked' => $result['bookmarked'],
            'userCollections' => $collections->forUser($user),
            'collectionMap' => $collections->membershipMap($user, $image),
        ])->render();

        return response()->json($result);
    }
}