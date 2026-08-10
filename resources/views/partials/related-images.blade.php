@if ($relatedImages->isNotEmpty())
    <section class="related-images">
        <h3 class="related-images-title">{{ $title ?? 'More images' }}</h3>

        <div class="related-images-grid">
            @foreach ($relatedImages as $relatedImage)
                <a href="{{ route('images.show', $relatedImage->slug) }}" class="related-image-card">
                    <div class="related-image-media">
                        <img
                            src="{{ $relatedImage->thumbnail_url }}"
                            alt="{{ $relatedImage->alt_text ?? $relatedImage->title }}"
                            loading="lazy"
                            @if ($relatedImage->width && $relatedImage->height)
                                width="{{ $relatedImage->width }}"
                                height="{{ $relatedImage->height }}"
                            @endif
                        >
                    </div>
                    <div class="related-image-body">
                        <h4 class="related-image-title line-clamp-2">{{ $relatedImage->title }}</h4>
                        @if ($relatedImage->hasDescription())
                            <p class="related-image-description line-clamp-2">{{ \Illuminate\Support\Str::limit($relatedImage->description_plain, 160) }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
