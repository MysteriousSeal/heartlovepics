@extends('layouts.app')

@section('title', 'Login — HeartLovePics')

@section('content')
    <div class="auth-page">
        <div class="auth-card">
            <h2>Sign in</h2>
            <p class="auth-card-intro">Use your username to access your profile and likes.</p>

            @include('partials.flash')

            <form method="POST" action="{{ route('login.submit') }}" class="auth-form">
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
                        <input type="checkbox" name="remember" value="1" @checked(old('remember', true))>
                        Keep me signed in
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
            </form>

            <p class="auth-card-footer">
                No account yet?
                <a href="{{ route('register') }}">Create one</a>
            </p>
        </div>
    </div>
@endsection