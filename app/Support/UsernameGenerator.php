<?php

namespace App\Support;

use App\Models\User;

class UsernameGenerator
{
    private const MAX_ATTEMPTS = 15;

    /** Generates a random "adjective_name42"-style username that isn't already taken. */
    public static function generate(): string
    {
        $adjectives = self::words('username-adjectives.txt');
        $names = self::words('username-names.txt');

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $username = $adjectives[array_rand($adjectives)]
                .'_'.$names[array_rand($names)]
                .random_int(10, 99);

            if (! User::where('username', $username)->exists()) {
                return $username;
            }
        }

        // Extremely unlikely fallback: widen the number range so collisions become negligible.
        return $adjectives[array_rand($adjectives)].'_'.$names[array_rand($names)].random_int(100, 9999);
    }

    /** @return list<string> */
    private static function words(string $filename): array
    {
        $lines = file(base_path('data/'.$filename), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return $lines !== false ? array_values($lines) : [];
    }
}
