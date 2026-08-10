<?php

namespace App\Support;

use Illuminate\Http\Request;

class ThemePreference
{
    public const COOKIE_NAME = 'theme';

    public static function resolve(Request $request): string
    {
        $theme = $request->cookie(self::COOKIE_NAME);

        return in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
    }
}