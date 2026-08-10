@extends('layouts.admin')

@section('title', 'Images')

@section('content')
    <div class="page-header">
        <h2>Images</h2>
    </div>

    <form method="GET" action="{{ route('admin.images.index') }}" class="filter-bar">
        <input
            type="search"
            name="search"
            class="form-control"
            placeholder="Search by title, description, or slug…"
            value="{{ request('search') }}"
        >
        <select name="status" class="form-control">
            <option value="">All statuses</option>
            <option value="published" @selected(request('status') === 'published')>Published</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>
        <select name="nsfw" class="form-control">
            <option value="">All content</option>
            <option value="yes" @selected(request('nsfw') === 'yes')>NSFW only</option>
            <option value="no" @selected(request('nsfw') === 'no')>SFW only</option>
        </select>
        <select name="sort" class="form-control">
            <option value="" @selected(($sort ?? '') === '')>Newest first</option>
            <option value="oldest" @selected(($sort ?? '') === 'oldest')>Oldest first</option>
            <option value="views" @selected(($sort ?? '') === 'views')>Most viewed</option>
            <option value="likes" @selected(($sort ?? '') === 'likes')>Most liked</option>
            <option value="comments" @selected(($sort ?? '') === 'comments')>Most commented</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if (request()->hasAny(['search', 'status', 'nsfw', 'sort']))
            <a href="{{ route('admin.images.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    @if ($images->isEmpty())
        <div class="empty-state">
            @if (request()->hasAny(['search', 'status', 'nsfw']))
                <p>No images match these filters.</p>
                <p><a href="{{ route('admin.images.index') }}">Clear filters</a></p>
            @else
                <p>No images found.</p>
            @endif
        </div>
    @else
        <p class="admin-result-count">
            Showing {{ $images->firstItem() }}&ndash;{{ $images->lastItem() }} of {{ number_format($images->total()) }}
            {{ \Illuminate\Support\Str::plural('image', $images->total()) }}
        </p>

        <div class="admin-grid">
            @foreach ($images as $image)
                @include('partials.admin-image-card', ['image' => $image])
            @endforeach
        </div>

        @if ($images->hasPages())
            <div class="pagination">
                @if ($images->onFirstPage())
                    <span>&laquo;</span>
                @else
                    <a href="{{ $images->previousPageUrl() }}">&laquo;</a>
                @endif

                @foreach ($images->getUrlRange(1, $images->lastPage()) as $page => $url)
                    @if ($page == $images->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($images->hasMorePages())
                    <a href="{{ $images->nextPageUrl() }}">&raquo;</a>
                @else
                    <span>&raquo;</span>
                @endif
            </div>
        @endif
    @endif
@endsection
