<?php
$page_title = "Strategic Plan - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch data from database
$hero = $pdo->query("SELECT * FROM strategic_plan_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$president = $pdo->query("SELECT * FROM strategic_plan_president_message WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$pillars = $pdo->query("SELECT * FROM strategic_plan_pillars WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$timeline = $pdo->query("SELECT * FROM strategic_plan_timeline WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$stats = $pdo->query("SELECT * FROM strategic_plan_stats WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$cta = $pdo->query("SELECT * FROM strategic_plan_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

// Editable section headings, keyed by section
$headings = [];
foreach ($pdo->query("SELECT * FROM strategic_plan_section_headings WHERE is_active=1")->fetchAll(PDO::FETCH_ASSOC) as $h) {
    $headings[$h['section_key']] = $h;
}
if (!function_exists('spHeading')) {
    function spHeading($headings, $key, $field, $default = '') {
        return htmlspecialchars($headings[$key][$field] ?? $default, ENT_QUOTES, 'UTF-8');
    }
}

include 'includes/header.php';
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
    /* Vice-Chancellor's message flows as one continuous column at every width */
    .sp-message p + p { margin-top: 1.5rem; }

    /* Wide content rail - fills the empty gutters on large screens.
       .container is already 96% wide site-wide, so this only caps the
       very widest displays. */
    .sp-wrap {
        max-width: 1720px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Portrait: keep the head high in the circular crop so the face reads
       well, and cap how large it grows on very wide screens. */
    .sp-portrait { object-position: center 22%; }
    @media (min-width: 1024px) {
        .sp-portrait { max-width: 340px; max-height: 340px; }
        .sp-sticky { position: sticky; top: 110px; }
    }

    .pillar-card {
        transition: all 0.3s ease;
    }
    .pillar-card:hover {
        transform: translateY(-10px);
    }

    /* Strategic pillars: wide white cards with a curved colour panel on the
       right, a white icon circle on the panel's edge and an accent line round
       the top-left corner, all in navy (--pc). */
    .sp-pillars {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px; max-width: 1200px; margin: 0 auto;
    }
    .sp-pillar {
        --pc: #1e3a8a; --pc-text: #1e3a8a;
        position: relative; overflow: hidden; display: flex; align-items: center;
        min-height: 240px; padding: 34px 190px 34px 38px; border-radius: 26px;
        background: #fff; box-shadow: 0 18px 40px -26px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .sp-pillar:hover { transform: translateY(-5px); box-shadow: 0 28px 50px -26px rgba(15, 23, 42, .5); }
    .dark .sp-pillar { background: #1f2937; }
    /* Accent line round the top-left corner */
    .sp-pillar::before {
        content: ""; position: absolute; left: 0; top: 0; width: 42%; height: 58%;
        border-top: 5px solid var(--pc); border-left: 5px solid var(--pc);
        /* Only the top and left sides: a stray right/bottom border showed as
           a grey line cutting across the text on phones */
        border-right: 0 !important; border-bottom: 0 !important;
        border-top-left-radius: 26px; pointer-events: none;
    }
    /* Curved colour panel */
    .sp-pillar-side {
        position: absolute; top: 0; right: 0; bottom: 0; width: 120px;
        background: var(--pc); border-radius: 50% 0 0 50% / 62% 0 0 62%;
    }
    .sp-pillar-badge {
        position: absolute; top: 50%; right: 72px; transform: translateY(-50%);
        display: inline-flex; align-items: center; justify-content: center;
        width: 92px; height: 92px; border-radius: 50%; background: #fff;
        box-shadow: 0 10px 26px -10px rgba(15, 23, 42, .45);
        transition: transform .35s ease;
    }
    .sp-pillar-badge .material-symbols-outlined {
        font-size: 42px; color: var(--pc-text);
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
    }
    .sp-pillar:hover .sp-pillar-badge { transform: translateY(-50%) scale(1.06); }
    .sp-pillar-body { position: relative; z-index: 1; }
    .sp-pillar-title {
        color: var(--pc-text); font-size: 19px; font-weight: 600; line-height: 1.3;
        letter-spacing: .04em; text-transform: uppercase;
    }
    .sp-pillar-text { margin: 10px 0 0; color: #4b5563; font-size: 16px; line-height: 1.65; }
    .dark .sp-pillar-text { color: #d1d5db; }
    .sp-pillar-points { list-style: none; margin: 14px 0 0; padding: 0; display: grid; gap: 6px; }
    .sp-pillar-points li { display: flex; align-items: center; gap: 8px; margin: 0; color: #1f2937; font-size: 14.5px; font-weight: 600; }
    .sp-pillar-points .material-symbols-outlined { font-size: 18px; color: var(--pc); }
    .dark .sp-pillar-points li { color: #e5e7eb; }

    @media (max-width: 1023px) {
        .sp-pillars { grid-template-columns: 1fr; max-width: 720px; }
    }
    @media (max-width: 560px) {
        .sp-pillar { min-height: 0; padding: 26px 104px 26px 24px; border-radius: 22px; }
        .sp-pillar::before { border-top-width: 4px; border-left-width: 4px; border-top-left-radius: 22px; }
        .sp-pillar-side { width: 70px; }
        .sp-pillar-badge { width: 64px; height: 64px; right: 38px; }
        .sp-pillar-badge .material-symbols-outlined { font-size: 30px; }
        .sp-pillar-title { font-size: 16px; }
        .sp-pillar-text { font-size: 14.5px; }
    }
    /* Closing band (CTA + foundation figures): a more compact scale than
       the shared vvu-modern.css defaults */
    .sp-cta { padding: 52px 0 46px; }
    .sp-cta .vm-cta-heading { font-size: clamp(22px, 2.6vw, 32px); }
    .sp-cta .vm-cta-heading::after { width: 44px; height: 3px; margin-top: 16px; }
    .sp-cta .vm-cta-lead { font-size: 16px; margin-top: 16px; }
    .sp-cta .vm-actions { margin-top: 24px; gap: 12px; }
    .sp-cta .vm-btn { padding: 11px 22px; font-size: 14.5px; }
    .sp-cta .vm-btn .material-symbols-outlined { font-size: 19px; }
    .sp-cta .vm-kicker { font-size: 12px; margin-bottom: 10px; }
    .sp-cta .vm-stats { max-width: 880px; gap: 10px 28px; }
    .sp-cta .vm-stat { padding-top: 14px; }
    .sp-cta .vm-stat-value { font-size: clamp(24px, 2.6vw, 34px); }
    .sp-cta .vm-stat-label { font-size: 11.5px; letter-spacing: .14em; line-height: 1.5; }
    @media (max-width: 767px) {
        .sp-cta { padding: 40px 0 36px; }
        .sp-cta .vm-cta-lead { font-size: 15px; }
        .sp-cta .vm-stat-label { font-size: 10.5px; letter-spacing: .06em; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? ''); ?>" 
                 alt="VVU Strategic Vision" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'Vision 2026 & Beyond'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title_1'] ?? 'Strategic Plan'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_title_2'] ?? 'Shaping Our Future'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? ''); ?>"
                </p>

                <div class="mt-12 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="<?php echo strip_tags($hero['download_pdf_url'] ?? 'uploads/VISION 2025.pdf'); ?>" download class="inline-flex items-center gap-3 px-8 py-4 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                        <span class="material-symbols-outlined text-3xl">download</span>
                        <?php echo strip_tags($hero['download_button_text'] ?? 'Download Vision 2025 (PDF)'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- President's Message Section -->
    <?php if ($president): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="sp-wrap">
                <div class="flex flex-col lg:flex-row items-center lg:items-stretch gap-12 lg:gap-16">
                    <div class="lg:w-1/4 w-full flex items-start justify-center lg:justify-start shrink-0">
                        <div class="sp-sticky relative">
                            <div class="absolute -inset-4 bg-blue-600/20 rounded-full blur-2xl"></div>
                            <img src="<?php echo strip_tags($president['president_image_url']); ?>"
                                 alt="<?php echo htmlspecialchars(strip_tags((string) $president['message_author']), ENT_QUOTES, 'UTF-8'); ?>"
                                 class="sp-portrait relative z-10 w-64 h-64 md:w-80 md:h-80 lg:w-full lg:h-auto lg:aspect-square rounded-full object-cover border-8 border-white dark:border-gray-800 shadow-2xl">
                        </div>
                    </div>
                    <div class="lg:w-3/4 text-center lg:text-left">
                        <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($president['section_title']); ?></h2>
                        <div class="h-2 w-24 bg-blue-600 mb-8 mx-auto lg:mx-0 rounded-full"></div>

                        <?php if (!empty(trim(strip_tags((string) $president['message_quote'])))): ?>
                        <p class="text-2xl sm:text-3xl text-gray-800 dark:text-gray-200 font-bold leading-relaxed italic mb-8 border-l-4 border-yellow-400 pl-6 text-left">
                            "<?php echo strip_tags($president['message_quote']); ?>"
                        </p>
                        <?php endif; ?>

                        <div class="sp-message text-lg sm:text-xl text-gray-600 dark:text-gray-400 leading-relaxed text-left">
                            <?php for ($i = 1; $i <= 5; $i++):
                                $para = trim(strip_tags((string) ($president["message_paragraph_{$i}"] ?? '')));
                                if ($para === '') continue; ?>
                            <p><?php echo htmlspecialchars($para, ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endfor; ?>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-800 flex items-center gap-4 justify-center lg:justify-start">
                            <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0 shadow-lg">
                                <span class="material-symbols-outlined text-3xl">draw</span>
                            </div>
                            <div class="text-left">
                                <p class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white leading-tight"><?php echo strip_tags($president['message_author']); ?></p>
                                <?php if (!empty($president['author_title'])): ?>
                                <p class="text-sm font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest"><?php echo strip_tags($president['author_title']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Strategic Pillars Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo spHeading($headings, 'pillars', 'heading', 'Our Strategic Pillars'); ?></h2>
                <div class="h-2 w-40 bg-yellow-500 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo spHeading($headings, 'pillars', 'subheading'); ?></p>
            </div>

            <!-- Wide cards: copy on the left, a coloured curved panel on the
                 right with the icon in a white circle on its edge, and an
                 accent line round the top-left corner, all in navy. -->
            <div class="sp-pillars">
                <?php foreach ($pillars as $pillar): ?>
                <div class="sp-pillar">
                    <div class="sp-pillar-body">
                        <div class="sp-pillar-title" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($pillar['title'])); ?></div>
                        <p class="sp-pillar-text"><?php echo htmlspecialchars(strip_tags($pillar['description'])); ?></p>
                        <?php if ($pillar['feature_1'] || $pillar['feature_2']): ?>
                        <ul class="sp-pillar-points">
                            <?php foreach (['feature_1', 'feature_2'] as $f): if (empty($pillar[$f])) continue; ?>
                            <li><span class="material-symbols-outlined">check_circle</span><?php echo htmlspecialchars(strip_tags($pillar[$f])); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <span class="sp-pillar-side" aria-hidden="true"></span>
                    <span class="sp-pillar-badge" aria-hidden="true"><span class="material-symbols-outlined"><?php echo strip_tags($pillar['icon']); ?></span></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Implementation Timeline Section -->
    <section class="py-24 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo spHeading($headings, 'timeline', 'heading', 'Implementation Timeline'); ?></h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo spHeading($headings, 'timeline', 'subheading'); ?></p>
            </div>

            <div class="relative max-w-5xl mx-auto">
                <!-- Vertical Line -->
                <div class="absolute left-1/2 top-0 bottom-0 w-1 bg-blue-100 dark:bg-gray-800 -translate-x-1/2 hidden md:block"></div>

                <div class="space-y-16">
                    <?php 
                    $timeline_count = count($timeline);
                    foreach ($timeline as $index => $phase): 
                        $is_odd = ($index % 2 == 0);
                        $align_class = $is_odd ? 'md:flex-row' : 'md:flex-row-reverse';
                        $text_align = $is_odd ? 'md:text-right' : 'md:text-left';
                        $border_class = $is_odd ? 'border-r-8' : 'border-l-8';
                    ?>
                    <div class="relative flex flex-col <?php echo $align_class; ?> items-center gap-8">
                        <div class="md:w-1/2 <?php echo $text_align; ?>">
                            <div class="p-8 glass rounded-3xl shadow-xl <?php echo $border_class; ?> border-<?php echo strip_tags($phase['border_color']); ?>">
                                <span class="text-xl font-black text-<?php echo strip_tags($phase['border_color']); ?> uppercase tracking-widest"><?php echo strip_tags($phase['phase_badge']); ?></span>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mt-2 mb-4"><?php echo strip_tags($phase['phase_title']); ?></h4>
                                <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($phase['phase_description']); ?></p>
                            </div>
                        </div>
                        <div class="absolute left-1/2 -translate-x-1/2 w-10 h-10 bg-<?php echo strip_tags($phase['dot_color']); ?> rounded-full border-4 border-white dark:border-gray-900 z-10 hidden md:block"></div>
                        <div class="md:w-1/2"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to action, with the foundation figures underneath -->
    <section class="vm-cta sp-cta">
        <div class="container">
            <?php if ($cta): ?>
            <div class="vm-cta-head">
                <div class="vm-cta-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(trim(strip_tags($cta['cta_title_1']) . ' ' . strip_tags($cta['cta_title_2']))); ?></div>
                <?php if (trim(strip_tags($cta['cta_description'])) !== ''): ?>
                <p class="vm-cta-lead"><?php echo htmlspecialchars(strip_tags($cta['cta_description'])); ?></p>
                <?php endif; ?>
                <div class="vm-actions">
                    <?php if (!empty($cta['button_1_text'])): ?>
                    <a href="<?php echo strip_tags($cta['button_1_url']); ?>" download class="vm-btn vm-btn--gold">
                        <span class="material-symbols-outlined">download</span><?php echo htmlspecialchars(strip_tags($cta['button_1_text'])); ?>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($cta['button_2_text'])): ?>
                    <a href="<?php echo strip_tags($cta['button_2_url']); ?>" class="vm-btn vm-btn--ghost">
                        <span class="material-symbols-outlined">mail</span><?php echo htmlspecialchars(strip_tags($cta['button_2_text'])); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($stats)): ?>
            <div class="vm-cta-head" style="margin-top: <?php echo $cta ? '52px' : '0'; ?>;">
                <span class="vm-kicker"><?php echo spHeading($headings, 'stats', 'heading', 'Our Foundation'); ?></span>
                <?php $stats_sub = spHeading($headings, 'stats', 'subheading'); if ($stats_sub !== ''): ?>
                <p class="vm-cta-lead" style="margin-top: 0;"><?php echo $stats_sub; ?></p>
                <?php endif; ?>
            </div>
            <div class="vm-stats" style="--vm-cols: <?php echo max(1, min(count($stats), 4)); ?>; margin-top: 20px;">
                <?php foreach ($stats as $stat): ?>
                <div class="vm-stat">
                    <span class="vm-stat-value"><?php echo htmlspecialchars(strip_tags($stat['stat_value'])); ?></span>
                    <span class="vm-stat-label"><?php echo htmlspecialchars(strip_tags($stat['stat_label'])); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>