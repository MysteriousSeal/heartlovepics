<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteStatistic extends Model
{
    protected $fillable = [
        'visits',
    ];

    protected function casts(): array
    {
        return [
            'visits' => 'integer',
        ];
    }
}