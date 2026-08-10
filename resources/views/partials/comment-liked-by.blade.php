@php
    $likers = $recentLikers ?? ['users' => collect(), 'others_count' => 0, 'total' => 0];
    $users = $likers['users'] ?? collect();
    $othersCount = $likers['others_count'] ?? 0;
    $total = $likers['total'] ?? 0;
@endphp

<div
    class="comment-liked-by {{ $total > 0 ? '' : 'is-empty' }}"
    data-comment-liked-by="{{ $comment->id }}"
    @if ($total === 0) hidden @endif
>
    <span class="liked-by-label">Liked by</span>
    <div
        class="liked-by-avatars"
        data-liked-by-avatars
        aria-label="{{ number_format($total) }} {{ \Illuminate\Support\Str::plural('like', $total) }}"
    >
        @foreach ($users as $user)
            <a
                href="{{ route('users.show', $user) }}"
                class="liked-by-avatar-link"
                title="{{ '@'.$user->username }}"
            >
                @include('partials.user-avatar', [
                    'user' => $user,
                    'class' => 'liked-by-avatar liked-by-avatar--sm',
                    'width' => 22,
                    'height' => 22,
                    'loading' => 'lazy',
                ])
            </a>
        @endforeach
        @if ($othersCount > 0)
            <span class="liked-by-more liked-by-more--sm" title="{{ number_format($othersCount) }} more" data-liked-by-more>
                +{{ $othersCount > 99 ? '99+' : $othersCount }}
            </span>
        @endif
    </div>
</div>
