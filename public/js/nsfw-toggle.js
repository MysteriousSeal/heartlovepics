(function () {
    const toggle = document.getElementById('show-nsfw-toggle');
    const modal = document.getElementById('nsfw-age-modal');
    const confirmButton = document.getElementById('nsfw-age-confirm');
    const cancelButton = document.getElementById('nsfw-age-cancel');
    const gallery = document.getElementById('gallery');
    const detailRoot = document.querySelector('[data-nsfw-detail="1"]');

    if (!modal) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let ageConfirmed = toggle
        ? toggle.dataset.ageConfirmed === '1'
        : detailRoot?.dataset.ageConfirmed === '1';
    let pendingRedirectUrl = null;
    const homeUrl = detailRoot?.dataset.homeUrl || null;
    const isDetailPage = Boolean(detailRoot);

    function openModal(redirectUrl = null) {
        pendingRedirectUrl = redirectUrl;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        confirmButton?.focus();
    }

    function closeModal() {
        pendingRedirectUrl = null;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    function applyNsfwVisibility(showNsfw) {
        document.querySelectorAll('[data-is-nsfw="1"]').forEach(function (media) {
            const isDetailMedia = media.classList.contains('image-detail-media');
            const shouldShow = isDetailMedia ? ageConfirmed : showNsfw;

            if (shouldShow) {
                media.classList.remove('is-nsfw');
                media.querySelector('.nsfw-age-label')?.remove();
                return;
            }

            media.classList.add('is-nsfw');

            if (!media.querySelector('.nsfw-age-label')) {
                const label = document.createElement('span');
                label.className = 'nsfw-age-label';
                label.setAttribute('aria-label', 'Adults only');
                label.textContent = '-18';
                media.appendChild(label);
            }
        });
    }

    async function savePreference(enabled, options = {}) {
        const previousChecked = toggle?.checked ?? false;

        if (toggle) {
            toggle.disabled = true;
        }

        try {
            const response = await fetch('/preferences/nsfw', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    enabled,
                    age_confirmed: options.ageConfirmed === true || (enabled && ageConfirmed),
                }),
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to save preference');
            }

            if (options.ageConfirmed) {
                ageConfirmed = true;

                if (toggle) {
                    toggle.dataset.ageConfirmed = '1';
                }

                if (detailRoot) {
                    detailRoot.dataset.ageConfirmed = '1';
                }
            }

            if (toggle) {
                toggle.checked = enabled;
            }

            applyNsfwVisibility(enabled);

            return true;
        } catch (error) {
            if (toggle) {
                toggle.checked = previousChecked;
            }

            applyNsfwVisibility(previousChecked);

            return false;
        } finally {
            if (toggle) {
                toggle.disabled = false;
            }
        }
    }

    function handleRefuse() {
        closeModal();

        if (toggle) {
            toggle.checked = false;
        }

        if (isDetailPage && !ageConfirmed && homeUrl) {
            window.location.href = homeUrl;
        }
    }

    toggle?.addEventListener('change', async function () {
        const enabling = toggle.checked;

        if (!enabling) {
            await savePreference(false);
            return;
        }

        if (!ageConfirmed) {
            toggle.checked = false;
            openModal();
            return;
        }

        await savePreference(true);
    });

    confirmButton?.addEventListener('click', async function () {
        const redirectUrl = pendingRedirectUrl;
        closeModal();

        const saved = await savePreference(true, { ageConfirmed: true });

        if (saved && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });

    cancelButton?.addEventListener('click', handleRefuse);

    modal.querySelectorAll('[data-nsfw-age-dismiss]').forEach(function (element) {
        element.addEventListener('click', handleRefuse);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            handleRefuse();
        }
    });

    gallery?.addEventListener('click', function (event) {
        if (ageConfirmed) {
            return;
        }

        const link = event.target.closest('a.nsfw-gallery-link');

        if (!link) {
            return;
        }

        event.preventDefault();
        openModal(link.href);
    });

    if (isDetailPage && !ageConfirmed) {
        openModal();
    }
})();