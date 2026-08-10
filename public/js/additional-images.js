(function () {
    const dropZone = document.getElementById('drop-zone');
    const picker = document.getElementById('image-files-picker');
    const mainInput = document.getElementById('image-input');
    const additionalInput = document.getElementById('additional-images-input');
    const list = document.getElementById('upload-images-list');
    const orderInput = document.getElementById('image-order-input');
    const expectedFileCountInput = document.getElementById('expected-file-count-input');
    const sidePreview = document.getElementById('image-side-preview');
    const emptyState = document.getElementById('image-preview-empty');
    const resolution = document.getElementById('image-preview-resolution');

    if (!dropZone || !picker || !mainInput || !additionalInput || !list) {
        return;
    }

    const MAX_TOTAL = 100;
    const DRAG_THRESHOLD = 6;
    const isEdit = list.dataset.hasMain === '1';
    /** @type {File[]} */
    let selectedFiles = [];

    /** @type {null | {
     *   item: HTMLElement,
     *   pointerId: number,
     *   startX: number,
     *   startY: number,
     *   offsetX: number,
     *   offsetY: number,
     *   width: number,
     *   height: number,
     *   moved: boolean,
     *   placeholder: HTMLElement | null,
     * }}
     */
    let drag = null;

    function attachDocumentDragListeners() {
        document.addEventListener('pointermove', onDocumentPointerMove, true);
        document.addEventListener('pointerup', onDocumentPointerUp, true);
        document.addEventListener('pointercancel', onDocumentPointerCancel, true);
    }

    function removeDocumentDragListeners() {
        document.removeEventListener('pointermove', onDocumentPointerMove, true);
        document.removeEventListener('pointerup', onDocumentPointerUp, true);
        document.removeEventListener('pointercancel', onDocumentPointerCancel, true);
    }

    function onDocumentPointerMove(event) {
        if (!drag || drag.pointerId !== event.pointerId) {
            return;
        }

        const dx = event.clientX - drag.startX;
        const dy = event.clientY - drag.startY;

        if (!drag.moved) {
            if (Math.hypot(dx, dy) < DRAG_THRESHOLD) {
                return;
            }

            beginDragVisual();
        }

        event.preventDefault();
        positionDragItem(event.clientX, event.clientY);
        updateDropTarget(event.clientX, event.clientY);
    }

    function onDocumentPointerUp(event) {
        if (!drag || drag.pointerId !== event.pointerId) {
            return;
        }

        removeDocumentDragListeners();
        endDrag(event, true);
    }

    function onDocumentPointerCancel(event) {
        if (!drag || drag.pointerId !== event.pointerId) {
            return;
        }

        removeDocumentDragListeners();
        endDrag(event, false);
    }

    function activeExistingItems() {
        return Array.from(list.querySelectorAll('.additional-images-item[data-image-key]:not(.is-marked-removed)'));
    }

    function allListItems() {
        return Array.from(list.querySelectorAll('.additional-images-item:not(.is-marked-removed)'));
    }

    function existingCount() {
        return activeExistingItems().length;
    }

    function maxNewFiles() {
        if (isEdit) {
            return Math.max(0, MAX_TOTAL - existingCount());
        }

        return MAX_TOTAL;
    }

    function setInputFiles(input, files) {
        const transfer = new DataTransfer();
        files.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    }

    function syncOrderInput() {
        if (!orderInput) {
            return;
        }

        // Preserve full list order of surviving existing keys (including main).
        const keys = allListItems()
            .map((item) => item.dataset.imageKey)
            .filter(Boolean);

        orderInput.value = keys.join(',');
    }

    function syncFormInputs() {
        const items = allListItems();
        const mainItem = list.querySelector('[data-image-key="main"]');
        const removeMainChecked = Boolean(mainItem?.classList.contains('is-marked-removed'));
        const removeMainCheckbox = document.getElementById('remove-main-checkbox');

        if (removeMainCheckbox) {
            removeMainCheckbox.checked = removeMainChecked;
        }

        // Map the visual list to form fields:
        // - first surviving tile is cover (existing key via image_order, or new file via main input)
        // - remaining new files go to additional_images[]
        const first = items[0];
        const newFilesInOrder = items.map((item) => item._file).filter(Boolean);

        if (!isEdit) {
            if (newFilesInOrder.length === 0) {
                setInputFiles(mainInput, []);
                setInputFiles(additionalInput, []);
            } else {
                setInputFiles(mainInput, [newFilesInOrder[0]]);
                setInputFiles(additionalInput, newFilesInOrder.slice(1));
            }
        } else if (first?._file) {
            // New file is cover — replace main on save.
            setInputFiles(mainInput, [first._file]);
            setInputFiles(
                additionalInput,
                items.slice(1).map((item) => item._file).filter(Boolean),
            );
        } else {
            // Cover is an existing image (main or promoted extra).
            setInputFiles(mainInput, []);
            setInputFiles(additionalInput, newFilesInOrder);
        }

        if (newFilesInOrder.length > 0 || isEdit) {
            picker.removeAttribute('required');
        }

        if (expectedFileCountInput) {
            expectedFileCountInput.value = String(newFilesInOrder.length);
        }

        rebuildSelectedFilesFromDom();
        syncOrderInput();
        updateCoverBadges();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const activeCount = allListItems().length;
        const canRemove = activeCount > 1;

        list.querySelectorAll('.additional-images-item').forEach((item) => {
            const removeButton = item.querySelector('.additional-images-remove');
            if (!removeButton) {
                return;
            }

            // Undo stays available on tiles marked for removal.
            // Otherwise only show delete when more than one image remains.
            if (item.classList.contains('is-marked-removed')) {
                removeButton.hidden = false;
                removeButton.disabled = false;
                return;
            }

            removeButton.hidden = !canRemove;
            removeButton.disabled = !canRemove;
        });
    }

    function updateCoverBadges() {
        const items = allListItems();

        items.forEach((item, index) => {
            item.classList.toggle('is-cover', index === 0);

            let badge = item.querySelector('.additional-images-badge');

            if (index === 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'additional-images-badge';
                    item.appendChild(badge);
                }
                badge.textContent = 'Cover';
            } else if (badge) {
                badge.remove();
            }
        });

        const cover = items[0];
        if (cover) {
            const img = cover.querySelector('img');
            if (img?.src) {
                updateSidePreviewFromUrl(img.src);
            }
        }
    }

    function updateSidePreviewFromUrl(src) {
        if (!sidePreview || !src) {
            return;
        }

        sidePreview.src = src;
        sidePreview.hidden = false;

        if (emptyState) {
            emptyState.hidden = true;
        }
    }

    function updateSidePreview(file) {
        if (!sidePreview || !file) {
            return;
        }

        const reader = new FileReader();

        reader.onload = (event) => {
            sidePreview.src = event.target.result;
            sidePreview.hidden = false;

            if (emptyState) {
                emptyState.hidden = true;
            }

            if (resolution) {
                const image = new Image();
                image.onload = () => {
                    const sizeKb = file.size >= 1024
                        ? `${(file.size / 1024).toFixed(1)} KB`
                        : `${file.size} B`;
                    resolution.textContent = `${image.naturalWidth} × ${image.naturalHeight} · ${sizeKb}`;
                };
                image.src = event.target.result;
            }

            if (typeof window.setMainImageFile === 'function') {
                window.setMainImageFile(file);
            }
        };

        reader.readAsDataURL(file);
    }

    function handleItemActivate(item) {
        if (!item) {
            return;
        }

        if (item._file) {
            updateSidePreview(item._file);
            return;
        }

        const img = item.querySelector('img');
        if (img?.src) {
            updateSidePreviewFromUrl(img.src);
        }
    }

    function createNewItem(file, index) {
        const item = document.createElement('div');
        item.className = 'additional-images-item';
        item.dataset.newIndex = String(index);
        item.tabIndex = 0;
        item.setAttribute('role', 'button');
        item.setAttribute('aria-label', 'Image ' + (index + 1) + '. Press arrow keys to reorder, Enter to preview.');

        const img = document.createElement('img');
        img.alt = '';
        img.draggable = false;
        item.appendChild(img);

        const reader = new FileReader();
        reader.onload = (event) => {
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);

        const handle = document.createElement('span');
        handle.className = 'upload-images-handle';
        handle.setAttribute('aria-hidden', 'true');
        handle.title = 'Drag to reorder';
        handle.textContent = '⋮⋮';
        item.appendChild(handle);

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'additional-images-remove';
        removeButton.setAttribute('aria-label', 'Remove this image');
        removeButton.textContent = '×';
        removeButton.addEventListener('click', () => {
            item.remove();
            rebuildSelectedFilesFromDom();
            syncFormInputs();
            refreshCoverPreview();
        });
        item.appendChild(removeButton);

        item._file = file;
        bindItemInteractions(item);

        return item;
    }

    function refreshCoverPreview() {
        const first = allListItems()[0];
        if (!first) {
            if (!isEdit && typeof window.clearMainImageFile === 'function') {
                window.clearMainImageFile();
            }
            return;
        }

        if (first._file) {
            updateSidePreview(first._file);
            return;
        }

        const img = first.querySelector('img');
        if (img?.src) {
            updateSidePreviewFromUrl(img.src);
        }
    }

    function rebuildSelectedFilesFromDom() {
        const newItems = Array.from(list.querySelectorAll('.additional-images-item[data-new-index]'));
        selectedFiles = newItems.map((item) => item._file).filter(Boolean);
        newItems.forEach((item, index) => {
            item.dataset.newIndex = String(index);
        });
    }

    function addFiles(fileList) {
        const incoming = Array.from(fileList).filter((file) => file.type.startsWith('image/'));
        const room = maxNewFiles() - selectedFiles.length;
        const toAdd = incoming.slice(0, Math.max(0, room));

        toAdd.forEach((file) => {
            const index = selectedFiles.length;
            selectedFiles.push(file);
            list.appendChild(createNewItem(file, index));
        });

        rebuildSelectedFilesFromDom();
        syncFormInputs();
        refreshCoverPreview();
    }

    function afterReorder() {
        rebuildSelectedFilesFromDom();
        syncFormInputs();
        refreshCoverPreview();
    }

    // --- Pointer-based drag reorder (mouse + touch) ---

    function onPointerDown(event) {
        const item = event.currentTarget;

        if (event.target.closest('button')) {
            return;
        }

        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        if (item.classList.contains('is-marked-removed')) {
            return;
        }

        const rect = item.getBoundingClientRect();

        drag = {
            item,
            pointerId: event.pointerId,
            startX: event.clientX,
            startY: event.clientY,
            offsetX: event.clientX - rect.left,
            offsetY: event.clientY - rect.top,
            width: rect.width,
            height: rect.height,
            moved: false,
            placeholder: null,
        };

        item.setPointerCapture(event.pointerId);
        attachDocumentDragListeners();
    }

    function onPointerMove(event) {
        if (!drag || drag.pointerId !== event.pointerId) {
            return;
        }

        const dx = event.clientX - drag.startX;
        const dy = event.clientY - drag.startY;

        if (!drag.moved) {
            if (Math.hypot(dx, dy) < DRAG_THRESHOLD) {
                return;
            }

            beginDragVisual();
        }

        event.preventDefault();
        positionDragItem(event.clientX, event.clientY);
        updateDropTarget(event.clientX, event.clientY);
    }

    function beginDragVisual() {
        drag.moved = true;

        const placeholder = document.createElement('div');
        placeholder.className = 'additional-images-item additional-images-placeholder';
        drag.item.after(placeholder);
        drag.placeholder = placeholder;

        drag.item.classList.add('is-dragging');
        drag.item.style.position = 'fixed';
        drag.item.style.width = drag.width + 'px';
        drag.item.style.height = drag.height + 'px';
        drag.item.style.zIndex = '80';
        drag.item.style.pointerEvents = 'none';

        // `position: fixed` already removes it from grid flow wherever it
        // lives in the DOM — the placeholder holds its slot. Reparenting it
        // (e.g. to document.body) would risk losing pointer capture in some
        // browsers, so it stays put; the CSS counter skips it explicitly.

        positionDragItem(drag.startX, drag.startY);
    }

    function positionDragItem(clientX, clientY) {
        drag.item.style.left = (clientX - drag.offsetX) + 'px';
        drag.item.style.top = (clientY - drag.offsetY) + 'px';
    }

    function updateDropTarget(clientX, clientY) {
        const others = Array.from(list.querySelectorAll('.additional-images-item'))
            .filter((el) => el !== drag.item && el !== drag.placeholder);

        let closestEl = null;
        let closestDistance = Infinity;
        let closestRect = null;

        others.forEach((el) => {
            const rect = el.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const distance = Math.hypot(clientX - centerX, clientY - centerY);

            if (distance < closestDistance) {
                closestDistance = distance;
                closestEl = el;
                closestRect = rect;
            }
        });

        if (!closestEl) {
            return;
        }

        const before = clientX < closestRect.left + closestRect.width / 2;

        if (before) {
            list.insertBefore(drag.placeholder, closestEl);
        } else {
            list.insertBefore(drag.placeholder, closestEl.nextSibling);
        }
    }

    function endDrag(event, shouldActivate) {
        if (!drag) {
            removeDocumentDragListeners();
            return;
        }

        const { item, moved, placeholder, pointerId } = drag;

        try {
            if (moved && placeholder) {
                placeholder.replaceWith(item);
            }

            item.classList.remove('is-dragging');
            item.style.position = '';
            item.style.left = '';
            item.style.top = '';
            item.style.width = '';
            item.style.height = '';
            item.style.zIndex = '';
            item.style.pointerEvents = '';

            if (item.hasPointerCapture(pointerId)) {
                item.releasePointerCapture(pointerId);
            }

            if (moved) {
                afterReorder();
            } else if (shouldActivate) {
                handleItemActivate(item);
            }
        } finally {
            removeDocumentDragListeners();
            drag = null;
        }
    }

    function onPointerUp(event) {
        if (!drag || drag.pointerId !== event.pointerId) {
            return;
        }

        endDrag(event, true);
    }

    function onPointerCancel(event) {
        if (!drag || drag.pointerId !== event.pointerId) {
            return;
        }

        endDrag(event, false);
    }

    function onKeyDown(event) {
        const item = event.currentTarget;

        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            const sibling = event.key === 'ArrowLeft' ? item.previousElementSibling : item.nextElementSibling;

            if (!sibling || !sibling.classList.contains('additional-images-item')) {
                return;
            }

            event.preventDefault();

            if (event.key === 'ArrowLeft') {
                list.insertBefore(item, sibling);
            } else {
                list.insertBefore(item, sibling.nextSibling);
            }

            afterReorder();
            item.focus();
            return;
        }

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            handleItemActivate(item);
        }
    }

    function bindItemInteractions(item) {
        item.addEventListener('pointerdown', onPointerDown);
        item.addEventListener('pointermove', onPointerMove);
        item.addEventListener('pointerup', onPointerUp);
        item.addEventListener('pointercancel', onPointerCancel);
        item.addEventListener('keydown', onKeyDown);
    }

    function bindExistingRemove(item) {
        const removeButton = item.querySelector('.additional-images-remove');
        const checkbox = item.querySelector('input[type="checkbox"]');

        if (!removeButton || !checkbox) {
            return;
        }

        removeButton.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const active = allListItems();
            const isRemoved = item.classList.contains('is-marked-removed');

            // Don't allow removing the last remaining image.
            if (!isRemoved && active.length <= 1) {
                return;
            }

            checkbox.checked = !checkbox.checked;
            item.classList.toggle('is-marked-removed', checkbox.checked);
            removeButton.setAttribute('aria-label', checkbox.checked ? 'Undo remove' : 'Remove this image');
            removeButton.textContent = checkbox.checked ? '↶' : '×';
            syncFormInputs();
            refreshCoverPreview();
        });
    }

    list.querySelectorAll('.additional-images-item[data-image-key]').forEach((item) => {
        bindItemInteractions(item);
        bindExistingRemove(item);
    });

    dropZone.addEventListener('click', (event) => {
        if (event.target.closest('button')) {
            return;
        }

        picker.click();
    });

    picker.addEventListener('change', () => {
        if (picker.files?.length) {
            addFiles(picker.files);
            picker.value = '';
        }
    });

    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('dragover');

        if (event.dataTransfer.files?.length) {
            addFiles(event.dataTransfer.files);
        }
    });

    const form = dropZone.closest('form');

    window.syncUploadImages = () => {
        rebuildSelectedFilesFromDom();
        syncFormInputs();
    };

    form?.addEventListener('submit', () => {
        rebuildSelectedFilesFromDom();
        syncFormInputs();
    });

    syncFormInputs();
    refreshCoverPreview();
})();
