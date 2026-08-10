(() => {
    const buttons = document.querySelectorAll('.layout-toggle-btn');

    if (!buttons.length) {
        return;
    }

    const STORAGE_KEY = 'imageDetailLayout';

    function applyLayout(layout) {
        document.documentElement.setAttribute('data-image-layout', layout);

        buttons.forEach((button) => {
            button.setAttribute('aria-pressed', button.dataset.layoutOption === layout ? 'true' : 'false');
        });
    }

    const current = document.documentElement.getAttribute('data-image-layout') || 'stacked';
    applyLayout(current);

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const layout = button.dataset.layoutOption;
            localStorage.setItem(STORAGE_KEY, layout);
            applyLayout(layout);
        });
    });
})();
