@extends('layouts.admin')

@section('title', 'Parodies')

@section('content')
    <div class="admin-list-page">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Credits</p>
            <h2 class="admin-list-title">Parodies</h2>
            <p class="admin-list-lede">
                Parody profiles are shared across every post that credits that name — set a cover
                image and description once here instead of on each post.
            </p>
            @if (! $parodies->isEmpty())
                <div class="admin-list-meta">
                    <span class="admin-list-chip">
                        {{ number_format($parodies->total()) }}
                        {{ \Illuminate\Support\Str::plural('parody', $parodies->total()) }}
                    </span>
                    @if (request()->filled('search'))
                        <span class="admin-list-chip is-filtered">Filtered</span>
                    @endif
                </div>
            @endif
        </header>

        <section class="admin-artist-add-card admin-config-card" aria-labelledby="admin-parody-add-heading">
            <header class="admin-config-card-header">
                <div class="admin-config-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </div>
                <div>
                    <h3 id="admin-parody-add-heading" class="admin-config-card-title">Add parody</h3>
                    <p class="admin-config-card-desc">
                        Create a credit profile with an optional cover image and description.
                    </p>
                </div>
            </header>
            <div class="admin-config-card-body admin-artist-add-body">
                <form method="POST" action="{{ route('admin.parodies.store') }}" enctype="multipart/form-data" class="admin-artist-add-form">
                    @csrf
                    <div class="form-group admin-artist-add-avatar-group">
                        <label for="new-parody-cover">Cover image</label>
                        <div class="admin-artist-add-avatar-row">
                            <span class="admin-parody-cover-placeholder" style="--avatar-color: hsl(220, 35%, 55%)" aria-hidden="true">?</span>
                            <input
                                type="file"
                                id="new-parody-cover"
                                name="cover"
                                class="form-control"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                            >
                        </div>
                        <p class="form-hint">Cropped to a 2:3 portrait (800×1200).</p>
                        @error('cover')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="new-parody-name">Name</label>
                        <input
                            type="text"
                            id="new-parody-name"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            maxlength="100"
                            required
                            placeholder="e.g. Stardew Valley"
                        >
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="admin-artist-add-submit">
                        <button type="submit" class="btn btn-primary">Add parody</button>
                    </div>
                </form>
            </div>
        </section>

        @if (! $pendingParodies->isEmpty())
            <section class="admin-config-card admin-parody-pending-card" aria-labelledby="admin-parody-pending-heading">
                <header class="admin-config-card-header">
                    <div class="admin-config-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 8v5l3 2"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="admin-parody-pending-heading" class="admin-config-card-title">
                            Pending
                            <span class="admin-list-tab-count">{{ $pendingParodies->count() }}</span>
                        </h3>
                        <p class="admin-config-card-desc">
                            Typed in on a post but not yet turned into a real profile. Validate one
                            to give it a cover image and description.
                        </p>
                    </div>
                </header>
                <div class="admin-config-card-body">
                    <ul class="admin-parody-pending-list">
                        @foreach ($pendingParodies as $pending)
                            <li class="admin-parody-pending-row">
                                <span class="admin-parody-pending-name">{{ $pending['name'] }}</span>
                                <span class="admin-parody-pending-count">
                                    {{ number_format($pending['count']) }}
                                    {{ \Illuminate\Support\Str::plural('post', $pending['count']) }}
                                </span>
                                <form method="POST" action="{{ route('admin.parodies.store') }}">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $pending['name'] }}">
                                    <button type="submit" class="btn btn-sm btn-primary">Validate</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        <form method="GET" action="{{ route('admin.parodies.index') }}" class="admin-list-toolbar filter-bar">
            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="Search by name…"
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if (request()->filled('search'))
                <a href="{{ route('admin.parodies.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>

        @if ($parodies->isEmpty())
            <div class="admin-list-empty empty-state">
                @if (request()->filled('search'))
                    <p>No parodies match this search.</p>
                    <p><a href="{{ route('admin.parodies.index') }}">Clear search</a></p>
                @else
                    <p>No parodies yet. Add one above, or credit a parody on a post — it&rsquo;ll show up here.</p>
                @endif
            </div>
        @else
            <p class="admin-result-count">
                Showing {{ $parodies->firstItem() }}&ndash;{{ $parodies->lastItem() }} of {{ number_format($parodies->total()) }}
                {{ \Illuminate\Support\Str::plural('parody', $parodies->total()) }}
            </p>

            <div class="admin-parodies-table admin-list-table">
                <div class="admin-parodies-head" aria-hidden="true">
                    <span>Parody</span>
                    <span>Posts</span>
                    <span></span>
                </div>

                <ul class="admin-parodies-list">
                    @foreach ($parodies as $parody)
                        <li class="admin-parody-row">
                            <div class="admin-artist-identity" data-label="Parody">
                                <span class="admin-artist-avatar-preview">
                                    @if ($parody->hasCover())
                                        <img src="{{ $parody->cover_url }}" alt="" width="40" height="40">
                                    @else
                                        <span class="admin-artist-avatar-placeholder" style="--avatar-color: {{ $parody->cover_color }}">{{ $parody->cover_initials }}</span>
                                    @endif
                                </span>
                                <div class="admin-artist-identity-text">
                                    <span class="admin-artist-name">{{ $parody->name }}</span>
                                    @if ($parody->hasDescription())
                                        <span class="admin-artist-meta-note">Has description</span>
                                    @endif
                                </div>
                            </div>

                            <span class="admin-artist-posts-count" data-label="Posts">
                                @if ($parody->images_count > 0)
                                    <a href="{{ route('gallery.parody', $parody->name) }}" target="_blank" rel="noopener noreferrer">{{ number_format($parody->images_count) }}</a>
                                @else
                                    0
                                @endif
                            </span>

                            <div class="admin-artist-actions">
                                <a href="{{ route('admin.parodies.edit', $parody) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.parodies.destroy', $parody) }}"
                                    class="delete-form"
                                    data-confirm="Delete parody &quot;{{ $parody->name }}&quot;? Posts crediting it keep the name, just without the cover or description."
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

            @if ($parodies->hasPages())
                <div class="pagination">
                    @if ($parodies->onFirstPage())
                        <span>&laquo;</span>
                    @else
                        <a href="{{ $parodies->previousPageUrl() }}">&laquo;</a>
                    @endif

                    @foreach ($parodies->getUrlRange(1, $parodies->lastPage()) as $page => $url)
                        @if ($page == $parodies->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($parodies->hasMorePages())
                        <a href="{{ $parodies->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span>&raquo;</span>
                    @endif
                </div>
            @endif
        @endif
    </div>
@endsection
