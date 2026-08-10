@php
    $isFollowing = $isFollowing ?? false;
@endphp

@auth
    @unless ($isOwner)
        <button
            type="button"
            class="follow-btn btn {{ $isFollowing ? 'follow-btn--following' : 'btn-secondary' }}"
            data-url="{{ route('users.follow', $user) }}"
            data-label-following="Unfollow"
            data-label-unfollowed="Follow"
            aria-label="{{ $isFollowing ? 'Unfollow' : 'Follow' }}"
        >
            {{ $isFollowing ? 'Following' : 'Follow' }}
        </button>
    @endunless
@else
    @unless ($isOwner)
        <a
            href="{{ route('login') }}"
            class="btn btn-secondary follow-btn follow-btn--guest"
        >
            Follow
        </a>
    @endunless
@endauth