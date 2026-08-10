<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

class CollectionService
{
    public function forUser(User $user): EloquentCollection
    {
        return $user->collections()->withCount('images')->orderBy('name')->get();
    }

    /** @return array<int, bool> Collection id => whether $image is in it. */
    public function membershipMap(User $user, Image $image): array
    {
        $collectionIds = $user->collections()->pluck('id');

        if ($collectionIds->isEmpty()) {
            return [];
        }

        $inIds = DB::table('collection_image')
            ->whereIn('collection_id', $collectionIds)
            ->where('image_id', $image->id)
            ->pluck('collection_id');

        return $collectionIds->mapWithKeys(fn ($id) => [$id => $inIds->contains($id)])->all();
    }

    public function create(User $user, string $name): Collection
    {
        return $user->collections()->create(['name' => $name]);
    }

    public function rename(Collection $collection, string $name): void
    {
        $collection->update(['name' => $name]);
    }

    public function delete(Collection $collection): void
    {
        $collection->delete();
    }

    /** @return array{inCollection: bool, count: int} */
    public function toggleImage(Collection $collection, Image $image): array
    {
        $exists = $collection->images()->where('image_id', $image->id)->exists();

        if ($exists) {
            $collection->images()->detach($image->id);
        } else {
            $collection->images()->attach($image->id);
        }

        return [
            'inCollection' => ! $exists,
            'count' => $collection->images()->count(),
        ];
    }

    public function detachImageFromAllCollections(User $user, Image $image): void
    {
        $collectionIds = $user->collections()->pluck('id');

        if ($collectionIds->isEmpty()) {
            return;
        }

        DB::table('collection_image')
            ->whereIn('collection_id', $collectionIds)
            ->where('image_id', $image->id)
            ->delete();
    }
}
