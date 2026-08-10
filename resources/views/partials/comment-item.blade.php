@php
    $depth = $depth ?? 0;
    $canComment = $canComment ?? true;
    $canReply = $canComment && $depth < \App\Models\Comment::MAX_REPLY_DEPTH;
@endphp

<li
    class="comment-item {{ $depth > 0 ? 'comment-item-reply' : '' }}"
    id="comment-{{ $comment->id }}"
    style="--comment-depth: {{ $depth }}"
>
    <div class="comment-header">
        @if ($comment->user)
            <a href="{{ route('users.show', $comment->user) }}" class="comment-author">{{ '@'.$comment->author_name }}</a>
        @else
            <span class="comment-author">{{ $comment->author_name }}</span>
        @endif
        <time class="comment-date" datetime="{{ $comment->created_at->toIso8601String() }}">
            {{ $comment->created_at->diffForHumans() }}
        </time>
    </div>
    <p class="comment-body">{!! app(\App\Support\CommentFormatter::class)->formatBodyHtml($comment->body) !!}</p>
    <div class="comment-actions">
        @include('partials.comment-like-button', [
            'comment' => $comment,
            'liked' => $commentLikedMap[$comment->id] ?? false,
        ])
        @if ($canReply)
            <button
                type="button"
                class="comment-reply-btn stat-btn"
                data-reply-target="reply-form-{{ $comment->id }}"
                aria-label="Reply to this comment"
                aria-expanded="false"
            >
                <svg class="reply-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                    <path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4V5Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                </svg>
                <span>Reply</span>
            </button>
        @endif
    </div>

    @include('partials.comment-liked-by', [
        'comment' => $comment,
        'recentLikers' => $commentLikersMap[$comment->id] ?? ['users' => collect(), 'others_count' => 0, 'total' => 0],
    ])

    @if ($canReply)
        <form
            method="POST"
            action="{{ route('images.comments.store', $image->slug) }}"
            class="comment-reply-form"
            id="reply-form-{{ $comment->id }}"
            hidden
        >
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">

            @guest
                <div class="form-group">
                    <label for="reply-author-{{ $comment->id }}" class="sr-only">Name</label>
                    <input
                        type="text"
                        id="reply-author-{{ $comment->id }}"
                        name="author_name"
                        class="form-control"
                        value="{{ old('parent_id') == $comment->id ? old('author_name') : '' }}"
                        placeholder="Name"
                        maxlength="50"
                    >
                </div>
            @endguest

            <div class="form-group">
                <label for="reply-body-{{ $comment->id }}" class="sr-only">Reply</label>
                <textarea
                    id="reply-body-{{ $comment->id }}"
                    name="body"
                    class="form-control"
                    rows="2"
                    placeholder="Write a reply… Use @username to mention someone."
                    required
                    maxlength="1000"
                >{{ old('parent_id') == $comment->id ? old('body') : '' }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-sm btn-primary">Post reply</button>
                <button type="button" class="btn btn-sm btn-secondary comment-reply-cancel">Cancel</button>
            </div>
        </form>
    @endif

    @if ($comment->replies->isNotEmpty())
        <ul class="comments-list comments-replies">
            @foreach ($comment->replies as $reply)
                @include('partials.comment-item', [
                    'comment' => $reply,
                    'image' => $image,
                    'commentLikedMap' => $commentLikedMap,
                    'commentLikersMap' => $commentLikersMap ?? [],
                    'canComment' => $canComment,
                    'depth' => $depth + 1,
                ])
            @endforeach
        </ul>
    @endif
</li>