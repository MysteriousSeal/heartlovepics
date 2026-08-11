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
        <span class="image-author-name">
            <span class="image-artist-label">Artist:</span>
            {{ $image->artist_name }}
        </span>
    </a>
@endif
