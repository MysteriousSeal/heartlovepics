<?php

namespace App\Http\Middleware;

use App\Services\VisitCounterService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordSiteVisit
{
    public function __construct(private VisitCounterService $visitCounter) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldRecord($request)) {
            $this->visitCounter->record($request);
        }

        return $next($request);
    }

    private function shouldRecord(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        if ($request->is('admin', 'admin/*', 'up', 'sitemap.xml', 'robots.txt')) {
            return false;
        }

        return true;
    }
}