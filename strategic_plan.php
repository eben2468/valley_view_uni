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
    /* Vice Chancellor's message flows as one continuous column at every width */
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

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-<?php echo max(1, min(count($pillars), 4)); ?> gap-8 lg:gap-10">
                <?php foreach ($pillars as $pillar): ?>
                <div class="pillar-card relative group">
                    <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($pillar['border_color']); ?> flex flex-col">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo strip_tags($pillar['border_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($pillar['icon']); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($pillar['title']); ?></h3>
                        <p class="text-2xl text-gray-700 dark:text-gray-300 mb-8 flex-grow leading-relaxed">
                            <?php echo strip_tags($pillar['description']); ?>
                        </p>
                        <?php if ($pillar['feature_1'] || $pillar['feature_2']): ?>
                        <ul class="space-y-4 mb-8">
                            <?php if ($pillar['feature_1']): ?>
                            <li class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-<?php echo strip_tags($pillar['border_color']); ?> text-3xl">check_circle</span>
                                <span class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($pillar['feature_1']); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if ($pillar['feature_2']): ?>
                            <li class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-<?php echo strip_tags($pillar['border_color']); ?> text-3xl">check_circle</span>
                                <span class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($pillar['feature_2']); ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
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

    <!-- Impact Stats Section -->
    <section class="py-24 bg-blue-900 text-white overflow-hidden relative">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black mb-4"><?php echo spHeading($headings, 'stats', 'heading', 'Our Foundation'); ?></h2>
                <?php $stats_sub = spHeading($headings, 'stats', 'subheading'); ?>
                <?php if ($stats_sub !== ''): ?>
                <p class="text-xl sm:text-2xl text-blue-100 font-medium leading-relaxed mb-12"><?php echo $stats_sub; ?></p>
                <?php endif; ?>
                <div class="grid grid-cols-2 lg:grid-cols-<?php echo max(1, min(count($stats), 4)); ?> gap-10 lg:gap-12 mt-12">
                    <?php foreach ($stats as $index => $stat): ?>
                    <div class="animate-fadeInUp" style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                        <div class="text-6xl md:text-7xl font-black text-yellow-400 mb-4"><?php echo strip_tags($stat['stat_value']); ?></div>
                        <div class="text-xl md:text-2xl uppercase tracking-widest font-black text-blue-100"><?php echo strip_tags($stat['stat_label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <?php if ($cta): ?>
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-gray-900"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($cta['cta_title_1']); ?> <br><span class="text-yellow-400 text-5xl sm:text-6xl md:text-7xl lg:text-5xl block mt-2"><?php echo strip_tags($cta['cta_title_2']); ?></span>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($cta['cta_description']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($cta['button_1_url']); ?>" download class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">download</span>
                        <?php echo strip_tags($cta['button_1_text']); ?>
                    </a>
                    <a href="<?php echo strip_tags($cta['button_2_url']); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags($cta['button_2_text']); ?>
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