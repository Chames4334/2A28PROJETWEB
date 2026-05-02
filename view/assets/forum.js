// assets/forum.js - Client-side JS validation (côté client)

document.addEventListener('DOMContentLoaded', function () {

    // ── SEARCH FILTER ──────────────────────────────────────────
    const searchInput = document.getElementById('forumSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('[data-searchable]').forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = (q === '' || text.includes(q)) ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('.sort-select').forEach(function (select) {
        select.addEventListener('change', function () {
            if (select.form) select.form.submit();
        });
    });

    const reportModal = document.getElementById('reportModal');
    const reportForm = document.getElementById('reportForm');
    const otherReasonWrap = document.getElementById('otherReasonWrap');
    const otherReason = document.getElementById('otherReason');
    const reportError = document.getElementById('reportError');

    function closeReportModal() {
        if (reportModal) reportModal.style.display = 'none';
    }

    document.querySelectorAll('.btn-report').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!reportModal || !reportForm) return;
            reportForm.reset();
            if (reportError) reportError.style.display = 'none';
            if (otherReasonWrap) otherReasonWrap.style.display = 'none';
            document.getElementById('reportTargetType').value = btn.getAttribute('data-report-type');
            document.getElementById('reportTargetId').value = btn.getAttribute('data-report-id');
            document.getElementById('reportPostId').value = btn.getAttribute('data-report-post');
            reportModal.style.display = 'flex';
        });
    });

    document.querySelectorAll('[name="reason"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (otherReasonWrap) otherReasonWrap.style.display = radio.value === 'Autre' ? 'block' : 'none';
        });
    });

    ['reportModalClose', 'reportCancel'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', closeReportModal);
    });

    if (reportModal) {
        reportModal.addEventListener('click', function (e) {
            if (e.target === reportModal) closeReportModal();
        });
    }

    if (reportForm) {
        reportForm.addEventListener('submit', function (e) {
            const selected = reportForm.querySelector('[name="reason"]:checked');
            let message = '';

            if (!selected) {
                message = 'Choisissez une raison.';
            } else if (selected.value === 'Autre' && (!otherReason || otherReason.value.trim().length < 5)) {
                message = 'Decrivez la raison en au moins 5 caracteres.';
            } else if (otherReason && otherReason.value.length > 500) {
                message = 'La precision ne peut pas depasser 500 caracteres.';
            }

            if (message) {
                e.preventDefault();
                if (reportError) {
                    reportError.textContent = message;
                    reportError.style.display = 'block';
                }
            }
        });
    }

    // ── CHAR COUNTER ───────────────────────────────────────────
    document.querySelectorAll('[data-maxlength]').forEach(function (el) {
        const max = parseInt(el.getAttribute('data-maxlength'));
        const counter = document.getElementById(el.id + '_count');
        function update() {
            const left = max - el.value.length;
            if (counter) {
                counter.textContent = left + ' caractères restants';
                counter.style.color = left < 20 ? '#d9534f' : '#8a9e6a';
            }
        }
        el.addEventListener('input', update);
        update();
    });

    // ── POST FORM VALIDATION ────────────────────────────────────
    const postForm = document.getElementById('postForm');
    if (postForm) {
        postForm.addEventListener('submit', function (e) {
            let valid = true;
            clearErrors(postForm);

            const titre = document.getElementById('titre');
            const contenu = document.getElementById('contenu');

            if (!titre || titre.value.trim().length < 5) {
                showError(titre, 'Le titre doit contenir au moins 5 caractères.');
                valid = false;
            } else if (titre.value.trim().length > 200) {
                showError(titre, 'Le titre ne peut pas dépasser 200 caractères.');
                valid = false;
            }

            if (!contenu || contenu.value.trim().length < 10) {
                showError(contenu, 'Le contenu doit contenir au moins 10 caractères.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ── REPLY FORM VALIDATION ───────────────────────────────────
    const replyForms = document.querySelectorAll('.reply-form');
    replyForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            clearErrors(form);
            const contenu = form.querySelector('[name="contenu"]');
            if (!contenu || contenu.value.trim().length < 3) {
                showError(contenu, 'La réponse doit contenir au moins 3 caractères.');
                e.preventDefault();
            }
        });
    });

    // ── DELETE CONFIRM ──────────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    // ── INLINE REPLY TOGGLE ─────────────────────────────────────
    document.querySelectorAll('.btn-reply-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-target');
            const target = document.getElementById(targetId);
            if (target) {
                const isHidden = target.style.display === 'none' || target.style.display === '';
                target.style.display = isHidden ? 'block' : 'none';
                if (isHidden) {
                    const ta = target.querySelector('textarea');
                    if (ta) ta.focus();
                }
            }
        });
    });

    // ── REACTION FORMS (no AJAX - server-side form submission) ────
    // Reactions are handled via form submission, no client-side AJAX needed

    // ── AUTO RESIZE TEXTAREA ────────────────────────────────────
    document.querySelectorAll('textarea.auto-resize').forEach(function (ta) {
        ta.addEventListener('input', function () {
            ta.style.height = 'auto';
            ta.style.height = ta.scrollHeight + 'px';
        });
    });

    // ── HELPERS ─────────────────────────────────────────────────
    function showError(el, msg) {
        if (!el) return;
        el.style.borderColor = '#d9534f';
        const span = document.createElement('span');
        span.className = 'error-msg js-error';
        span.textContent = msg;
        el.parentNode.appendChild(span);
    }

    function clearErrors(form) {
        form.querySelectorAll('.js-error').forEach(function (e) { e.remove(); });
        form.querySelectorAll('input, textarea, select').forEach(function (el) {
            el.style.borderColor = '';
        });
    }

});
