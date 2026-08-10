@php
    $rank = $rank ?? null;
@endphp
<a href="{{ route('gallery.tag', $tag->name) }}" class="tags-index-card{{ $rank ? ' tags-index-card-ranked' : '' }}">
    @if ($rank)
        <span class="tags-index-card-rank" aria-hidden="true">{{ $rank }}</span>
    @endif
    <span class="tags-index-card-body">
        <span class="tags-index-card-name">{{ $tag->name }}</span>
        <span class="tags-index-card-count">
            <span class="tags-index-card-count-num">{{ number_format($tag->images_count) }}</span>
            {{ \Illuminate\Support\Str::plural('post', $tag->images_count) }}
        </span>
    </span>
    <span class="tags-index-card-arrow" aria-hidden="true">→</span>
</a>
