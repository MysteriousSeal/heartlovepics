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
                <span class="admin-list-chip">
                    {{ number_format($contactsCount) }}
                    {{ \Illuminate\Support\Str::plural('contact', $contactsCount) }}
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
            <a
                href="{{ route('admin.messages.index', array_filter(['tab' => 'contacts', 'search' => request('search')])) }}"
                class="admin-list-tab {{ $tab === 'contacts' ? 'active' : '' }}"
            >
                Contacts
                <span class="admin-list-tab-count">{{ number_format($contactsCount) }}</span>
            </a>
        </nav>

        <form method="GET" action="{{ route('admin.messages.index') }}" class="admin-list-toolbar filter-bar">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="{{ $tab === 'contacts' ? 'Search email…' : 'Search username, email, subject, or message…' }}"
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Search</button>
            @if (request()->filled('search'))
                <a href="{{ route('admin.messages.index', ['tab' => $tab]) }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>

        @if ($tab === 'contacts')
            @if ($contacts->isEmpty())
                <div class="admin-list-empty empty-state">
                    @if (request()->filled('search'))
                        <p>No contacts match this search.</p>
                        <p><a href="{{ route('admin.messages.index', ['tab' => 'contacts']) }}">Clear search</a></p>
                    @else
                        <p>No contacts yet.</p>
                        <p class="text-muted">Every unique email address that has used the contact form will show up here.</p>
                    @endif
                </div>
            @else
                <p class="admin-result-count">
                    Showing {{ $contacts->firstItem() }}&ndash;{{ $contacts->lastItem() }} of {{ number_format($contacts->total()) }}
                    {{ \Illuminate\Support\Str::plural('contact', $contacts->total()) }}
                </p>

                <div class="admin-contacts-table admin-list-table">
                    <div class="admin-contacts-head" aria-hidden="true">
                        <span>Email</span>
                        <span>Name</span>
                        <span>Messages sent</span>
                        <span>Last message</span>
                    </div>

                    <ul class="admin-contacts-list">
                        @foreach ($contacts as $contact)
                            <li class="admin-contact-row">
                                <a
                                    href="{{ route('admin.messages.index', ['tab' => 'new', 'search' => $contact->email]) }}"
                                    class="admin-contact-email"
                                    data-label="Email"
                                >{{ $contact->email }}</a>
                                <span class="admin-contact-name" data-label="Name">{{ $contact->latest_username }}</span>
                                <span class="admin-contact-stat" data-label="Messages sent">{{ number_format($contact->messages_count) }}</span>
                                <time
                                    class="admin-contact-date"
                                    data-label="Last message"
                                    datetime="{{ \Illuminate\Support\Carbon::parse($contact->last_message_at)->toIso8601String() }}"
                                    title="{{ \Illuminate\Support\Carbon::parse($contact->last_message_at)->format('M j, Y g:i A') }}"
                                >
                                    {{ \Illuminate\Support\Carbon::parse($contact->last_message_at)->diffForHumans() }}
                                </time>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($contacts->hasPages())
                    <div class="pagination">
                        @if ($contacts->onFirstPage())
                            <span>&laquo;</span>
                        @else
                            <a href="{{ $contacts->previousPageUrl() }}">&laquo;</a>
                        @endif

                        @foreach ($contacts->getUrlRange(1, $contacts->lastPage()) as $page => $url)
                            @if ($page == $contacts->currentPage())
                                <span class="active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($contacts->hasMorePages())
                            <a href="{{ $contacts->nextPageUrl() }}">&raquo;</a>
                        @else
                            <span>&raquo;</span>
                        @endif
                    </div>
                @endif
            @endif
        @elseif ($messages->isEmpty())
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
                                @unless ($message->is_archived)
                                    <span class="badge {{ $message->replied_at ? 'badge-published' : 'badge-banned' }}">
                                        {{ $message->replied_at ? 'Answered' : 'Not answered' }}
                                    </span>
                                @endunless
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

                        @if ($message->replies->isNotEmpty())
                            <section class="admin-message-history" aria-label="Reply history">
                                <header class="admin-message-history-title-row">
                                    <span class="admin-message-history-label">
                                        Reply history
                                    </span>
                                    <span class="admin-message-history-count">
                                        {{ $message->replies->count() }}
                                        {{ \Illuminate\Support\Str::plural('reply', $message->replies->count()) }}
                                    </span>
                                </header>

                                <ol class="admin-message-history-list">
                                    @foreach ($message->replies as $index => $reply)
                                        <li class="admin-message-history-item">
                                            <header class="admin-message-history-header">
                                                <span class="admin-message-history-label">
                                                    Reply {{ $index + 1 }}
                                                    @if ($reply->admin)
                                                        <span class="admin-message-history-admin">
                                                            by {{ $reply->admin->username }}
                                                        </span>
                                                    @endif
                                                </span>
                                                <time
                                                    class="admin-message-history-date"
                                                    datetime="{{ $reply->created_at->toIso8601String() }}"
                                                    title="{{ $reply->created_at->format('M j, Y g:i A') }}"
                                                >
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </time>
                                            </header>
                                            <p class="admin-message-history-meta">
                                                From {{ $reply->from_address ?: config('mail.from.address') }}
                                                → {{ $message->email }}
                                            </p>
                                            <h4 class="admin-message-history-subject">{{ $reply->subject }}</h4>
                                            <p class="admin-message-history-body">{{ $reply->body }}</p>
                                        </li>
                                    @endforeach
                                </ol>
                            </section>
                        @endif

                        @php
                            $replyOpen = (string) old('reply_message_id', request('reply')) === (string) $message->id;
                            $defaultSubject = old('reply_subject', str_starts_with(mb_strtolower($message->subject), 're:')
                                ? $message->subject
                                : 'Re: '.$message->subject);
                        @endphp

                        <details class="admin-message-reply" @if ($replyOpen) open @endif>
                            <summary class="admin-message-reply-summary">
                                Reply by email
                                <span class="admin-message-reply-from">from {{ config('mail.from.address') }}</span>
                            </summary>

                            <form
                                method="POST"
                                action="{{ route('admin.messages.reply', $message) }}"
                                class="admin-message-reply-form"
                            >
                                @csrf
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="reply_message_id" value="{{ $message->id }}">

                                <div class="form-group">
                                    <label for="reply-subject-{{ $message->id }}">Subject</label>
                                    <input
                                        type="text"
                                        id="reply-subject-{{ $message->id }}"
                                        name="reply_subject"
                                        class="form-control"
                                        value="{{ $defaultSubject }}"
                                        required
                                        maxlength="{{ \App\Models\ContactMessage::REPLY_SUBJECT_MAX }}"
                                    >
                                    @error('reply_subject')
                                        @if ($replyOpen)
                                            <p class="form-error">{{ $messageBag }}</p>
                                        @endif
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="reply-body-{{ $message->id }}">Message</label>
                                    <textarea
                                        id="reply-body-{{ $message->id }}"
                                        name="reply_body"
                                        class="form-control"
                                        rows="6"
                                        required
                                        maxlength="{{ \App\Models\ContactMessage::REPLY_BODY_MAX }}"
                                        placeholder="Write your reply…"
                                    >{{ $replyOpen ? old('reply_body') : '' }}</textarea>
                                    <p class="form-hint">
                                        Sent from {{ config('mail.from.address') }}. The original message is quoted below your reply.
                                    </p>
                                    @error('reply_body')
                                        @if ($replyOpen)
                                            <p class="form-error">{{ $messageBag }}</p>
                                        @endif
                                    @enderror
                                </div>

                                <div class="admin-message-reply-actions">
                                    <label class="form-check">
                                        <input type="hidden" name="archive_after" value="0">
                                        <input
                                            type="checkbox"
                                            name="archive_after"
                                            value="1"
                                            @checked(old('archive_after', '1') == '1')
                                        >
                                        Mark as done after sending
                                    </label>
                                    <button type="submit" class="btn btn-sm btn-primary">Send reply</button>
                                </div>
                            </form>
                        </details>

                        <footer class="admin-message-card-footer">
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
                                    <button type="submit" class="btn btn-sm btn-secondary">Mark as done</button>
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
