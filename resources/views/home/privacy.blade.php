@extends('layouts.app')

@section('title', 'Privacy Policy — HeartLovePics')
@section('meta_description', 'How HeartLovePics collects, uses, and protects your information, including account data, uploaded content, cookies, and your privacy choices.')
@section('canonical', route('pages.privacy'))

@section('content')
    <article class="static-page legal-page">
        <header class="legal-hero">
            <p class="legal-kicker">Legal</p>
            <h1>Privacy Policy</h1>
            <p class="legal-lede">
                What we collect, how we use it, and the choices you have. HeartLovePics is a
                public image-sharing gallery.
            </p>
            <div class="legal-meta">
                <time class="legal-updated" datetime="2026-08-12">Last updated August 12, 2026</time>
                <a href="{{ route('pages.terms') }}" class="legal-related-link">Terms of Use</a>
            </div>
        </header>

        <div class="legal-intro">
            <p>
                HeartLovePics (“we,” “us,” or “the Service”) is a public image-sharing gallery.
                This policy explains what information we collect, how we use it, and the choices
                you have. By using the Service, you agree to the practices described here.
            </p>
        </div>

        <nav class="legal-toc" aria-label="On this page">
            <p class="legal-toc-label">On this page</p>
            <ol class="legal-toc-list">
                <li><a href="#collect">Information We Collect</a></li>
                <li><a href="#cookies">Cookies &amp; Local Storage</a></li>
                <li><a href="#use">How We Use Your Information</a></li>
                <li><a href="#visibility">Public Visibility</a></li>
                <li><a href="#age">Age &amp; Adult Content</a></li>
                <li><a href="#retention">Data Retention &amp; Deletion</a></li>
                <li><a href="#rights">Your Choices &amp; Rights</a></li>
                <li><a href="#sharing">Data Sharing</a></li>
                <li><a href="#security">Data Security</a></li>
                <li><a href="#children">Children's Privacy</a></li>
                <li><a href="#changes">Changes</a></li>
                <li><a href="#contact">Contact</a></li>
            </ol>
        </nav>

        <div class="legal-sections">
            <section class="legal-section" id="collect">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">1</span>
                    <span class="legal-section-title">Information We Collect</span>
                </h2>
                <div class="legal-section-body">
                    <p><strong>Account information.</strong> If you register, we collect a username and
                    password (stored as a one-way hash — we never see or store your password in plain text).
                    We also record the date you accepted our Terms of Use. You may optionally add a profile
                    avatar, banner image, and bio.</p>

                    <p><strong>Content you submit.</strong> Anything you post yourself is collected and
                    stored, including comments and shouts, journal entries, and the collections you create.
                    Gallery posts themselves (comics, illustrations, and images, together with their
                    titles, descriptions, alt text, and tags) are published by site administrators rather
                    than uploaded by ordinary accounts. See “Public Visibility” below — most of this content
                    is public by design.</p>

                    <p><strong>Usage and engagement data.</strong> We store the actions you take so the
                    Service works as expected: likes, bookmarks, follows, and which images you've viewed.
                    For visitors who aren't logged in, likes are tied to an anonymized identifier (see
                    Cookies below) rather than an account.</p>

                    <p><strong>Site traffic data.</strong> For every page you load, we record the page
                    visited, your IP address, your browser's user agent string, the referring page (if
                    any), an approximate location derived from your IP address (typically country and
                    city), and your account if you're logged in. This is used internally to understand
                    traffic to the Service and is visible only to site administrators; it is not shared
                    with third parties.</p>

                    <p><strong>Contact form.</strong> If you send us a message, we store your name or
                    username, email address, the message itself, and the IP address it was sent from, so we
                    can respond to you and guard against abuse.</p>

                    <p>Separately, where we handle an IP address to let a visitor like an image without
                    creating an account, we don't store that address as part of the like record itself. It's
                    combined with your session ID and passed through a one-way SHA-256 hash before being
                    saved, so the original IP address cannot be recovered from that hash.</p>
                </div>
            </section>

            <section class="legal-section" id="cookies">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">2</span>
                    <span class="legal-section-title">Cookies &amp; Local Storage</span>
                </h2>
                <div class="legal-section-body">
                    <ul>
                        <li><strong>Session cookie</strong> — keeps you signed in and protects forms against
                        cross-site request forgery. Required for the Service to function.</li>
                        <li><strong>Theme preference</strong> — remembers whether you've chosen light or dark
                        mode.</li>
                        <li><strong>NSFW display &amp; age-confirmation cookies</strong> — remember whether
                        you've chosen to show adult content and confirmed you're old enough to see it, so you
                        aren't asked again on every page.</li>
                    </ul>
                    <p>
                        Some display preferences (such as your preferred layout on image pages) are saved
                        only in your browser's local storage. That data never leaves your device or reaches
                        our servers.
                    </p>
                    <p>The site traffic data described under “Information We Collect” above is gathered
                    directly by HeartLovePics, not by a third party. We do not use third-party advertising
                    or analytics trackers, and we do not sell or share your data with data brokers.</p>
                </div>
            </section>

            <section class="legal-section" id="use">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">3</span>
                    <span class="legal-section-title">How We Use Your Information</span>
                </h2>
                <div class="legal-section-body">
                    <ul>
                        <li>To operate core features: publishing images, commenting, liking, bookmarking,
                        following, and collections.</li>
                        <li>To personalize your experience, such as your “Following” feed and saved display
                        preferences.</li>
                        <li>To notify you, within the Service, about activity on your content (likes,
                        comments, follows, and similar). These notifications are shown in-app only — we do
                        not currently send marketing or notification emails.</li>
                        <li>To maintain the security of the Service and prevent abuse, spam, and duplicate
                        voting.</li>
                        <li>To moderate content and enforce our content policies. Actions taken by
                        moderators are recorded in an internal administrative log.</li>
                    </ul>
                </div>
            </section>

            <section class="legal-section" id="visibility">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">4</span>
                    <span class="legal-section-title">Public Visibility of Your Content</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        HeartLovePics is a public gallery. Unless marked private or saved as a draft, any
                        image, title, description, tag, comment, shout, or journal entry you post is visible
                        to anyone who visits the site — including anonymous visitors and search engines. Your
                        username, avatar, and bio are similarly public on your profile. Please don't post
                        anything you don't want to be publicly visible.
                    </p>
                    <p>
                        Private images are visible only to you. Drafts are unpublished and visible only to
                        you until you publish them.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="age">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">5</span>
                    <span class="legal-section-title">Age Requirement &amp; Adult Content</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        You must be at least 18 years old, or the age of majority in your jurisdiction if
                        higher, to use HeartLovePics. The Service hosts user-submitted adult (NSFW) content.
                        NSFW images are blurred by default and require you to confirm your age before they're
                        shown; this confirmation is self-reported, and we have no independent way to verify
                        your age. HeartLovePics is not intended for anyone under 18, and we do not knowingly
                        collect information from minors — see “Children's Privacy” below.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="retention">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">6</span>
                    <span class="legal-section-title">Data Retention &amp; Deletion</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        When you delete an image, it isn't erased immediately — it's held for a short grace
                        period (currently 24 hours) so you can undo the deletion, after which it and its
                        files are permanently removed on an automated schedule. Comments, journal entries,
                        and other content are removed when you delete them or when the account that posted
                        them is deleted.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="rights">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">7</span>
                    <span class="legal-section-title">Your Choices &amp; Rights</span>
                </h2>
                <div class="legal-section-body">
                    <ul>
                        <li>You can edit or delete your images, comments, journal entries, and collections
                        at any time from your account.</li>
                        <li>You can control which in-app notifications you receive from your notification
                        settings.</li>
                        <li>You can turn NSFW content on or off at any time from the gallery toolbar.</li>
                        <li>You can request access to, correction of, or deletion of your account and
                        associated data by contacting the site administrator (see “Contact” below).</li>
                    </ul>
                </div>
            </section>

            <section class="legal-section" id="sharing">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">8</span>
                    <span class="legal-section-title">Data Sharing</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        We do not sell your personal data. We do not use third-party advertising networks or
                        share your data with them. Your data may be accessed by service providers that host
                        our infrastructure (such as our hosting provider), solely to operate the Service, and
                        may be disclosed if required by law or to protect the rights, safety, or property of
                        HeartLovePics or its users.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="security">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">9</span>
                    <span class="legal-section-title">Data Security</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        We use industry-standard measures to protect your data, including password hashing
                        and hashed rather than raw storage of IP addresses used for anonymous likes. No
                        method of storage or transmission is completely secure, and we can't guarantee
                        absolute security.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="children">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">10</span>
                    <span class="legal-section-title">Children's Privacy</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        HeartLovePics is not directed at, and is not intended for use by, anyone under 18.
                        We do not knowingly collect personal information from children. If you believe a
                        minor has provided us with personal information, please contact us so we can remove
                        it.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="changes">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">11</span>
                    <span class="legal-section-title">Changes to This Policy</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        We may update this policy from time to time. Material changes will be reflected by
                        updating the “Last updated” date above. Continued use of the Service after a change
                        means you accept the updated policy.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="contact">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">12</span>
                    <span class="legal-section-title">Contact</span>
                </h2>
                <div class="legal-section-body">
                    <p>Questions about this policy or your data can be sent to the site administrator.</p>
                </div>
            </section>
        </div>

        <footer class="legal-footer">
            <a href="{{ route('pages.terms') }}" class="legal-footer-link">Terms of Use</a>
            <a href="{{ route('home') }}" class="legal-footer-link legal-back-link">&larr; Back to gallery</a>
        </footer>
    </article>
@endsection
