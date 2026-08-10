(function () {
    const lightbox = document.getElementById('image-lightbox');

    if (!lightbox) {
        return;
    }

    const lightboxImg = document.getElementById('image-lightbox-img');
    const closeButton = document.getElementById('image-lightbox-close');
    const prevButton = document.getElementById('image-lightbox-prev');
    const nextButton = document.getElementById('image-lightbox-next');
    const counter = document.getElementById('image-lightbox-counter');
    let activeTrigger = null;
    /** @type {{ src: string, alt: string }[]} */
    let items = [];
    let currentIndex = 0;

    function isOpen() {
        return !lightbox.classList.contains('hidden');
    }

    function collectGalleryItems() {
        const thumbs = document.querySelectorAll('.image-detail-gallery-thumb[data-full-src]');

        if (thumbs.length > 0) {
            return Array.from(thumbs).map(function (thumb) {
                return {
                    src: thumb.dataset.fullSrc,
                    alt: thumb.dataset.fullAlt || '',
                };
            });
        }

        return [];
    }

    function showItem(index) {
        if (items.length === 0) {
            return;
        }

        currentIndex = (index + items.length) % items.length;
        const item = items[currentIndex];

        lightboxImg.src = item.src;
        lightboxImg.alt = item.alt || '';

        if (counter) {
            if (items.length > 1) {
                counter.hidden = false;
                counter.textContent = (currentIndex + 1) + ' / ' + items.length;
            } else {
                counter.hidden = true;
                counter.textContent = '';
            }
        }

        updateNavVisibility();
    }

    function updateNavVisibility() {
        const multi = items.length > 1;

        if (prevButton) {
            prevButton.hidden = !multi;
        }

        if (nextButton) {
            nextButton.hidden = !multi;
        }
    }

    function openLightbox(trigger) {
        const src = trigger.dataset.lightboxSrc;

        if (!src || trigger.classList.contains('is-nsfw')) {
            return;
        }

        activeTrigger = trigger;
        items = collectGalleryItems();

        if (items.length === 0) {
            items = [{
                src: src,
                alt: trigger.dataset.lightboxAlt || '',
            }];
        }

        const matchIndex = items.findIndex(function (item) {
            return item.src === src;
        });

        currentIndex = matchIndex >= 0 ? matchIndex : 0;
        showItem(currentIndex);

        lightbox.classList.remove('hidden');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        closeButton.focus();
    }

    function closeLightbox() {
        lightbox.classList.add('hidden');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        lightboxImg.src = '';
        items = [];
        currentIndex = 0;

        if (counter) {
            counter.hidden = true;
            counter.textContent = '';
        }

        updateNavVisibility();
        activeTrigger?.focus();
        activeTrigger = null;
    }

    function goPrev() {
        if (items.length < 2) {
            return;
        }

        showItem(currentIndex - 1);
    }

    function goNext() {
        if (items.length < 2) {
            return;
        }

        showItem(currentIndex + 1);
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-lightbox-src]');

        if (trigger) {
            openLightbox(trigger);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (!isOpen()) {
            if (event.key === 'Enter' || event.key === ' ') {
                const trigger = event.target.closest('[data-lightbox-src]');

                if (trigger) {
                    event.preventDefault();
                    openLightbox(trigger);
                }
            }

            return;
        }

        if (event.key === 'Escape') {
            closeLightbox();
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            goPrev();
            return;
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            goNext();
        }
    });

    closeButton.addEventListener('click', closeLightbox);
    prevButton?.addEventListener('click', function (event) {
        event.stopPropagation();
        goPrev();
    });
    nextButton?.addEventListener('click', function (event) {
        event.stopPropagation();
        goNext();
    });

    lightbox.querySelectorAll('[data-lightbox-dismiss]').forEach(function (element) {
        element.addEventListener('click', closeLightbox);
    });
})();
