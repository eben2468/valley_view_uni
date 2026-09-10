<?php
/**
 * ATHE @ Valley View University — Level 3 pre-degree pathway.
 *
 * Every word on this page comes from the shared page CMS under the key
 * 'athe' (academic_pages_content / _sections / _items), so it is edited at
 *     Admin → Admissions Info Pages → ATHE (Pre-Degree)
 * exactly like Mature Entrance, Entry Requirements and the rest of that family.
 * See sql/athe_page_content.sql for the seed content and the section keys.
 *
 * A section that is switched off in the admin simply does not render, so the
 * page stays coherent however much of it is turned on.
 */
$page_title = "ATHE Level 3 Diplomas — Valley View University";
$active_page = "admissions";
include 'includes/header.php';
require_once 'includes/db_connect.php';

$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'athe'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'athe' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $s) {
    $sections[$s['section_key']] = $s;
}

$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'athe' AND is_active = 1 ORDER BY display_order, id");
$stmt->execute();
$items = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $i) {
    $items[$i['section_key']][] = $i;
}

// ---------------------------------------------------------------------------
// Small helpers. Presentation only — every value still comes from the CMS.
// ---------------------------------------------------------------------------

/** Escape for attributes and short strings. */
$e = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

/** Body copy: keep the light formatting an editor may add, drop the rest. */
$rich = function ($value) {
    return trim(strip_tags((string)$value, '<strong><b><em><i><br><ul><ol><li><p>'));
};

/** A section, or null when it has been switched off in the admin. */
$sec = function ($key) use ($sections) {
    return $sections[$key] ?? null;
};

/** The active items of a section, in display order. */
$list = function ($key) use ($items) {
    return $items[$key] ?? [];
};

/** First item of a section — used for the one-line pull quotes. */
$first = function ($key) use ($items) {
    return $items[$key][0] ?? null;
};

// Accent pairs: [light-mode ink, dark-mode ink]. Both meet AA on their
// background. Keyed by the base of a stored token such as "amber-500".
$athe_accents = [
    'blue'   => ['#1d4ed8', '#93b4fd'],
    'indigo' => ['#4338ca', '#a5b4fc'],
    'purple' => ['#6d28d9', '#c4b5fd'],
    'green'  => ['#15803d', '#86efac'],
    'teal'   => ['#0f766e', '#5eead4'],
    'amber'  => ['#a16207', '#fcd34d'],
    'yellow' => ['#a16207', '#fcd34d'],
    'orange' => ['#c2410c', '#fdba74'],
    'red'    => ['#b42318', '#fca5a5'],
    'slate'  => ['#334155', '#cbd5e1'],
];
$accent = function ($color) use ($athe_accents) {
    $base = explode('-', (string)($color ?: 'blue'))[0];
    [$light, $dark] = $athe_accents[$base] ?? $athe_accents['blue'];
    return '--acc-l:' . $light . ';--acc-d:' . $dark . ';';
};

/** Human-readable size of a file on disk, or null when it is not there. */
$filesize_of = function ($path) {
    $abs = __DIR__ . '/' . ltrim((string)$path, '/');
    if (!is_file($abs)) {
        return null;
    }
    $bytes = filesize($abs);
    if ($bytes === false) {
        return null;
    }
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, ($i > 0 && $bytes < 100) ? 1 : 0) . ' ' . $units[$i];
};

/** Spaces in an uploaded file name are legal on disk but not in an href. */
$href = function ($path) {
    $path = trim((string)$path);
    // Anchors and absolute URLs are used as-is; an uploaded file name may
    // contain spaces, which are legal on disk but not in an href.
    return preg_match('~^(https?:|mailto:|tel:|#)~i', $path) ? $path : str_replace(' ', '%20', $path);
};

$form_path  = $page_data['cta_button_link'] ?? 'uploads/Download Forms/Athe Application Form.pdf';
$form_size  = $filesize_of($form_path);
$whatsapp   = $page_data['cta_button_link_2'] ?? 'https://wa.me/233243416960';
$apply_text = $page_data['cta_button_text'] ?? 'Download Application Form';

// A form that points at this server but is not actually on it would hand the
// visitor a 404 page saved as ".htm" — the browser follows the download
// attribute whatever comes back. So the button only offers a download when the
// file is really there, and otherwise asks the team for a copy, which is what
// the flyer tells applicants to do anyway.
$form_is_remote = (bool)preg_match('~^(https?:|mailto:|tel:)~i', trim((string)$form_path));
$form_ready     = $form_is_remote || $form_size !== null;
$form_href      = $form_ready ? $href($form_path) : $whatsapp;
$form_label     = $form_ready ? $apply_text : 'Request the application form';
$form_icon      = $form_ready ? 'download' : 'chat';
$form_attrs     = $form_ready
    ? ($form_is_remote ? 'target="_blank" rel="noopener"' : 'download')
    : 'target="_blank" rel="noopener"';

// Jump nav — only the sections that are actually switched on.
$jump = [];
foreach ([
    ['intro',        'overview',    'Overview',      'explore'],
    ['pathways',     'pathways',    'Programmes',    'school'],
    ['why',          'why',         'Why ATHE',      'star'],
    ['eligibility',  'eligibility', 'Eligibility',   'checklist'],
    ['how_it_works', 'how-it-works','How it works',  'timeline'],
    ['progression',  'progression', 'Progression',   'alt_route'],
    ['faqs',         'faqs',        'FAQs',          'quiz'],
    ['apply',        'apply',       'Apply',         'send'],
] as $row) {
    if (isset($sections[$row[0]])) {
        $jump[] = ['id' => $row[1], 'label' => $row[2], 'icon' => $row[3]];
    }
}
?>

<style>
/* --------------------------------------------------------------------------
   Hero animations. Same keyframes and utility names every other page hero
   uses (freshmen_info, entry-requirement, scholarships, mature-entrance …),
   so this header behaves exactly like the rest of the site.
   -------------------------------------------------------------------------- */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes slowZoom {
    0%   { transform: scale(1); }
    100% { transform: scale(1.1); }
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 20px rgba(251, 191, 36, .3); }
    50%      { box-shadow: 0 0 40px rgba(251, 191, 36, .5); }
}

.animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
.animate-fadeInUp  { animation: fadeInUp .6s ease-out forwards; }
.animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

/* ==========================================================================
   ATHE @ VVU
   Scoped under .athe-scope so nothing leaks into the global theme. The palette
   is the one on the ATHE flyer: VVU navy with the ATHE gold.
   Note: bootstrap.css sets html{font-size:10px}, so this block works in px.
   ========================================================================== */
.athe-scope {
    --athe-navy:      #0b2350;
    --athe-navy-soft: #12336d;
    --athe-gold:      #a16207;
    --athe-gold-lit:  #e9b833;
    --athe-ink:       #0f172a;
    --athe-ink-soft:  #475569;
    --athe-ink-muted: #64748b;
    --athe-surface:   #ffffff;
    --athe-canvas:    #f5f7fb;
    --athe-line:      #e2e8f0;
    --athe-shadow:    0 1px 2px rgba(15, 23, 42, .04), 0 14px 30px -18px rgba(15, 23, 42, .3);
    --athe-shadow-lg: 0 2px 4px rgba(15, 23, 42, .04), 0 34px 64px -30px rgba(15, 23, 42, .36);
    font-family: 'Open Sans', system-ui, -apple-system, "Segoe UI", sans-serif;
    color: var(--athe-ink);
}

