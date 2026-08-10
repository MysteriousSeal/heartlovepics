<div class="gallery-activity-header">
<div class="gallery-activity-bar" aria-label="Gallery activity">
    <div class="gallery-activity-group">
        <span class="gallery-activity-heading">Last 24 hours</span>

        <div class="gallery-activity-stats">
        <span class="gallery-activity-stat stat-btn" title="New posts" aria-label="{{ number_format($activity['posts']) }} new posts">
            <svg class="image-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                <rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                <path d="M21 15l-5-5L5 21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ number_format($activity['posts']) }}</span>
        </span>

        <span class="gallery-activity-stat stat-btn" title="New likes" aria-label="{{ number_format($activity['likes']) }} new likes">
            <svg class="like-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <span>{{ number_format($activity['likes']) }}</span>
        </span>

        <span class="gallery-activity-stat stat-btn" title="New comments" aria-label="{{ number_format($activity['comments']) }} new comments">
            <svg class="comment-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                <path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4V5Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
            <span>{{ number_format($activity['comments']) }}</span>
        </span>

        <span class="gallery-activity-stat stat-btn" title="New views" aria-label="{{ number_format($activity['views']) }} new views">
            <svg class="view-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                <path d="M12 5C7 5 2.73 8.11 1 12.5 2.73 16.89 7 20 12 20s9.27-3.11 11-7.5C21.27 8.11 17 5 12 5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
            </svg>
            <span>{{ number_format($activity['views']) }}</span>
        </span>
        </div>
    </div>

    <div class="gallery-activity-end">
        <div class="gallery-activity-group gallery-activity-nsfw">
            <span class="gallery-activity-heading">NSFW</span>
            <label class="nsfw-visibility-toggle" for="show-nsfw-toggle">
                <input
                    type="checkbox"
                    id="show-nsfw-toggle"
                    data-age-confirmed="{{ ($ageConfirmed ?? false) ? '1' : '0' }}"
                    @checked($showNsfw ?? false)
                >
                <span class="nsfw-visibility-switch" aria-hidden="true"></span>
                <span class="nsfw-visibility-label">Show</span>
            </label>
        </div>

        <div class="gallery-activity-group">
        <span class="gallery-activity-heading">All time</span>

        <div class="gallery-activity-stats">
            <span class="gallery-activity-stat stat-btn" title="Total visits" aria-label="{{ number_format($totalVisits) }} total visits">
                <svg class="visit-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M2.5 12h19M12 2.5c2.8 2.9 4.2 6.4 4.2 9.5S14.8 18.6 12 21.5M12 2.5C9.2 5.4 7.8 8.9 7.8 12s1.4 6.6 4.2 9.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span>{{ number_format($totalVisits) }}</span>
            </span>

            <span class="gallery-activity-stat stat-btn" title="Total images" aria-label="{{ number_format($totalImages) }} total images">
                <svg class="image-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                    <path d="M21 15l-5-5L5 21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ number_format($totalImages) }}</span>
            </span>
        </div>
        </div>
    </div>
</div>
</div>