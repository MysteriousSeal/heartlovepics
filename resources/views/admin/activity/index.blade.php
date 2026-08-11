@extends('layouts.admin')

@section('title', 'Activity Log')

@php
    $badgeClasses = [
        \App\Models\AdminActivityLog::ACTION_USER_BANNED => 'badge-banned',
        \App\Models\AdminActivityLog::ACTION_USER_UNBANNED => 'badge-published',
        \App\Models\AdminActivityLog::ACTION_USER_DELETED => 'badge-banned',
        \App\Models\AdminActivityLog::ACTION_IMAGE_UPDATED => 'badge-draft',
        \App\Models\AdminActivityLog::ACTION_IMAGE_DELETED => 'badge-banned',
        \App\Models\AdminActivityLog::ACTION_COMMENT_DELETED => 'badge-banned',
        \App\Models\AdminActivityLog::ACTION_ARTIST_CREATED => 'badge-published',
        \App\Models\AdminActivityLog::ACTION_ARTIST_UPDATED => 'badge-draft',
        \App\Models\AdminActivityLog::ACTION_ARTIST_DELETED => 'badge-banned',
        \App\Models\AdminActivityLog::ACTION_CONFIGURATION_UPDATED => 'badge-draft',
        \App\Models\AdminActivityLog::ACTION_CONTACT_MESSAGE_DELETED => 'badge-banned',
    ];
@endphp

@section('content')
    <div class="admin-list-page">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Audit</p>
            <h2 class="admin-list-title">Activity Log</h2>
            <p class="admin-list-lede">
                A running record of admin actions — bans, edits, deletes, and configuration changes.
            </p>
            @if (! $logs->isEmpty())
                <div class="admin-list-meta">
                    <span class="admin-list-chip">
                        {{ number_format($logs->total()) }}
                        {{ \Illuminate\Support\Str::plural('entry', $logs->total()) }}
                    </span>
                    @if (request()->hasAny(['search', 'action']))
                        <span class="admin-list-chip is-filtered">Filtered</span>
                    @endif
                </div>
            @endif
        </header>

        <form method="GET" action="{{ route('admin.activity.index') }}" class="admin-list-toolbar filter-bar">
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
            <div class="admin-list-empty empty-state">
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

            <div class="admin-activity-table admin-list-table">
                <div class="admin-activity-head" aria-hidden="true">
                    <span>Admin</span>
                    <span>Action</span>
                    <span>Description</span>
                    <span>Date</span>
                </div>

                <ul class="admin-activity-list">
                    @foreach ($logs as $log)
                        <li class="admin-activity-row">
                            <div class="admin-activity-admin">
                                @if ($log->admin)
                                    <a href="{{ route('users.show', $log->admin) }}" class="admin-activity-admin-link" target="_blank" rel="noopener noreferrer">
                                        @include('partials.user-avatar', [
                                            'user' => $log->admin,
                                            'class' => 'admin-activity-avatar',
                                            'width' => 36,
                                            'height' => 36,
                                        ])
                                        <span class="admin-activity-admin-name">{{ $log->admin->username }}</span>
                                    </a>
                                @else
                                    <span class="admin-activity-admin-missing">
                                        <span class="admin-activity-avatar-placeholder" aria-hidden="true">?</span>
                                        <span class="text-muted">Deleted admin</span>
                                    </span>
                                @endif
                            </div>
                            <span class="admin-activity-action">
                                <span class="badge {{ $badgeClasses[$log->action] ?? 'badge-draft' }}">
                                    {{ $actions[$log->action] ?? $log->action }}
                                </span>
                            </span>
                            <div class="admin-activity-body">
                                <p class="admin-activity-description">{{ $log->description }}</p>
                                @if ($log->subject_label)
                                    <p class="admin-activity-subject">
                                        <span class="admin-activity-subject-label">{{ $log->subject_type ?: 'subject' }}</span>
                                        {{ $log->subject_label }}
                                    </p>
                                @endif
                            </div>
                            <time class="admin-activity-date" datetime="{{ $log->created_at->toIso8601String() }}" title="{{ $log->created_at->toIso8601String() }}">
                                <span class="admin-activity-date-primary">{{ $log->created_at->format('M j, Y') }}</span>
                                <span class="admin-activity-date-secondary">{{ $log->created_at->format('g:i A') }}</span>
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
