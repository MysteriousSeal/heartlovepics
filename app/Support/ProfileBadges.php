<?php

namespace App\Support;

use App\Models\Image;
use App\Models\ImageLike;
use App\Models\User;

class ProfileBadges
{
    /**
     * Milestone badges earned by a creator, derived entirely from existing
     * data — no badge table, nothing to award or revoke by hand. Only the
     * highest tier reached per category is shown.
     *
     * @return list<array{label: string}>
     */
    public static function for(User $user): array
    {
        $badges = [];

        if ($tier = self::tier(self::uploadCount($user), [100, 50, 10, 1])) {
            $badges[] = ['label' => $tier === 1 ? 'First upload' : $tier.'+ uploads'];
        }

        if ($tier = self::tier($user->followers()->count(), [500, 100, 50, 10])) {
            $badges[] = ['label' => $tier.'+ followers'];
        }

        if ($tier = self::tier(self::likesReceived($user), [1000, 200, 50])) {
            $badges[] = ['label' => $tier.'+ likes'];
        }

        if ($years = self::memberYears($user)) {
            $badges[] = ['label' => $years === 1 ? '1 year member' : $years.' years member'];
        }

        return $badges;
    }

    private static function uploadCount(User $user): int
    {
        return Image::query()
            ->where('user_id', $user->id)
            ->where('is_published', true)
            ->where('is_private', false)
            ->count();
    }

    private static function likesReceived(User $user): int
    {
        return ImageLike::query()
            ->whereHas('image', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('is_published', true)
                ->where('is_private', false))
            ->count();
    }

    private static function memberYears(User $user): int
    {
        return (int) floor($user->created_at->diffInYears(now()));
    }

    /** @param  list<int>  $tiers  Highest first. */
    private static function tier(int $value, array $tiers): ?int
    {
        foreach ($tiers as $tier) {
            if ($value >= $tier) {
                return $tier;
            }
        }

        return null;
    }
}
