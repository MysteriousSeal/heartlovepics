<?php

namespace App\Services;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowService
{
    public function __construct(private NotificationService $notifications) {}

    public function isFollowing(User $user, Request $request): bool
    {
        $follower = $request->user();

        if (! $follower || $follower->is($user)) {
            return false;
        }

        return Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $user->id)
            ->exists();
    }

    /** @return array<int, int> */
    public function followedUserIds(Request $request): array
    {
        $user = $request->user();

        if (! $user) {
            return [];
        }

        return Follow::query()
            ->where('follower_id', $user->id)
            ->pluck('following_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /** @return array{following: bool, followers_count: int} */
    public function toggle(User $user, Request $request): array
    {
        $follower = $request->user();

        abort_unless($follower, 401);
        abort_if($follower->is($user), 403);

        $follow = Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();
            $following = false;
        } else {
            Follow::create([
                'follower_id' => $follower->id,
                'following_id' => $user->id,
            ]);
            $following = true;

            $this->notifications->recordFollow($user, $follower);
        }

        return [
            'following' => $following,
            'followers_count' => $user->followers()->count(),
        ];
    }
}