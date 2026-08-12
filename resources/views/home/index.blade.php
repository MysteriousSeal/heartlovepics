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

    @if (! in_array($sort ?? '', ['search', 'tag', 'artist', 'parody'], true))
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

    @if (($sort ?? '') === 'parody' && ($parodyName ?? null))
        @include('partials.parody-profile-header', [
            'parodyName' => $parodyName,
            'parody' => $parodyModel ?? null,
            'postCount' => $images->total(),
        ])

        @if ($images->total() === 0)
            @include('partials.empty-state-suggestions', [
                'message' => 'No images parody this.',
                'suggestions' => [
                    ['label' => 'Back to the gallery', 'url' => route('home')],
                    ['label' => 'Try random picks', 'url' => route('gallery.random')],
                ],
            ])
        @endif
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
                HeartLovePics is a gallery built around the story, not just the image. Comics,
                illustrated sets, and one-off pieces all belong here, whatever the format,
                because they're posted for telling you something, not just showing you
                something. No single genre owns the place; if it's got a story worth
                following, it fits.
            </p>

            <div class="home-about-columns">
                <div class="home-about-col">
                    <h3 class="home-about-col-title">Browse &amp; discover</h3>
                    <p>
                        New posts land in the gallery all the time. Start with the
                        <a href="{{ route('home') }}">latest uploads</a>, see what everyone's
                        talking about via <a href="{{ route('gallery.likes') }}">most liked</a>
                        and <a href="{{ route('gallery.views') }}">most viewed</a>, or hit
                        <a href="{{ route('gallery.random') }}">random</a> and let the site pick
                        your next read for you. Know exactly what you're in the mood for?
                        <a href="{{ route('gallery.search') }}">Search</a> by title or
                        description.
                    </p>
                    <p>
                        Prefer to browse by genre, kink, or theme? Head to
                        <a href="{{ route('gallery.tags') }}">all tags</a> and dig through the
                        gallery your way. Every tag page lists every public post carrying that
                        label, so you can follow one thread as far as it goes.
                    </p>
                </div>

                <div class="home-about-col">
                    <h3 class="home-about-col-title">Artists &amp; collections</h3>
                    <p>
                        Every post credits the artist who made it, with links out to their
                        DeviantArt, FurAffinity, or Patreon when they have one. Think of this as
                        a showcase for their work, not a replacement for supporting it directly.
                        Open a post to read the full story, comment, or follow the artist so
                        their next piece lands in your feed. Bookmarks and collections let you
                        save favorites and organize them your own way.
                    </p>
                    <p>
                        Looking around costs nothing and needs no account. Want likes, bookmarks,
                        collections, and a following feed tied to your own profile? You can
                        <a href="{{ route('register') }}">create an account</a> or
                        <a href="{{ route('login') }}">log in</a> in seconds. Read how we handle
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
