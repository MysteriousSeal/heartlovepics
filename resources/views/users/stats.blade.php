@extends('layouts.app')

@section('title', 'Stats — ' . $user->username . ' — HeartLovePics')
@section('meta_description', 'Creator stats for ' . $user->username . ' on HeartLovePics.')
@section('canonical', route('users.stats', $user))

@section('content')
    <section class="user-profile">
        @include('partials.flash')

        @include('partials.user-profile-header', compact('user', 'isOwner', 'uploadCount', 'isFollowing', 'followersCount', 'followingCount'))

        @include('partials.user-profile-tabs', compact('user', 'activeTab', 'uploadCount', 'likedCount', 'bookmarkCount', 'draftCount', 'journalCount', 'isOwner'))

        <section class="user-profile-stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($allTime['uploads']) }}</div>
                    <div class="stat-label">Published uploads</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($allTime['views']) }}</div>
                    <div class="stat-label">Total views</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($allTime['likes']) }}</div>
                    <div class="stat-label">Total likes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($allTime['comments']) }}</div>
                    <div class="stat-label">Total comments</div>
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
        </section>
    </section>
@endsection
