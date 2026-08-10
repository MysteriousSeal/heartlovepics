@php
    $isEdit = isset($journal);
@endphp

<div class="form-group">
    <label for="title">Title <span style="color:#c44">*</span></label>
    <input
        type="text"
        id="title"
        name="title"
        class="form-control"
        value="{{ old('title', $journal->title ?? '') }}"
        maxlength="{{ \App\Models\Journal::TITLE_MAX_LENGTH }}"
        required
    >
    <p class="form-char-count" id="title-char-count" aria-live="polite">0 / {{ \App\Models\Journal::TITLE_MAX_LENGTH }}</p>
    @error('title')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

<div class="form-group description-editor-group">
    <label for="description-editor">Entry</label>
    <div
        id="description-editor"
        class="description-editor"
        aria-label="Journal entry editor"
    ></div>
    <textarea
        id="description"
        name="body"
        class="description-editor-source"
        hidden
        placeholder="What's on your mind?"
    >{{ old('body', $journal->body ?? '') }}</textarea>
    <p class="form-hint">Rich text editor — bold, links, and lists are kept. HTML is cleaned for safety.</p>
    @error('body')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
