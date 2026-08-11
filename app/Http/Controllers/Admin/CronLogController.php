<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CronLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CronLogController extends Controller
{
    public function index(Request $request): View
    {
        $tab = match ($request->input('tab')) {
            'success' => 'success',
            'failed' => 'failed',
            'running' => 'running',
            default => 'all',
        };

        $query = CronLog::query()->latest('started_at')->latest('id');

        if ($tab === 'success') {
            $query->where('status', CronLog::STATUS_SUCCESS);
        } elseif ($tab === 'failed') {
            $query->where('status', CronLog::STATUS_FAILED);
        } elseif ($tab === 'running') {
            $query->where('status', CronLog::STATUS_RUNNING);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('command', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('output', 'like', "%{$search}%")
                    ->orWhere('error', 'like', "%{$search}%");
            });
        }

        if ($command = $request->string('command')->trim()->toString()) {
            $query->where('command', 'like', $command.'%');
        }

        $logs = $query->paginate(40)->withQueryString();

        $counts = [
            'all' => CronLog::count(),
            'success' => CronLog::where('status', CronLog::STATUS_SUCCESS)->count(),
            'failed' => CronLog::where('status', CronLog::STATUS_FAILED)->count(),
            'running' => CronLog::where('status', CronLog::STATUS_RUNNING)->count(),
        ];

        $commands = CronLog::query()
            ->select('command')
            ->distinct()
            ->orderBy('command')
            ->pluck('command');

        return view('admin.cron.index', compact('logs', 'tab', 'counts', 'commands'));
    }
}
