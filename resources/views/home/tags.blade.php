@extends('layouts.app')

@section('title', 'Tags — HeartLovePics')
@section('meta_description', 'Browse all image tags on HeartLovePics and explore photos by topic.')
@section('canonical', route('gallery.tags'))

@section('content')
    @php
        $tagTotal = $tags->count();
        $imageTotal = $tags->sum('images_count');
    @endphp

    <section class="tags-index">
        <header class="tags-index-hero">
            <p class="tags-index-kicker">Browse</p>
            <h1 class="tags-index-title">Tags</h1>
            <p class="tags-index-lede">
                Explore the gallery by topic. Each tag opens its public image collection.
            </p>

            @if ($tagTotal > 0)
                <div class="tags-index-stats" aria-label="Tag statistics">
                    <div class="tags-index-stat">
                        <span class="tags-index-stat-value">{{ number_format($tagTotal) }}</span>
                        <span class="tags-index-stat-label">{{ \Illuminate\Support\Str::plural('tag', $tagTotal) }}</span>
                    </div>
                    <div class="tags-index-stat">
                        <span class="tags-index-stat-value">{{ number_format($imageTotal) }}</span>
                        <span class="tags-index-stat-label">tagged {{ \Illuminate\Support\Str::plural('post', $imageTotal) }}</span>
                    </div>
                </div>
            @endif
        </header>

        @if (! $tags->isEmpty())
            <div class="tags-index-toolbar">
                <nav class="tags-index-sort" aria-label="Sort tags">
                    <a
                        href="{{ route('gallery.tags', ['sort' => 'name']) }}"
                        class="tags-index-sort-tab {{ $sort === 'name' ? 'active' : '' }}"
                    >
                        A–Z
                    </a>
                    <a
                        href="{{ route('gallery.tags', ['sort' => 'count']) }}"
                        class="tags-index-sort-tab {{ $sort === 'count' ? 'active' : '' }}"
                    >
                        Most used
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

        @if ($tags->isEmpty())
            @include('partials.empty-state-suggestions', [
                'message' => 'No tags yet.',
                'suggestions' => [
                    ['label' => 'Browse the gallery', 'url' => route('home')],
                    ['label' => 'See random picks', 'url' => route('gallery.random')],
                ],
            ])
        @elseif ($sort === 'name')
            @php
                $groups = $tags->groupBy(function ($tag) {
                    $first = mb_strtoupper(mb_substr($tag->name, 0, 1));

                    return preg_match('/^[A-Z]$/u', $first) ? $first : '#';
                });
            @endphp

            <div class="tags-index-groups">
                @foreach ($groups as $letter => $groupTags)
                    <section class="tags-index-group" aria-labelledby="tags-letter-{{ $letter === '#' ? 'other' : $letter }}">
                        <h2 class="tags-index-letter" id="tags-letter-{{ $letter === '#' ? 'other' : $letter }}">
                            <span class="tags-index-letter-mark">{{ $letter }}</span>
                            <span class="tags-index-letter-count">{{ $groupTags->count() }}</span>
                        </h2>
                        <div class="tags-index-grid">
                            @foreach ($groupTags as $tag)
                                @include('partials.tags-index-card', ['tag' => $tag])
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @else
            <div class="tags-index-grid">
                @foreach ($tags as $index => $tag)
                    @include('partials.tags-index-card', [
                        'tag' => $tag,
                        'rank' => $index < 3 ? $index + 1 : null,
                    ])
                @endforeach
            </div>
        @endif
    </section>
@endsection
