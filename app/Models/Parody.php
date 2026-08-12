<?php

namespace App\Models;

use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'cover_path', 'description'])]
class Parody extends Model
{
    /**
     * Matched to images by name, not a foreign key — parody on Image stays
     * free text so posts can credit a parody before a matching Parody
     * record exists.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'parody', 'name');
    }

    public function hasCover(): bool
    {
        return (bool) $this->cover_path;
    }

    public function hasDescription(): bool
    {
        return ! HtmlSanitizer::isEmpty($this->description);
    }

    public function getDescriptionHtmlAttribute(): ?string
    {
        return HtmlSanitizer::clean($this->description);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path ? '/storage/'.$this->cover_path : null;
    }

    public function getCoverInitialsAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->name ?? '', 0, 2));
    }

    public function getCoverColorAttribute(): string
    {
        $hue = abs(crc32($this->name ?? 'parody')) % 360;

        return "hsl({$hue}, 35%, 55%)";
    }
}
