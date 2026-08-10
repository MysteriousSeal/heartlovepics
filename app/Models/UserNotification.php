<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'type', 'actor_id', 'image_id', 'comment_id', 'read_at'])]
class UserNotification extends Model
{
    public const TYPE_IMAGE_LIKE = 'image_like';

    public const TYPE_IMAGE_COMMENT = 'image_comment';

    public const TYPE_IMAGE_BOOKMARK = 'image_bookmark';

    public const TYPE_COMMENT_REPLY = 'comment_reply';

    public const TYPE_COMMENT_MENTION = 'comment_mention';

    public const TYPE_USER_FOLLOW = 'user_follow';

    public const TYPE_PROFILE_SHOUT = 'profile_shout';

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function actorLabel(): string
    {
        if ($this->actor) {
            return '@'.$this->actor->username;
        }

        if ($this->comment && in_array($this->type, [self::TYPE_COMMENT_REPLY, self::TYPE_IMAGE_COMMENT, self::TYPE_COMMENT_MENTION], true)) {
            return $this->comment->author_name;
        }

        return 'Someone';
    }

    public function message(): string
    {
        $actor = $this->actorLabel();
        $imageTitle = Str::limit($this->image?->title ?? 'your image', 60);

        return match ($this->type) {
            self::TYPE_COMMENT_REPLY => "{$actor} replied to your comment on “{$imageTitle}”",
            self::TYPE_IMAGE_COMMENT => "{$actor} commented on your image “{$imageTitle}”",
            self::TYPE_IMAGE_BOOKMARK => "{$actor} bookmarked your image “{$imageTitle}”",
            self::TYPE_COMMENT_MENTION => "{$actor} mentioned you in a comment on “{$imageTitle}”",
            self::TYPE_USER_FOLLOW => "{$actor} started following you",
            self::TYPE_PROFILE_SHOUT => "{$actor} left a shout on your profile",
            default => "{$actor} liked your image “{$imageTitle}”",
        };
    }

    public function targetUrl(): string
    {
        if ($this->type === self::TYPE_USER_FOLLOW) {
            return $this->actor
                ? route('users.show', $this->actor)
                : route('home');
        }

        if ($this->type === self::TYPE_PROFILE_SHOUT) {
            return $this->user
                ? route('users.show', $this->user).'#shouts'
                : route('home');
        }

        $slug = $this->image?->slug;

        if (! $slug) {
            return route('home');
        }

        $url = route('images.show', $slug);

        if ($this->comment_id && in_array($this->type, [self::TYPE_COMMENT_REPLY, self::TYPE_IMAGE_COMMENT, self::TYPE_COMMENT_MENTION], true)) {
            return $url.'#comment-'.$this->comment_id;
        }

        return $url;
    }
}