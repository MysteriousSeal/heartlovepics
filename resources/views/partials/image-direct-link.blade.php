<div class="image-direct-link">
    <button
        type="button"
        class="btn btn-secondary btn-sm copy-link-btn"
        data-copy-url="{{ $image->direct_url }}"
        aria-label="Copy image link"
    >
        Copy link
    </button>
    <a href="{{ $image->url }}" class="image-direct-link-url" target="_blank" rel="noopener noreferrer">{{ $image->direct_url }}</a>
</div>