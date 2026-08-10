@php
    $hasImage = isset($image);
    $allowImageUpload = $allowImageUpload ?? true;
@endphp

<aside class="image-preview-panel">
    <p class="image-preview-label">Preview</p>
    <div class="image-preview-frame">
        <img
            id="image-side-preview"
            @if($hasImage) src="{{ $image->url }}" @endif
            alt="Image preview"
            @unless($hasImage) hidden @endunless
        >
        <div id="image-preview-empty" class="image-preview-empty" @if($hasImage) hidden @endif>
            <span>Select an image to preview</span>
        </div>
    </div>
    @if ($allowImageUpload)
        <div class="image-preview-tools">
            <button type="button" class="btn btn-sm btn-secondary" id="image-rotate-left" title="Rotate left" aria-label="Rotate left">
                ↺
            </button>
            <button type="button" class="btn btn-sm btn-secondary" id="image-rotate-right" title="Rotate right" aria-label="Rotate right">
                ↻
            </button>
        </div>
    @endif
    <p id="image-preview-resolution" class="image-preview-meta">
        @if($hasImage && $image->fileMeta())
            {{ $image->fileMeta() }}
        @endif
    </p>
</aside>