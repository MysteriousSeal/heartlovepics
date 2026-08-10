<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="site-footer-top">
            <div class="site-footer-brand">
                <a href="{{ route('home') }}" class="site-footer-brand-name">
                    <span class="logo-primary">HeartLove</span><span class="logo-secondary">Pics</span>
                </a>
                <p class="site-footer-about">
                    HeartLovePics is a free gallery for original photo stories.
                    Browse the latest uploads, sort by likes or views, explore images by tag,
                    and follow creators you like — no account needed to look around.
                </p>
            </div>

            <nav class="site-footer-col" aria-labelledby="footer-explore-heading">
                <h2 id="footer-explore-heading" class="site-footer-heading">Explore</h2>
                <ul class="site-footer-links">
                    <li><a href="{{ route('home') }}">Latest posts</a></li>
                    <li><a href="{{ route('gallery.likes') }}">Most liked</a></li>
                    <li><a href="{{ route('gallery.views') }}">Most viewed</a></li>
                    <li><a href="{{ route('gallery.random') }}">Random images</a></li>
                    <li><a href="{{ route('gallery.search') }}">Search gallery</a></li>
                </ul>
            </nav>

            <nav class="site-footer-col" aria-labelledby="footer-site-heading">
                <h2 id="footer-site-heading" class="site-footer-heading">Site</h2>
                <ul class="site-footer-links">
                    <li><a href="{{ route('gallery.tags') }}">All tags</a></li>
                    <li><a href="{{ route('pages.terms') }}">Terms of use</a></li>
                    <li><a href="{{ route('pages.privacy') }}">Privacy policy</a></li>
                    <li><a href="{{ route('sitemap') }}">XML sitemap</a></li>
                    @guest
                        <li><a href="{{ route('register') }}">Create account</a></li>
                        <li><a href="{{ route('login') }}">Log in</a></li>
                    @else
                        @if (auth()->user()->canPost())
                            <li><a href="{{ route('profile.images.create') }}">Upload image</a></li>
                        @endif
                        <li><a href="{{ route('users.show', auth()->user()) }}">Your profile</a></li>
                    @endguest
                </ul>
            </nav>

            @if (! empty($footerPopularTags))
                <nav class="site-footer-col site-footer-col-tags" aria-labelledby="footer-tags-heading">
                    <div class="site-footer-heading-row">
                        <h2 id="footer-tags-heading" class="site-footer-heading">Popular tags</h2>
                        <a href="{{ route('gallery.tags') }}" class="site-footer-heading-link">View all</a>
                    </div>
                    <ul class="site-footer-tag-list">
                        @foreach ($footerPopularTags as $tag)
                            <li>
                                <a href="{{ route('gallery.tag', $tag['name']) }}" class="site-footer-tag">
                                    <span class="site-footer-tag-name">{{ $tag['name'] }}</span>
                                    @if (! empty($tag['count']))
                                        <span class="site-footer-tag-count">{{ number_format($tag['count']) }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </div>

        <div class="site-footer-bottom">
            <p class="site-footer-copy">
                &copy; {{ date('Y') }}
                <a href="{{ route('home') }}">HeartLovePics</a>
                <span class="site-footer-copy-sep" aria-hidden="true">·</span>
                <span class="site-footer-copy-note">A quiet place for photo stories</span>
            </p>
        </div>
    </div>
</footer>
