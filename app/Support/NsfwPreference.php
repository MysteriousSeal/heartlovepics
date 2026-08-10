<?php

namespace App\Support;

use Illuminate\Http\Request;

class NsfwPreference
{
    public const COOKIE_NAME = 'show_nsfw';

    public const AGE_CONFIRMED_COOKIE_NAME = 'nsfw_age_confirmed';

    public static function isEnabled(Request $request): bool
    {
        return $request->cookie(self::COOKIE_NAME) === '1'
            && self::hasAgeConfirmed($request);
    }

    public static function hasAgeConfirmed(Request $request): bool
    {
        return $request->cookie(self::AGE_CONFIRMED_COOKIE_NAME) === '1';
    }
}