<?php

namespace App\Services;

use App\Models\SiteStatistic;

class VisitCounterService
{
    private const ROW_ID = 1;

    public function record(): void
    {
        SiteStatistic::query()
            ->where('id', self::ROW_ID)
            ->increment('visits');
    }

    public function total(): int
    {
        return (int) SiteStatistic::query()
            ->where('id', self::ROW_ID)
            ->value('visits');
    }
}