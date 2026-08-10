<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configured = config('services.admin_api.token');
        $provided = $request->bearerToken();

        if (! $configured || ! $provided || ! hash_equals($configured, $provided)) {
            abort(401, 'Invalid or missing admin API token.');
        }

        return $next($request);
    }
}
