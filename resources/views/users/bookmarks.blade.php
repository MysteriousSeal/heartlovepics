@extends('layouts.app')

@section('title', 'Bookmarked images — ' . $user->username . ' — HeartLovePics')
@section('meta_description', 'Images bookmarked by ' . $user->username . ' on HeartLovePics.')
@section('canonical', route('users.bookmarks', $user))

@push('meta')
    @include('partials.og-meta', [
        'title' => '@' . $user->username . ' — Bookmarked images — HeartLovePics',
        'description' => 'Images bookmarked by ' . $user->username . ' on HeartLovePics.',
        'url' => route('users.bookmarks', $user),
        'image' => $ogImage ?? null,
    ])
@endpush

@section('content')
    <section class="user-profile">
        @include('partials.flash')

        @include('partials.user-profile-header', compact('user', 'isOwner', 'uploadCount', 'isFollowing', 'followersCount', 'followingCount'))

        @include('partials.user-profile-tabs', compact('user', 'activeTab', 'uploadCount', 'likedCount', 'bookmarkCount', 'draftCount', 'journalCount', 'isOwner'))

        @include('partials.collections-bar', compact('user', 'collections', 'activeCollection'))

        <section class="user-profile-gallery">
            @if ($images->isEmpty())
                @include('partials.empty-state-suggestions', [
                    'message' => $activeCollection
                        ? 'No images in this collection yet.'
                        : 'You have not bookmarked any images yet.',
                    'suggestions' => $activeCollection
                        ? [
                            ['label' => 'View all bookmarks', 'url' => route('users.bookmarks', $user)],
                        ]
                        : [
                            ['label' => 'Browse the latest gallery', 'url' => route('home')],
                            ['label' => 'See most liked images', 'url' => route('gallery.likes')],
                            ['label' => 'Explore random picks', 'url' => route('gallery.random')],
                            ['label' => 'Browse by tag', 'url' => route('gallery.tags')],
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