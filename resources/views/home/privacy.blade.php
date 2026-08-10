@extends('layouts.app')

@section('title', 'Privacy Policy — HeartLovePics')
@section('meta_description', 'How HeartLovePics collects, uses, and protects your information, including account data, uploaded content, cookies, and your privacy choices.')
@section('canonical', route('pages.privacy'))

@section('content')
    <article class="static-page">
        <h1>Privacy Policy</h1>
        <p class="static-page-updated">Last updated: August 6, 2026</p>

        <p>
            HeartLovePics (“we,” “us,” or “the Service”) is a public image-sharing gallery.
            This policy explains what information we collect, how we use it, and the choices
            you have. By using the Service, you agree to the practices described here.
        </p>

        <h3>1. Information We Collect</h3>

        <p><strong>Account information.</strong> If you register, we collect a username and
        password (stored as a one-way hash — we never see or store your password in plain text).
        We also record the date you accepted our Terms of Use. You may optionally add a profile
        avatar, banner image, and bio.</p>

        <p><strong>Content you submit.</strong> Anything you post is collected and stored,
        including uploaded images and their titles, descriptions, alt text, and tags; comments
        and shouts; journal entries; and the collections you create. See “Public Visibility” below —
        most of this content is public by design.</p>

        <p><strong>Usage and engagement data.</strong> We store the actions you take so the
        Service works as expected: likes, bookmarks, follows, and which images you've viewed.
        For visitors who aren't logged in, likes are tied to an anonymized identifier (see
        Cookies below) rather than an account.</p>

        <p><strong>Technical data.</strong> We log aggregate, non-identifying view counts for
        images and the site as a whole. Where we do handle an IP address — to let a visitor like
        an image without creating an account — we never store the address itself. It's combined
        with your session ID and passed through a one-way SHA-256 hash before being saved, so the
        original IP address cannot be recovered from what we store.</p>

        <h3>2. Cookies &amp; Local Storage</h3>
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
        <p>We do not use third-party advertising or analytics trackers, and we do not sell or
        share your data with data brokers.</p>

        <h3>3. How We Use Your Information</h3>
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

        <h3>4. Public Visibility of Your Content</h3>
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

        <h3>5. Age Requirement &amp; Adult Content</h3>
        <p>
            You must be at least 18 years old, or the age of majority in your jurisdiction if
            higher, to use HeartLovePics. The Service hosts user-submitted adult (NSFW) content.
            NSFW images are blurred by default and require you to confirm your age before they're
            shown; this confirmation is self-reported, and we have no independent way to verify
            your age. HeartLovePics is not intended for anyone under 18, and we do not knowingly
            collect information from minors — see “Children's Privacy” below.
        </p>

        <h3>6. Data Retention &amp; Deletion</h3>
        <p>
            When you delete an image, it isn't erased immediately — it's held for a short grace
            period (currently 24 hours) so you can undo the deletion, after which it and its
            files are permanently removed on an automated schedule. Comments, journal entries,
            and other content are removed when you delete them or when the account that posted
            them is deleted.
        </p>

        <h3>7. Your Choices &amp; Rights</h3>
        <ul>
            <li>You can edit or delete your images, comments, journal entries, and collections
            at any time from your account.</li>
            <li>You can control which in-app notifications you receive from your notification
            settings.</li>
            <li>You can turn NSFW content on or off at any time from the gallery toolbar.</li>
            <li>You can request access to, correction of, or deletion of your account and
            associated data by contacting the site administrator (see “Contact” below).</li>
        </ul>

        <h3>8. Data Sharing</h3>
        <p>
            We do not sell your personal data. We do not use third-party advertising networks or
            share your data with them. Your data may be accessed by service providers that host
            our infrastructure (such as our hosting provider), solely to operate the Service, and
            may be disclosed if required by law or to protect the rights, safety, or property of
            HeartLovePics or its users.
        </p>

        <h3>9. Data Security</h3>
        <p>
            We use industry-standard measures to protect your data, including password hashing
            and hashed rather than raw storage of IP addresses used for anonymous likes. No
            method of storage or transmission is completely secure, and we can't guarantee
            absolute security.
        </p>

        <h3>10. Children's Privacy</h3>
        <p>
            HeartLovePics is not directed at, and is not intended for use by, anyone under 18.
            We do not knowingly collect personal information from children. If you believe a
            minor has provided us with personal information, please contact us so we can remove
            it.
        </p>

        <h3>11. Changes to This Policy</h3>
        <p>
            We may update this policy from time to time. Material changes will be reflected by
            updating the “Last updated” date above. Continued use of the Service after a change
            means you accept the updated policy.
        </p>

        <h3>12. Contact</h3>
        <p>Questions about this policy or your data can be sent to the site administrator.</p>

        <p class="static-page-back"><a href="{{ route('home') }}">&larr; Back to gallery</a></p>
    </article>
@endsection
