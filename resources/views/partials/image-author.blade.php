@if ($image->user)
    <a href="{{ route('users.show', $image->user) }}" class="image-author">
        @include('partials.user-avatar', [
            'user' => $image->user,
            'class' => 'image-author-avatar',
            'width' => 24,
            'height' => 24,
            'loading' => 'lazy',
        ])
        <span class="image-author-name">{{ '@'.$image->user->username }}</span>
    </a>
@else
    <span class="image-author image-author-anonymous">
        <span
            class="user-avatar-initials image-author-avatar anonymous-avatar"
            style="--avatar-color: #c5c0ba; width: 24px; height: 24px;"
            aria-hidden="true"
        >?</span>
        <span class="image-author-name">Anonymous</span>
    </span>
@endif