document.addEventListener('click', async (event) => {
    const button = event.target.closest('.copy-link-btn');

    if (!button) {
        return;
    }

    const url = button.dataset.copyUrl;

    if (!url) {
        return;
    }

    const originalLabel = button.textContent;

    try {
        await navigator.clipboard.writeText(url);
        button.textContent = 'Copied!';
        button.classList.add('copied');

        window.setTimeout(() => {
            button.textContent = originalLabel;
            button.classList.remove('copied');
        }, 2000);
    } catch (error) {
        console.error('Failed to copy link', error);
    }
});