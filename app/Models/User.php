<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\HtmlSanitizer;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'avatar_path',
    'banner_path',
    'bio',
    'is_admin',
    'is_banned',
    'terms_accepted_at',
    'notify_likes',
    'notify_comments',
    'notify_bookmarks',
    'notify_replies',
    'notify_mentions',
    'notify_follows',
    'notify_shouts',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function imageLikes(): HasMany
    {
        return $this->hasMany(ImageLike::class);
    }

    public function imageBookmarks(): HasMany
    {
        return $this->hasMany(ImageBookmark::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function receivedImageLikes(): HasManyThrough
    {
        return $this->hasManyThrough(ImageLike::class, Image::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** Shouts left on this user's profile wall. */
    public function shouts(): HasMany
    {
        return $this->hasMany(Shout::class);
    }

    /** Shouts this user has posted on other profiles. */
    public function shoutsPosted(): HasMany
    {
        return $this->hasMany(Shout::class, 'author_id');
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    public function canPost(): bool
    {
        return ! $this->is_banned;
    }

    public function hasAvatar(): bool
    {
        return (bool) $this->avatar_path;
    }

    public function getBioHtmlAttribute(): ?string
    {
        return HtmlSanitizer::clean($this->bio);
    }

    public function getBioPlainAttribute(): string
    {
        return HtmlSanitizer::toPlainText($this->bio);
    }

    public function hasBio(): bool
    {
        return ! HtmlSanitizer::isEmpty($this->bio);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path) {
            return '/storage/'.$this->avatar_path;
        }

        return null;
    }

    public function hasBanner(): bool
    {
        return (bool) $this->banner_path;
    }

    public function getBannerUrlAttribute(): ?string
    {
        if ($this->banner_path) {
            return '/storage/'.$this->banner_path;
        }

        return null;
    }

    public function getAvatarInitialsAttribute(): string
    {
        $username = $this->username ?? '';

        return mb_strtoupper(mb_substr($username, 0, 2));
    }

    public function getAvatarColorAttribute(): string
    {
        $hue = abs(crc32($this->username ?? 'user')) % 360;

        return "hsl({$hue}, 35%, 55%)";
    }

    public function getOgImageUrlAttribute(): ?string
    {
        if ($this->avatar_path) {
            return url('/storage/'.$this->avatar_path);
        }

        $firstImage = $this->relationLoaded('images')
            ? $this->images->where('is_published', true)->where('is_private', false)->sortByDesc('created_at')->first()
            : $this->images()->where('is_published', true)->where('is_private', false)->latest()->first();

        return $firstImage?->direct_url;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'notify_likes' => 'boolean',
            'notify_comments' => 'boolean',
            'notify_bookmarks' => 'boolean',
            'notify_replies' => 'boolean',
            'notify_mentions' => 'boolean',
            'notify_follows' => 'boolean',
            'notify_shouts' => 'boolean',
        ];
    }
}
