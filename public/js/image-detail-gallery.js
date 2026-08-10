(function () {
    const primary = document.getElementById('image-detail-primary');
    const primaryImg = document.getElementById('image-detail-primary-img');
    const thumbs = document.getElementById('image-detail-gallery-more');

    if (!primary || !primaryImg || !thumbs) {
        return;
    }

    function activate(thumb) {
        const src = thumb.dataset.fullSrc;

        if (!src) {
            return;
        }

        primaryImg.src = src;
        primaryImg.alt = thumb.dataset.fullAlt || '';

        if (thumb.dataset.fullWidth && thumb.dataset.fullHeight) {
            primaryImg.setAttribute('width', thumb.dataset.fullWidth);
            primaryImg.setAttribute('height', thumb.dataset.fullHeight);
        } else {
            primaryImg.removeAttribute('width');
            primaryImg.removeAttribute('height');
        }

        primary.dataset.lightboxSrc = src;
        primary.dataset.lightboxAlt = thumb.dataset.fullAlt || '';

        thumbs.querySelectorAll('.image-detail-gallery-thumb').forEach(function (element) {
            element.classList.toggle('is-active', element === thumb);
        });
    }

    thumbs.addEventListener('click', function (event) {
        const thumb = event.target.closest('.image-detail-gallery-thumb');

        if (thumb) {
            activate(thumb);
        }
    });

    thumbs.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        const thumb = event.target.closest('.image-detail-gallery-thumb');

        if (thumb) {
            event.preventDefault();
            activate(thumb);
        }
    });
})();
