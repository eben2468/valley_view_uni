<?php
/**
 * Shared gallery styling.
 *
 * Used by gallery.php and src_gallery.php. Kept in one place so the two
 * gallery pages cannot drift apart visually.
 *
 * Included BEFORE the hero, because the hero uses .animate-slow-zoom and
 * .animate-fadeInUp from here.
 */
if (!defined('VVU_GALLERY_STYLES')) {
    define('VVU_GALLERY_STYLES', 1);
?>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .text-gradient {
        background: linear-gradient(to right, #2563eb, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ---------------- Album + photo cards ---------------- */
    .g-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        background: #0f172a;
        transition: transform .35s cubic-bezier(.2,.8,.2,1), box-shadow .35s ease;
        box-shadow: 0 10px 30px -12px rgba(15, 23, 42, .35);
    }
    .g-card:hover,
    .g-card:focus-within {
        transform: translateY(-6px);
        box-shadow: 0 26px 50px -18px rgba(15, 23, 42, .55);
    }
    .g-card__media { position: relative; aspect-ratio: 4 / 3; overflow: hidden; background: #1e293b; }
    .g-card__media img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform .6s cubic-bezier(.2,.8,.2,1);
    }
    .g-card:hover .g-card__media img,
    .g-card:focus-within .g-card__media img { transform: scale(1.07); }
    .g-card__scrim {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(2,6,23,.95) 0%, rgba(2,6,23,.55) 42%, rgba(2,6,23,.05) 75%, transparent 100%);
    }
    .g-card__body { position: absolute; inset-inline: 0; bottom: 0; padding: 1.15rem 1.25rem 1.25rem; }
    .g-card__title {
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; line-height: 1.25;
    }
    .g-card__chip {
        position: absolute; top: .85rem; left: .85rem;
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .35rem .75rem; border-radius: 999px;
        font-size: .75rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
        color: #0b2447; background: rgba(251, 191, 36, .95);
        box-shadow: 0 4px 14px rgba(0,0,0,.25);
    }
    .g-card__count {
        position: absolute; top: .85rem; right: .85rem;
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .35rem .7rem; border-radius: 999px;
        font-size: .75rem; font-weight: 700; color: #fff;
        background: rgba(2, 6, 23, .6);
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    }
    .g-card__cta {
        display: inline-flex; align-items: center; gap: .3rem;
        margin-top: .6rem; font-weight: 800; color: #fbbf24;
        opacity: 0; transform: translateY(6px);
        transition: opacity .3s ease, transform .3s ease;
    }
    .g-card:hover .g-card__cta,
    .g-card:focus-within .g-card__cta { opacity: 1; transform: translateY(0); }
    /* Touch devices have no hover, so never hide the affordance there. */
    @media (hover: none) {
        .g-card__cta { opacity: 1; transform: none; }
    }

    /* ---------------- Filter pills ---------------- */
    .g-pill {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .7rem 1.35rem; border-radius: 999px;
        font-weight: 700; font-size: 1rem; white-space: nowrap;
        border: 2px solid rgb(226 232 240); background: #fff; color: rgb(15 23 42);
        transition: all .2s ease; cursor: pointer;
    }
    .dark .g-pill { background: rgb(31 41 55); border-color: rgb(55 65 81); color: #fff; }
    .g-pill:hover { border-color: #2563eb; color: #2563eb; }
    .dark .g-pill:hover { color: #93c5fd; }
    .g-pill[aria-pressed="true"] {
        background: #1d4ed8; border-color: #1d4ed8; color: #fff;
        box-shadow: 0 10px 22px -10px rgba(29, 78, 216, .8);
    }
    .dark .g-pill[aria-pressed="true"] { background: #2563eb; border-color: #2563eb; color: #fff; }
    .g-pill__n {
        font-size: .75rem; font-weight: 800; padding: .1rem .5rem; border-radius: 999px;
        background: rgba(15, 23, 42, .08);
    }
    .g-pill[aria-pressed="true"] .g-pill__n { background: rgba(255,255,255,.22); }
    .dark .g-pill__n { background: rgba(255,255,255,.12); }

    /* ---------------- Photo grid (album detail) ---------------- */
    .g-photo {
        position: relative; display: block; width: 100%;
        aspect-ratio: 1 / 1; overflow: hidden; border-radius: 1rem;
        background: #e2e8f0; cursor: zoom-in;
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .dark .g-photo { background: #1e293b; }
    .g-photo img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s ease; }
    .g-photo:hover { transform: translateY(-4px); box-shadow: 0 18px 32px -18px rgba(2,6,23,.6); }
    .g-photo:hover img { transform: scale(1.08); }
    .g-photo__zoom {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        background: rgba(2, 6, 23, .45); color: #fff; opacity: 0; transition: opacity .25s ease;
    }
    .g-photo:hover .g-photo__zoom, .g-photo:focus-visible .g-photo__zoom { opacity: 1; }
    .g-photo:focus-visible { outline: 3px solid #fbbf24; outline-offset: 3px; }

    /* ---------------- Lightbox ---------------- */
    .g-lb {
        position: fixed; inset: 0; z-index: 9999; display: none;
        background: rgba(2, 6, 23, .96);
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    }
    .g-lb.is-open { display: flex; flex-direction: column; }
    .g-lb__bar {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: .9rem 1.1rem; color: #e2e8f0; flex: 0 0 auto;
    }
    .g-lb__stage {
        flex: 1 1 auto; position: relative; display: flex; align-items: center; justify-content: center;
        padding: 0 .5rem 1rem; min-height: 0;
    }
    .g-lb__img {
        max-width: 100%; max-height: 100%; object-fit: contain;
        border-radius: .5rem; box-shadow: 0 30px 60px -20px rgba(0,0,0,.8);
    }
    .g-lb__nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 3rem; height: 3rem; border-radius: 999px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.12); color: #fff; border: 0; cursor: pointer;
        transition: background .2s ease;
    }
    .g-lb__nav:hover { background: rgba(255,255,255,.28); }
    .g-lb__nav--prev { left: .75rem; }
    .g-lb__nav--next { right: .75rem; }
    .g-lb__cap {
        flex: 0 0 auto; text-align: center; color: #cbd5e1;
        padding: 0 1.25rem 1.25rem; font-size: .95rem; min-height: 1.5rem;
    }
    .g-lb__close {
        width: 2.75rem; height: 2.75rem; border-radius: 999px; border: 0; cursor: pointer;
        background: rgba(255,255,255,.12); color: #fff;
        display: flex; align-items: center; justify-content: center;
    }
    .g-lb__close:hover { background: rgba(255,255,255,.28); }
    .g-lb__spin {
        position: absolute; width: 2.5rem; height: 2.5rem; border-radius: 999px;
        border: 3px solid rgba(255,255,255,.25); border-top-color: #fbbf24;
        animation: g-spin .8s linear infinite; display: none;
    }
    .g-lb.is-loading .g-lb__spin { display: block; }
    @keyframes g-spin { to { transform: rotate(360deg); } }
    body.g-lb-open { overflow: hidden; }

    @media (max-width: 640px) {
        .g-lb__nav { width: 2.5rem; height: 2.5rem; }
        .g-lb__nav--prev { left: .25rem; }
        .g-lb__nav--next { right: .25rem; }
    }

    /* Respect a reduced-motion preference for every decorative animation here. */
    @media (prefers-reduced-motion: reduce) {
        .animate-slow-zoom, .animate-fadeInUp, .animate-float { animation: none !important; }
        .g-card, .g-card__media img, .g-photo, .g-photo img, .g-card__cta { transition: none !important; }
    }
</style>
<?php } ?>
