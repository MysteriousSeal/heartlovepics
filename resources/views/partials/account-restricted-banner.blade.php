@if (auth()->check() && auth()->user()->is_banned)
    <div class="account-restricted-banner" role="status">
        <div class="account-restricted-banner-inner">
            <span class="account-restricted-banner-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <path d="M8 11V8a4 4 0 1 1 8 0v3" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>
                    <rect x="6" y="11" width="12" height="9" fill="none" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </span>
            <div class="account-restricted-banner-content">
                <p class="account-restricted-banner-title">Account restricted</p>
                <p class="account-restricted-banner-text">You cannot upload images or post comments. Browsing and liking are still available.</p>
            </div>
        </div>
    </div>
@endif