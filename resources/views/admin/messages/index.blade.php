@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
    <div class="admin-list-page">
        <header class="admin-list-hero">
            <p class="admin-list-kicker">Inbox</p>
            <h2 class="admin-list-title">Messages</h2>
            <p class="admin-list-lede">
                Contact form submissions from the site — mark as done when handled, or reply by email.
            </p>
            <div class="admin-list-meta">
                <span class="admin-list-chip">
                    {{ number_format($newCount) }}
                    new
                </span>
                <span class="admin-list-chip">
                    {{ number_format($archivedCount) }}
                    archived
                </span>
                @if (request()->filled('search'))
                    <span class="admin-list-chip is-filtered">Filtered</span>
                @endif
            </div>
        </header>

        <nav class="admin-list-tabs" aria-label="Message folders">
            <a
                href="{{ route('admin.messages.index', array_filter(['tab' => 'new', 'search' => request('search')])) }}"
                class="admin-list-tab {{ $tab === 'new' ? 'active' : '' }}"
            >
                New
                <span class="admin-list-tab-count">{{ number_format($newCount) }}</span>
            </a>
            <a
                href="{{ route('admin.messages.index', array_filter(['tab' => 'archived', 'search' => request('search')])) }}"
                class="admin-list-tab {{ $tab === 'archived' ? 'active' : '' }}"
            >
                Archived
                <span class="admin-list-tab-count">{{ number_format($archivedCount) }}</span>
            </a>
        </nav>

        <form method="GET" action="{{ route('admin.messages.index') }}" class="admin-list-toolbar filter-bar">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="Search username, email, subject, or message…"
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Search</button>
            @if (request()->filled('search'))
                <a href="{{ route('admin.messages.index', ['tab' => $tab]) }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>

        @if ($messages->isEmpty())
            <div class="admin-list-empty empty-state">
                @if (request()->filled('search'))
                    <p>No {{ $tab === 'archived' ? 'archived' : 'new' }} messages match this search.</p>
                    <p><a href="{{ route('admin.messages.index', ['tab' => $tab]) }}">Clear search</a></p>
                @elseif ($tab === 'archived')
                    <p>No archived messages yet.</p>
                    <p class="text-muted">Mark a message as done and it will appear here.</p>
                @else
                    <p>No new messages.</p>
                    <p class="text-muted">When someone uses the public contact form, their message will show up here.</p>
                @endif
            </div>
        @else
            <p class="admin-result-count">
                Showing {{ $messages->firstItem() }}&ndash;{{ $messages->lastItem() }} of {{ number_format($messages->total()) }}
                {{ $tab === 'archived' ? 'archived' : 'new' }}
                {{ \Illuminate\Support\Str::plural('message', $messages->total()) }}
            </p>

            <ul class="admin-messages-list">
                @foreach ($messages as $message)
                    <li class="admin-message-card {{ $message->is_archived ? 'is-archived' : '' }}">
                        <header class="admin-message-card-header">
                            <div class="admin-message-from">
                                <div class="admin-message-avatar" aria-hidden="true">
                                    @if ($message->user)
                                        @include('partials.user-avatar', [
                                            'user' => $message->user,
                                            'class' => 'admin-message-avatar-img',
                                            'width' => 40,
                                            'height' => 40,
                                        ])
                                    @else
                                        <span class="admin-message-avatar-fallback">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($message->username, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="admin-message-identity">
                                    <div class="admin-message-name-row">
                                        @if ($message->user)
                                            <a
                                                href="{{ route('users.show', $message->user) }}"
                                                class="admin-message-username"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >{{ $message->username }}</a>
                                            <span class="badge badge-published">Account</span>
                                        @else
                                            <span class="admin-message-username">{{ $message->username }}</span>
                                            <span class="badge badge-draft">Guest</span>
                                        @endif
                                        @if ($message->is_archived)
                                            <span class="badge badge-draft">Done</span>
                                        @endif
                                    </div>
                                    <a href="mailto:{{ $message->email }}" class="admin-message-email">
                                        {{ $message->email }}
                                    </a>
                                </div>
                            </div>

                            <div class="admin-message-meta">
                                <time
                                    class="admin-message-date"
                                    datetime="{{ $message->created_at->toIso8601String() }}"
                                    title="{{ $message->created_at->format('M j, Y g:i A') }}"
                                >
                                    {{ $message->created_at->diffForHumans() }}
                                </time>
                                @if ($message->is_archived && $message->archived_at)
                                    <span class="admin-message-archived-at" title="{{ $message->archived_at->format('M j, Y g:i A') }}">
                                        Done {{ $message->archived_at->diffForHumans() }}
                                    </span>
                                @endif
                                @if ($message->ip_address)
                                    <span class="admin-message-ip" title="IP address">{{ $message->ip_address }}</span>
                                @endif
                            </div>
                        </header>

                        <div class="admin-message-card-body">
                            <h3 class="admin-message-subject">{{ $message->subject }}</h3>
                            <p class="admin-message-body">{{ $message->message }}</p>
                        </div>

                        <footer class="admin-message-card-footer">
                            <a
                                href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}"
                                class="btn btn-sm btn-secondary"
                            >
                                Reply
                            </a>
                            @if ($message->is_archived)
                                <form
                                    action="{{ route('admin.messages.unarchive', $message) }}"
                                    method="POST"
                                    class="admin-message-action-form"
                                >
                                    @csrf
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-sm btn-secondary">Restore to New</button>
                                </form>
                            @else
                                <form
                                    action="{{ route('admin.messages.archive', $message) }}"
                                    method="POST"
                                    class="admin-message-action-form"
                                >
                                    @csrf
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-sm btn-primary">Mark as done</button>
                                </form>
                            @endif
                            <form
                                action="{{ route('admin.messages.destroy', $message) }}"
                                method="POST"
                                class="admin-message-delete-form"
                                data-confirm="Delete this message from {{ $message->username }}?"
                                data-confirm-label="Delete"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </footer>
                    </li>
                @endforeach
            </ul>

            @if ($messages->hasPages())
                <div class="pagination">
                    @if ($messages->onFirstPage())
                        <span>&laquo;</span>
                    @else
                        <a href="{{ $messages->previousPageUrl() }}">&laquo;</a>
                    @endif

                    @foreach ($messages->getUrlRange(1, $messages->lastPage()) as $page => $url)
                        @if ($page == $messages->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($messages->hasMorePages())
                        <a href="{{ $messages->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span>&raquo;</span>
                    @endif
                </div>
            @endif
        @endif
    </div>
@endsection
