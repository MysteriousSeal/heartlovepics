<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

class CommentLikeService
{
    public function __construct(private FingerprintLikeService $fingerprintLikes) {}

    public function isLiked(Comment $comment, Request $request): bool
    {
        if ($user = $request->user()) {
            return CommentLike::query()
                ->where('comment_id', $comment->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        return $this->fingerprintLikes->isLiked(CommentLike::class, 'comment_id', $comment->id, $request);
    }

    /** @return array<int, bool> */
    public function likedMapForComments(iterable $comments, Request $request): array
    {
        if ($user = $request->user()) {
            return $this->likedMapForUser($comments, $user->id);
        }

        return $this->fingerprintLikes->likedMap(CommentLike::class, 'comment_id', $comments, $request);
    }

    /** @return array{liked: bool, count: int, recent_likers: array{users: list<array{username: string, url: string, avatar_url: ?string, initials: string, color: string}>, others_count: int, total: int}} */
    public function toggle(Comment $comment, Request $request): array
    {
        if ($user = $request->user()) {
            $result = $this->toggleForUser($comment, $user->id);
        } else {
            $result = $this->fingerprintLikes->toggle(
                CommentLike::class,
                'comment_id',
                $comment->id,
                fn () => $comment->likes()->count(),
                $request,
            );
        }

        $result['recent_likers'] = $this->recentLikersPayload($comment);

        return $result;
    }

    /**
     * @return array<int, array{users: Collection<int, User>, others_count: int, total: int}>
     */
    public function recentLikersMapForComments(iterable $comments, int $limit = 4): array
    {
        $collection = $comments instanceof AbstractPaginator
            ? $comments->getCollection()
            : collect($comments);

        $commentIds = $collection->pluck('id')->filter()->values();

        if ($commentIds->isEmpty()) {
            return [];
        }

        $likes = CommentLike::query()
            ->whereIn('comment_id', $commentIds)
            ->with('user')
            ->latest()
            ->get()
            ->groupBy('comment_id');

        $map = [];

        foreach ($commentIds as $commentId) {
            $map[(int) $commentId] = $this->formatLikersGroup($likes->get($commentId, collect()), $limit);
        }

        return $map;
    }

    /** @return array{users: Collection<int, User>, others_count: int, total: int} */
    public function recentLikersForComment(Comment $comment, int $limit = 4): array
    {
        $likes = CommentLike::query()
            ->where('comment_id', $comment->id)
            ->with('user')
            ->latest()
            ->get();

        return $this->formatLikersGroup($likes, $limit);
    }

    /** @return array{users: list<array{username: string, url: string, avatar_url: ?string, initials: string, color: string}>, others_count: int, total: int} */
    public function recentLikersPayload(Comment $comment, int $limit = 4): array
    {
        $likers = $this->recentLikersForComment($comment, $limit);

        return [
            'users' => $likers['users']->map(fn (User $user) => [
                'username' => $user->username,
                'url' => route('users.show', $user),
                'avatar_url' => $user->avatar_url,
                'initials' => $user->avatar_initials,
                'color' => $user->avatar_color,
            ])->values()->all(),
            'others_count' => $likers['others_count'],
            'total' => $likers['total'],
        ];
    }

    /** @return array<int, bool> */
    private function likedMapForUser(iterable $comments, int $userId): array
    {
        $collection = $comments instanceof AbstractPaginator
            ? $comments->getCollection()
            : collect($comments);

        $commentIds = $collection->pluck('id')->filter()->values();

        if ($commentIds->isEmpty()) {
            return [];
        }

        $likedIds = CommentLike::query()
            ->whereIn('comment_id', $commentIds)
            ->where('user_id', $userId)
            ->pluck('comment_id');

        return $commentIds
            ->mapWithKeys(fn ($id) => [(int) $id => $likedIds->contains($id)])
            ->all();
    }

    /** @return array{liked: bool, count: int} */
    private function toggleForUser(Comment $comment, int $userId): array
    {
        $like = CommentLike::query()
            ->where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
                // Keep fingerprint non-null for legacy unique index compatibility.
                'fingerprint' => 'user:'.$userId,
            ]);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'count' => $comment->likes()->count(),
        ];
    }

    /**
     * @param  Collection<int, CommentLike>  $likes
     * @return array{users: Collection<int, User>, others_count: int, total: int}
     */
    private function formatLikersGroup(Collection $likes, int $limit): array
    {
        $users = collect();
        $seenUserIds = [];

        foreach ($likes as $like) {
            if (! $like->user_id || in_array($like->user_id, $seenUserIds, true)) {
                continue;
            }

            $user = $like->relationLoaded('user') ? $like->user : User::query()->find($like->user_id);

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
}