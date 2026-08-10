<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ImageBookmark;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;

class BookmarkService
{
    public function __construct(
        private NotificationService $notifications,
        private CollectionService $collections,
    ) {}
    public function isBookmarked(Image $image, Request $request): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        return ImageBookmark::query()
            ->where('image_id', $image->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /** @return array<int, bool> */
    public function bookmarkedMapForImages(iterable $images, Request $request): array
    {
        $user = $request->user();

        if (! $user) {
            return [];
        }

        $collection = $images instanceof AbstractPaginator
            ? $images->getCollection()
            : collect($images);

        $imageIds = $collection->pluck('id')->filter()->values();

        if ($imageIds->isEmpty()) {
            return [];
        }

        $bookmarkedIds = ImageBookmark::query()
            ->whereIn('image_id', $imageIds)
            ->where('user_id', $user->id)
            ->pluck('image_id');

        return $imageIds
            ->mapWithKeys(fn ($id) => [(int) $id => $bookmarkedIds->contains($id)])
            ->all();
    }

    /** @return array{bookmarked: bool, count: int} */
    public function toggle(Image $image, Request $request): array
    {
        $user = $request->user();

        abort_unless($user, 401);

        $bookmark = ImageBookmark::query()
            ->where('image_id', $image->id)
            ->where('user_id', $user->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $this->collections->detachImageFromAllCollections($user, $image);
            $bookmarked = false;
        } else {
            ImageBookmark::create([
                'image_id' => $image->id,
                'user_id' => $user->id,
            ]);
            $bookmarked = true;
            $this->notifications->recordImageBookmark($image, $user);
        }

        return [
            'bookmarked' => $bookmarked,
            'count' => $image->bookmarks()->count(),
        ];
    }
}