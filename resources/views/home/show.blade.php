@extends('layouts.app')

@section('title', $image->title . ' — HeartLovePics')

@php
    $ogDescription = \Illuminate\Support\Str::limit(
        $image->hasDescription() ? $image->description_plain : $image->title,
        160
    );
@endphp

@section('meta_description', $ogDescription)
@section('canonical', route('images.show', $image->slug))

@php
    $blurNsfw = $image->is_nsfw && !($ageConfirmed ?? false);
@endphp

@if (! $blurNsfw)
    @push('preload')
        <link rel="preload" as="image" href="{{ $image->direct_url }}" fetchpriority="high">
    @endpush
@endif

@push('meta')
    @include('partials.og-meta', [
        'title' => $image->title . ' — HeartLovePics',
        'description' => $ogDescription,
        'url' => url(route('images.show', $image->slug)),
        // NSFW posts never get their raw image in the OG/Twitter preview — link
        // unfurls on Discord/Slack/iMessage/X would otherwise expose the
        // unblurred image outside the site's own age-gate.
        'image' => $image->is_nsfw ? null : $image->direct_url,
    ])

    @if ($imageSchema ?? null)
        <script type="application/ld+json">
            {!! json_encode($imageSchema, JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
@endpush

@section('content')
    <article
        class="image-detail"
        @if ($image->is_nsfw)
            data-nsfw-detail="1"
            data-age-confirmed="{{ ($ageConfirmed ?? false) ? '1' : '0' }}"
            data-home-url="{{ route('home') }}"
        @endif
    >
        <div class="image-detail-toolbar">
            <a href="{{ route('home') }}" class="back-link">&larr; Back to gallery</a>

            <div class="layout-toggle" role="group" aria-label="Post layout">
                <button type="button" class="layout-toggle-btn" data-layout-option="stacked" aria-pressed="true" title="Stacked layout">
                    <svg viewBox="0 0 20 16" width="18" height="15" aria-hidden="true">
                        <rect x="1" y="1" width="18" height="8" rx="1"/>
                        <rect x="1" y="11" width="18" height="1.6" rx="0.8"/>
                        <rect x="1" y="14" width="11" height="1.6" rx="0.8"/>
                    </svg>
                    <span>Stacked</span>
                </button>
                <button type="button" class="layout-toggle-btn" data-layout-option="side_by_side" aria-pressed="false" title="Side by side layout">
                    <svg viewBox="0 0 20 16" width="18" height="15" aria-hidden="true">
                        <rect x="1" y="1" width="8.2" height="1.6" rx="0.8"/>
                        <rect x="1" y="4.7" width="8.2" height="1.6" rx="0.8"/>
                        <rect x="1" y="8.4" width="5.5" height="1.6" rx="0.8"/>
                        <rect x="10.8" y="1" width="8.2" height="14" rx="1"/>
                    </svg>
                    <span>Side by side</span>
                </button>
            </div>
        </div>

        @include('partials.flash')

        @if ($isDraft ?? false)
            <p class="draft-banner">This image is a draft and only visible to you.</p>
        @elseif ($image->is_private)
            <p class="draft-banner">This image is private — only visible to you on your profile.</p>
        @endif

        <div class="image-detail-title-stacked">
            <h1>{{ $image->title }}</h1>
        </div>

        @if ($image->is_nsfw)
            <p class="content-warning-banner">
                <strong>Content warning:</strong>
                {{ $image->content_warning ?: 'Adults only (NSFW)' }}
            </p>
        @endif

        <div class="image-detail-columns">
            <div class="image-detail-columns-media">
                @include('partials.image-detail-gallery', ['image' => $image, 'blurNsfw' => $blurNsfw])
            </div>
            <div class="image-detail-info image-detail-columns-content">
                @include('partials.image-detail-content', compact('image', 'recentLikers', 'canManageImage', 'isDraft', 'isLiked', 'isBookmarked', 'userCollections', 'collectionMap'))
                @include('partials.image-tags', ['image' => $image])
            </div>
        </div>

        @include('partials.related-images', [
            'relatedImages' => $relatedImages,
            'title' => $relatedTitle ?? 'More images',
        ])

        @include('partials.related-images', [
            'relatedImages' => $sameAuthorImages,
            'title' => 'More by @' . $image->user?->username,
        ])

        @include('partials.related-images', [
            'relatedImages' => $sameArtistImages,
            'title' => 'More by ' . $image->artist_name,
        ])

        <section class="image-comments" id="comments">
            <h3 class="comments-title">Comments</h3>

            @if ($canComment)
                <form method="POST" action="{{ route('images.comments.store', $image->slug) }}" class="comment-form">
                    @csrf

                    @auth
                        <p class="comment-form-as">Posting as <strong>{{ '@'.auth()->user()->username }}</strong></p>
                    @else
                        <div class="form-group">
                            <label for="author_name">Name</label>
                            <input
                                type="text"
                                id="author_name"
                                name="author_name"
                                class="form-control"
                                value="{{ old('author_name') }}"
                                placeholder="Anonymous"
                                maxlength="50"
                            >
                            @error('author_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endauth

                    <div class="form-group">
                        <label for="body">Comment</label>
                        <textarea
                            id="body"
                            name="body"
                            class="form-control"
                            rows="3"
                            placeholder="Write a comment… Use @username to mention someone."
                            required
                            maxlength="1000"
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Post comment</button>
                </form>
            @elseif (auth()->check())
                <div class="comment-restricted-notice" role="status">
                    <span class="comment-restricted-notice-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18">
                            <path d="M8 11V8a4 4 0 1 1 8 0v3" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>
                            <rect x="6" y="11" width="12" height="9" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </span>
                    <div class="comment-restricted-notice-content">
                        <p class="comment-restricted-notice-title">Commenting disabled</p>
                        <p class="comment-restricted-notice-text">Your account is restricted from posting comments. You can still browse images and like content.</p>
                    </div>
                </div>
            @endif

            @if ($image->comments->isEmpty())
                <p class="comments-empty">No comments yet. Be the first to share your thoughts.</p>
            @else
                <ul class="comments-list">
                    @foreach ($image->comments as $comment)
                        @include('partials.comment-item', [
                            'comment' => $comment,
                            'image' => $image,
                            'commentLikedMap' => $commentLikedMap,
                            'commentLikersMap' => $commentLikersMap ?? [],
                            'canComment' => $canComment,
                        ])
                    @endforeach
                </ul>
            @endif
        </section>
    </article>

    @if ($image->is_nsfw)
        @include('partials.nsfw-age-modal')
    @endif

    @include('partials.image-lightbox')
@endsection

@push('scripts')
    <script src="{{ asset('js/comments.js') }}" defer></script>
    <script src="{{ asset('js/image-detail-gallery.js') }}" defer></script>
    <script src="{{ asset('js/lightbox.js') }}" defer></script>
    <script src="{{ asset('js/image-detail-layout.js') }}" defer></script>
    @if ($image->is_nsfw)
        <script src="{{ asset('js/nsfw-toggle.js') }}" defer></script>
    @endif
@endpush
