@php
    $authenticated = auth()->check();
    $bookmarked = $bookmarked ?? false;
@endphp

<button
    type="button"
    class="bookmark-btn stat-btn {{ $bookmarked ? 'bookmarked' : '' }}{{ $authenticated ? '' : ' bookmark-btn--disabled' }}"
    @if ($authenticated)
        data-slug="{{ $image->slug }}"
        data-url="{{ route('images.bookmark', $image->slug) }}"
        data-label-bookmarked="Remove bookmark"
        data-label-unbookmarked="Bookmark this image"
        aria-label="{{ $bookmarked ? 'Remove bookmark' : 'Bookmark this image' }}"
    @else
        disabled
        aria-label="Log in to bookmark images"
        title="Log in to bookmark images"
    @endif
>
    <svg class="bookmark-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
    </svg>
    <span class="bookmark-count">{{ $image->bookmarks_count ?? 0 }}</span>
</button>