/* Per-card accent. Each block sets --acc-l / --acc-d inline (see $accent in
   the PHP above); --acc has to be resolved on the element that carries them,
   not once on the scope, or every card would come out the same blue. */
.athe-scope,
.athe-scope * { --acc: var(--acc-l, #1d4ed8); }

/* The global stylesheet hard-codes 15px/#636363 on p, li, a and span.
   :where() keeps this reset at zero specificity so every rule below wins. */
.athe-scope :where(p, li, a, span, h2, h3, h4, summary, strong, em) {
    font-size: inherit;
    line-height: inherit;
    color: inherit;
    font-weight: inherit;
}

.dark .athe-scope,
.athe-scope.is-dark {
    --athe-gold:      #f3cf6b;
    --athe-ink:       #f1f5f9;
    --athe-ink-soft:  #cbd5e1;
    --athe-ink-muted: #94a3b8;
    --athe-surface:   #101a2e;
    --athe-canvas:    #0b1220;
    --athe-line:      #23304a;
    --athe-shadow:    0 1px 2px rgba(0, 0, 0, .4), 0 14px 30px -18px rgba(0, 0, 0, .7);
    --athe-shadow-lg: 0 2px 4px rgba(0, 0, 0, .4), 0 34px 64px -30px rgba(0, 0, 0, .8);
}

.dark .athe-scope,
.dark .athe-scope *,
.athe-scope.is-dark,
.athe-scope.is-dark * { --acc: var(--acc-d, #93b4fd); }

@media (prefers-color-scheme: dark) {
    html:not(.light) .athe-scope {
        --athe-gold:      #f3cf6b;
        --athe-ink:       #f1f5f9;
        --athe-ink-soft:  #cbd5e1;
        --athe-ink-muted: #94a3b8;
        --athe-surface:   #101a2e;
        --athe-canvas:    #0b1220;
        --athe-line:      #23304a;
    }

    html:not(.light) .athe-scope,
    html:not(.light) .athe-scope * { --acc: var(--acc-d, #93b4fd); }
}

/* ---------- Shell & bands ---------- */
.athe-shell {
    width: 100%;
    /* 100vw guard: the legacy layout can be wider than the viewport on small
       screens — the content area must not follow it. */
    max-width: min(1240px, 100vw);
    margin-inline: auto;
    padding-inline: clamp(16px, 4vw, 40px);
}

.athe-band {
    padding-block: clamp(52px, 7vw, 96px);
    background: var(--athe-surface);
    scroll-margin-top: 110px;
}

.athe-band-alt { background: var(--athe-canvas); }
.athe-band + .athe-band { border-top: 1px solid var(--athe-line); }

/* ---------- Section headers ---------- */
.athe-head { max-width: 760px; margin-bottom: clamp(26px, 3.6vw, 44px); }
.athe-head.is-centred { margin-inline: auto; text-align: center; }

.athe-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: var(--acc);
    background: color-mix(in srgb, var(--acc) 10%, transparent);
    border: 1px solid color-mix(in srgb, var(--acc) 22%, transparent);
}

.athe-eyebrow .material-symbols-outlined { font-size: 17px; }

.athe-scope .athe-head h2 {
    margin: 18px 0 0;
    font-size: clamp(25px, 3.2vw, 38px);
    line-height: 1.16;
    letter-spacing: -.025em;
    font-weight: 700;
    color: var(--athe-ink);
}

.athe-scope .athe-lede {
    margin: 13px 0 0;
    font-size: clamp(15px, 1.5vw, 17px);
    line-height: 1.65;
    color: var(--athe-ink-soft);
    font-weight: 400;
}

.athe-scope .athe-note {
    margin-top: 14px;
    font-size: 13.5px;
    line-height: 1.6;
    color: var(--athe-ink-muted);
}

/* ---------- Jump navigation ---------- */
.athe-jump {
    position: sticky;
    top: 12px;
    z-index: 30;
    width: max-content;
    max-width: 100%;
    margin-bottom: clamp(28px, 4vw, 48px);
    padding: 6px;
    display: flex;
    gap: 4px;
    overflow-x: auto;
    scrollbar-width: none;
    background: color-mix(in srgb, var(--athe-surface) 88%, transparent);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid var(--athe-line);
    border-radius: 999px;
    box-shadow: var(--athe-shadow);
}

.athe-jump::-webkit-scrollbar { display: none; }
@media (min-width: 1024px) { .athe-jump { top: 104px; } }

.athe-jump a {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
    color: var(--athe-ink-soft);
    text-decoration: none;
    white-space: nowrap;
    transition: background-color .2s ease, color .2s ease;
}

.athe-jump a .material-symbols-outlined { font-size: 18px; }
.athe-jump a:hover,
.athe-jump a:focus-visible { background: var(--athe-canvas); color: var(--athe-ink); }

/* ---------- Grids & cards ---------- */
.athe-grid { display: grid; gap: clamp(14px, 1.8vw, 22px); grid-template-columns: 1fr; }

@media (min-width: 680px) {
    .athe-grid.cols-2,
    .athe-grid.cols-3,
    .athe-grid.cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (min-width: 1040px) {
    .athe-grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .athe-grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}

.athe-card {
    display: flex;
    flex-direction: column;
    padding: clamp(20px, 2.2vw, 28px);
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    border-radius: 18px;
    box-shadow: var(--athe-shadow);
    transition: transform .28s cubic-bezier(.4, 0, .2, 1), box-shadow .28s ease, border-color .28s ease;
}

.athe-card:hover,
.athe-card:focus-within {
    transform: translateY(-4px);
    border-color: color-mix(in srgb, var(--acc) 35%, transparent);
    box-shadow: var(--athe-shadow-lg);
}

.athe-card-icon {
    display: grid;
    place-items: center;
    width: 46px;
    height: 46px;
    margin-bottom: 18px;
    border-radius: 14px;
    color: var(--acc);
    background: color-mix(in srgb, var(--acc) 12%, transparent);
    border: 1px solid color-mix(in srgb, var(--acc) 20%, transparent);
}

.athe-card-icon .material-symbols-outlined { font-size: 24px; }

.athe-scope .athe-card h3 {
    margin: 0;
    font-size: clamp(16px, 1.7vw, 18.5px);
    line-height: 1.3;
    font-weight: 700;
    letter-spacing: -.015em;
    color: var(--athe-ink);
}

.athe-scope .athe-card p {
    margin: 10px 0 0;
    font-size: 15px;
    line-height: 1.65;
    color: var(--athe-ink-soft);
    font-weight: 400;
}

/* ---------- Pathway flow (WASSCE → ATHE → degree → career) ---------- */
.athe-flow {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 26px;
    grid-template-columns: 1fr;
    counter-reset: athe-flow;
}

.athe-flow-node {
    position: relative;
    padding: clamp(16px, 1.8vw, 22px);
    text-align: center;
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    border-radius: 16px;
    box-shadow: var(--athe-shadow);
}

/* The connector: a chevron pointing at the next node. Down the page on a
   phone, along the row once there is width for one. */
.athe-flow-node:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: -18px;
    width: 11px;
    height: 11px;
    border-right: 2.5px solid color-mix(in srgb, var(--athe-ink-muted) 55%, transparent);
    border-bottom: 2.5px solid color-mix(in srgb, var(--athe-ink-muted) 55%, transparent);
    transform: translateX(-50%) rotate(45deg);
}

.athe-flow-dot {
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    margin: 0 auto 12px;
    border-radius: 50%;
    color: var(--acc);
    background: color-mix(in srgb, var(--acc) 12%, transparent);
    border: 1px solid color-mix(in srgb, var(--acc) 25%, transparent);
}

.athe-flow-dot .material-symbols-outlined { font-size: 22px; }

.athe-scope .athe-flow-label {
    display: block;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.35;
    letter-spacing: -.01em;
    color: var(--athe-ink);
}

.athe-scope .athe-flow-sub {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    line-height: 1.5;
    color: var(--athe-ink-muted);
    font-weight: 400;
}

@media (min-width: 900px) {
    .athe-flow:not(.is-stacked) {
        grid-auto-flow: column;
        grid-auto-columns: minmax(0, 1fr);
        gap: 30px;
    }

    .athe-flow:not(.is-stacked) .athe-flow-node:not(:last-child)::after {
        left: auto;
        right: -21px;
        top: 50%;
        bottom: auto;
        transform: translateY(-50%) rotate(-45deg);
    }
}

/* ---------- The two programmes ---------- */
.athe-prog {
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    border-radius: 22px;
    box-shadow: var(--athe-shadow);
    transition: transform .3s cubic-bezier(.4, 0, .2, 1), box-shadow .3s ease;
}

.athe-prog:hover { transform: translateY(-6px); box-shadow: var(--athe-shadow-lg); }

.athe-prog-top {
    padding: clamp(22px, 2.6vw, 30px);
    color: #fff;
    background: linear-gradient(135deg, var(--athe-navy) 0%, var(--athe-navy-soft) 100%);
}

.athe-prog-top .athe-card-icon {
    margin-bottom: 16px;
    color: var(--athe-gold-lit);
    background: rgba(255, 255, 255, .12);
    border-color: rgba(255, 255, 255, .22);
}

.athe-scope .athe-prog-top h3 {
    margin: 0;
    font-size: clamp(20px, 2.4vw, 26px);
    line-height: 1.2;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #fff;
}

.athe-scope .athe-prog-level {
    display: inline-block;
    margin-top: 12px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--athe-gold-lit);
    background: rgba(255, 255, 255, .1);
    border: 1px solid rgba(255, 255, 255, .2);
}

.athe-prog-body {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: clamp(22px, 2.6vw, 30px);
}

.athe-scope .athe-prog-body p {
    margin: 0;
    font-size: 15.5px;
    line-height: 1.7;
    color: var(--athe-ink-soft);
    font-weight: 400;
}

.athe-prog-foot {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin-top: auto;
    padding-top: 22px;
}

/* ---------- Buttons ---------- */
.athe-scope .athe-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 13px 24px;
    border-radius: 12px;
    border: 1.5px solid transparent;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -.01em;
    text-decoration: none;
    cursor: pointer;
    transition: transform .2s ease, background-color .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease;
}

.athe-scope .athe-btn .material-symbols-outlined { font-size: 20px; }
.athe-scope .athe-btn:hover { transform: translateY(-2px); }

.athe-scope .athe-btn-primary {
    background: var(--athe-navy);
    border-color: var(--athe-navy);
    color: #fff;
    box-shadow: 0 10px 24px -12px rgba(11, 35, 80, .8);
}

.athe-scope .athe-btn-primary:hover { background: var(--athe-navy-soft); border-color: var(--athe-navy-soft); color: #fff; }

.athe-scope .athe-btn-gold {
    background: var(--athe-gold-lit);
    border-color: var(--athe-gold-lit);
    color: #2a1e00;
    box-shadow: 0 10px 24px -12px rgba(233, 184, 51, .9);
}

.athe-scope .athe-btn-gold:hover { background: #f2c94c; border-color: #f2c94c; color: #2a1e00; }

.athe-scope .athe-btn-ghost {
    background: transparent;
    border-color: var(--athe-line);
    color: var(--athe-ink);
}

.athe-scope .athe-btn-ghost:hover {
    border-color: color-mix(in srgb, var(--acc) 45%, transparent);
    color: var(--acc);
}

.athe-scope .athe-btn-wa {
    background: #25d366;
    border-color: #25d366;
    color: #06301a;
}

.athe-scope .athe-btn-wa:hover { background: #1fbe5b; border-color: #1fbe5b; color: #06301a; }

/* ---------- Bullet lists ---------- */
.athe-bullets { list-style: none; margin: 0; padding: 0; display: grid; gap: 10px; }

.athe-bullets li {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 12px 14px;
    border-radius: 12px;
    background: var(--athe-canvas);
    border: 1px solid var(--athe-line);
    font-size: 15px;
    line-height: 1.5;
    font-weight: 500;
    color: var(--athe-ink);
}

.athe-bullets .material-symbols-outlined {
    flex: 0 0 auto;
    font-size: 20px;
    color: var(--acc);
    margin-top: 1px;
}

/* ---------- Programme detail block ---------- */
.athe-detail-head {
    display: grid;
    gap: clamp(20px, 3vw, 40px);
    align-items: start;
    margin-bottom: clamp(26px, 3.4vw, 44px);
}

@media (min-width: 940px) {
    .athe-detail-head { grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr); }
}

.athe-detail-grid { display: grid; gap: clamp(18px, 2.4vw, 30px); grid-template-columns: 1fr; }

@media (min-width: 940px) {
    .athe-detail-grid { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
}

.athe-panel {
    padding: clamp(20px, 2.4vw, 30px);
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    border-radius: 20px;
    box-shadow: var(--athe-shadow);
}

.athe-band-alt .athe-panel { background: var(--athe-surface); }

.athe-scope .athe-panel-title {
    margin: 0 0 16px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--acc);
}

/* ---------- Degree routes ---------- */
.athe-routes { margin-top: clamp(18px, 2.4vw, 30px); }

.athe-route-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 10px;
    grid-template-columns: 1fr;
}

@media (min-width: 640px) { .athe-route-list { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (min-width: 1100px) { .athe-route-list { grid-template-columns: repeat(3, minmax(0, 1fr)); } }

.athe-route-list li {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 13px 15px;
    border-radius: 12px;
    background: var(--athe-canvas);
    border: 1px solid var(--athe-line);
    border-left: 3px solid var(--acc);
    font-size: 15px;
    font-weight: 600;
    line-height: 1.35;
    color: var(--athe-ink);
    transition: transform .2s ease, border-color .2s ease;
}

.athe-route-list li:hover { transform: translateY(-2px); }

.athe-route-list .material-symbols-outlined {
    flex: 0 0 auto;
    font-size: 20px;
    color: var(--acc);
}

/* ---------- Quote ---------- */
.athe-quote {
    display: flex;
    align-items: center;
    gap: clamp(14px, 2vw, 22px);
    margin-top: clamp(26px, 3.4vw, 40px);
    padding: clamp(20px, 2.6vw, 30px);
    border-radius: 20px;
    background: linear-gradient(135deg, var(--athe-navy) 0%, var(--athe-navy-soft) 100%);
    color: #fff;
}

.athe-quote .material-symbols-outlined {
    flex: 0 0 auto;
    font-size: clamp(30px, 4vw, 44px);
    color: var(--athe-gold-lit);
}

.athe-scope .athe-quote strong {
    display: block;
    font-size: clamp(18px, 2.4vw, 26px);
    line-height: 1.25;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #fff;
}

.athe-scope .athe-quote span {
    display: block;
    margin-top: 6px;
    font-size: clamp(14px, 1.6vw, 17px);
    line-height: 1.55;
    color: rgba(255, 255, 255, .82);
    font-weight: 400;
}

/* ---------- Numbered steps ---------- */
.athe-steps { list-style: none; margin: 0; padding: 0; display: grid; gap: clamp(14px, 1.8vw, 20px); }

.athe-step {
    position: relative;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: clamp(14px, 2vw, 22px);
    align-items: start;
    padding: clamp(18px, 2.2vw, 26px);
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    border-radius: 18px;
    box-shadow: var(--athe-shadow);
}

.athe-step-num {
    display: grid;
    place-items: center;
    width: 54px;
    height: 54px;
    border-radius: 16px;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.02em;
    color: var(--acc);
    background: color-mix(in srgb, var(--acc) 12%, transparent);
    border: 1px solid color-mix(in srgb, var(--acc) 22%, transparent);
}

.athe-scope .athe-step h3 {
    margin: 0;
    font-size: clamp(16.5px, 1.8vw, 19px);
    font-weight: 700;
    letter-spacing: -.015em;
    color: var(--athe-ink);
}

.athe-scope .athe-step p {
    margin: 8px 0 0;
    font-size: 15px;
    line-height: 1.65;
    color: var(--athe-ink-soft);
    font-weight: 400;
}

/* ---------- Chips ---------- */
.athe-chips { display: flex; flex-wrap: wrap; gap: 10px; }

.athe-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 999px;
    font-size: 14.5px;
    font-weight: 600;
    color: var(--athe-ink);
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    box-shadow: var(--athe-shadow);
}

.athe-chip .material-symbols-outlined { font-size: 19px; color: var(--acc); }

/* ---------- FAQ ---------- */
.athe-faq { display: grid; gap: 12px; }

.athe-faq-item {
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    border-radius: 16px;
    box-shadow: var(--athe-shadow);
    overflow: hidden;
}

.athe-scope .athe-faq-item summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: clamp(16px, 2vw, 22px);
    cursor: pointer;
    list-style: none;
    font-size: clamp(15.5px, 1.7vw, 17.5px);
    font-weight: 700;
    line-height: 1.4;
    letter-spacing: -.015em;
    color: var(--athe-ink);
}

.athe-faq-item summary::-webkit-details-marker { display: none; }

.athe-faq-item summary .material-symbols-outlined {
    flex: 0 0 auto;
    font-size: 24px;
    color: var(--acc);
    transition: transform .25s ease;
}

.athe-faq-item[open] summary .material-symbols-outlined { transform: rotate(180deg); }

.athe-scope .athe-faq-answer {
    margin: 0;
    padding: 0 clamp(16px, 2vw, 22px) clamp(18px, 2.2vw, 24px);
    font-size: 15px;
    line-height: 1.7;
    color: var(--athe-ink-soft);
    font-weight: 400;
}

/* ---------- Apply block ---------- */
.athe-apply-grid { display: grid; gap: clamp(20px, 2.8vw, 34px); grid-template-columns: 1fr; }

@media (min-width: 1000px) {
    .athe-apply-grid { grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); align-items: start; }
}

.athe-flyer {
    position: sticky;
    top: 100px;
    overflow: hidden;
    border-radius: 20px;
    border: 1px solid var(--athe-line);
    background: var(--athe-surface);
    box-shadow: var(--athe-shadow-lg);
}

.athe-flyer img { display: block; width: 100%; height: auto; }

.athe-flyer-foot { padding: clamp(16px, 2vw, 22px); display: grid; gap: 12px; }

.athe-scope .athe-flyer-foot p { margin: 0; font-size: 14px; line-height: 1.6; color: var(--athe-ink-muted); font-weight: 400; }

@media (max-width: 999px) { .athe-flyer { position: static; } }

/* ---------- Contact tiles ---------- */
.athe-contact { display: grid; gap: 12px; grid-template-columns: 1fr; }

@media (min-width: 680px) { .athe-contact { grid-template-columns: repeat(2, minmax(0, 1fr)); } }

.athe-contact a,
.athe-contact div {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 14px;
    background: var(--athe-surface);
    border: 1px solid var(--athe-line);
    box-shadow: var(--athe-shadow);
    text-decoration: none;
    transition: transform .2s ease, border-color .2s ease;
}

.athe-contact a:hover { transform: translateY(-3px); border-color: color-mix(in srgb, var(--acc) 40%, transparent); }

.athe-contact .material-symbols-outlined {
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    font-size: 22px;
    color: var(--acc);
    background: color-mix(in srgb, var(--acc) 12%, transparent);
}

.athe-scope .athe-contact-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--athe-ink-muted);
}

.athe-scope .athe-contact-value {
    display: block;
    margin-top: 3px;
    font-size: 15.5px;
    font-weight: 700;
    line-height: 1.35;
    color: var(--athe-ink);
    word-break: break-word;
}

/* ---------- Closing call to action ---------- */
.athe-cta-band {
    padding-block: clamp(56px, 7vw, 100px);
    background: linear-gradient(135deg, var(--athe-navy) 0%, #071837 100%);
    color: #fff;
}

.athe-cta-inner { max-width: 860px; margin-inline: auto; text-align: center; }

.athe-scope .athe-cta-inner h2 {
    margin: 0;
    font-size: clamp(28px, 4.2vw, 46px);
    line-height: 1.12;
    font-weight: 800;
    letter-spacing: -.03em;
    color: #fff;
}

.athe-scope .athe-cta-inner p {
    margin: 18px auto 0;
    max-width: 640px;
    font-size: clamp(15px, 1.7vw, 18px);
    line-height: 1.7;
    color: rgba(255, 255, 255, .8);
    font-weight: 400;
}

.athe-cta-actions { display: flex; flex-wrap: wrap; gap: 14px; justify-content: center; margin-top: clamp(26px, 3.4vw, 38px); }

.athe-cta-tag {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 22px;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: var(--athe-gold-lit);
    background: rgba(255, 255, 255, .08);
    border: 1px solid rgba(255, 255, 255, .18);
}

/* ---------- Hero extras ---------- */
.athe-hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    border-radius: 999px;
    font-size: clamp(13px, 1.4vw, 15px);
    font-weight: 700;
    color: #fff;
    background: rgba(255, 255, 255, .1);
    border: 1px solid rgba(255, 255, 255, .22);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.athe-hero-chip .material-symbols-outlined { font-size: 20px; color: #e9b833; }

@media (prefers-reduced-motion: reduce) {
    .athe-scope * { transition: none !important; animation: none !important; }
}
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">

    <!-- ================= HERO =================
         Same structure, overlay, type scale and animations as every other
         page header on the site (see entry-requirement.php, scholarships.php,
         mature-entrance.php). Only the wording and the two chips below the
         buttons are particular to this page. -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo $e($page_data['hero_image'] ?? 'images/happy_students.png'); ?>"
                 alt="ATHE @ Valley View University" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo $e($page_data['hero_badge'] ?? 'ATHE @ Valley View University'); ?></span>
                </div>

                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $e($page_data['hero_title'] ?? 'Your university journey'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo $e($page_data['hero_subtitle'] ?? 'is not over.'); ?></span>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo $e($page_data['hero_description'] ?? 'Your results are not your destination.'); ?>"
                </p>

                <?php $hero_pathways = $list('pathways'); ?>
                <?php if ($hero_pathways): ?>
                <div class="flex flex-wrap justify-center gap-3 mt-10 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <?php foreach ($hero_pathways as $p): ?>
                    <span class="athe-hero-chip">
                        <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($p['item_icon'] ?: 'school'); ?></span>
                        <?php echo $e($p['item_title']); ?><?php if (!empty($p['item_subtitle'])): ?> &middot; <?php echo $e($p['item_subtitle']); ?><?php endif; ?>
                    </span>
                    <?php endforeach; ?>
                    <span class="athe-hero-chip">
                        <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                        Approximately 6 months
                    </span>
                </div>
                <?php endif; ?>

                <div class="mt-12 flex flex-col sm:flex-row gap-6 justify-center animate-fadeInUp" style="animation-delay: 0.4s;">
                    <a href="<?php echo $e($form_href); ?>" <?php echo $form_attrs; ?>
                       class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-3 animate-pulse-glow">
                        <span class="material-symbols-outlined text-2xl"><?php echo $e($form_icon); ?></span>
                        <?php echo $e($form_label); ?>
                    </a>
                    <a href="<?php echo $e($whatsapp); ?>" target="_blank" rel="noopener"
                       class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl">chat</span>
                        <?php echo $e($page_data['cta_button_text_2'] ?? 'Chat on WhatsApp'); ?>
                    </a>
                </div>

                <p class="mt-8 text-sm font-semibold tracking-wider uppercase text-white/60">
                    In partnership with ATHE, United Kingdom
                </p>
            </div>
        </div>
    </section>

    <div class="athe-scope">

        <!-- ================= OVERVIEW ================= -->
        <?php if ($intro = $sec('intro')): ?>
        <section class="athe-band" id="overview">
            <div class="athe-shell">
                <?php if (count($jump) > 1): ?>
                <nav class="athe-jump" aria-label="Sections on this page">
                    <?php foreach ($jump as $j): ?>
                    <a href="#<?php echo $e($j['id']); ?>">
                        <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($j['icon']); ?></span>
                        <?php echo $e($j['label']); ?>
                    </a>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>

                <div class="athe-head" style="<?php echo $accent('amber-500'); ?>">
                    <?php if (!empty($intro['section_subtitle'])): ?>
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">trending_up</span>
                        <?php echo $e($intro['section_subtitle']); ?>
                    </span>
                    <?php endif; ?>
                    <h2><?php echo $e($intro['section_title']); ?></h2>
                    <?php if ($body = $rich($intro['section_description'])): ?>
                    <p class="athe-lede"><?php echo $body; ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($quote = $first('intro')): ?>
                <div class="athe-quote">
                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($quote['item_icon'] ?: 'alt_route'); ?></span>
                    <div>
                        <strong><?php echo $e($quote['item_title']); ?></strong>
                        <?php if (!empty($quote['item_subtitle'])): ?>
                        <span><?php echo $e($quote['item_subtitle']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= THE JOURNEY ================= -->
        <?php if (($journey = $sec('journey')) && $list('journey')): ?>
        <section class="athe-band athe-band-alt">
            <div class="athe-shell">
                <div class="athe-head is-centred" style="<?php echo $accent('blue-600'); ?>">
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">timeline</span>
                        The pathway
                    </span>
                    <h2><?php echo $e($journey['section_title']); ?></h2>
                    <?php if (!empty($journey['section_subtitle'])): ?>
                    <p class="athe-lede"><?php echo $e($journey['section_subtitle']); ?></p>
                    <?php endif; ?>
                </div>

                <ol class="athe-flow">
                    <?php foreach ($list('journey') as $step): ?>
                    <li class="athe-flow-node" style="<?php echo $accent($step['item_color']); ?>">
                        <span class="athe-flow-dot">
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($step['item_icon'] ?: 'chevron_right'); ?></span>
                        </span>
                        <span class="athe-flow-label"><?php echo $e($step['item_title']); ?></span>
                        <?php if (!empty($step['item_subtitle'])): ?>
                        <span class="athe-flow-sub"><?php echo $e($step['item_subtitle']); ?></span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= CHOOSE YOUR PATHWAY ================= -->
        <?php if (($pathways = $sec('pathways')) && $list('pathways')): ?>
        <section class="athe-band" id="pathways">
            <div class="athe-shell">
                <div class="athe-head is-centred" style="<?php echo $accent('blue-600'); ?>">
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">school</span>
                        Programmes
                    </span>
                    <h2><?php echo $e($pathways['section_title']); ?></h2>
                    <?php if (!empty($pathways['section_subtitle'])): ?>
                    <p class="athe-lede"><?php echo $e($pathways['section_subtitle']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="athe-grid cols-2">
                    <?php foreach ($list('pathways') as $prog): ?>
                    <article class="athe-prog" style="<?php echo $accent($prog['item_color']); ?>">
                        <div class="athe-prog-top">
                            <span class="athe-card-icon">
                                <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($prog['item_icon'] ?: 'school'); ?></span>
                            </span>
                            <h3><?php echo $e($prog['item_title']); ?></h3>
                            <?php if (!empty($prog['item_subtitle'])): ?>
                            <span class="athe-prog-level"><?php echo $e($prog['item_subtitle']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="athe-prog-body">
                            <p><?php echo $rich($prog['item_description']); ?></p>
                            <div class="athe-prog-foot">
                                <?php if (!empty($prog['item_link'])): ?>
                                <a class="athe-btn athe-btn-primary" href="<?php echo $e($href($prog['item_link'])); ?>">
                                    <span class="material-symbols-outlined">arrow_forward</span>
                                    Explore programme
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($prog['item_stat_value'])): ?>
                                <span class="athe-chip">
                                    <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                                    <?php echo $e($prog['item_stat_value']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= WHY ATHE @ VVU ================= -->
        <?php if (($why = $sec('why')) && $list('why')): ?>
        <section class="athe-band athe-band-alt" id="why">
            <div class="athe-shell">
                <div class="athe-head" style="<?php echo $accent('amber-500'); ?>">
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">star</span>
                        Why ATHE
                    </span>
                    <h2><?php echo $e($why['section_title']); ?></h2>
                    <?php if (!empty($why['section_subtitle'])): ?>
                    <p class="athe-lede"><?php echo $e($why['section_subtitle']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="athe-grid cols-3">
                    <?php foreach ($list('why') as $card): ?>
                    <article class="athe-card" style="<?php echo $accent($card['item_color']); ?>">
                        <span class="athe-card-icon">
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($card['item_icon'] ?: 'check_circle'); ?></span>
                        </span>
                        <h3><?php echo $e($card['item_title']); ?></h3>
                        <p><?php echo $rich($card['item_description']); ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= ELIGIBILITY ================= -->
        <?php if ($elig = $sec('eligibility')): ?>
        <section class="athe-band" id="eligibility">
            <div class="athe-shell">
                <div class="athe-detail-head">
                    <div class="athe-head" style="<?php echo $accent('green-600'); ?>margin-bottom:0;">
                        <span class="athe-eyebrow">
                            <span class="material-symbols-outlined" aria-hidden="true">checklist</span>
                            Entry requirements
                        </span>
                        <h2><?php echo $e($elig['section_title']); ?></h2>
                        <?php if (!empty($elig['section_subtitle'])): ?>
                        <p class="athe-lede"><?php echo $e($elig['section_subtitle']); ?></p>
                        <?php endif; ?>
                        <?php if ($note = $rich($elig['section_description'])): ?>
                        <p class="athe-note"><?php echo $note; ?></p>
                        <?php endif; ?>
                        <div class="athe-prog-foot">
                            <a class="athe-btn athe-btn-wa" href="<?php echo $e($whatsapp); ?>" target="_blank" rel="noopener">
                                <span class="material-symbols-outlined">chat</span>
                                Check my eligibility
                            </a>
                        </div>
                    </div>

                    <?php if ($list('eligibility')): ?>
                    <div class="athe-panel" style="<?php echo $accent('green-600'); ?>">
                        <p class="athe-panel-title">You may apply if you have</p>
                        <ul class="athe-bullets">
                            <?php foreach ($list('eligibility') as $req): ?>
                            <li style="<?php echo $accent($req['item_color']); ?>">
                                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                                <span>
                                    <strong><?php echo $e($req['item_title']); ?></strong>
                                    <?php if (!empty($req['item_description'])): ?>
                                    <br><span style="font-weight:400;color:var(--athe-ink-muted);font-size:14px;"><?php echo $e($req['item_description']); ?></span>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= PROGRAMME DETAIL BLOCKS ================= -->
        <?php
        $detail_blocks = [
            ['key' => 'idt',      'anchor' => 'idt',      'alt' => true,  'accent' => 'blue-600',  'eyebrow' => 'ATHE Level 3'],
            ['key' => 'business', 'anchor' => 'business', 'alt' => false, 'accent' => 'amber-500', 'eyebrow' => 'ATHE Level 3'],
        ];
        foreach ($detail_blocks as $block):
            $detail = $sec($block['key']);
            if (!$detail) { continue; }
            $audience = $sec($block['key'] . '_audience');
            $journeyS = $sec($block['key'] . '_journey');
            $reasons  = $sec($block['key'] . '_why') ?: $sec($block['key'] . '_build');
            $reason_key = $sec($block['key'] . '_why') ? $block['key'] . '_why' : $block['key'] . '_build';
            $tagline  = $first($block['key']);
        ?>
        <section class="athe-band<?php echo $block['alt'] ? ' athe-band-alt' : ''; ?>" id="<?php echo $e($block['anchor']); ?>">
            <div class="athe-shell" style="<?php echo $accent($block['accent']); ?>">

                <div class="athe-detail-head">
                    <div class="athe-head" style="margin-bottom:0;">
                        <span class="athe-eyebrow">
                            <span class="material-symbols-outlined" aria-hidden="true">workspace_premium</span>
                            <?php echo $e($block['eyebrow']); ?>
                        </span>
                        <h2><?php echo $e($detail['section_title']); ?></h2>
                        <?php if (!empty($detail['section_subtitle'])): ?>
                        <p class="athe-lede" style="font-weight:600;color:var(--acc);"><?php echo $e($detail['section_subtitle']); ?></p>
                        <?php endif; ?>
                        <?php if ($body = $rich($detail['section_description'])): ?>
                        <p class="athe-lede"><?php echo $body; ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($journeyS && $list($block['key'] . '_journey')): ?>
                    <div class="athe-panel">
                        <p class="athe-panel-title"><?php echo $e($journeyS['section_title']); ?></p>
                        <ol class="athe-flow is-stacked" style="gap:22px;">
                            <?php foreach ($list($block['key'] . '_journey') as $step): ?>
                            <li class="athe-flow-node" style="<?php echo $accent($step['item_color']); ?>padding:14px;">
                                <span class="athe-flow-dot" style="width:38px;height:38px;margin-bottom:8px;">
                                    <span class="material-symbols-outlined" aria-hidden="true" style="font-size:20px;"><?php echo $e($step['item_icon'] ?: 'chevron_right'); ?></span>
                                </span>
                                <span class="athe-flow-label" style="font-size:14px;"><?php echo $e($step['item_title']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="athe-detail-grid">
                    <?php if ($audience && $list($block['key'] . '_audience')): ?>
                    <div class="athe-panel">
                        <p class="athe-panel-title"><?php echo $e($audience['section_title']); ?></p>
                        <?php if (!empty($audience['section_subtitle'])): ?>
                        <p class="athe-lede" style="margin:0 0 16px;font-size:15px;"><?php echo $e($audience['section_subtitle']); ?></p>
                        <?php endif; ?>
                        <ul class="athe-bullets">
                            <?php foreach ($list($block['key'] . '_audience') as $bullet): ?>
                            <li>
                                <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($bullet['item_icon'] ?: 'check'); ?></span>
                                <span><?php echo $e($bullet['item_title']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if ($reasons && $list($reason_key)): ?>
                    <div>
                        <p class="athe-panel-title"><?php echo $e($reasons['section_title']); ?></p>
                        <div class="athe-grid">
                            <?php foreach ($list($reason_key) as $card): ?>
                            <article class="athe-card" style="<?php echo $accent($card['item_color']); ?>padding:18px 20px;">
                                <h3 style="display:flex;align-items:center;gap:10px;">
                                    <span class="material-symbols-outlined" aria-hidden="true" style="color:var(--acc);font-size:22px;"><?php echo $e($card['item_icon'] ?: 'check_circle'); ?></span>
                                    <?php echo $e($card['item_title']); ?>
                                </h3>
                                <p><?php echo $rich($card['item_description']); ?></p>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php
                // The degrees this diploma can lead on to. This is the question
                // every applicant asks next, so it sits with the programme
                // rather than in the general progression section further down.
                $routes = $sec($block['key'] . '_progression');
                if ($routes && $list($block['key'] . '_progression')):
                ?>
                <div class="athe-panel athe-routes" id="<?php echo $e($block['anchor']); ?>-routes">
                    <p class="athe-panel-title"><?php echo $e($routes['section_title']); ?></p>
                    <?php if (!empty($routes['section_subtitle'])): ?>
                    <p class="athe-lede" style="margin:0 0 18px;font-size:15px;"><?php echo $e($routes['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <ul class="athe-route-list">
                        <?php foreach ($list($block['key'] . '_progression') as $route): ?>
                        <li style="<?php echo $accent($route['item_color']); ?>">
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($route['item_icon'] ?: 'school'); ?></span>
                            <span><?php echo $e($route['item_title']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($tagline): ?>
                <div class="athe-quote">
                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($tagline['item_icon'] ?: 'rocket_launch'); ?></span>
                    <div style="flex:1;">
                        <strong><?php echo $e($tagline['item_title']); ?></strong>
                        <?php if (!empty($tagline['item_description'])): ?>
                        <span><?php echo $e($tagline['item_description']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="athe-prog-foot" style="justify-content:flex-start;">
                    <a class="athe-btn athe-btn-gold" href="<?php echo $e($form_href); ?>" <?php echo $form_attrs; ?>>
                        <span class="material-symbols-outlined"><?php echo $e($form_icon); ?></span>
                        <?php echo $form_ready ? 'Apply for ' . $e($detail['section_title']) : $e($form_label); ?>
                    </a>
                    <a class="athe-btn athe-btn-ghost" href="<?php echo $e($whatsapp); ?>" target="_blank" rel="noopener">
                        <span class="material-symbols-outlined">support_agent</span>
                        Chat with an adviser
                    </a>
                </div>
            </div>
        </section>
        <?php endforeach; ?>

        <!-- ================= HOW ATHE WORKS ================= -->
        <?php if ($how = $sec('how_it_works')): ?>
        <section class="athe-band athe-band-alt" id="how-it-works">
            <div class="athe-shell">
                <div class="athe-head" style="<?php echo $accent('indigo-600'); ?>">
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">timeline</span>
                        The process
                    </span>
                    <h2><?php echo $e($how['section_title']); ?></h2>
                    <?php if (!empty($how['section_subtitle'])): ?>
                    <p class="athe-lede" style="font-weight:600;"><?php echo $e($how['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if ($body = $rich($how['section_description'])): ?>
                    <p class="athe-lede"><?php echo $body; ?></p>
                    <?php endif; ?>
                </div>

                <ol class="athe-steps">
                    <?php foreach ($list('how_it_works') as $i => $step): ?>
                    <li class="athe-step" style="<?php echo $accent($step['item_color']); ?>">
                        <span class="athe-step-num"><?php echo $e($step['item_stat_value'] ?: str_pad($i + 1, 2, '0', STR_PAD_LEFT)); ?></span>
                        <div>
                            <h3><?php echo $e($step['item_title']); ?></h3>
                            <p><?php echo $rich($step['item_description']); ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ol>

                <?php if (($why_now = $sec('why_now')) && $list('why_now')): ?>
                <div class="athe-panel" style="margin-top:clamp(26px,3.4vw,40px);<?php echo $accent('amber-500'); ?>">
                    <p class="athe-panel-title"><?php echo $e($why_now['section_title']); ?></p>
                    <?php if (!empty($why_now['section_subtitle'])): ?>
                    <p class="athe-lede" style="margin:0 0 18px;font-size:15px;"><?php echo $e($why_now['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <div class="athe-chips">
                        <?php foreach ($list('why_now') as $chip): ?>
                        <span class="athe-chip" style="<?php echo $accent($chip['item_color']); ?>">
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($chip['item_icon'] ?: 'bolt'); ?></span>
                            <?php echo $e($chip['item_title']); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= PROGRESSION ================= -->
        <?php if ($prog_sec = $sec('progression')): ?>
        <section class="athe-band" id="progression">
            <div class="athe-shell">
                <div class="athe-head" style="<?php echo $accent('teal-600'); ?>">
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">alt_route</span>
                        Progression
                    </span>
                    <h2><?php echo $e($prog_sec['section_title']); ?></h2>
                    <?php if (!empty($prog_sec['section_subtitle'])): ?>
                    <p class="athe-lede" style="font-weight:600;"><?php echo $e($prog_sec['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if ($body = $rich($prog_sec['section_description'])): ?>
                    <p class="athe-lede"><?php echo $body; ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($list('progression')): ?>
                <ol class="athe-flow">
                    <?php foreach ($list('progression') as $step): ?>
                    <li class="athe-flow-node" style="<?php echo $accent($step['item_color']); ?>">
                        <span class="athe-flow-dot">
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($step['item_icon'] ?: 'chevron_right'); ?></span>
                        </span>
                        <span class="athe-flow-label"><?php echo $e($step['item_title']); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <?php endif; ?>

                <?php
                // The concrete answer to "where can ATHE take you" lives with
                // each programme; point readers who jumped straight here at it.
                $route_links = [];
                foreach ([['idt', 'Information & Digital Technologies'], ['business', 'Business']] as $r) {
                    if ($sec($r[0] . '_progression') && $list($r[0] . '_progression')) {
                        $route_links[] = ['anchor' => $r[0] . '-routes', 'label' => $r[1]];
                    }
                }
                ?>
                <?php if ($route_links): ?>
                <div class="athe-prog-foot" style="margin-top:clamp(22px,3vw,34px);">
                    <?php foreach ($route_links as $rl): ?>
                    <a class="athe-btn athe-btn-ghost" href="#<?php echo $e($rl['anchor']); ?>">
                        <span class="material-symbols-outlined">school</span>
                        Degrees after <?php echo $e($rl['label']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="athe-detail-grid" style="margin-top:clamp(26px,3.4vw,42px);">
                    <?php if (($backwards = $sec('progression_backwards')) && $list('progression_backwards')): ?>
                    <div class="athe-panel" style="<?php echo $accent('indigo-600'); ?>">
                        <p class="athe-panel-title"><?php echo $e($backwards['section_title']); ?></p>
                        <?php if (!empty($backwards['section_subtitle'])): ?>
                        <p class="athe-lede" style="margin:0 0 18px;font-size:15px;"><?php echo $e($backwards['section_subtitle']); ?></p>
                        <?php endif; ?>
                        <ol class="athe-flow is-stacked" style="gap:22px;">
                            <?php foreach ($list('progression_backwards') as $step): ?>
                            <li class="athe-flow-node" style="<?php echo $accent($step['item_color']); ?>padding:14px;">
                                <span class="athe-flow-dot" style="width:38px;height:38px;margin-bottom:8px;">
                                    <span class="material-symbols-outlined" aria-hidden="true" style="font-size:20px;"><?php echo $e($step['item_icon'] ?: 'chevron_right'); ?></span>
                                </span>
                                <span class="athe-flow-label" style="font-size:14px;"><?php echo $e($step['item_title']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <?php endif; ?>

                    <?php if (($help = $sec('progression_help')) && $list('progression_help')): ?>
                    <div class="athe-panel" style="<?php echo $accent('teal-600'); ?>">
                        <p class="athe-panel-title"><?php echo $e($help['section_title']); ?></p>
                        <?php if (!empty($help['section_subtitle'])): ?>
                        <p class="athe-lede" style="margin:0 0 18px;font-size:15px;"><?php echo $e($help['section_subtitle']); ?></p>
                        <?php endif; ?>
                        <ul class="athe-bullets">
                            <?php foreach ($list('progression_help') as $bullet): ?>
                            <li>
                                <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($bullet['item_icon'] ?: 'check'); ?></span>
                                <span><?php echo $e($bullet['item_title']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="athe-prog-foot">
                            <a class="athe-btn athe-btn-primary" href="<?php echo $e($whatsapp); ?>" target="_blank" rel="noopener">
                                <span class="material-symbols-outlined">support_agent</span>
                                Talk to an adviser
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= FAQ ================= -->
        <?php if (($faq = $sec('faqs')) && $list('faqs')): ?>
        <section class="athe-band athe-band-alt" id="faqs">
            <div class="athe-shell">
                <div class="athe-head" style="<?php echo $accent('blue-600'); ?>">
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">quiz</span>
                        Questions
                    </span>
                    <h2><?php echo $e($faq['section_title']); ?></h2>
                    <?php if (!empty($faq['section_subtitle'])): ?>
                    <p class="athe-lede"><?php echo $e($faq['section_subtitle']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="athe-faq">
                    <?php foreach ($list('faqs') as $index => $qa): ?>
                    <details class="athe-faq-item" style="<?php echo $accent($qa['item_color']); ?>" <?php echo $index === 0 ? 'open' : ''; ?>>
                        <summary>
                            <span><?php echo $e($qa['item_title']); ?></span>
                            <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                        </summary>
                        <p class="athe-faq-answer"><?php echo $rich($qa['item_description']); ?></p>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= APPLY ================= -->
        <?php if ($apply = $sec('apply')): ?>
        <section class="athe-band" id="apply">
            <div class="athe-shell">
                <div class="athe-head" style="<?php echo $accent('amber-500'); ?>">
                    <span class="athe-eyebrow">
                        <span class="material-symbols-outlined" aria-hidden="true">send</span>
                        How to apply
                    </span>
                    <h2><?php echo $e($apply['section_title']); ?></h2>
                    <?php if (!empty($apply['section_subtitle'])): ?>
                    <p class="athe-lede" style="font-weight:600;"><?php echo $e($apply['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if ($body = $rich($apply['section_description'])): ?>
                    <p class="athe-lede"><?php echo $body; ?></p>
                    <?php endif; ?>
                </div>

                <div class="athe-apply-grid">
                    <div>
                        <ol class="athe-steps">
                            <?php foreach ($list('apply') as $i => $step): ?>
                            <li class="athe-step" style="<?php echo $accent($step['item_color']); ?>">
                                <span class="athe-step-num"><?php echo $e($step['item_stat_value'] ?: str_pad($i + 1, 2, '0', STR_PAD_LEFT)); ?></span>
                                <div>
                                    <h3><?php echo $e($step['item_title']); ?></h3>
                                    <p><?php echo $rich($step['item_description']); ?></p>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ol>

                        <?php if ($contact = $sec('contact')): ?>
                        <div style="margin-top:clamp(26px,3.4vw,40px);">
                            <p class="athe-panel-title" style="<?php echo $accent('green-600'); ?>"><?php echo $e($contact['section_title']); ?></p>
                            <div class="athe-contact">
                                <?php foreach ($list('contact') as $tile): ?>
                                <?php $link = trim((string)$tile['item_link']); ?>
                                <?php if ($link !== ''): ?>
                                <a href="<?php echo $e($href($link)); ?>" style="<?php echo $accent($tile['item_color']); ?>"
                                   <?php echo preg_match('#^https?://#i', $link) ? 'target="_blank" rel="noopener"' : ''; ?>>
                                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($tile['item_icon'] ?: 'call'); ?></span>
                                    <span>
                                        <span class="athe-contact-label"><?php echo $e($tile['item_title']); ?></span>
                                        <span class="athe-contact-value"><?php echo $e($tile['item_description']); ?></span>
                                    </span>
                                </a>
                                <?php else: ?>
                                <div style="<?php echo $accent($tile['item_color']); ?>">
                                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo $e($tile['item_icon'] ?: 'call'); ?></span>
                                    <span>
                                        <span class="athe-contact-label"><?php echo $e($tile['item_title']); ?></span>
                                        <span class="athe-contact-value"><?php echo $e($tile['item_description']); ?></span>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <?php if (!empty($apply['section_image'])): ?>
                        <figure class="athe-flyer" style="<?php echo $accent('blue-600'); ?>margin:0;">
                            <img src="<?php echo $e($href($apply['section_image'])); ?>"
                                 alt="ATHE @ Valley View University programme flyer" loading="lazy">
                            <figcaption class="athe-flyer-foot">
                                <a class="athe-btn athe-btn-gold" href="<?php echo $e($form_href); ?>"
                                   <?php echo $form_attrs; ?>>
                                    <span class="material-symbols-outlined"><?php echo $e($form_icon); ?></span>
                                    <?php echo $e($form_label); ?><?php if ($form_size): ?> <span style="font-weight:600;opacity:.75;">(PDF, <?php echo $e($form_size); ?>)</span><?php endif; ?>
                                </a>
                                <a class="athe-btn athe-btn-wa" href="<?php echo $e($whatsapp); ?>" target="_blank" rel="noopener">
                                    <span class="material-symbols-outlined">chat</span>
                                    <?php echo $e($page_data['cta_button_text_2'] ?? 'Chat on WhatsApp'); ?>
                                </a>
                                <p>Scan the QR code on the flyer, or download the form here, complete it and submit it at the Main Campus or by email.</p>
                            </figcaption>
                        </figure>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ================= CLOSING CALL TO ACTION ================= -->
        <?php if (!empty($page_data['cta_title'])): ?>
        <section class="athe-cta-band">
            <div class="athe-shell">
                <div class="athe-cta-inner">
                    <span class="athe-cta-tag">
                        <span class="material-symbols-outlined" aria-hidden="true" style="font-size:18px;">auto_awesome</span>
                        Start. Progress. Achieve.
                    </span>
                    <h2><?php echo $e($page_data['cta_title']); ?></h2>
                    <?php if (!empty($page_data['cta_subtitle'])): ?>
                    <p><?php echo $e($page_data['cta_subtitle']); ?></p>
                    <?php endif; ?>

                    <div class="athe-cta-actions">
                        <a class="athe-btn athe-btn-gold" href="<?php echo $e($form_href); ?>"
                           <?php echo $form_attrs; ?>>
                            <span class="material-symbols-outlined"><?php echo $e($form_icon); ?></span>
                            <?php echo $e($form_label); ?>
                        </a>
                        <a class="athe-btn athe-btn-wa" href="<?php echo $e($whatsapp); ?>" target="_blank" rel="noopener">
                            <span class="material-symbols-outlined">chat</span>
                            <?php echo $e($page_data['cta_button_text_2'] ?? 'Chat on WhatsApp'); ?>
                        </a>
                    </div>

                    <?php if (!empty($page_data['help_description'])): ?>
                    <p style="margin-top:26px;font-size:14px;color:rgba(255,255,255,.62);">
                        <?php echo $e($page_data['help_description']); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </div><!-- /.athe-scope -->
</main>

<?php
include 'includes/footer.php';
?>
