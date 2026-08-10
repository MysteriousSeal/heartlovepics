<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['image_id'])]
class ImageViewEvent extends Model
{
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }
}