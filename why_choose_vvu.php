<?php
$page_title = "Why Choose Valley View University";
$active_page = "admissions";
require_once 'includes/db_connect.php';
include 'includes/header.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'why_choose_vvu'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'why_choose_vvu' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_raw as $s) {
    $sections[$s['section_key']] = $s;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'why_choose_vvu' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($items_raw as $i) {
    $items[$i['section_key']][] = $i;
}
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
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    
    .glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .feature-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .feature-card:hover { 
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
    }
    
    .text-gradient {
        background: linear-gradient(to right, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-spacing {
        padding-top: 10rem;
        padding-bottom: 10rem;
    }

    /* ======================================================================
       WHY CHOOSE VVU — CONTENT AREA (redesign)
       Scoped under .wcv-scope so nothing leaks into the hero / CTA / globals.
       Note: bootstrap.css sets html{font-size:10px}, so this block uses px.
       ====================================================================== */
    .wcv-scope {
        --wcv-ink:       #0f172a;
        --wcv-ink-soft:  #475569;
        --wcv-ink-muted: #64748b;
        --wcv-surface:   #ffffff;
        --wcv-canvas:    #f6f8fb;
        --wcv-line:      #e2e8f0;
        --wcv-brand:     #1d4ed8;
        --wcv-navy:      #0e1c37;
        --wcv-shadow:    0 1px 2px rgba(15, 23, 42, .04), 0 14px 30px -18px rgba(15, 23, 42, .3);
        --wcv-shadow-lg: 0 2px 4px rgba(15, 23, 42, .04), 0 34px 64px -30px rgba(15, 23, 42, .36);
        --acc: #1d4ed8;
        font-family: 'Open Sans', system-ui, -apple-system, "Segoe UI", sans-serif;
        color: var(--wcv-ink);
    }
    /* The global stylesheet hard-codes 15px/#636363 on p, li, a and span.
       :where() keeps this reset at zero specificity so every rule below wins. */
    .wcv-scope :where(p, li, a, span) {
        font-size: inherit; line-height: inherit; color: inherit; font-weight: inherit;
    }
    .dark .wcv-scope {
        --wcv-ink:       #f1f5f9;
        --wcv-ink-soft:  #cbd5e1;
        --wcv-ink-muted: #94a3b8;
        --wcv-surface:   #111a2e;
        --wcv-canvas:    #0b1220;
        --wcv-line:      #23304a;
        --wcv-brand:     #93b4fd;
        --wcv-shadow:    0 1px 2px rgba(0, 0, 0, .4), 0 14px 30px -18px rgba(0, 0, 0, .7);
        --wcv-shadow-lg: 0 2px 4px rgba(0, 0, 0, .4), 0 34px 64px -30px rgba(0, 0, 0, .8);
    }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .wcv-scope {
            --wcv-ink:       #f1f5f9;
            --wcv-ink-soft:  #cbd5e1;
            --wcv-ink-muted: #94a3b8;
            --wcv-surface:   #111a2e;
            --wcv-canvas:    #0b1220;
            --wcv-line:      #23304a;
            --wcv-brand:     #93b4fd;
        }
    }

    /* Accent tokens: accented blocks carry inline --acc-l / --acc-d. */
    .wcv-accent { --acc: var(--acc-l, #1d4ed8); }
    .dark .wcv-accent { --acc: var(--acc-d, #93b4fd); }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .wcv-accent { --acc: var(--acc-d, #93b4fd); }
    }

    .wcv-shell {
        width: 100%;
        /* 100vw guard: the legacy layout can be wider than the viewport on
           small screens — the content area must not follow it. */
        max-width: min(1240px, 100vw);
        margin-inline: auto;
        padding-inline: clamp(16px, 4vw, 40px);
    }
    .wcv-band { padding-block: clamp(56px, 8vw, 104px); background: var(--wcv-surface); }
    .wcv-band-alt { background: var(--wcv-canvas); }
    .wcv-band + .wcv-band { border-top: 1px solid var(--wcv-line); }

    /* ---------- Section headers ---------- */
    .wcv-head { max-width: 760px; margin-bottom: clamp(28px, 4vw, 48px); }
    .wcv-eyebrow {
        display: inline-flex; align-items: center; gap: 9px;
        font-size: 12px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
        color: var(--acc);
        padding: 7px 14px; border-radius: 999px;
        background: color-mix(in srgb, var(--acc) 10%, transparent);
        border: 1px solid color-mix(in srgb, var(--acc) 22%, transparent);
    }
    .wcv-eyebrow .material-symbols-outlined { font-size: 17px; }
    .wcv-scope .wcv-head h2 {
        margin: 20px 0 0;
        font-size: clamp(26px, 3.4vw, 40px);
        line-height: 1.15; letter-spacing: -.025em; font-weight: 700;
        color: var(--wcv-ink);
    }
    .wcv-scope .wcv-lede {
        margin: 14px 0 0;
        font-size: clamp(16px, 1.6vw, 18px);
        line-height: 1.7; color: var(--wcv-ink-soft);
    }

    /* ---------- In-page navigation ---------- */
    .wcv-jump {
        position: sticky; top: 12px; z-index: 30;
        width: max-content; max-width: 100%;
        margin-bottom: clamp(32px, 5vw, 56px);
        padding: 6px;
        display: flex; gap: 4px;
        overflow-x: auto; scrollbar-width: none;
        background: color-mix(in srgb, var(--wcv-surface) 88%, transparent);
        backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
        border: 1px solid var(--wcv-line);
        border-radius: 999px;
        box-shadow: var(--wcv-shadow);
    }
    .wcv-jump::-webkit-scrollbar { display: none; }
    @media (min-width: 1024px) { .wcv-jump { top: 104px; } }
    .wcv-jump a {
        flex: 0 0 auto;
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 999px;
        font-size: 14px; font-weight: 600; letter-spacing: -.01em;
        color: var(--wcv-ink-soft); text-decoration: none; white-space: nowrap;
        transition: background-color .2s ease, color .2s ease;
    }
    .wcv-jump a .material-symbols-outlined { font-size: 18px; }
    .wcv-jump a:hover, .wcv-jump a:focus-visible { background: var(--wcv-canvas); color: var(--wcv-ink); }

    /* ---------- Grids & cards ---------- */
    .wcv-grid { display: grid; gap: clamp(16px, 2vw, 24px); grid-template-columns: 1fr; }
    @media (min-width: 700px) {
        .wcv-grid.wcv-cols-2, .wcv-grid.wcv-cols-3, .wcv-grid.wcv-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (min-width: 1080px) {
        .wcv-grid.wcv-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .wcv-grid.wcv-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .wcv-card {
        display: flex; flex-direction: column;
        padding: clamp(22px, 2.4vw, 30px);
        background: var(--wcv-surface);
        border: 1px solid var(--wcv-line);
        border-radius: 20px;
        box-shadow: var(--wcv-shadow);
        transition: transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s ease, border-color .28s ease;
    }
    .wcv-band-alt .wcv-card { background: var(--wcv-surface); }
    .wcv-card:hover, .wcv-card:focus-within {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--acc) 35%, transparent);
        box-shadow: var(--wcv-shadow-lg);
    }
    .wcv-card-icon {
        display: grid; place-items: center;
        width: 46px; height: 46px; border-radius: 14px; margin-bottom: 20px;
        background: color-mix(in srgb, var(--acc) 12%, transparent);
        color: var(--acc);
    }
    .wcv-card-icon .material-symbols-outlined { font-size: 25px; }
    .wcv-scope .wcv-card h3 {
        margin: 0;
        font-size: clamp(17px, 1.6vw, 20px);
        line-height: 1.3; letter-spacing: -.02em; font-weight: 700;
        color: var(--wcv-ink);
    }
    .wcv-scope .wcv-card p {
        margin: 10px 0 0;
        font-size: 15px; line-height: 1.7; color: var(--wcv-ink-soft);
    }

    /* ---------- Two-column split (mission / student life) ---------- */
    .wcv-split { display: grid; gap: clamp(28px, 4vw, 56px); align-items: center; }
    @media (min-width: 940px) { .wcv-split { grid-template-columns: 1fr 1fr; } }
    .wcv-split-media { display: grid; gap: 16px; grid-template-columns: 1fr 1fr; }
    .wcv-split-media img {
        width: 100%; height: clamp(200px, 26vw, 300px); object-fit: cover;
        border-radius: 20px; box-shadow: var(--wcv-shadow);
    }
    .wcv-split-media img:nth-child(2) { margin-top: clamp(20px, 4vw, 48px); }

    /* Compact item rows used beside body copy */
    .wcv-rows { display: grid; gap: 12px; margin-top: clamp(24px, 3vw, 36px); }
    .wcv-row {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 16px 18px;
        background: var(--wcv-canvas);
        border: 1px solid var(--wcv-line);
        border-radius: 16px;
    }
    .wcv-row-icon {
        flex: 0 0 auto; display: grid; place-items: center;
        width: 40px; height: 40px; border-radius: 12px;
        background: color-mix(in srgb, var(--acc) 12%, transparent);
        color: var(--acc);
    }
    .wcv-row-icon .material-symbols-outlined { font-size: 22px; }
    .wcv-scope .wcv-row h4 {
        margin: 0; font-size: 16px; line-height: 1.35; font-weight: 700;
        letter-spacing: -.01em; color: var(--wcv-ink);
    }
    .wcv-scope .wcv-row p { margin: 4px 0 0; font-size: 14px; line-height: 1.6; color: var(--wcv-ink-soft); }

    /* ---------- Feature panel (sustainability) ---------- */
    .wcv-feature {
        background: var(--wcv-navy);
        border-radius: 28px;
        padding: clamp(28px, 4vw, 60px);
        color: #e8eefc;
        display: grid; gap: clamp(28px, 4vw, 56px); align-items: center;
    }
    @media (min-width: 940px) { .wcv-feature { grid-template-columns: 1.05fr .95fr; } }
    .wcv-feature .wcv-eyebrow {
        color: #86efac;
        background: rgba(134, 239, 172, .12);
        border-color: rgba(134, 239, 172, .3);
    }
    .wcv-scope .wcv-feature h2 { color: #ffffff; }
    .wcv-scope .wcv-feature .wcv-lede { color: #b9c6e4; }
    .wcv-feature .wcv-head { margin-bottom: 0; }
    .wcv-feature-list { display: grid; gap: 12px; margin-top: clamp(24px, 3vw, 34px); }
    .wcv-feature-item {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 16px 18px;
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 16px;
    }
    .wcv-feature-item .wcv-row-icon {
        background: rgba(134, 239, 172, .16); color: #86efac;
    }
    .wcv-scope .wcv-feature-item h4 { margin: 0; font-size: 16px; font-weight: 700; color: #ffffff; }
    .wcv-scope .wcv-feature-item p { margin: 4px 0 0; font-size: 14px; line-height: 1.6; color: #b9c6e4; }
    .wcv-feature-media img {
        width: 100%; height: clamp(260px, 34vw, 420px); object-fit: cover;
        border-radius: 22px;
    }

    /* ---------- Shared a11y ---------- */
    .wcv-band { scroll-margin-top: 120px; }
    .wcv-scope a:focus-visible { outline: 2px solid var(--acc); outline-offset: 3px; }
    .wcv-feature a:focus-visible { outline-color: #86efac; }
    @media (prefers-reduced-motion: reduce) {
        .wcv-scope * { transition: none !important; animation: none !important; }
        .wcv-card:hover { transform: none; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'Education-Website-and-AdminPanel/images/pro-bg.jpg'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-7xl mx-auto text-center">
                <?php if ($page_data['hero_badge']): ?>
                <div class="inline-flex items-center gap-4 px-12 py-5 mb-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-4 h-4 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge']); ?></span>
                </div>
                <?php endif; ?>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-12 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $page_data['hero_title']; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-6"><?php echo $page_data['hero_subtitle']; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-5xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description']); ?>"
                </p>

                <div class="mt-12 flex flex-wrap justify-center gap-6 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="apply.php" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-black rounded-xl transition-all transform hover:scale-105 shadow-2xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-blue-900">how_to_reg</span>
                        Apply Now
                    </a>
                    <a href="#discover" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-lg font-black rounded-xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-2xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">explore</span>
                        Explore More
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
    $wcv_accents = [
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
    $wcv_accent_style = function ($color) use ($wcv_accents) {
        $base = explode('-', (string)($color ?: 'blue'))[0];
        [$light, $dark] = $wcv_accents[$base] ?? $wcv_accents['blue'];
        return '--acc-l:' . $light . '; --acc-d:' . $dark . ';';
    };

    // Anchor id, jump-nav label and eyebrow icon per section.
    $wcv_meta = [
        'mission'      => ['discover',     'Mission',      'school'],
        'achievements' => ['achievements', 'Achievements', 'military_tech'],
        'eco_friendly' => ['sustainability','Sustainability','eco'],
        'facilities'   => ['facilities',   'Facilities',   'apartment'],
        'student_life' => ['student-life', 'Student life', 'diversity_3'],
    ];
    // Only advertise sections that actually have content.
    $wcv_nav = [];
    foreach ($wcv_meta as $skey => $meta) {
        if (isset($sections[$skey])) $wcv_nav[$skey] = $meta;
    }
    ?>

    <div class="wcv-scope">

    <!-- Mission -->
    <?php if (isset($sections['mission'])): ?>
    <?php $sec = $sections['mission']; ?>
    <section id="discover" class="wcv-band">
        <div class="wcv-shell">
            <?php if (count($wcv_nav) > 1): ?>
            <nav class="wcv-jump" aria-label="Sections on this page">
                <?php foreach ($wcv_nav as $meta): ?>
                <a href="#<?php echo $meta[0]; ?>">
                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo $meta[2]; ?></span>
                    <?php echo $meta[1]; ?>
                </a>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>

            <div class="wcv-head wcv-accent">
                <span class="wcv-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">school</span>
                    <?php echo strip_tags($sec['section_title']); ?>
                </span>
                <?php if (!empty($sec['section_subtitle'])): ?>
                <h2><?php echo strip_tags($sec['section_subtitle']); ?></h2>
                <?php endif; ?>
                <?php if (!empty($sec['section_description'])): ?>
                <p class="wcv-lede"><?php echo strip_tags($sec['section_description']); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($items['mission'])): ?>
            <div class="wcv-grid wcv-cols-<?php echo max(1, min(count($items['mission']), 3)); ?>">
                <?php foreach ($items['mission'] as $item): ?>
                <article class="wcv-card wcv-accent" style="<?php echo $wcv_accent_style($item['item_color']); ?>">
                    <?php if (!empty($item['item_icon'])): ?>
                    <span class="wcv-card-icon" aria-hidden="true">
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

    <!-- Achievements -->
    <?php if (isset($sections['achievements'])): ?>
    <?php $sec = $sections['achievements']; ?>
    <section id="achievements" class="wcv-band wcv-band-alt">
        <div class="wcv-shell">
            <div class="wcv-head wcv-accent">
                <span class="wcv-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">military_tech</span>
                    Track record
                </span>
                <h2><?php echo strip_tags($sec['section_title']); ?></h2>
                <?php if (!empty($sec['section_subtitle'])): ?>
                <p class="wcv-lede"><?php echo strip_tags($sec['section_subtitle']); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($items['achievements'])): ?>
            <div class="wcv-grid wcv-cols-3">
                <?php foreach ($items['achievements'] as $item): ?>
                <article class="wcv-card wcv-accent" style="<?php echo $wcv_accent_style($item['item_color']); ?>">
                    <?php if (!empty($item['item_icon'])): ?>
                    <span class="wcv-card-icon" aria-hidden="true">
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

    <!-- Sustainability -->
    <?php if (isset($sections['eco_friendly'])): ?>
    <?php $sec = $sections['eco_friendly']; ?>
    <section id="sustainability" class="wcv-band">
        <div class="wcv-shell">
            <div class="wcv-feature">
                <div>
                    <div class="wcv-head">
                        <?php if (!empty($sec['section_subtitle'])): ?>
                        <span class="wcv-eyebrow">
                            <span class="material-symbols-outlined" aria-hidden="true">eco</span>
                            <?php echo strip_tags($sec['section_subtitle']); ?>
                        </span>
                        <?php endif; ?>
                        <h2><?php echo strip_tags($sec['section_title']); ?></h2>
                        <?php if (!empty($sec['section_description'])): ?>
                        <p class="wcv-lede"><?php echo strip_tags($sec['section_description']); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($items['eco_friendly'])): ?>
                    <div class="wcv-feature-list">
                        <?php foreach ($items['eco_friendly'] as $item): ?>
                        <div class="wcv-feature-item">
                            <span class="wcv-row-icon" aria-hidden="true">
                                <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'eco'); ?></span>
                            </span>
                            <div>
                                <h4><?php echo strip_tags($item['item_title']); ?></h4>
                                <?php if (!empty($item['item_description'])): ?>
                                <p><?php echo strip_tags($item['item_description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($sec['section_image'])): ?>
                <div class="wcv-feature-media">
                    <img src="<?php echo htmlspecialchars(strip_tags($sec['section_image']), ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars(strip_tags($sec['section_title']), ENT_QUOTES); ?>" loading="lazy">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Facilities -->
    <?php if (isset($sections['facilities'])): ?>
    <?php $sec = $sections['facilities']; ?>
    <section id="facilities" class="wcv-band wcv-band-alt">
        <div class="wcv-shell">
            <div class="wcv-head wcv-accent">
                <span class="wcv-eyebrow">
                    <span class="material-symbols-outlined" aria-hidden="true">apartment</span>
                    On campus
                </span>
                <h2><?php echo strip_tags($sec['section_title']); ?></h2>
                <?php if (!empty($sec['section_subtitle'])): ?>
                <p class="wcv-lede"><?php echo strip_tags($sec['section_subtitle']); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($items['facilities'])): ?>
            <div class="wcv-grid wcv-cols-<?php echo max(1, min(count($items['facilities']), 4)); ?>">
                <?php foreach ($items['facilities'] as $item): ?>
                <article class="wcv-card wcv-accent" style="<?php echo $wcv_accent_style($item['item_color']); ?>">
                    <?php if (!empty($item['item_icon'])): ?>
                    <span class="wcv-card-icon" aria-hidden="true">
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

    <!-- Student life -->
    <?php if (isset($sections['student_life'])): ?>
    <?php $sec = $sections['student_life']; ?>
    <section id="student-life" class="wcv-band">
        <div class="wcv-shell">
            <div class="wcv-split">
                <?php if (!empty($sec['section_image']) || !empty($sec['section_image_2'])): ?>
                <div class="wcv-split-media">
                    <?php if (!empty($sec['section_image'])): ?>
                    <img src="<?php echo htmlspecialchars(strip_tags($sec['section_image']), ENT_QUOTES); ?>"
                         alt="Campus life at Valley View University" loading="lazy">
                    <?php endif; ?>
                    <?php if (!empty($sec['section_image_2'])): ?>
                    <img src="<?php echo htmlspecialchars(strip_tags($sec['section_image_2']), ENT_QUOTES); ?>"
                         alt="Students at Valley View University" loading="lazy">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div>
                    <div class="wcv-head wcv-accent">
                        <span class="wcv-eyebrow">
                            <span class="material-symbols-outlined" aria-hidden="true">diversity_3</span>
                            Student life
                        </span>
                        <h2><?php echo strip_tags($sec['section_title']); ?></h2>
                        <?php if (!empty($sec['section_description'])): ?>
                        <p class="wcv-lede"><?php echo strip_tags($sec['section_description']); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($items['student_life'])): ?>
                    <div class="wcv-rows">
                        <?php foreach ($items['student_life'] as $item): ?>
                        <div class="wcv-row wcv-accent" style="<?php echo $wcv_accent_style($item['item_color']); ?>">
                            <span class="wcv-row-icon" aria-hidden="true">
                                <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'check'); ?></span>
                            </span>
                            <div>
                                <h4><?php echo strip_tags($item['item_title']); ?></h4>
                                <?php if (!empty($item['item_description'])): ?>
                                <p><?php echo strip_tags($item['item_description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    </div><!-- /.wcv-scope -->

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-10 leading-tight">
                    <?php echo $page_data['cta_title']; ?> <br><span class="text-lg sm:text-xl md:text-2xl lg:text-4xl text-white font-medium"><?php echo $page_data['cta_subtitle']; ?></span>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-white mb-20 max-w-5xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['hero_description'] ?? 'Join the thousands of successful graduates who chose Valley View University for their future.'); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-8 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-blue-900">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-white">support_agent</span>
                        Talk to an Advisor
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>