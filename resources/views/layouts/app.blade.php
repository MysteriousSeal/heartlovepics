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

            var layout = localStorage.getItem('imageDetailLayout');
            if (layout === 'side_by_side' || layout === 'stacked') {
                document.documentElement.setAttribute('data-image-layout', layout);
            }
        })();
    </script>
    @stack('preload')
    <title>@yield('title', 'HeartLovePics')</title>
    <meta name="description" content="@yield('meta_description', 'A quiet collection of images. Browse, view, and like photos on HeartLovePics.')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    @include('partials.favicon')
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')">
    @endif
    @stack('meta')
    @include('partials.async-styles')
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <div class="site-header-main">
                <h1 class="site-logo">
                    <a href="{{ route('home') }}" class="site-logo-link">
                        <span class="site-logo-wordmark">
                            <span class="logo-primary">HeartLove</span><span class="logo-secondary">Pics</span>
                        </span>
                    </a>
                </h1>
                <p class="site-tagline">A quiet collection of images</p>
            </div>
            <a
                href="{{ route('pages.contact') }}"
                class="site-contact-btn"
                title="Contact"
                aria-label="Contact"
            >
                <svg class="site-contact-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                    <path d="M4 6h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="m4 8 8 5 8-5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </header>

    <div class="site-subheader" id="site-subheader">
        <div class="site-subheader-inner">
            <div class="site-subheader-nav">
                @include('partials.gallery-search')
                @include('partials.sort-tabs')
            </div>
            <span class="site-header-divider" aria-hidden="true"></span>
            @include('partials.site-auth')
        </div>
    </div>

    @include('partials.account-restricted-banner')

    <main class="site-main">
        <div class="container">
            @yield('content')
        </div>
    </main>

    @include('partials.site-footer')

    <div class="confirm-dialog hidden" id="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-dialog-text" aria-hidden="true">
        <div class="confirm-dialog-backdrop" data-confirm-dismiss></div>
        <div class="confirm-dialog-panel">
            <span class="confirm-dialog-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v4M12 17h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                </svg>
            </span>
            <p class="confirm-dialog-text" id="confirm-dialog-text"></p>
            <div class="confirm-dialog-actions">
                <button type="button" class="btn btn-secondary" id="confirm-dialog-cancel" data-confirm-dismiss>Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-dialog-confirm">Delete</button>
            </div>
        </div>
    </div>

    <div class="toast-viewport" id="toast-viewport"></div>
    @if (session('toast'))
        @php $toast = session('toast'); @endphp
        <div
            id="toast-init-data"
            hidden
            data-message="{{ $toast['message'] }}"
            data-action-label="{{ $toast['action_label'] ?? '' }}"
            data-action-url="{{ $toast['action_url'] ?? '' }}"
            data-duration="{{ $toast['duration'] ?? 8000 }}"
        ></div>
    @endif

    <script src="{{ asset('js/site-header.js') }}" defer></script>
    <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
    <script src="{{ asset('js/likes.js') }}" defer></script>
    <script src="{{ asset('js/bookmarks.js') }}" defer></script>
    <script src="{{ asset('js/collections.js') }}" defer></script>
    <script src="{{ asset('js/follows.js') }}" defer></script>
    <script src="{{ asset('js/notifications-poll.js') }}" defer></script>
    <script src="{{ asset('js/toast.js') }}" defer></script>
    <script src="{{ asset('js/confirm-dialog.js') }}" defer></script>
    @stack('scripts')
</body>
</html>