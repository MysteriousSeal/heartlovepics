(() => {
    const dialog = document.getElementById('confirm-dialog');
    const textEl = document.getElementById('confirm-dialog-text');
    const confirmBtn = document.getElementById('confirm-dialog-confirm');

    if (!dialog || !textEl || !confirmBtn) {
        return;
    }

    let pendingForm = null;

    function openDialog(form) {
        pendingForm = form;
        textEl.textContent = form.dataset.confirm;
        confirmBtn.textContent = form.dataset.confirmLabel || 'Delete';
        confirmBtn.classList.toggle('btn-danger', form.dataset.confirmTone !== 'neutral');
        confirmBtn.classList.toggle('btn-primary', form.dataset.confirmTone === 'neutral');
        dialog.classList.remove('hidden');
        dialog.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        confirmBtn.focus();
    }

    function closeDialog() {
        pendingForm = null;
        dialog.classList.add('hidden');
        dialog.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) {
            return;
        }

        if (form.dataset.confirmed === '1') {
            delete form.dataset.confirmed;
            return;
        }

        event.preventDefault();
        openDialog(form);
    });

    confirmBtn.addEventListener('click', () => {
        const form = pendingForm;
        closeDialog();

        if (form) {
            form.dataset.confirmed = '1';
            form.requestSubmit();
        }
    });

    dialog.querySelectorAll('[data-confirm-dismiss]').forEach((el) => {
        el.addEventListener('click', closeDialog);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !dialog.classList.contains('hidden')) {
            closeDialog();
        }
    });
})();
