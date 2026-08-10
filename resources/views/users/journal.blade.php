@extends('layouts.app')

@section('title', 'Journal — ' . $user->username . ' — HeartLovePics')
@section('meta_description', 'Journal entries posted by ' . $user->username . ' on HeartLovePics.')
@section('canonical', route('users.journal', $user))

@push('meta')
    @include('partials.og-meta', [
        'title' => '@' . $user->username . ' — Journal — HeartLovePics',
        'description' => 'Journal entries posted by ' . $user->username . ' on HeartLovePics.',
        'url' => route('users.journal', $user),
        'image' => $ogImage ?? null,
    ])
@endpush

@section('content')
    <section class="user-profile">
        @include('partials.flash')

        @include('partials.user-profile-header', compact('user', 'isOwner', 'uploadCount', 'isFollowing', 'followersCount', 'followingCount'))

        @include('partials.user-profile-tabs', compact('user', 'activeTab', 'uploadCount', 'likedCount', 'bookmarkCount', 'draftCount', 'journalCount', 'isOwner'))

        <section class="user-profile-gallery">
            @if ($isOwner)
                <div class="journal-list-actions">
                    <a href="{{ route('profile.journal.create') }}" class="btn btn-primary btn-sm">New journal entry</a>
                </div>
            @endif

            @if ($journals->isEmpty())
                @include('partials.empty-state-suggestions', [
                    'message' => $isOwner
                        ? 'You have not posted any journal entries yet.'
                        : $user->username . ' has not posted any journal entries yet.',
                    'suggestions' => $isOwner
                        ? [['label' => 'Write your first entry', 'url' => route('profile.journal.create')]]
                        : [['label' => 'View ' . $user->username . '\'s images', 'url' => route('users.show', $user)]],
                ])
            @else
                <ul class="journal-list">
                    @foreach ($journals as $journal)
                        <li class="journal-item">
                            <a href="{{ route('journals.show', $journal) }}" class="journal-item-title">{{ $journal->title }}</a>
                            <time class="journal-item-date" datetime="{{ $journal->created_at->toIso8601String() }}">
                                {{ $journal->created_at->format('F j, Y') }}
                            </time>
                            <p class="journal-item-excerpt">{{ \Illuminate\Support\Str::limit($journal->body_plain, 200) }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </section>
@endsection
