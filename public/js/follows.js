document.addEventListener('click', async (event) => {
    const button = event.target.closest('.follow-btn');

    if (!button || button.disabled || button.classList.contains('follow-btn--guest')) {
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
            throw new Error('Follow request failed');
        }

        const data = await response.json();

        const labelFollowing = button.dataset.labelFollowing || 'Unfollow';
        const labelUnfollowed = button.dataset.labelUnfollowed || 'Follow';

        button.classList.toggle('follow-btn--following', data.following);
        button.textContent = data.following ? 'Following' : 'Follow';
        button.setAttribute('aria-label', data.following ? labelFollowing : labelUnfollowed);

        const countEl = document.querySelector('[data-followers-count]');
        if (countEl && typeof data.followers_count === 'number') {
            const count = data.followers_count;
            const valueEl = countEl.querySelector('.user-profile-stat-value');
            const labelEl = countEl.querySelector('.user-profile-stat-label');

            if (valueEl && labelEl) {
                valueEl.textContent = count.toLocaleString();
                labelEl.textContent = count === 1 ? 'follower' : 'followers';
            } else {
                countEl.textContent = `${count.toLocaleString()} ${count === 1 ? 'follower' : 'followers'}`;
            }
        }
    } catch (error) {
        console.error(error);
    } finally {
        button.classList.remove('loading');
    }
});