<?php

namespace App\Services;

use App\Models\CronLog;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

/**
 * Persist scheduled-task runs for the admin Cron log.
 *
 * Scheduled commands often run in a child PHP process, so open-run IDs are
 * also stored in cache for commands to attach output from that child.
 */
class CronLogService
{
    /** @var array<string, int> task key => cron_logs.id */
    private array $openByKey = [];

    private ?int $currentLogId = null;

    public function start(Event $task): CronLog
    {
        $key = $this->taskKey($task);

        $log = CronLog::query()->create([
            'command' => $this->commandLabel($task),
            'description' => $task->description ?: null,
            'expression' => $task->expression ?? null,
            'status' => CronLog::STATUS_RUNNING,
            'started_at' => now(),
        ]);

        $this->openByKey[$key] = $log->id;
        $this->currentLogId = $log->id;
        Cache::put($this->cacheKey($key), $log->id, now()->addMinutes(30));
        Cache::put($this->cacheKeyForCommand($log->command), $log->id, now()->addMinutes(30));

        return $log;
    }

    public function finish(Event $task, float $runtimeSeconds): void
    {
        $log = $this->resolveOpenLog($task);

        if (! $log) {
            return;
        }

        $log->forceFill([
            'status' => CronLog::STATUS_SUCCESS,
            'exit_code' => 0,
            'runtime_ms' => round($runtimeSeconds * 1000, 2),
            'finished_at' => now(),
        ])->save();

        $this->forget($task, $log);
    }

    public function fail(Event $task, Throwable $exception): void
    {
        $log = $this->resolveOpenLog($task);

        if (! $log) {
            $log = CronLog::query()->create([
                'command' => $this->commandLabel($task),
                'description' => $task->description ?: null,
                'expression' => $task->expression ?? null,
                'status' => CronLog::STATUS_FAILED,
                'started_at' => now(),
                'finished_at' => now(),
            ]);
        }

        $log->forceFill([
            'status' => CronLog::STATUS_FAILED,
            'exit_code' => 1,
            'error' => Str::limit($exception->getMessage(), 2000),
            'finished_at' => now(),
        ])->save();

        $this->forget($task, $log);
    }

    /**
     * Append a line of output to the currently running cron log (if any).
     * Works from child artisan processes via cache lookup.
     */
    public function appendOutput(string $line, ?string $commandHint = null): void
    {
        $log = $this->currentRunningLog($commandHint);

        if (! $log) {
            return;
        }

        $existing = $log->output ? rtrim((string) $log->output)."\n" : '';
        $log->forceFill([
            'output' => Str::limit($existing.$line, 5000, '…'),
        ])->save();
    }

    public function pruneOlderThanDays(int $days = 7): int
    {
        $days = max(1, $days);

        return CronLog::query()
            ->where(function ($query) use ($days) {
                $query->where('started_at', '<', now()->subDays($days))
                    ->orWhere(function ($inner) use ($days) {
                        $inner->whereNull('started_at')
                            ->where('created_at', '<', now()->subDays($days));
                    });
            })
            ->delete();
    }

    private function currentRunningLog(?string $commandHint = null): ?CronLog
    {
        if ($this->currentLogId) {
            $log = CronLog::query()->find($this->currentLogId);
            if ($log && $log->isRunning()) {
                return $log;
            }
        }

        if ($commandHint) {
            $cachedId = Cache::get($this->cacheKeyForCommand($commandHint));
            if ($cachedId) {
                $log = CronLog::query()->find($cachedId);
                if ($log && $log->isRunning()) {
                    return $log;
                }
            }

            return CronLog::query()
                ->where('status', CronLog::STATUS_RUNNING)
                ->where('command', 'like', '%'.$commandHint.'%')
                ->latest('id')
                ->first();
        }

        return CronLog::query()
            ->where('status', CronLog::STATUS_RUNNING)
            ->latest('id')
            ->first();
    }

    private function resolveOpenLog(Event $task): ?CronLog
    {
        $key = $this->taskKey($task);
        $id = $this->openByKey[$key] ?? Cache::get($this->cacheKey($key)) ?? $this->currentLogId;

        if ($id) {
            $log = CronLog::query()->find($id);
            if ($log && $log->isRunning()) {
                return $log;
            }
        }

        // Fallback: newest running row for this command.
        return CronLog::query()
            ->where('status', CronLog::STATUS_RUNNING)
            ->where('command', $this->commandLabel($task))
            ->latest('id')
            ->first();
    }

    private function forget(Event $task, CronLog $log): void
    {
        $key = $this->taskKey($task);
        unset($this->openByKey[$key]);
        Cache::forget($this->cacheKey($key));
        Cache::forget($this->cacheKeyForCommand($log->command));

        if ($this->currentLogId === $log->id) {
            $this->currentLogId = null;
        }
    }

    private function taskKey(Event $task): string
    {
        return md5(($task->command ?? '').'|'.($task->expression ?? '').'|'.($task->description ?? ''));
    }

    private function cacheKey(string $taskKey): string
    {
        return 'cron_log:open:'.$taskKey;
    }

    private function cacheKeyForCommand(string $command): string
    {
        // Normalize to artisan command name when possible.
        if (preg_match('/\b([a-z0-9]+:[a-z0-9:-]+)\b/i', $command, $matches)) {
            $command = $matches[1];
        }

        return 'cron_log:open_cmd:'.md5($command);
    }

    private function commandLabel(Event $task): string
    {
        $command = (string) ($task->command ?? $task->getSummaryForDisplay());

        // Strip the php/artisan binary prefix for readability.
        $command = preg_replace('/^\'[^\']+\'\s+/', '', $command) ?? $command;
        $command = preg_replace('/^"[^"]+"\s+/', '', $command) ?? $command;
        $command = preg_replace('/\s*2?\s*(?:>>?|&>)\s*\S+$/', '', $command) ?? $command;

        return Str::limit(trim($command), 255, '');
    }
}
