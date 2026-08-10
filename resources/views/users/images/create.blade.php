@extends('layouts.app')

@section('title', 'Upload Image — HeartLovePics')

@section('content')
    <div class="page-header">
        <h2>Upload Image</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary">Back</a>
    </div>

    @include('partials.flash')

    <div
        class="upload-progress-modal hidden"
        id="upload-progress"
        role="dialog"
        aria-modal="true"
        aria-labelledby="upload-progress-label"
    >
        <div class="upload-progress-backdrop"></div>
        <div class="upload-progress-panel">
            <p class="upload-progress-label" id="upload-progress-label">Preparing upload…</p>
            <progress class="upload-progress-bar" id="upload-progress-bar" max="100" value="0"></progress>
            <p class="upload-progress-hint">Don't close this tab while your images upload.</p>
        </div>
    </div>

    <div class="admin-form-layout">
        <div class="form-card">
            <form
                method="POST"
                action="{{ route('profile.images.store') }}"
                enctype="multipart/form-data"
                data-ajax-upload="1"
            >
                @csrf
                @include('partials.image-form', ['userUpload' => true])

                <div class="form-actions">
                    <button type="submit" name="publish_action" value="publish" class="btn btn-primary">Publish</button>
                    @auth
                        <button type="submit" name="publish_action" value="draft" class="btn btn-secondary">Save draft</button>
                    @endauth
                    <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        @include('partials.admin-image-preview')
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
    <script src="{{ asset('js/image-upload.js') }}" defer></script>
@endpush