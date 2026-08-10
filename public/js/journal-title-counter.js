(() => {
    const input = document.getElementById('title');
    const counter = document.getElementById('title-char-count');

    if (!input || !counter) {
        return;
    }

    const max = input.maxLength;

    const update = () => {
        counter.textContent = `${input.value.length} / ${max}`;
        counter.classList.toggle('form-char-count--limit', input.value.length >= max);
    };

    input.addEventListener('input', update);
    update();
})();
