<div class="image-detail-header">
    <h1 class="image-detail-title-inline">{{ $image->title }}</h1>
    <div class="image-detail-header-actions">
        @include('partials.image-stats', [
            'image' => $image,
            'liked' => $isLiked,
            'bookmarked' => $isBookmarked,
        ])
        @auth
            @include('partials.collection-picker', [
                'image' => $image,
                'bookmarked' => $isBookmarked,
                'userCollections' => $userCollections,
                'collectionMap' => $collectionMap,
            ])
        @endauth
    </div>
</div>

@include('partials.liked-by', compact('recentLikers'))

@if ($image->hasDescription())
    <div class="image-detail-description">
        {!! $image->description_html !!}
    </div>
@endif

@if ($image->hasArtistCredit())
    <div class="image-artist-credit">
        <span class="image-artist-credit-label">Original artist:</span>
        @if ($image->artist?->hasAvatar())
            <img src="{{ $image->artist->avatar_url }}" alt="" width="20" height="20" class="image-artist-credit-avatar">
        @endif
        <a href="{{ route('gallery.artist', $image->artist_name) }}" class="image-artist-credit-name">{{ $image->artist_name }}</a>
        @if ($image->artist?->deviantart_url)
            <a href="{{ $image->artist->deviantart_url }}" class="image-artist-credit-link" target="_blank" rel="noopener noreferrer nofollow">DeviantArt</a>
        @endif
        @if ($image->artist?->furaffinity_url)
            <a href="{{ $image->artist->furaffinity_url }}" class="image-artist-credit-link" target="_blank" rel="noopener noreferrer nofollow">FurAffinity</a>
        @endif
        @if ($image->artist?->patreon_url)
            <a href="{{ $image->artist->patreon_url }}" class="image-artist-credit-link" target="_blank" rel="noopener noreferrer nofollow">Patreon</a>
        @endif
    </div>
@endif

@if (filled($image->parody))
    <div class="image-parody-credit">
        <span class="image-parody-credit-label">Parody:</span>
        <a href="{{ route('gallery.parody', $image->parody) }}" class="image-parody-credit-name">{{ $image->parody }}</a>
    </div>
@endif

<div class="image-detail-meta">
    <div class="image-detail-meta-primary">
        <span>Added {{ $image->created_at->format('F j, Y') }}</span>
    </div>
    @include('partials.image-author', ['image' => $image, 'showLabel' => true])
</div>

@if ($canManageImage ?? false)
    <div class="image-owner-actions">
        @if ($isDraft ?? false)
            <form method="POST" action="{{ route('profile.images.publish', $image) }}" class="image-publish-form">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary btn-sm">Publish</button>
            </form>
        @endif
        <a href="{{ route('profile.images.edit', $image) }}" class="btn btn-secondary btn-sm">Edit image</a>
        <form
            method="POST"
            action="{{ route('profile.images.destroy', $image) }}"
            class="image-delete-form"
            data-confirm="Delete this image? You can undo this for a short time after."
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
    </div>
@endif
