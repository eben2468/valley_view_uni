/* VVU Scholar — researcher account pages: list filter, select-all, busy buttons. */
(function () {
    'use strict';

    // Filter "My publications" as you type.
    document.querySelectorAll('[data-filter]').forEach(function (input) {
        var list = document.querySelector(input.getAttribute('data-filter'));
        if (!list) return;
        var rows = Array.prototype.slice.call(list.children);
        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            rows.forEach(function (row) {
                row.style.display = (q === '' || (row.getAttribute('data-text') || '').indexOf(q) !== -1) ? '' : 'none';
            });
        });
    });

    // "Select all" on the import preview.
    document.querySelectorAll('[data-checkall]').forEach(function (box) {
        var name = box.getAttribute('data-checkall');
        box.addEventListener('change', function () {
            var form = box.form || document;
            form.querySelectorAll('input[name="' + name + '"]:not(:disabled)').forEach(function (c) {
                c.checked = box.checked;
            });
        });
    });

    // Slow server round-trips (ORCID, Crossref): say so, and block double submits.
    document.querySelectorAll('.ra-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.sent) { e.preventDefault(); return; }
            form.dataset.sent = '1';
            var btn = e.submitter || form.querySelector('button[type="submit"]');
            if (btn) {
                btn.setAttribute('aria-busy', 'true');
                btn.style.opacity = '.7';
                if (btn.dataset.busy) { btn.textContent = btn.dataset.busy; }
            }
        });
    });

    // Coming back with the browser's Back button restores the page from cache
    // with the busy state still on; clear it so the form can be used again.
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('.ra-form').forEach(function (form) {
            delete form.dataset.sent;
            form.querySelectorAll('[aria-busy]').forEach(function (b) { b.removeAttribute('aria-busy'); b.style.opacity = ''; });
        });
    });
})();
