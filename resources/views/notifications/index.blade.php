@extends('layouts.app')

@section('title', 'Notifications — HeartLovePics')
@section('meta_description', 'Your activity notifications on HeartLovePics.')
@section('canonical', route('notifications.index'))

@section('content')
    <section class="notifications-page">
        @include('partials.flash')

        <div class="notifications-header">
            <h2 class="notifications-title">Notifications</h2>
            @if ($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" class="notifications-mark-all">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">Mark all as read</button>
                </form>
            @endif
        </div>

        <details class="notifications-preferences">
            <summary class="notifications-preferences-summary">Notification preferences</summary>
            <form action="{{ route('notifications.preferences') }}" method="POST" class="notifications-preferences-form">
                @csrf
                @method('PUT')
                <ul class="notifications-preferences-list">
                    @foreach ([
                        'notify_likes' => 'Likes on your images',
                        'notify_comments' => 'Comments on your images',
                        'notify_bookmarks' => 'Bookmarks on your images',
                        'notify_replies' => 'Replies to your comments',
                        'notify_mentions' => '@mentions in comments',
                        'notify_follows' => 'New followers',
                        'notify_shouts' => 'Shouts on your profile',
                    ] as $field => $label)
                        <li>
                            <label class="notifications-preference">
                                <input type="hidden" name="{{ $field }}" value="0">
                                <input
                                    type="checkbox"
                                    name="{{ $field }}"
                                    value="1"
                                    @checked(old($field, $user->{$field}))
                                >
                                <span>{{ $label }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                <button type="submit" class="btn btn-sm btn-secondary">Save preferences</button>
            </form>
        </details>

        @if ($groups->isEmpty())
            @include('partials.empty-state-suggestions', [
                'message' => 'No notifications yet.',
                'suggestions' => [
                    ['label' => 'Browse the latest gallery', 'url' => route('home')],
                    ['label' => 'View your profile', 'url' => route('users.show', auth()->user())],
                ],
            ])
        @else
            <ul class="notifications-list">
                @foreach ($groups as $group)
                    @php
                        $visitParams = ['type' => $group->type];

                        if ($group->image) {
                            $visitParams['image_id'] = $group->image->id;
                        }

                        if (in_array($group->type, [\App\Models\UserNotification::TYPE_COMMENT_REPLY, \App\Models\UserNotification::TYPE_COMMENT_MENTION], true)) {
                            $visitParams['comment_id'] = $group->comment?->id;
                        }

                        $avatarActor = in_array($group->type, [
                            \App\Models\UserNotification::TYPE_USER_FOLLOW,
                            \App\Models\UserNotification::TYPE_PROFILE_SHOUT,
                        ], true)
                            ? $group->actorUsers()->first()
                            : null;
                    @endphp
                    <li class="notifications-item {{ $group->isUnread() ? 'notifications-item--unread' : 'notifications-item--read' }}">
                        <a href="{{ route('notifications.visit', $visitParams) }}" class="notifications-link">
                            @if ($group->image)
                                <img
                                    class="notifications-thumb"
                                    src="{{ $group->image->thumbnail_url }}"
                                    alt="{{ $group->image->alt_text ?? $group->image->title }}"
                                    width="56"
                                    height="56"
                                    loading="lazy"
                                >
                            @elseif ($avatarActor)
                                @include('partials.user-avatar', [
                                    'user' => $avatarActor,
                                    'class' => 'notifications-thumb',
                                    'width' => 56,
                                    'height' => 56,
                                ])
                            @endif
                            <span class="notifications-body">
                                <span class="notifications-message">{{ $group->message() }}</span>
                                <time class="notifications-time" datetime="{{ $group->latestAt()->toIso8601String() }}">
                                    {{ $group->latestAt()->diffForHumans() }}
                                </time>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($groups->hasPages())
                <div class="pagination">
                    @if ($groups->onFirstPage())
                        <span>&laquo;</span>
                    @else
                        <a href="{{ $groups->previousPageUrl() }}">&laquo;</a>
                    @endif

                    @foreach ($groups->getUrlRange(1, $groups->lastPage()) as $page => $url)
                        @if ($page == $groups->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($groups->hasMorePages())
                        <a href="{{ $groups->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span>&raquo;</span>
                    @endif
                </div>
            @endif
        @endif
    </section>
@endsection