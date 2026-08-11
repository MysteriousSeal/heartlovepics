@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
    <div class="admin-list-page">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Traffic</p>
            <h2 class="admin-list-title">Analytics</h2>
            <p class="admin-list-lede">
                Visit breakdowns and a detailed log of recent traffic.
            </p>
            <div class="admin-list-meta">
                <span class="admin-list-chip admin-active-now-chip {{ $activeNow['total'] > 0 ? 'is-live' : '' }}" title="Distinct visitors with a page view in the last 2 minutes (bots counted separately)">
                    <span class="admin-active-now-dot" aria-hidden="true"></span>
                    <span class="admin-active-now-count">{{ number_format($activeNow['total']) }}</span>
                    active
                    <span class="admin-list-chip-muted">
                        · {{ number_format($activeNow['users']) }} logged in
                        · {{ number_format($activeNow['guests']) }} {{ \Illuminate\Support\Str::plural('guest', $activeNow['guests']) }}
                        · {{ number_format($activeNow['bots']) }} {{ \Illuminate\Support\Str::plural('bot', $activeNow['bots']) }}
                    </span>
                </span>
                <span class="admin-list-chip" title="Page views and distinct visitors in the selected range (bots counted separately from guests)">
                    {{ number_format($visits->total()) }}
                    {{ \Illuminate\Support\Str::plural('visit', $visits->total()) }}
                    <span class="admin-list-chip-muted">
                        · {{ number_format($rangeVisitors['total']) }}
                        unique
                        {{ \Illuminate\Support\Str::plural('visitor', $rangeVisitors['total']) }}
                        · {{ number_format($rangeVisitors['users']) }}
                        {{ \Illuminate\Support\Str::plural('user', $rangeVisitors['users']) }}
                        · {{ number_format($rangeVisitors['guests']) }}
                        {{ \Illuminate\Support\Str::plural('guest', $rangeVisitors['guests']) }}
                        · {{ number_format($rangeVisitors['bots']) }}
                        {{ \Illuminate\Support\Str::plural('bot', $rangeVisitors['bots']) }}
                        · {{ $ranges[$range] }}
                    </span>
                </span>
            </div>
        </header>

        <nav class="admin-list-tabs" aria-label="Time range">
            @foreach ($ranges as $key => $label)
                <a
                    href="{{ route('admin.analytics.index', ['range' => $key]) }}"
                    class="admin-list-tab {{ $range === $key ? 'active' : '' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        @if ($visits->isEmpty())
            <div class="admin-list-empty empty-state">
                <p>
                    @if ($range === 'all')
                        No visits recorded yet.
                    @else
                        No visits in {{ strtolower($ranges[$range]) }}.
                        <a href="{{ route('admin.analytics.index', ['range' => 'all']) }}">View all time</a>
                    @endif
                </p>
            </div>
        @else
            @if (! empty($charts))
                <section class="admin-analytics-charts" aria-label="Visit breakdown charts for {{ $ranges[$range] }}">
                    @foreach ($charts as $chart)
                        @include('partials.admin-donut-chart', ['chart' => $chart])
                    @endforeach
                </section>
            @endif

            <div class="admin-dashboard-section-header admin-dashboard-section-header-row">
                <div>
                    <h3 class="admin-dashboard-section-title">Visits</h3>
                    <p class="admin-dashboard-section-desc">
                        Page views for {{ strtolower($ranges[$range]) }}.
                    </p>
                </div>
            </div>

            <p class="admin-result-count">
                Showing {{ $visits->firstItem() }}&ndash;{{ $visits->lastItem() }} of {{ number_format($visits->total()) }}
                {{ \Illuminate\Support\Str::plural('visit', $visits->total()) }}
            </p>

            <div class="admin-visits-table admin-list-table">
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
    </div>
@endsection
