/**
 * Valley View University - News & Events interactions
 * Reading progress bar + staggered scroll reveal.
 * Paired with css/news-modern.css.
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.addEventListener('DOMContentLoaded', function () {
        buildProgressBar();
        buildEditorialReveal();
        buildReveal();
    });

    /* Editorial layer: elements already carry .ed-rise in the markup,
       so they only need the .ed-in switch flipped as they scroll in. */
    function buildEditorialReveal() {
        var items = Array.prototype.slice.call(document.querySelectorAll('.ed-rise'));
        if (!items.length) return;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('ed-in'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                var row = items.indexOf(el);
                setTimeout(function () { el.classList.add('ed-in'); }, Math.min(row, 4) * 80);
                observer.unobserve(el);
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });

        items.forEach(function (el) { observer.observe(el); });
    }

    /* Reading progress — only on article/event detail pages */
    function buildProgressBar() {
        var body = document.querySelector('.article-body');
        if (!body) return;

        var bar = document.createElement('div');
        bar.className = 'nm-progress';
        bar.innerHTML = '<span></span>';
        document.body.appendChild(bar);

        var fill = bar.firstChild;

        function update() {
            var rect = body.getBoundingClientRect();
            var total = rect.height - window.innerHeight;
            var progress = total > 0 ? (-rect.top / total) : (rect.top < 0 ? 1 : 0);
            progress = Math.min(Math.max(progress, 0), 1);
            fill.style.width = (progress * 100) + '%';
        }

        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    }

    /* Fade content blocks in as they enter the viewport */
    function buildReveal() {
        var selectors = [
            '.article-featured-image',
            '.article-lead',
            '.article-tags',
            '.article-share',
            '.article-navigation',
            '.article-sidebar .sidebar-card',
            '.related-article-card',
            '.news-card'
        ];

        // The editorial layer handles its own reveal
        if (document.querySelector('.ed-rise')) {
            selectors = ['.related-article-card'];
        }

        var targets = [];
        selectors.forEach(function (sel) {
            targets = targets.concat(Array.prototype.slice.call(document.querySelectorAll(sel)));
        });

        if (!targets.length) return;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            return; // leave everything visible in its natural state
        }

        targets.forEach(function (el) { el.classList.add('nm-reveal'); });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry, i) {
                if (!entry.isIntersecting) return;
                var delay = Math.min(i, 5) * 70;
                setTimeout(function () {
                    entry.target.classList.add('nm-in');
                }, delay);
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -50px 0px' });

        targets.forEach(function (el) { observer.observe(el); });
    }
})();
