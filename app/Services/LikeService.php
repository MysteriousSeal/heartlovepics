<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ImageLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

class LikeService
{
    public function __construct(
        private FingerprintLikeService $fingerprintLikes,
        private NotificationService $notifications,
    ) {}

    public function fingerprint(Request $request): string
    {
        return $this->fingerprintLikes->fingerprint($request);
    }

    public function isLiked(Image $image, Request $request): bool
    {
        if ($user = $request->user()) {
            return ImageLike::query()
                ->where('image_id', $image->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        return $this->fingerprintLikes->isLiked(ImageLike::class, 'image_id', $image->id, $request);
    }

    /** @return array<int, bool> */
    public function likedMapForImages(iterable $images, Request $request): array
    {
        if ($user = $request->user()) {
            return $this->likedMapForUser($images, $user->id);
        }

        return $this->fingerprintLikes->likedMap(ImageLike::class, 'image_id', $images, $request);
    }

    /** @return array{users: Collection<int, User>, others_count: int, total: int} */
    public function recentLikersForImage(Image $image, int $limit = 6): array
    {
        $likes = ImageLike::query()
            ->where('image_id', $image->id)
            ->latest()
            ->get();

        $users = collect();
        $seenUserIds = [];

        foreach ($likes as $like) {
            if (! $like->user_id || in_array($like->user_id, $seenUserIds, true)) {
                continue;
            }

            $user = User::query()->find($like->user_id);

            if (! $user) {
                continue;
            }

            $seenUserIds[] = $like->user_id;
            $users->push($user);

            if ($users->count() >= $limit) {
                break;
            }
        }

        return [
            'users' => $users,
            'others_count' => max(0, $likes->count() - $users->count()),
            'total' => $likes->count(),
        ];
    }

    public function toggle(Image $image, Request $request): array
    {
        if ($user = $request->user()) {
            return $this->toggleForUser($image, $user->id);
        }

        $result = $this->fingerprintLikes->toggle(
            ImageLike::class,
            'image_id',
            $image->id,
            fn () => $image->likes()->count(),
            $request,
        );

        if ($result['liked']) {
            $this->notifications->recordImageLike($image);
        }

        return $result;
    }

    /** @return array<int, bool> */
    private function likedMapForUser(iterable $images, int $userId): array
    {
        $collection = $images instanceof AbstractPaginator
            ? $images->getCollection()
            : collect($images);

        $imageIds = $collection->pluck('id')->filter()->values();

        if ($imageIds->isEmpty()) {
            return [];
        }

        $likedIds = ImageLike::query()
            ->whereIn('image_id', $imageIds)
            ->where('user_id', $userId)
            ->pluck('image_id');

        return $imageIds
            ->mapWithKeys(fn ($id) => [(int) $id => $likedIds->contains($id)])
            ->all();
    }

    /** @return array{liked: bool, count: int} */
    private function toggleForUser(Image $image, int $userId): array
    {
        $like = ImageLike::query()
            ->where('image_id', $image->id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            ImageLike::create([
                'image_id' => $image->id,
                'user_id' => $userId,
            ]);
            $liked = true;

            $this->notifications->recordImageLike(
                $image,
                User::query()->find($userId),
            );
        }

        return [
            'liked' => $liked,
            'count' => $image->likes()->count(),
        ];
    }
}