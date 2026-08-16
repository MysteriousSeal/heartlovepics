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
                        <span class="changelog-version">v1.0.37</span>
                        <time class="changelog-date" datetime="2026-08-16">August 16, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Resolved a short list of small bugs found during this round of testing.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.36</span>
                        <time class="changelog-date" datetime="2026-08-14">August 14, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Bot traffic in admin analytics now shows the real crawler name instead of a generic "Bot" label.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.35</span>
                        <time class="changelog-date" datetime="2026-08-14">August 14, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Resolved a short list of small bugs found during this round of testing.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.34</span>
                        <time class="changelog-date" datetime="2026-08-14">August 14, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Resolved a short list of small bugs found during this round of testing.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.33</span>
                        <time class="changelog-date" datetime="2026-08-14">August 14, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Style updates now show up immediately after a release instead of waiting on your browser's cache to expire.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.32</span>
                        <time class="changelog-date" datetime="2026-08-13">August 13, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-change">Change</span>
                            <span class="changelog-text">Removed the Contact page and its links from the header, menu, and footer.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Resolved a short list of small bugs found during this round of testing.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.31</span>
                        <time class="changelog-date" datetime="2026-08-13">August 13, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Sort the homepage by date, views, or likes with buttons next to the gallery heading.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Redesigned the Contact page with a clearer two-column layout and a quick-tips panel.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Enlarged the mobile menu button and removed a duplicate contact icon on mobile.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Resolved a short list of small bugs found during this round of testing.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.30</span>
                        <time class="changelog-date" datetime="2026-08-13">August 13, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Added Artists and Parodies browse pages, sortable A&ndash;Z or by most posts, matching the existing Tags page.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-change">Change</span>
                            <span class="changelog-text">Parody cover images now crop to a 2:3 portrait instead of 9:16.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.29</span>
                        <time class="changelog-date" datetime="2026-08-13">August 13, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">New</span>
                            <span class="changelog-text">Posts can now credit a parody (the comic, movie, or game they're based on) — shown on the post page and clickable through to every other post parodying the same thing.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Resolved a short list of small bugs found during this round of testing.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.28</span>
                        <time class="changelog-date" datetime="2026-08-12">August 12, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Rewrote the homepage's About section and footer to better describe what HeartLovePics actually is, and refreshed the Terms of Use and Privacy Policy to match how the site works today.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Cleared up a small batch of inconsistencies found while reviewing recent pages.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.27</span>
                        <time class="changelog-date" datetime="2026-08-12">August 12, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Gallery post cards: the view, comment, like, and bookmark buttons now stretch evenly across the full card width, with cleaner spacing above and below.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Sorted out a few small inconsistencies spotted across recent pages.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Nudged some queries to run leaner, keeping things responsive as the gallery grows.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.26</span>
                        <time class="changelog-date" datetime="2026-08-12">August 12, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Corrected a few pages that were displaying stale or incorrect data in some cases.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Cut down database work on a couple of busy pages for quicker load times.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.25</span>
                        <time class="changelog-date" datetime="2026-08-12">August 12, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Mobile menu is cleaner: search, navigation, and contact live in the hamburger panel, with full-width controls.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Homepage activity bar sits flush under the nav like a second subheader.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Desktop header no longer shows a double search bar or a broken layout.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.24</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Squashed a handful of small bugs reported across the site over the past few days.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Trimmed some slow spots so pages feel snappier when loading and scrolling.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.23</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Fixed a batch of small bugs turned up by user reports this week.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Made a handful of pages load and respond noticeably faster.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.22</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-feature">Feature</span>
                            <span class="changelog-text">New contact page so you can get in touch with HeartLovePics.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Squashed several rough edges found while testing recent changes.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Trimmed unnecessary work behind the scenes to speed things up.</span>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="changelog-entry">
                <div class="changelog-entry-marker" aria-hidden="true"></div>
                <div class="changelog-entry-card">
                    <header class="changelog-entry-heading">
                        <span class="changelog-version">v1.0.21</span>
                        <time class="changelog-date" datetime="2026-08-11">August 11, 2026</time>
                    </header>
                    <ul class="changelog-list">
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Footer now says the site is made from scratch with love.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-fix">Fix</span>
                            <span class="changelog-text">Patched a handful of edge-case bugs across a few pages.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Sped up a few slower queries that were dragging on some pages.</span>
                        </li>
                    </ul>
                </div>
            </li>

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
                            <span class="changelog-text">Resolved a handful of glitches spotted in day-to-day use.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Reduced load times across a few of the busier pages.</span>
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
                            <span class="changelog-text">Cleared out a few lingering bugs from the last couple of releases.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Tuned a few pages to feel snappier when scrolling and loading.</span>
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
                            <span class="changelog-text">Fixed assorted small issues affecting a handful of pages.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Cut down on unnecessary requests to speed up browsing.</span>
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
                            <span class="changelog-text">Ironed out a few quirks reported by early users.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Optimized a few slow-loading pages for a smoother feel.</span>
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
                            <span class="changelog-text">Knocked out a short list of minor bugs.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Shaved some load time off a handful of pages.</span>
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
                            <span class="changelog-text">Fixed a few things that were behaving oddly under certain conditions.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Improved responsiveness on a few pages that felt sluggish.</span>
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
                            <span class="changelog-text">Addressed a handful of small but annoying bugs.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Streamlined a few background processes for better speed.</span>
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
                            <span class="changelog-text">Cleaned up a few rough spots left over from earlier work.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Made the gallery feel a bit snappier while browsing.</span>
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
                            <span class="changelog-text">Fixed a small round of bugs found during routine testing.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Reduced some unnecessary overhead to keep things quick.</span>
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
                            <span class="changelog-text">Tidied up a few inconsistencies and broken bits.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Polished a few slow spots so pages open more smoothly.</span>
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
                            <span class="changelog-text">Chased down and fixed a few small bugs across the site.</span>
                        </li>
                        <li>
                            <span class="changelog-tag changelog-tag-polish">Polish</span>
                            <span class="changelog-text">Lightened a few heavy pages so they open quicker.</span>
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
