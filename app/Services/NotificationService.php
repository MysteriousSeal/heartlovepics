<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Image;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\CommentFormatter;
use App\Support\GroupedNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class NotificationService
{
    public function recordImageLike(Image $image, ?User $actor = null): void
    {
        $recipient = $image->user;

        if (! $recipient || ! $recipient->notify_likes || ($actor && $recipient->is($actor))) {
            return;
        }

        UserNotification::create([
            'user_id' => $recipient->id,
            'type' => UserNotification::TYPE_IMAGE_LIKE,
            'actor_id' => $actor?->id,
            'image_id' => $image->id,
        ]);
    }

    public function recordImageComment(Image $image, Comment $comment, ?User $actor): void
    {
        $recipient = $image->user;

        if (! $recipient || ! $recipient->notify_comments) {
            return;
        }

        if ($this->isSameUser($recipient, $actor, $comment->user_id, $comment->author_name)) {
            return;
        }

        if ($comment->parent_id) {
            $parent = $comment->parent ?? Comment::query()->find($comment->parent_id);

            if ($parent && $this->isSameUser($recipient, null, $parent->user_id, $parent->author_name)) {
                return;
            }
        }

        UserNotification::create([
            'user_id' => $recipient->id,
            'type' => UserNotification::TYPE_IMAGE_COMMENT,
            'actor_id' => $actor?->id,
            'image_id' => $image->id,
            'comment_id' => $comment->id,
        ]);
    }

    public function recordImageBookmark(Image $image, User $actor): void
    {
        $recipient = $image->user;

        if (! $recipient || ! $recipient->notify_bookmarks || $recipient->is($actor)) {
            return;
        }

        UserNotification::create([
            'user_id' => $recipient->id,
            'type' => UserNotification::TYPE_IMAGE_BOOKMARK,
            'actor_id' => $actor->id,
            'image_id' => $image->id,
        ]);
    }

    public function recordFollow(User $recipient, User $actor): void
    {
        if (! $recipient->notify_follows || $recipient->is($actor)) {
            return;
        }

        UserNotification::create([
            'user_id' => $recipient->id,
            'type' => UserNotification::TYPE_USER_FOLLOW,
            'actor_id' => $actor->id,
        ]);
    }

    public function recordShout(User $recipient, User $actor): void
    {
        if (! $recipient->notify_shouts || $recipient->is($actor)) {
            return;
        }

        UserNotification::create([
            'user_id' => $recipient->id,
            'type' => UserNotification::TYPE_PROFILE_SHOUT,
            'actor_id' => $actor->id,
        ]);
    }

    public function recordCommentMentions(Image $image, Comment $comment, ?User $actor, ?Comment $parent = null): void
    {
        $usernames = app(CommentFormatter::class)->mentionedUsernames($comment->body);

        if ($usernames === []) {
            return;
        }

        $mentionedUsers = User::query()
            ->whereIn('username', $usernames)
            ->get();

        $imageOwnerId = $image->user_id;
        $parentRecipient = $parent ? $this->resolveCommentAuthor($parent) : null;

        foreach ($mentionedUsers as $recipient) {
            if (! $recipient->notify_mentions) {
                continue;
            }

            if ($actor?->is($recipient)) {
                continue;
            }

            if ($imageOwnerId && $recipient->id === $imageOwnerId) {
                continue;
            }

            if ($parentRecipient && $recipient->is($parentRecipient)) {
                continue;
            }

            UserNotification::create([
                'user_id' => $recipient->id,
                'type' => UserNotification::TYPE_COMMENT_MENTION,
                'actor_id' => $actor?->id,
                'image_id' => $image->id,
                'comment_id' => $comment->id,
            ]);
        }
    }

    public function recordCommentReply(Comment $reply, Comment $parent, ?User $actor): void
    {
        $recipient = $this->resolveCommentAuthor($parent);

        if (! $recipient || ! $recipient->notify_replies) {
            return;
        }

        if ($this->isSameUser($recipient, $actor, $reply->user_id, $reply->author_name)) {
            return;
        }

        UserNotification::create([
            'user_id' => $recipient->id,
            'type' => UserNotification::TYPE_COMMENT_REPLY,
            'actor_id' => $actor?->id,
            'image_id' => $reply->image_id,
            'comment_id' => $reply->id,
        ]);
    }

    public function unreadCount(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function groupedPaginatedFor(User $user, int $perPage = 20): LengthAwarePaginator
    {
        $notifications = UserNotification::query()
            ->where('user_id', $user->id)
            ->with(['actor', 'image', 'comment'])
            ->latest()
            ->get();

        $groups = $this->buildGroups($notifications);
        $page = max(1, (int) request()->query('page', 1));
        $total = $groups->count();
        $items = $groups->slice(($page - 1) * $perPage, $perPage)->values();

        return new Paginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()],
        );
    }

    /** Notification types that aren't tied to an image at all. */
    private const IMAGELESS_TYPES = [
        UserNotification::TYPE_USER_FOLLOW,
        UserNotification::TYPE_PROFILE_SHOUT,
    ];

    /** @return Collection<int, GroupedNotification> */
    private function buildGroups(Collection $notifications): Collection
    {
        $grouped = [];

        foreach ($notifications as $notification) {
            // Follow/shout notifications carry no image; every other type needs
            // one to render, and a missing image means the post was deleted.
            if (! $notification->image && ! in_array($notification->type, self::IMAGELESS_TYPES, true)) {
                continue;
            }

            $key = $this->groupKey($notification);

            if (! isset($grouped[$key])) {
                $grouped[$key] = new GroupedNotification(
                    $notification->type,
                    $notification->image,
                    collect(),
                    $notification->comment,
                );
            }

            $grouped[$key]->notifications->push($notification);
        }

        return collect($grouped)
            ->sortByDesc(fn (GroupedNotification $group) => $group->latestAt())
            ->values();
    }

    private function groupKey(UserNotification $notification): string
    {
        if (in_array($notification->type, self::IMAGELESS_TYPES, true)) {
            return $notification->type;
        }

        if (in_array($notification->type, [UserNotification::TYPE_COMMENT_REPLY, UserNotification::TYPE_COMMENT_MENTION], true)) {
            return $notification->type.':comment:'.$notification->comment_id;
        }

        return $notification->type.':image:'.$notification->image_id;
    }

    public function markGroupAsRead(User $user, string $type, ?int $imageId, ?int $commentId = null): void
    {
        $query = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('read_at');

        if (in_array($type, self::IMAGELESS_TYPES, true)) {
            $query->update(['read_at' => now()]);

            return;
        }

        $query->where('image_id', $imageId);

        if (in_array($type, [UserNotification::TYPE_COMMENT_REPLY, UserNotification::TYPE_COMMENT_MENTION], true)) {
            $query->where('comment_id', $commentId);
        }

        $query->update(['read_at' => now()]);
    }

    public function markAsRead(UserNotification $notification, User $user): void
    {
        abort_unless($notification->user_id === $user->id, 403);

        if ($notification->read_at) {
            return;
        }

        $notification->update(['read_at' => now()]);
    }

    public function markAllAsRead(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /** @param  array<string, bool>  $preferences */
    public function updatePreferences(User $user, array $preferences): void
    {
        $user->update([
            'notify_likes' => $preferences['notify_likes'] ?? $user->notify_likes,
            'notify_comments' => $preferences['notify_comments'] ?? $user->notify_comments,
            'notify_bookmarks' => $preferences['notify_bookmarks'] ?? $user->notify_bookmarks,
            'notify_replies' => $preferences['notify_replies'] ?? $user->notify_replies,
            'notify_mentions' => $preferences['notify_mentions'] ?? $user->notify_mentions,
            'notify_follows' => $preferences['notify_follows'] ?? $user->notify_follows,
            'notify_shouts' => $preferences['notify_shouts'] ?? $user->notify_shouts,
        ]);
    }

    private function resolveCommentAuthor(Comment $comment): ?User
    {
        if ($comment->relationLoaded('user') && $comment->user) {
            return $comment->user;
        }

        if ($comment->user_id) {
            return User::query()->find($comment->user_id);
        }

        return User::query()
            ->where('username', $comment->author_name)
            ->first();
    }

    private function isSameUser(User $recipient, ?User $actor, ?int $commentUserId, string $authorName): bool
    {
        if ($actor && $recipient->is($actor)) {
            return true;
        }

        if ($commentUserId && $recipient->id === $commentUserId) {
            return true;
        }

        return $authorName === $recipient->username;
    }
}