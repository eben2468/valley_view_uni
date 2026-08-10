<?php
/**
 * Valley View University - Caution to Applicants Page
 * Fetching content dynamically from academic_pages_* tables
 */
require_once 'includes/db_connect.php';

$page_key = 'caution_to_applicants';

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

$page_title = ($page_data['page_title'] ?? 'Caution to Applicants') . " - Valley View University";
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
    
    .caution-card { transition: all 0.4s ease; }
    .caution-card:hover { 
        transform: translateY(-15px);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
    }
    
    .text-gradient {
        background: linear-gradient(to right, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ======================================================================
       CAUTION TO APPLICANTS — CONTENT AREA (redesign)
       Scoped under .cau-scope so nothing leaks into the hero / CTA / globals.
       Note: bootstrap.css sets html{font-size:10px}, so this block uses px.
       ====================================================================== */
    .cau-scope {
        --cau-ink:       #0f172a;
        --cau-ink-soft:  #475569;
        --cau-ink-muted: #64748b;
        --cau-surface:   #ffffff;
        --cau-canvas:    #f6f8fb;
        --cau-line:      #e2e8f0;
        --cau-brand:     #1d4ed8;
        --cau-brand-ink: #ffffff;
        --cau-danger:      #b42318;
        --cau-danger-tint: #fdeceb;
        --cau-danger-line: #f6c9c4;
        --cau-shadow:    0 1px 2px rgba(15, 23, 42, .04), 0 14px 30px -18px rgba(15, 23, 42, .3);
        --cau-shadow-lg: 0 2px 4px rgba(15, 23, 42, .04), 0 34px 64px -30px rgba(15, 23, 42, .36);
        /* per-card accent, set inline */
        --acc: #1d4ed8;
        font-family: 'Open Sans', system-ui, -apple-system, "Segoe UI", sans-serif;
        background: var(--cau-canvas);
        color: var(--cau-ink);
    }
    /* The global stylesheet hard-codes 15px/#636363 on p, li, a and span.
       :where() keeps this reset at zero specificity so every rule below wins. */
    .cau-scope :where(p, li, a, span) {
        font-size: inherit; line-height: inherit; color: inherit; font-weight: inherit;
    }
    .dark .cau-scope {
        --cau-ink:       #f1f5f9;
        --cau-ink-soft:  #cbd5e1;
        --cau-ink-muted: #94a3b8;
        --cau-surface:   #111a2e;
        --cau-canvas:    #0b1220;
        --cau-line:      #23304a;
        --cau-brand:     #93b4fd;
        --cau-brand-ink: #0b1220;
        --cau-danger:      #fca5a5;
        --cau-danger-tint: rgba(252, 165, 165, .12);
        --cau-danger-line: rgba(252, 165, 165, .32);
        --cau-shadow:    0 1px 2px rgba(0, 0, 0, .4), 0 14px 30px -18px rgba(0, 0, 0, .7);
        --cau-shadow-lg: 0 2px 4px rgba(0, 0, 0, .4), 0 34px 64px -30px rgba(0, 0, 0, .8);
    }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .cau-scope {
            --cau-ink:       #f1f5f9;
            --cau-ink-soft:  #cbd5e1;
            --cau-ink-muted: #94a3b8;
            --cau-surface:   #111a2e;
            --cau-canvas:    #0b1220;
            --cau-line:      #23304a;
            --cau-brand:     #93b4fd;
            --cau-brand-ink: #0b1220;
            --cau-danger:      #fca5a5;
            --cau-danger-tint: rgba(252, 165, 165, .12);
            --cau-danger-line: rgba(252, 165, 165, .32);
        }
    }

    /* Accent tokens: each accented block carries inline --acc-l / --acc-d. */
    .cau-accent { --acc: var(--acc-l, #1d4ed8); }
    .dark .cau-accent { --acc: var(--acc-d, #93b4fd); }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .cau-accent { --acc: var(--acc-d, #93b4fd); }
    }

    .cau-shell {
        width: 100%;
        /* 100vw guard: the legacy layout can be wider than the viewport on
           small screens — the content area must not follow it. */
        max-width: min(1180px, 100vw);
        margin-inline: auto;
        padding-inline: clamp(16px, 4vw, 40px);
    }
    .cau-band { padding-block: clamp(48px, 7vw, 88px); }

    /* ---------- Official warning ----------
       Styled as a posted notice rather than a tinted alert box: plain paper
       surface, a hazard-striped rule down the left edge, a ruled small-caps
       label and the statement set in the institutional serif. */
    .cau-alert {
        position: relative;
        display: flex; gap: clamp(16px, 2.4vw, 28px);
        padding: clamp(24px, 3.2vw, 38px) clamp(22px, 3vw, 40px)
                 clamp(24px, 3.2vw, 38px) clamp(30px, 4vw, 52px);
        background: var(--cau-surface);
        border: 1px solid var(--cau-line);
        border-radius: 4px;
        box-shadow: var(--cau-shadow);
        overflow: hidden;
    }
    /* Hazard rule: 45° stripes in the danger colour, left edge only. */
    .cau-alert::before {
        content: '';
        position: absolute; inset: 0 auto 0 0; width: 12px;
        background: repeating-linear-gradient(
            135deg,
            var(--cau-danger) 0 9px,
            color-mix(in srgb, var(--cau-danger) 55%, transparent) 9px 18px
        );
    }
    .cau-alert-icon {
        flex: 0 0 auto; display: grid; place-items: center;
        width: 44px; height: 44px; border-radius: 50%;
        color: var(--cau-danger);
        border: 2px solid currentColor;
        background: transparent;
    }
    .cau-alert-icon .material-symbols-outlined { font-size: 26px; color: inherit; }
    .cau-alert-body { min-width: 0; }
    .cau-alert-label {
        display: block;
        font-size: 12px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase;
        color: var(--cau-danger);
        padding-bottom: 10px; margin-bottom: 14px;
        border-bottom: 1px solid color-mix(in srgb, var(--cau-danger) 26%, transparent);
    }
    .cau-scope .cau-alert-body p {
        margin: 0;
        font-family: 'Cinzel', Georgia, serif;
        font-size: clamp(16px, 1.8vw, 21px);
        line-height: 1.6; font-weight: 400; letter-spacing: 0;
        color: var(--cau-ink);
    }
    @media (max-width: 560px) {
        .cau-alert { flex-direction: column; gap: 16px; }
    }

    /* ---------- Section headers ---------- */
    .cau-section { padding-top: clamp(44px, 6vw, 76px); }
    .cau-section-head { max-width: 720px; margin-bottom: clamp(24px, 3.5vw, 40px); }
    .cau-eyebrow {
        display: inline-flex; align-items: center; gap: 9px;
        font-size: 12px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
        color: var(--acc);
        padding: 7px 14px; border-radius: 999px;
        background: color-mix(in srgb, var(--acc) 10%, transparent);
        border: 1px solid color-mix(in srgb, var(--acc) 22%, transparent);
    }
    .cau-eyebrow .material-symbols-outlined { font-size: 17px; }
    .cau-scope .cau-section-head h2 {
        margin: 18px 0 0;
        font-size: clamp(24px, 3vw, 34px);
        line-height: 1.18; letter-spacing: -.022em; font-weight: 700;
        color: var(--cau-ink);
    }
    .cau-scope .cau-section-head p {
        margin: 12px 0 0;
        font-size: clamp(15px, 1.5vw, 17px);
        line-height: 1.65; color: var(--cau-ink-soft);
    }

    /* ---------- Grids ---------- */
    .cau-grid { display: grid; gap: clamp(16px, 2vw, 24px); grid-template-columns: 1fr; }
    @media (min-width: 700px) {
        .cau-grid.cau-cols-2, .cau-grid.cau-cols-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .cau-grid.cau-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    /* ---------- Cards ---------- */
    .cau-card {
        display: flex; flex-direction: column;
        padding: clamp(22px, 2.4vw, 30px);
        background: var(--cau-surface);
        border: 1px solid var(--cau-line);
        border-radius: 20px;
        box-shadow: var(--cau-shadow);
        transition: transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s ease, border-color .28s ease;
    }
    .cau-card:hover, .cau-card:focus-within {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--acc) 35%, transparent);
        box-shadow: var(--cau-shadow-lg);
    }
    .cau-card-icon {
        display: grid; place-items: center;
        width: 46px; height: 46px; border-radius: 14px; margin-bottom: 20px;
        background: color-mix(in srgb, var(--acc) 12%, transparent);
        color: var(--acc);
    }
    .cau-card-icon .material-symbols-outlined { font-size: 25px; }
    .cau-scope .cau-card h3 {
        margin: 0;
        font-size: clamp(18px, 1.7vw, 21px);
        line-height: 1.3; letter-spacing: -.02em; font-weight: 700;
        color: var(--cau-ink);
    }
    .cau-scope .cau-card p {
        margin: 10px 0 0;
        font-size: 15px; line-height: 1.7; color: var(--cau-ink-soft);
    }
    .cau-card-link {
        margin-top: auto; padding-top: 20px;
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 15px; font-weight: 700; color: var(--acc); text-decoration: none;
    }
    .cau-card-link .material-symbols-outlined { font-size: 19px; transition: transform .25s ease; }
    .cau-card-link:hover .material-symbols-outlined,
    .cau-card-link:focus-visible .material-symbols-outlined { transform: translateX(4px); }

    /* Red-flag variant: numbered, danger-toned, no lift needed. */
    .cau-flag { position: relative; }
    .cau-flag .cau-flag-no {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 12px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
        color: var(--cau-danger);
        padding: 6px 12px; border-radius: 999px;
        background: var(--cau-danger-tint);
        border: 1px solid var(--cau-danger-line);
        margin-bottom: 18px; align-self: flex-start;
    }
    .cau-flag .cau-flag-no .material-symbols-outlined { font-size: 17px; }

    /* ---------- Verify panel ---------- */
    .cau-verify {
        background: #0e1c37;
        border-radius: 24px;
        padding: clamp(26px, 3.6vw, 48px);
        color: #e8eefc;
    }
    .cau-verify .cau-eyebrow {
        color: #ffd66b;
        background: rgba(255, 214, 107, .12);
        border-color: rgba(255, 214, 107, .32);
    }
    .cau-scope .cau-verify h2 { color: #ffffff; }
    .cau-scope .cau-verify .cau-section-head p { color: #b9c6e4; }
    .cau-verify-grid { display: grid; gap: 14px; grid-template-columns: 1fr; }
    @media (min-width: 760px) { .cau-verify-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .cau-tile {
        display: flex; align-items: center; gap: 14px;
        padding: 18px 20px;
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 16px;
        text-decoration: none; color: inherit;
        transition: background-color .25s ease, border-color .25s ease, transform .25s ease;
    }
    a.cau-tile:hover, a.cau-tile:focus-visible {
        background: rgba(255, 255, 255, .12);
        border-color: rgba(255, 255, 255, .28);
        transform: translateY(-2px);
    }
    .cau-tile-icon {
        flex: 0 0 auto; display: grid; place-items: center;
        width: 42px; height: 42px; border-radius: 12px;
        background: rgba(255, 214, 107, .16); color: #ffd66b;
    }
    .cau-tile-icon .material-symbols-outlined { font-size: 23px; }
    .cau-tile-text { min-width: 0; }
    .cau-tile-label {
        display: block; font-size: 12px; font-weight: 700;
        letter-spacing: .12em; text-transform: uppercase; color: #9fb2d8;
    }
    .cau-tile-value {
        display: block; margin-top: 3px;
        font-size: 15px; font-weight: 600; color: #ffffff;
        overflow-wrap: anywhere;
    }

    /* ---------- Shared a11y ---------- */
    .cau-scope a:focus-visible {
        outline: 2px solid var(--acc);
        outline-offset: 3px;
    }
    .cau-verify a:focus-visible { outline-color: #ffd66b; }
    @media (prefers-reduced-motion: reduce) {
        .cau-scope * { transition: none !important; animation: none !important; }
        .cau-card:hover, a.cau-tile:hover { transform: none; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="Caution to Applicants" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Important Notice'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Caution to'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Applicants'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Protecting your future starts with a secure application process.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <?php
    // ------------------------------------------------------------------
    // Content-area helpers (presentation only — data still comes from DB)
    // ------------------------------------------------------------------

    // Light-mode ink meets WCAG AA on white; dark-mode ink meets AA on the
    // dark surface. Keyed by the base of a stored token like "yellow-500".
    $cau_accents = [
        'blue'   => ['#1d4ed8', '#93b4fd'],
        'indigo' => ['#4338ca', '#a5b4fc'],
        'purple' => ['#6d28d9', '#c4b5fd'],
        'green'  => ['#15803d', '#86efac'],
        'teal'   => ['#0f766e', '#5eead4'],
        'cyan'   => ['#0e7490', '#67e8f9'],
        'red'    => ['#b42318', '#fca5a5'],
        'rose'   => ['#be123c', '#fda4af'],
        'orange' => ['#c2410c', '#fdba74'],
        'amber'  => ['#b45309', '#fcd34d'],
        'yellow' => ['#b45309', '#fcd34d'],
        'slate'  => ['#334155', '#cbd5e1'],
    ];
    $cau_accent_style = function ($color) use ($cau_accents) {
        $base = explode('-', (string)($color ?: 'blue'))[0];
        [$light, $dark] = $cau_accents[$base] ?? $cau_accents['blue'];
        return '--acc-l:' . $light . '; --acc-d:' . $dark . ';';
    };

    // Section eyebrows: a short label + icon that frames the heading rather
    // than repeating it. Falls back to the section title for unknown keys.
    $cau_section_meta = [
        'red_flags'  => ['report',           'Stay alert'],
        'why_direct' => ['verified_user',    'Your protection'],
        'verify'     => ['contact_support',  'Verify with us'],
        'channels'   => ['how_to_reg',       'Official channels'],
    ];

    /** Turn a contact value into a usable link (tel: / mailto: / url). */
    $cau_contact_href = function ($value) {
        $value = trim((string)$value);
        if ($value === '') return null;
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) return 'mailto:' . $value;
        $digits = preg_replace('/[^0-9+]/', '', $value);
        if (strlen(preg_replace('/[^0-9]/', '', $digits)) >= 7) return 'tel:' . $digits;
        return null;
    };
    ?>

    <!-- Content Sections -->
    <div class="cau-scope">
        <div class="cau-shell cau-band">
            <?php foreach ($page_sections as $section): ?>
            <?php
                $key      = $section['section_key'];
                $items    = $items_map[$key] ?? [];
                [$eyebrow_icon, $eyebrow_text] = $cau_section_meta[$key]
                    ?? ['info', strip_tags($section['section_title'])];
                // A section inherits the accent of its first item.
                $sec_style = $cau_accent_style($items[0]['item_color'] ?? 'blue-600');
            ?>

            <?php if ($key === 'warning'): ?>
                <!-- Official warning -->
                <div class="cau-alert" role="note">
                    <span class="cau-alert-icon" aria-hidden="true">
                        <span class="material-symbols-outlined">warning</span>
                    </span>
                    <div class="cau-alert-body">
                        <span class="cau-alert-label"><?php echo strip_tags($section['section_title']); ?></span>
                        <p><?php echo nl2br(strip_tags($section['section_subtitle'])); ?></p>
                    </div>
                </div>

            <?php elseif ($key === 'verify'): ?>
                <!-- Verification contact panel -->
                <section class="cau-section cau-accent" style="<?php echo $sec_style; ?>">
                    <div class="cau-verify">
                        <div class="cau-section-head">
                            <span class="cau-eyebrow">
                                <span class="material-symbols-outlined" aria-hidden="true"><?php echo $eyebrow_icon; ?></span>
                                <?php echo $eyebrow_text; ?>
                            </span>
                            <h2><?php echo strip_tags($section['section_title']); ?></h2>
                            <p><?php echo strip_tags($section['section_subtitle']); ?></p>
                        </div>
                        <div class="cau-verify-grid">
                            <?php foreach ($items as $item): ?>
                            <?php
                                $value = trim(strip_tags((string)$item['item_stat_value']));
                                $href  = $cau_contact_href($value);
                                $tag   = $href ? 'a' : 'div';
                            ?>
                            <<?php echo $tag; ?> class="cau-tile"<?php echo $href ? ' href="' . htmlspecialchars($href, ENT_QUOTES) . '"' : ''; ?>>
                                <span class="cau-tile-icon" aria-hidden="true">
                                    <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'contact_support'); ?></span>
                                </span>
                                <span class="cau-tile-text">
                                    <span class="cau-tile-label"><?php echo strip_tags($item['item_title']); ?></span>
                                    <span class="cau-tile-value"><?php echo $value; ?></span>
                                </span>
                            </<?php echo $tag; ?>>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

            <?php else: ?>
                <?php $is_flags = ($key === 'red_flags'); ?>
                <section class="cau-section cau-accent" style="<?php echo $sec_style; ?>">
                    <div class="cau-section-head">
                        <span class="cau-eyebrow">
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo $eyebrow_icon; ?></span>
                            <?php echo $eyebrow_text; ?>
                        </span>
                        <h2><?php echo strip_tags($section['section_title']); ?></h2>
                        <?php if (!empty($section['section_subtitle'])): ?>
                        <p><?php echo strip_tags($section['section_subtitle']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="cau-grid cau-cols-<?php echo max(1, min(count($items), $is_flags ? 3 : 2)); ?>">
                        <?php foreach ($items as $i => $item): ?>
                        <article class="cau-card <?php echo $is_flags ? 'cau-flag' : ''; ?> cau-accent"
                                 style="<?php echo $cau_accent_style($item['item_color'] ?? 'blue-600'); ?>">
                            <?php if ($is_flags): ?>
                                <span class="cau-flag-no">
                                    <span class="material-symbols-outlined" aria-hidden="true">flag</span>
                                    Red flag <?php printf('%02d', $i + 1); ?>
                                </span>
                            <?php elseif (!empty($item['item_icon'])): ?>
                                <span class="cau-card-icon" aria-hidden="true">
                                    <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon']); ?></span>
                                </span>
                            <?php endif; ?>

                            <h3><?php echo strip_tags($item['item_title']); ?></h3>
                            <p><?php echo strip_tags($item['item_description']); ?></p>

                            <?php $link = trim(strip_tags((string)($item['item_link'] ?? ''))); ?>
                            <?php if ($link !== '' && $link !== '#'): ?>
                            <a class="cau-card-link" href="<?php echo htmlspecialchars(str_replace(' ', '%20', $link), ENT_QUOTES); ?>">
                                <?php echo $key === 'channels' ? 'Apply here' : 'Learn more'; ?>
                                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                            </a>
                            <?php endif; ?>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-32 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-8 leading-tight">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-2xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-blue-900">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Directly'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-white">support_agent</span>
                        Contact Admissions
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
