(function () {
    const root = document.getElementById('tag-input-root');
    const chips = document.getElementById('tag-input-chips');
    const input = document.getElementById('tag-input');
    const suggestions = document.getElementById('tag-suggestions');
    const suggestionsList = document.getElementById('tag-suggestions-list');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const errorMessage = document.getElementById('tag-input-error');

    if (!root || !chips || !input) {
        return;
    }

    const tagCounter = document.getElementById('tag-char-count');
    const maxTags = Number.parseInt(root.dataset.maxTags || '10', 10);
    const maxLength = Number.parseInt(root.dataset.maxLength || '50', 10);
    const suggestUrl = root.dataset.suggestUrl || '';
    const tagPattern = /^[\p{L}\p{N}\s-]+$/u;
    let suggestTimer = null;

    function showError(message) {
        if (!errorMessage) {
            return;
        }

        errorMessage.textContent = message;
        errorMessage.hidden = !message;
    }

    function updateTagCounter() {
        const count = currentTags().length;
        const atLimit = count >= maxTags;

        if (tagCounter) {
            tagCounter.textContent = `${count} / ${maxTags}`;
            tagCounter.classList.toggle('form-char-count--limit', atLimit);
        }

        input.disabled = atLimit;
        input.placeholder = atLimit ? 'Maximum tags reached' : 'Type a tag and press Enter';
    }

    function normalizeTag(value) {
        return value.trim().replace(/\s+/g, ' ').toLowerCase();
    }

    function currentTags() {
        return Array.from(chips.querySelectorAll('input[name="tags[]"]')).map(function (field) {
            return field.value;
        });
    }

    function hasTag(name) {
        const normalized = normalizeTag(name);

        return currentTags().some(function (tag) {
            return normalizeTag(tag) === normalized;
        });
    }

    // Wires the remove button for a chip, whether it was rendered by the
    // server on page load (editing an image that already has tags) or
    // created client-side just now via addTag().
    function wireRemoveButton(chip) {
        const removeButton = chip.querySelector('.tag-chip-remove');

        if (!removeButton) {
            return;
        }

        removeButton.addEventListener('click', function () {
            chip.remove();
            updateTagCounter();
            showError('');
            input.disabled = false;
            input.placeholder = 'Type a tag and press Enter';
            input.focus();
            fetchSuggestions();
        });
    }

    function createChip(name) {
        const chip = document.createElement('span');
        chip.className = 'tag-chip';
        chip.dataset.tag = name;

        const label = document.createElement('span');
        label.className = 'tag-chip-label';
        label.textContent = name;

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'tag-chip-remove';
        removeButton.setAttribute('aria-label', `Remove tag ${name}`);
        removeButton.textContent = '×';

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'tags[]';
        hiddenInput.value = name;

        chip.append(label, removeButton, hiddenInput);
        wireRemoveButton(chip);

        return chip;
    }

    function addTag(rawValue) {
        const value = rawValue.trim().replace(/\s+/g, ' ');

        if (!value) {
            return false;
        }

        if (currentTags().length >= maxTags) {
            showError(`You can add up to ${maxTags} tags. Remove one to add another.`);

            return false;
        }

        if (value.length > maxLength) {
            showError(`Tags must be ${maxLength} characters or fewer.`);

            return false;
        }

        if (!tagPattern.test(value)) {
            showError('Tags can only contain letters, numbers, spaces, and hyphens.');

            return false;
        }

        if (hasTag(value)) {
            showError('That tag is already added.');

            return false;
        }

        showError('');
        chips.appendChild(createChip(value));
        updateTagCounter();
        fetchSuggestions();

        return true;
    }

    window.HeartLoveTags = {
        addTag: addTag,
    };

    function renderSuggestions(tags) {
        if (!suggestions || !suggestionsList) {
            return;
        }

        suggestionsList.innerHTML = '';

        const available = tags.filter(function (tag) {
            return !hasTag(tag);
        });

        if (available.length === 0) {
            suggestions.hidden = true;

            return;
        }

        available.forEach(function (tag) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'tag-suggestion-chip';
            button.textContent = tag;
            button.addEventListener('click', function () {
                addTag(tag);
            });
            suggestionsList.appendChild(button);
        });

        suggestions.hidden = false;
    }

    function fetchSuggestions() {
        if (!suggestUrl || !titleInput) {
            return;
        }

        const title = titleInput.value.trim();
        let description = '';

        if (window.descriptionEditor && typeof window.descriptionEditor.getPlainText === 'function') {
            description = window.descriptionEditor.getPlainText();
        } else if (descriptionInput) {
            description = descriptionInput.value.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
        }

        if (!title && !description) {
            if (suggestions) {
                suggestions.hidden = true;
            }

            return;
        }

        const params = new URLSearchParams({ title: title, description: description });

        fetch(`${suggestUrl}?${params.toString()}`, {
            headers: { Accept: 'application/json' },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                renderSuggestions(data.tags || []);
            })
            .catch(function () {});
    }

    function scheduleSuggestions() {
        clearTimeout(suggestTimer);
        suggestTimer = setTimeout(fetchSuggestions, 300);
    }

    input.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') {
            return;
        }

        event.preventDefault();

        if (addTag(input.value)) {
            input.value = '';
        }
    });

    input.addEventListener('input', function () {
        showError('');
    });

    if (titleInput) {
        titleInput.addEventListener('input', scheduleSuggestions);
    }

    if (descriptionInput) {
        descriptionInput.addEventListener('input', scheduleSuggestions);
    }

    // Chips already in the DOM on load are server-rendered (editing an image
    // that already has tags) and need their remove buttons wired up too.
    Array.from(chips.querySelectorAll('.tag-chip')).forEach(wireRemoveButton);

    updateTagCounter();
    fetchSuggestions();
})();
