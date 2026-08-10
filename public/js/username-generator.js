(() => {
    const btn = document.getElementById('generate-username-btn');
    const input = document.getElementById('username');

    if (!btn || !input) {
        return;
    }

    btn.addEventListener('click', async () => {
        const url = btn.dataset.suggestUrl;

        if (!url || btn.disabled) {
            return;
        }

        btn.disabled = true;

        try {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Failed to generate username');
            }

            const data = await response.json();
            input.value = data.username;
            input.focus();
        } catch (error) {
            console.error(error);
        } finally {
            btn.disabled = false;
        }
    });
})();
