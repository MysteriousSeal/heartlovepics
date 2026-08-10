@extends('layouts.app')

@section('title', 'Terms of Use — HeartLovePics')
@section('meta_description', 'The terms that govern your use of HeartLovePics, including content rules, account responsibilities, and adult-content requirements.')
@section('canonical', route('pages.terms'))

@section('content')
    <article class="static-page">
        <h1>Terms of Use</h1>
        <p class="static-page-updated">Last updated: August 6, 2026</p>

        <p>
            These Terms of Use (“Terms”) govern your access to and use of HeartLovePics (“we,”
            “us,” or “the Service”). By accessing or using the Service, you agree to be bound by
            these Terms. If you do not agree, please do not use the Service. See also our
            <a href="{{ route('pages.privacy') }}">Privacy Policy</a>, which explains how we
            handle your information.
        </p>

        <h3>1. Eligibility</h3>
        <p>
            You must be at least 18 years old, or the age of majority in your jurisdiction if
            higher, to use HeartLovePics. The Service hosts user-submitted adult (NSFW) content,
            and by using it you confirm that you meet this age requirement. Age confirmation on
            this site is self-reported; we do not independently verify your age.
        </p>

        <h3>2. Your Account</h3>
        <ul>
            <li>An account is not required to browse, like, or comment as a guest, but is
            required to upload images, save bookmarks and collections, follow other users, or
            keep a journal.</li>
            <li>You're responsible for keeping your password secure and for all activity that
            happens under your account.</li>
            <li>You must provide accurate information and keep it up to date.</li>
            <li>One account per person. You may not share, sell, or transfer your account.</li>
        </ul>

        <h3>3. Content You Submit</h3>
        <p>
            You retain ownership of the images, titles, descriptions, comments, journal entries,
            and other content you submit (“Your Content”). By submitting Your Content, you grant
            HeartLovePics a non-exclusive, worldwide, royalty-free license to host, store,
            reproduce, display, and distribute it as necessary to operate and promote the
            Service — for example, showing it in the gallery, thumbnails, tag pages, and related-
            image sections. This license ends when you delete Your Content, subject to the short
            retention window described in our Privacy Policy and any copies reasonably required
            for backups or legal compliance.
        </p>
        <p>
            You're solely responsible for Your Content. By submitting it, you confirm that you
            own it or otherwise have the right to post it, and that it doesn't infringe anyone
            else's rights.
        </p>

        <h3>4. Prohibited Content &amp; Conduct</h3>
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

        <h3>5. Adult (NSFW) Content</h3>
        <p>
            Some content on HeartLovePics is sexually explicit. Creators are expected to mark
            such content as NSFW and add a content warning where appropriate; we display it
            blurred behind an age-confirmation prompt by default. Mislabeling adult content as
            safe-for-work is a violation of these Terms.
        </p>

        <h3>6. Content Moderation &amp; Enforcement</h3>
        <p>
            We may review, remove, or restrict access to any content that violates these Terms,
            with or without notice. We may restrict an account's ability to post, or suspend or
            terminate an account entirely, for violations of these Terms or for any conduct we
            reasonably believe is harmful to the Service or other users. Moderation actions are
            recorded in an internal administrative log.
        </p>

        <h3>7. Copyright Complaints</h3>
        <p>
            If you believe content on HeartLovePics infringes your copyright, contact the site
            administrator with a description of the work, the URL of the infringing content, and
            your contact information, and we will investigate and remove infringing content we
            confirm.
        </p>

        <h3>8. Termination</h3>
        <p>
            You may stop using the Service and request deletion of your account at any time by
            contacting the site administrator. We may suspend or terminate your access to the
            Service at our discretion, including for violation of these Terms. Sections of these
            Terms that by their nature should survive termination (such as the content license
            for previously public content, disclaimers, and limitation of liability) will
            survive.
        </p>

        <h3>9. Disclaimers</h3>
        <p>
            The Service is provided “as is” and “as available,” without warranties of any kind,
            express or implied. We don't guarantee that the Service will be uninterrupted,
            error-free, or secure, and we aren't responsible for content posted by users, which
            reflects the views of its creator, not HeartLovePics.
        </p>

        <h3>10. Limitation of Liability</h3>
        <p>
            To the fullest extent permitted by law, HeartLovePics will not be liable for any
            indirect, incidental, special, or consequential damages arising from your use of, or
            inability to use, the Service.
        </p>

        <h3>11. Changes to These Terms</h3>
        <p>
            We may update these Terms from time to time. Material changes will be reflected by
            updating the “Last updated” date above. Continued use of the Service after a change
            means you accept the updated Terms.
        </p>

        <h3>12. Contact</h3>
        <p>Questions about these Terms can be sent to the site administrator.</p>

        <p class="static-page-back"><a href="{{ route('home') }}">&larr; Back to gallery</a></p>
    </article>
@endsection
