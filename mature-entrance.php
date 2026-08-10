<?php
$page_title = "Mature Entrance Examination - Valley View University";
$active_page = "admissions";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'mature_entrance'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'mature_entrance' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_raw as $s) {
    $sections[$s['section_key']] = $s;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'mature_entrance' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($items_raw as $i) {
    $items[$i['section_key']][] = $i;
}
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
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(251, 191, 36, 0.3); }
        50% { box-shadow: 0 0 40px rgba(251, 191, 36, 0.5); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
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
    .program-card {
        transition: all 0.3s ease;
    }
    .program-card:hover {
        transform: translateY(-10px);
    }
    .session-card {
        transition: all 0.3s ease;
    }
    .session-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* ======================================================================
       MATURE ENTRANCE — CONTENT AREA (redesign)
       Scoped under .mte-scope so nothing leaks into the hero / globals.
       Note: bootstrap.css sets html{font-size:10px}, so this block uses px.
       ====================================================================== */
    .mte-scope {
        --mte-ink:       #0f172a;
        --mte-ink-soft:  #475569;
        --mte-ink-muted: #64748b;
        --mte-surface:   #ffffff;
        --mte-canvas:    #f6f8fb;
        --mte-line:      #e2e8f0;
        --mte-brand:     #1d4ed8;
        --mte-brand-ink: #ffffff;
        --mte-navy:      #0e1c37;
        --mte-gold:      #ffd66b;
        --mte-shadow:    0 1px 2px rgba(15, 23, 42, .04), 0 14px 30px -18px rgba(15, 23, 42, .3);
        --mte-shadow-lg: 0 2px 4px rgba(15, 23, 42, .04), 0 34px 64px -30px rgba(15, 23, 42, .36);
        --acc: #1d4ed8;
        font-family: 'Open Sans', system-ui, -apple-system, "Segoe UI", sans-serif;
        color: var(--mte-ink);
    }
    /* The global stylesheet hard-codes 15px/#636363 on p, li, a and span.
       :where() keeps this reset at zero specificity so every rule below wins. */
    .mte-scope :where(p, li, a, span, h2, h3, h4) {
        font-size: inherit; line-height: inherit; color: inherit; font-weight: inherit;
    }
    .dark .mte-scope {
        --mte-ink:       #f1f5f9;
        --mte-ink-soft:  #cbd5e1;
        --mte-ink-muted: #94a3b8;
        --mte-surface:   #111a2e;
        --mte-canvas:    #0b1220;
        --mte-line:      #23304a;
        --mte-brand:     #93b4fd;
        --mte-brand-ink: #0b1220;
        --mte-shadow:    0 1px 2px rgba(0, 0, 0, .4), 0 14px 30px -18px rgba(0, 0, 0, .7);
        --mte-shadow-lg: 0 2px 4px rgba(0, 0, 0, .4), 0 34px 64px -30px rgba(0, 0, 0, .8);
    }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .mte-scope {
            --mte-ink:       #f1f5f9;
            --mte-ink-soft:  #cbd5e1;
            --mte-ink-muted: #94a3b8;
            --mte-surface:   #111a2e;
            --mte-canvas:    #0b1220;
            --mte-line:      #23304a;
            --mte-brand:     #93b4fd;
            --mte-brand-ink: #0b1220;
        }
    }
    .mte-accent { --acc: var(--acc-l, #1d4ed8); }
    .dark .mte-accent { --acc: var(--acc-d, #93b4fd); }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .mte-accent { --acc: var(--acc-d, #93b4fd); }
    }

    .mte-shell {
        width: 100%;
        /* 100vw guard: the legacy layout can be wider than the viewport on
           small screens — the content area must not follow it. */
        max-width: min(1240px, 100vw);
        margin-inline: auto;
        padding-inline: clamp(16px, 4vw, 40px);
    }
    .mte-band { padding-block: clamp(52px, 7vw, 96px); background: var(--mte-surface); scroll-margin-top: 120px; }
    .mte-band-alt { background: var(--mte-canvas); }
    .mte-band + .mte-band { border-top: 1px solid var(--mte-line); }

    /* ---------- Section headers ---------- */
    .mte-head { max-width: 740px; margin-bottom: clamp(26px, 3.6vw, 44px); }
    .mte-eyebrow {
        display: inline-flex; align-items: center; gap: 9px;
        font-size: 12px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
        color: var(--acc);
        padding: 7px 14px; border-radius: 999px;
        background: color-mix(in srgb, var(--acc) 10%, transparent);
        border: 1px solid color-mix(in srgb, var(--acc) 22%, transparent);
    }
    .mte-eyebrow .material-symbols-outlined { font-size: 17px; }
    .mte-scope .mte-head h2 {
        margin: 18px 0 0;
        font-size: clamp(25px, 3.2vw, 38px);
        line-height: 1.16; letter-spacing: -.025em; font-weight: 700;
        color: var(--mte-ink);
    }
    .mte-scope .mte-lede {
        margin: 13px 0 0;
        font-size: clamp(15px, 1.5vw, 17px);
        line-height: 1.7; color: var(--mte-ink-soft);
    }

    /* ---------- In-page navigation ---------- */
    .mte-jump {
        position: sticky; top: 12px; z-index: 30;
        width: max-content; max-width: 100%;
        margin-bottom: clamp(28px, 4vw, 48px);
        padding: 6px; display: flex; gap: 4px;
        overflow-x: auto; scrollbar-width: none;
        background: color-mix(in srgb, var(--mte-surface) 88%, transparent);
        backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
        border: 1px solid var(--mte-line); border-radius: 999px;
        box-shadow: var(--mte-shadow);
    }
    .mte-jump::-webkit-scrollbar { display: none; }
    @media (min-width: 1024px) { .mte-jump { top: 104px; } }
    .mte-jump a {
        flex: 0 0 auto; display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 999px;
        font-size: 14px; font-weight: 600; letter-spacing: -.01em;
        color: var(--mte-ink-soft); text-decoration: none; white-space: nowrap;
        transition: background-color .2s ease, color .2s ease;
    }
    .mte-jump a .material-symbols-outlined { font-size: 18px; }
    .mte-jump a:hover, .mte-jump a:focus-visible { background: var(--mte-canvas); color: var(--mte-ink); }

    /* ---------- Grids & cards ---------- */
    .mte-grid { display: grid; gap: clamp(14px, 1.8vw, 22px); grid-template-columns: 1fr; }
    @media (min-width: 680px) {
        .mte-grid.mte-cols-2, .mte-grid.mte-cols-3, .mte-grid.mte-cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1040px) {
        .mte-grid.mte-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .mte-grid.mte-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .mte-card {
        display: flex; flex-direction: column;
        padding: clamp(20px, 2.2vw, 28px);
        background: var(--mte-surface);
        border: 1px solid var(--mte-line);
        border-radius: 18px;
        box-shadow: var(--mte-shadow);
        transition: transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s ease, border-color .28s ease;
    }
    .mte-card:hover, .mte-card:focus-within {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--acc) 35%, transparent);
        box-shadow: var(--mte-shadow-lg);
    }
    .mte-card-icon {
        display: grid; place-items: center;
        width: 44px; height: 44px; border-radius: 13px; margin-bottom: 18px;
        background: color-mix(in srgb, var(--acc) 12%, transparent);
        color: var(--acc);
    }
    .mte-card-icon .material-symbols-outlined { font-size: 24px; }
    .mte-scope .mte-card h3 {
        margin: 0; font-size: clamp(16px, 1.5vw, 19px);
        line-height: 1.3; letter-spacing: -.02em; font-weight: 700; color: var(--mte-ink);
    }
    .mte-scope .mte-card p {
        margin: 9px 0 0; font-size: 14.5px; line-height: 1.65; color: var(--mte-ink-soft);
    }
    /* margin-top:auto pins the tag to the card foot so tags line up across a
       row whatever the description length; the sibling margin keeps a gap. */
    .mte-scope .mte-card p + .mte-tag,
    .mte-scope .mte-card h3 + .mte-tag { margin-top: auto; }
    .mte-scope .mte-card p:has(+ .mte-tag) { margin-bottom: 16px; }
    .mte-tag {
        align-self: flex-start; margin-top: 16px;
        padding: 5px 11px; border-radius: 999px;
        font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
        background: color-mix(in srgb, var(--acc) 10%, transparent);
        color: var(--acc);
        border: 1px solid color-mix(in srgb, var(--acc) 22%, transparent);
    }

    /* ---------- Eligibility facts ---------- */
    .mte-facts { display: grid; gap: 12px; grid-template-columns: 1fr; margin-top: clamp(24px, 3vw, 34px); }
    @media (min-width: 680px) { .mte-facts { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .mte-fact {
        display: flex; align-items: center; gap: 13px;
        padding: 16px 18px;
        background: var(--mte-canvas);
        border: 1px solid var(--mte-line);
        border-radius: 16px;
    }
    .mte-band-alt .mte-fact { background: var(--mte-surface); }
    .mte-fact-icon {
        flex: 0 0 auto; display: grid; place-items: center;
        width: 38px; height: 38px; border-radius: 11px;
        background: color-mix(in srgb, var(--acc) 12%, transparent); color: var(--acc);
    }
    .mte-fact-icon .material-symbols-outlined { font-size: 21px; }
    .mte-fact-label { display: block; font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--mte-ink-muted); }
    .mte-fact-value { display: block; margin-top: 2px; font-size: 15px; font-weight: 700; color: var(--mte-ink); }

    /* ---------- Programme filters ---------- */
    .mte-filters {
        display: flex; flex-wrap: wrap; gap: 8px;
        margin-bottom: clamp(20px, 2.6vw, 30px);
    }
    .mte-filter {
        appearance: none; cursor: pointer;
        padding: 9px 16px; border-radius: 999px;
        font-family: inherit; font-size: 14px; font-weight: 600; letter-spacing: -.01em;
        color: var(--mte-ink-soft);
        background: var(--mte-surface);
        border: 1px solid var(--mte-line);
        transition: background-color .2s ease, color .2s ease, border-color .2s ease;
    }
    .mte-band-alt .mte-filter { background: var(--mte-canvas); }
    .mte-filter:hover { color: var(--mte-ink); border-color: color-mix(in srgb, var(--mte-brand) 35%, transparent); }
    .mte-filter[aria-pressed="true"] {
        background: var(--mte-brand); color: var(--mte-brand-ink);
        border-color: var(--mte-brand);
    }
    .mte-result-count { margin-bottom: 18px; font-size: 14px; color: var(--mte-ink-muted); }

    /* ---------- Steps ---------- */
    .mte-steps { counter-reset: mte-step; display: grid; gap: clamp(14px, 1.8vw, 22px); grid-template-columns: 1fr; }
    @media (min-width: 680px) { .mte-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1040px) { .mte-steps { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    .mte-step {
        position: relative;
        padding: clamp(20px, 2.2vw, 28px);
        background: var(--mte-surface);
        border: 1px solid var(--mte-line);
        border-radius: 18px;
        box-shadow: var(--mte-shadow);
    }
    .mte-step-no {
        display: inline-grid; place-items: center;
        width: 38px; height: 38px; border-radius: 50%;
        margin-bottom: 16px;
        background: var(--acc); color: #fff;
        font-size: 15px; font-weight: 700; font-variant-numeric: tabular-nums;
    }
    .dark .mte-step-no { color: #0b1220; }
    .mte-scope .mte-step h3 { margin: 0; font-size: 17px; line-height: 1.3; font-weight: 700; color: var(--mte-ink); }
    .mte-scope .mte-step p { margin: 9px 0 0; font-size: 14.5px; line-height: 1.65; color: var(--mte-ink-soft); }

    /* ---------- Callout (sessions) ---------- */
    .mte-callout {
        display: flex; flex-wrap: wrap; align-items: center; gap: clamp(16px, 2.4vw, 28px);
        padding: clamp(22px, 3vw, 34px);
        background: var(--mte-surface);
        border: 1px solid var(--mte-line);
        border-left: 4px solid var(--mte-brand);
        border-radius: 18px;
        box-shadow: var(--mte-shadow);
    }
    .mte-band-alt .mte-callout { background: var(--mte-surface); }
    .mte-callout-icon {
        flex: 0 0 auto; display: grid; place-items: center;
        width: 48px; height: 48px; border-radius: 14px;
        background: color-mix(in srgb, var(--mte-brand) 12%, transparent); color: var(--mte-brand);
    }
    .mte-callout-icon .material-symbols-outlined { font-size: 26px; }
    .mte-callout-body { flex: 1 1 340px; min-width: 0; }
    .mte-scope .mte-callout h2 { margin: 0; font-size: clamp(19px, 2vw, 23px); line-height: 1.25; font-weight: 700; color: var(--mte-ink); }
    .mte-scope .mte-callout p { margin: 8px 0 0; font-size: 15px; line-height: 1.65; color: var(--mte-ink-soft); }

    /* ---------- Buttons ---------- */
    .mte-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 9px;
        min-height: 46px; padding: 0 20px; border-radius: 12px;
        font-size: 15px; font-weight: 700; letter-spacing: -.01em;
        text-decoration: none; white-space: nowrap;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
    }
    .mte-btn .material-symbols-outlined { font-size: 20px; }
    .mte-btn-primary { background: var(--mte-brand); color: var(--mte-brand-ink); }
    .mte-btn-primary:hover, .mte-btn-primary:focus-visible {
        background: color-mix(in srgb, var(--mte-brand) 84%, #000); color: var(--mte-brand-ink);
    }
    .mte-btn-ghost {
        background: transparent; color: var(--mte-ink); border: 1px solid var(--mte-line);
    }
    .mte-btn-ghost:hover, .mte-btn-ghost:focus-visible {
        color: var(--mte-brand); border-color: color-mix(in srgb, var(--mte-brand) 40%, transparent);
    }

    /* ---------- Download panel ---------- */
    .mte-download {
        display: grid; gap: 0; overflow: hidden;
        background: var(--mte-surface);
        border: 1px solid var(--mte-line);
        border-radius: 24px;
        box-shadow: var(--mte-shadow-lg);
    }
    @media (min-width: 900px) { .mte-download.has-aside { grid-template-columns: 1.15fr .85fr; } }
    .mte-download-main { padding: clamp(26px, 3.6vw, 48px); }
    .mte-download-aside {
        padding: clamp(26px, 3.6vw, 48px);
        background: var(--mte-navy); color: #e8eefc;
    }
    .mte-filemeta {
        display: inline-flex; align-items: center; gap: 8px;
        margin-top: 16px; font-size: 13px; color: var(--mte-ink-muted);
    }
    .mte-filemeta .material-symbols-outlined { font-size: 18px; }
    .mte-scope .mte-download-aside h3 {
        margin: 0 0 18px; font-size: 17px; font-weight: 700; letter-spacing: .01em; color: #ffffff;
    }
    .mte-aside-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 12px; }
    .mte-aside-list li { display: flex; align-items: flex-start; gap: 12px; font-size: 15px; line-height: 1.55; color: #cfdaf2; }
    .mte-aside-list .material-symbols-outlined { font-size: 21px; color: var(--mte-gold); flex: 0 0 auto; }

    /* ---------- Contact ---------- */
    .mte-contact-tile {
        display: flex; align-items: center; gap: 14px;
        padding: 18px 20px;
        background: var(--mte-surface);
        border: 1px solid var(--mte-line);
        border-radius: 16px;
        text-decoration: none; color: inherit;
        transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    }
    .mte-band-alt .mte-contact-tile { background: var(--mte-surface); }
    a.mte-contact-tile:hover, a.mte-contact-tile:focus-visible {
        transform: translateY(-3px);
        border-color: color-mix(in srgb, var(--acc) 40%, transparent);
        box-shadow: var(--mte-shadow);
    }
    .mte-contact-tile .mte-fact-icon { width: 42px; height: 42px; border-radius: 12px; }
    .mte-address {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;
        margin-top: clamp(20px, 2.6vw, 30px);
        padding: clamp(20px, 2.6vw, 30px);
        background: var(--mte-canvas);
        border: 1px solid var(--mte-line);
        border-radius: 18px;
    }
    .mte-band-alt .mte-address { background: var(--mte-surface); }
    .mte-address-text { flex: 1 1 380px; min-width: 0; display: flex; align-items: flex-start; gap: 14px; }

    /* ---------- Closing CTA ---------- */
    .mte-cta { background: var(--mte-navy); padding-block: clamp(52px, 7vw, 90px); }
    .mte-cta-inner { max-width: 780px; }
    .mte-scope .mte-cta h2 {
        margin: 18px 0 0; font-size: clamp(26px, 3.4vw, 40px);
        line-height: 1.15; letter-spacing: -.025em; font-weight: 700; color: #ffffff;
    }
    .mte-scope .mte-cta p { margin: 14px 0 0; font-size: clamp(15px, 1.5vw, 17px); line-height: 1.7; color: #b9c6e4; }
    .mte-cta .mte-eyebrow {
        color: var(--mte-gold);
        background: rgba(255, 214, 107, .12);
        border-color: rgba(255, 214, 107, .32);
    }
    .mte-cta-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: clamp(24px, 3vw, 32px); }
    .mte-cta .mte-btn-primary { background: var(--mte-gold); color: #0e1c37; }
    .mte-cta .mte-btn-primary:hover, .mte-cta .mte-btn-primary:focus-visible {
        background: #ffe08f; color: #0e1c37;
    }
    .mte-cta .mte-btn-ghost { color: #ffffff; border-color: rgba(255, 255, 255, .3); }
    .mte-cta .mte-btn-ghost:hover, .mte-cta .mte-btn-ghost:focus-visible {
        color: #ffffff; border-color: rgba(255, 255, 255, .6); background: rgba(255, 255, 255, .08);
    }
    .mte-stats {
        display: grid; gap: 20px; grid-template-columns: 1fr;
        margin-top: clamp(34px, 5vw, 56px);
        padding-top: clamp(26px, 3.4vw, 40px);
        border-top: 1px solid rgba(255, 255, 255, .14);
    }
    @media (min-width: 680px) { .mte-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .mte-stat-value {
        display: block; font-size: clamp(30px, 3.6vw, 40px); font-weight: 700;
        letter-spacing: -.03em; color: var(--mte-gold); font-variant-numeric: tabular-nums;
    }
    .mte-stat-label {
        display: block; margin-top: 4px;
        font-size: 13px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #9fb2d8;
    }

    /* ---------- Shared a11y ---------- */
    .mte-scope a:focus-visible, .mte-scope button:focus-visible {
        outline: 2px solid var(--acc); outline-offset: 3px;
    }
    .mte-cta a:focus-visible { outline-color: var(--mte-gold); }
    @media (prefers-reduced-motion: reduce) {
        .mte-scope * { transition: none !important; animation: none !important; }
        .mte-card:hover, a.mte-contact-tile:hover { transform: none; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); ?>" 
                 alt="Mature Students" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <?php if ($page_data['hero_badge']): ?>
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge']); ?></span>
                </div>
                <?php endif; ?>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $page_data['hero_title']; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo $page_data['hero_subtitle']; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description']); ?>"
                </p>

                <div class="mt-12 flex flex-col sm:flex-row gap-6 justify-center animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="uploads/Download Forms/mature-admissions-form.pdf" download class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4 animate-pulse-glow">
                        <span class="material-symbols-outlined text-3xl">download</span>
                        Download Form
                    </a>
                    <a href="#programs" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        View Programs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php
    // ------------------------------------------------------------------
    // Content-area helpers (presentation only — data still comes from DB)
    // ------------------------------------------------------------------

    // Light-mode ink meets WCAG AA on white; dark-mode ink meets AA on the
    // dark surface. Keyed by the base of a stored token like "yellow-500".
    $mte_accents = [
        'blue'   => ['#1d4ed8', '#93b4fd'],
        'indigo' => ['#4338ca', '#a5b4fc'],
        'purple' => ['#6d28d9', '#c4b5fd'],
        'green'  => ['#15803d', '#86efac'],
        'teal'   => ['#0f766e', '#5eead4'],
        'cyan'   => ['#0e7490', '#67e8f9'],
        'red'    => ['#b42318', '#fca5a5'],
        'orange' => ['#c2410c', '#fdba74'],
        'amber'  => ['#b45309', '#fcd34d'],
        'yellow' => ['#b45309', '#fcd34d'],
        'slate'  => ['#334155', '#cbd5e1'],
    ];
    $mte_accent_style = function ($color) use ($mte_accents) {
        $base = explode('-', (string)($color ?: 'blue'))[0];
        [$light, $dark] = $mte_accents[$base] ?? $mte_accents['blue'];
        return '--acc-l:' . $light . '; --acc-d:' . $dark . ';';
    };

    /** Section copy lives in section_description on this page; subtitle is a fallback. */
    $mte_blurb = function ($section) {
        foreach (['section_description', 'section_subtitle'] as $field) {
            $v = trim(strip_tags((string)($section[$field] ?? '')));
            if ($v !== '') return $v;
        }
        return '';
    };

    /** Human-readable size for a file on disk, or null when it's missing. */
    $mte_filesize = function ($path) {
        $abs = __DIR__ . '/' . ltrim((string)$path, '/');
        if (!is_file($abs)) return null;
        $bytes = filesize($abs);
        if ($bytes === false) return null;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) { $bytes /= 1024; $i++; }
        return round($bytes, ($i > 0 && $bytes < 100) ? 1 : 0) . ' ' . $units[$i];
    };

    $mte_form_path = 'uploads/Download Forms/mature-admissions-form.pdf';
    $mte_form_href = str_replace(' ', '%20', $mte_form_path);
    $mte_form_size = $mte_filesize($mte_form_path);

    /** Turn a stored contact value into tel:/mailto: where possible. */
    $mte_contact_href = function ($value) {
        $value = trim((string)$value);
        if ($value === '') return null;
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) return 'mailto:' . $value;
        if (strlen(preg_replace('/[^0-9]/', '', $value)) >= 7) {
            return 'tel:' . preg_replace('/[^0-9+]/', '', $value);
        }
        return null;
    };

    // Jump nav — only sections that actually have content to show.
    $mte_nav = [];
    if (isset($sections['programs']))     $mte_nav[] = ['programs',   'Programmes',  'school'];
    if (isset($sections['sessions']))     $mte_nav[] = ['sessions',   'Sessions',    'event_repeat'];
    if (isset($sections['how_to_apply'])) $mte_nav[] = ['how-to-apply','How to apply','list_alt'];
    if (isset($sections['form_download']))$mte_nav[] = ['form',       'Download form','download'];
    if (isset($sections['contact']))      $mte_nav[] = ['contact',    'Contact',     'call'];

    // Programme categories come from item_subtitle (Education, Business, …).
    $mte_categories = [];
    foreach ($items['programs'] ?? [] as $p) {
        $cat = trim(strip_tags((string)$p['item_subtitle']));
        if ($cat !== '' && !in_array($cat, $mte_categories, true)) $mte_categories[] = $cat;
    }
    sort($mte_categories);
    ?>

    <div class="mte-scope">

    <!-- Introduction -->
    <?php if (isset($sections['intro'])): ?>
    <section class="mte-band">
        <div class="mte-shell">
            <?php if (count($mte_nav) > 1): ?>
            <nav class="mte-jump" aria-label="Sections on this page">
                <?php foreach ($mte_nav as $nav): ?>
                <a href="#<?php echo $nav[0]; ?>">
                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo $nav[2]; ?></span>
                    <?php echo $nav[1]; ?>
                </a>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>

            <div class="mte-head mte-accent">
                <span class="mte-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">diversity_3</span>
                    Mature entrance
                </span>
                <h2><?php echo strip_tags($sections['intro']['section_title']); ?></h2>
                <?php $intro_blurb = $mte_blurb($sections['intro']); ?>
                <?php if ($intro_blurb !== ''): ?>
                <p class="mte-lede"><?php echo $intro_blurb; ?></p>
                <?php endif; ?>
            </div>

            <div class="mte-facts mte-accent">
                <div class="mte-fact">
                    <span class="mte-fact-icon" aria-hidden="true"><span class="material-symbols-outlined">calendar_month</span></span>
                    <span>
                        <span class="mte-fact-label">Minimum age</span>
                        <span class="mte-fact-value">25 years and above</span>
                    </span>
                </div>
                <div class="mte-fact">
                    <span class="mte-fact-icon" aria-hidden="true"><span class="material-symbols-outlined">task_alt</span></span>
                    <span>
                        <span class="mte-fact-label">Entry route</span>
                        <span class="mte-fact-value">No WASSCE required</span>
                    </span>
                </div>
                <div class="mte-fact">
                    <span class="mte-fact-icon" aria-hidden="true"><span class="material-symbols-outlined">event_repeat</span></span>
                    <span>
                        <span class="mte-fact-label">Intakes</span>
                        <span class="mte-fact-value">Several sessions a year</span>
                    </span>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Why choose -->
    <?php if (isset($sections['why_choose']) && !empty($items['why_choose'])): ?>
    <section class="mte-band mte-band-alt">
        <div class="mte-shell">
            <div class="mte-head mte-accent">
                <span class="mte-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">workspace_premium</span>
                    Why Valley View
                </span>
                <h2><?php echo strip_tags($sections['why_choose']['section_title']); ?></h2>
                <?php $why_blurb = $mte_blurb($sections['why_choose']); ?>
                <?php if ($why_blurb !== ''): ?>
                <p class="mte-lede"><?php echo $why_blurb; ?></p>
                <?php endif; ?>
            </div>

            <div class="mte-grid mte-cols-<?php echo max(1, min(count($items['why_choose']), 4)); ?>">
                <?php foreach ($items['why_choose'] as $item): ?>
                <article class="mte-card mte-accent" style="<?php echo $mte_accent_style($item['item_color']); ?>">
                    <?php if (!empty($item['item_icon'])): ?>
                    <span class="mte-card-icon" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon']); ?></span>
                    </span>
                    <?php endif; ?>
                    <h3><?php echo strip_tags($item['item_title']); ?></h3>
                    <p><?php echo strip_tags($item['item_description']); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Available programmes -->
    <?php if (isset($sections['programs'])): ?>
    <section id="programs" class="mte-band">
        <div class="mte-shell">
            <div class="mte-head mte-accent">
                <span class="mte-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">school</span>
                    Programmes
                </span>
                <h2><?php echo strip_tags($sections['programs']['section_title']); ?></h2>
                <?php $prog_blurb = $mte_blurb($sections['programs']); ?>
                <?php if ($prog_blurb !== ''): ?>
                <p class="mte-lede"><?php echo $prog_blurb; ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($items['programs'])): ?>
            <?php if (count($mte_categories) > 1): ?>
            <div class="mte-filters" role="group" aria-label="Filter programmes by field" id="mte-filters">
                <button type="button" class="mte-filter" data-filter="all" aria-pressed="true">All programmes</button>
                <?php foreach ($mte_categories as $cat): ?>
                <button type="button" class="mte-filter" data-filter="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>" aria-pressed="false">
                    <?php echo htmlspecialchars($cat); ?>
                </button>
                <?php endforeach; ?>
            </div>
            <p class="mte-result-count" id="mte-count" role="status">
                Showing all <?php echo count($items['programs']); ?> programmes
            </p>
            <?php endif; ?>

            <div class="mte-grid mte-cols-4" id="mte-programs">
                <?php foreach ($items['programs'] as $item): ?>
                <?php $cat = trim(strip_tags((string)$item['item_subtitle'])); ?>
                <article class="mte-card mte-accent" style="<?php echo $mte_accent_style($item['item_color']); ?>"
                         data-category="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>">
                    <?php if (!empty($item['item_icon'])): ?>
                    <span class="mte-card-icon" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon']); ?></span>
                    </span>
                    <?php endif; ?>
                    <h3><?php echo strip_tags($item['item_title']); ?></h3>
                    <p><?php echo strip_tags($item['item_description']); ?></p>
                    <?php if ($cat !== ''): ?>
                    <span class="mte-tag"><?php echo htmlspecialchars($cat); ?></span>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Enrollment sessions -->
    <?php if (isset($sections['sessions'])): ?>
    <section id="sessions" class="mte-band mte-band-alt">
        <div class="mte-shell">
            <div class="mte-callout">
                <span class="mte-callout-icon" aria-hidden="true">
                    <span class="material-symbols-outlined">event_repeat</span>
                </span>
                <div class="mte-callout-body">
                    <h2><?php echo strip_tags($sections['sessions']['section_title']); ?></h2>
                    <?php $sess_blurb = $mte_blurb($sections['sessions']); ?>
                    <?php if ($sess_blurb !== ''): ?>
                    <p><?php echo $sess_blurb; ?></p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($items['sessions'])): ?>
                <?php endif; ?>
                <a class="mte-btn mte-btn-ghost" href="contact_us.php">
                    <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                    Ask about session dates
                </a>
            </div>

            <?php if (!empty($items['sessions'])): ?>
            <div class="mte-grid mte-cols-<?php echo max(1, min(count($items['sessions']), 3)); ?>" style="margin-top: 22px;">
                <?php foreach ($items['sessions'] as $item): ?>
                <article class="mte-card mte-accent" style="<?php echo $mte_accent_style($item['item_color']); ?>">
                    <?php if (!empty($item['item_icon'])): ?>
                    <span class="mte-card-icon" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon']); ?></span>
                    </span>
                    <?php endif; ?>
                    <h3><?php echo strip_tags($item['item_title']); ?></h3>
                    <p><?php echo strip_tags($item['item_description']); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- How to apply -->
    <?php if (isset($sections['how_to_apply']) && !empty($items['how_to_apply'])): ?>
    <section id="how-to-apply" class="mte-band">
        <div class="mte-shell">
            <div class="mte-head mte-accent">
                <span class="mte-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">list_alt</span>
                    Four steps
                </span>
                <h2><?php echo strip_tags($sections['how_to_apply']['section_title']); ?></h2>
                <?php $apply_blurb = $mte_blurb($sections['how_to_apply']); ?>
                <?php if ($apply_blurb !== ''): ?>
                <p class="mte-lede"><?php echo $apply_blurb; ?></p>
                <?php endif; ?>
            </div>

            <ol class="mte-steps" style="list-style: none; margin: 0; padding: 0;">
                <?php foreach ($items['how_to_apply'] as $index => $item): ?>
                <li class="mte-step mte-accent" style="<?php echo $mte_accent_style($item['item_color']); ?>">
                    <span class="mte-step-no" aria-hidden="true"><?php echo $index + 1; ?></span>
                    <h3><span class="sr-only">Step <?php echo $index + 1; ?>: </span><?php echo strip_tags($item['item_title']); ?></h3>
                    <p><?php echo strip_tags($item['item_description']); ?></p>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>
    <?php endif; ?>

    <!-- Download the form -->
    <?php if (isset($sections['form_download'])): ?>
    <?php $has_locations = !empty($items['form_locations']); ?>
    <section id="form" class="mte-band mte-band-alt">
        <div class="mte-shell">
            <div class="mte-download<?php echo $has_locations ? ' has-aside' : ''; ?>">
                <div class="mte-download-main mte-accent">
                    <span class="mte-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">description</span>
                        Application form
                    </span>
                    <div class="mte-head" style="margin-bottom: 0;">
                        <h2><?php echo strip_tags($sections['form_download']['section_title']); ?></h2>
                        <?php $form_blurb = $mte_blurb($sections['form_download']); ?>
                        <?php if ($form_blurb !== ''): ?>
                        <p class="mte-lede"><?php echo $form_blurb; ?></p>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 26px;">
                        <a class="mte-btn mte-btn-primary" href="<?php echo htmlspecialchars($mte_form_href, ENT_QUOTES); ?>" download>
                            <span class="material-symbols-outlined" aria-hidden="true">download</span>
                            Download the form
                        </a>
                        <a class="mte-btn mte-btn-ghost" href="contact_us.php">
                            <span class="material-symbols-outlined" aria-hidden="true">support_agent</span>
                            Ask a question
                        </a>
                    </div>
                    <?php if ($mte_form_size !== null): ?>
                    <span class="mte-filemeta">
                        <span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span>
                        PDF · <?php echo $mte_form_size; ?>
                    </span>
                    <?php endif; ?>
                </div>

                <?php if ($has_locations): ?>
                <aside class="mte-download-aside">
                    <h3>Also available at</h3>
                    <ul class="mte-aside-list">
                        <?php foreach ($items['form_locations'] as $item): ?>
                        <li>
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo strip_tags($item['item_icon'] ?: 'place'); ?></span>
                            <span><?php echo strip_tags($item['item_title']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Contact -->
    <?php if (isset($sections['contact'])): ?>
    <section id="contact" class="mte-band">
        <div class="mte-shell">
            <div class="mte-head mte-accent">
                <span class="mte-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">call</span>
                    Admissions office
                </span>
                <h2><?php echo strip_tags($sections['contact']['section_title']); ?></h2>
                <?php $contact_blurb = $mte_blurb($sections['contact']); ?>
                <?php if ($contact_blurb !== ''): ?>
                <p class="mte-lede"><?php echo $contact_blurb; ?></p>
                <?php endif; ?>
            </div>

            <?php $contact_items = $items['contact'] ?? $items['contact_info'] ?? []; ?>
            <?php if ($contact_items): ?>
            <div class="mte-grid mte-cols-<?php echo max(1, min(count($contact_items), 4)); ?>">
                <?php foreach ($contact_items as $item): ?>
                <?php
                    // The value sits in item_subtitle on this page; description is a fallback.
                    $value = trim(strip_tags((string)$item['item_subtitle']));
                    if ($value === '') $value = trim(strip_tags((string)$item['item_description']));
                    $href  = $mte_contact_href($value);
                    $tag   = $href ? 'a' : 'div';
                ?>
                <<?php echo $tag; ?> class="mte-contact-tile mte-accent" style="<?php echo $mte_accent_style($item['item_color']); ?>"
                   <?php echo $href ? 'href="' . htmlspecialchars($href, ENT_QUOTES) . '"' : ''; ?>>
                    <span class="mte-fact-icon" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'call'); ?></span>
                    </span>
                    <span>
                        <span class="mte-fact-label"><?php echo strip_tags($item['item_title']); ?></span>
                        <span class="mte-fact-value"><?php echo $value; ?></span>
                    </span>
                </<?php echo $tag; ?>>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="mte-address mte-accent">
                <div class="mte-address-text">
                    <span class="mte-fact-icon" aria-hidden="true"><span class="material-symbols-outlined">location_on</span></span>
                    <span>
                        <span class="mte-fact-label">Campus address</span>
                        <span class="mte-fact-value" style="font-weight: 600; line-height: 1.6;">
                            Mile 19 Off the Adenta-Dodowa Road, P.O. Box AF 595 Adentan
                        </span>
                    </span>
                </div>
                <a class="mte-btn mte-btn-primary" href="contact_us.php">
                    <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                    Contact admissions
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Closing call to action -->
    <?php if (!empty($page_data['cta_title'])): ?>
    <section class="mte-band mte-cta">
        <div class="mte-shell">
            <div class="mte-cta-inner">
                <span class="mte-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">rocket_launch</span>
                    Get started
                </span>
                <h2><?php echo strip_tags($page_data['cta_title']); ?></h2>
                <?php if (!empty($page_data['cta_subtitle'])): ?>
                <p><?php echo strip_tags($page_data['cta_subtitle']); ?></p>
                <?php endif; ?>

                <div class="mte-cta-actions">
                    <a class="mte-btn mte-btn-primary" href="<?php echo htmlspecialchars($mte_form_href, ENT_QUOTES); ?>" download>
                        <span class="material-symbols-outlined" aria-hidden="true">download</span>
                        Download the form
                    </a>
                    <a class="mte-btn mte-btn-ghost" href="<?php echo htmlspecialchars(strip_tags($page_data['cta_button_link'] ?: 'apply.php'), ENT_QUOTES); ?>">
                        <span class="material-symbols-outlined" aria-hidden="true">edit_square</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Online'); ?>
                    </a>
                </div>

                <div class="mte-stats">
                    <div>
                        <span class="mte-stat-value"><?php echo count($items['programs'] ?? []); ?></span>
                        <span class="mte-stat-label">Programmes available</span>
                    </div>
                    <div>
                        <span class="mte-stat-value">25+</span>
                        <span class="mte-stat-label">Minimum age</span>
                    </div>
                    <div>
                        <span class="mte-stat-value">45+</span>
                        <span class="mte-stat-label">Years of excellence</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    </div><!-- /.mte-scope -->

    <script>
    // Programme filter: progressive enhancement — every card is visible until
    // this runs, so the list still works with JavaScript disabled.
    (function () {
        var filters = document.getElementById('mte-filters');
        var grid    = document.getElementById('mte-programs');
        var count   = document.getElementById('mte-count');
        if (!filters || !grid) return;

        var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-category]'));

        filters.addEventListener('click', function (event) {
            var button = event.target.closest('.mte-filter');
            if (!button) return;

            filters.querySelectorAll('.mte-filter').forEach(function (b) {
                b.setAttribute('aria-pressed', String(b === button));
            });

            var wanted = button.getAttribute('data-filter');
            var shown = 0;
            cards.forEach(function (card) {
                var match = wanted === 'all' || card.getAttribute('data-category') === wanted;
                card.hidden = !match;
                if (match) shown++;
            });

            if (count) {
                count.textContent = wanted === 'all'
                    ? 'Showing all ' + shown + ' programmes'
                    : 'Showing ' + shown + ' ' + (shown === 1 ? 'programme' : 'programmes') + ' in ' + wanted;
            }
        });
    })();
    </script>

</main>

<?php
include 'includes/footer.php';
?>
