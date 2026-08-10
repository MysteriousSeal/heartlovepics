<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }

    public static function normalizeName(string $name): string
    {
        return Str::of($name)
            ->trim()
            ->lower()
            ->replaceMatches('/\s+/', ' ')
            ->toString();
    }
}