<?php
/**
 * Valley View University - Provisional Admission List Page
 * Modern, responsive page to display admission lists (PDFs)
 */

require_once('includes/db_connect.php');

$page_key = 'provisional_admission_list';

// Fetch page content from academic_pages_content
try {
    $page_stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ?");
    $page_stmt->execute([$page_key]);
    $page_data = $page_stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    
    // Fetch sections
    $sections_stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
    $sections_stmt->execute([$page_key]);
    $page_sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);
    $sections_map = [];
    foreach ($page_sections as $s) {
        $sections_map[$s['section_key']] = $s;
    }

    // Fetch items grouped by section
    $items_stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY display_order");
    $items_stmt->execute([$page_key]);
    $all_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    $items_map = [];
    foreach ($all_items as $item) {
        $items_map[$item['section_key']][] = $item;
    }
} catch (PDOException $e) {
    $page_data = [];
    $sections_map = [];
    $items_map = [];
}

$page_title = ($page_data['page_title'] ?? "Provisional Admission List") . " - Valley View University";
$active_page = "admissions";

include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
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

    .list-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .list-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .pdf-icon-bg {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }

    /* ======================================================================
       OFFICIAL ADMISSION LISTS — section redesign
       Scoped under .pal-scope. Note: bootstrap.css sets html{font-size:10px},
       so this block uses px throughout rather than rem.
       ====================================================================== */
    .pal-scope {
        --pal-ink:       #0f172a;
        --pal-ink-soft:  #475569;
        --pal-ink-muted: #64748b;
        --pal-surface:   #ffffff;
        --pal-canvas:    #f6f8fb;
        --pal-line:      #e2e8f0;
        --pal-brand:     #1d4ed8;
        --pal-brand-ink: #ffffff;
        --pal-pdf:       #c02626;
        --pal-pdf-tint:  #fdeceb;
        --pal-shadow:    0 1px 2px rgba(15, 23, 42, .04), 0 14px 30px -18px rgba(15, 23, 42, .3);
        --pal-shadow-lg: 0 2px 4px rgba(15, 23, 42, .04), 0 34px 64px -30px rgba(15, 23, 42, .36);
        font-family: 'Inter', system-ui, -apple-system, "Segoe UI", sans-serif;
        color: var(--pal-ink);
    }
    /* The global stylesheet hard-codes 15px/#636363 on p, li, a and span.
       :where() keeps this reset at zero specificity so every rule below wins. */
    .pal-scope :where(p, li, a, span, dt, dd) {
        font-size: inherit; line-height: inherit; color: inherit; font-weight: inherit;
    }
    .dark .pal-scope {
        --pal-ink:       #f1f5f9;
        --pal-ink-soft:  #cbd5e1;
        --pal-ink-muted: #94a3b8;
        --pal-surface:   #111a2e;
        --pal-canvas:    #0b1220;
        --pal-line:      #23304a;
        --pal-brand:     #93b4fd;
        --pal-brand-ink: #0b1220;
        --pal-pdf:       #fca5a5;
        --pal-pdf-tint:  rgba(252, 165, 165, .14);
        --pal-shadow:    0 1px 2px rgba(0, 0, 0, .4), 0 14px 30px -18px rgba(0, 0, 0, .7);
        --pal-shadow-lg: 0 2px 4px rgba(0, 0, 0, .4), 0 34px 64px -30px rgba(0, 0, 0, .8);
    }
    @media (prefers-color-scheme: dark) {
        html:not(.light) .pal-scope {
            --pal-ink:       #f1f5f9;
            --pal-ink-soft:  #cbd5e1;
            --pal-ink-muted: #94a3b8;
            --pal-surface:   #111a2e;
            --pal-canvas:    #0b1220;
            --pal-line:      #23304a;
            --pal-brand:     #93b4fd;
            --pal-brand-ink: #0b1220;
            --pal-pdf:       #fca5a5;
            --pal-pdf-tint:  rgba(252, 165, 165, .14);
        }
    }

    .pal-shell {
        width: 100%;
        /* 100vw guard: the legacy layout can be wider than the viewport on
           small screens — this section must not follow it. */
        max-width: min(1240px, 100vw);
        margin-inline: auto;
        padding-inline: clamp(16px, 4vw, 40px);
    }
    /* The section is a full-bleed band that follows the hero — no overlapping
       floating panel, so nothing sits on top of the hero image. */
    .pal-band {
        background: var(--pal-canvas);
        border-bottom: 1px solid var(--pal-line);
        padding-block: clamp(56px, 8vw, 104px);
    }

    /* ---------- Section header ---------- */
    .pal-head {
        display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
        gap: 24px;
        padding-bottom: 28px;
        margin-bottom: clamp(28px, 4vw, 44px);
        border-bottom: 1px solid var(--pal-line);
    }
    .pal-head-text { flex: 1 1 420px; min-width: 0; max-width: 720px; }
    .pal-eyebrow {
        display: inline-flex; align-items: center; gap: 9px;
        font-size: 12px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
        color: var(--pal-brand);
        padding: 7px 14px; border-radius: 999px;
        background: color-mix(in srgb, var(--pal-brand) 10%, transparent);
        border: 1px solid color-mix(in srgb, var(--pal-brand) 22%, transparent);
    }
    .pal-eyebrow .material-symbols-outlined { font-size: 17px; }
    .pal-scope .pal-head-text h2 {
        margin: 20px 0 0;
        font-size: clamp(21px, 2.4vw, 30px);
        line-height: 1.25; letter-spacing: -.015em; font-weight: 600;
        color: var(--pal-ink);
    }
    .pal-head-meta {
        flex: 0 0 auto; display: flex; flex-direction: column; gap: 6px;
        text-align: right; font-size: 14px; color: var(--pal-ink-muted);
    }
    .pal-head-meta strong { color: var(--pal-ink); font-weight: 700; }
    @media (max-width: 640px) { .pal-head-meta { text-align: left; } }

    /* ---------- Grid ---------- */
    .pal-grid { display: grid; gap: 24px; grid-template-columns: 1fr; }
    @media (min-width: 700px) {
        .pal-grid.pal-cols-2, .pal-grid.pal-cols-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1100px) {
        .pal-grid.pal-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    /* ---------- List card ---------- */
    .pal-card {
        display: flex; flex-direction: column;
        padding: clamp(22px, 2.4vw, 30px);
        background: var(--pal-surface);
        border: 1px solid var(--pal-line);
        border-radius: 22px;
        box-shadow: var(--pal-shadow);
        transition: transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s ease, border-color .28s ease;
    }
    .pal-card:hover, .pal-card:focus-within {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--pal-brand) 35%, transparent);
        box-shadow: var(--pal-shadow-lg);
    }
    .pal-card-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 20px; }
    .pal-filetype {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 7px 12px 7px 8px; border-radius: 12px;
        background: var(--pal-pdf-tint); color: var(--pal-pdf);
        font-size: 12px; font-weight: 800; letter-spacing: .1em;
    }
    .pal-filetype .material-symbols-outlined { font-size: 20px; }
    .pal-tag {
        padding: 6px 12px; border-radius: 999px;
        background: color-mix(in srgb, var(--pal-brand) 10%, transparent);
        color: var(--pal-brand);
        font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    }
    .pal-scope .pal-card h3 {
        margin: 0;
        font-size: clamp(19px, 1.6vw, 22px);
        line-height: 1.3; letter-spacing: -.02em; font-weight: 700;
        color: var(--pal-ink);
    }
    .pal-scope .pal-card p {
        margin: 10px 0 0;
        font-size: 15px; line-height: 1.65; color: var(--pal-ink-soft);
    }
    .pal-meta {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        margin: 18px 0 0;
        font-size: 13px; color: var(--pal-ink-muted);
    }
    .pal-meta .sep { opacity: .5; }

    /* ---------- Actions ---------- */
    .pal-actions {
        margin-top: auto; padding-top: 22px;
        display: flex; align-items: center; gap: 10px;
    }
    .pal-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        height: 44px; padding: 0 18px; border-radius: 12px;
        font-size: 15px; font-weight: 700; letter-spacing: -.01em;
        text-decoration: none; white-space: nowrap;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease;
    }
    .pal-btn .material-symbols-outlined { font-size: 20px; }
    .pal-btn-primary { flex: 1 1 auto; background: var(--pal-brand); color: var(--pal-brand-ink); }
    .pal-btn-primary:hover, .pal-btn-primary:focus-visible {
        background: color-mix(in srgb, var(--pal-brand) 84%, #000);
        color: var(--pal-brand-ink);
    }
    .pal-btn-ghost {
        width: 44px; padding: 0;
        background: var(--pal-canvas); color: var(--pal-ink-soft);
        border: 1px solid var(--pal-line);
    }
    .pal-btn-ghost:hover, .pal-btn-ghost:focus-visible {
        color: var(--pal-brand);
        border-color: color-mix(in srgb, var(--pal-brand) 40%, transparent);
    }
    .pal-pending {
        margin-top: auto; padding-top: 22px;
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--pal-ink-muted);
    }
    .pal-pending .material-symbols-outlined { font-size: 19px; }

    /* ---------- Empty state ---------- */
    .pal-empty {
        grid-column: 1 / -1;
        display: flex; flex-direction: column; align-items: center; text-align: center;
        padding: clamp(40px, 6vw, 72px) 24px;
        border: 1px dashed var(--pal-line); border-radius: 22px;
        background: var(--pal-surface);
    }
    .pal-empty .material-symbols-outlined { font-size: 44px; color: var(--pal-ink-muted); margin-bottom: 14px; }
    .pal-scope .pal-empty h3 { margin: 0; font-size: 19px; font-weight: 700; color: var(--pal-ink); }
    .pal-scope .pal-empty p { margin: 8px 0 0; font-size: 15px; color: var(--pal-ink-soft); }

    /* ---------- Shared a11y ---------- */
    .pal-scope a:focus-visible {
        outline: 2px solid var(--pal-brand);
        outline-offset: 3px;
    }
    @media (prefers-reduced-motion: reduce) {
        .pal-scope * { transition: none !important; animation: none !important; }
        .pal-card:hover { transform: none; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'images/pro-bg.jpg'); ?>" 
                 alt="Admissions List" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400">
                        <?php echo strip_tags($page_data['hero_badge'] ?? 'Admissions'); ?>
                    </span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Provisional'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3">
                        <?php echo strip_tags($page_data['hero_subtitle'] ?? 'Admission List'); ?>
                    </span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Check your status and join the family of excellence.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <?php foreach ($page_sections as $section): ?>
    <?php if ($section['section_key'] === 'official_lists'): ?>
        <?php
        $list_items = $items_map['official_lists'] ?? [];

        /** Human-readable file size, or null when the file isn't on disk. */
        $pal_filesize = function ($path) {
            $path = ltrim((string)$path, '/');
            if ($path === '' || !is_file(__DIR__ . '/' . $path)) return null;
            $bytes = filesize(__DIR__ . '/' . $path);
            if ($bytes === false) return null;
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) { $bytes /= 1024; $i++; }
            return round($bytes, ($i > 0 && $bytes < 100) ? 1 : 0) . ' ' . $units[$i];
        };

        // Most recently updated list — shown as the section's freshness stamp.
        $pal_latest = null;
        foreach ($list_items as $it) {
            $ts = strtotime((string)($it['updated_at'] ?? ''));
            if ($ts && (!$pal_latest || $ts > $pal_latest)) $pal_latest = $ts;
        }
        $pal_cols = max(1, min(count($list_items), 3));
        ?>
        <section class="pal-scope pal-band">
            <div class="pal-shell">
                <header class="pal-head">
                    <div class="pal-head-text">
                        <span class="pal-eyebrow">
                            <span class="material-symbols-outlined" aria-hidden="true">description</span>
                            <?php echo strip_tags($section['section_title']); ?>
                        </span>
                        <h2><?php echo strip_tags($section['section_subtitle'] ?? 'Admission Documents'); ?></h2>
                    </div>
                    <?php if ($list_items): ?>
                    <div class="pal-head-meta">
                        <span><strong><?php echo count($list_items); ?></strong> <?php echo count($list_items) === 1 ? 'list' : 'lists'; ?> published</span>
                        <?php if ($pal_latest): ?>
                        <span>Last updated <?php echo date('j M Y', $pal_latest); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </header>

                <div class="pal-grid pal-cols-<?php echo $pal_cols; ?>">
                    <?php if (empty($list_items)): ?>
                        <div class="pal-empty">
                            <span class="material-symbols-outlined" aria-hidden="true">description</span>
                            <h3><?php echo strip_tags($page_data['empty_list_message'] ?: 'No admission lists published yet.'); ?></h3>
                            <p>Please check back here once results have been released.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($list_items as $item): ?>
                        <?php
                            $link  = trim(strip_tags((string)($item['item_link'] ?? '')));
                            $href  = str_replace(' ', '%20', $link);
                            $size  = $pal_filesize($link);
                            $ready = $link !== '' && $size !== null;   // file actually on disk
                            $when  = strtotime((string)($item['updated_at'] ?? ''));
                            $tag   = trim(strip_tags((string)($item['item_stat_value'] ?? '')));
                        ?>
                        <article class="pal-card">
                            <div class="pal-card-top">
                                <span class="pal-filetype">
                                    <span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span>
                                    PDF
                                </span>
                                <?php if ($tag !== ''): ?>
                                <span class="pal-tag"><?php echo $tag; ?></span>
                                <?php endif; ?>
                            </div>

                            <h3><?php echo strip_tags($item['item_title']); ?></h3>
                            <p><?php echo strip_tags($item['item_description']); ?></p>

                            <p class="pal-meta">
                                <?php if ($size !== null): ?>
                                <span><?php echo $size; ?></span>
                                <?php endif; ?>
                                <?php if ($size !== null && $when): ?><span class="sep" aria-hidden="true">·</span><?php endif; ?>
                                <?php if ($when): ?>
                                <span>Updated <?php echo date('j M Y', $when); ?></span>
                                <?php endif; ?>
                            </p>

                            <?php if ($ready): ?>
                            <div class="pal-actions">
                                <a class="pal-btn pal-btn-primary" href="<?php echo htmlspecialchars($href, ENT_QUOTES); ?>" target="_blank" rel="noopener">
                                    View list
                                    <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                </a>
                                <a class="pal-btn pal-btn-ghost" href="<?php echo htmlspecialchars($href, ENT_QUOTES); ?>" download
                                   aria-label="Download <?php echo htmlspecialchars(strip_tags($item['item_title']), ENT_QUOTES); ?> (PDF<?php echo $size !== null ? ', ' . $size : ''; ?>)">
                                    <span class="material-symbols-outlined" aria-hidden="true">download</span>
                                </a>
                            </div>
                            <?php else: ?>
                            <p class="pal-pending">
                                <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                                Not yet available
                            </p>
                            <?php endif; ?>
                        </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php elseif ($section['section_key'] === 'guidance'): ?>
        <section class="py-24 bg-gray-50 dark:bg-gray-950">
            <div class="container">
                <div class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div>
                            <span class="inline-block px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold text-sm mb-6 uppercase tracking-widest">Next Steps</span>
                            <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($section['section_title']); ?></h2>
                            <div class="space-y-8">
                                <?php if (isset($items_map['guidance'])): ?>
                                    <?php foreach ($items_map['guidance'] as $item): ?>
                                    <div class="flex gap-6 animate-fadeInUp">
                                        <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black shrink-0 shadow-lg">
                                            <?php echo strip_tags($item['item_stat_value'] ?? '!'); ?>
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($item['item_title']); ?></h4>
                                            <p class="text-lg text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-blue-600 to-yellow-400 rounded-[3rem] blur-2xl opacity-20"></div>
                            <img src="<?php echo strip_tags($section['section_image'] ?: 'Education-Website-and-AdminPanel/images/h-adm.jpg'); ?>" alt="Student Success" class="relative w-full h-[500px] object-cover rounded-[3rem] shadow-2xl">
                            <div class="absolute -bottom-8 -right-8 glass p-8 rounded-[2rem] shadow-2xl max-w-xs animate-float">
                                <h5 class="text-xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($page_data['help_title'] ?: 'Need Help?'); ?></h5>
                                <p class="text-gray-600 dark:text-gray-400 font-bold mb-4"><?php echo strip_tags($page_data['help_description'] ?: 'Our admission officers are ready to assist you.'); ?></p>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $page_data['help_phone'] ?: '+233302230990'); ?>" class="flex items-center gap-3 text-blue-600 font-black">
                                    <span class="material-symbols-outlined">call</span>
                                    <?php echo strip_tags($page_data['help_phone'] ?: '+233 302 230 990'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php endforeach; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-yellow-400 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        </div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-black text-white mb-6">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-lg text-blue-100 mb-10 font-medium leading-relaxed">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'contact_us.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">chat</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Contact Admissions'); ?>
                    </a>
                    <?php if ($page_data['cta_button_text_2']): ?>
                    <a href="<?php echo strip_tags($page_data['cta_button_link_2'] ?: 'admissions.php'); ?>" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4 backdrop-blur-md">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        <?php echo strip_tags($page_data['cta_button_text_2']); ?>
                    </a>
                    <?php else: ?>
                    <a href="admissions.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4 backdrop-blur-md">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        Admission Requirements
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
