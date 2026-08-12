@extends('layouts.app')

@section('title', 'Parodies — HeartLovePics')
@section('meta_description', 'Browse parodies on HeartLovePics.')
@section('canonical', route('gallery.parodies'))

@section('content')
    @php
        $parodyTotal = $parodies->count();
        $imageTotal = $parodies->sum('images_count');
    @endphp

    <section class="tags-index">
        <header class="tags-index-hero">
            <p class="tags-index-kicker">Browse</p>
            <h1 class="tags-index-title">Parodies</h1>
            <p class="tags-index-lede">
                Explore the gallery by parody.
            </p>

            @if ($parodyTotal > 0)
                <div class="tags-index-stats" aria-label="Parody statistics">
                    <div class="tags-index-stat">
                        <span class="tags-index-stat-value">{{ number_format($parodyTotal) }}</span>
                        <span class="tags-index-stat-label">{{ \Illuminate\Support\Str::plural('parody', $parodyTotal) }}</span>
                    </div>
                    <div class="tags-index-stat">
                        <span class="tags-index-stat-value">{{ number_format($imageTotal) }}</span>
                        <span class="tags-index-stat-label">tagged {{ \Illuminate\Support\Str::plural('post', $imageTotal) }}</span>
                    </div>
                </div>
            @endif
        </header>

        @if (! $parodies->isEmpty())
            <div class="tags-index-toolbar">
                <nav class="tags-index-sort" aria-label="Sort parodies">
                    <a
                        href="{{ route('gallery.parodies', ['sort' => 'name']) }}"
                        class="tags-index-sort-tab {{ $sort === 'name' ? 'active' : '' }}"
                    >
                        A–Z
                    </a>
                    <a
                        href="{{ route('gallery.parodies', ['sort' => 'count']) }}"
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

        @if ($parodies->isEmpty())
            @include('partials.empty-state-suggestions', [
                'message' => 'No parodies yet.',
                'suggestions' => [
                    ['label' => 'Browse the gallery', 'url' => route('home')],
                    ['label' => 'See random picks', 'url' => route('gallery.random')],
                ],
            ])
        @elseif ($sort === 'name')
            @php
                $groups = $parodies->groupBy(function ($parody) {
                    $first = mb_strtoupper(mb_substr($parody->name, 0, 1));

                    return preg_match('/^[A-Z]$/u', $first) ? $first : '#';
                });
            @endphp

            <div class="tags-index-groups">
                @foreach ($groups as $letter => $groupParodies)
                    <section class="tags-index-group" aria-labelledby="parodies-letter-{{ $letter === '#' ? 'other' : $letter }}">
                        <h2 class="tags-index-letter" id="parodies-letter-{{ $letter === '#' ? 'other' : $letter }}">
                            <span class="tags-index-letter-mark">{{ $letter }}</span>
                            <span class="tags-index-letter-count">{{ $groupParodies->count() }}</span>
                        </h2>
                        <div class="tags-index-grid">
                            @foreach ($groupParodies as $parody)
                                @include('partials.parodies-index-card', ['parody' => $parody])
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @else
            <div class="tags-index-grid">
                @foreach ($parodies as $index => $parody)
                    @include('partials.parodies-index-card', [
                        'parody' => $parody,
                        'rank' => $index < 3 ? $index + 1 : null,
                    ])
                @endforeach
            </div>
        @endif
    </section>
@endsection
