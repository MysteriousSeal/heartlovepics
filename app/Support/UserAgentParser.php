<?php

namespace App\Support;

/**
 * Lightweight UA parsing for admin analytics — browser, device, OS, bot flag.
 * Not a full client database; good enough for a scannable visits table.
 */
class UserAgentParser
{
    /** @var list<string> */
    private const BOT_NEEDLES = [
        'bot',
        'crawl',
        'spider',
        'slurp',
        'mediapartners',
        'facebookexternalhit',
        'facebot',
        'ia_archiver',
        'bingpreview',
        'yandex',
        'baidu',
        'duckduckbot',
        'applebot',
        'semrush',
        'ahrefs',
        'mj12bot',
        'dotbot',
        'petalbot',
        'bytespider',
        'gptbot',
        'claudebot',
        'anthropic',
        'chatgpt',
        'python-requests',
        'curl/',
        'wget/',
        'httpclient',
        'libwww',
        'scrapy',
        'headlesschrome',
        'phantomjs',
        'prerender',
        'uptime',
        'pingdom',
        'statuscake',
        'gtmetrix',
        'pagespeed',
    ];

    /**
     * @return array{browser: string, device: string, os: string, is_bot: bool}
     */
    public static function parse(?string $userAgent): array
    {
        $ua = trim((string) $userAgent);

        if ($ua === '') {
            return [
                'browser' => 'Unknown',
                'device' => 'Unknown',
                'os' => 'Unknown',
                'is_bot' => false,
            ];
        }

        $lower = mb_strtolower($ua);
        $isBot = self::detectBot($lower, $ua);

        return [
            'browser' => $isBot ? self::detectBotName($ua, $lower) : self::detectBrowser($ua),
            'device' => $isBot ? 'Bot' : self::detectDevice($ua, $lower),
            'os' => $isBot ? '—' : self::detectOs($ua, $lower),
            'is_bot' => $isBot,
        ];
    }

    public static function browser(?string $userAgent): string
    {
        return self::parse($userAgent)['browser'];
    }

    public static function device(?string $userAgent): string
    {
        return self::parse($userAgent)['device'];
    }

    public static function os(?string $userAgent): string
    {
        return self::parse($userAgent)['os'];
    }

    public static function isBot(?string $userAgent): bool
    {
        return self::parse($userAgent)['is_bot'];
    }

    private static function detectBot(string $lower, string $ua): bool
    {
        foreach (self::BOT_NEEDLES as $needle) {
            if (str_contains($lower, $needle)) {
                return true;
            }
        }

        // Very short / non-browser agents are usually scripts.
        if (strlen($ua) < 30 && ! str_contains($lower, 'mozilla')) {
            return true;
        }

        return false;
    }

    private static function detectBotName(string $ua, string $lower): string
    {
        $named = [
            'Googlebot' => 'googlebot',
            'Bingbot' => 'bingbot',
            'DuckDuckBot' => 'duckduckbot',
            'Yandex' => 'yandex',
            'Baidu' => 'baidu',
            'Applebot' => 'applebot',
            'Facebook' => 'facebookexternalhit',
            'Twitter' => 'twitterbot',
            'LinkedIn' => 'linkedinbot',
            'Discord' => 'discordbot',
            'Slack' => 'slackbot',
            'Telegram' => 'telegrambot',
            'Ahrefs' => 'ahrefs',
            'Semrush' => 'semrush',
            'PetalBot' => 'petalbot',
            'Bytespider' => 'bytespider',
            'GPTBot' => 'gptbot',
            'ClaudeBot' => 'claudebot',
            'curl' => 'curl/',
            'Wget' => 'wget/',
            'Python' => 'python-requests',
        ];

        foreach ($named as $label => $needle) {
            if (str_contains($lower, $needle)) {
                return $label;
            }
        }

        if (preg_match('/^([A-Za-z0-9._-]{2,40})/', $ua, $matches) === 1) {
            $token = $matches[1];

            if (! in_array(mb_strtolower($token), ['mozilla', 'opera'], true)) {
                return $token;
            }
        }

        return 'Bot';
    }

    private static function detectBrowser(string $ua): string
    {
        // Order matters: Edge/Opera/Samsung include Chrome; Chrome includes Safari.
        $browsers = [
            'Edge' => ['Edg/', 'EdgA/', 'EdgiOS/'],
            'Opera' => ['OPR/', 'Opera'],
            'Samsung Internet' => ['SamsungBrowser/'],
            'Vivaldi' => ['Vivaldi/'],
            'Brave' => ['Brave/'],
            'Firefox' => ['Firefox/', 'FxiOS/'],
            'Chrome' => ['Chrome/', 'CriOS/'],
            'Safari' => ['Safari/'],
            'Internet Explorer' => ['MSIE ', 'Trident/'],
        ];

        foreach ($browsers as $name => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($ua, $needle)) {
                    if ($name === 'Safari' && ! str_contains($ua, 'Version/')) {
                        continue;
                    }

                    return $name;
                }
            }
        }

        return 'Unknown';
    }

    private static function detectDevice(string $ua, string $lower): string
    {
        if (str_contains($lower, 'ipad')
            || str_contains($lower, 'tablet')
            || (str_contains($lower, 'android') && ! str_contains($lower, 'mobile'))) {
            return 'Tablet';
        }

        if (str_contains($lower, 'mobi')
            || str_contains($lower, 'iphone')
            || str_contains($lower, 'ipod')
            || str_contains($lower, 'android')
            || str_contains($lower, 'windows phone')
            || str_contains($ua, 'IEMobile')) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private static function detectOs(string $ua, string $lower): string
    {
        if (str_contains($lower, 'android')) {
            return 'Android';
        }

        if (str_contains($lower, 'iphone') || str_contains($lower, 'ipad') || str_contains($lower, 'ipod')) {
            return 'iOS';
        }

        if (str_contains($lower, 'mac os x') || str_contains($lower, 'macintosh')) {
            return 'macOS';
        }

        if (str_contains($lower, 'windows')) {
            return 'Windows';
        }

        if (str_contains($lower, 'cros')) {
            return 'ChromeOS';
        }

        if (str_contains($lower, 'linux')) {
            return 'Linux';
        }

        if (str_contains($lower, 'cros') || str_contains($lower, 'chromium os')) {
            return 'ChromeOS';
        }

        return 'Unknown';
    }
}
