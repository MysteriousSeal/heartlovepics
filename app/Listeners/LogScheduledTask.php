<?php

namespace App\Listeners;

use App\Services\CronLogService;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskStarting;

class LogScheduledTask
{
    public function __construct(private CronLogService $cronLogs) {}

    public function handleStarting(ScheduledTaskStarting $event): void
    {
        $this->cronLogs->start($event->task);
    }

    public function handleFinished(ScheduledTaskFinished $event): void
    {
        $this->cronLogs->finish($event->task, $event->runtime);
    }

    public function handleFailed(ScheduledTaskFailed $event): void
    {
        $this->cronLogs->fail($event->task, $event->exception);
    }
}
