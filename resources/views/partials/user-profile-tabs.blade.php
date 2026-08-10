@php
    $isOwner = $isOwner ?? false;
@endphp

<nav class="user-profile-tabs" aria-label="Profile sections">
    <a
        href="{{ route('users.show', $user) }}"
        class="user-profile-tab {{ $activeTab === 'uploads' ? 'active' : '' }}"
    >
        Images
        <span class="user-profile-tab-count">{{ number_format($uploadCount) }}</span>
    </a>
    <a
        href="{{ route('users.likes', $user) }}"
        class="user-profile-tab {{ $activeTab === 'likes' ? 'active' : '' }}"
    >
        Likes
        <span class="user-profile-tab-count">{{ number_format($likedCount) }}</span>
    </a>
    <a
        href="{{ route('users.journal', $user) }}"
        class="user-profile-tab {{ $activeTab === 'journal' ? 'active' : '' }}"
    >
        Journal
        <span class="user-profile-tab-count">{{ number_format($journalCount ?? 0) }}</span>
    </a>
    @if ($isOwner)
        <a
            href="{{ route('users.bookmarks', $user) }}"
            class="user-profile-tab {{ $activeTab === 'bookmarks' ? 'active' : '' }}"
        >
            Bookmarks
            <span class="user-profile-tab-count">{{ number_format($bookmarkCount ?? 0) }}</span>
        </a>
        <a
            href="{{ route('users.drafts', $user) }}"
            class="user-profile-tab {{ $activeTab === 'drafts' ? 'active' : '' }}"
        >
            Drafts
            <span class="user-profile-tab-count">{{ number_format($draftCount ?? 0) }}</span>
        </a>
        <a
            href="{{ route('users.stats', $user) }}"
            class="user-profile-tab {{ $activeTab === 'stats' ? 'active' : '' }}"
        >
            Stats
        </a>
    @endif
</nav>