document.addEventListener('click', async (event) => {
    const button = event.target.closest('.like-btn');

    if (!button) {
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
            throw new Error('Like request failed');
        }

        const data = await response.json();

        const labelLiked = button.dataset.labelLiked || 'Unlike this image';
        const labelUnliked = button.dataset.labelUnliked || 'Like this image';

        button.classList.toggle('liked', data.liked);
        button.querySelector('.like-count').textContent = data.count;
        button.setAttribute('aria-label', data.liked ? labelLiked : labelUnliked);

        if (data.recent_likers) {
            updateCommentLikedBy(button, data.recent_likers);
        }
    } catch (error) {
        console.error(error);
    } finally {
        button.classList.remove('loading');
    }
});

function updateCommentLikedBy(button, likers) {
    const item = button.closest('.comment-item');
    if (!item) {
        return;
    }

    const root = item.querySelector('[data-comment-liked-by]');
    if (!root) {
        return;
    }

    const avatars = root.querySelector('[data-liked-by-avatars]');
    if (!avatars) {
        return;
    }

    const total = Number(likers.total || 0);
    const others = Number(likers.others_count || 0);
    const users = Array.isArray(likers.users) ? likers.users : [];

    if (total <= 0) {
        root.hidden = true;
        root.classList.add('is-empty');
        avatars.innerHTML = '';
        return;
    }

    root.hidden = false;
    root.classList.remove('is-empty');
    avatars.setAttribute(
        'aria-label',
        `${total} ${total === 1 ? 'like' : 'likes'}`,
    );

    const parts = users.map((user) => {
        const title = `@${escapeHtml(user.username)}`;
        if (user.avatar_url) {
            return `<a href="${escapeAttr(user.url)}" class="liked-by-avatar-link" title="${title}"><img src="${escapeAttr(user.avatar_url)}" alt="${title}" class="liked-by-avatar liked-by-avatar--sm" width="22" height="22" loading="lazy"></a>`;
        }

        return `<a href="${escapeAttr(user.url)}" class="liked-by-avatar-link" title="${title}"><span class="user-avatar-initials liked-by-avatar liked-by-avatar--sm" style="--avatar-color: ${escapeAttr(user.color)}; width: 22px; height: 22px;" role="img" aria-label="${title}">${escapeHtml(user.initials)}</span></a>`;
    });

    if (others > 0) {
        const label = others > 99 ? '99+' : String(others);
        parts.push(`<span class="liked-by-more liked-by-more--sm" title="${others} more" data-liked-by-more>+${label}</span>`);
    }

    avatars.innerHTML = parts.join('');
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function escapeAttr(value) {
    return escapeHtml(value).replace(/'/g, '&#39;');
}
