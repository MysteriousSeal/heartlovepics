<div class="masonry-item">
    <article class="masonry-card">
        <a
            href="{{ route('images.show', $image->slug) }}"
            class="masonry-card-link {{ $image->is_nsfw ? 'nsfw-gallery-link' : '' }}"
        >
            @php
                $blurNsfw = $image->is_nsfw && !($showNsfw ?? false);
                $imageId = (int) $image->id;
                $isLcp = isset($lcpImageId) && (int) $lcpImageId === $imageId;
                $isFirstPage = $isFirstPage ?? false;
            @endphp
            <div
                class="masonry-card-media {{ $blurNsfw ? 'is-nsfw' : '' }}"
                @if ($image->is_nsfw) data-is-nsfw="1" @endif
            >
                <img
                    src="{{ $image->thumbnail_url }}"
                    alt="{{ $image->alt_text ?? $image->title }}"
                    @if ($isLcp)
                        fetchpriority="high"
                        loading="eager"
                    @elseif ($isFirstPage)
                        loading="eager"
                    @else
                        loading="lazy"
                    @endif
                    @if ($image->width && $image->height)
                        width="{{ $image->width }}"
                        height="{{ $image->height }}"
                    @endif
                >
                @if ($image->is_nsfw)
                    <span class="stat-btn nsfw-stat-badge" aria-label="NSFW content">NSFW</span>
                @endif
                @if ($image->is_private)
                    <span class="stat-btn private-stat-badge" aria-label="Private — only visible to you">Private</span>
                @endif
                @if ($blurNsfw)
                    <span class="nsfw-age-label" aria-label="Adults only">-18</span>
                @endif
                @if (($image->additional_images_count ?? 0) > 0)
                    <span class="masonry-card-count-badge" aria-label="{{ $image->additional_images_count }} more images in this post">
                        +{{ $image->additional_images_count }}
                    </span>
                @endif
            </div>
        </a>
        <div class="card-caption">
            <a
                href="{{ route('images.show', $image->slug) }}"
                class="card-title-link {{ $image->is_nsfw ? 'nsfw-gallery-link' : '' }}"
            >
                @if ($image->title)
                    <h2>{{ $image->title }}</h2>
                @endif
            </a>
            @if ($image->hasDescription())
                <p class="line-clamp-2">{{ \Illuminate\Support\Str::limit($image->description_plain, 120) }}</p>
            @endif
            <div class="card-byline">
                @include('partials.image-author', ['image' => $image])
                <time class="card-date" datetime="{{ $image->created_at->toIso8601String() }}">
                    {{ $image->created_at->diffForHumans() }}
                </time>
            </div>
            <div class="card-footer">
                @include('partials.image-stats', [
                    'image' => $image,
                    'liked' => $likedMap[$image->id] ?? false,
                    'bookmarked' => $bookmarkMap[$image->id] ?? false,
                ])
            </div>
        </div>
    </article>
</div>