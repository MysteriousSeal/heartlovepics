@php
    $rank = $rank ?? null;
    $profile = $parody->profile;
@endphp
<a href="{{ route('gallery.parody', $parody->name) }}" class="tags-index-card artists-index-card{{ $rank ? ' tags-index-card-ranked' : '' }}">
    @if ($rank)
        <span class="tags-index-card-rank" aria-hidden="true">{{ $rank }}</span>
    @endif
    @if ($profile?->hasCover())
        <img src="{{ $profile->cover_url }}" alt="" width="40" height="40" class="artists-index-card-avatar">
    @else
        <span
            class="artists-index-card-avatar artists-index-card-avatar-placeholder"
            style="--avatar-color: {{ $profile?->cover_color ?? 'hsl(220, 35%, 55%)' }}"
            aria-hidden="true"
        >{{ $profile?->cover_initials ?? mb_strtoupper(mb_substr($parody->name, 0, 2)) }}</span>
    @endif
    <span class="tags-index-card-body">
        <span class="tags-index-card-name">{{ $parody->name }}</span>
        <span class="tags-index-card-count">
            <span class="tags-index-card-count-num">{{ number_format($parody->images_count) }}</span>
            {{ \Illuminate\Support\Str::plural('post', $parody->images_count) }}
        </span>
    </span>
    <span class="tags-index-card-arrow" aria-hidden="true">→</span>
</a>
