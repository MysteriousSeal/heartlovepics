<header class="user-profile-header {{ $user->hasBanner() ? 'user-profile-header--has-banner' : '' }}">
    @if ($user->hasBanner())
        <div class="user-profile-banner">
            <img src="{{ $user->banner_url }}" alt="" loading="lazy">
        </div>
    @endif

    @if ($isOwner)
        <a href="{{ route('profile.edit') }}" class="btn btn-secondary user-profile-edit-btn">Edit profile</a>
    @else
        @include('partials.follow-button', compact('user', 'isOwner', 'isFollowing'))
    @endif

    <div class="user-profile-header-row">
        @include('partials.user-avatar', [
            'user' => $user,
            'class' => 'user-profile-avatar',
            'width' => 144,
            'height' => 144,
        ])

        <div class="user-profile-meta">
            <div class="user-profile-identity">
                <h2>{{ '@'.$user->username }}</h2>
                <p class="user-profile-joined">Member since {{ $user->created_at->format('F j, Y') }}</p>
                @php $profileBadges = \App\Support\ProfileBadges::for($user); @endphp
                @if (! empty($profileBadges))
                    <ul class="profile-badges">
                        @foreach ($profileBadges as $badge)
                            <li class="profile-badge">{{ $badge['label'] }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if ($user->hasBio())
                <div class="user-profile-bio">{!! $user->bio_html !!}</div>
            @endif
            <p class="user-profile-stats">
                <span class="user-profile-stat" data-followers-count>
                    <span class="user-profile-stat-value">{{ number_format($followersCount ?? 0) }}</span>
                    <span class="user-profile-stat-label">{{ \Illuminate\Support\Str::plural('follower', $followersCount ?? 0) }}</span>
                </span>
                <span class="user-profile-stats-sep" aria-hidden="true">·</span>
                <span class="user-profile-stat">
                    <span class="user-profile-stat-value">{{ number_format($followingCount ?? 0) }}</span>
                    <span class="user-profile-stat-label">following</span>
                </span>
            </p>
            @unless ($isOwner)
                <p class="user-profile-count">
                    {{ number_format($uploadCount) }}
                    {{ \Illuminate\Support\Str::plural('image', $uploadCount) }}
                </p>
            @endunless
        </div>
    </div>
</header>
