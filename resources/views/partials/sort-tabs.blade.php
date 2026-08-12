@php
    $currentSort = match (true) {
        request()->routeIs('gallery.following') => 'following',
        request()->routeIs('gallery.tags', 'gallery.tag') => 'tags',
        request()->routeIs('gallery.artists', 'gallery.artist') => 'artists',
        request()->routeIs('gallery.parodies', 'gallery.parody') => 'parodies',
        request()->routeIs('gallery.search') => null,
        default => 'latest',
    };
@endphp

<nav class="sort-tabs" aria-label="Sort gallery">
    <a
        href="{{ route('home') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'latest' ? 'active' : '' }}"
    >
        Latest
    </a>
    @auth
        <a
            href="{{ route('gallery.following') }}"
            class="sort-tab {{ ($currentSort ?? '') === 'following' ? 'active' : '' }}"
        >
            Following
        </a>
    @endauth
    <a
        href="{{ route('gallery.artists') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'artists' ? 'active' : '' }}"
    >
        Artists
    </a>
    <a
        href="{{ route('gallery.parodies') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'parodies' ? 'active' : '' }}"
    >
        Parodies
    </a>
    <a
        href="{{ route('gallery.tags') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'tags' ? 'active' : '' }}"
    >
        Tags
    </a>
</nav>