@php
    $isEdit = isset($image);
    $allowImageUpload = $allowImageUpload ?? true;
    $lockContentRating = ($userUpload ?? false) && $isEdit && ($image->is_nsfw_locked ?? false);
    $isNsfw = (bool) old('is_nsfw', $image->is_nsfw ?? false);
    $contentWarning = old('content_warning', $image->content_warning ?? '');
@endphp

@if ($allowImageUpload)
    <div class="form-group">
        <label>Images @unless($isEdit)<span style="color:#c44">*</span>@endunless</label>
        <p class="form-hint">
            @if ($isEdit)
                Drag tiles to reorder. The first image is the cover. Add more below (up to 100 total).
            @else
                Drop all images here (up to 100). The first is the cover; the rest appear in the gallery.
            @endif
        </p>

        <div
            class="upload-images-list"
            id="upload-images-list"
            @if ($isEdit) data-has-main="1" @endif
            aria-label="Image order"
        >
            @if ($isEdit)
                <div
                    class="additional-images-item is-cover"
                    data-image-key="main"
                    tabindex="0"
                    role="button"
                    aria-label="Cover image. Press arrow keys to reorder, Enter to preview."
                >
                    <img src="{{ $image->thumbnail_url }}" alt="Cover image" draggable="false">
                    <span class="additional-images-badge">Cover</span>
                    <button type="button" class="additional-images-remove" aria-label="Remove this image" hidden>&times;</button>
                    <input type="checkbox" name="remove_main" value="1" id="remove-main-checkbox" hidden>
                    <span class="upload-images-handle" aria-hidden="true" title="Drag to reorder">⋮⋮</span>
                </div>
                @foreach ($image->additionalImages as $extra)
                    <div
                        class="additional-images-item"
                        data-image-key="{{ $extra->id }}"
                        tabindex="0"
                        role="button"
                        aria-label="Existing image. Press arrow keys to reorder, Enter to preview."
                    >
                        <img src="{{ $extra->thumbnail_url }}" alt="" draggable="false">
                        <button type="button" class="additional-images-remove" aria-label="Remove this image">&times;</button>
                        <input type="checkbox" name="remove_images[]" value="{{ $extra->id }}" hidden>
                        <span class="upload-images-handle" aria-hidden="true" title="Drag to reorder">⋮⋮</span>
                    </div>
                @endforeach
            @endif
        </div>
        <input type="hidden" name="image_order" id="image-order-input" value="">

        <div class="drop-zone" id="drop-zone">
            <p>Drag & drop images here, or <strong>click to browse</strong></p>
            <p class="drop-zone-hint">JPEG, PNG, GIF, WebP · Max 10 MB each · Multiple files allowed</p>
            <input
                type="file"
                id="image-files-picker"
                accept="image/jpeg,image/png,image/gif,image/webp"
                multiple
                @unless($isEdit) required @endunless
            >
        </div>

        <input type="file" name="image" id="image-input" accept="image/jpeg,image/png,image/gif,image/webp" hidden>
        <input type="file" name="additional_images[]" id="additional-images-input" accept="image/jpeg,image/png,image/gif,image/webp" multiple hidden>
        <input type="hidden" name="expected_file_count" id="expected-file-count-input" value="0">

        @error('image')
            <p class="form-error">{{ $message }}</p>
        @enderror
        @error('additional_images')
            <p class="form-error">{{ $message }}</p>
        @enderror
        @error('additional_images.*')
            <p class="form-error">{{ $message }}</p>
        @enderror
        @error('image_order')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>
@endif

<div class="form-group">
    <label for="title">Title <span style="color:#c44">*</span></label>
    <input
        type="text"
        id="title"
        name="title"
        class="form-control"
        value="{{ old('title', $image->title ?? '') }}"
        maxlength="255"
        required
    >
    <p class="form-char-count" id="title-char-count" aria-live="polite">0 / 255</p>
    @error('title')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

@if ($userUpload ?? false)
    <div class="form-group">
        <label class="form-switch">
            <input
                type="checkbox"
                name="is_private"
                value="1"
                @checked(old('is_private', $image->is_private ?? false))
            >
            <span class="form-switch-track"></span>
            <span class="form-switch-text">
                <span class="form-switch-label">Private</span>
                <span class="form-switch-hint">Only you can see this on your profile. Hidden from the gallery, search, tags, and everyone else.</span>
            </span>
        </label>
        @error('is_private')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>
@endif

<div class="form-group description-editor-group">
    <label for="description-editor">Description</label>
    <div
        id="description-editor"
        class="description-editor"
        aria-label="Description editor"
    ></div>
    <textarea
        id="description"
        name="description"
        class="description-editor-source"
        hidden
        placeholder="Write a description or a full story…"
    >{{ old('description', $image->description ?? '') }}</textarea>
    <p class="form-hint">Rich text editor — paste from other websites to keep bold, links, and lists. HTML is cleaned for safety.</p>
    @error('description')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

@php
    // old('tags') mirrors whatever was posted, so a malformed request can make it a
    // scalar. Cast so the loop below can never fatal on non-iterable input.
    $imageTags = array_filter((array) old('tags', isset($image) ? $image->tags->pluck('name')->all() : []), 'is_scalar');
@endphp

