@php
    $likers = $recentLikers ?? ['users' => collect(), 'others_count' => 0, 'total' => 0];
    $users = $likers['users'];
    $othersCount = $likers['others_count'];
    $total = $likers['total'];
@endphp

@if ($total > 0)
    <div class="liked-by">
        <span class="liked-by-label">Liked by</span>
        <div class="liked-by-avatars" aria-label="{{ number_format($total) }} {{ \Illuminate\Support\Str::plural('like', $total) }}">
            @foreach ($users as $user)
                <a
                    href="{{ route('users.show', $user) }}"
                    class="liked-by-avatar-link"
                    title="{{ '@'.$user->username }}"
                >
                    @include('partials.user-avatar', [
                        'user' => $user,
                        'class' => 'liked-by-avatar',
                        'width' => 28,
                        'height' => 28,
                        'loading' => 'lazy',
                    ])
                </a>
            @endforeach
            @if ($othersCount > 0)
                <span class="liked-by-more" title="{{ number_format($othersCount) }} more">+{{ $othersCount > 99 ? '99+' : $othersCount }}</span>
            @endif
        </div>
    </div>
@endif