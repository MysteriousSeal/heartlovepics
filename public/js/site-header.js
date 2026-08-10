(function () {
    var subheader = document.getElementById('site-subheader');

    if (!subheader) {
        return;
    }

    function syncSubheaderHeight() {
        document.documentElement.style.setProperty(
            '--site-subheader-height',
            subheader.offsetHeight + 'px'
        );
    }

    syncSubheaderHeight();

    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(syncSubheaderHeight).observe(subheader);
    } else {
        window.addEventListener('resize', syncSubheaderHeight);
    }
})();