/* VVU Scholar — recommend without leaving the page (the form works without JS too). */
(function () {
    'use strict';
    document.querySelectorAll('form[data-recommend]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.fetch || !window.FormData) { return; }
            e.preventDefault();
            var btn = form.querySelector('button');
            btn.disabled = true;
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data && data.ok) {
                    btn.classList.toggle('is-on', !!data.recommended);
                    btn.setAttribute('aria-pressed', data.recommended ? 'true' : 'false');
                    form.querySelector('[data-recommend-label]').textContent = data.recommended ? 'Recommended' : 'Recommend';
                    form.querySelector('[data-recommend-count]').textContent = data.count;
                }
            }).catch(function () { form.submit(); })
              .then(function () { btn.disabled = false; });
        });
    });
})();
