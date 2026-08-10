@if ($image->tags->isNotEmpty())
    <section class="image-detail-tags">
        <h3 class="image-detail-tags-title">Tags</h3>
        <ul class="image-detail-tags-list">
            @foreach ($image->tags as $tag)
                <li>
                    <a href="{{ route('gallery.tag', $tag->name) }}" class="image-tag-badge">{{ $tag->name }}</a>
                </li>
            @endforeach
        </ul>
    </section>
@endif