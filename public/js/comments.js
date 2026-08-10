(function () {
    function closeReplyForms() {
        document.querySelectorAll('.comment-reply-form').forEach(function (form) {
            form.hidden = true;
        });

        document.querySelectorAll('.comment-reply-btn').forEach(function (button) {
            button.classList.remove('is-active');
            button.setAttribute('aria-expanded', 'false');
        });
    }

    document.querySelectorAll('.comment-reply-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var targetId = button.getAttribute('data-reply-target');
            var form = targetId ? document.getElementById(targetId) : null;

            if (!form) {
                return;
            }

            var isOpen = !form.hidden && button.classList.contains('is-active');

            closeReplyForms();

            if (!isOpen) {
                form.hidden = false;
                button.classList.add('is-active');
                button.setAttribute('aria-expanded', 'true');

                var textarea = form.querySelector('textarea');

                if (textarea) {
                    textarea.focus();
                }
            }
        });
    });

    document.querySelectorAll('.comment-reply-cancel').forEach(function (button) {
        button.addEventListener('click', function () {
            closeReplyForms();
        });
    });

    document.querySelectorAll('.comment-reply-form').forEach(function (form) {
        if (!form.hasAttribute('hidden') && form.querySelector('textarea')?.value) {
            form.hidden = false;

            var commentId = form.id.replace('reply-form-', '');
            var toggle = document.querySelector('[data-reply-target="reply-form-' + commentId + '"]');

            if (toggle) {
                toggle.classList.add('is-active');
                toggle.setAttribute('aria-expanded', 'true');
            }
        }
    });
})();