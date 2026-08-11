<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public const SHOW_GALLERY_AUTHORS = 'show_gallery_authors';

    public const SHOW_GALLERY_ARTISTS = 'show_gallery_artists';

    protected $fillable = [
        'key',
        'value',
    ];
}
