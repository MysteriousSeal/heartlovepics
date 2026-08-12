@php
    $rank = $rank ?? null;
    $profile = $artist->profile;
@endphp
<a href="{{ route('gallery.artist', $artist->name) }}" class="tags-index-card artists-index-card{{ $rank ? ' tags-index-card-ranked' : '' }}">
    @if ($rank)
        <span class="tags-index-card-rank" aria-hidden="true">{{ $rank }}</span>
    @endif
    @if ($profile?->hasAvatar())
        <img src="{{ $profile->avatar_url }}" alt="" width="40" height="40" class="artists-index-card-avatar">
    @else
        <span
            class="artists-index-card-avatar artists-index-card-avatar-placeholder"
            style="--avatar-color: {{ $profile?->avatar_color ?? 'hsl(220, 35%, 55%)' }}"
            aria-hidden="true"
        >{{ $profile?->avatar_initials ?? mb_strtoupper(mb_substr($artist->name, 0, 2)) }}</span>
    @endif
    <span class="tags-index-card-body">
        <span class="tags-index-card-name">{{ $artist->name }}</span>
        <span class="tags-index-card-count">
            <span class="tags-index-card-count-num">{{ number_format($artist->images_count) }}</span>
            {{ \Illuminate\Support\Str::plural('post', $artist->images_count) }}
        </span>
    </span>
    <span class="tags-index-card-arrow" aria-hidden="true">→</span>
</a>
