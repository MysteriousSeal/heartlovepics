@extends('layouts.app')

@section('title', 'Drafts — ' . $user->username . ' — HeartLovePics')
@section('meta_description', 'Draft images by ' . $user->username . ' on HeartLovePics.')
@section('canonical', route('users.drafts', $user))

@section('content')
    <section class="user-profile">
        @include('partials.flash')

        @include('partials.user-profile-header', compact('user', 'isOwner', 'uploadCount', 'isFollowing', 'followersCount', 'followingCount'))

        @include('partials.user-profile-tabs', compact('user', 'activeTab', 'uploadCount', 'likedCount', 'bookmarkCount', 'draftCount', 'journalCount', 'isOwner'))

        <section class="user-profile-gallery">
            @if ($images->isEmpty())
                @include('partials.empty-state-suggestions', [
                    'message' => 'You have no draft images.',
                    'suggestions' => array_values(array_filter([
                        auth()->user()->canPost()
                            ? ['label' => 'Upload a new image', 'url' => route('profile.images.create')]
                            : null,
                        ['label' => 'View your uploads', 'url' => route('users.show', $user)],
                    ])),
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