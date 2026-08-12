<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['admin_id', 'action', 'subject_type', 'subject_id', 'subject_label', 'description'])]
class AdminActivityLog extends Model
{
    public const ACTION_USER_BANNED = 'user.banned';

    public const ACTION_USER_UNBANNED = 'user.unbanned';

    public const ACTION_USER_DELETED = 'user.deleted';

    public const ACTION_IMAGE_UPDATED = 'image.updated';

    public const ACTION_IMAGE_DELETED = 'image.deleted';

    public const ACTION_COMMENT_DELETED = 'comment.deleted';

    public const ACTION_ARTIST_CREATED = 'artist.created';

    public const ACTION_ARTIST_UPDATED = 'artist.updated';

    public const ACTION_ARTIST_DELETED = 'artist.deleted';

    public const ACTION_PARODY_CREATED = 'parody.created';

    public const ACTION_PARODY_UPDATED = 'parody.updated';

    public const ACTION_PARODY_DELETED = 'parody.deleted';

    public const ACTION_CONFIGURATION_UPDATED = 'configuration.updated';

    public const ACTION_CONTACT_MESSAGE_DELETED = 'contact_message.deleted';

    public const ACTION_CONTACT_MESSAGE_REPLIED = 'contact_message.replied';

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public static function record(
        ?User $admin,
        string $action,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?string $subjectLabel = null,
    ): self {
        return self::create([
            'admin_id' => $admin?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_label' => $subjectLabel,
            'description' => $description,
        ]);
    }
}
