(() => {
    const input = document.getElementById('username');
    const status = document.getElementById('username-availability');

    if (!input || !status) {
        return;
    }

    const checkUrl = input.dataset.checkUrl;
    const currentUsername = input.dataset.currentUsername || '';
    const DEBOUNCE_MS = 400;

    let debounceTimer = null;
    let requestToken = 0;

    const setStatus = (state, message) => {
        status.textContent = message;
        status.className = 'username-availability-status'
            + (state ? ` username-availability-${state}` : '');
    };

    input.addEventListener('input', () => {
        const value = input.value.trim();
        clearTimeout(debounceTimer);
        requestToken++;

        if (value === '') {
            setStatus(null, '');
            return;
        }

        if (value === currentUsername) {
            setStatus('ok', 'This is your current username.');
            return;
        }

        if (value.length < 3) {
            setStatus('error', 'Too short — minimum 3 characters.');
            return;
        }

        if (!/^[A-Za-z0-9_-]+$/.test(value)) {
            setStatus('error', 'Only letters, numbers, dashes, and underscores.');
            return;
        }

        setStatus('checking', 'Checking availability…');

        const token = ++requestToken;

        debounceTimer = setTimeout(async () => {
            try {
                const url = new URL(checkUrl, window.location.origin);
                url.searchParams.set('username', value);

                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();

                if (token !== requestToken) {
                    return;
                }

                setStatus(data.available ? 'ok' : 'error', data.available ? 'Available!' : (data.message || 'Not available.'));
            } catch (error) {
                if (token !== requestToken) {
                    return;
                }

                console.error(error);
                setStatus('error', 'Could not check availability right now.');
            }
        }, DEBOUNCE_MS);
    });
})();
