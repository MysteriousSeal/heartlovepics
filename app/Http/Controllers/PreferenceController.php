<?php

namespace App\Http\Controllers;

use App\Support\NsfwPreference;
use App\Support\ThemePreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Cookie;

class PreferenceController extends Controller
{
    private const COOKIE_MINUTES = 60 * 24 * 365 * 5;

    public function updateNsfw(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => ['required', 'boolean'],
            'age_confirmed' => ['sometimes', 'boolean'],
        ]);

        $enabled = $request->boolean('enabled');
        $ageConfirmed = NsfwPreference::hasAgeConfirmed($request) || $request->boolean('age_confirmed');

        if ($enabled && ! $ageConfirmed) {
            throw ValidationException::withMessages([
                'age_confirmed' => 'Age confirmation is required to show NSFW content.',
            ]);
        }

        $response = response()->json(['ok' => true])
            ->cookie($this->preferenceCookie(NsfwPreference::COOKIE_NAME, $enabled ? '1' : '0'));

        if ($enabled && $ageConfirmed) {
            $response->cookie($this->preferenceCookie(NsfwPreference::AGE_CONFIRMED_COOKIE_NAME, '1'));
        }

        return $response;
    }

    public function updateTheme(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        return response()->json(['ok' => true])
            ->cookie($this->preferenceCookie(ThemePreference::COOKIE_NAME, $request->input('theme')));
    }

    private function preferenceCookie(string $name, string $value): Cookie
    {
        return cookie(
            name: $name,
            value: $value,
            minutes: self::COOKIE_MINUTES,
            path: '/',
            secure: $this->cookieShouldBeSecure(),
            httpOnly: false,
            raw: false,
            sameSite: 'lax',
        );
    }

    private function cookieShouldBeSecure(): bool
    {
        $configured = config('session.secure');

        if ($configured !== null) {
            return (bool) $configured;
        }

        return request()->isSecure();
    }
}