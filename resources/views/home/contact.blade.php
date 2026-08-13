@extends('layouts.app')

@section('title', 'Contact — HeartLovePics')
@section('meta_description', 'Get in touch with HeartLovePics. Questions, feedback, or anything else.')
@section('canonical', route('pages.contact'))

@section('content')
    <article class="contact-page">
        <header class="contact-hero">
            <p class="contact-kicker">Site</p>
            <h1 class="contact-title">Contact</h1>
            <p class="contact-lede">
                Questions, feedback, bug reports, or anything else about HeartLovePics.
                We read every message.
            </p>
        </header>

        @include('partials.flash')

        <div class="contact-layout">
            <section class="contact-panel" aria-labelledby="contact-form-heading">
                <div class="contact-panel-header">
                    <h2 id="contact-form-heading" class="contact-panel-title">Send a message</h2>
                    <p class="contact-panel-sub">Fields marked required must be filled in.</p>
                </div>

                <form method="POST" action="{{ route('pages.contact.submit') }}" class="contact-form">
                    @csrf

                    <div class="form-group">
                        <label for="username">Username <span class="contact-required" aria-hidden="true">*</span></label>
                        @auth
                            <input
                                type="text"
                                id="username"
                                class="form-control"
                                value="{{ auth()->user()->username }}"
                                readonly
                                disabled
                                aria-readonly="true"
                            >
                            <p class="form-hint">Signed in as this account. Username can’t be changed.</p>
                        @else
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control"
                                value="{{ old('username') }}"
                                required
                                maxlength="{{ \App\Models\ContactMessage::USERNAME_MAX }}"
                                autocomplete="username"
                                autofocus
                                placeholder="How should we address you?"
                            >
                            @error('username')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        @endauth
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="contact-required" aria-hidden="true">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', auth()->user()->email ?? '') }}"
                            required
                            maxlength="{{ \App\Models\ContactMessage::EMAIL_MAX }}"
                            autocomplete="email"
                            inputmode="email"
                            placeholder="you@example.com"
                        >
                        <p class="form-hint">We only use this to reply to your message.</p>
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="contact-label-row">
                            <label for="subject">Subject <span class="contact-required" aria-hidden="true">*</span></label>
                            <p
                                class="form-char-count"
                                id="subject-char-count"
                                data-max-length="{{ \App\Models\ContactMessage::SUBJECT_MAX }}"
                                aria-live="polite"
                            >0 / {{ \App\Models\ContactMessage::SUBJECT_MAX }}</p>
                        </div>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            class="form-control"
                            value="{{ old('subject') }}"
                            required
                            maxlength="{{ \App\Models\ContactMessage::SUBJECT_MAX }}"
                            autocomplete="off"
                            data-char-counter="subject-char-count"
                            placeholder="What’s this about?"
                            @auth autofocus @endauth
                        >
                        @error('subject')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="contact-label-row">
                            <label for="message">Message <span class="contact-required" aria-hidden="true">*</span></label>
                            <p
                                class="form-char-count"
                                id="message-char-count"
                                data-max-length="{{ \App\Models\ContactMessage::MESSAGE_MAX }}"
                                aria-live="polite"
                            >0 / {{ \App\Models\ContactMessage::MESSAGE_MAX }}</p>
                        </div>
                        <textarea
                            id="message"
                            name="message"
                            class="form-control contact-message"
                            rows="8"
                            required
                            maxlength="{{ \App\Models\ContactMessage::MESSAGE_MAX }}"
                            data-char-counter="message-char-count"
                            placeholder="Write as much as you need. Include links or post titles if it helps."
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="contact-actions">
                        <button type="submit" class="btn btn-primary contact-submit">Send message</button>
                    </div>
                </form>
            </section>

            <aside class="contact-aside" aria-label="Contact tips">
                <div class="contact-aside-card">
                    <h2 class="contact-aside-title">Good to know</h2>
                    <ul class="contact-aside-list">
                        <li>
                            <strong>Response time</strong>
                            <span>We aim to reply within a few days when we can.</span>
                        </li>
                        <li>
                            <strong>Account help</strong>
                            <span>Include your username so we can look up the right profile.</span>
                        </li>
                        <li>
                            <strong>Content issues</strong>
                            <span>Link the post or page if you’re reporting something specific.</span>
                        </li>
                        <li>
                            <strong>Privacy</strong>
                            <span>Your message stays private. We don’t share contact details.</span>
                        </li>
                    </ul>
                    <div class="contact-aside-links">
                        <a href="{{ route('pages.privacy') }}">Privacy policy</a>
                        <a href="{{ route('pages.terms') }}">Terms of use</a>
                        <a href="{{ route('home') }}">Back to gallery</a>
                    </div>
                </div>
            </aside>
        </div>
    </article>
@endsection

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('[data-char-counter]').forEach(function (input) {
                var counter = document.getElementById(input.getAttribute('data-char-counter'));
                if (!counter) return;
                var max = Number(counter.getAttribute('data-max-length')) || input.maxLength || 0;
                function update() {
                    var len = input.value.length;
                    counter.textContent = len + ' / ' + max;
                    counter.classList.toggle('form-char-count--limit', max > 0 && len >= max);
                }
                input.addEventListener('input', update);
                update();
            });
        })();
    </script>
@endpush
