@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="page-header">
        <h2>Users</h2>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
        <input
            type="search"
            name="search"
            class="form-control"
            placeholder="Search by username…"
            value="{{ request('search') }}"
        >
        <select name="status" class="form-control">
            <option value="">All users</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="banned" @selected(request('status') === 'banned')>Banned</option>
        </select>
        <select name="sort" class="form-control">
            <option value="" @selected(($sort ?? '') === '')>Newest first</option>
            <option value="oldest" @selected(($sort ?? '') === 'oldest')>Oldest first</option>
            <option value="images" @selected(($sort ?? '') === 'images')>Most images</option>
            <option value="likes_received" @selected(($sort ?? '') === 'likes_received')>Most likes received</option>
            <option value="comments" @selected(($sort ?? '') === 'comments')>Most comments</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if (request()->hasAny(['search', 'status', 'sort']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    @if ($users->isEmpty())
        <div class="empty-state">
            @if (request()->hasAny(['search', 'status']))
                <p>No users match these filters.</p>
                <p><a href="{{ route('admin.users.index') }}">Clear filters</a></p>
            @else
                <p>No users found.</p>
            @endif
        </div>
    @else
        <p class="admin-result-count">
            Showing {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} of {{ number_format($users->total()) }}
            {{ \Illuminate\Support\Str::plural('user', $users->total()) }}
        </p>

        <div class="admin-users-table">
            <div class="admin-users-head" aria-hidden="true">
                <span>User</span>
                <span>Images</span>
                <span>Likes given</span>
                <span>Likes received</span>
                <span>Comments</span>
                <span>Joined</span>
                <span>Status</span>
                <span></span>
            </div>

            <ul class="admin-users-list">
                @foreach ($users as $user)
                    <li class="admin-user-row">
                        <div class="admin-user-identity">
                            <a href="{{ route('users.show', $user) }}" class="admin-user-profile-link" target="_blank" rel="noopener noreferrer">
                                @include('partials.user-avatar', [
                                    'user' => $user,
                                    'class' => 'admin-user-avatar',
                                    'width' => 40,
                                    'height' => 40,
                                ])
                                <span class="admin-user-username">{{ $user->username }}</span>
                            </a>
                        </div>
                        <span class="admin-user-stat" data-label="Images">{{ number_format($user->images_count) }}</span>
                        <span class="admin-user-stat" data-label="Likes given">{{ number_format($user->likes_given_count) }}</span>
                        <span class="admin-user-stat" data-label="Likes received">{{ number_format($user->likes_received_count) }}</span>
                        <span class="admin-user-stat" data-label="Comments">{{ number_format($user->comments_posted_count) }}</span>
                        <time class="admin-user-joined" data-label="Joined" datetime="{{ $user->created_at->toIso8601String() }}">
                            {{ $user->created_at->format('M j, Y') }}
                        </time>
                        <span class="admin-user-status">
                            @if ($user->is_banned)
                                <span class="badge badge-banned">Banned</span>
                            @else
                                <span class="badge badge-published">Active</span>
                            @endif
                        </span>
                        <div class="admin-user-actions">
                            @if ($user->is_banned)
                                <form
                                    action="{{ route('admin.users.unban', $user) }}"
                                    method="POST"
                                    class="delete-form"
                                    data-confirm="Unban {{ $user->username }}?"
                                    data-confirm-label="Unban"
                                    data-confirm-tone="neutral"
                                >
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary">Unban</button>
                                </form>
                            @else
                                <form
                                    action="{{ route('admin.users.ban', $user) }}"
                                    method="POST"
                                    class="delete-form"
                                    data-confirm="Ban {{ $user->username }}? They will be unable to upload images or comment."
                                    data-confirm-label="Ban"
                                >
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Ban</button>
                                </form>
                            @endif

                            @if ($user->canBeDeleted())
                                <form
                                    action="{{ route('admin.users.destroy', $user) }}"
                                    method="POST"
                                    class="delete-form"
                                    data-confirm="Permanently delete {{ '@'.$user->username }}? This only works because they have no posts, comments, or other activity. This cannot be undone."
                                    data-confirm-label="Delete"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            @else
                                @php
                                    $deleteBlockers = $user->deletionBlockers();
                                @endphp
                                <button
                                    type="button"
                                    class="btn btn-sm btn-secondary"
                                    disabled
                                    title="Cannot delete: has {{ implode(', ', $deleteBlockers) }}"
                                >
                                    Delete
                                </button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        @if ($users->hasPages())
            <div class="pagination">
                @if ($users->onFirstPage())
                    <span>&laquo;</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}">&laquo;</a>
                @endif

                @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if ($page == $users->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}">&raquo;</a>
                @else
                    <span>&raquo;</span>
                @endif
            </div>
        @endif
    @endif
@endsection
