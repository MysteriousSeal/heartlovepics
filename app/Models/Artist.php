<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'deviantart_url', 'furaffinity_url', 'patreon_url'])]
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
}
