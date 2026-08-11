@extends('layouts.admin')

@section('title', 'Configuration')

@section('content')
    <div class="admin-config-page">
        <header class="admin-config-hero">
            <p class="admin-config-kicker">Site settings</p>
            <h2 class="admin-config-title">Configuration</h2>
            <p class="admin-config-lede">
                Tune public gallery behavior and other site-wide options. Changes apply immediately after save.
            </p>
            <div class="admin-config-meta">
                <span class="admin-config-chip {{ $showGalleryAuthors ? 'is-on' : 'is-off' }}">
                    Authors {{ $showGalleryAuthors ? 'on' : 'off' }}
                </span>
                <span class="admin-config-chip {{ $showGalleryArtists ? 'is-on' : 'is-off' }}">
                    Artists {{ $showGalleryArtists ? 'on' : 'off' }}
                </span>
                <a href="{{ route('home') }}" class="admin-config-meta-link" target="_blank" rel="noopener noreferrer">
                    View gallery
                </a>
            </div>
        </header>

        <form method="POST" action="{{ route('admin.configuration.update') }}" class="admin-config-form">
            @csrf
            @method('PUT')

            <section class="admin-config-card" aria-labelledby="gallery-settings-heading">
                <header class="admin-config-card-header">
                    <div class="admin-config-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="0.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="0.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="0.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="0.5"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="gallery-settings-heading" class="admin-config-card-title">Gallery</h3>
                        <p class="admin-config-card-desc">
                            Control what credit lines appear on public gallery cards.
                        </p>
                    </div>
                </header>

                <div class="admin-config-card-body">
                    <div class="admin-config-options" role="group" aria-label="Gallery credit options">
                        <label class="admin-config-option">
                            <span class="admin-config-option-copy">
                                <span class="admin-config-option-label">Show post author</span>
                                <span class="admin-config-option-desc">
                                    Display the uploader on each gallery card with a link to their profile.
                                    When off, cards show title, description, date, and stats only.
                                </span>
                            </span>
                            <span class="form-switch admin-config-option-switch">
                                <input
                                    type="checkbox"
                                    name="show_gallery_authors"
                                    value="1"
                                    @checked(old('show_gallery_authors', $showGalleryAuthors))
                                >
                                <span class="form-switch-track" aria-hidden="true"></span>
                            </span>
                        </label>

                        <label class="admin-config-option">
                            <span class="admin-config-option-copy">
                                <span class="admin-config-option-label">Show original artist</span>
                                <span class="admin-config-option-desc">
                                    Display credited artists on gallery cards, including their avatar when available.
                                </span>
                            </span>
                            <span class="form-switch admin-config-option-switch">
                                <input
                                    type="checkbox"
                                    name="show_gallery_artists"
                                    value="1"
                                    @checked(old('show_gallery_artists', $showGalleryArtists))
                                >
                                <span class="form-switch-track" aria-hidden="true"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </section>

            <div class="admin-config-actions">
                <p class="admin-config-actions-hint">Saved settings apply site-wide for all visitors.</p>
                <button type="submit" class="btn btn-primary">Save configuration</button>
            </div>
        </form>
    </div>
@endsection
