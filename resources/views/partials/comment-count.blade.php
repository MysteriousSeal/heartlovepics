<span class="comment-btn stat-btn" aria-label="{{ number_format($image->comments_count ?? 0) }} comments">
    <svg class="comment-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
        <path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4V5Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
    </svg>
    <span>{{ number_format($image->comments_count ?? 0) }}</span>
</span>