<?php

namespace App\Services;

use App\Models\SiteStatistic;
use App\Models\SiteVisit;
use Illuminate\Http\Request;

class VisitCounterService
{
    private const ROW_ID = 1;

    public function record(Request $request): void
    {
        SiteStatistic::query()
            ->where('id', self::ROW_ID)
            ->increment('visits');

        SiteVisit::create([
            'path' => mb_substr('/'.ltrim($request->path(), '/'), 0, 2048),
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 512) ?: null,
            'referrer' => mb_substr((string) $request->header('referer'), 0, 2048) ?: null,
        ]);
    }

    public function total(): int
    {
        return (int) SiteStatistic::query()
            ->where('id', self::ROW_ID)
            ->value('visits');
    }
}