@extends('layouts.app')

@section('title', $pageMeta['title'])
@section('meta_description', $pageMeta['description'])
@section('canonical', $pageMeta['canonical'])
@if ($pageMeta['noindex'] ?? false)
    @section('robots', 'noindex, follow')
@endif

@push('meta')
    @include('partials.og-meta', [
        'title' => $pageMeta['title'],
        'description' => $pageMeta['description'],
        'url' => $pageMeta['canonical'],
        'image' => $pageMeta['og_image'] ?? null,
    ])

    @if ($sort === 'latest' && ($isFirstPage ?? false))
        <script type="application/ld+json">
            {!! json_encode([
                '@@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'HeartLovePics',
                'url' => url('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => route('gallery.search') . '?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ], JSON_UNESCAPED_SLASHES) !!}
        </script>

        @if ($imageListSchema ?? null)
            <script type="application/ld+json">
                {!! json_encode($imageListSchema, JSON_UNESCAPED_SLASHES) !!}
            </script>
        @endif
    @endif
@endpush

@if ($lcpImage ?? null)
    @push('preload')
        <link rel="preload" as="image" href="{{ $lcpImage->thumbnail_direct_url }}" fetchpriority="high">
    @endpush
@endif

@section('content')
    @include('partials.gallery-activity-bar', [
        'activity' => $activityStats,
        'totalVisits' => $totalVisits,
        'totalImages' => $totalImages,
        'showNsfw' => $showNsfw,
        'ageConfirmed' => $ageConfirmed,
    ])

    @include('partials.nsfw-age-modal')

    @if (! in_array($sort ?? '', ['search', 'tag', 'artist'], true))
        <h1 class="gallery-heading">{{ $pageMeta['heading'] }}</h1>
    @endif

    @if (($sort ?? '') === 'search')
        <div class="search-results-header">
            @if ($searchQuery)
                <h1 class="search-results-title">Results for “{{ $searchQuery }}”</h1>
                @if ($images->total() === 0)
                    @include('partials.empty-state-suggestions', [
                        'message' => 'No images matched your search.',
                        'suggestions' => [
                            ['label' => 'Browse the latest gallery', 'url' => route('home')],
                            ['label' => 'Explore tags', 'url' => route('gallery.tags')],
                            ['label' => 'Try random picks', 'url' => route('gallery.random')],
                        ],
                    ])
                @else
                    <p class="search-results-count">{{ number_format($images->total()) }} {{ \Illuminate\Support\Str::plural('image', $images->total()) }} found</p>
                @endif
            @else
                <h1 class="search-results-title">Search the gallery</h1>
                @include('partials.empty-state-suggestions', [
                    'message' => 'Enter a title or description above to find images.',
                    'suggestions' => [
                        ['label' => 'Browse the latest gallery', 'url' => route('home')],
                        ['label' => 'See most liked images', 'url' => route('gallery.likes')],
                        ['label' => 'Explore tags', 'url' => route('gallery.tags')],
                    ],
                ])
            @endif
        </div>
    @endif

    @if (($sort ?? '') === 'tag' && ($tag ?? null))
        <div class="search-results-header">
            <h1 class="search-results-title">Tag: {{ $tag->name }}</h1>
            @if ($images->total() === 0)
                @include('partials.empty-state-suggestions', [
                    'message' => 'No images are tagged with this label.',
                    'suggestions' => [
                        ['label' => 'Browse all tags', 'url' => route('gallery.tags')],
                        ['label' => 'Back to the gallery', 'url' => route('home')],
                        ['label' => 'Try random picks', 'url' => route('gallery.random')],
                    ],
                ])
            @else
                <p class="search-results-count">{{ number_format($images->total()) }} {{ \Illuminate\Support\Str::plural('image', $images->total()) }} found</p>
            @endif
        </div>
    @endif

    @if (($sort ?? '') === 'artist' && ($artistName ?? null))
        @include('partials.artist-profile-header', [
            'artistName' => $artistName,
            'artist' => $artistModel ?? null,
            'postCount' => $images->total(),
        ])

        @if ($images->total() === 0)
            @include('partials.empty-state-suggestions', [
                'message' => 'No images are credited to this artist.',
                'suggestions' => [
                    ['label' => 'Browse all tags', 'url' => route('gallery.tags')],
                    ['label' => 'Back to the gallery', 'url' => route('home')],
                    ['label' => 'Try random picks', 'url' => route('gallery.random')],
                ],
            ])
        @endif
    @endif

    @if (($sort ?? '') === 'following' && $images->isEmpty())
        @include('partials.empty-state-suggestions', [
            'message' => auth()->user()->following()->exists()
                ? 'The people you follow have not shared any images yet.'
                : 'You are not following anyone yet.',
            'suggestions' => [
                ['label' => 'Browse the latest gallery', 'url' => route('home')],
                ['label' => 'Explore tags', 'url' => route('gallery.tags')],
                ['label' => 'See random picks', 'url' => route('gallery.random')],
            ],
        ])
    @elseif ($images->isEmpty() && ! in_array($sort ?? '', ['search', 'tag', 'artist', 'following'], true))
        @include('partials.empty-state-suggestions', [
            'message' => 'No images to display yet.',
            'suggestions' => array_values(array_filter([
                (auth()->check() && auth()->user()->canUploadImages())
                    ? ['label' => 'Upload an image', 'url' => route('profile.images.create')]
                    : null,
                ['label' => 'Explore tags', 'url' => route('gallery.tags')],
                ['label' => 'See random picks', 'url' => route('gallery.random')],
            ])),
        ])
    @elseif ($images->isNotEmpty())
        <div
            class="masonry"
            id="gallery"
            data-next-page="{{ $images->nextPageUrl() }}"
            data-has-more="{{ $images->hasMorePages() ? 'true' : 'false' }}"
            data-loaded-count="{{ $images->count() }}"
        >
            @include('partials.gallery-columns', compact('columns', 'likedMap', 'bookmarkMap', 'lcpImage', 'isFirstPage', 'showNsfw', 'showGalleryAuthors', 'showGalleryArtists'))
        </div>

        <div id="gallery-loader" class="gallery-loader {{ $images->hasMorePages() ? '' : 'hidden' }}">
            <span class="gallery-loader-spinner"></span>
            <span>Loading more images…</span>
        </div>

        <div id="gallery-sentinel" class="gallery-sentinel" aria-hidden="true"></div>
    @endif

    @if ($sort === 'latest' && ($isFirstPage ?? false))
        <section class="home-about" aria-labelledby="home-about-heading">
            <h2 id="home-about-heading" class="home-about-heading">About HeartLovePics</h2>
            <p class="home-about-lead">
                HeartLovePics is a quiet space to share and discover original photo stories —
                browse by tag, follow creators you like, and keep a personal collection of your
                favorites.
            </p>

            <div class="home-about-columns">
                <div class="home-about-col">
                    <h3 class="home-about-col-title">Browse &amp; discover</h3>
                    <p>
                        The gallery is updated with new uploads every day. Start with the
                        <a href="{{ route('home') }}">latest images</a>, see what the community
                        enjoys via <a href="{{ route('gallery.likes') }}">most liked</a> and
                        <a href="{{ route('gallery.views') }}">most viewed</a>, or open a
                        <a href="{{ route('gallery.random') }}">random shuffle</a> when you want
                        something unexpected. You can also
                        <a href="{{ route('gallery.search') }}">search</a> by title or description
                        when you know what you are looking for.
                    </p>
                    <p>
                        Prefer topics over recency?
                        <a href="{{ route('gallery.tags') }}">Browse all tags</a> to explore photo
                        stories by theme — from settings and styles to moods and characters — each
                        tag page lists every public image that uses that label.
                    </p>
                </div>

                <div class="home-about-col">
                    <h3 class="home-about-col-title">Creators &amp; collections</h3>
                    <p>
                        Every public post belongs to a creator profile. Open an image to read the
                        story, view the full gallery when a post has multiple frames, leave a
                        comment, or follow the author so their new work shows up in your
                        following feed. Bookmarks and collections help you save favorites and
                        group images the way you like.
                    </p>
                    <p>
                        Looking is free and does not require an account. When you are ready to
                        share your own photo stories, you can
                        <a href="{{ route('register') }}">create an account</a>,
                        <a href="{{ route('login') }}">log in</a>, and upload from your profile.
                        We keep the experience calm and readable — fewer distractions, more room
                        for the images and the writing that goes with them. Read how we handle
                        data in our <a href="{{ route('pages.privacy') }}">privacy policy</a>.
                    </p>
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/nsfw-toggle.js') }}" defer></script>
    <script src="{{ asset('js/gallery.js') }}" defer></script>
@endpush
