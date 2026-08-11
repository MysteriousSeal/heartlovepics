<?php

namespace App\Console\Commands;

use App\Services\CronLogService;
use Illuminate\Console\Command;

class PruneCronLogs extends Command
{
    protected $signature = 'cron-logs:prune {--days=7 : Delete cron logs older than this many days}';

    protected $description = 'Delete old scheduled-task log rows';

    public function handle(CronLogService $cronLogs): int
    {
        $days = max(1, (int) $this->option('days'));
        $deleted = $cronLogs->pruneOlderThanDays($days);

        $this->info("Deleted {$deleted} cron log(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
