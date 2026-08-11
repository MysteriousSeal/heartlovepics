<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Remove empty duplicate cron_logs rows created while LogScheduledTask was
 * registered twice (manual Event::listen + Laravel event discovery).
 *
 * Keeps the row that has output/error when two success rows share the same
 * command + started_at + runtime; otherwise keeps the lowest id.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('cron_logs')) {
            return;
        }

        $groups = DB::table('cron_logs')
            ->select('command', 'started_at', 'runtime_ms', 'status')
            ->groupBy('command', 'started_at', 'runtime_ms', 'status')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $deleted = 0;

        foreach ($groups as $group) {
            $rows = DB::table('cron_logs')
                ->where('command', $group->command)
                ->where('status', $group->status)
                ->when(
                    $group->started_at === null,
                    fn ($q) => $q->whereNull('started_at'),
                    fn ($q) => $q->where('started_at', $group->started_at),
                )
                ->when(
                    $group->runtime_ms === null,
                    fn ($q) => $q->whereNull('runtime_ms'),
                    fn ($q) => $q->where('runtime_ms', $group->runtime_ms),
                )
                ->orderBy('id')
                ->get(['id', 'output', 'error']);

            if ($rows->count() < 2) {
                continue;
            }

            // Prefer the row that recorded command output or an error.
            $keep = $rows->first(function ($row) {
                return filled($row->output) || filled($row->error);
            }) ?? $rows->first();

            $idsToDelete = $rows
                ->pluck('id')
                ->reject(fn ($id) => (int) $id === (int) $keep->id)
                ->values()
                ->all();

            if ($idsToDelete !== []) {
                $deleted += DB::table('cron_logs')->whereIn('id', $idsToDelete)->delete();
            }
        }

        // Also drop stale "running" shells left behind by the double-listener bug.
        $deleted += DB::table('cron_logs')
            ->where('status', 'running')
            ->where('started_at', '<', now()->subHours(1))
            ->delete();

        // No-op if nothing matched; migration still records as applied.
        unset($deleted);
    }

    public function down(): void
    {
        // Irreversible data cleanup.
    }
};
