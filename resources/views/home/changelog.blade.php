@extends('layouts.app')

@section('title', 'Changelog — HeartLovePics')
@section('meta_description', 'What\'s new on HeartLovePics — recent updates, fixes, and features.')
@section('canonical', route('pages.changelog'))
@section('robots', 'noindex, follow')

@section('content')
    <article class="static-page">
        <h1>Changelog</h1>
        <p class="static-page-updated">What's changed, newest first.</p>

        <div class="changelog-entry">
            <h3 class="changelog-entry-heading">
                <span class="changelog-version">v1.0.1</span>
                <time class="changelog-date" datetime="2026-08-10">August 10, 2026</time>
            </h3>
            <ul>
                <li>Added this changelog page, and a version number in the footer.</li>
            </ul>
        </div>

        <p class="static-page-back">
            <a href="{{ route('home') }}">&larr; Back to gallery</a>
        </p>
    </article>
@endsection
