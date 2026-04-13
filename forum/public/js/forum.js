/**
 * Comportements légers côté client (pas de fetch / XHR).
 */
(function () {
    'use strict';

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var msg = form.getAttribute('data-confirm');
            if (msg && !window.confirm(msg)) {
                e.preventDefault();
            }
        });
    });
})();
