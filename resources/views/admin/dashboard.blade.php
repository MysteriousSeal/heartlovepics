@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-dashboard">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Overview</p>
            <h2 class="admin-list-title">Dashboard</h2>
            <p class="admin-list-lede">
                Site pulse at a glance — activity, trends, library health, and recent work.
            </p>
            <div class="admin-list-meta admin-dashboard-quick-links">
                <a href="{{ route('admin.images.index') }}" class="admin-list-chip admin-dashboard-chip-link">Images</a>
                <a href="{{ route('admin.users.index') }}" class="admin-list-chip admin-dashboard-chip-link">Users</a>
                <a href="{{ route('admin.messages.index') }}" class="admin-list-chip admin-dashboard-chip-link">Messages</a>
                <a href="{{ route('admin.analytics.index') }}" class="admin-list-chip admin-dashboard-chip-link">Analytics</a>
                <a href="{{ route('admin.activity.index') }}" class="admin-list-chip admin-dashboard-chip-link">Activity</a>
                <a href="{{ route('home') }}" class="admin-list-chip admin-dashboard-chip-link" target="_blank" rel="noopener noreferrer">View site</a>
            </div>
        </header>

        <section class="admin-dashboard-section" aria-labelledby="dashboard-pulse-heading">
            <div class="admin-dashboard-section-header">
                <h3 id="dashboard-pulse-heading" class="admin-dashboard-section-title">Site pulse</h3>
                <p class="admin-dashboard-section-desc">Live counters for the last day and all time.</p>
            </div>

            <div class="admin-dashboard-pulse">
                <div class="admin-dashboard-pulse-group">
                    <span class="admin-dashboard-pulse-label">Last 24 hours</span>
                    <div class="admin-dashboard-pulse-stats">
                        <div class="admin-dashboard-pulse-stat" title="New posts">
                            <span class="admin-dashboard-pulse-stat-label">Posts</span>
                            <span class="admin-dashboard-pulse-stat-value">{{ number_format($activityStats['posts']) }}</span>
                        </div>
                        <div class="admin-dashboard-pulse-stat" title="New likes">
                            <span class="admin-dashboard-pulse-stat-label">Likes</span>
                            <span class="admin-dashboard-pulse-stat-value">{{ number_format($activityStats['likes']) }}</span>
                        </div>
                        <div class="admin-dashboard-pulse-stat" title="New comments">
                            <span class="admin-dashboard-pulse-stat-label">Comments</span>
                            <span class="admin-dashboard-pulse-stat-value">{{ number_format($activityStats['comments']) }}</span>
                        </div>
                        <div class="admin-dashboard-pulse-stat" title="New views">
                            <span class="admin-dashboard-pulse-stat-label">Views</span>
                            <span class="admin-dashboard-pulse-stat-value">{{ number_format($activityStats['views']) }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-dashboard-pulse-group">
                    <span class="admin-dashboard-pulse-label">All time</span>
                    <div class="admin-dashboard-pulse-stats">
                        <div class="admin-dashboard-pulse-stat" title="Total visits">
                            <span class="admin-dashboard-pulse-stat-label">Visits</span>
                            <span class="admin-dashboard-pulse-stat-value">{{ number_format($stats['visits']) }}</span>
                        </div>
                        <div class="admin-dashboard-pulse-stat" title="Total images">
                            <span class="admin-dashboard-pulse-stat-label">Images</span>
                            <span class="admin-dashboard-pulse-stat-value">{{ number_format($stats['total']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="admin-dashboard-section" aria-labelledby="dashboard-trends-heading">
            <div class="admin-dashboard-section-header">
                <h3 id="dashboard-trends-heading" class="admin-dashboard-section-title">Last 30 days</h3>
                <p class="admin-dashboard-section-desc">Daily activity trends across the gallery.</p>
            </div>

            <div class="trend-grid">
                @foreach ([
                    'posts' => 'New posts',
                    'likes' => 'New likes',
                    'comments' => 'New comments',
                    'views' => 'New views',
                ] as $key => $label)
                    @php $chart = $trendCharts[$key]; @endphp
                    <div class="trend-card">
                        <div class="trend-card-header">
                            <span class="trend-card-label">{{ $label }}</span>
                            <span class="trend-card-total">{{ number_format($chart['total']) }}</span>
                        </div>
                        <svg class="trend-sparkline" viewBox="0 0 220 48" preserveAspectRatio="none" aria-hidden="true">
                            @if ($chart['points'])
                                <path d="{{ $chart['area'] }}" class="trend-sparkline-area"></path>
                                <polyline points="{{ $chart['points'] }}" class="trend-sparkline-line"></polyline>
                            @endif
                        </svg>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="admin-dashboard-section" aria-labelledby="dashboard-library-heading">
            <div class="admin-dashboard-section-header">
                <h3 id="dashboard-library-heading" class="admin-dashboard-section-title">Library</h3>
                <p class="admin-dashboard-section-desc">Content totals — click through where available.</p>
            </div>

            <div class="stats-grid">
                <a href="{{ route('admin.images.index', ['status' => 'published']) }}" class="stat-card stat-card-link">
                    <div class="stat-value">{{ number_format($stats['published']) }}</div>
                    <div class="stat-label">Published</div>
                </a>
                <a href="{{ route('admin.images.index', ['status' => 'draft']) }}" class="stat-card stat-card-link">
                    <div class="stat-value">{{ number_format($stats['draft']) }}</div>
                    <div class="stat-label">Drafts</div>
                </a>
                <a href="{{ route('admin.comments.index') }}" class="stat-card stat-card-link">
                    <div class="stat-value">{{ number_format($stats['comments']) }}</div>
                    <div class="stat-label">Comments</div>
                </a>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['likes']) }}</div>
                    <div class="stat-label">Image likes</div>
                </div>
            </div>
        </section>

        @if ($recentImages->isNotEmpty())
            <section class="admin-dashboard-section" aria-labelledby="dashboard-uploads-heading">
                <div class="admin-dashboard-section-header admin-dashboard-section-header-row">
                    <div>
                        <h3 id="dashboard-uploads-heading" class="admin-dashboard-section-title">Recent uploads</h3>
                        <p class="admin-dashboard-section-desc">Latest images added to the library.</p>
                    </div>
                    <a href="{{ route('admin.images.index') }}" class="admin-dashboard-section-link">See all images &rarr;</a>
                </div>

                <div class="admin-recent-table admin-list-table">
                    <div class="admin-recent-head" aria-hidden="true">
                        <span>Image</span>
                        <span>Author</span>
                        <span>Status</span>
                        <span>Date</span>
                        <span></span>
                    </div>

                    <ul class="admin-recent-list">
                        @foreach ($recentImages as $image)
                            <li class="admin-recent-row">
                                @if ($image->is_published)
                                    <a href="{{ route('images.show', $image->slug) }}" class="admin-recent-image-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ $image->thumbnail_url }}" alt="" class="admin-recent-thumb" width="44" height="44" loading="lazy">
                                        <span class="admin-recent-title" title="{{ $image->title }}">{{ $image->title }}</span>
                                    </a>
                                @else
                                    <span class="admin-recent-image-link">
                                        <img src="{{ $image->thumbnail_url }}" alt="" class="admin-recent-thumb" width="44" height="44" loading="lazy">
                                        <span class="admin-recent-title" title="{{ $image->title }}">{{ $image->title }}</span>
                                    </span>
                                @endif

                                <span class="admin-recent-author">
                                    @if ($image->user)
                                        <a href="{{ route('users.show', $image->user) }}" target="_blank" rel="noopener noreferrer">{{ '@'.$image->user->username }}</a>
                                    @else
                                        Anonymous
                                    @endif
                                </span>

                                <span class="admin-recent-status">
                                    <span class="badge {{ $image->is_published ? 'badge-published' : 'badge-draft' }}">
                                        {{ $image->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                    @if ($image->is_nsfw)
                                        <span class="badge badge-nsfw">NSFW</span>
                                    @endif
                                </span>

                                <time class="admin-recent-date" datetime="{{ $image->created_at->toIso8601String() }}">
                                    {{ $image->created_at->format('M j, Y') }}
                                </time>

                                <a href="{{ route('admin.images.edit', $image) }}" class="btn btn-sm btn-secondary">Edit</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        @if ($recentActivity->isNotEmpty())
            <section class="admin-dashboard-section" aria-labelledby="dashboard-activity-heading">
                <div class="admin-dashboard-section-header admin-dashboard-section-header-row">
                    <div>
                        <h3 id="dashboard-activity-heading" class="admin-dashboard-section-title">Recent admin activity</h3>
                        <p class="admin-dashboard-section-desc">Latest actions from the admin team.</p>
                    </div>
                    <a href="{{ route('admin.activity.index') }}" class="admin-dashboard-section-link">See all activity &rarr;</a>
                </div>

                <div class="admin-recent-table admin-list-table">
                    <div class="admin-recent-activity-head" aria-hidden="true">
                        <span>Admin</span>
                        <span>Description</span>
                        <span>Date</span>
                    </div>

                    <ul class="admin-recent-list">
                        @foreach ($recentActivity as $log)
                            <li class="admin-recent-activity-row">
                                <span class="admin-recent-author">
                                    @if ($log->admin)
                                        <a href="{{ route('users.show', $log->admin) }}" target="_blank" rel="noopener noreferrer">{{ $log->admin->username }}</a>
                                    @else
                                        <span class="text-muted">Deleted admin</span>
                                    @endif
                                </span>
                                <span class="admin-recent-activity-description">{{ $log->description }}</span>
                                <time class="admin-recent-date" datetime="{{ $log->created_at->toIso8601String() }}" title="{{ $log->created_at->toDayDateTimeString() }}">
                                    {{ $log->created_at->diffForHumans() }}
                                </time>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif
    </div>
@endsection
