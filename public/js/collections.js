function updateCollectionPickerCount(picker) {
    const badge = picker?.querySelector('.collection-picker-count');

    if (!badge) {
        return;
    }

    const count = picker.querySelectorAll('.collection-picker-list input[type="checkbox"]:checked').length;
    badge.textContent = String(count);
    badge.hidden = count < 1;
}

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('.collection-picker-toggle');
    const openMenu = document.querySelector('.collection-picker-menu:not([hidden])');

    if (toggle) {
        const menu = toggle.nextElementSibling;
        const isOpen = !menu.hidden;

        if (openMenu && openMenu !== menu) {
            openMenu.hidden = true;
            openMenu.previousElementSibling?.setAttribute('aria-expanded', 'false');
        }

        menu.hidden = isOpen;
        toggle.setAttribute('aria-expanded', String(!isOpen));
        return;
    }

    if (openMenu && !event.target.closest('.collection-picker')) {
        openMenu.hidden = true;
        openMenu.previousElementSibling?.setAttribute('aria-expanded', 'false');
    }
});

document.addEventListener('change', async (event) => {
    const checkbox = event.target.closest('.collection-picker-list input[type="checkbox"]');

    if (!checkbox) {
        return;
    }

    const url = checkbox.dataset.url;
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!url || !token) {
        return;
    }

    checkbox.disabled = true;

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
            throw new Error('Collection toggle failed');
        }

        const data = await response.json();
        checkbox.checked = data.inCollection;
    } catch (error) {
        checkbox.checked = !checkbox.checked;
        console.error(error);
    } finally {
        checkbox.disabled = false;
        updateCollectionPickerCount(checkbox.closest('.collection-picker'));
    }
});
