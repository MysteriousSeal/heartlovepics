<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Image;
use App\Services\BookmarkService;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CollectionController extends Controller
{
    public function store(Request $request, CollectionService $collections): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:'.Collection::MAX_NAME_LENGTH,
                Rule::unique('collections')->where('user_id', $user->id),
            ],
        ]);

        $collections->create($user, trim($validated['name']));

        return redirect()
            ->route('users.bookmarks', $user)
            ->with('success', 'Collection created.');
    }

    public function update(Request $request, Collection $collection, CollectionService $collections): RedirectResponse
    {
        abort_unless($request->user()->is($collection->user), 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:'.Collection::MAX_NAME_LENGTH,
                Rule::unique('collections')->where('user_id', $collection->user_id)->ignore($collection->id),
            ],
        ]);

        $collections->rename($collection, trim($validated['name']));

        return redirect()
            ->route('users.bookmarks', ['user' => $collection->user, 'collection' => $collection->id])
            ->with('success', 'Collection renamed.');
    }

    public function destroy(Request $request, Collection $collection, CollectionService $collections): RedirectResponse
    {
        abort_unless($request->user()->is($collection->user), 403);

        $collections->delete($collection);

        return redirect()
            ->route('users.bookmarks', $collection->user)
            ->with('success', 'Collection deleted.');
    }

    public function toggleImage(Request $request, Collection $collection, Image $image, CollectionService $collections, BookmarkService $bookmarks): JsonResponse
    {
        abort_unless($request->user()->is($collection->user), 403);
        abort_unless($bookmarks->isBookmarked($image, $request), 422, 'Bookmark this image before adding it to a collection.');

        return response()->json($collections->toggleImage($collection, $image));
    }
}
