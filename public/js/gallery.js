(function () {
    const gallery = document.getElementById('gallery');
    const sentinel = document.getElementById('gallery-sentinel');
    const loader = document.getElementById('gallery-loader');

    if (!gallery || !sentinel) {
        return;
    }

    let nextPageUrl = gallery.dataset.nextPage || '';
    let hasMore = gallery.dataset.hasMore === 'true';
    let loading = false;

    function getColumns() {
        return gallery.querySelectorAll('.masonry-column');
    }

    function appendItems(html, offset) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();

        const items = wrapper.querySelectorAll('.masonry-item');
        const columns = getColumns();

        if (!columns.length) {
            return 0;
        }

        items.forEach((item, index) => {
            const column = columns[(offset + index) % columns.length];
            column.appendChild(item);
        });

        return items.length;
    }

    async function loadMore() {
        if (!hasMore || !nextPageUrl || loading) {
            return;
        }

        loading = true;
        loader?.classList.remove('hidden');

        try {
            const response = await fetch(nextPageUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load images');
            }

            const data = await response.json();
            const offset = Number(data.offset) || 0;
            const appended = appendItems(data.html, offset);

            nextPageUrl = data.next_page_url || '';
            hasMore = data.has_more;
            gallery.dataset.nextPage = nextPageUrl;
            gallery.dataset.hasMore = hasMore ? 'true' : 'false';
            gallery.dataset.loadedCount = String(offset + appended);

            if (!hasMore) {
                loader?.classList.add('hidden');
                observer.disconnect();
            }
        } catch (error) {
            console.error(error);
            loader?.classList.add('hidden');
        } finally {
            loading = false;
        }
    }

    const observer = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
            loadMore();
        }
    }, {
        rootMargin: '400px 0px',
    });

    if (hasMore) {
        observer.observe(sentinel);
    } else {
        loader?.classList.add('hidden');
    }
})();