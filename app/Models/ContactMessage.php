<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'username',
    'email',
    'subject',
    'message',
    'ip_address',
    'is_archived',
    'archived_at',
    'replied_at',
    'reply_body',
    'reply_subject',
])]
class ContactMessage extends Model
{
    public const SUBJECT_MAX = 80;

    public const MESSAGE_MAX = 1000;

    public const USERNAME_MAX = 30;

    public const EMAIL_MAX = 255;

    public const REPLY_SUBJECT_MAX = 120;

    public const REPLY_BODY_MAX = 5000;

    protected function casts(): array
    {
        return [
            'is_archived' => 'boolean',
            'archived_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
