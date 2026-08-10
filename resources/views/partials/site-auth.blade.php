<div class="site-auth">
    @if (! auth()->check() || auth()->user()->canPost())
        <a href="{{ route('profile.images.create') }}" class="site-upload-btn" title="Upload image" aria-label="Upload image">
            <svg class="site-upload-icon" viewBox="0 0 16 16" width="14" height="14" aria-hidden="true">
                <path d="M8 3.5v9M3.5 8h9" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"></path>
            </svg>
        </a>
    @endif
    <button
        type="button"
        class="theme-toggle-btn"
        id="theme-toggle"
        data-theme="{{ $theme ?? \App\Support\ThemePreference::resolve(request()) }}"
        title="Toggle dark mode"
        aria-label="Toggle dark mode"
    >
        <span class="theme-toggle-icon theme-toggle-icon-sun" aria-hidden="true">☀</span>
        <span class="theme-toggle-icon theme-toggle-icon-moon" aria-hidden="true">☾</span>
    </button>
    <span class="site-header-divider" aria-hidden="true"></span>
    @auth
        <a
            href="{{ route('notifications.index') }}"
            class="notifications-bell"
            data-unread-url="{{ route('notifications.unread-count') }}"
            title="Notifications"
            aria-label="{{ ($unreadNotificationCount ?? 0) > 0 ? ($unreadNotificationCount . ' unread notifications') : 'Notifications' }}"
        >
            <svg class="notifications-bell-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span
                class="notifications-bell-badge"
                @if (($unreadNotificationCount ?? 0) <= 0) hidden @endif
            >{{ ($unreadNotificationCount ?? 0) > 99 ? '99+' : ($unreadNotificationCount ?? 0) }}</span>
        </a>
        <a href="{{ route('users.show', auth()->user()) }}" class="site-auth-profile">
            @include('partials.user-avatar', [
                'user' => auth()->user(),
                'class' => 'site-auth-avatar',
                'width' => 36,
                'height' => 36,
            ])
            <span class="site-auth-name">{{ auth()->user()->username }}</span>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="site-auth-logout">
            @csrf
            <button type="submit" class="btn btn-sm btn-secondary">Logout</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="btn btn-sm btn-secondary">Login</a>
        <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a>
    @endauth
</div>