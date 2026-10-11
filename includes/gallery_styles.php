<?php
/**
 * Shared gallery styling.
 *
 * Used by gallery.php, src_gallery.php and graduation_gallery.php. Kept in one place so the
 * gallery pages cannot drift apart visually.
 *
 * Included BEFORE the hero, because the hero uses .animate-slow-zoom and
 * .animate-fadeInUp from here.
 */
if (!defined('VVU_GALLERY_STYLES')) {
    define('VVU_GALLERY_STYLES', 1);
?>
<link rel="stylesheet" href="css/vvu-modern.css?v=1.0">
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

    /* ============================================================
       Gallery body — Mission & Vision style (css/vvu-modern.css)
       Navy #1e3a8a / #172554, gold #fbbf24 / #b45309, greys.
       Spans always get an explicit colour (the legacy theme paints
       every <span> grey).
       ============================================================ */
    .g-intro { padding-bottom: 24px; }
    .g-intro .vm-head { margin-bottom: 0; }
    .g-albums-section { padding-top: 72px; }

    .g-notice {
        display: flex; align-items: flex-start; gap: 10px; max-width: 760px; margin: 32px auto 0;
        padding: 14px 18px; border-radius: 14px; background: #fffbeb; border: 1px solid #fcd34d;
    }
    .g-notice .material-symbols-outlined { color: #b45309; }
    .g-notice__text { color: #374151; font-size: 16px; line-height: 1.5; }

    /* ---------------- Search + filter pills ---------------- */
    .g-search { margin-bottom: 22px; }
    .g-pills { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-bottom: 40px; }
    .g-pill {
        display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 999px;
        border: 1.5px solid #d6deeb; background: #fff; color: #1e3a8a; cursor: pointer;
        font-size: 15px; font-weight: 600; white-space: nowrap;
        transition: background-color .25s ease, color .25s ease, border-color .25s ease;
    }
    .g-pill .material-symbols-outlined { font-size: 19px; color: inherit; }
    .g-pill:hover { border-color: #1e3a8a; }
    .g-pill[aria-pressed="true"] { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }
    .g-pill__n {
        min-width: 24px; padding: 1px 8px; border-radius: 999px; text-align: center;
        font-size: 12px; font-weight: 700; color: inherit; background: rgba(30, 58, 138, .08);
    }
    .g-pill[aria-pressed="true"] .g-pill__n { background: rgba(255, 255, 255, .2); color: #fff; }
    .dark .g-pill { background: #1f2937; border-color: #374151; color: #e5e7eb; }

    /* ---------------- Album cards ---------------- */
    .g-albums { display: grid; gap: 24px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .g-card {
        position: relative; display: flex; flex-direction: column; overflow: hidden;
        border-radius: 20px; background: #fff; border: 1px solid #e5e7eb;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .g-card:hover, .g-card:focus-within { transform: translateY(-6px); box-shadow: 0 28px 50px -28px rgba(15, 23, 42, .5); }
    .dark .g-card { background: #1f2937; border-color: #374151; }

    .g-card__media {
        position: relative; display: block; height: 230px; overflow: hidden; background: #1e3a8a;
    }
    .g-card__media img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform .7s cubic-bezier(.2, .8, .2, 1);
    }
    .g-card:hover .g-card__media img { transform: scale(1.06); }
    .g-card__placeholder {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #1e3a8a, #172554);
    }
    .g-card__placeholder .material-symbols-outlined { font-size: 56px; color: rgba(255, 255, 255, .35); }
    .g-card__chip {
        position: absolute; left: 14px; top: 14px; padding: 5px 12px; border-radius: 999px;
        background: #1e3a8a; color: #fff; font-size: 12px; font-weight: 700;
        letter-spacing: .06em; text-transform: uppercase;
    }
    .g-card__count {
        position: absolute; right: 14px; top: 14px; display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 10px; border-radius: 999px; background: rgba(23, 37, 84, .72);
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    }
    .g-card__count .material-symbols-outlined { font-size: 16px; color: #fbbf24; }
    .g-card__count-n { color: #fff; font-size: 13px; font-weight: 700; }
    .g-card__hover {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        background: rgba(23, 37, 84, .35); opacity: 0; transition: opacity .3s ease;
    }
    .g-card__hover .material-symbols-outlined {
        width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        background: #fbbf24; color: #172554; font-size: 26px;
    }
    .g-card:hover .g-card__hover { opacity: 1; }

    .g-card__body { display: flex; flex-direction: column; flex: 1 1 auto; padding: 20px 22px 18px; }
    .g-card__date {
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px;
        color: #b45309; font-size: 13px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
    }
    .g-card__date .material-symbols-outlined { font-size: 17px; color: #b45309; }
    .g-card__title { margin-bottom: 16px; font-size: 19px; font-weight: 600; line-height: 1.35; }
    .g-card__title a {
        color: #1e3a8a; font-weight: 600; text-decoration: none;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .g-card__title a:hover { color: #172554; text-decoration: underline; text-underline-offset: 3px; }
    .dark .g-card__title a { color: #fff; }
    .g-card__text {
        margin: -8px 0 16px; color: #6b7280; font-size: 15px; line-height: 1.6; font-weight: 400;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .g-card__foot {
        display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;
        margin-top: auto; min-height: 61px; padding-top: 16px; border-top: 1px solid #eef1f6;
    }
    .g-card__link {
        display: inline-flex; align-items: center; gap: 6px; color: #1e3a8a; font-size: 15px; font-weight: 700;
        text-decoration: none !important;
    }
    .g-card__link .material-symbols-outlined { font-size: 19px; color: inherit; transition: transform .25s ease; }
    .g-card__link:hover .material-symbols-outlined { transform: translateX(4px); }
    .g-card__all {
        display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 999px;
        background: #fbbf24; color: #172554 !important; font-size: 14px; font-weight: 700;
        text-decoration: none !important; transition: background-color .25s ease;
    }
    .g-card__all .material-symbols-outlined { font-size: 17px; color: inherit; }
    .g-card__all:hover { background: #fcd34d; }
    .dark .g-card__link { color: #93c5fd; }

    /* ---------------- Album detail ---------------- */
    .g-detail { padding: 48px 0 56px; }
    .g-back {
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 32px; padding: 8px 16px;
        border-radius: 999px; border: 1.5px solid #d6deeb; color: #1e3a8a !important;
        font-size: 15px; font-weight: 600; text-decoration: none !important;
        transition: background-color .25s ease, color .25s ease, border-color .25s ease;
    }
    .g-back .material-symbols-outlined { font-size: 19px; color: inherit; }
    .g-back:hover { background: #1e3a8a; border-color: #1e3a8a; color: #fff !important; }
    .g-detail__grid { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 48px; align-items: center; }
    .g-detail__grid--solo { grid-template-columns: minmax(0, 1fr); max-width: 860px; }
    .g-detail__title { font-size: clamp(26px, 3.2vw, 38px); }
    .g-detail__lead { font-size: 17px; }
    .g-meta { list-style: none; margin: 22px 0 0; padding: 0; display: flex; flex-wrap: wrap; gap: 10px; }
    .g-meta li {
        display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 999px;
        background: rgba(30, 58, 138, .07);
    }
    .g-meta .material-symbols-outlined { font-size: 18px; color: #1e3a8a; }
    .g-meta__text { color: #1e3a8a; font-size: 14px; font-weight: 600; }

    .g-full {
        position: relative; overflow: hidden; padding: 30px 28px; border-radius: 22px;
        background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%); color: #fff;
        box-shadow: 0 24px 48px -28px rgba(23, 37, 84, .8);
    }
    .g-full::after {
        content: ""; position: absolute; width: 220px; height: 220px; right: -80px; top: -90px;
        border-radius: 50%; background: rgba(255, 255, 255, .06); pointer-events: none;
    }
    .g-full > * { position: relative; z-index: 1; }
    .g-full__icon {
        display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px;
        border-radius: 14px; background: rgba(251, 191, 36, .16);
    }
    .g-full__icon .material-symbols-outlined { font-size: 28px; color: #fbbf24; }
    .g-full__title {
        margin-top: 16px; color: #fff; font-size: 24px; line-height: 1.25;
        font-family: var(--vvu-title-font); font-weight: var(--vvu-title-weight);
    }
    .g-full__text { margin: 10px 0 22px; color: rgba(255, 255, 255, .82); font-size: 15.5px; line-height: 1.65; font-weight: 400; }

    .g-photos-section { padding: 56px 0 80px; }
    .g-photos { display: grid; gap: 14px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .g-more {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
        margin-top: 36px; padding: 22px 26px; border-radius: 20px; background: #fff; border: 1px solid #e5e7eb;
    }
    .g-more__text { margin: 0; color: #374151; font-size: 16.5px; font-weight: 400; }

    /* ---------------- Photo tiles (album detail) ---------------- */
    .g-photo {
        position: relative; display: block; width: 100%; padding: 0; border: 0;
        aspect-ratio: 1 / 1; overflow: hidden; border-radius: 16px;
        background: #e2e8f0; cursor: zoom-in;
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .dark .g-photo { background: #1e293b; }
    .g-photo img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s ease; }
    .g-photo:hover { transform: translateY(-4px); box-shadow: 0 18px 32px -18px rgba(23, 37, 84, .6); }
    .g-photo:hover img { transform: scale(1.06); }
    .g-photo__zoom {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        background: rgba(23, 37, 84, .4); opacity: 0; transition: opacity .25s ease;
    }
    .g-photo__zoom .material-symbols-outlined {
        width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        background: #fbbf24; color: #172554; font-size: 24px;
    }
    .g-photo:hover .g-photo__zoom, .g-photo:focus-visible .g-photo__zoom { opacity: 1; }
    .g-photo:focus-visible { outline: 3px solid #fbbf24; outline-offset: 3px; }

    @media (hover: none) {
        .g-card__hover { display: none; }
    }
    @media (max-width: 1024px) {
        .g-albums { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .g-photos { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .g-detail__grid { grid-template-columns: minmax(0, 1fr); gap: 28px; }
    }
    @media (max-width: 640px) {
        .g-albums { grid-template-columns: minmax(0, 1fr); }
        .g-photos { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .g-card__media { height: 210px; }
        .g-albums-section { padding-top: 56px; }
        .g-more { justify-content: center; text-align: center; }
    }

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
