@extends('layouts.admin')

@section('title', 'Edit Artist')

@section('content')
    <div class="page-header">
        <h2>Edit Artist</h2>
        <a href="{{ route('admin.artists.index') }}" class="btn btn-secondary">Back to Artists</a>
    </div>

    <div class="form-card admin-artist-edit-card">
        <form method="POST" action="{{ route('admin.artists.update', $artist) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="artist-avatar">Avatar</label>
                <div class="admin-artist-add-avatar-row">
                    <span class="admin-artist-avatar-preview">
                        @if ($artist->hasAvatar())
                            <img src="{{ $artist->avatar_url }}" alt="" width="36" height="36">
                        @else
                            <span class="admin-artist-avatar-placeholder" style="--avatar-color: {{ $artist->avatar_color }}">{{ $artist->avatar_initials }}</span>
                        @endif
                    </span>
                    <input
                        type="file"
                        id="artist-avatar"
                        name="avatar"
                        class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                    >
                </div>
                @if ($artist->hasAvatar())
                    <label class="admin-artist-avatar-remove">
                        <input type="checkbox" name="remove_avatar" value="1"> Remove current avatar
                    </label>
                @endif
                @error('avatar')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="artist-name">Name</label>
                <input
                    type="text"
                    id="artist-name"
                    name="name"
                    class="form-control"
                    value="{{ old('name', $artist->name) }}"
                    maxlength="100"
                    required
                >
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group description-editor-group">
                <label for="description-editor">Description</label>
                <div
                    id="description-editor"
                    class="description-editor"
                    aria-label="Description editor"
                ></div>
                <textarea
                    id="description"
                    name="description"
                    class="description-editor-source"
                    placeholder="Write something about this artist, or paste HTML…"
                    rows="12"
                >{{ old('description', $artist->description) }}</textarea>
                <p class="form-hint">
                    Shown on this artist&rsquo;s public page. Use <strong>Visual</strong> for the
                    rich editor, or <strong>HTML</strong> to paste/edit markup. Unsafe tags are
                    stripped on save. Optional.
                </p>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="artist-deviantart">DeviantArt</label>
                <input
                    type="url"
                    id="artist-deviantart"
                    name="deviantart_url"
                    class="form-control"
                    value="{{ old('deviantart_url', $artist->deviantart_url) }}"
                    placeholder="https://www.deviantart.com/username"
                    maxlength="255"
                >
                @error('deviantart_url')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="artist-furaffinity">FurAffinity</label>
                <input
                    type="url"
                    id="artist-furaffinity"
                    name="furaffinity_url"
                    class="form-control"
                    value="{{ old('furaffinity_url', $artist->furaffinity_url) }}"
                    placeholder="https://www.furaffinity.net/user/username"
                    maxlength="255"
                >
                @error('furaffinity_url')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="artist-patreon">Patreon</label>
                <input
                    type="url"
                    id="artist-patreon"
                    name="patreon_url"
                    class="form-control"
                    value="{{ old('patreon_url', $artist->patreon_url) }}"
                    placeholder="https://www.patreon.com/username"
                    maxlength="255"
                >
                @error('patreon_url')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            @php $postCount = $artist->images()->count(); @endphp
            <p class="admin-artist-edit-stats">
                {{ number_format($postCount) }}
                {{ \Illuminate\Support\Str::plural('post', $postCount) }} credited to this artist
                @if ($postCount > 0)
                    · <a href="{{ route('gallery.artist', $artist->name) }}" target="_blank" rel="noopener noreferrer">View public page</a>
                @endif
            </p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('admin.artists.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vendor/quill.snow.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/vendor/quill.js') }}"></script>
    <script src="{{ asset('js/description-editor.js') }}" defer></script>
@endpush
