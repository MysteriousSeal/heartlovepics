@extends('layouts.admin')

@section('title', 'Activity')

@section('content')
    <div class="admin-list-page">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Live feed</p>
            <h2 class="admin-list-title">Activity</h2>
            <p class="admin-list-lede">
                Signups, likes, and comments across the site — logged in or guest.
            </p>
            <div class="admin-list-meta">
                <span class="admin-list-chip">{{ number_format($typeCounts->get('signup', 0)) }} accounts</span>
                <span class="admin-list-chip">{{ number_format($typeCounts->get('like', 0)) }} real likes</span>
                <span class="admin-list-chip">{{ number_format($typeCounts->get('like_cron', 0)) }} cron likes</span>
                <span class="admin-list-chip">{{ number_format($typeCounts->get('comment', 0)) }} comments</span>
                <span class="admin-list-chip">{{ number_format($typeCounts->get('bookmark', 0)) }} bookmarks</span>
            </div>
        </header>

        <nav class="admin-list-tabs" aria-label="Activity type">
            <a
                href="{{ route('admin.user-activity.index', array_filter(['hide_cron' => $hideCron ?: null])) }}"
                class="admin-list-tab {{ ! $type ? 'active' : '' }}"
            >
                All
            </a>
            @foreach ($types as $value => $label)
                <a
                    href="{{ route('admin.user-activity.index', array_filter(['type' => $value, 'hide_cron' => $hideCron ?: null])) }}"
                    class="admin-list-tab {{ $type === $value ? 'active' : '' }}"
                >
                    {{ $label }}
                    <span class="admin-list-tab-count">{{ number_format($typeCounts->get($value, 0)) }}</span>
                </a>
            @endforeach

            <a
                href="{{ route('admin.user-activity.index', array_filter(['type' => $type, 'hide_cron' => $hideCron ? null : '1'])) }}"
                class="admin-list-chip {{ $hideCron ? 'is-filtered' : '' }}"
                style="margin-left: auto;"
            >
                {{ $hideCron ? '✓ ' : '' }}Hide cron-seeded likes
            </a>
        </nav>

        @if ($activities->isEmpty())
            <div class="admin-list-empty empty-state">
                <p>No activity matches this filter.</p>
            </div>
        @else
            <p class="admin-result-count">
                Showing {{ $activities->firstItem() }}&ndash;{{ $activities->lastItem() }} of {{ number_format($activities->total()) }}
                {{ \Illuminate\Support\Str::plural('event', $activities->total()) }}
            </p>

            <div class="admin-activity-table admin-list-table">
                <div class="admin-activity-head" aria-hidden="true">
                    <span>Who</span>
                    <span>Action</span>
                    <span>Description</span>
                    <span>Date</span>
                </div>

                <ul class="admin-activity-list">
                    @foreach ($activities as $event)
                        <li class="admin-activity-row">
                            <div class="admin-activity-admin">
                                @if ($event['user'])
                                    <a href="{{ route('users.show', $event['user']) }}" class="admin-activity-admin-link" target="_blank" rel="noopener noreferrer">
                                        @include('partials.user-avatar', [
                                            'user' => $event['user'],
                                            'class' => 'admin-activity-avatar',
                                            'width' => 36,
                                            'height' => 36,
                                        ])
                                        <span class="admin-activity-admin-name">{{ $event['user']->username }}</span>
                                    </a>
                                @else
                                    <span class="admin-activity-admin-missing">
                                        <img
                                            src="{{ asset($event['type'] === 'like_cron' ? 'images/bot-avatar.jpg' : 'images/blank-avatar.jpg') }}"
                                            alt=""
                                            class="admin-activity-avatar"
                                            width="36"
                                            height="36"
                                        >
                                        <span class="text-muted">{{ $event['guest_label'] ?? 'Guest' }}</span>
                                    </span>
                                @endif
                            </div>
                            <span>
                                <span class="badge {{ match ($event['type']) {
                                    'signup' => 'badge-published',
                                    'like' => 'badge-has-description',
                                    'like_cron' => 'badge-draft',
                                    'comment' => 'badge-banned',
                                    'bookmark' => 'badge-sfw',
                                } }}">
                                    {{ $types[$event['type']] }}
                                </span>
                            </span>
                            <p class="admin-activity-description">
                                @if ($event['type'] === 'signup')
                                    Created an account.
                                @elseif ($event['type'] === 'like' || $event['type'] === 'like_cron')
                                    @if ($event['image'])
                                        Liked <a href="{{ route('images.show', $event['image']->slug) }}" target="_blank" rel="noopener noreferrer">{{ $event['image']->title }}</a>
                                    @else
                                        Liked a post that has since been removed.
                                    @endif
                                @elseif ($event['type'] === 'comment')
                                    @if ($event['image'])
                                        Commented on <a href="{{ route('images.show', $event['image']->slug) }}" target="_blank" rel="noopener noreferrer">{{ $event['image']->title }}</a>:
                                    @else
                                        Commented on a post that has since been removed:
                                    @endif
                                    <em>&ldquo;{{ $event['comment_body'] }}&rdquo;</em>
                                @elseif ($event['type'] === 'bookmark')
                                    @if ($event['image'])
                                        Bookmarked <a href="{{ route('images.show', $event['image']->slug) }}" target="_blank" rel="noopener noreferrer">{{ $event['image']->title }}</a>
                                    @else
                                        Bookmarked a post that has since been removed.
                                    @endif
                                @endif
                            </p>
                            <time class="admin-activity-date" datetime="{{ $event['created_at']->toIso8601String() }}" title="{{ $event['created_at']->toIso8601String() }}">
                                <span class="admin-activity-date-primary">{{ $event['created_at']->format('M j, Y') }}</span>
                                <span class="admin-activity-date-secondary">{{ $event['created_at']->format('g:i A') }}</span>
                            </time>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($activities->hasPages())
                <div class="pagination">
                    @if ($activities->onFirstPage())
                        <span>&laquo;</span>
                    @else
                        <a href="{{ $activities->previousPageUrl() }}">&laquo;</a>
                    @endif

                    @foreach ($activities->getUrlRange(1, $activities->lastPage()) as $page => $url)
                        @if ($page == $activities->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($activities->hasMorePages())
                        <a href="{{ $activities->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span>&raquo;</span>
                    @endif
                </div>
            @endif
        @endif
    </div>
@endsection
