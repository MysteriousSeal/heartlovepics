<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['image_id', 'user_id', 'parent_id', 'author_name', 'body'])]
class Comment extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->latest();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function depth(): int
    {
        $depth = 0;
        $parentId = $this->parent_id;

        while ($parentId) {
            $depth++;
            $parentId = static::query()->whereKey($parentId)->value('parent_id');
        }

        return $depth;
    }

    public const MAX_REPLY_DEPTH = 3;
}