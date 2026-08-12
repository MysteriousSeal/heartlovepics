@extends('layouts.admin')

@section('title', 'Edit Parody')

@section('content')
    <div class="page-header">
        <h2>Edit Parody</h2>
        <a href="{{ route('admin.parodies.index') }}" class="btn btn-secondary">Back to Parodies</a>
    </div>

    <div class="form-card admin-artist-edit-card">
        <form method="POST" action="{{ route('admin.parodies.update', $parody) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="parody-cover">Cover image</label>
                <div class="admin-artist-add-avatar-row">
                    <span class="admin-artist-avatar-preview">
                        @if ($parody->hasCover())
                            <img src="{{ $parody->cover_url }}" alt="" width="36" height="36">
                        @else
                            <span class="admin-artist-avatar-placeholder" style="--avatar-color: {{ $parody->cover_color }}">{{ $parody->cover_initials }}</span>
                        @endif
                    </span>
                    <input
                        type="file"
                        id="parody-cover"
                        name="cover"
                        class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                    >
                </div>
                @if ($parody->hasCover())
                    <label class="admin-artist-avatar-remove">
                        <input type="checkbox" name="remove_cover" value="1"> Remove current cover
                    </label>
                @endif
                @error('cover')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="parody-name">Name</label>
                <input
                    type="text"
                    id="parody-name"
                    name="name"
                    class="form-control"
                    value="{{ old('name', $parody->name) }}"
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
                    placeholder="Write something about this parody, or paste HTML…"
                    rows="12"
                >{{ old('description', $parody->description) }}</textarea>
                <p class="form-hint">
                    Shown on this parody&rsquo;s public page. Use <strong>Visual</strong> for the
                    rich editor, or <strong>HTML</strong> to paste/edit markup. Unsafe tags are
                    stripped on save. Optional.
                </p>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            @php $postCount = $parody->images()->count(); @endphp
            <p class="admin-artist-edit-stats">
                {{ number_format($postCount) }}
                {{ \Illuminate\Support\Str::plural('post', $postCount) }} credited to this parody
                @if ($postCount > 0)
                    · <a href="{{ route('gallery.parody', $parody->name) }}" target="_blank" rel="noopener noreferrer">View public page</a>
                @endif
            </p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('admin.parodies.index') }}" class="btn btn-secondary">Cancel</a>
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
