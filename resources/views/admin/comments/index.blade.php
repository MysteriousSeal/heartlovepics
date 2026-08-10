@extends('layouts.admin')

@section('title', 'Comments')

@section('content')
    <div class="page-header">
        <h2>Comments</h2>
    </div>

    <form method="GET" action="{{ route('admin.comments.index') }}" class="filter-bar">
        <input
            type="search"
            name="search"
            class="form-control"
            placeholder="Search by author, body, or image…"
            value="{{ request('search') }}"
        >
        <select name="type" class="form-control">
            <option value="" @selected(! request()->filled('type'))>All comments</option>
            <option value="top" @selected(request('type') === 'top')>Top-level only</option>
            <option value="replies" @selected(request('type') === 'replies')>Replies only</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if (request()->hasAny(['search', 'type']))
            <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    @if ($comments->isEmpty())
        <div class="empty-state">
            @if (request()->hasAny(['search', 'type']))
                <p>No comments match these filters.</p>
                <p><a href="{{ route('admin.comments.index') }}">Clear filters</a></p>
            @else
                <p>No comments found.</p>
            @endif
        </div>
    @else
        <p class="admin-result-count">
            Showing {{ $comments->firstItem() }}&ndash;{{ $comments->lastItem() }} of {{ number_format($comments->total()) }}
            {{ \Illuminate\Support\Str::plural('comment', $comments->total()) }}
        </p>

        <div class="admin-comments-table">
            <div class="admin-comments-head" aria-hidden="true">
                <span>Author</span>
                <span>Comment</span>
                <span>Image</span>
                <span>Date</span>
                <span></span>
            </div>

            <ul class="admin-comments-list">
                @foreach ($comments as $comment)
                    <li class="admin-comment-row">
                        <span class="admin-comment-author">
                            @if ($comment->user)
                                <a href="{{ route('users.show', $comment->user) }}" target="_blank" rel="noopener noreferrer">{{ $comment->author_name }}</a>
                            @else
                                {{ $comment->author_name }}
                            @endif
                            @if ($comment->isReply())
                                <span class="badge admin-comment-reply-badge">Reply</span>
                            @endif
                        </span>

                        <div class="admin-comment-body-wrap">
                            @if ($comment->isReply())
                                <p class="admin-comment-reply-context">
                                    &#8617; replying to {{ $comment->parent->author_name ?? 'a deleted comment' }}
                                    @if ($comment->parent)
                                        : &ldquo;{{ \Illuminate\Support\Str::limit($comment->parent->body, 60) }}&rdquo;
                                    @endif
                                </p>
                            @endif
                            <p class="admin-comment-body">{{ $comment->body }}</p>
                            @if ($comment->likes_count)
                                <p class="admin-comment-likes">
                                    {{ number_format($comment->likes_count) }} {{ \Illuminate\Support\Str::plural('like', $comment->likes_count) }}
                                </p>
                            @endif
                        </div>

                        <div class="admin-comment-image">
                            @if ($comment->image)
                                <a href="{{ route('images.show', $comment->image->slug) }}" class="admin-comment-image-link" target="_blank" rel="noopener noreferrer">
                                    <img
                                        src="{{ $comment->image->thumbnail_url }}"
                                        alt=""
                                        class="admin-comment-thumb"
                                        width="48"
                                        height="48"
                                        loading="lazy"
                                    >
                                    <span class="admin-comment-image-title" title="{{ $comment->image->title }}">{{ $comment->image->title }}</span>
                                </a>
                            @else
                                <span class="text-muted">Deleted image</span>
                            @endif
                        </div>
                        <time class="admin-comment-date" datetime="{{ $comment->created_at->toIso8601String() }}">
                            {{ $comment->created_at->format('M j, Y g:i A') }}
                        </time>
                        <form
                            action="{{ route('admin.comments.destroy', $comment) }}"
                            method="POST"
                            class="delete-form"
                            data-confirm="Delete this comment?"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>

        @if ($comments->hasPages())
            <div class="pagination">
                @if ($comments->onFirstPage())
                    <span>&laquo;</span>
                @else
                    <a href="{{ $comments->previousPageUrl() }}">&laquo;</a>
                @endif

                @foreach ($comments->getUrlRange(1, $comments->lastPage()) as $page => $url)
                    @if ($page == $comments->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($comments->hasMorePages())
                    <a href="{{ $comments->nextPageUrl() }}">&raquo;</a>
                @else
                    <span>&raquo;</span>
                @endif
            </div>
        @endif
    @endif
@endsection
