<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'author_id', 'body'])]
class Shout extends Model
{
    public const MAX_LENGTH = 500;

    /** The profile this shout was left on. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Who posted the shout. */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $user->is($this->user) || $user->is($this->author);
    }
}
