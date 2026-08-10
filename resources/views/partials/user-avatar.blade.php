@php
    $class = $class ?? '';
    $width = $width ?? 36;
    $height = $height ?? 36;
    $alt = $alt ?? ($user->username.' profile picture');
@endphp

@if ($user->hasAvatar())
    <img
        src="{{ $user->avatar_url }}"
        alt="{{ $alt }}"
        class="{{ $class }}"
        width="{{ $width }}"
        height="{{ $height }}"
        @if (($loading ?? null) === 'lazy') loading="lazy" @endif
    >
@else
    <span
        class="user-avatar-initials {{ $class }}"
        style="--avatar-color: {{ $user->avatar_color }}; width: {{ $width }}px; height: {{ $height }}px;"
        role="img"
        aria-label="{{ $alt }}"
    >{{ $user->avatar_initials }}</span>
@endif