<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;

class FingerprintLikeService
{
    public function fingerprint(Request $request): string
    {
        return hash('sha256', $request->ip().'|'.$request->session()->getId());
    }

    public function isLiked(string $modelClass, string $foreignKey, int $parentId, Request $request): bool
    {
        return $modelClass::query()
            ->where($foreignKey, $parentId)
            ->where('fingerprint', $this->fingerprint($request))
            ->exists();
    }

    /** @return array<int, bool> */
    public function likedMap(string $modelClass, string $foreignKey, iterable $items, Request $request): array
    {
        $collection = $items instanceof AbstractPaginator
            ? $items->getCollection()
            : collect($items);

        $parentIds = $collection->pluck('id')->filter()->values();

        if ($parentIds->isEmpty()) {
            return [];
        }

        $likedIds = $modelClass::query()
            ->whereIn($foreignKey, $parentIds)
            ->where('fingerprint', $this->fingerprint($request))
            ->pluck($foreignKey);

        return $parentIds->mapWithKeys(fn ($id) => [(int) $id => $likedIds->contains($id)])->all();
    }

    /** @return array{liked: bool, count: int} */
    public function toggle(
        string $modelClass,
        string $foreignKey,
        int $parentId,
        callable $countResolver,
        Request $request,
    ): array {
        $fingerprint = $this->fingerprint($request);

        $like = $modelClass::query()
            ->where($foreignKey, $parentId)
            ->where('fingerprint', $fingerprint)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $modelClass::create([
                $foreignKey => $parentId,
                'fingerprint' => $fingerprint,
            ]);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'count' => (int) $countResolver(),
        ];
    }
}