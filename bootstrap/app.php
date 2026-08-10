<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'admin.api' => \App\Http\Middleware\EnsureAdminApiToken::class,
        ]);

        $middleware->encryptCookies(except: [
            'show_nsfw',
            'nsfw_age_confirmed',
            'theme',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\RecordSiteVisit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Also defer to the request's own Accept/X-Requested-With signals —
        // otherwise an AJAX call to a non-api web route (e.g. the upload form)
        // gets a full HTML error page instead of JSON when validation fails,
        // and JS trying to JSON.parse it blows up.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
