<?php

namespace App\Models;

use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'body'])]
class Journal extends Model
{
    public const TITLE_MAX_LENGTH = 150;

    public const BODY_MAX_LENGTH = 10000;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBodyHtmlAttribute(): ?string
    {
        return HtmlSanitizer::clean($this->body);
    }

    public function getBodyPlainAttribute(): string
    {
        return HtmlSanitizer::toPlainText($this->body);
    }

    public function canBeManagedBy(?User $user): bool
    {
        return $user !== null && $user->id === $this->user_id;
    }
}
