@extends('layouts.app')

@section('title', 'Artists — HeartLovePics')
@section('meta_description', 'Browse artists credited on HeartLovePics.')
@section('canonical', route('gallery.artists'))

@section('content')
    @php
        $artistTotal = $artists->count();
        $imageTotal = $artists->sum('images_count');
    @endphp

    <section class="tags-index">
        <header class="tags-index-hero">
            <p class="tags-index-kicker">Browse</p>
            <h1 class="tags-index-title">Artists</h1>
            <p class="tags-index-lede">
                Explore the gallery by artist credit.
            </p>

            @if ($artistTotal > 0)
                <div class="tags-index-stats" aria-label="Artist statistics">
                    <div class="tags-index-stat">
                        <span class="tags-index-stat-value">{{ number_format($artistTotal) }}</span>
                        <span class="tags-index-stat-label">{{ \Illuminate\Support\Str::plural('artist', $artistTotal) }}</span>
                    </div>
                    <div class="tags-index-stat">
                        <span class="tags-index-stat-value">{{ number_format($imageTotal) }}</span>
                        <span class="tags-index-stat-label">credited {{ \Illuminate\Support\Str::plural('post', $imageTotal) }}</span>
                    </div>
                </div>
            @endif
        </header>

        @if (! $artists->isEmpty())
            <div class="tags-index-toolbar">
                <nav class="tags-index-sort" aria-label="Sort artists">
                    <a
                        href="{{ route('gallery.artists', ['sort' => 'name']) }}"
                        class="tags-index-sort-tab {{ $sort === 'name' ? 'active' : '' }}"
                    >
                        A–Z
                    </a>
                    <a
                        href="{{ route('gallery.artists', ['sort' => 'count']) }}"
                        class="tags-index-sort-tab {{ $sort === 'count' ? 'active' : '' }}"
                    >
                        Most posts
                    </a>
                </nav>
                <p class="tags-index-sort-hint">
                    @if ($sort === 'count')
                        Sorted by public post count
                    @else
                        Grouped alphabetically
                    @endif
                </p>
            </div>
        @endif

        @if ($artists->isEmpty())
            @include('partials.empty-state-suggestions', [
                'message' => 'No artists yet.',
                'suggestions' => [
                    ['label' => 'Browse the gallery', 'url' => route('home')],
                    ['label' => 'See random picks', 'url' => route('gallery.random')],
                ],
            ])
        @elseif ($sort === 'name')
            @php
                $groups = $artists->groupBy(function ($artist) {
                    $first = mb_strtoupper(mb_substr($artist->name, 0, 1));

                    return preg_match('/^[A-Z]$/u', $first) ? $first : '#';
                });
            @endphp

            <div class="tags-index-groups">
                @foreach ($groups as $letter => $groupArtists)
                    <section class="tags-index-group" aria-labelledby="artists-letter-{{ $letter === '#' ? 'other' : $letter }}">
                        <h2 class="tags-index-letter" id="artists-letter-{{ $letter === '#' ? 'other' : $letter }}">
                            <span class="tags-index-letter-mark">{{ $letter }}</span>
                            <span class="tags-index-letter-count">{{ $groupArtists->count() }}</span>
                        </h2>
                        <div class="tags-index-grid">
                            @foreach ($groupArtists as $artist)
                                @include('partials.artists-index-card', ['artist' => $artist])
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @else
            <div class="tags-index-grid">
                @foreach ($artists as $index => $artist)
                    @include('partials.artists-index-card', [
                        'artist' => $artist,
                        'rank' => $index < 3 ? $index + 1 : null,
                    ])
                @endforeach
            </div>
        @endif
    </section>
@endsection
