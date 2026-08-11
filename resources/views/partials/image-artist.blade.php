@if ($image->hasArtistCredit())
    <a href="{{ route('gallery.artist', $image->artist_name) }}" class="image-author image-artist">
        @if ($image->artist?->hasAvatar())
            <img
                src="{{ $image->artist->avatar_url }}"
                alt=""
                width="24"
                height="24"
                class="image-author-avatar image-artist-avatar"
                loading="lazy"
            >
        @endif
        <span class="image-artist-info">
            <span class="image-author-name">
                <span class="image-artist-label">Artist:</span>
                {{ $image->artist_name }}
            </span>
            <span class="image-artist-post-count">
                {{ trans_choice(':count post|:count posts', $image->artist_posts_count ?? 0, ['count' => $image->artist_posts_count ?? 0]) }}
            </span>
        </span>
    </a>
@endif
