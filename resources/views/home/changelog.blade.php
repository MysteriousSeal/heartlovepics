@extends('layouts.app')

@section('title', 'Changelog — HeartLovePics')
@section('meta_description', 'What\'s new on HeartLovePics — recent updates, fixes, and features.')
@section('canonical', route('pages.changelog'))
@section('robots', 'noindex, follow')

@section('content')
    <article class="static-page changelog-page">
        <header class="changelog-hero">
            <p class="changelog-kicker">Product updates</p>
            <h1>Changelog</h1>
            <p class="changelog-lede">
                What&rsquo;s new on HeartLovePics — fixes, features, and polish. Newest first.
            </p>
            <div class="changelog-current">
                <span class="changelog-current-label">Current version</span>
                <span class="changelog-current-version">v{{ config('app.version') }}</span>
            </div>
        </header>

        <ol class="changelog-timeline">
            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.8</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            The tags page content now lines up with the site header width.
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.7</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            Redesigned the profile edit page into clearer sections for identity,
                            appearance, bio, and password.
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            Artist pages now use a two-column layout: avatar, post count, and links
                            on the left; description on the right.
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            Description editors support a Visual / HTML mode toggle, so you can
                            paste or edit markup as well as use the rich text tools.
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.6</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            Artist credits on gallery cards now show how many posts that artist
                            has on the site.
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.5</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            Artists can now have an avatar. It appears next to their name in the
                            &ldquo;Original artist&rdquo; credit on posts, and on gallery cards
                            when an artist is credited.
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.4</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            You can now change your username from your profile edit page —
                            availability is checked live as you type.
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-change">Change</span>
                            Uploading new posts is now limited to admins.
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.3</span>
                        <time class="changelog-date" datetime="2026-08-10">August 10, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            Redesigned the Terms of Use and Privacy Policy pages with section
                            cards, a table of contents, and clearer hierarchy.
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            Redesigned the tags index with stats, A–Z grouping, ranked “most used”
                            cards, and a cleaner toolbar.
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.2</span>
                        <time class="changelog-date" datetime="2026-08-10">August 10, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            Post edit page: moved the &ldquo;Original artist&rdquo; field&rsquo;s hint
                            text below the input, to match the Alt text field.
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            Redesigned the changelog page with a timeline layout, release cards,
                            and a current-version badge.
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.1</span>
                        <time class="changelog-date" datetime="2026-08-10">August 10, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            Added this changelog page, and a version number in the footer.
                        </li>
                    </ul>
                </div>
            </li>
        </ol>

        <p class="static-page-back">
            <a href="{{ route('home') }}" class="changelog-back-link">&larr; Back to gallery</a>
        </p>
    </article>
@endsection
