<header class="artist-profile-header">
    <div class="artist-profile-header-grid">
        <div class="artist-profile-sidebar">
            @if ($parody?->hasCover())
                <img src="{{ $parody->cover_url }}" alt="" width="126" height="224" class="parody-profile-cover">
            @else
                <span
                    class="parody-profile-cover parody-profile-cover-placeholder"
                    style="--avatar-color: {{ $parody?->cover_color ?? 'hsl(220, 35%, 55%)' }}"
                    aria-hidden="true"
                >{{ $parody?->cover_initials ?? mb_strtoupper(mb_substr($parodyName, 0, 2)) }}</span>
            @endif

            <div class="artist-profile-sidebar-meta">
                <h1>{{ $parodyName }}</h1>
                <p class="artist-profile-count">
                    {{ number_format($postCount) }} {{ \Illuminate\Support\Str::plural('post', $postCount) }}
                </p>
            </div>
        </div>

        <div class="artist-profile-main">
            @if ($parody?->hasDescription())
                <div class="artist-profile-description">{!! $parody->description_html !!}</div>
            @else
                <p class="artist-profile-description-empty">No description yet.</p>
            @endif
        </div>
    </div>
</header>
