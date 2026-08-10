@extends('layouts.app')

@section('title', 'Tags — HeartLovePics')
@section('meta_description', 'Browse all image tags on HeartLovePics and explore photos by topic.')
@section('canonical', route('gallery.tags'))

@section('content')
    <section class="tags-index">
        <header class="tags-index-header">
            <h2>Tags</h2>
            <p class="tags-index-intro">Browse the gallery by topic. Each tag links to its image collection.</p>
        </header>

        @if (! $tags->isEmpty())
            <nav class="tags-index-sort" aria-label="Sort tags">
                <a
                    href="{{ route('gallery.tags', ['sort' => 'name']) }}"
                    class="tags-index-sort-tab {{ $sort === 'name' ? 'active' : '' }}"
                >
                    Name
                </a>
                <a
                    href="{{ route('gallery.tags', ['sort' => 'count']) }}"
                    class="tags-index-sort-tab {{ $sort === 'count' ? 'active' : '' }}"
                >
                    Post count
                </a>
            </nav>
        @endif

        @if ($tags->isEmpty())
            @include('partials.empty-state-suggestions', [
                'message' => 'No tags yet.',
                'suggestions' => [
                    ['label' => 'Browse the gallery', 'url' => route('home')],
                    ['label' => 'See random picks', 'url' => route('gallery.random')],
                ],
            ])
        @else
            <div class="tags-index-grid">
                @foreach ($tags as $tag)
                    <a href="{{ route('gallery.tag', $tag->name) }}" class="tags-index-card">
                        <span class="tags-index-card-name">{{ $tag->name }}</span>
                        <span class="tags-index-card-count">
                            {{ number_format($tag->images_count) }}
                            {{ \Illuminate\Support\Str::plural('image', $tag->images_count) }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection