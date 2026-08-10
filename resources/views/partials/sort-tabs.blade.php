@php
    $currentSort = match (true) {
        request()->routeIs('gallery.following') => 'following',
        request()->routeIs('gallery.tags', 'gallery.tag') => 'tags',
        request()->routeIs('gallery.views') => 'views',
        request()->routeIs('gallery.likes') => 'likes',
        request()->routeIs('gallery.random') => 'random',
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
        href="{{ route('gallery.views') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'views' ? 'active' : '' }}"
    >
        Views
    </a>
    <a
        href="{{ route('gallery.likes') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'likes' ? 'active' : '' }}"
    >
        Likes
    </a>
    <a
        href="{{ route('gallery.random') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'random' ? 'active' : '' }}"
    >
        Random
    </a>
    <a
        href="{{ route('gallery.tags') }}"
        class="sort-tab {{ ($currentSort ?? '') === 'tags' ? 'active' : '' }}"
    >
        Tags
    </a>
</nav>