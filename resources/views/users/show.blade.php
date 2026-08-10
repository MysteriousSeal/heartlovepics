@extends('layouts.app')

@section('title', $user->username . ' — HeartLovePics')
@section('meta_description', 'View images shared by ' . $user->username . ' on HeartLovePics.')
@section('canonical', route('users.show', $user))

@push('meta')
    @include('partials.og-meta', [
        'title' => '@' . $user->username . ' — HeartLovePics',
        'description' => 'View images shared by ' . $user->username . ' on HeartLovePics.',
        'url' => route('users.show', $user),
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
                        ? 'You have not shared any images yet.'
                        : $user->username . ' has not shared any images yet.',
                    'suggestions' => $isOwner
                        ? array_values(array_filter([
                            $user->canPost()
                                ? ['label' => 'Upload your first image', 'url' => route('profile.images.create')]
                                : null,
                            ['label' => 'Browse the gallery for inspiration', 'url' => route('home')],
                            ['label' => 'Explore tags', 'url' => route('gallery.tags')],
                        ]))
                        : [
                            ['label' => 'Browse the latest gallery', 'url' => route('home')],
                            ['label' => 'See random picks', 'url' => route('gallery.random')],
                            ['label' => 'Explore tags', 'url' => route('gallery.tags')],
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

        @include('partials.shoutbox', compact('user', 'shouts', 'canShout'))
    </section>
@endsection