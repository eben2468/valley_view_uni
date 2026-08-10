<?php
/**
 * Valley View University - Entry Requirements Page
 * Fetching content dynamically from academic_pages_* tables
 */
require_once 'includes/db_connect.php';

$page_key = 'entry_requirements';

// Fetch page content
try {
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ?");
    $stmt->execute([$page_key]);
    $page_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    
    // Fetch sections
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
    $stmt->execute([$page_key]);
    $page_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch items
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY display_order");
    $stmt->execute([$page_key]);
    $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $items_map = [];
    foreach ($all_items as $item) {
        $items_map[$item['section_key']][] = $item;
    }
} catch (PDOException $e) {
    $page_data = [];
    $page_sections = [];
    $items_map = [];
}

$page_title = ($page_data['page_title'] ?? 'Entry Requirements') . " - Valley View University";
$active_page = "admissions";

include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    @keyframes glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); }
        50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.4); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 6s ease-in-out infinite; }
    

    /* ======================================================================
       ENTRY REQUIREMENTS — CONTENT AREA (redesign)
       Scoped under .er-scope so nothing leaks into the hero / CTA / globals.
       ====================================================================== */
    .er-scope {
        --er-ink:        #0f172a;
        --er-ink-soft:   #475569;
        --er-ink-muted:  #64748b;
        --er-surface:    #ffffff;
        --er-canvas:     #f6f8fb;
        --er-line:       #e2e8f0;
        --er-line-soft:  #eef2f7;
        --er-brand:      #1d4ed8;
        --er-shadow:     0 1px 2px rgba(15, 23, 42, .04), 0 12px 28px -18px rgba(15, 23, 42, .28);
        --er-shadow-lg:  0 2px 4px rgba(15, 23, 42, .04), 0 28px 56px -28px rgba(15, 23, 42, .34);
        /* per-card accent, overridden inline */
        --acc:      #1d4ed8;
        --acc-tint: #eff6ff;
        --acc-line: #bfdbfe;
        font-family: 'Open Sans', system-ui, -apple-system, "Segoe UI", sans-serif;
        background: var(--er-canvas);
        color: var(--er-ink);
    }
    /* The global stylesheet hard-codes 15px/#636363 on p, li, a and span.
       Neutralise that inside the content area only; :where() keeps the
       specificity at zero so every rule below still wins. */
    .er-scope :where(p, li, a, span) {
        font-size: inherit;
        line-height: inherit;
        color: inherit;
        font-weight: inherit;
    }

    /* Dark mode: honour both the class strategy and the OS preference. */
    .dark .er-scope,
    .er-scope.er-dark {
        --er-ink:       #f1f5f9;
        --er-ink-soft:  #cbd5e1;
        --er-ink-muted: #94a3b8;
        --er-surface:   #111a2e;
        --er-canvas:    #0b1220;
        --er-line:      #23304a;
        --er-line-soft: #1a2438;
        --er-brand:     #93b4fd;
        --er-shadow:    0 1px 2px rgba(0, 0, 0, .4), 0 12px 28px -18px rgba(0, 0, 0, .7);
        --er-shadow-lg: 0 2px 4px rgba(0, 0, 0, .4), 0 28px 56px -28px rgba(0, 0, 0, .8);
    }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .er-scope {
            --er-ink:       #f1f5f9;
            --er-ink-soft:  #cbd5e1;
            --er-ink-muted: #94a3b8;
            --er-surface:   #111a2e;
            --er-canvas:    #0b1220;
            --er-line:      #23304a;
            --er-line-soft: #1a2438;
            --er-brand:     #93b4fd;
            --er-shadow:    0 1px 2px rgba(0, 0, 0, .4), 0 12px 28px -18px rgba(0, 0, 0, .7);
            --er-shadow-lg: 0 2px 4px rgba(0, 0, 0, .4), 0 28px 56px -28px rgba(0, 0, 0, .8);
        }
    }

    /* Accent tokens. Each accented block carries inline --acc-l (light ink) and
       --acc-d (dark ink); tints are derived so contrast stays predictable. */
    .er-accent {
        --acc:      var(--acc-l, #1d4ed8);
        --acc-tint: color-mix(in srgb, var(--acc-l, #1d4ed8) 10%, #ffffff);
        --acc-line: color-mix(in srgb, var(--acc-l, #1d4ed8) 28%, #ffffff);
    }
    .dark .er-accent, .er-scope.er-dark .er-accent {
        --acc:      var(--acc-d, #93b4fd);
        --acc-tint: color-mix(in srgb, var(--acc-d, #93b4fd) 16%, transparent);
        --acc-line: color-mix(in srgb, var(--acc-d, #93b4fd) 38%, transparent);
    }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .er-accent {
            --acc:      var(--acc-d, #93b4fd);
            --acc-tint: color-mix(in srgb, var(--acc-d, #93b4fd) 16%, transparent);
            --acc-line: color-mix(in srgb, var(--acc-d, #93b4fd) 38%, transparent);
        }
    }

    .er-shell {
        width: 100%;
        /* 100vw guard: the surrounding legacy layout can be wider than the
           viewport on small screens — the content area must not follow it. */
        max-width: min(1240px, 100vw);
        margin-inline: auto;
        padding-inline: clamp(20px, 4vw, 48px);
    }

    /* ---------- Intro ---------- */
    .er-intro { max-width: 736px; padding-block: clamp(56px, 7vw, 96px) clamp(32px, 4vw, 48px); }
    .er-eyebrow {
        display: inline-flex; align-items: center; gap: 8.8px;
        font-size: 12px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
        color: var(--er-brand);
        padding: 7.2px 14.4px; border-radius: 999px;
        background: color-mix(in srgb, var(--er-brand) 10%, transparent);
        border: 1px solid color-mix(in srgb, var(--er-brand) 22%, transparent);
    }
    .er-eyebrow .material-symbols-outlined { font-size: 16.8px; }
    .er-scope .er-intro h2 {
        margin: 24px 0 0;
        font-size: clamp(33.6px, 4.6vw, 54.4px);
        line-height: 1.08;
        letter-spacing: -.03em;
        font-weight: 800;
        color: var(--er-ink);
    }
    .er-scope .er-lede {
        margin: 18.4px 0 0;
        font-size: clamp(16.8px, 1.6vw, 19.2px);
        line-height: 1.7;
        color: var(--er-ink-soft);
        font-weight: 400;
    }

    /* ---------- In-page navigation ---------- */
    .er-jump {
        position: sticky; top: 12px; z-index: 30;
        width: max-content; max-width: 100%;
        margin-bottom: clamp(40px, 5vw, 64px);
        padding: 8px;
        display: flex; gap: 6.4px;
        overflow-x: auto; scrollbar-width: none;
        background: color-mix(in srgb, var(--er-surface) 88%, transparent);
        backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
        border: 1px solid var(--er-line);
        border-radius: 999px;
        box-shadow: var(--er-shadow);
    }
    .er-jump::-webkit-scrollbar { display: none; }
    @media (min-width: 1024px) { .er-jump { top: 104px; } }
    .er-jump a {
        flex: 0 0 auto;
        display: inline-flex; align-items: center; gap: 7.2px;
        padding: 9.6px 16.8px;
        border-radius: 999px;
        font-size: 14px; font-weight: 600; letter-spacing: -.01em;
        color: var(--er-ink-soft); text-decoration: none;
        white-space: nowrap;
        transition: background-color .2s ease, color .2s ease;
    }
    .er-jump a .material-symbols-outlined { font-size: 18.4px; }
    .er-jump a:hover, .er-jump a:focus-visible { background: var(--er-line-soft); color: var(--er-ink); }

    /* ---------- Pathway section ---------- */
    .er-section { padding-bottom: clamp(56px, 7vw, 96px); scroll-margin-top: 120px; }
    .er-section + .er-section { border-top: 1px solid var(--er-line); padding-top: clamp(48px, 6vw, 80px); }
    .er-section-head {
        display: flex; flex-wrap: wrap; align-items: flex-start; gap: 20px;
        margin-bottom: clamp(28px, 3.5vw, 44px);
    }
    .er-section-icon {
        flex: 0 0 auto;
        display: grid; place-items: center;
        width: 52px; height: 52px;
        border-radius: 16px;
        background: var(--acc-tint);
        color: var(--acc);
        border: 1px solid var(--acc-line);
    }
    .er-section-icon .material-symbols-outlined { font-size: 28px; }
    .er-section-headings { flex: 1 1 320px; min-width: 0; }
    .er-kicker {
        display: block;
        font-size: 12.48px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
        color: var(--er-ink-muted);
        margin-bottom: 6.4px;
    }
    .er-scope .er-section-head h3 {
        margin: 0;
        font-size: clamp(25.6px, 3vw, 36px);
        line-height: 1.15; letter-spacing: -.025em; font-weight: 800;
        color: var(--er-ink);
    }
    .er-count {
        flex: 0 0 auto;
        align-self: center;
        font-size: 12.8px; font-weight: 600; color: var(--er-ink-muted);
        padding: 6.4px 13.6px; border-radius: 999px;
        border: 1px solid var(--er-line); background: var(--er-surface);
    }

    /* ---------- Cards ---------- */
    .er-grid {
        display: grid;
        gap: clamp(17.6px, 2vw, 26.4px);
        grid-template-columns: 1fr;
    }
    @media (min-width: 700px)  { .er-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1100px) { .er-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .er-card {
        position: relative;
        display: flex; flex-direction: column;
        padding: clamp(24px, 2.6vw, 32px);
        background: var(--er-surface);
        border: 1px solid var(--er-line);
        border-radius: 24px;
        box-shadow: var(--er-shadow);
        overflow: hidden;
        transition: transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s ease, border-color .28s ease;
    }
    .er-card::before {
        content: ''; position: absolute; inset: 0 0 auto 0; height: 3px;
        background: var(--acc); opacity: .85;
    }
    .er-card:hover, .er-card:focus-within {
        transform: translateY(-4px);
        box-shadow: var(--er-shadow-lg);
        border-color: var(--acc-line);
    }
    .er-card-top { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 18.4px; }
    .er-card-icon {
        display: grid; place-items: center;
        width: 44px; height: 44px; border-radius: 14.4px;
        background: var(--acc-tint); color: var(--acc);
    }
    .er-card-icon .material-symbols-outlined { font-size: 24px; }
    .er-card-index {
        font-size: 12.8px; font-weight: 700; letter-spacing: .1em;
        color: var(--er-ink-muted); font-variant-numeric: tabular-nums;
    }
    .er-scope .er-card h4 {
        margin: 0;
        font-size: clamp(19.2px, 1.8vw, 22.4px);
        line-height: 1.25; letter-spacing: -.02em; font-weight: 700;
        color: var(--er-ink);
    }
    .er-scope .er-card-lede {
        margin: 10.4px 0 0;
        font-size: 15.6px; line-height: 1.65; color: var(--er-ink-soft);
    }
    .er-scope .er-reqs { list-style: none; margin: 21.6px 0 0; padding: 0; display: grid; gap: 9.6px; }
    .er-scope .er-reqs li {
        display: flex; align-items: flex-start; gap: 9.6px;
        font-size: 15.2px; line-height: 1.55; color: var(--er-ink-soft);
    }
    .er-reqs .material-symbols-outlined {
        flex: 0 0 auto; margin-top: 1.6px;
        font-size: 18.4px; color: var(--acc);
    }
    .er-card-foot { margin-top: auto; padding-top: 24px; }
    .er-link {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 14.4px; font-weight: 700; letter-spacing: -.01em;
        color: var(--acc); text-decoration: none;
        border-radius: 8px;
    }
    .er-link .material-symbols-outlined { font-size: 18.4px; transition: transform .25s ease; }
    .er-link:hover .material-symbols-outlined, .er-link:focus-visible .material-symbols-outlined { transform: translateX(4px); }

    /* ---------- Resources ---------- */
    .er-resources { background: var(--er-surface); border-top: 1px solid var(--er-line); }
    .er-resources-inner { padding-block: clamp(56px, 7vw, 96px); }
    .er-resources-head { max-width: 672px; margin-bottom: clamp(32px, 4vw, 48px); }
    .er-scope .er-resources-head h2 {
        margin: 24px 0 0;
        font-size: clamp(28.8px, 3.4vw, 41.6px);
        line-height: 1.12; letter-spacing: -.028em; font-weight: 800; color: var(--er-ink);
    }
    .er-res-card {
        display: flex; flex-direction: column;
        padding: clamp(24px, 2.6vw, 30.4px);
        border: 1px solid var(--er-line);
        border-radius: 24px;
        background: var(--er-canvas);
        text-decoration: none;
        transition: transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s ease, border-color .28s ease, background-color .28s ease;
    }
    .er-res-card:hover, .er-res-card:focus-visible {
        transform: translateY(-4px);
        background: var(--er-surface);
        border-color: var(--acc-line);
        box-shadow: var(--er-shadow-lg);
    }
    .er-res-icon {
        display: grid; place-items: center;
        width: 48px; height: 48px; border-radius: 16px; margin-bottom: 20px;
        background: var(--acc-tint); color: var(--acc);
        transition: background-color .28s ease, color .28s ease;
    }
    .er-res-card:hover .er-res-icon { background: var(--acc); color: #fff; }
    .er-res-icon .material-symbols-outlined { font-size: 25.6px; }
    .er-res-card .er-card-foot { display: block; }
    .er-scope .er-res-card h4 {
        margin: 0; font-size: 19.2px; line-height: 1.3; font-weight: 700;
        letter-spacing: -.02em; color: var(--er-ink);
    }
    .er-scope .er-res-card p { margin: 9.6px 0 0; font-size: 15.2px; line-height: 1.65; color: var(--er-ink-soft); }

    /* ---------- Shared a11y ---------- */
    html { scroll-behavior: smooth; }
    .er-scope .sr-only {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
    }
    .er-scope a:focus-visible,
    .er-scope .er-res-card:focus-visible {
        outline: 2px solid var(--acc);
        outline-offset: 3px;
    }
    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .er-scope *, .er-scope *::before { transition: none !important; animation: none !important; }
        .er-card:hover, .er-res-card:hover { transform: none; }
    }
</style>

<main class="flex-grow bg-[#f8fafc] dark:bg-gray-950">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1523050853063-bd80e27433fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="Entry Requirements" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Admissions'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Entry'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Requirements'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Your journey to excellence begins here.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <?php
    // ------------------------------------------------------------------
    // Content-area helpers (presentation only — data still comes from DB)
    // ------------------------------------------------------------------

    // Accent palette: light-mode ink meets WCAG AA on white; dark-mode ink
    // meets AA on the dark surface.
    $er_accents = [
        'blue'   => ['#1d4ed8', '#93b4fd'],
        'indigo' => ['#4338ca', '#a5b4fc'],
        'purple' => ['#6d28d9', '#c4b5fd'],
        'violet' => ['#6d28d9', '#c4b5fd'],
        'green'  => ['#15803d', '#86efac'],
        'emerald'=> ['#047857', '#6ee7b7'],
        'teal'   => ['#0f766e', '#5eead4'],
        'cyan'   => ['#0e7490', '#67e8f9'],
        'red'    => ['#b91c1c', '#fca5a5'],
        'rose'   => ['#be123c', '#fda4af'],
        'orange' => ['#c2410c', '#fdba74'],
        'amber'  => ['#b45309', '#fcd34d'],
        'yellow' => ['#b45309', '#fcd34d'],
        'slate'  => ['#334155', '#cbd5e1'],
    ];

    /** Inline custom-property pair for a stored colour token like "purple-600". */
    $er_accent_style = function ($color) use ($er_accents) {
        $base = explode('-', (string)($color ?: 'blue'))[0];
        [$light, $dark] = $er_accents[$base] ?? $er_accents['blue'];
        return '--acc-l:' . $light . '; --acc-d:' . $dark . ';';
    };

    /**
     * Split an item into a one-line summary and a checklist.
     * - With a subtitle: description = summary, subtitle = comma-separated criteria.
     * - Without: a single description line is the summary, multiple lines are criteria.
     */
    $er_split_item = function (array $item) {
        $lines = array_values(array_filter(array_map(
            function ($l) { return trim(strip_tags($l)); },
            preg_split('/\r\n|\r|\n/', (string)$item['item_description'])
        ), 'strlen'));
        $subtitle = trim(strip_tags((string)($item['item_subtitle'] ?? '')));

        if ($subtitle !== '') {
            $reqs = array_values(array_filter(array_map('trim', explode(',', $subtitle)), 'strlen'));
            return ['lede' => implode(' ', $lines), 'reqs' => $reqs];
        }
        if (count($lines) > 1) {
            return ['lede' => '', 'reqs' => $lines];
        }
        return ['lede' => $lines[0] ?? '', 'reqs' => []];
    };

    // Pathway sections drive both the content and the in-page jump navigation.
    $er_pathways = array_values(array_filter($page_sections, function ($s) {
        return !in_array($s['section_key'], ['intro', 'resources'], true);
    }));
    $er_intro = null;
    foreach ($page_sections as $s) { if ($s['section_key'] === 'intro') $er_intro = $s; }

    /** Fall back to the first icon defined on a section's items. */
    $er_section_icon = function ($section_key) use ($items_map) {
        foreach ($items_map[$section_key] ?? [] as $item) {
            if (!empty($item['item_icon'])) return strip_tags($item['item_icon']);
        }
        return 'school';
    };
    $er_section_accent = function ($section_key) use ($items_map) {
        return $items_map[$section_key][0]['item_color'] ?? 'blue-600';
    };
    ?>

    <!-- Main Content Section -->
    <section id="pathways" class="er-scope">
        <div class="er-shell">
            <!-- Overview -->
            <div class="er-intro">
                <span class="er-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">explore</span>
                    Overview
                </span>
                <h2><?php echo strip_tags($er_intro['section_title'] ?? 'Pathways to Success'); ?></h2>
                <?php if (!empty($er_intro['section_subtitle'])): ?>
                <p class="er-lede"><?php echo nl2br(strip_tags($er_intro['section_subtitle'])); ?></p>
                <?php endif; ?>
            </div>

            <!-- In-page navigation -->
            <?php if (count($er_pathways) > 1): ?>
            <nav class="er-jump" aria-label="Entry requirement pathways">
                <?php foreach ($er_pathways as $section): ?>
                <a href="#pathway-<?php echo urlencode($section['section_key']); ?>">
                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo $er_section_icon($section['section_key']); ?></span>
                    <?php echo strip_tags($section['section_title']); ?>
                </a>
                <?php endforeach; ?>
                <?php if (isset($items_map['resources'])): ?>
                <a href="#admission-resources">
                    <span class="material-symbols-outlined" aria-hidden="true">folder_open</span>
                    Resources
                </a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

            <?php foreach ($er_pathways as $index => $section): ?>
            <?php $sec_key = $section['section_key']; ?>
            <section id="pathway-<?php echo urlencode($sec_key); ?>"
                     class="er-section er-accent"
                     style="<?php echo $er_accent_style($er_section_accent($sec_key)); ?>"
                     aria-labelledby="heading-<?php echo urlencode($sec_key); ?>">
                <header class="er-section-head">
                    <span class="er-section-icon" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo $er_section_icon($sec_key); ?></span>
                    </span>
                    <div class="er-section-headings">
                        <span class="er-kicker">
                            <?php printf('%02d', $index + 1); ?> · <?php echo strip_tags($section['section_title']); ?>
                        </span>
                        <h3 id="heading-<?php echo urlencode($sec_key); ?>">
                            <?php echo strip_tags($section['section_subtitle'] ?: $section['section_title']); ?>
                        </h3>
                    </div>
                    <?php $sec_count = count($items_map[$sec_key] ?? []); ?>
                    <?php if ($sec_count): ?>
                    <span class="er-count"><?php echo $sec_count; ?> <?php echo $sec_count === 1 ? 'pathway' : 'pathways'; ?></span>
                    <?php endif; ?>
                </header>

                <div class="er-grid">
                    <?php foreach ($items_map[$sec_key] ?? [] as $i => $item): ?>
                    <?php $parts = $er_split_item($item); ?>
                    <article class="er-card er-accent" style="<?php echo $er_accent_style($item['item_color'] ?? 'blue-600'); ?>">
                        <div class="er-card-top">
                            <span class="er-card-icon" aria-hidden="true">
                                <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: $er_section_icon($sec_key)); ?></span>
                            </span>
                            <span class="er-card-index" aria-hidden="true"><?php printf('%02d', $i + 1); ?></span>
                        </div>

                        <h4><?php echo strip_tags($item['item_title']); ?></h4>

                        <?php if ($parts['lede'] !== ''): ?>
                        <p class="er-card-lede"><?php echo $parts['lede']; ?></p>
                        <?php endif; ?>

                        <?php if ($parts['reqs']): ?>
                        <ul class="er-reqs">
                            <?php foreach ($parts['reqs'] as $req): ?>
                            <li>
                                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                                <span><?php echo $req; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>

                        <?php $link = trim(strip_tags((string)($item['item_link'] ?? ''))); ?>
                        <?php if ($link !== '' && $link !== '#'): ?>
                        <div class="er-card-foot">
                            <a class="er-link" href="<?php echo htmlspecialchars(str_replace(' ', '%20', $link), ENT_QUOTES); ?>">
                                View programmes
                                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                                <span class="sr-only">for <?php echo strip_tags($item['item_title']); ?></span>
                            </a>
                        </div>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- Resources Section -->
    <?php
    $resource_section = null;
    foreach ($page_sections as $s) if ($s['section_key'] === 'resources') $resource_section = $s;
    if ($resource_section && isset($items_map['resources'])):
    ?>
    <section id="admission-resources" class="er-scope er-resources">
        <div class="er-shell er-resources-inner">
            <div class="er-resources-head">
                <span class="er-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">folder_open</span>
                    Tools &amp; Guides
                </span>
                <h2><?php echo strip_tags($resource_section['section_title']); ?></h2>
                <?php if (!empty($resource_section['section_subtitle'])): ?>
                <p class="er-lede"><?php echo strip_tags($resource_section['section_subtitle']); ?></p>
                <?php endif; ?>
            </div>

            <div class="er-grid">
                <?php foreach ($items_map['resources'] as $item): ?>
                <?php
                    $res_link = trim(strip_tags((string)($item['item_link'] ?? ''))) ?: '#';
                    $is_download = (bool)preg_match('/\.(pdf|docx?|xlsx?|zip)$/i', $res_link);
                    // Stored paths may contain spaces (e.g. "uploads/Student Handbook.pdf").
                    $res_href = str_replace(' ', '%20', $res_link);
                ?>
                <a class="er-res-card er-accent"
                   style="<?php echo $er_accent_style($item['item_color'] ?? 'blue-600'); ?>"
                   href="<?php echo htmlspecialchars($res_href, ENT_QUOTES); ?>"
                   <?php echo $is_download ? 'target="_blank" rel="noopener"' : ''; ?>>
                    <span class="er-res-icon" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'description'); ?></span>
                    </span>
                    <h4><?php echo strip_tags($item['item_title']); ?></h4>
                    <p><?php echo strip_tags($item['item_description']); ?></p>
                    <span class="er-card-foot">
                        <span class="er-link">
                            <?php echo $is_download ? 'Download' : 'Open'; ?>
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo $is_download ? 'download' : 'arrow_forward'; ?></span>
                        </span>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-40 overflow-hidden bg-blue-600">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-700 to-blue-900 opacity-90"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] animate-slow-zoom"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-5xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed font-semibold italic">
                    "<?php echo strip_tags($page_data['cta_subtitle']); ?>"
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'apply.php'); ?>" class="group px-12 py-6 bg-white text-blue-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-2xl flex items-center justify-center gap-4">
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Now'); ?>
                        <span class="material-symbols-outlined text-3xl transition-transform group-hover:translate-x-1">trending_flat</span>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-2xl transition-all backdrop-blur-xl border-2 border-white/30 transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        Contact Admissions
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Design Accents -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
