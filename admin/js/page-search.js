/**
 * Admin page search.
 *
 * Finding a page meant knowing which of the 26 sidebar managers owned it —
 * and most editable pages ("Work Study", "Entry Requirements", …) are tabs
 * inside those managers, so they are not in the sidebar at all. This searches
 * every destination at once, including the nested ones.
 *
 * Open with the search box, Ctrl+K (Cmd+K on Mac), or "/".
 * Navigate with ↑/↓, open with Enter, dismiss with Escape.
 *
 * The index is injected by admin/header.php as window.VVU_PAGE_INDEX.
 */
(function () {
    'use strict';

    var pages = window.VVU_PAGE_INDEX || [];
    if (!pages.length) return;

    var MAX_RESULTS = 12;
    var overlay, input, list, results = [], activeIndex = 0;

    /**
     * Scores a page against the query. Returns -1 for no match.
     *
     * Ranking favours, in order: exact title, title prefix, word-start inside
     * the title, any title substring, then parent/keyword hits. This keeps
     * "work" showing "Work Study Program" above pages that merely mention it.
     */
    function score(page, query) {
        var title = page.title.toLowerCase();
        var parent = (page.parent || '').toLowerCase();
        var keywords = (page.keywords || '').toLowerCase();

        if (title === query) return 1000;
        if (title.indexOf(query) === 0) return 900 - title.length;

        // Word-start match, e.g. "study" inside "Work Study Program"
        var words = title.split(/[^a-z0-9]+/);
        for (var i = 0; i < words.length; i++) {
            if (words[i].indexOf(query) === 0) return 800 - title.length;
        }

        if (title.indexOf(query) !== -1) return 700 - title.length;
        if (parent.indexOf(query) !== -1) return 500 - title.length;
        if (keywords.indexOf(query) !== -1) return 400 - title.length;

        // Subsequence match so "wsp" still finds "Work Study Program"
        var t = 0;
        for (var c = 0; c < query.length; c++) {
            t = title.indexOf(query[c], t);
            if (t === -1) return -1;
            t++;
        }
        return 200 - title.length;
    }

    function search(query) {
        query = query.trim().toLowerCase();

        if (!query) {
            return pages.slice(0, MAX_RESULTS);
        }

        var scored = [];
        for (var i = 0; i < pages.length; i++) {
            var s = score(pages[i], query);
            if (s >= 0) scored.push({ page: pages[i], score: s });
        }

        scored.sort(function (a, b) { return b.score - a.score; });
        return scored.slice(0, MAX_RESULTS).map(function (r) { return r.page; });
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /** Wraps the matched span of the title so the reason for the hit is visible. */
    function highlight(title, query) {
        query = query.trim().toLowerCase();
        if (!query) return escapeHtml(title);

        var at = title.toLowerCase().indexOf(query);
        if (at === -1) return escapeHtml(title);

        return escapeHtml(title.slice(0, at)) +
               '<mark class="vvu-search-mark">' + escapeHtml(title.slice(at, at + query.length)) + '</mark>' +
               escapeHtml(title.slice(at + query.length));
    }

    function render() {
        var query = input.value;

        if (!results.length) {
            list.innerHTML = '<div class="vvu-search-empty">No pages match “' +
                             escapeHtml(query) + '”.</div>';
            return;
        }

        list.innerHTML = results.map(function (page, i) {
            return '<a class="vvu-search-item' + (i === activeIndex ? ' is-active' : '') + '" ' +
                   'href="' + escapeHtml(page.url) + '" data-index="' + i + '">' +
                   '<i class="fas ' + escapeHtml(page.icon || 'fa-file-lines') + '"></i>' +
                   '<span class="vvu-search-text">' +
                   '<span class="vvu-search-title">' + highlight(page.title, query) + '</span>' +
                   (page.parent
                       ? '<span class="vvu-search-parent">' + escapeHtml(page.parent) + '</span>'
                       : '') +
                   '</span>' +
                   '<i class="fas fa-arrow-right vvu-search-go"></i>' +
                   '</a>';
        }).join('');
    }

    function update() {
        results = search(input.value);
        activeIndex = 0;
        render();
    }

    function open() {
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        input.value = '';
        update();
        input.focus();
    }

    function close() {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function move(delta) {
        if (!results.length) return;
        activeIndex = (activeIndex + delta + results.length) % results.length;
        render();

        var active = list.querySelector('.is-active');
        if (active && active.scrollIntoView) {
            active.scrollIntoView({ block: 'nearest' });
        }
    }

    function build() {
        overlay = document.createElement('div');
        overlay.className = 'vvu-search-overlay';
        overlay.innerHTML =
            '<div class="vvu-search-panel" role="dialog" aria-modal="true" aria-label="Search admin pages">' +
              '<div class="vvu-search-head">' +
                '<i class="fas fa-magnifying-glass"></i>' +
                '<input type="text" class="vvu-search-input" placeholder="Search pages… (e.g. work study, fees, radio)" ' +
                       'autocomplete="off" spellcheck="false" aria-label="Search admin pages">' +
                '<button type="button" class="vvu-search-close" aria-label="Close search">Esc</button>' +
              '</div>' +
              '<div class="vvu-search-results"></div>' +
              '<div class="vvu-search-foot">' +
                '<span><kbd>↑</kbd><kbd>↓</kbd> navigate</span>' +
                '<span><kbd>Enter</kbd> open</span>' +
                '<span><kbd>Esc</kbd> close</span>' +
              '</div>' +
            '</div>';

        document.body.appendChild(overlay);
        input = overlay.querySelector('.vvu-search-input');
        list  = overlay.querySelector('.vvu-search-results');

        input.addEventListener('input', update);

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) close();
        });
        overlay.querySelector('.vvu-search-close').addEventListener('click', close);

        // Hovering syncs the highlight so mouse and keyboard agree
        list.addEventListener('mousemove', function (e) {
            var item = e.target.closest('.vvu-search-item');
            if (!item) return;
            var i = parseInt(item.dataset.index, 10);
            if (i !== activeIndex) { activeIndex = i; render(); }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown')      { e.preventDefault(); move(1); }
            else if (e.key === 'ArrowUp')   { e.preventDefault(); move(-1); }
            else if (e.key === 'Enter')     {
                e.preventDefault();
                if (results[activeIndex]) window.location.href = results[activeIndex].url;
            }
            else if (e.key === 'Escape')    { e.preventDefault(); close(); }
        });
    }

    document.addEventListener('keydown', function (e) {
        var tag = (e.target.tagName || '').toLowerCase();
        var typing = tag === 'input' || tag === 'textarea' || tag === 'select' || e.target.isContentEditable;

        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            overlay.classList.contains('is-open') ? close() : open();
            return;
        }

        // "/" is a convenient shortcut, but must not hijack real typing
        if (e.key === '/' && !typing && !overlay.classList.contains('is-open')) {
            e.preventDefault();
            open();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            build();
            wireTriggers();
        });
    } else {
        build();
        wireTriggers();
    }

    function wireTriggers() {
        var triggers = document.querySelectorAll('[data-vvu-search-trigger]');
        Array.prototype.forEach.call(triggers, function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                open();
            });
        });
    }
})();
