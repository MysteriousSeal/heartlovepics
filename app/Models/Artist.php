<?php

namespace App\Models;

use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'avatar_path', 'description', 'deviantart_url', 'furaffinity_url', 'patreon_url'])]
class Artist extends Model
{
    /**
     * Matched to images by name, not a foreign key — artist_name on Image
     * stays free text so posts can credit an artist before a matching Artist
     * record exists.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'artist_name', 'name');
    }

    public function hasLinks(): bool
    {
        return filled($this->deviantart_url) || filled($this->furaffinity_url) || filled($this->patreon_url);
    }

    public function hasAvatar(): bool
    {
        return (bool) $this->avatar_path;
    }

    public function hasDescription(): bool
    {
        return ! HtmlSanitizer::isEmpty($this->description);
    }

    public function getDescriptionHtmlAttribute(): ?string
    {
        return HtmlSanitizer::clean($this->description);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? '/storage/'.$this->avatar_path : null;
    }

    public function getAvatarInitialsAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->name ?? '', 0, 2));
    }

    public function getAvatarColorAttribute(): string
    {
        $hue = abs(crc32($this->name ?? 'artist')) % 360;

        return "hsl({$hue}, 35%, 55%)";
    }
}
