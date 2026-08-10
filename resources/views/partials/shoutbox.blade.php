@php
    $shouts = $shouts ?? collect();
    $canShout = $canShout ?? false;
@endphp

<section class="shoutbox" id="shouts">
    <h3 class="comments-title">Shoutbox</h3>

    @if ($canShout)
        <form method="POST" action="{{ route('shouts.store', $user) }}" class="comment-form">
            @csrf
            <div class="form-group">
                <label for="shout-body" class="sr-only">Leave a shout</label>
                <textarea
                    id="shout-body"
                    name="body"
                    class="form-control"
                    rows="2"
                    placeholder="Leave a shout on {{ '@'.$user->username }}'s wall…"
                    required
                    maxlength="{{ \App\Models\Shout::MAX_LENGTH }}"
                >{{ old('body') }}</textarea>
                @error('body')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Post shout</button>
        </form>
    @elseif (auth()->check() && auth()->user()->is($user))
        {{-- Owners moderate their wall but don't shout on it themselves. --}}
    @elseif (auth()->check())
        <div class="comment-restricted-notice" role="status">
            <span class="comment-restricted-notice-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <path d="M8 11V8a4 4 0 1 1 8 0v3" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>
                    <rect x="6" y="11" width="12" height="9" fill="none" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </span>
            <div class="comment-restricted-notice-content">
                <p class="comment-restricted-notice-title">Shouting disabled</p>
                <p class="comment-restricted-notice-text">Your account is restricted from posting.</p>
            </div>
        </div>
    @else
        <p class="shoutbox-guest-prompt">
            <a href="{{ route('login') }}">Sign in</a> to leave a shout on {{ '@'.$user->username }}'s wall.
        </p>
    @endif

    @if ($shouts->isEmpty())
        <p class="comments-empty">No shouts yet.</p>
    @else
        <ul class="shout-list">
            @foreach ($shouts as $shout)
                <li class="shout-item">
                    <div class="shout-header">
                        <a href="{{ route('users.show', $shout->author) }}" class="shout-author">{{ '@'.$shout->author->username }}</a>
                        <time class="shout-date" datetime="{{ $shout->created_at->toIso8601String() }}">
                            {{ $shout->created_at->diffForHumans() }}
                        </time>
                    </div>
                    <p class="shout-body">{{ $shout->body }}</p>
                    @auth
                        @if ($shout->canBeDeletedBy(auth()->user()))
                            <form
                                method="POST"
                                action="{{ route('shouts.destroy', $shout) }}"
                                class="shout-delete-form"
                                data-confirm="Delete this shout?"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="shout-delete-btn">Delete</button>
                            </form>
                        @endif
                    @endauth
                </li>
            @endforeach
        </ul>
    @endif
</section>
