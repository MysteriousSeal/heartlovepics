@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
    <div class="page-header">
        <h2>Analytics</h2>
    </div>

    <nav class="admin-analytics-tabs" aria-label="Analytics sections">
        <span class="admin-analytics-tab active">Visits</span>
    </nav>

    @if ($visits->isEmpty())
        <div class="empty-state">
            <p>No visits recorded yet.</p>
        </div>
    @else
        <p class="admin-result-count">
            Showing {{ $visits->firstItem() }}&ndash;{{ $visits->lastItem() }} of {{ number_format($visits->total()) }}
            {{ \Illuminate\Support\Str::plural('visit', $visits->total()) }}
        </p>

        <div class="admin-visits-table">
            <div class="admin-visits-head" aria-hidden="true">
                <span>Date &amp; time</span>
                <span>Path</span>
                <span>Referrer</span>
                <span>Location</span>
                <span>Browser</span>
                <span>Device</span>
                <span>OS</span>
                <span>Bot</span>
                <span>IP address</span>
                <span>Username</span>
            </div>

            <ul class="admin-visits-list">
                @foreach ($visits as $visit)
                    <li class="admin-visits-row {{ $visit->is_bot ? 'is-bot' : '' }}">
                        <time class="admin-visits-date" datetime="{{ $visit->created_at->toIso8601String() }}">
                            {{ $visit->created_at->format('M j, Y g:i A') }}
                        </time>
                        <span class="admin-visits-path" @if ($visit->path) title="{{ $visit->path }}" @endif>
                            {{ $visit->path ?: '—' }}
                        </span>
                        <span class="admin-visits-referrer" @if ($visit->referrer) title="{{ $visit->referrer }}" @endif>
                            @if ($visit->referrer)
                                @php
                                    $referrerHost = parse_url($visit->referrer, PHP_URL_HOST);
                                    $referrerLabel = $referrerHost
                                        ? \Illuminate\Support\Str::of($referrerHost)->replaceStart('www.', '')->toString()
                                        : \Illuminate\Support\Str::limit($visit->referrer, 40);
                                @endphp
                                {{ $referrerLabel }}
                            @else
                                &mdash;
                            @endif
                        </span>
                        <span class="admin-visits-location" title="{{ $visit->location_label }}">
                            {{ $visit->location_label }}
                        </span>
                        <span class="admin-visits-browser" @if ($visit->user_agent) title="{{ $visit->user_agent }}" @endif>
                            {{ $visit->browser }}
                        </span>
                        <span class="admin-visits-device">
                            {{ $visit->device }}
                        </span>
                        <span class="admin-visits-os">
                            {{ $visit->os }}
                        </span>
                        <span class="admin-visits-bot">
                            @if ($visit->is_bot)
                                <span class="badge badge-bot">Bot</span>
                            @else
                                <span class="admin-visits-bot-no">No</span>
                            @endif
                        </span>
                        <span class="admin-visits-ip">
                            {{ $visit->ip_address ?: '—' }}
                        </span>
                        <span class="admin-visits-username">
                            @if ($visit->user)
                                <a href="{{ route('users.show', $visit->user) }}" target="_blank" rel="noopener noreferrer">{{ $visit->user->username }}</a>
                            @else
                                &mdash;
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>

        @if ($visits->hasPages())
            <div class="pagination">
                @if ($visits->onFirstPage())
                    <span>&laquo;</span>
                @else
                    <a href="{{ $visits->previousPageUrl() }}">&laquo;</a>
                @endif

                @foreach ($visits->getUrlRange(1, $visits->lastPage()) as $page => $url)
                    @if ($page == $visits->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($visits->hasMorePages())
                    <a href="{{ $visits->nextPageUrl() }}">&raquo;</a>
                @else
                    <span>&raquo;</span>
                @endif
            </div>
        @endif
    @endif
@endsection
