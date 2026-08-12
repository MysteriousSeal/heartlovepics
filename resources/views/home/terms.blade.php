@extends('layouts.app')

@section('title', 'Terms of Use — HeartLovePics')
@section('meta_description', 'The terms that govern your use of HeartLovePics, including content rules, account responsibilities, and adult-content requirements.')
@section('canonical', route('pages.terms'))

@section('content')
    <article class="static-page legal-page">
        <header class="legal-hero">
            <p class="legal-kicker">Legal</p>
            <h1>Terms of Use</h1>
            <p class="legal-lede">
                These Terms govern your access to and use of HeartLovePics. By using the
                Service, you agree to them.
            </p>
            <div class="legal-meta">
                <time class="legal-updated" datetime="2026-08-12">Last updated August 12, 2026</time>
                <a href="{{ route('pages.privacy') }}" class="legal-related-link">Privacy Policy</a>
            </div>
        </header>

        <div class="legal-intro">
            <p>
                These Terms of Use (“Terms”) govern your access to and use of HeartLovePics (“we,”
                “us,” or “the Service”). By accessing or using the Service, you agree to be bound by
                these Terms. If you do not agree, please do not use the Service. See also our
                <a href="{{ route('pages.privacy') }}">Privacy Policy</a>, which explains how we
                handle your information.
            </p>
        </div>

        <nav class="legal-toc" aria-label="On this page">
            <p class="legal-toc-label">On this page</p>
            <ol class="legal-toc-list">
                <li><a href="#eligibility">Eligibility</a></li>
                <li><a href="#account">Your Account</a></li>
                <li><a href="#content">Content You Submit</a></li>
                <li><a href="#prohibited">Prohibited Content &amp; Conduct</a></li>
                <li><a href="#nsfw">Adult (NSFW) Content</a></li>
                <li><a href="#moderation">Content Moderation</a></li>
                <li><a href="#copyright">Copyright Complaints</a></li>
                <li><a href="#termination">Termination</a></li>
                <li><a href="#disclaimers">Disclaimers</a></li>
                <li><a href="#liability">Limitation of Liability</a></li>
                <li><a href="#changes">Changes</a></li>
                <li><a href="#contact">Contact</a></li>
            </ol>
        </nav>

        <div class="legal-sections">
            <section class="legal-section" id="eligibility">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">1</span>
                    <span class="legal-section-title">Eligibility</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        You must be at least 18 years old, or the age of majority in your jurisdiction if
                        higher, to use HeartLovePics. The Service hosts user-submitted adult (NSFW) content,
                        and by using it you confirm that you meet this age requirement. Age confirmation on
                        this site is self-reported; we do not independently verify your age.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="account">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">2</span>
                    <span class="legal-section-title">Your Account</span>
                </h2>
                <div class="legal-section-body">
                    <ul>
                        <li>An account is not required to browse, like, or comment as a guest, but is
                        required to save bookmarks and collections, follow other users, or keep a journal.</li>
                        <li>Publishing new posts to the gallery is limited to HeartLovePics administrators.
                        Regular accounts can still comment, like, bookmark, follow, and keep a journal.</li>
                        <li>You're responsible for keeping your password secure and for all activity that
                        happens under your account.</li>
                        <li>You must provide accurate information and keep it up to date.</li>
                        <li>One account per person. You may not share, sell, or transfer your account.</li>
                    </ul>
                </div>
            </section>

            <section class="legal-section" id="content">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">3</span>
                    <span class="legal-section-title">Content You Submit</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        Posts in the gallery, comics, illustrated sets, and single images alike, are
                        published by HeartLovePics administrators and credited to the artist who made them,
                        with links to that artist's DeviantArt, FurAffinity, or Patreon where available.
                        Publishing a post doesn't imply the artist submitted it themselves or endorses
                        HeartLovePics; if you're the artist behind a piece and want it credited differently,
                        updated, or removed, contact the site administrator.
                    </p>
                    <p>
                        You retain ownership of the comments, journal entries, collections, and other
                        content you personally submit (“Your Content”). By submitting Your Content, you
                        grant HeartLovePics a non-exclusive, worldwide, royalty-free license to host, store,
                        reproduce, and display it as necessary to operate the Service. This license ends
                        when you delete Your Content, subject to the short retention window described in
                        our Privacy Policy and any copies reasonably required for backups or legal
                        compliance.
                    </p>
                    <p>
                        You're solely responsible for Your Content. By submitting it, you confirm that you
                        own it or otherwise have the right to post it, and that it doesn't infringe anyone
                        else's rights.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="prohibited">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">4</span>
                    <span class="legal-section-title">Prohibited Content &amp; Conduct</span>
                </h2>
                <div class="legal-section-body">
                    <p>Regardless of NSFW status, you may not post or use the Service to:</p>
                    <ul>
                        <li>Post content depicting or sexualizing minors, in any form, real or fictional.
                        This is never permitted and will result in an immediate, permanent ban and any
                        legally required reporting.</li>
                        <li>Post sexually explicit content of a real, identifiable person without their
                        clear consent, including deepfakes or “face-swapped” images.</li>
                        <li>Post content that is illegal, that depicts non-consensual acts, or that
                        infringes another party's copyright, trademark, or other rights.</li>
                        <li>Harass, threaten, impersonate, or share the private information of others.</li>
                        <li>Upload malware, or attempt to interfere with, overload, or gain unauthorized
                        access to the Service.</li>
                        <li>Use automated means (bots, scrapers) to access the Service outside of normal
                        browsing, or to inflate views, likes, or comments.</li>
                    </ul>
                </div>
            </section>

            <section class="legal-section" id="nsfw">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">5</span>
                    <span class="legal-section-title">Adult (NSFW) Content</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        Some content on HeartLovePics is sexually explicit. Creators are expected to mark
                        such content as NSFW and add a content warning where appropriate; we display it
                        blurred behind an age-confirmation prompt by default. Mislabeling adult content as
                        safe-for-work is a violation of these Terms.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="moderation">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">6</span>
                    <span class="legal-section-title">Content Moderation &amp; Enforcement</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        We may review, remove, or restrict access to any content that violates these Terms,
                        with or without notice. We may restrict an account's ability to post, or suspend or
                        terminate an account entirely, for violations of these Terms or for any conduct we
                        reasonably believe is harmful to the Service or other users. Moderation actions are
                        recorded in an internal administrative log.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="copyright">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">7</span>
                    <span class="legal-section-title">Copyright Complaints</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        If you believe content on HeartLovePics infringes your copyright, contact the site
                        administrator with a description of the work, the URL of the infringing content, and
                        your contact information, and we will investigate and remove infringing content we
                        confirm.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="termination">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">8</span>
                    <span class="legal-section-title">Termination</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        You may stop using the Service and request deletion of your account at any time by
                        contacting the site administrator. We may suspend or terminate your access to the
                        Service at our discretion, including for violation of these Terms. Sections of these
                        Terms that by their nature should survive termination (such as the content license
                        for previously public content, disclaimers, and limitation of liability) will
                        survive.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="disclaimers">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">9</span>
                    <span class="legal-section-title">Disclaimers</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        The Service is provided “as is” and “as available,” without warranties of any kind,
                        express or implied. We don't guarantee that the Service will be uninterrupted,
                        error-free, or secure, and we aren't responsible for content posted by users, which
                        reflects the views of its creator, not HeartLovePics.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="liability">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">10</span>
                    <span class="legal-section-title">Limitation of Liability</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        To the fullest extent permitted by law, HeartLovePics will not be liable for any
                        indirect, incidental, special, or consequential damages arising from your use of, or
                        inability to use, the Service.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="changes">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">11</span>
                    <span class="legal-section-title">Changes to These Terms</span>
                </h2>
                <div class="legal-section-body">
                    <p>
                        We may update these Terms from time to time. Material changes will be reflected by
                        updating the “Last updated” date above. Continued use of the Service after a change
                        means you accept the updated Terms.
                    </p>
                </div>
            </section>

            <section class="legal-section" id="contact">
                <h2 class="legal-section-heading">
                    <span class="legal-section-num">12</span>
                    <span class="legal-section-title">Contact</span>
                </h2>
                <div class="legal-section-body">
                    <p>Questions about these Terms can be sent to the site administrator.</p>
                </div>
            </section>
        </div>

        <footer class="legal-footer">
            <a href="{{ route('pages.privacy') }}" class="legal-footer-link">Privacy Policy</a>
            <a href="{{ route('home') }}" class="legal-footer-link legal-back-link">&larr; Back to gallery</a>
        </footer>
    </article>
@endsection
