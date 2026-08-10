<?php

namespace App\Console\Commands;

use App\Models\ImageViewEvent;
use Illuminate\Console\Command;

class PruneImageViewEvents extends Command
{
    protected $signature = 'view-events:prune {--days=30 : Delete events older than this many days}';

    protected $description = 'Delete image view events older than the retention period';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $deleted = ImageViewEvent::query()
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deleted} image view event(s) older than {$days} days.");

        return self::SUCCESS;
    }
}