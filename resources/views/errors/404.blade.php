@extends('layouts.app')

@section('title', 'Page not found — HeartLovePics')
@section('meta_description', 'The page you were looking for could not be found on HeartLovePics.')

@section('content')
    <section class="error-page">
        <p class="error-page-code" aria-hidden="true">404</p>
        <h2 class="error-page-title">This page wandered off</h2>
        <p class="error-page-text">
            The page you're looking for doesn't exist, was moved, or never made it here in the first place.
        </p>

        <form class="error-page-search" action="{{ route('gallery.search') }}" method="GET" role="search">
            <label class="sr-only" for="error-search-input">Search images</label>
            <input
                type="search"
                id="error-search-input"
                name="q"
                class="error-page-search-input"
                placeholder="Search images…"
                maxlength="100"
                autocomplete="off"
            >
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <ul class="error-page-links">
            <li><a href="{{ route('home') }}">Browse the latest gallery</a></li>
            <li><a href="{{ route('gallery.random') }}">See random picks</a></li>
            <li><a href="{{ route('gallery.tags') }}">Explore tags</a></li>
        </ul>
    </section>
@endsection
