@extends('layouts.admin')

@section('title', 'Cron')

@section('content')
    <div class="admin-list-page">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Scheduler</p>
            <h2 class="admin-list-title">Cron</h2>
            <p class="admin-list-lede">
                Every scheduled task run — success, failure, timing, and command output.
            </p>
            <div class="admin-list-meta">
                <span class="admin-list-chip">{{ number_format($counts['all']) }} total</span>
                <span class="admin-list-chip">{{ number_format($counts['success']) }} success</span>
                <span class="admin-list-chip">{{ number_format($counts['failed']) }} failed</span>
                @if ($counts['running'] > 0)
                    <span class="admin-list-chip is-filtered">{{ number_format($counts['running']) }} running</span>
                @endif
                @if (request()->hasAny(['search', 'command']))
                    <span class="admin-list-chip is-filtered">Filtered</span>
                @endif
            </div>
        </header>

        <nav class="admin-list-tabs" aria-label="Cron status">
            @foreach ([
                'all' => 'All',
                'success' => 'Success',
                'failed' => 'Failed',
                'running' => 'Running',
            ] as $key => $label)
                <a
                    href="{{ route('admin.cron.index', array_filter(['tab' => $key === 'all' ? null : $key, 'search' => request('search'), 'command' => request('command')])) }}"
                    class="admin-list-tab {{ $tab === $key ? 'active' : '' }}"
                >
                    {{ $label }}
                    <span class="admin-list-tab-count">{{ number_format($counts[$key]) }}</span>
                </a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('admin.cron.index') }}" class="admin-list-toolbar filter-bar">
            @if ($tab !== 'all')
                <input type="hidden" name="tab" value="{{ $tab }}">
            @endif
            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="Search command, output, or error…"
                value="{{ request('search') }}"
            >
            <select name="command" class="form-control">
                <option value="">All commands</option>
                @foreach ($commands as $cmd)
                    <option value="{{ $cmd }}" @selected(request('command') === $cmd)>{{ $cmd }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if (request()->hasAny(['search', 'command']))
                <a href="{{ route('admin.cron.index', array_filter(['tab' => $tab === 'all' ? null : $tab])) }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>

        @if ($logs->isEmpty())
            <div class="admin-list-empty empty-state">
                @if (request()->hasAny(['search', 'command']) || $tab !== 'all')
                    <p>No cron runs match these filters.</p>
                    <p><a href="{{ route('admin.cron.index') }}">Clear filters</a></p>
                @else
                    <p>No cron runs logged yet.</p>
                    <p class="text-muted">Runs appear when <code>php artisan schedule:run</code> executes scheduled tasks.</p>
                @endif
            </div>
        @else
            <p class="admin-result-count">
                Showing {{ $logs->firstItem() }}&ndash;{{ $logs->lastItem() }} of {{ number_format($logs->total()) }}
                {{ \Illuminate\Support\Str::plural('run', $logs->total()) }}
            </p>

            <div class="admin-cron-table admin-list-table">
                <div class="admin-cron-head" aria-hidden="true">
                    <span>Status</span>
                    <span>Command</span>
                    <span>Output</span>
                    <span>Runtime</span>
                    <span>When</span>
                </div>

                <ul class="admin-cron-list">
                    @foreach ($logs as $log)
                        <li class="admin-cron-row status-{{ $log->status }}">
                            <span class="admin-cron-status">
                                @if ($log->status === \App\Models\CronLog::STATUS_SUCCESS)
                                    <span class="badge badge-published">Success</span>
                                @elseif ($log->status === \App\Models\CronLog::STATUS_FAILED)
                                    <span class="badge badge-banned">Failed</span>
                                @else
                                    <span class="badge badge-draft">Running</span>
                                @endif
                            </span>

                            <div class="admin-cron-command-wrap">
                                <code class="admin-cron-command">{{ $log->command }}</code>
                                @if ($log->description)
                                    <span class="admin-cron-description">{{ $log->description }}</span>
                                @endif
                                @if ($log->expression)
                                    <span class="admin-cron-expression" title="Cron expression">{{ $log->expression }}</span>
                                @endif
                            </div>

                            <div class="admin-cron-output-wrap">
                                @if ($log->output)
                                    <p class="admin-cron-output">{{ $log->output }}</p>
                                @elseif ($log->error)
                                    <p class="admin-cron-error">{{ $log->error }}</p>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>

                            <span class="admin-cron-runtime">
                                @if ($log->runtime_ms !== null)
                                    {{ number_format($log->runtime_ms, $log->runtime_ms >= 100 ? 0 : 1) }} ms
                                @else
                                    —
                                @endif
                            </span>

                            <time
                                class="admin-cron-date"
                                datetime="{{ optional($log->started_at)->toIso8601String() }}"
                                title="{{ optional($log->started_at)->format('M j, Y g:i:s A') }}"
                            >
                                {{ optional($log->started_at)->diffForHumans() ?? '—' }}
                            </time>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($logs->hasPages())
                <div class="pagination">
                    @if ($logs->onFirstPage())
                        <span>&laquo;</span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}">&laquo;</a>
                    @endif

                    @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                        @if ($page == $logs->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span>&raquo;</span>
                    @endif
                </div>
            @endif
        @endif
    </div>
@endsection
