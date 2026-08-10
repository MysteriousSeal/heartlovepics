@extends('layouts.app')

@section('title', 'Edit Image — HeartLovePics')

@section('content')
    <div class="page-header">
        <h2>Edit Image</h2>
        <a href="{{ route('images.show', $image->slug) }}" class="btn btn-secondary">Back to image</a>
    </div>

    @include('partials.flash')

    <div class="admin-form-layout">
        <div class="form-card">
            <form method="POST" action="{{ route('profile.images.update', $image) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('partials.image-form', ['image' => $image, 'userUpload' => true])

                <p class="image-admin-meta">
                    Slug: <code>{{ $image->slug }}</code> ·
                    Created {{ $image->created_at->format('M j, Y g:i A') }}
                </p>

                @if (auth()->user()->is_banned && ! $image->is_published)
                    <p class="form-warning">Your account is restricted from publishing images. You can still save this draft.</p>
                @endif

                <div class="form-actions">
                    @if (! auth()->user()->is_banned || $image->is_published)
                        <button type="submit" name="publish_action" value="publish" class="btn btn-primary">
                            {{ $image->is_published ? 'Save changes' : 'Publish' }}
                        </button>
                    @endif
                    @unless ($image->is_published)
                        <button type="submit" name="publish_action" value="draft" class="btn btn-secondary">Save draft</button>
                    @endunless
                    <a href="{{ route('images.show', $image->slug) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            <form
                method="POST"
                action="{{ route('profile.images.destroy', $image) }}"
                class="image-delete-form"
                data-confirm="Delete this image? You can undo this for a short time after."
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete image</button>
            </form>
        </div>

        @include('partials.admin-image-preview', ['image' => $image])
    </div>
@endsection

@push('meta')
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