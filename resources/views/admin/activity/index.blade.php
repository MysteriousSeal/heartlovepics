@extends('layouts.admin')

@section('title', 'Activity Log')

@php
    $badgeClasses = [
        \App\Models\AdminActivityLog::ACTION_USER_BANNED => 'badge-banned',
        \App\Models\AdminActivityLog::ACTION_USER_UNBANNED => 'badge-published',
        \App\Models\AdminActivityLog::ACTION_IMAGE_UPDATED => 'badge-draft',
        \App\Models\AdminActivityLog::ACTION_IMAGE_DELETED => 'badge-banned',
        \App\Models\AdminActivityLog::ACTION_COMMENT_DELETED => 'badge-banned',
    ];
@endphp

@section('content')
    <div class="page-header">
        <h2>Activity Log</h2>
    </div>

    <form method="GET" action="{{ route('admin.activity.index') }}" class="filter-bar">
        <input
            type="search"
            name="search"
            class="form-control"
            placeholder="Search description or subject…"
            value="{{ request('search') }}"
        >
        <select name="action" class="form-control">
            <option value="">All actions</option>
            @foreach ($actions as $value => $label)
                <option value="{{ $value }}" @selected(request('action') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if (request()->hasAny(['search', 'action']))
            <a href="{{ route('admin.activity.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    @if ($logs->isEmpty())
        <div class="empty-state">
            @if (request()->hasAny(['search', 'action']))
                <p>No activity matches these filters.</p>
                <p><a href="{{ route('admin.activity.index') }}">Clear filters</a></p>
            @else
                <p>No admin activity recorded yet.</p>
            @endif
        </div>
    @else
        <p class="admin-result-count">
            Showing {{ $logs->firstItem() }}&ndash;{{ $logs->lastItem() }} of {{ number_format($logs->total()) }}
            {{ \Illuminate\Support\Str::plural('entry', $logs->total()) }}
        </p>

        <div class="admin-activity-table">
            <div class="admin-activity-head" aria-hidden="true">
                <span>Admin</span>
                <span>Action</span>
                <span>Description</span>
                <span>Date</span>
            </div>

            <ul class="admin-activity-list">
                @foreach ($logs as $log)
                    <li class="admin-activity-row">
                        <span class="admin-activity-admin">
                            @if ($log->admin)
                                <a href="{{ route('users.show', $log->admin) }}" target="_blank" rel="noopener noreferrer">{{ $log->admin->username }}</a>
                            @else
                                <span class="text-muted">Deleted admin</span>
                            @endif
                        </span>
                        <span>
                            <span class="badge {{ $badgeClasses[$log->action] ?? 'badge-draft' }}">
                                {{ $actions[$log->action] ?? $log->action }}
                            </span>
                        </span>
                        <p class="admin-activity-description">{{ $log->description }}</p>
                        <time class="admin-activity-date" datetime="{{ $log->created_at->toIso8601String() }}">
                            {{ $log->created_at->format('M j, Y g:i A') }}
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
@endsection
