@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h2>Dashboard</h2>
    </div>

    <div class="admin-activity-bar" aria-label="Site activity">
        <div class="gallery-activity-group">
            <span class="gallery-activity-heading">Last 24 hours</span>
            <div class="gallery-activity-stats">
                <span class="gallery-activity-stat stat-btn" title="New posts" aria-label="{{ number_format($activityStats['posts']) }} new posts">
                    <svg class="image-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                        <path d="M21 15l-5-5L5 21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ number_format($activityStats['posts']) }}</span>
                </span>
                <span class="gallery-activity-stat stat-btn" title="New likes" aria-label="{{ number_format($activityStats['likes']) }} new likes">
                    <svg class="like-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span>{{ number_format($activityStats['likes']) }}</span>
                </span>
                <span class="gallery-activity-stat stat-btn" title="New comments" aria-label="{{ number_format($activityStats['comments']) }} new comments">
                    <svg class="comment-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4V5Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ number_format($activityStats['comments']) }}</span>
                </span>
                <span class="gallery-activity-stat stat-btn" title="New views" aria-label="{{ number_format($activityStats['views']) }} new views">
                    <svg class="view-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <path d="M12 5C7 5 2.73 8.11 1 12.5 2.73 16.89 7 20 12 20s9.27-3.11 11-7.5C21.27 8.11 17 5 12 5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                    <span>{{ number_format($activityStats['views']) }}</span>
                </span>
            </div>
        </div>

        <div class="gallery-activity-group">
            <span class="gallery-activity-heading">All time</span>
            <div class="gallery-activity-stats">
                <span class="gallery-activity-stat stat-btn" title="Total visits" aria-label="{{ number_format($stats['visits']) }} total visits">
                    <svg class="visit-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M2.5 12h19M12 2.5c2.8 2.9 4.2 6.4 4.2 9.5S14.8 18.6 12 21.5M12 2.5C9.2 5.4 7.8 8.9 7.8 12s1.4 6.6 4.2 9.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>{{ number_format($stats['visits']) }}</span>
                </span>
                <span class="gallery-activity-stat stat-btn" title="Total images" aria-label="{{ number_format($stats['total']) }} total images">
                    <svg class="image-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                        <path d="M21 15l-5-5L5 21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ number_format($stats['total']) }}</span>
                </span>
            </div>
        </div>
    </div>

    <h3 class="admin-stats-heading">Last 30 days</h3>
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

    @if ($recentImages->isNotEmpty())
        <h3 class="admin-stats-heading">Recent uploads</h3>

        <div class="admin-recent-table">
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

        <p class="admin-recent-see-all">
            <a href="{{ route('admin.images.index') }}">See all images &rarr;</a>
        </p>
    @endif

    @if ($recentActivity->isNotEmpty())
        <h3 class="admin-stats-heading">Recent admin activity</h3>

        <div class="admin-recent-table">
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
                                {{ $log->admin->username }}
                            @else
                                <span class="text-muted">Deleted admin</span>
                            @endif
                        </span>
                        <span class="admin-recent-activity-description">{{ $log->description }}</span>
                        <time class="admin-recent-date" datetime="{{ $log->created_at->toIso8601String() }}">
                            {{ $log->created_at->diffForHumans() }}
                        </time>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="admin-recent-see-all">
            <a href="{{ route('admin.activity.index') }}">See all activity &rarr;</a>
        </p>
    @endif
@endsection
