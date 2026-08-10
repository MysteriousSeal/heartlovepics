(() => {
    const textarea = document.getElementById('description');
    const host = document.getElementById('description-editor');

    if (!textarea || !host || typeof Quill === 'undefined') {
        return;
    }

    const toolbar = [
        [{ header: [2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['blockquote', 'link'],
        ['clean'],
    ];

    const quill = new Quill(host, {
        theme: 'snow',
        placeholder: textarea.getAttribute('placeholder') || 'Write a description or a full story…',
        modules: {
            toolbar,
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

    const syncTextarea = () => {
        const html = quill.getSemanticHTML ? quill.getSemanticHTML() : quill.root.innerHTML;
        const plain = quill.getText().replace(/\u00a0/g, ' ').trim();
        textarea.value = plain === '' ? '' : html;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    };

    quill.on('text-change', syncTextarea);
    syncTextarea();

    const form = textarea.closest('form');

    if (form) {
        form.addEventListener('submit', syncTextarea);
    }

    window.descriptionEditor = {
        getPlainText: () => quill.getText().replace(/\u00a0/g, ' ').trim(),
        getHtml: () => textarea.value,
        quill,
    };
})();