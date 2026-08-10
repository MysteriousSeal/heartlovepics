@extends('layouts.app')

@section('title', 'Edit profile — HeartLovePics')

@section('content')
    <section class="user-profile-edit">
        <a href="{{ route('users.show', $user) }}" class="back-link">&larr; Back to profile</a>

        <h2>Edit profile</h2>

        @include('partials.flash')

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-edit-form">
            @csrf
            @method('PUT')

            <div class="form-group">
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

            <div class="form-group">
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
                <p class="form-hint">Leave empty to keep your current picture.</p>
                @error('avatar')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

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

            <div class="form-actions">
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
@endpush