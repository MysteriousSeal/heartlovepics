<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'image_id',
    'file_path',
    'thumbnail_path',
    'width',
    'height',
    'position',
])]
class ImageFile extends Model
{
    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'position' => 'integer',
        ];
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function getUrlAttribute(): string
    {
        return '/storage/'.$this->file_path;
    }

    public function getThumbnailUrlAttribute(): string
    {
        return '/storage/'.($this->thumbnail_path ?: $this->file_path);
    }

    public function getDirectUrlAttribute(): string
    {
        return url($this->url);
    }
}
