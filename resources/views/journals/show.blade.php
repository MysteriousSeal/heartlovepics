@extends('layouts.app')

@section('title', $journal->title . ' — HeartLovePics')
@section('meta_description', \Illuminate\Support\Str::limit($journal->body_plain, 160))
@section('canonical', route('journals.show', $journal))

@push('meta')
    @include('partials.og-meta', [
        'title' => $journal->title . ' — HeartLovePics',
        'description' => \Illuminate\Support\Str::limit($journal->body_plain, 160),
        'url' => url(route('journals.show', $journal, absolute: false)),
    ])
@endpush

@section('content')
    <article class="image-detail">
        <a href="{{ route('users.journal', $journal->user) }}" class="back-link">&larr; Back to journal</a>

        @include('partials.flash')

        <div class="image-detail-info">
            <div class="image-detail-header">
                <h2>{{ $journal->title }}</h2>
            </div>

            <div class="image-detail-meta">
                <div class="image-detail-meta-primary">
                    <span>Posted {{ $journal->created_at->format('F j, Y') }}</span>
                </div>
                <a href="{{ route('users.show', $journal->user) }}" class="image-author">
                    @include('partials.user-avatar', [
                        'user' => $journal->user,
                        'class' => 'image-author-avatar',
                        'width' => 24,
                        'height' => 24,
                        'loading' => 'lazy',
                    ])
                    <span class="image-author-name">{{ '@'.$journal->user->username }}</span>
                </a>
            </div>

            <div class="image-detail-description">
                {!! $journal->body_html !!}
            </div>

            @if ($canManage)
                <div class="image-owner-actions">
                    <a href="{{ route('profile.journal.edit', $journal) }}" class="btn btn-secondary btn-sm">Edit entry</a>
                    <form
                        method="POST"
                        action="{{ route('profile.journal.destroy', $journal) }}"
                        class="image-delete-form"
                        data-confirm="Delete this journal entry permanently? This cannot be undone."
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete entry</button>
                    </form>
                </div>
            @endif
        </div>
    </article>
@endsection
