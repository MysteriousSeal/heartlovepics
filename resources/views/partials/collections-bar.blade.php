@php
    $activeCollection = $activeCollection ?? null;
@endphp

<div class="collections-bar">
    <a
        href="{{ route('users.bookmarks', $user) }}"
        class="collection-chip {{ ! $activeCollection ? 'active' : '' }}"
    >
        All bookmarks
    </a>

    @foreach ($collections as $collection)
        <span class="collection-chip-wrap">
            <a
                href="{{ route('users.bookmarks', ['user' => $user, 'collection' => $collection->id]) }}"
                class="collection-chip {{ $activeCollection?->id === $collection->id ? 'active' : '' }}"
            >
                {{ $collection->name }}
                <span class="collection-chip-count">{{ $collection->images_count }}</span>
            </a>
            <form
                method="POST"
                action="{{ route('collections.destroy', $collection) }}"
                class="collection-delete-form"
                data-confirm="Delete the &quot;{{ $collection->name }}&quot; collection? Images stay bookmarked."
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="collection-delete-btn" aria-label="Delete {{ $collection->name }}">&times;</button>
            </form>
        </span>
    @endforeach

    <form method="POST" action="{{ route('collections.store') }}" class="collection-create-form">
        @csrf
        <input
            type="text"
            name="name"
            class="collection-create-input"
            placeholder="New collection…"
            maxlength="{{ \App\Models\Collection::MAX_NAME_LENGTH }}"
            required
        >
        <button type="submit" class="btn btn-secondary btn-sm">+ Add</button>
    </form>
</div>
@error('name')
    <p class="form-error">{{ $message }}</p>
@enderror
