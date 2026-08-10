@extends('layouts.admin')

@section('title', 'Artists')

@section('content')
    <div class="page-header">
        <h2>Artists</h2>
    </div>

    <p class="admin-artists-intro">
        Artist links are shared across every post that credits that name — set them once here
        instead of on each post.
    </p>

    <div class="form-card admin-artist-add-card">
        <h3 class="admin-artist-add-title">Add artist</h3>
        <form method="POST" action="{{ route('admin.artists.store') }}" class="admin-artist-add-form">
            @csrf
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
                    placeholder="https://www.deviantart.com/username"
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
                    placeholder="https://www.furaffinity.net/user/username"
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
                    placeholder="https://www.patreon.com/username"
                    maxlength="255"
                >
            </div>
            <button type="submit" class="btn btn-primary">Add artist</button>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.artists.index') }}" class="filter-bar">
        <input
            type="search"
            name="search"
            class="form-control"
            placeholder="Search by name…"
            value="{{ request('search') }}"
        >
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if (request()->hasAny(['search']))
            <a href="{{ route('admin.artists.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    @if ($artists->isEmpty())
        <div class="empty-state">
            @if (request()->filled('search'))
                <p>No artists match this search.</p>
                <p><a href="{{ route('admin.artists.index') }}">Clear search</a></p>
            @else
                <p>No artists yet. Add one above, or credit an artist on a post — it'll show up here.</p>
            @endif
        </div>
    @else
        <p class="admin-result-count">
            Showing {{ $artists->firstItem() }}&ndash;{{ $artists->lastItem() }} of {{ number_format($artists->total()) }}
            {{ \Illuminate\Support\Str::plural('artist', $artists->total()) }}
        </p>

        <div class="admin-artists-table">
            <div class="admin-artists-head" aria-hidden="true">
                <span>Name</span>
                <span>DeviantArt</span>
                <span>FurAffinity</span>
                <span>Patreon</span>
                <span>Posts</span>
                <span></span>
                <span></span>
            </div>

            <ul class="admin-artists-list">
                @foreach ($artists as $artist)
                    <li class="admin-artist-row">
                        <form
                            method="POST"
                            action="{{ route('admin.artists.update', $artist) }}"
                            class="admin-artist-row-form"
                        >
                            @csrf
                            @method('PUT')
                            <span data-label="Name">
                                <input type="text" name="name" class="form-control" value="{{ old('name', $artist->name) }}" maxlength="100" required>
                            </span>
                            <span data-label="DeviantArt">
                                <input type="url" name="deviantart_url" class="form-control" value="{{ old('deviantart_url', $artist->deviantart_url) }}" placeholder="https://www.deviantart.com/username" maxlength="255">
                            </span>
                            <span data-label="FurAffinity">
                                <input type="url" name="furaffinity_url" class="form-control" value="{{ old('furaffinity_url', $artist->furaffinity_url) }}" placeholder="https://www.furaffinity.net/user/username" maxlength="255">
                            </span>
                            <span data-label="Patreon">
                                <input type="url" name="patreon_url" class="form-control" value="{{ old('patreon_url', $artist->patreon_url) }}" placeholder="https://www.patreon.com/username" maxlength="255">
                            </span>
                            <span class="admin-artist-posts-count" data-label="Posts">
                                @if ($artist->images_count > 0)
                                    <a href="{{ route('gallery.artist', $artist->name) }}" target="_blank" rel="noopener noreferrer">{{ number_format($artist->images_count) }}</a>
                                @else
                                    0
                                @endif
                            </span>
                            <span class="admin-artist-actions">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </span>
                        </form>
                        <form
                            method="POST"
                            action="{{ route('admin.artists.destroy', $artist) }}"
                            class="admin-artist-delete-form delete-form"
                            data-confirm="Delete artist &quot;{{ $artist->name }}&quot;? Posts crediting them keep the name, just without links."
                            data-confirm-label="Delete"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
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
@endsection
