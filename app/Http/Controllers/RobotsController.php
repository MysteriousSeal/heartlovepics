<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $sitemap = url(route('sitemap', absolute: false));

        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            '',
            "Sitemap: {$sitemap}",
            '',
        ]);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}