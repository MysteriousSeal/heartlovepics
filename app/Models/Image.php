<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'title',
    'slug',
    'description',
    'alt_text',
    'file_path',
    'thumbnail_path',
    'width',
    'height',
    'is_published',
    'is_private',
    'is_nsfw',
    'is_nsfw_locked',
    'content_warning',
    'artist_name',
])]
class Image extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_private' => 'boolean',
            'is_nsfw' => 'boolean',
            'is_nsfw_locked' => 'boolean',
            'views' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /**
     * Published, non-private images — safe to expose in feeds, search, tags,
     * and any other listing. Private images are excluded unconditionally,
     * even for their own owner: the only place a private image is visible is
     * the owner's profile page (handled separately via isVisibleTo()).
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('is_private', false);
    }

    public function isVisibleTo(?User $viewer): bool
    {
        if (! $this->is_published) {
            return $viewer !== null && $viewer->id === $this->user_id;
        }

        if (! $this->is_private) {
            return true;
        }

        return $viewer !== null && $viewer->id === $this->user_id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Matched by name, not a foreign key — see Artist::images(). */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_name', 'name');
    }

    public function getDescriptionHtmlAttribute(): ?string
    {
        return HtmlSanitizer::clean($this->description);
    }

    public function getDescriptionPlainAttribute(): string
    {
        return HtmlSanitizer::toPlainText($this->description);
    }

    public function hasDescription(): bool
    {
        return ! HtmlSanitizer::isEmpty($this->description);
    }

    public function hasArtistCredit(): bool
    {
        return filled($this->artist_name);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ImageLike::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(ImageBookmark::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_image')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function additionalImages(): HasMany
    {
        return $this->hasMany(ImageFile::class)->orderBy('position');
    }

    public function hasAdditionalImages(): bool
    {
        return $this->relationLoaded('additionalImages')
            ? $this->additionalImages->isNotEmpty()
            : $this->additionalImages()->exists();
    }

    public function getUrlAttribute(): string
    {
        return '/storage/'.$this->file_path;
    }

    public function getThumbnailUrlAttribute(): string
    {
        return '/storage/'.($this->thumbnail_path ?: $this->file_path);
    }

    public function getThumbnailDirectUrlAttribute(): string
    {
        return url($this->thumbnail_url);
    }

    public function getDirectUrlAttribute(): string
    {
        return url($this->url);
    }

    public function resolution(): ?string
    {
        $this->syncDimensions();

        if (! $this->width || ! $this->height) {
            return null;
        }

        return $this->width.' × '.$this->height;
    }

    public function fileExtension(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);

        return $extension !== '' ? strtoupper($extension) : null;
    }

    public function fileSize(): ?int
    {
        if (! $this->file_path || ! Storage::disk('public')->exists($this->file_path)) {
            return null;
        }

        return Storage::disk('public')->size($this->file_path);
    }

    public function formattedFileSize(): ?string
    {
        $bytes = $this->fileSize();

        return $bytes !== null ? Number::fileSize($bytes, precision: 1) : null;
    }

    public function fileMeta(): ?string
    {
        $parts = array_filter([
            $this->resolution(),
            $this->fileExtension(),
            $this->formattedFileSize(),
        ]);

        return $parts !== [] ? implode(' · ', $parts) : null;
    }

    public function syncDimensions(): void
    {
        if ($this->width && $this->height) {
            return;
        }

        [$width, $height] = self::readDimensionsFromPath($this->file_path);

        if ($width && $height) {
            $this->forceFill([
                'width' => $width,
                'height' => $height,
            ])->save();
        }
    }

    /** @return array{0: ?int, 1: ?int} */
    public static function readDimensionsFromPath(string $filePath): array
    {
        $absolutePath = Storage::disk('public')->path($filePath);

        if (! is_file($absolutePath)) {
            return [null, null];
        }

        $size = @getimagesize($absolutePath);

        if ($size === false) {
            return [null, null];
        }

        return [(int) $size[0], (int) $size[1]];
    }

    /**
     * Titles run to 255 characters but the slug column is only 255 wide, so the
     * base is capped to leave room for the "-1", "-2"… uniqueness suffix.
     */
    private const SLUG_MAX_LENGTH = 200;

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'image';

        if (mb_strlen($baseSlug) > self::SLUG_MAX_LENGTH) {
            $baseSlug = rtrim(mb_substr($baseSlug, 0, self::SLUG_MAX_LENGTH), '-') ?: 'image';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::withTrashed()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}