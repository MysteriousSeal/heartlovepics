@php
    $extraImages = $image->additionalImages ?? collect();
    $altText = $image->alt_text ?? $image->title;
    $allImages = collect([$image])->concat($extraImages);
@endphp

<div
    class="image-detail-media image-detail-media-primary {{ $blurNsfw ? 'is-nsfw' : '' }}"
    @if ($image->is_nsfw) data-is-nsfw="1" @endif
    role="button"
    tabindex="0"
    aria-label="View full image"
    id="image-detail-primary"
    data-lightbox-src="{{ $image->url }}"
    data-lightbox-alt="{{ $altText }}"
>
    <img
        id="image-detail-primary-img"
        src="{{ $image->url }}"
        alt="{{ $altText }}"
        fetchpriority="high"
        loading="eager"
        decoding="sync"
        @if ($image->width && $image->height)
            width="{{ $image->width }}"
            height="{{ $image->height }}"
        @endif
    >
    @if ($blurNsfw)
        <span class="nsfw-age-label" aria-label="Adults only">-18</span>
    @endif
</div>

@if ($extraImages->isNotEmpty())
    <div class="image-detail-gallery-more" id="image-detail-gallery-more">
        @foreach ($allImages as $item)
            <div
                class="image-detail-media image-detail-gallery-thumb {{ $loop->first ? 'is-active' : '' }} {{ $blurNsfw ? 'is-nsfw' : '' }}"
                @if ($image->is_nsfw) data-is-nsfw="1" @endif
                role="button"
                tabindex="0"
                aria-label="Show image {{ $loop->iteration }} of {{ $allImages->count() }}"
                data-full-src="{{ $item->url }}"
                data-full-alt="{{ $altText }}"
                data-full-width="{{ $item->width }}"
                data-full-height="{{ $item->height }}"
            >
                <img
                    src="{{ $item->thumbnail_url }}"
                    alt="{{ $altText }}"
                    loading="lazy"
                    @if ($item->width && $item->height)
                        width="{{ $item->width }}"
                        height="{{ $item->height }}"
                    @endif
                >
                @if ($blurNsfw)
                    <span class="nsfw-age-label" aria-label="Adults only">-18</span>
                @endif
            </div>
        @endforeach
    </div>
@endif