<div class="form-group">
    <label for="tag-input">Tags</label>
    <div class="tag-input" id="tag-input-root" data-max-tags="10" data-max-length="50" data-suggest-url="{{ route('tags.suggest') }}">
        <div class="tag-input-chips" id="tag-input-chips">
            @foreach ($imageTags as $tag)
                <span class="tag-chip" data-tag="{{ $tag }}">
                    <span class="tag-chip-label">{{ $tag }}</span>
                    <button type="button" class="tag-chip-remove" aria-label="Remove tag {{ $tag }}">&times;</button>
                    <input type="hidden" name="tags[]" value="{{ $tag }}">
                </span>
            @endforeach
        </div>
        <input
            type="text"
            id="tag-input"
            class="tag-input-field"
            placeholder="Type a tag and press Enter"
            autocomplete="off"
        >
    </div>
    <div class="tag-suggestions" id="tag-suggestions" hidden>
        <span class="tag-suggestions-label">Suggested:</span>
        <div class="tag-suggestions-list" id="tag-suggestions-list"></div>
    </div>
    <p class="form-char-count" id="tag-char-count" aria-live="polite">0 / 5</p>
    <p class="form-hint">Press Enter to add each tag. Suggestions appear from your title and description.</p>
    <p class="form-error" id="tag-input-error" aria-live="polite" hidden></p>
    @error('tags')
        <p class="form-error">{{ $message }}</p>
    @enderror
    @error('tags.*')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

<div class="form-group alt-text-group {{ ($userUpload ?? false) && ! $isEdit ? 'alt-text-group--prompt' : '' }}">
    <label for="alt_text">
        Alt text
        @if ($userUpload ?? false)
            <span class="alt-text-recommended">Recommended</span>
        @endif
    </label>
    @if (($userUpload ?? false) && ! $isEdit)
        <div class="alt-text-callout" role="note">
            <p><strong>Describe what is in the image.</strong> Alt text is read aloud by screen readers and shown when the image cannot load.</p>
            <p class="alt-text-callout-examples">Examples: “Golden retriever on a beach at sunset”, “Watercolor portrait of a woman in a red hat”.</p>
        </div>
    @endif
    <input
        type="text"
        id="alt_text"
        name="alt_text"
        class="form-control"
        value="{{ old('alt_text', $image->alt_text ?? '') }}"
        placeholder="{{ ($userUpload ?? false) ? 'Who or what is in the image? Include setting, colors, or mood.' : 'Describe the image for accessibility' }}"
        maxlength="125"
        aria-describedby="alt-text-char-count{{ ($userUpload ?? false) ? ' alt-text-guidance' : '' }}"
    >
    @if ($userUpload ?? false)
        <p class="form-hint alt-text-guidance" id="alt-text-guidance">Keep it concise (125 characters max) but specific enough that someone who cannot see the image understands it.</p>
    @endif
    <p class="form-char-count" id="alt-text-char-count" aria-live="polite">0 / 125</p>
    @error('alt_text')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="artist_name">Original artist</label>
    <p class="form-hint">Credit the artist who created this piece, if it isn't your own work.</p>
    <input
        type="text"
        id="artist_name"
        name="artist_name"
        class="form-control"
        value="{{ old('artist_name', $image->artist_name ?? '') }}"
        placeholder="Artist name"
        maxlength="100"
    >
    @error('artist_name')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

@if ($lockContentRating)
    <div class="form-group content-rating-locked-group">
        <span class="form-label" id="content-rating-label">Content rating</span>
        <div class="content-rating-locked">
            <span class="badge {{ $isNsfw ? 'badge-nsfw' : 'badge-sfw' }}">
                {{ $isNsfw ? 'NSFW' : 'SFW' }}
            </span>
            <span class="content-rating-locked-label">Locked by moderator</span>
        </div>
        <p class="form-warning">A moderator has set this rating. You cannot change the SFW/NSFW status or content warning.</p>
        <input type="hidden" name="is_nsfw" value="{{ $isNsfw ? 1 : 0 }}">
        <input type="hidden" name="content_warning" value="{{ $contentWarning }}">
    </div>

    @if ($isNsfw)
        <div class="form-group">
            <label for="content_warning">Content warning</label>
            <p class="form-control-static" id="content_warning">{{ $contentWarning ?: '—' }}</p>
        </div>
    @endif
@else
    <div class="form-group">
        <span class="form-label" id="content-rating-label">Content rating</span>
        <div class="rating-toggle" role="group" aria-labelledby="content-rating-label">
            <label class="rating-toggle-option">
                <input type="radio" name="is_nsfw" value="0" @checked(! $isNsfw)>
                <span>SFW</span>
            </label>
            <label class="rating-toggle-option rating-toggle-option-nsfw">
                <input type="radio" name="is_nsfw" value="1" @checked($isNsfw)>
                <span>NSFW</span>
            </label>
        </div>
        @error('is_nsfw')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group content-warning-group" id="content-warning-group" @unless($isNsfw) hidden @endunless>
        <label for="content_warning">Content warning</label>
        <input
            type="text"
            id="content_warning"
            name="content_warning"
            class="form-control"
            value="{{ $contentWarning }}"
            list="content-warning-presets"
            placeholder="e.g. Nudity, violence, gore"
            maxlength="100"
        >
        <datalist id="content-warning-presets">
            <option value="Nudity"></option>
            <option value="Sexual content"></option>
            <option value="Violence"></option>
            <option value="Gore"></option>
            <option value="Disturbing imagery"></option>
            <option value="Sensitive content"></option>
        </datalist>
        <p class="form-hint">Shown on the image page in addition to blur and age confirmation.</p>
        @error('content_warning')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>
@endif

@unless($userUpload ?? false)
    <div class="form-group">
        <label class="form-check">
            <input
                type="checkbox"
                name="is_published"
                value="1"
                @checked(old('is_published', $image->is_published ?? true))
            >
            Publish on the public gallery
        </label>
    </div>
@endunless