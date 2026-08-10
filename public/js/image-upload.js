(() => {
    const form = document.querySelector('form[action*="profile/upload"]');

    if (!form || form.dataset.ajaxUpload !== '1') {
        return;
    }

    const progressRoot = document.getElementById('upload-progress');
    const progressBar = document.getElementById('upload-progress-bar');
    const progressLabel = document.getElementById('upload-progress-label');
    const altTextInput = document.getElementById('alt_text');
    const mainInput = document.getElementById('image-input');
    const additionalInput = document.getElementById('additional-images-input');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    let isSubmitting = false;

    const countSelectedImages = () => {
        const mainCount = mainInput?.files?.length || 0;
        const additionalCount = additionalInput?.files?.length || 0;
        return mainCount + additionalCount;
    };

    const setProgress = (percent, label) => {
        if (!progressRoot || !progressBar || !progressLabel) {
            return;
        }

        progressRoot.classList.remove('hidden');
        document.body.classList.add('modal-open');
        progressBar.value = percent;
        progressLabel.textContent = label;
    };

    const hideProgress = () => {
        if (progressRoot) {
            progressRoot.classList.add('hidden');
        }

        document.body.classList.remove('modal-open');

        if (progressBar) {
            progressBar.value = 0;
        }
    };

    const setSubmitting = (submitting) => {
        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.disabled = submitting;
        });
    };

    const confirmAltText = () => {
        if (!altTextInput || altTextInput.value.trim() !== '') {
            return true;
        }

        return window.confirm(
            'Alt text helps people using screen readers understand your image. Post without alt text?',
        );
    };

    // PHP can prepend raw warning/notice text before the JSON body (e.g. a
    // "max_file_uploads exceeded" notice from the built-in dev server) —
    // fall back to parsing from the first `{`/`[` so a stray warning doesn't
    // hide the real error message behind a generic one.
    const parseJsonResponse = (text) => {
        try {
            return JSON.parse(text);
        } catch (error) {
            const start = text.search(/[{[]/);

            if (start > 0) {
                return JSON.parse(text.slice(start));
            }

            throw error;
        }
    };

    const uploadWithProgress = (formData, imageCount) => new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        const label = imageCount > 1 ? `${imageCount} images` : 'image';

        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-CSRF-TOKEN', token);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', (event) => {
            if (!event.lengthComputable) {
                setProgress(0, `Uploading ${label}…`);
                return;
            }

            const percent = Math.round((event.loaded / event.total) * 100);
            setProgress(percent, `Uploading ${label}… ${percent}%`);
        });

        xhr.addEventListener('load', () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    resolve(parseJsonResponse(xhr.responseText));
                } catch (error) {
                    reject(error);
                }

                return;
            }

            // Validation (422) and other JSON error responses carry a real
            // message from the server — surface the specific field error when
            // present, since it's more useful than the generic top-level one.
            try {
                const body = parseJsonResponse(xhr.responseText);
                const fieldError = body.errors ? Object.values(body.errors).flat()[0] : null;
                reject(new Error(fieldError || body.message || 'Upload failed'));
            } catch (error) {
                reject(new Error('Upload failed'));
            }
        });

        xhr.addEventListener('error', () => reject(new Error('Upload failed')));
        xhr.send(formData);
    });

    form.addEventListener('submit', async (event) => {
        if (isSubmitting || !confirmAltText()) {
            event.preventDefault();
            return;
        }

        event.preventDefault();
        isSubmitting = true;
        setSubmitting(true);
        setProgress(0, 'Preparing upload…');

        try {
            if (typeof window.syncUploadImages === 'function') {
                window.syncUploadImages();
            }

            const imageCount = countSelectedImages();
            const label = imageCount > 1 ? `${imageCount} images` : 'image';

            if (typeof form.prepareForUpload === 'function') {
                setProgress(0, `Processing ${label}…`);
                await form.prepareForUpload();
            }

            const formData = new FormData(form);
            // Ensure the clicked submit button is included in form data
            if (form.clickedSubmitter && form.clickedSubmitter.name) {
                formData.set(form.clickedSubmitter.name, form.clickedSubmitter.value);
            }
            const data = await uploadWithProgress(formData, imageCount);

            setProgress(100, data.message || 'Upload complete. Redirecting…');

            if (data.redirect) {
                window.location.assign(data.redirect);
                return;
            }

            window.location.reload();
        } catch (error) {
            console.error(error);
            hideProgress();
            isSubmitting = false;
            setSubmitting(false);
            window.alert(error.message || 'Upload failed. Please try again.');
        }
    });
})();