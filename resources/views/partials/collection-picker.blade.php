@php
    $bookmarked = $bookmarked ?? false;
    $userCollections = $userCollections ?? collect();
    $collectionMap = $collectionMap ?? [];
    $inCollectionCount = collect($collectionMap)->filter()->count();
@endphp

<div class="collection-picker">
    <button type="button" class="collection-picker-toggle stat-btn" aria-haspopup="true" aria-expanded="false">
        <svg class="collection-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
            <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" fill="none" stroke="currentColor" stroke-width="1.5"/>
        </svg>
        <span>Collections</span>
        <span
            class="collection-picker-count"
            @if ($inCollectionCount < 1) hidden @endif
        >{{ $inCollectionCount }}</span>
    </button>

    <div class="collection-picker-menu" hidden>
        @include('partials.collection-picker-menu', compact('image', 'bookmarked', 'userCollections', 'collectionMap'))
    </div>
</div>
