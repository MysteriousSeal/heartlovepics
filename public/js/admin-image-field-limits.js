(function () {
    const fields = [
        { inputId: 'title', counterId: 'title-char-count', max: 255 },
        { inputId: 'alt_text', counterId: 'alt-text-char-count', max: 125 },
    ];

    fields.forEach(function (field) {
        const input = document.getElementById(field.inputId);
        const counter = document.getElementById(field.counterId);

        if (!input || !counter) {
            return;
        }

        function updateCounter() {
            const length = input.value.length;
            counter.textContent = `${length} / ${field.max}`;
            counter.classList.toggle('form-char-count--limit', length >= field.max);
        }

        input.addEventListener('input', updateCounter);
        updateCounter();
    });
})();