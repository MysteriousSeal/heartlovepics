<form class="gallery-search" action="{{ route('gallery.search') }}" method="GET" role="search">
    <label class="sr-only" for="gallery-search-input">Search images</label>
    <input
        type="search"
        id="gallery-search-input"
        name="q"
        class="gallery-search-input"
        value="{{ request('q', '') }}"
        placeholder="Search images…"
        maxlength="100"
        autocomplete="off"
    >
    <button type="submit" class="gallery-search-btn" aria-label="Search">
        <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
            <circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
            <path d="M16 16l4.5 4.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </button>
</form>