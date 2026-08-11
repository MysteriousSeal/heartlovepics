@extends('layouts.app')

@section('title', 'Edit profile — HeartLovePics')

@section('content')
    <section class="profile-edit-page">
        <header class="profile-edit-hero">
            <a href="{{ route('users.show', $user) }}" class="profile-edit-back">&larr; Back to profile</a>
            <p class="profile-edit-kicker">Account</p>
            <h1 class="profile-edit-title">Edit profile</h1>
            <p class="profile-edit-lede">
                Update how you appear on HeartLovePics — username, photos, bio, and password.
            </p>
            <div class="profile-edit-meta">
                <span class="profile-edit-meta-chip">
                    {{ '@'.$user->username }}
                </span>
                <a href="{{ route('users.show', $user) }}" class="profile-edit-meta-link">View public profile</a>
            </div>
        </header>

        @include('partials.flash')

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-edit-form">
            @csrf
            @method('PUT')

            <section class="profile-edit-card" aria-labelledby="profile-edit-identity">
                <header class="profile-edit-card-header">
                    <h2 id="profile-edit-identity" class="profile-edit-card-title">Identity</h2>
                    <p class="profile-edit-card-desc">Your public handle and profile URL.</p>
                </header>
                <div class="profile-edit-card-body">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            value="{{ old('username', $user->username) }}"
                            required
                            minlength="3"
                            maxlength="30"
                            autocomplete="username"
                            data-check-url="{{ route('profile.check-username') }}"
                            data-current-username="{{ $user->username }}"
                        >
                        <p class="username-availability-status" id="username-availability" aria-live="polite"></p>
                        <p class="form-hint">Letters, numbers, dashes, and underscores only. Changing this changes your profile URL — old links to your current profile will stop working.</p>
                        @error('username')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="profile-edit-card" aria-labelledby="profile-edit-appearance">
                <header class="profile-edit-card-header">
                    <h2 id="profile-edit-appearance" class="profile-edit-card-title">Appearance</h2>
                    <p class="profile-edit-card-desc">Banner and profile picture shown on your page.</p>
                </header>
                <div class="profile-edit-card-body">
                    <div class="profile-edit-media-grid">
                        <div class="form-group profile-edit-banner-group">
                            <label for="banner">Profile banner</label>
                            <div class="profile-edit-banner-preview">
                                @if ($user->hasBanner())
                                    <img src="{{ $user->banner_url }}" alt="" width="1600" height="800">
                                @else
                                    <div class="profile-edit-banner-empty">No banner set</div>
                                @endif
                            </div>
                            <input
                                type="file"
                                id="banner"
                                name="banner"
                                class="form-control"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                            >
                            <p class="form-hint">Wide image recommended (1600&times;800, 2:1). Leave empty to keep your current banner.</p>
                            @error('banner')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group profile-edit-avatar-group">
                            <label for="avatar">Profile picture</label>
                            <div class="profile-edit-avatar-preview">
                                @include('partials.user-avatar', [
                                    'user' => $user,
                                    'width' => 112,
                                    'height' => 112,
                                ])
                            </div>
                            <input
                                type="file"
                                id="avatar"
                                name="avatar"
                                class="form-control"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                            >
                            <p class="form-hint">Square works best. Leave empty to keep your current picture.</p>
                            @error('avatar')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="profile-edit-card" aria-labelledby="profile-edit-about">
                <header class="profile-edit-card-header">
                    <h2 id="profile-edit-about" class="profile-edit-card-title">About you</h2>
                    <p class="profile-edit-card-desc">A short bio shown on your public profile.</p>
                </header>
                <div class="profile-edit-card-body">
                    <div class="form-group description-editor-group">
                        <label for="bio-editor">Bio</label>
                        <div
                            id="bio-editor"
                            class="description-editor bio-editor"
                            aria-label="Bio editor"
                        ></div>
                        <textarea
                            id="bio"
                            name="bio"
                            class="description-editor-source"
                            hidden
                            placeholder="A short intro about you"
                        >{{ old('bio', $user->bio) }}</textarea>
                        <p
                            class="form-char-count"
                            id="bio-char-count"
                            data-max-length="{{ \App\Http\Requests\UpdateProfileRequest::BIO_MAX_LENGTH }}"
                            aria-live="polite"
                        >0 / {{ \App\Http\Requests\UpdateProfileRequest::BIO_MAX_LENGTH }}</p>
                        <p class="form-hint">Rich text editor — bold, links, and lists are kept. HTML is cleaned for safety.</p>
                        @error('bio')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="profile-edit-card" aria-labelledby="profile-edit-security">
                <header class="profile-edit-card-header">
                    <h2 id="profile-edit-security" class="profile-edit-card-title">Password</h2>
                    <p class="profile-edit-card-desc">Optional — leave blank to keep your current password.</p>
                </header>
                <div class="profile-edit-card-body">
                    <div class="profile-edit-password-grid">
                        <div class="form-group">
                            <label for="password">New password</label>
                            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password">
                            @error('password')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm new password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </section>

            <div class="profile-edit-actions form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
@endsection

@push('meta')
    <link rel="stylesheet" href="{{ asset('css/vendor/quill.snow.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/vendor/quill.js') }}"></script>
    <script src="{{ asset('js/bio-editor.js') }}" defer></script>
    <script src="{{ asset('js/username-availability.js') }}" defer></script>
@endpush
