/**
 * VALLEY VIEW UNIVERSITY — MASTHEAD BEHAVIOUR
 * ---------------------------------------------------------------------------
 * - Condensing sticky nav (CSS handles the pinning via a negative sticky
 *   offset; JS only measures that offset and flags the stuck state).
 * - Mega menus with hover intent, click/tap fallback and full keyboard support.
 * - Quick-links panel and search overlay.
 * - Off-canvas mobile drawer with accordion, scroll lock and focus trap.
 *
 * Vanilla, dependency-free, and safe to load in <head> with `defer`.
 */
(function () {
    'use strict';

    var HOVER_OPEN_DELAY = 90;
    var HOVER_CLOSE_DELAY = 220;
    var DESKTOP_MIN = 1024;

    var header, nav, drawer, scrim, mobilebar;
    var megaItems = [];
    var openTimer = null;
    var closeTimer = null;
    var lastFocused = null;

    /* ----------------------------------------------------------------- utils */

    function $(sel, root) {
        return (root || document).querySelector(sel);
    }

    function $$(sel, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(sel));
    }

    function isDesktop() {
        return window.innerWidth >= DESKTOP_MIN;
    }

    /* --------------------------------------------------- sticky measurements */

    /**
     * The header sticks with `top: -(utility strip height)` so it slides up
     * until only the logo/menu bar is left pinned. The strip's height changes
     * with the viewport, so the offset is measured rather than hard-coded.
     */
    function measureOffset() {
        if (!header) return 0;

        var utility = $('.vvu-utility', header);
        var offset = (utility && utility.offsetParent !== null) ? utility.offsetHeight : 0;

        // While stuck the strip is scrolled out of view but still measurable,
        // so the value stays stable across state changes.
        document.documentElement.style.setProperty('--vvu-scrolloff', offset + 'px');
        return offset;
    }

    var scrollOffset = 0;
    var ticking = false;

    function onScroll() {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(function () {
            ticking = false;
            var y = window.pageYOffset || document.documentElement.scrollTop || 0;

            if (header) {
                header.classList.toggle('is-stuck', isDesktop() && y > scrollOffset - 2);
            }
            if (mobilebar) {
                mobilebar.classList.toggle('is-stuck', y > 8);
            }

            // The nav slides up as the masthead condenses, which changes how
            // much room an open dropdown has beneath it.
            if (header && $('.vvu-nav__item--mega.is-open', header)) sizeMega();
        });
    }

    /* ------------------------------------------------------------ mega menus */

    function closeMega(item) {
        if (!item) return;
        item.classList.remove('is-open');
        var link = $('.vvu-nav__link', item);
        if (link) link.setAttribute('aria-expanded', 'false');
    }

    function closeAllMega(except) {
        megaItems.forEach(function (item) {
            if (item !== except) closeMega(item);
        });
    }

    /**
     * Caps the dropdown to the space between the nav and the bottom of the
     * window. Without this a long menu overflows the screen with no way to
     * reach its lower groups — the panel is anchored to the nav, so the page
     * scrollbar moves the whole header instead of revealing the overflow.
     */
    function sizeMega() {
        if (!nav) return;
        var available = window.innerHeight - nav.getBoundingClientRect().bottom - 16;
        document.documentElement.style.setProperty(
            '--vvu-mega-max', Math.max(240, Math.round(available)) + 'px');
    }

    function openMega(item) {
        if (!item) return;
        closeAllMega(item);
        closeSearch();
        sizeMega();
        item.classList.add('is-open');
        var link = $('.vvu-nav__link', item);
        if (link) link.setAttribute('aria-expanded', 'true');
    }

    function clearTimers() {
        window.clearTimeout(openTimer);
        window.clearTimeout(closeTimer);
    }

    function initMegaMenus() {
        megaItems = $$('.vvu-nav__item--mega', header);

        megaItems.forEach(function (item) {
            var link = $('.vvu-nav__link', item);
            if (!link) return;

            // Hover intent — only on real pointers, never on touch.
            item.addEventListener('mouseenter', function () {
                if (!isDesktop() || !window.matchMedia('(hover: hover)').matches) return;
                clearTimers();
                openTimer = window.setTimeout(function () { openMega(item); }, HOVER_OPEN_DELAY);
            });

            item.addEventListener('mouseleave', function () {
                if (!isDesktop() || !window.matchMedia('(hover: hover)').matches) return;
                clearTimers();
                closeTimer = window.setTimeout(function () { closeMega(item); }, HOVER_CLOSE_DELAY);
            });

            // Click/tap toggles. Placeholder hrefs never navigate.
            link.addEventListener('click', function (event) {
                var href = link.getAttribute('href') || '';
                var placeholder = !href || href === '#' || href.indexOf('javascript:') === 0;
                var open = item.classList.contains('is-open');

                if (placeholder) {
                    event.preventDefault();
                    clearTimers();
                    if (open) { closeMega(item); } else { openMega(item); }
                    return;
                }

                // Real destination: first tap opens the panel on touch devices,
                // a second tap follows the link.
                if (!window.matchMedia('(hover: hover)').matches && !open) {
                    event.preventDefault();
                    openMega(item);
                }
            });

            // Keyboard: Down opens and moves into the panel.
            link.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    openMega(item);
                    var first = $('a', $('.mm-pos', item));
                    if (first) first.focus();
                }
            });

            // Close once focus leaves the whole item.
            item.addEventListener('focusout', function (event) {
                if (!item.contains(event.relatedTarget)) closeMega(item);
            });
        });
    }

    /* ------------------------------------------------------------- search */

    function closeSearch() {
        var panel = $('#vvu-search');
        if (!panel) return;
        panel.classList.remove('is-open');
        $$('[data-vvu-toggle="search"]').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    function toggleSearch() {
        var panel = $('#vvu-search');
        if (!panel) return;
        var open = panel.classList.contains('is-open');

        closeAllMega();
        panel.classList.toggle('is-open', !open);
        $$('[data-vvu-toggle="search"]').forEach(function (btn) {
            btn.setAttribute('aria-expanded', String(!open));
        });

        if (!open) {
            var input = $('.vvu-search__input', panel);
            if (input) window.setTimeout(function () { input.focus(); }, 120);
        }
    }

    /* ------------------------------------------------------------- drawer */

    function focusables(root) {
        return $$('a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])', root)
            .filter(function (el) { return el.offsetParent !== null; });
    }

    function openDrawer() {
        if (!drawer) return;
        lastFocused = document.activeElement;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        if (scrim) scrim.classList.add('is-open');
        document.documentElement.classList.add('vvu-locked');

        $$('[data-vvu-toggle="drawer"]').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'true');
        });

        var first = focusables(drawer)[0];
        if (first) window.setTimeout(function () { first.focus(); }, 160);
    }

    function closeDrawer() {
        if (!drawer) return;
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        if (scrim) scrim.classList.remove('is-open');
        document.documentElement.classList.remove('vvu-locked');

        $$('[data-vvu-toggle="drawer"]').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });

        if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
    }

    function isDrawerOpen() {
        return !!drawer && drawer.classList.contains('is-open');
    }

    function trapFocus(event) {
        if (event.key !== 'Tab' || !isDrawerOpen()) return;
        var items = focusables(drawer);
        if (!items.length) return;

        var first = items[0];
        var last = items[items.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    function initAccordion() {
        $$('.vvu-acc__row > button[data-vvu-acc]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var li = btn.closest('li');
                if (!li) return;
                var open = li.classList.contains('is-open');

                // Accordion: only one branch expanded at a time.
                $$('.vvu-acc > li.is-open', drawer).forEach(function (other) {
                    if (other !== li) {
                        other.classList.remove('is-open');
                        var otherBtn = $('.vvu-acc__row > button', other);
                        if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                li.classList.toggle('is-open', !open);
                btn.setAttribute('aria-expanded', String(!open));
            });
        });
    }

    /* --------------------------------------------------------------- wiring */

    function init() {
        header = $('.vvu-header');
        nav = $('.vvu-nav');
        drawer = $('.vvu-drawer');
        scrim = $('.vvu-scrim');
        mobilebar = $('.vvu-mobilebar');

        if (!header) return;

        scrollOffset = measureOffset();
        initMegaMenus();
        initAccordion();

        // Toggle buttons
        document.addEventListener('click', function (event) {
            var target = event.target;
            if (!target || typeof target.closest !== 'function') return;

            var trigger = target.closest('[data-vvu-toggle]');

            if (trigger) {
                var what = trigger.getAttribute('data-vvu-toggle');
                if (what === 'search') { event.preventDefault(); toggleSearch(); return; }
                if (what === 'drawer') {
                    event.preventDefault();
                    if (isDrawerOpen()) { closeDrawer(); } else { openDrawer(); }
                    return;
                }
                if (what === 'drawer-close') { event.preventDefault(); closeDrawer(); return; }
                if (what === 'drawer-search') {
                    event.preventDefault();
                    openDrawer();
                    var field = $('.vvu-drawer__search .vvu-search__input', drawer);
                    if (field) window.setTimeout(function () { field.focus(); }, 420);
                    return;
                }
                if (what === 'search-close') { event.preventDefault(); closeSearch(); return; }
            }

            // Click-away for the desktop overlays.
            if (!target.closest('.vvu-search')) closeSearch();
            if (!target.closest('.vvu-nav__item--mega')) closeAllMega();
        });

        if (scrim) scrim.addEventListener('click', closeDrawer);

        // Escape closes whatever is open, innermost first.
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Tab') { trapFocus(event); return; }
            if (event.key !== 'Escape') return;

            if (isDrawerOpen()) { closeDrawer(); return; }

            var searchPanel = $('#vvu-search');
            if (searchPanel && searchPanel.classList.contains('is-open')) {
                closeSearch();
                var btn = $('[data-vvu-toggle="search"]');
                if (btn) btn.focus();
                return;
            }

            var openItem = $('.vvu-nav__item--mega.is-open', header);
            if (openItem) {
                closeMega(openItem);
                var link = $('.vvu-nav__link', openItem);
                if (link) link.focus();
            }
        });

        // Don't submit an empty search.
        $$('.vvu-search-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var input = $('.vvu-search__input', form);
                if (!input || !input.value.trim()) {
                    event.preventDefault();
                    if (input) input.focus();
                }
            });
        });

        window.addEventListener('scroll', onScroll, { passive: true });

        var resizeTimer = null;
        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                scrollOffset = measureOffset();
                sizeMega();
                if (isDesktop() && isDrawerOpen()) closeDrawer();
                if (!isDesktop()) { closeAllMega(); closeSearch(); }
                onScroll();
            }, 150);
        });

        // Fonts land after first paint and change the band heights.
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                scrollOffset = measureOffset();
                onScroll();
            });
        }

        onScroll();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
