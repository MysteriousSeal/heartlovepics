(() => {
    const textarea = document.getElementById('description');
    const host = document.getElementById('description-editor');
    const group = textarea?.closest('.description-editor-group');

    if (!textarea || !host || !group || typeof Quill === 'undefined') {
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

    let mode = 'visual';

    const modeBar = document.createElement('div');
    modeBar.className = 'description-editor-mode';
    modeBar.setAttribute('role', 'group');
    modeBar.setAttribute('aria-label', 'Editor mode');

    const visualBtn = document.createElement('button');
    visualBtn.type = 'button';
    visualBtn.className = 'description-editor-mode-btn is-active';
    visualBtn.dataset.mode = 'visual';
    visualBtn.textContent = 'Visual';

    const htmlBtn = document.createElement('button');
    htmlBtn.type = 'button';
    htmlBtn.className = 'description-editor-mode-btn';
    htmlBtn.dataset.mode = 'html';
    htmlBtn.textContent = 'HTML';

    modeBar.append(visualBtn, htmlBtn);
    group.insertBefore(modeBar, group.querySelector('.ql-toolbar') || host);

    textarea.classList.add('description-editor-html-input');
    textarea.removeAttribute('hidden');
    // Keep screen-reader / form field role; visibility is toggled via CSS + mode class.
    textarea.setAttribute('aria-label', 'HTML source');
    textarea.spellcheck = false;

    const toolbarEl = group.querySelector('.ql-toolbar');
    const containerEl = group.querySelector('.ql-container');

    const htmlFromQuill = () => {
        const html = quill.getSemanticHTML ? quill.getSemanticHTML() : quill.root.innerHTML;
        const plain = quill.getText().replace(/\u00a0/g, ' ').trim();

        return plain === '' ? '' : html;
    };

    const syncTextareaFromQuill = () => {
        if (mode !== 'visual') {
            return;
        }

        textarea.value = htmlFromQuill();
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    };

    const applyMode = (nextMode) => {
        if (nextMode === mode) {
            return;
        }

        if (nextMode === 'html') {
            // Push latest visual markup into the source field before showing it.
            textarea.value = htmlFromQuill();
            mode = 'html';
        } else {
            // Load edited HTML back into Quill (paste path handles structure better than innerHTML alone).
            const html = textarea.value.trim();
            quill.setContents([]);
            if (html) {
                quill.clipboard.dangerouslyPasteHTML(html);
            }
            mode = 'visual';
            syncTextareaFromQuill();
        }

        group.classList.toggle('is-html-mode', mode === 'html');
        visualBtn.classList.toggle('is-active', mode === 'visual');
        htmlBtn.classList.toggle('is-active', mode === 'html');
        visualBtn.setAttribute('aria-pressed', mode === 'visual' ? 'true' : 'false');
        htmlBtn.setAttribute('aria-pressed', mode === 'html' ? 'true' : 'false');

        if (toolbarEl) {
            toolbarEl.hidden = mode === 'html';
        }
        if (containerEl) {
            containerEl.hidden = mode === 'html';
        }
    };

    visualBtn.addEventListener('click', () => applyMode('visual'));
    htmlBtn.addEventListener('click', () => applyMode('html'));

    quill.on('text-change', syncTextareaFromQuill);
    syncTextareaFromQuill();

    // Start in visual mode UI state.
    group.classList.remove('is-html-mode');
    visualBtn.setAttribute('aria-pressed', 'true');
    htmlBtn.setAttribute('aria-pressed', 'false');

    const form = textarea.closest('form');

    if (form) {
        form.addEventListener('submit', () => {
            if (mode === 'visual') {
                syncTextareaFromQuill();
            }
            // HTML mode already edits the textarea directly.
        });
    }

    window.descriptionEditor = {
        getPlainText: () => {
            if (mode === 'html') {
                const tmp = document.createElement('div');
                tmp.innerHTML = textarea.value;

                return (tmp.textContent || '').replace(/\u00a0/g, ' ').trim();
            }

            return quill.getText().replace(/\u00a0/g, ' ').trim();
        },
        getHtml: () => (mode === 'html' ? textarea.value : textarea.value),
        getMode: () => mode,
        setMode: applyMode,
        quill,
    };
})();
