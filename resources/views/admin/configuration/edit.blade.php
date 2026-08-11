@extends('layouts.admin')

@section('title', 'Configuration')

@section('content')
    <div class="page-header">
        <h2>Configuration</h2>
    </div>

    <form method="POST" action="{{ route('admin.configuration.update') }}" class="settings-panel">
        @csrf
        @method('PUT')

        <section class="settings-section" aria-labelledby="gallery-settings-heading">
            <div>
                <h3 id="gallery-settings-heading">Gallery</h3>
                <p class="text-muted">Control what appears on the public gallery page.</p>
            </div>

            <label class="form-switch">
                <input
                    type="checkbox"
                    name="show_gallery_authors"
                    value="1"
                    @checked(old('show_gallery_authors', $showGalleryAuthors))
                >
                <span class="form-switch-track" aria-hidden="true"></span>
                <span class="form-switch-text">
                    <span class="form-switch-label">Show post author on gallery page</span>
                    <span class="form-switch-description">When disabled, gallery cards show the title, description, date, and stats without the author link.</span>
                </span>
            </label>

            <label class="form-switch">
                <input
                    type="checkbox"
                    name="show_gallery_artists"
                    value="1"
                    @checked(old('show_gallery_artists', $showGalleryArtists))
                >
                <span class="form-switch-track" aria-hidden="true"></span>
                <span class="form-switch-text">
                    <span class="form-switch-label">Show artist on gallery page</span>
                    <span class="form-switch-description">When enabled, gallery cards show credited artists and their avatar when one is available.</span>
                </span>
            </label>
        </section>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save configuration</button>
        </div>
    </form>
@endsection
