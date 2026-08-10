@extends('layouts.app')

@section('title', 'Register — HeartLovePics')

@section('content')
    <div class="auth-page">
        <div class="auth-card">
            <h2>Create account</h2>
            <p class="auth-card-intro">Pick a username to share images, build a profile, and save your likes.</p>

            @include('partials.flash')

            <form method="POST" action="{{ route('register.submit') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="username-input-row">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            maxlength="30"
                        >
                        <button
                            type="button"
                            id="generate-username-btn"
                            class="btn btn-secondary"
                            data-suggest-url="{{ route('register.suggest-username') }}"
                        >
                            Generate
                        </button>
                    </div>
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
                        autocomplete="new-password"
                    >
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="terms" value="1" required @checked(old('terms'))>
                        I agree to the <a href="{{ route('pages.terms') }}" target="_blank" rel="noopener noreferrer">Terms of Use</a>
                        and <a href="{{ route('pages.privacy') }}" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
                    </label>
                    @error('terms')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>

            <p class="auth-card-footer">
                Already have an account?
                <a href="{{ route('login') }}">Sign in</a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/username-generator.js') }}" defer></script>
@endpush