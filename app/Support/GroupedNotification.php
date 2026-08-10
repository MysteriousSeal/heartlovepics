<?php

namespace App\Support;

use App\Models\Comment;
use App\Models\Image;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GroupedNotification
{
    /** @param  Collection<int, UserNotification>  $notifications */
    public function __construct(
        public string $type,
        public ?Image $image,
        public Collection $notifications,
        public ?Comment $comment = null,
    ) {}

    public function groupKey(): string
    {
        if (in_array($this->type, [UserNotification::TYPE_USER_FOLLOW, UserNotification::TYPE_PROFILE_SHOUT], true)) {
            return $this->type;
        }

        if (in_array($this->type, [UserNotification::TYPE_COMMENT_REPLY, UserNotification::TYPE_COMMENT_MENTION], true)) {
            return $this->type.':comment:'.$this->comment?->id;
        }

        return $this->type.':image:'.$this->image?->id;
    }

    public function isUnread(): bool
    {
        return $this->notifications->contains(fn (UserNotification $notification) => ! $notification->isRead());
    }

    public function latestAt(): Carbon
    {
        return $this->notifications->max('created_at');
    }

    /** @return array<int, int> */
    public function notificationIds(): array
    {
        return $this->notifications->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function actorCount(): int
    {
        $actors = $this->notifications
            ->map(fn (UserNotification $notification) => $notification->actor_id ?: 'guest:'.$notification->id)
            ->unique();

        return $actors->count();
    }

    public function targetUrl(): string
    {
        $latest = $this->notifications->sortByDesc('created_at')->first();

        return $latest?->targetUrl() ?? route('home');
    }

    public function message(): string
    {
        $imageTitle = Str::limit($this->image?->title ?? 'your image', 60);
        $actors = $this->actorLabels();
        $actorText = $this->formatActorList($actors);
        $count = $this->actorCount();

        return match ($this->type) {
            UserNotification::TYPE_IMAGE_COMMENT => $this->pluralAction($actorText, $count, "commented on your image “{$imageTitle}”"),
            UserNotification::TYPE_IMAGE_BOOKMARK => $this->pluralAction($actorText, $count, "bookmarked your image “{$imageTitle}”"),
            UserNotification::TYPE_COMMENT_REPLY => "{$actorText} replied to your comment on “{$imageTitle}”",
            UserNotification::TYPE_COMMENT_MENTION => "{$actorText} mentioned you in a comment on “{$imageTitle}”",
            UserNotification::TYPE_USER_FOLLOW => "{$actorText} started following you",
            UserNotification::TYPE_PROFILE_SHOUT => $this->pluralAction($actorText, $count, 'left a shout on your profile'),
            default => $this->pluralAction($actorText, $count, "liked your image “{$imageTitle}”"),
        };
    }

    /** @return Collection<int, string> */
    private function actorLabels(): Collection
    {
        return $this->notifications
            ->sortByDesc('created_at')
            ->map(fn (UserNotification $notification) => $notification->actorLabel())
            ->unique()
            ->values();
    }

    /** @param  Collection<int, string>  $actors */
    private function formatActorList(Collection $actors): string
    {
        $labels = $actors->values()->all();
        $count = count($labels);

        if ($count === 0) {
            return 'Someone';
        }

        if ($count === 1) {
            return $labels[0];
        }

        if ($count === 2) {
            return "{$labels[0]} and {$labels[1]}";
        }

        $others = $count - 2;

        return "{$labels[0]}, {$labels[1]} and {$others} ".($others === 1 ? 'other' : 'others');
    }

    private function pluralAction(string $actorText, int $count, string $action): string
    {
        if ($count > 1) {
            return "{$actorText} {$action}";
        }

        return "{$actorText} {$action}";
    }

    /** @return Collection<int, User> */
    public function actorUsers(): Collection
    {
        return $this->notifications
            ->sortByDesc('created_at')
            ->map(fn (UserNotification $notification) => $notification->actor)
            ->filter()
            ->unique('id')
            ->values();
    }
}