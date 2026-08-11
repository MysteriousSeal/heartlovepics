@extends('layouts.app')

@section('title', 'Contact — HeartLovePics')
@section('meta_description', 'Get in touch with HeartLovePics.')
@section('canonical', route('pages.contact'))

@section('content')
    <article class="static-page contact-page">
        <header class="legal-hero">
            <p class="legal-kicker">Site</p>
            <h1>Contact</h1>
            <p class="legal-lede">
                Questions, feedback, or anything else about HeartLovePics.
            </p>
        </header>

        @include('partials.flash')

        <form method="POST" action="{{ route('pages.contact.submit') }}" class="contact-form">
            @csrf

            <div class="form-group">
                <label for="username">Username</label>
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
                    <p class="form-hint">Signed in as this account — username can’t be changed.</p>
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
                    >
                    @error('username')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                @endauth
            </div>

            <div class="form-group">
                <label for="email">Email</label>
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
                >
                <p class="form-hint">So we can reply to your message.</p>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
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
                    @auth autofocus @endauth
                >
                <p
                    class="form-char-count"
                    id="subject-char-count"
                    data-max-length="{{ \App\Models\ContactMessage::SUBJECT_MAX }}"
                    aria-live="polite"
                >0 / {{ \App\Models\ContactMessage::SUBJECT_MAX }}</p>
                @error('subject')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea
                    id="message"
                    name="message"
                    class="form-control"
                    rows="8"
                    required
                    maxlength="{{ \App\Models\ContactMessage::MESSAGE_MAX }}"
                    data-char-counter="message-char-count"
                >{{ old('message') }}</textarea>
                <p
                    class="form-char-count"
                    id="message-char-count"
                    data-max-length="{{ \App\Models\ContactMessage::MESSAGE_MAX }}"
                    aria-live="polite"
                >0 / {{ \App\Models\ContactMessage::MESSAGE_MAX }}</p>
                @error('message')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Send message</button>
        </form>

        <footer class="legal-footer">
            <a href="{{ route('pages.privacy') }}" class="legal-footer-link">Privacy Policy</a>
            <a href="{{ route('pages.terms') }}" class="legal-footer-link">Terms of Use</a>
            <a href="{{ route('home') }}" class="legal-footer-link legal-back-link">&larr; Back to gallery</a>
        </footer>
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
