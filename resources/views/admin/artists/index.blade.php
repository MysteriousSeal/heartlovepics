@extends('layouts.admin')

@section('title', 'Artists')

@section('content')
    <div class="admin-list-page">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Credits</p>
            <h2 class="admin-list-title">Artists</h2>
            <p class="admin-list-lede">
                Artist profiles are shared across every post that credits that name — set avatar,
                bio, and links once here instead of on each post.
            </p>
            @if (! $artists->isEmpty())
                <div class="admin-list-meta">
                    <span class="admin-list-chip">
                        {{ number_format($artists->total()) }}
                        {{ \Illuminate\Support\Str::plural('artist', $artists->total()) }}
                    </span>
                    @if (request()->filled('search'))
                        <span class="admin-list-chip is-filtered">Filtered</span>
                    @endif
                </div>
            @endif
        </header>

        <section class="admin-artist-add-card admin-config-card" aria-labelledby="admin-artist-add-heading">
            <header class="admin-config-card-header">
                <div class="admin-config-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </div>
                <div>
                    <h3 id="admin-artist-add-heading" class="admin-config-card-title">Add artist</h3>
                    <p class="admin-config-card-desc">
                        Create a credit profile with optional avatar and social links.
                    </p>
                </div>
            </header>
            <div class="admin-config-card-body admin-artist-add-body">
                <form method="POST" action="{{ route('admin.artists.store') }}" enctype="multipart/form-data" class="admin-artist-add-form">
                    @csrf
                    <div class="form-group admin-artist-add-avatar-group">
                        <label for="new-artist-avatar">Avatar</label>
                        <div class="admin-artist-add-avatar-row">
                            <span class="admin-artist-avatar-placeholder" style="--avatar-color: hsl(220, 35%, 55%)" aria-hidden="true">?</span>
                            <input
                                type="file"
                                id="new-artist-avatar"
                                name="avatar"
                                class="form-control"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                            >
                        </div>
                        @error('avatar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="new-artist-name">Name</label>
                        <input
                            type="text"
                            id="new-artist-name"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            maxlength="100"
                            required
                            placeholder="Display name"
                        >
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="new-artist-deviantart">DeviantArt</label>
                        <input
                            type="url"
                            id="new-artist-deviantart"
                            name="deviantart_url"
                            class="form-control"
                            value="{{ old('deviantart_url') }}"
                            placeholder="https://www.deviantart.com/…"
                            maxlength="255"
                        >
                    </div>
                    <div class="form-group">
                        <label for="new-artist-furaffinity">FurAffinity</label>
                        <input
                            type="url"
                            id="new-artist-furaffinity"
                            name="furaffinity_url"
                            class="form-control"
                            value="{{ old('furaffinity_url') }}"
                            placeholder="https://www.furaffinity.net/user/…"
                            maxlength="255"
                        >
                    </div>
                    <div class="form-group">
                        <label for="new-artist-patreon">Patreon</label>
                        <input
                            type="url"
                            id="new-artist-patreon"
                            name="patreon_url"
                            class="form-control"
                            value="{{ old('patreon_url') }}"
                            placeholder="https://www.patreon.com/…"
                            maxlength="255"
                        >
                    </div>
                    <div class="admin-artist-add-submit">
                        <button type="submit" class="btn btn-primary">Add artist</button>
                    </div>
                </form>
            </div>
        </section>

        <form method="GET" action="{{ route('admin.artists.index') }}" class="admin-list-toolbar filter-bar">
            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="Search by name…"
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if (request()->filled('search'))
                <a href="{{ route('admin.artists.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>

        @if ($artists->isEmpty())
            <div class="admin-list-empty empty-state">
                @if (request()->filled('search'))
                    <p>No artists match this search.</p>
                    <p><a href="{{ route('admin.artists.index') }}">Clear search</a></p>
                @else
                    <p>No artists yet. Add one above, or credit an artist on a post — it&rsquo;ll show up here.</p>
                @endif
            </div>
        @else
            <p class="admin-result-count">
                Showing {{ $artists->firstItem() }}&ndash;{{ $artists->lastItem() }} of {{ number_format($artists->total()) }}
                {{ \Illuminate\Support\Str::plural('artist', $artists->total()) }}
            </p>

            <div class="admin-artists-table admin-list-table">
                <div class="admin-artists-head" aria-hidden="true">
                    <span>Artist</span>
                    <span>Links</span>
                    <span>Posts</span>
                    <span></span>
                </div>

                <ul class="admin-artists-list">
                    @foreach ($artists as $artist)
                        <li class="admin-artist-row">
                            <div class="admin-artist-identity" data-label="Artist">
                                <span class="admin-artist-avatar-preview">
                                    @if ($artist->hasAvatar())
                                        <img src="{{ $artist->avatar_url }}" alt="" width="40" height="40">
                                    @else
                                        <span class="admin-artist-avatar-placeholder" style="--avatar-color: {{ $artist->avatar_color }}">{{ $artist->avatar_initials }}</span>
                                    @endif
                                </span>
                                <div class="admin-artist-identity-text">
                                    <span class="admin-artist-name">{{ $artist->name }}</span>
                                    @if ($artist->hasDescription())
                                        <span class="admin-artist-meta-note">Has description</span>
                                    @endif
                                </div>
                            </div>

                            <div class="admin-artist-links" data-label="Links">
                                @if ($artist->deviantart_url)
                                    <a href="{{ $artist->deviantart_url }}" class="admin-artist-link-chip" target="_blank" rel="noopener noreferrer nofollow">DeviantArt</a>
                                @endif
                                @if ($artist->furaffinity_url)
                                    <a href="{{ $artist->furaffinity_url }}" class="admin-artist-link-chip" target="_blank" rel="noopener noreferrer nofollow">FurAffinity</a>
                                @endif
                                @if ($artist->patreon_url)
                                    <a href="{{ $artist->patreon_url }}" class="admin-artist-link-chip" target="_blank" rel="noopener noreferrer nofollow">Patreon</a>
                                @endif
                                @unless ($artist->hasLinks())
                                    <span class="admin-artist-links-empty">&mdash;</span>
                                @endunless
                            </div>

                            <span class="admin-artist-posts-count" data-label="Posts">
                                @if ($artist->images_count > 0)
                                    <a href="{{ route('gallery.artist', $artist->name) }}" target="_blank" rel="noopener noreferrer">{{ number_format($artist->images_count) }}</a>
                                @else
                                    0
                                @endif
                            </span>

                            <div class="admin-artist-actions">
                                <a href="{{ route('admin.artists.edit', $artist) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.artists.destroy', $artist) }}"
                                    class="delete-form"
                                    data-confirm="Delete artist &quot;{{ $artist->name }}&quot;? Posts crediting them keep the name, just without links."
                                    data-confirm-label="Delete"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($artists->hasPages())
                <div class="pagination">
                    @if ($artists->onFirstPage())
                        <span>&laquo;</span>
                    @else
                        <a href="{{ $artists->previousPageUrl() }}">&laquo;</a>
                    @endif

                    @foreach ($artists->getUrlRange(1, $artists->lastPage()) as $page => $url)
                        @if ($page == $artists->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($artists->hasMorePages())
                        <a href="{{ $artists->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span>&raquo;</span>
                    @endif
                </div>
            @endif
        @endif
    </div>
@endsection
