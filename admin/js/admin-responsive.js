/**
 * Admin responsive helpers.
 *
 * The panel shell (sidebar, overlay, hamburger) is already responsive in
 * admin-styles.css. What was missing was the page content, and two of those
 * problems cannot be solved in a stylesheet alone:
 *
 *   1. Wide data tables need a scroll container, and CSS cannot wrap an
 *      element in a new parent.
 *   2. Several managers declare fixed grids (grid-template-columns:
 *      repeat(4, 1fr)) inside a page-level <style> block, which loads after
 *      the admin stylesheets and therefore wins. Tagging those grids here
 *      lets admin-responsive.css collapse them on small screens.
 *
 * Loaded with defer from admin/header.php, so it runs on every admin page
 * whether or not that page includes footer.php.
 */
(function () {
    'use strict';

    function eachRoot(callback) {
        var roots = document.querySelectorAll('.main-content, .sb2-2');
        Array.prototype.forEach.call(roots, callback);
    }

    /** Give every unwrapped table its own horizontal scroll box. */
    function wrapTables(root) {
        var tables = root.querySelectorAll('table');

        Array.prototype.forEach.call(tables, function (table) {
            if (table.closest('.table-responsive, .vvu-scroll-x')) {
                return; // already handled by Bootstrap or by an earlier pass
            }

            var box = document.createElement('div');
            box.className = 'vvu-scroll-x';
            table.parentNode.insertBefore(box, table);
            box.appendChild(table);
        });
    }

    /**
     * Tag grids that are laid out in more than one column.
     *
     * getComputedStyle reports the tracks actually in use, so a grid that is
     * already fluid (auto-fit) reports one track on a narrow screen and is
     * left alone.
     */
    function tagGrids(root) {
        var candidates = root.querySelectorAll('div, section, ul, ol, form, aside, main');

        Array.prototype.forEach.call(candidates, function (el) {
            var style = window.getComputedStyle(el);

            if (style.display !== 'grid' && style.display !== 'inline-grid') {
                return;
            }

            var tracks = (style.gridTemplateColumns || '').split(/\s+/).filter(Boolean);
            if (tracks.length > 1) {
                el.classList.add('vvu-grid-fluid');
            }
        });
    }

    eachRoot(function (root) {
        wrapTables(root);
        tagGrids(root);
    });
})();
