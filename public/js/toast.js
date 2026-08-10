(() => {
    const viewport = document.getElementById('toast-viewport');
    const initData = document.getElementById('toast-init-data');

    if (!viewport || !initData) {
        return;
    }

    const message = initData.dataset.message;

    if (!message) {
        return;
    }

    const actionLabel = initData.dataset.actionLabel || '';
    const actionUrl = initData.dataset.actionUrl || '';
    const duration = Number(initData.dataset.duration) || 8000;

    const toast = document.createElement('div');
    toast.className = 'toast';

    const iconEl = document.createElement('span');
    iconEl.className = 'toast-icon';
    iconEl.innerHTML = '<svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M5 13l4 4L19 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    toast.appendChild(iconEl);

    const body = document.createElement('div');
    body.className = 'toast-body';
    toast.appendChild(body);

    const messageEl = document.createElement('span');
    messageEl.className = 'toast-message';
    messageEl.textContent = message;
    body.appendChild(messageEl);

    let actionBtn = null;

    if (actionLabel && actionUrl) {
        actionBtn = document.createElement('button');
        actionBtn.type = 'button';
        actionBtn.className = 'toast-action';
        actionBtn.textContent = actionLabel;
        body.appendChild(actionBtn);
    }

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'toast-close';
    closeBtn.setAttribute('aria-label', 'Dismiss');
    closeBtn.textContent = '×';
    toast.appendChild(closeBtn);

    viewport.appendChild(toast);

    let timer = setTimeout(dismiss, duration);

    function dismiss() {
        clearTimeout(timer);
        toast.classList.add('toast-leaving');
        setTimeout(() => toast.remove(), 150);
    }

    closeBtn.addEventListener('click', dismiss);

    if (actionBtn) {
        actionBtn.addEventListener('click', async () => {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!token) {
                return;
            }

            actionBtn.disabled = true;
            clearTimeout(timer);

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    window.location.reload();

                    return;
                }

                actionBtn.disabled = false;
                timer = setTimeout(dismiss, duration);
            } catch (error) {
                console.error(error);
                actionBtn.disabled = false;
                timer = setTimeout(dismiss, duration);
            }
        });
    }
})();
