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
                        <span class="changelog-version">v1.0.20</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.19</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.18</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.17</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.16</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Changelog entries are split into clearer, separate bullets.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Changelog tags stay on the left with text always starting beside them and wrapping under the text.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.15</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.14</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.13</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.12</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.11</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.10</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.9</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bug fixes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Design improvements.</span>
                        </li>
                    </ul>
                </div>
            </li>

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
                            <span class="changelog-text">The tags page content now lines up with the site header width.</span>
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
                            <span class="changelog-text">Redesigned the profile edit page into clearer sections.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Profile edit sections cover identity, appearance, bio, and password.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Artist pages use a two-column layout.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Artist page left column shows avatar, post count, and links.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Artist page right column shows the description.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Description editors support a Visual / HTML mode toggle.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">You can paste or edit markup as well as use the rich text tools.</span>
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
                            <span class="changelog-text">Artist credits on gallery cards show how many posts that artist has.</span>
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
                            <span class="changelog-text">Artists can now have an avatar.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Artist avatars appear next to their name in the &ldquo;Original artist&rdquo; credit on posts.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Artist avatars appear on gallery cards when an artist is credited.</span>
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
                            <span class="changelog-text">You can change your username from your profile edit page.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Username availability is checked live as you type.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-change">Change</span>
                            <span class="changelog-text">Uploading new posts is now limited to admins.</span>
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
                            <span class="changelog-text">Redesigned the Terms of Use page with section cards.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Redesigned the Privacy Policy page with section cards.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Legal pages include a table of contents and clearer hierarchy.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Redesigned the tags index with stats and a cleaner toolbar.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Tags index supports A–Z grouping.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Tags index supports ranked &ldquo;most used&rdquo; cards.</span>
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
                            <span class="changelog-text">Redesigned the changelog page with a timeline layout.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Changelog shows release cards and a current-version badge.</span>
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
                            <span class="changelog-text">Added this changelog page.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Added a version number in the site footer.</span>
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
