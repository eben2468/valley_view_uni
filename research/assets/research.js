/* ==========================================================================
   VVU SCHOLAR — portal behaviour
   --------------------------------------------------------------------------
   Progressive enhancement only. Every feature here has a working no-JS path:
   the search box is a real <form> that submits to publications.php, the stat
   tiles already contain their final number in the markup, and the bars are
   sized from a data attribute the CSS can ignore.
   ========================================================================== */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ----------------------------------------------------------------------
       Count-up on the stat tiles. The element's text is already correct; we
       only replay it from zero once the tile scrolls into view.
       ---------------------------------------------------------------------- */
    function countUp(el) {
        var target = parseFloat(el.getAttribute('data-count'));
        if (!isFinite(target) || reduceMotion) { return; }

        var suffix   = el.getAttribute('data-suffix') || '';
        var decimals = (el.getAttribute('data-decimals') | 0);
        var final    = el.textContent;
        var start    = null;
        var DURATION = 1400;

        function frame(ts) {
            if (start === null) { start = ts; }
            var p = Math.min((ts - start) / DURATION, 1);
            // easeOutExpo — fast out of the gate, settles on the real figure.
            var eased = p === 1 ? 1 : 1 - Math.pow(2, -10 * p);
            if (p < 1) {
                el.textContent = formatNumber(target * eased, decimals) + suffix;
                requestAnimationFrame(frame);
            } else {
                el.textContent = final; // restore the server's exact rendering
            }
        }
        el.textContent = formatNumber(0, decimals) + suffix;
        requestAnimationFrame(frame);
    }

    function formatNumber(n, decimals) {
        if (decimals > 0) { return n.toFixed(decimals); }
        return Math.round(n).toLocaleString('en-US');
    }

    /* ----------------------------------------------------------------------
       Reveal-on-scroll, bar fills and counters share one observer.
       ---------------------------------------------------------------------- */
    function observe() {
        var targets = document.querySelectorAll('.vvus-reveal, [data-count], [data-bar]');
        if (!targets.length) { return; }

        if (!('IntersectionObserver' in window)) {
            Array.prototype.forEach.call(targets, activate);
            return;
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) { return; }
                activate(entry.target);
                io.unobserve(entry.target);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        Array.prototype.forEach.call(targets, function (t) { io.observe(t); });
    }

    function activate(el) {
        el.classList.add('is-in');
        if (el.hasAttribute('data-count')) { countUp(el); }
        if (el.hasAttribute('data-bar')) {
            var fill = el.querySelector('i');
            if (fill) { fill.style.width = el.getAttribute('data-bar') + '%'; }
        }
    }

    /* ----------------------------------------------------------------------
       Live search. Queries research/api.php and shows grouped suggestions;
       Enter with nothing highlighted still submits the form as normal.
       ---------------------------------------------------------------------- */
    function liveSearch(root) {
        var input   = root.querySelector('.vvus-search__input');
        var panel   = root.querySelector('.vvus-search__results');
        var api     = root.getAttribute('data-api') || 'api.php';
        if (!input || !panel) { return; }

        var timer   = null;
        var cursor  = -1;
        var lastQ   = '';
        var controller = null;

        function close() {
            panel.hidden = true;
            panel.innerHTML = '';
            cursor = -1;
        }

        function hits() { return panel.querySelectorAll('.vvus-search__hit'); }

        function move(delta) {
            var items = hits();
            if (!items.length) { return; }
            if (cursor > -1) { items[cursor].classList.remove('is-cursor'); }
            cursor = (cursor + delta + items.length) % items.length;
            items[cursor].classList.add('is-cursor');
            items[cursor].scrollIntoView({ block: 'nearest' });
        }

        function render(data) {
            var html = '';
            (data.groups || []).forEach(function (group) {
                if (!group.items || !group.items.length) { return; }
                html += '<div class="vvus-search__group">' + esc(group.label) + '</div>';
                group.items.forEach(function (item) {
                    html += '<a class="vvus-search__hit" href="' + esc(item.url) + '">' +
                        '<span class="vvus-search__hit-icon"><i class="' + esc(item.icon) + '"></i></span>' +
                        '<span><strong>' + esc(item.title) + '</strong>' +
                        (item.meta ? '<small>' + esc(item.meta) + '</small>' : '') +
                        '</span></a>';
                });
            });

            if (!html) {
                html = '<p class="vvus-search__empty">Nothing matched &ldquo;' + esc(input.value) +
                    '&rdquo;. Try a surname, a topic, or part of a paper title.</p>';
            }
            panel.innerHTML = html;
            panel.hidden = false;
            cursor = -1;
        }

        function query() {
            var q = input.value.trim();
            if (q === lastQ) { return; }
            lastQ = q;

            if (q.length < 2) { close(); return; }

            // Abandon the previous request so a slow one cannot overwrite a
            // newer, more specific result set.
            if (controller) { controller.abort(); }
            controller = ('AbortController' in window) ? new AbortController() : null;

            fetch(api + '?action=suggest&q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller ? controller.signal : undefined
            })
                .then(function (r) { return r.ok ? r.json() : Promise.reject(r.status); })
                .then(render)
                .catch(function (err) {
                    if (err && err.name === 'AbortError') { return; }
                    close(); // the form still works — let it fall back to that
                });
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(query, 220);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
            else if (e.key === 'Escape') { close(); }
            else if (e.key === 'Enter' && cursor > -1) {
                e.preventDefault();
                hits()[cursor].click();
            }
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length >= 2 && panel.innerHTML) { panel.hidden = false; }
        });

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) { close(); }
        });
    }

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /* ----------------------------------------------------------------------
       Copy-to-clipboard for citations.
       ---------------------------------------------------------------------- */
    var toastTimer = null;
    function toast(message) {
        var el = document.querySelector('.vvus-toast');
        if (!el) {
            el = document.createElement('div');
            el.className = 'vvus-toast';
            document.body.appendChild(el);
        }
        el.textContent = message;
        el.classList.add('is-on');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { el.classList.remove('is-on'); }, 2200);
    }

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        // http:// on the campus network has no async clipboard — fall back.
        return new Promise(function (resolve, reject) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy') ? resolve() : reject();
            } catch (e) {
                reject(e);
            } finally {
                document.body.removeChild(ta);
            }
        });
    }

    function wireCopy() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest ? e.target.closest('[data-copy]') : null;
            if (!btn) { return; }
            e.preventDefault();

            var source = btn.getAttribute('data-copy');
            var text = source.charAt(0) === '#'
                ? (document.querySelector(source) || {}).textContent
                : source;
            if (!text) { return; }

            copyText(text.trim())
                .then(function () { toast(btn.getAttribute('data-copied') || 'Copied to clipboard'); })
                .catch(function () { toast('Press Ctrl+C to copy'); });
        });
    }

    /* ----------------------------------------------------------------------
       Filter bars submit on change, so a visitor never has to hunt for a
       button. The submit button stays in the markup for the no-JS path.
       ---------------------------------------------------------------------- */
    function wireAutoSubmit() {
        document.querySelectorAll('form[data-autosubmit] select').forEach(function (select) {
            select.addEventListener('change', function () { select.form.submit(); });
        });
    }

    /* ----------------------------------------------------------------------
       Trend chart. Chart.js is loaded by the page only when there is data.
       ---------------------------------------------------------------------- */
    function drawTrend() {
        var canvas = document.getElementById('vvusTrendChart');
        if (!canvas || typeof Chart === 'undefined') { return; }

        var data;
        try {
            data = JSON.parse(canvas.getAttribute('data-series') || '[]');
        } catch (e) {
            return;
        }
        if (!data.length) { return; }

        var dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var ink  = dark ? '#93a1bb' : '#64748b';
        var grid = dark ? 'rgba(255,255,255,.07)' : 'rgba(15,23,42,.07)';

        new Chart(canvas.getContext('2d'), {
            data: {
                labels: data.map(function (d) { return d.year; }),
                datasets: [
                    {
                        type: 'bar',
                        label: 'Publications',
                        data: data.map(function (d) { return d.publications; }),
                        backgroundColor: dark ? 'rgba(147,180,253,.55)' : 'rgba(29,78,216,.75)',
                        borderRadius: 5,
                        maxBarThickness: 34,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Citations',
                        data: data.map(function (d) { return d.citations; }),
                        borderColor: dark ? '#e9b833' : '#a16207',
                        backgroundColor: dark ? 'rgba(233,184,51,.14)' : 'rgba(161,98,7,.10)',
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: dark ? '#e9b833' : '#a16207',
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                animation: reduceMotion ? false : { duration: 900 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: dark ? '#1b2a47' : '#0b2350',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Open Sans', size: 13, weight: '700' },
                        bodyFont: { family: 'Open Sans', size: 12.5 },
                        displayColors: true,
                        boxPadding: 4
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: ink, font: { family: 'Open Sans', size: 11.5 } } },
                    y: {
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: grid, drawBorder: false },
                        ticks: { color: ink, font: { family: 'Open Sans', size: 11.5 }, precision: 0 },
                        title: { display: true, text: 'Publications', color: ink, font: { family: 'Open Sans', size: 11, weight: '700' } }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { color: ink, font: { family: 'Open Sans', size: 11.5 }, precision: 0 },
                        title: { display: true, text: 'Citations', color: ink, font: { family: 'Open Sans', size: 11, weight: '700' } }
                    }
                }
            }
        });
    }

    /* ----------------------------------------------------------------------
       Abstract toggles on the publication list.
       ---------------------------------------------------------------------- */
    function wireToggles() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest ? e.target.closest('[data-toggle-target]') : null;
            if (!btn) { return; }
            e.preventDefault();
            var target = document.querySelector(btn.getAttribute('data-toggle-target'));
            if (!target) { return; }
            var open = target.hasAttribute('hidden');
            if (open) { target.removeAttribute('hidden'); } else { target.setAttribute('hidden', ''); }
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            var label = btn.querySelector('[data-toggle-label]');
            if (label) {
                label.textContent = open
                    ? (btn.getAttribute('data-label-open') || 'Hide abstract')
                    : (btn.getAttribute('data-label-closed') || 'Abstract');
            }
        });
    }

    /* ---------------------------------------------------------------------- */
    function init() {
        observe();
        wireCopy();
        wireAutoSubmit();
        wireToggles();
        drawTrend();
        document.querySelectorAll('.vvus-search').forEach(liveSearch);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
