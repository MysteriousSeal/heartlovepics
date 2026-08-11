<header class="artist-profile-header">
    <div class="artist-profile-header-grid">
        <div class="artist-profile-sidebar">
            @if ($artist?->hasAvatar())
                <img src="{{ $artist->avatar_url }}" alt="" width="112" height="112" class="artist-profile-avatar">
            @else
                <span
                    class="artist-profile-avatar artist-profile-avatar-placeholder"
                    style="--avatar-color: {{ $artist?->avatar_color ?? 'hsl(220, 35%, 55%)' }}"
                    aria-hidden="true"
                >{{ $artist?->avatar_initials ?? mb_strtoupper(mb_substr($artistName, 0, 2)) }}</span>
            @endif

            <div class="artist-profile-sidebar-meta">
                <h1>{{ $artistName }}</h1>
                <p class="artist-profile-count">
                    {{ number_format($postCount) }} {{ \Illuminate\Support\Str::plural('post', $postCount) }}
                </p>

                @if ($artist?->hasLinks())
                    <div class="artist-profile-links">
                        @if ($artist->deviantart_url)
                            <a href="{{ $artist->deviantart_url }}" class="artist-profile-link" target="_blank" rel="noopener noreferrer nofollow">DeviantArt</a>
                        @endif
                        @if ($artist->furaffinity_url)
                            <a href="{{ $artist->furaffinity_url }}" class="artist-profile-link" target="_blank" rel="noopener noreferrer nofollow">FurAffinity</a>
                        @endif
                        @if ($artist->patreon_url)
                            <a href="{{ $artist->patreon_url }}" class="artist-profile-link" target="_blank" rel="noopener noreferrer nofollow">Patreon</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="artist-profile-main">
            @if ($artist?->hasDescription())
                <div class="artist-profile-description">{!! $artist->description_html !!}</div>
            @else
                <p class="artist-profile-description-empty">No description yet.</p>
            @endif
        </div>
    </div>
</header>
