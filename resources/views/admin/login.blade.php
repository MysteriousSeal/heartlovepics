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
    <title>Admin Login — HeartLovePics</title>
    @include('partials.favicon')
    @include('partials.font-preload')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <button
        type="button"
        class="theme-toggle-btn login-page-theme-toggle"
        id="theme-toggle"
        data-theme="{{ $theme }}"
        title="Toggle dark mode"
        aria-label="Toggle dark mode"
    >
        <span class="theme-toggle-icon theme-toggle-icon-sun" aria-hidden="true">☀</span>
        <span class="theme-toggle-icon theme-toggle-icon-moon" aria-hidden="true">☾</span>
    </button>

    <div class="login-page">
        <div class="login-card">
            <h1>HeartLovePics</h1>
            <p class="subtitle">Admin sign in</p>

            @include('partials.flash')

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        autocomplete="username"
                    >
                    @error('username')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required
                        autocomplete="current-password"
                    >
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="remember" value="1">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
</body>
</html>