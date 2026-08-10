@extends('layouts.admin')

@section('title', 'Edit Image')

@section('content')
    <div class="page-header">
        <h2>Edit Image</h2>
        <a href="{{ route('admin.images.index') }}" class="btn btn-secondary">Back to Images</a>
    </div>

    <div class="admin-form-layout">
        <div class="form-card">
            <form
                method="POST"
                action="{{ route('admin.images.update', $image) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')
                @include('partials.image-form', ['image' => $image, 'allowImageUpload' => true, 'userUpload' => false])

                <p class="image-admin-meta">
                    Slug: <code>{{ $image->slug }}</code> ·
                    Created {{ $image->created_at->format('M j, Y g:i A') }} ·
                    Updated {{ $image->updated_at->format('M j, Y g:i A') }}
                    @if ($image->user)
                        · Owner: <a href="{{ route('users.show', $image->user) }}">{{ '@'.$image->user->username }}</a>
                    @endif
                </p>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.images.index') }}" class="btn btn-secondary">Cancel</a>
                    <a href="{{ route('images.show', $image->slug) }}" class="btn btn-secondary" target="_blank" rel="noopener noreferrer">View post</a>
                </div>
            </form>
        </div>

        @include('partials.admin-image-preview', ['image' => $image, 'allowImageUpload' => true])
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vendor/quill.snow.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/vendor/quill.js') }}"></script>
    <script src="{{ asset('js/description-editor.js') }}" defer></script>
    <script src="{{ asset('js/admin-image-form.js') }}" defer></script>
    <script src="{{ asset('js/admin-image-field-limits.js') }}" defer></script>
    <script src="{{ asset('js/admin-image-tags.js') }}" defer></script>
    <script src="{{ asset('js/additional-images.js') }}" defer></script>
@endpush
