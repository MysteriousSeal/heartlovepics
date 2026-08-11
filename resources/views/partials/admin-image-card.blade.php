@php
    $showDelete = $showDelete ?? true;
    $extraCount = $image->additionalImages->count();
@endphp

<article class="admin-image-card">
    <div class="thumb">
        <img src="{{ $image->thumbnail_url }}" alt="{{ $image->alt_text ?? $image->title }}" loading="lazy">
        @if ($extraCount > 0)
            <span class="admin-image-card-count" aria-label="{{ $extraCount }} more {{ \Illuminate\Support\Str::plural('image', $extraCount) }}">
                +{{ number_format($extraCount) }}
            </span>
            <div class="admin-image-card-extras" aria-hidden="true">
                @foreach ($image->additionalImages->take(6) as $extra)
                    <div class="admin-image-card-extra">
                        <img src="{{ $extra->thumbnail_url }}" alt="" loading="lazy">
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="card-body">
        <h3 title="{{ $image->title }}">{{ $image->title }}</h3>
        <p class="admin-image-card-author">
            by
            @if ($image->user)
                <a href="{{ route('users.show', $image->user) }}" target="_blank" rel="noopener noreferrer">{{ '@'.$image->user->username }}</a>
            @else
                <span>Anonymous</span>
            @endif
        </p>
        @if ($image->hasArtistCredit())
            <p class="admin-image-card-artist">
                artist
                @if ($image->artist)
                    <a href="{{ route('gallery.artist', $image->artist->name) }}" target="_blank" rel="noopener noreferrer">{{ $image->artist->name }}</a>
                @else
                    <span>{{ $image->artist_name }}</span>
                @endif
            </p>
        @endif
        @if (isset($image->likes_count) || isset($image->comments_count))
            <p class="admin-image-card-stats">
                <span title="Views">{{ number_format($image->views) }} views</span>
                <span aria-hidden="true">·</span>
                <span title="Likes">{{ number_format($image->likes_count ?? 0) }} likes</span>
                <span aria-hidden="true">·</span>
                <span title="Comments">{{ number_format($image->comments_count ?? 0) }} comments</span>
            </p>
        @endif
        <div class="card-meta">
            <span class="badge {{ $image->is_published ? 'badge-published' : 'badge-draft' }}">
                {{ $image->is_published ? 'Published' : 'Draft' }}
            </span>
            @if ($image->is_nsfw)
                <span class="badge badge-nsfw">NSFW</span>
            @endif
            @if ($image->hasDescription())
                <span class="badge badge-has-description" title="Description is set">Desc</span>
            @else
                <span class="badge badge-no-description" title="No description yet">No desc</span>
            @endif
            <time class="admin-image-card-date" datetime="{{ $image->updated_at->toIso8601String() }}">
                {{ $image->updated_at->format('M j, Y') }}
            </time>
        </div>
        <div class="card-actions">
            @if ($image->is_published)
                <a href="{{ route('images.show', $image->slug) }}" class="btn btn-sm btn-secondary" target="_blank" rel="noopener noreferrer">View</a>
            @endif
            <a href="{{ route('admin.images.edit', $image) }}" class="btn btn-sm btn-secondary">Edit</a>
            @if ($showDelete)
                <form
                    action="{{ route('admin.images.destroy', $image) }}"
                    method="POST"
                    class="delete-form"
                    data-confirm="Delete this image? This cannot be undone."
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            @endif
        </div>
    </div>
</article>
