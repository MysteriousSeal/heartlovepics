@php
    $showDelete = $showDelete ?? true;
@endphp

<div class="admin-image-card">
    <div class="thumb">
        <img src="{{ $image->thumbnail_url }}" alt="{{ $image->alt_text ?? $image->title }}">
        @if ($image->additionalImages->isNotEmpty())
            <div class="admin-image-card-extras" aria-label="{{ $image->additionalImages->count() }} more {{ \Illuminate\Support\Str::plural('image', $image->additionalImages->count()) }}">
                @foreach ($image->additionalImages as $extra)
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
        @if (isset($image->likes_count) || isset($image->comments_count))
            <p class="admin-image-card-stats">
                {{ number_format($image->views) }} {{ \Illuminate\Support\Str::plural('view', $image->views) }}
                &middot; {{ number_format($image->likes_count ?? 0) }} {{ \Illuminate\Support\Str::plural('like', $image->likes_count ?? 0) }}
                &middot; {{ number_format($image->comments_count ?? 0) }} {{ \Illuminate\Support\Str::plural('comment', $image->comments_count ?? 0) }}
                @if (isset($image->collections_count))
                    &middot; {{ number_format($image->collections_count) }} {{ \Illuminate\Support\Str::plural('collection', $image->collections_count) }}
                @endif
            </p>
        @endif
        <div class="card-meta">
            <span class="badge {{ $image->is_published ? 'badge-published' : 'badge-draft' }}">
                {{ $image->is_published ? 'Published' : 'Draft' }}
            </span>
            @if ($image->is_nsfw)
                <span class="badge badge-nsfw">NSFW</span>
            @endif
            · {{ $image->updated_at->format('M j, Y') }}
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
</div>
