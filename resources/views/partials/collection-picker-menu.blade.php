@php
    $bookmarked = $bookmarked ?? false;
    $userCollections = $userCollections ?? collect();
    $collectionMap = $collectionMap ?? [];
@endphp

@if (! $bookmarked)
    <p class="collection-picker-hint">Bookmark this image first to add it to a collection.</p>
@elseif ($userCollections->isEmpty())
    <p class="collection-picker-hint">
        You don't have any collections yet. Create one from your
        <a href="{{ route('users.bookmarks', auth()->user()) }}">bookmarks page</a>.
    </p>
@else
    <ul class="collection-picker-list">
        @foreach ($userCollections as $collection)
            <li>
                <label>
                    <input
                        type="checkbox"
                        data-url="{{ route('collections.images.toggle', [$collection, $image]) }}"
                        {{ ($collectionMap[$collection->id] ?? false) ? 'checked' : '' }}
                    >
                    {{ $collection->name }}
                </label>
            </li>
        @endforeach
    </ul>
@endif
