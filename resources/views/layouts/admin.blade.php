<!DOCTYPE html>
@php
    $theme = \App\Support\ThemePreference::resolve(request());
@endphp
<html lang="en" data-theme="{{ $theme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function () {
            var match = document.cookie.match(/(?:^|; )theme=(light|dark)/);
            if (match) {
                document.documentElement.setAttribute('data-theme', match[1]);
            }
        })();
    </script>
    <title>@yield('title', 'Admin') — HeartLovePics</title>
    @include('partials.favicon')
    @include('partials.font-preload')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
    <header class="admin-nav">
        <div class="admin-nav-inner">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">HeartLovePics <span>Admin</span></a>

            <nav class="admin-nav-links" aria-label="Admin sections">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.images.index') }}" class="{{ request()->routeIs('admin.images.*') ? 'active' : '' }}">Images</a>
                <a href="{{ route('admin.artists.index') }}" class="{{ request()->routeIs('admin.artists.*') ? 'active' : '' }}">Artists</a>
                <a href="{{ route('admin.parodies.index') }}" class="{{ request()->routeIs('admin.parodies.*') ? 'active' : '' }}">Parodies</a>
                <a href="{{ route('admin.comments.index') }}" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">Comments</a>
                <a href="{{ route('admin.messages.index') }}" class="admin-nav-link-badged {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                    Messages
                    @php $unrepliedCount = \App\Models\ContactMessage::whereNull('replied_at')->where('is_archived', false)->count(); @endphp
                    @if ($unrepliedCount > 0)
                        <span class="admin-nav-badge">{{ $unrepliedCount > 99 ? '99+' : $unrepliedCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a>
                <a href="{{ route('admin.user-activity.index') }}" class="{{ request()->routeIs('admin.user-activity.*') ? 'active' : '' }}">Activity</a>
                <a href="{{ route('admin.activity.index') }}" class="{{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">Admin Activity</a>
                <a href="{{ route('admin.cron.index') }}" class="{{ request()->routeIs('admin.cron.*') ? 'active' : '' }}">Cron</a>
                <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">Analytics</a>
                <a href="{{ route('admin.configuration.edit') }}" class="{{ request()->routeIs('admin.configuration.*') ? 'active' : '' }}">Configuration</a>
            </nav>

            <div class="admin-nav-actions">
                <span
                    class="admin-list-chip admin-active-now-chip admin-nav-active-now"
                    id="admin-nav-active-now"
                    title="Distinct visitors with a page view in the last 2 minutes"
                >
                    <span class="admin-active-now-dot" aria-hidden="true"></span>
                    <span class="admin-active-now-count">&hellip;</span>
                    active
                </span>
                <span class="admin-nav-divider" aria-hidden="true"></span>
                <button
                    type="button"
                    class="theme-toggle-btn"
                    id="theme-toggle"
                    data-theme="{{ $theme }}"
                    title="Toggle dark mode"
                    aria-label="Toggle dark mode"
                >
                    <span class="theme-toggle-icon theme-toggle-icon-sun" aria-hidden="true">☀</span>
                    <span class="theme-toggle-icon theme-toggle-icon-moon" aria-hidden="true">☾</span>
                </button>
                <span class="admin-nav-divider" aria-hidden="true"></span>
                <a href="{{ route('home') }}" class="admin-nav-utility" target="_blank" rel="noopener noreferrer">View Site</a>
                <span class="admin-nav-divider" aria-hidden="true"></span>
                <form action="{{ route('admin.logout') }}" method="POST" class="admin-nav-logout">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-main">
        @include('partials.flash')
        @yield('content')
    </main>

    <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
    <script
        src="{{ asset('js/admin-active-now.js') }}"
        data-active-now-url="{{ route('admin.analytics.active-now') }}"
        defer
    ></script>
    @stack('scripts')
</body>
</html>
