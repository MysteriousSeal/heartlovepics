document.addEventListener('click', async (event) => {
    const button = event.target.closest('.bookmark-btn');

    if (!button || button.disabled || button.classList.contains('bookmark-btn--disabled')) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    if (button.classList.contains('loading')) {
        return;
    }

    const url = button.dataset.url;
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!url || !token) {
        return;
    }

    button.classList.add('loading');

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Bookmark request failed');
        }

        const data = await response.json();

        const labelBookmarked = button.dataset.labelBookmarked || 'Remove bookmark';
        const labelUnbookmarked = button.dataset.labelUnbookmarked || 'Bookmark this image';

        button.classList.toggle('bookmarked', data.bookmarked);
        const countEl = button.querySelector('.bookmark-count');
        if (countEl && typeof data.count === 'number') {
            countEl.textContent = data.count;
        }
        button.setAttribute('aria-label', data.bookmarked ? labelBookmarked : labelUnbookmarked);

        if (typeof data.collectionPickerHtml === 'string') {
            const picker = button.closest('.image-detail-header-actions')?.querySelector('.collection-picker');
            const menu = picker?.querySelector('.collection-picker-menu');
            if (menu) {
                menu.innerHTML = data.collectionPickerHtml;
            }
            if (typeof updateCollectionPickerCount === 'function') {
                updateCollectionPickerCount(picker);
            }
        }
    } catch (error) {
        console.error(error);
    } finally {
        button.classList.remove('loading');
    }
});