(() => {
    const bell = document.querySelector('.notifications-bell');

    if (!bell) {
        return;
    }

    const url = bell.dataset.unreadUrl;
    const badge = bell.querySelector('.notifications-bell-badge');
    const pollIntervalMs = 45000;
    let timerId = null;

    if (!url || !badge) {
        return;
    }

    const updateBadge = (count) => {
        const safeCount = Number.isFinite(count) ? Math.max(0, count) : 0;

        if (safeCount > 0) {
            badge.hidden = false;
            badge.textContent = safeCount > 99 ? '99+' : String(safeCount);
            bell.setAttribute('aria-label', `${safeCount} unread notifications`);
        } else {
            badge.hidden = true;
            badge.textContent = '';
            bell.setAttribute('aria-label', 'Notifications');
        }
    };

    const fetchUnreadCount = async () => {
        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            updateBadge(data.count);
        } catch (error) {
            console.error(error);
        }
    };

    const startPolling = () => {
        if (timerId !== null) {
            return;
        }

        timerId = window.setInterval(fetchUnreadCount, pollIntervalMs);
    };

    const stopPolling = () => {
        if (timerId === null) {
            return;
        }

        window.clearInterval(timerId);
        timerId = null;
    };

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            fetchUnreadCount();
            startPolling();
        } else {
            stopPolling();
        }
    });

    startPolling();
})();