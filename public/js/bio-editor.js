(() => {
    const textarea = document.getElementById('bio');
    const host = document.getElementById('bio-editor');
    const counter = document.getElementById('bio-char-count');

    if (!textarea || !host || typeof Quill === 'undefined') {
        return;
    }

    const maxLength = Number(counter?.dataset.maxLength) || 500;

    const quill = new Quill(host, {
        theme: 'snow',
        placeholder: textarea.getAttribute('placeholder') || 'A short intro about you',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean'],
            ],
            clipboard: {
                matchVisual: false,
            },
        },
    });

    // Prefer HTML paste from websites while stripping risky content client-side.
    quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
        if (node.tagName === 'SCRIPT' || node.tagName === 'STYLE' || node.tagName === 'IFRAME') {
            return new (Quill.import('delta'))();
        }

        return delta;
    });

    const initialHtml = textarea.value.trim();

    if (initialHtml) {
        quill.root.innerHTML = initialHtml;
    }

    const plainText = () => quill.getText().replace(/\u00a0/g, ' ').trim();

    const syncTextarea = () => {
        const html = quill.getSemanticHTML ? quill.getSemanticHTML() : quill.root.innerHTML;
        const plain = plainText();
        textarea.value = plain === '' ? '' : html;

        if (counter) {
            // The limit is on what was typed, so count text rather than markup.
            counter.textContent = `${plain.length} / ${maxLength}`;
            counter.classList.toggle('form-char-count--limit', plain.length >= maxLength);
        }
    };

    // Keep the editor within the limit instead of failing on submit. Trim just
    // the overflow — undo() would roll back the whole preceding edit.
    quill.on('text-change', (delta, oldDelta, source) => {
        if (source === 'user') {
            // getLength() counts the trailing newline Quill always keeps.
            const overflow = quill.getLength() - 1 - maxLength;

            if (overflow > 0) {
                quill.deleteText(maxLength, overflow, 'silent');
            }
        }

        syncTextarea();
    });

    syncTextarea();

    textarea.closest('form')?.addEventListener('submit', syncTextarea);
})();
