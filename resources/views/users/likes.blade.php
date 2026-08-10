@extends('layouts.app')

@section('title', 'Liked images — ' . $user->username . ' — HeartLovePics')
@section('meta_description', 'Images liked by ' . $user->username . ' on HeartLovePics.')
@section('canonical', route('users.likes', $user))

@push('meta')
    @include('partials.og-meta', [
        'title' => '@' . $user->username . ' — Liked images — HeartLovePics',
        'description' => 'Images liked by ' . $user->username . ' on HeartLovePics.',
        'url' => route('users.likes', $user),
        'image' => $ogImage ?? null,
    ])
@endpush

@section('content')
    <section class="user-profile">
        @include('partials.flash')

        @include('partials.user-profile-header', compact('user', 'isOwner', 'uploadCount', 'isFollowing', 'followersCount', 'followingCount'))

        @include('partials.user-profile-tabs', compact('user', 'activeTab', 'uploadCount', 'likedCount', 'bookmarkCount', 'draftCount', 'journalCount', 'isOwner'))

        <section class="user-profile-gallery">
            @if ($images->isEmpty())
                @include('partials.empty-state-suggestions', [
                    'message' => $isOwner
                        ? 'You have not liked any images yet.'
                        : $user->username . ' has not liked any images yet.',
                    'suggestions' => $isOwner
                        ? [
                            ['label' => 'Browse the latest gallery', 'url' => route('home')],
                            ['label' => 'See most liked images', 'url' => route('gallery.likes')],
                            ['label' => 'Explore random picks', 'url' => route('gallery.random')],
                            ['label' => 'Browse by tag', 'url' => route('gallery.tags')],
                        ]
                        : [
                            ['label' => 'View ' . $user->username . '\'s images', 'url' => route('users.show', $user)],
                            ['label' => 'Browse the latest gallery', 'url' => route('home')],
                            ['label' => 'See most liked images', 'url' => route('gallery.likes')],
                        ],
                ])
            @else
                <div class="masonry">
                    @include('partials.gallery-columns', [
                        'columns' => $columns,
                        'likedMap' => $likedMap,
                        'bookmarkMap' => $bookmarkMap,
                        'lcpImage' => null,
                        'isFirstPage' => true,
                        'showNsfw' => $showNsfw,
                    ])
                </div>
            @endif
        </section>
    </section>
@endsection