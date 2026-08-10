(function () {
    const zone = document.getElementById('drop-zone');
    const input = document.getElementById('image-input');
    const preview = document.getElementById('image-side-preview');
    const emptyState = document.getElementById('image-preview-empty');
    const resolution = document.getElementById('image-preview-resolution');
    const rotateLeft = document.getElementById('image-rotate-left');
    const rotateRight = document.getElementById('image-rotate-right');
    const warningGroup = document.getElementById('content-warning-group');
    const nsfwRadios = document.querySelectorAll('input[name="is_nsfw"]');
    const form = input ? input.closest('form') : null;

    if (!zone || !input || !preview) {
        return;
    }

    let sourceFile = null;
    let sourceImage = null;
    let rotation = 0;
    let skipImageProcessing = false;
    let clickedSubmitter = null;
    let isSubmitting = false;

    preview.addEventListener('error', () => {
        preview.hidden = true;
        preview.removeAttribute('src');

        if (emptyState) {
            emptyState.hidden = false;
        }

        updateResolution();
    });

    function formatFileSize(bytes) {
        if (!bytes) {
            return null;
        }

        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unit = 0;

        while (size >= 1024 && unit < units.length - 1) {
            size /= 1024;
            unit += 1;
        }

        const precision = unit === 0 ? 0 : 1;

        return `${size.toFixed(precision)} ${units[unit]}`;
    }

    function mimeTypeLabel(mimeType) {
        const labels = {
            'image/jpeg': 'JPEG',
            'image/png': 'PNG',
            'image/gif': 'GIF',
            'image/webp': 'WebP',
        };

        return labels[mimeType] || 'Image';
    }

    function updateResolution(width, height, fileSizeBytes, mimeType) {
        if (!resolution) {
            return;
        }

        if (width && height) {
            const parts = [`${width} × ${height}`, mimeTypeLabel(mimeType)];

            if (rotation !== 0) {
                parts.push(`rotated ${rotation}°`);
            }

            const fileSize = formatFileSize(fileSizeBytes);

            if (fileSize) {
                parts.push(fileSize);
            }

            resolution.textContent = parts.join(' · ');

            return;
        }

        resolution.textContent = '';
    }

    function rotatedDimensions(width, height) {
        if (rotation % 180 !== 0) {
            return [height, width];
        }

        return [width, height];
    }

    function applyRotationToCanvas(source, degrees) {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const radians = (degrees * Math.PI) / 180;
        const width = source.naturalWidth || source.width;
        const height = source.naturalHeight || source.height;

        if (degrees % 180 !== 0) {
            canvas.width = height;
            canvas.height = width;
        } else {
            canvas.width = width;
            canvas.height = height;
        }

        context.translate(canvas.width / 2, canvas.height / 2);
        context.rotate(radians);
        context.drawImage(source, -width / 2, -height / 2);

        return canvas;
    }

    function outputMimeType(file) {
        const supported = ['image/jpeg', 'image/png', 'image/webp'];

        if (supported.includes(file.type)) {
            return file.type;
        }

        return 'image/png';
    }

    function canvasToBlob(canvas, type) {
        return new Promise((resolve, reject) => {
            const quality = type === 'image/jpeg' ? 0.92 : undefined;

            canvas.toBlob((blob) => {
                if (blob) {
                    resolve(blob);
                    return;
                }

                reject(new Error('Failed to process image'));
            }, type, quality);
        });
    }

    function replaceInputFile(fileOrBlob, fileName) {
        const file = fileOrBlob instanceof File
            ? fileOrBlob
            : new File([fileOrBlob], fileName, { type: fileOrBlob.type || 'image/png' });
        const transfer = new DataTransfer();

        transfer.items.add(file);
        input.files = transfer.files;
        sourceFile = file;
    }

    function updatePreview() {
        if (!sourceImage || !sourceFile) {
            return;
        }

        const canvas = applyRotationToCanvas(sourceImage, rotation);
        const previewType = outputMimeType(sourceFile);

        preview.onload = () => {
            const [width, height] = rotatedDimensions(
                sourceImage.naturalWidth,
                sourceImage.naturalHeight
            );

            updateResolution(width, height, sourceFile.size, sourceFile.type);
        };
        preview.src = canvas.toDataURL(previewType);
        preview.hidden = false;

        if (emptyState) {
            emptyState.hidden = true;
        }
    }

    function loadSourceImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                const image = new Image();

                image.onload = () => resolve(image);
                image.onerror = () => reject(new Error('Failed to load image'));
                image.src = event.target.result;
            };
            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsDataURL(file);
        });
    }

    async function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) {
            return;
        }

        sourceFile = file;
        sourceImage = null;
        rotation = 0;
        skipImageProcessing = false;

        try {
            sourceImage = await loadSourceImage(file);
            updatePreview();
        } catch (error) {
            console.error(error);
            sourceFile = null;
            sourceImage = null;
            rotation = 0;
            preview.hidden = true;
            preview.removeAttribute('src');

            if (emptyState) {
                emptyState.hidden = false;
            }

            updateResolution();
        }
    }

    function rotate(direction) {
        if (!sourceImage) {
            return;
        }

        rotation = (rotation + direction + 360) % 360;
        updatePreview();
    }

    function setSubmittingState(submitting) {
        if (!form) {
            return;
        }

        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.disabled = submitting;
        });
    }

    async function prepareImageForUpload() {
        if (!sourceFile || rotation === 0) {
            return;
        }

        const canvas = applyRotationToCanvas(sourceImage, rotation);
        const blob = await canvasToBlob(canvas, outputMimeType(sourceFile));

        replaceInputFile(blob, sourceFile.name);
    }

    if (form) {
        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.addEventListener('click', (event) => {
                clickedSubmitter = event.submitter || button;
                form.clickedSubmitter = clickedSubmitter;
            });
        });

        form.prepareForUpload = async () => {
            if (!sourceFile || rotation === 0) {
                return;
            }

            await prepareImageForUpload();
        };

        form.addEventListener('submit', async (event) => {
            if (form.dataset.ajaxUpload === '1') {
                return;
            }

            if (skipImageProcessing) {
                return;
            }

            if (!sourceFile || rotation === 0) {
                return;
            }

            event.preventDefault();

            if (isSubmitting) {
                return;
            }

            isSubmitting = true;
            setSubmittingState(true);

            try {
                await prepareImageForUpload();
                skipImageProcessing = true;
                isSubmitting = false;
                setSubmittingState(false);

                if (clickedSubmitter && typeof form.requestSubmit === 'function') {
                    form.requestSubmit(clickedSubmitter);
                    return;
                }

                form.submit();
            } catch (error) {
                console.error(error);
                isSubmitting = false;
                setSubmittingState(false);
                skipImageProcessing = false;
            }
        });
    }

    // Multi-image picker (additional-images.js) owns the drop zone when present.
    const multiPicker = document.getElementById('image-files-picker');

    if (!multiPicker) {
        zone.addEventListener('click', () => input.click());

        input.addEventListener('change', () => {
            if (input.files[0]) {
                showPreview(input.files[0]);
            }
        });

        zone.addEventListener('dragover', (event) => {
            event.preventDefault();
            zone.classList.add('dragover');
        });

        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));

        zone.addEventListener('drop', (event) => {
            event.preventDefault();
            zone.classList.remove('dragover');

            const file = event.dataTransfer.files[0];

            if (!file) {
                return;
            }

            const transfer = new DataTransfer();

            transfer.items.add(file);
            input.files = transfer.files;
            showPreview(file);
        });
    }

    window.setMainImageFile = (file) => {
        showPreview(file);
    };

    window.clearMainImageFile = () => {
        sourceFile = null;
        sourceImage = null;
        rotation = 0;
        preview.hidden = true;
        preview.removeAttribute('src');

        if (emptyState) {
            emptyState.hidden = false;
        }

        updateResolution();
    };

    if (rotateLeft) {
        rotateLeft.addEventListener('click', () => rotate(-90));
    }

    if (rotateRight) {
        rotateRight.addEventListener('click', () => rotate(90));
    }

    function syncWarningGroup() {
        if (!warningGroup) {
            return;
        }

        const selected = document.querySelector('input[name="is_nsfw"]:checked');
        warningGroup.hidden = !selected || selected.value !== '1';
    }

    nsfwRadios.forEach((radio) => {
        radio.addEventListener('change', syncWarningGroup);
    });

    syncWarningGroup();
})();