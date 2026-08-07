<?php
$page_title = "Mission and Vision - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM mission_vision_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$cards = $pdo->query("SELECT * FROM mission_vision_cards WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$pillars = $pdo->query("SELECT * FROM mission_vision_pillars WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$environment = $pdo->query("SELECT * FROM mission_vision_environment WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

// Bottom call-to-action block. Falls back to the previous hard-coded copy if
// install_mission_vision_cta.php has not been run yet.
try {
    $cta = $pdo->query("SELECT * FROM mission_vision_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
    $cta_links = $pdo->query("SELECT * FROM mission_vision_cta_links WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
} catch (PDOException $e) {
    $cta = false;
    $cta_links = [];
}

if (!$cta_links) {
    $cta_links = [
        ['icon' => 'star',          'title' => 'Our Core Values',    'description' => 'The beliefs that shape how we teach, serve and live.',       'link_url' => 'core_values.php'],
        ['icon' => 'menu_book',     'title' => 'Academic Programs',  'description' => 'Undergraduate, graduate and professional courses.',          'link_url' => 'academic_programs_overview.php'],
        ['icon' => 'location_city', 'title' => 'Visit Our Campus',   'description' => 'See Oyibi for yourself — facilities, halls and green space.', 'link_url' => 'the_campus.php'],
    ];
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
    .text-gradient {
        background: linear-gradient(to right, #2563eb, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[75vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-32">
            <div class="max-w-6xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'About Our Institution'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title_1'] ?? 'Our Mission'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_title_2'] ?? 'Vision'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'Guiding principles and aspirations'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Content Section -->
    <section class="py-24 sm:py-32">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24">
                <?php foreach ($cards as $card): ?>
                <div class="flex flex-col">
                    <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-<?php echo strip_tags($card['gradient_from']); ?> to-<?php echo strip_tags($card['gradient_to']); ?> p-8 sm:p-12 shadow-2xl transform hover:scale-[1.02] transition-transform duration-300">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full -ml-32 -mb-32"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 rounded-[1.5rem] bg-white/20 backdrop-blur-sm mb-8">
                                <span class="material-symbols-outlined text-white text-4xl sm:text-5xl"><?php echo strip_tags($card['icon']); ?></span>
                            </div>
                            <h2 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black mb-6 leading-tight"><?php echo strip_tags($card['title']); ?></h2>
                            <div class="h-2 w-24 bg-yellow-400 mb-8 rounded-full"></div>
                            <p class="text-white/95 text-2xl sm:text-3xl leading-relaxed font-bold italic">
                                <?php echo strip_tags($card['content']); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Key Pillars Section -->
    <section class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-900/50">
        <div class="container">
            <div class="text-center mb-16 sm:mb-24">
                <h2 class="text-gray-900 dark:text-white text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-tight tracking-tight mb-8">Our Four Pillars of Development</h2>
                <p class="text-2xl sm:text-3xl font-bold text-gray-600 dark:text-gray-400 max-w-4xl mx-auto leading-relaxed">Valley View University is committed to the holistic development of every student and staff member through four key dimensions.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-16">
                <?php foreach ($pillars as $pillar): ?>
                <div class="group relative overflow-hidden rounded-[2rem] bg-white dark:bg-gray-800 p-8 shadow-2xl hover:shadow-3xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col items-center text-center space-y-6">
                        <div class="flex items-center justify-center w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-<?php echo strip_tags($pillar['icon_color']); ?> to-<?php echo strip_tags($pillar['icon_color']); ?> transform group-hover:scale-110 transition-transform duration-300 shadow-xl">
                            <span class="material-symbols-outlined text-white text-4xl"><?php echo strip_tags($pillar['icon']); ?></span>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($pillar['title']); ?></h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xl leading-relaxed font-bold">
                            <?php echo strip_tags($pillar['description']); ?>
                        </p>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-<?php echo strip_tags($pillar['icon_color']); ?>/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Learning Environment Section -->
    <section class="py-24 sm:py-32">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 lg:gap-32 items-center">
                <div class="relative order-last lg:order-first">
                    <div class="aspect-[4/3] w-full rounded-[3rem] overflow-hidden shadow-2xl">
                        <img class="w-full h-full object-cover" data-alt="Modern university library with students studying" src="<?php echo strip_tags($environment['image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'); ?>"/>
                    </div>
                    <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-blue-600 rounded-full opacity-20 blur-3xl"></div>
                    <div class="absolute -top-12 -left-12 w-64 h-64 bg-yellow-500 rounded-full opacity-20 blur-3xl"></div>
                </div>

                <div class="flex flex-col gap-16">
                    <div>
                        <span class="inline-block px-8 py-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-base font-black tracking-[0.2em] uppercase mb-6"><?php echo strip_tags($environment['badge_text'] ?? 'Our Commitment'); ?></span>
                        <h2 class="text-gray-900 dark:text-white text-4xl sm:text-5xl md:text-6xl font-black leading-tight mb-8"><?php echo strip_tags($environment['section_title'] ?? 'A Well-Designed Learning Environment'); ?></h2>
                        <p class="text-gray-600 dark:text-gray-400 text-2xl leading-relaxed mb-6 font-bold">
                            <?php echo strip_tags($environment['paragraph_1'] ?? ''); ?>
                        </p>
                        <p class="text-gray-600 dark:text-gray-400 text-2xl leading-relaxed font-bold">
                            <?php echo strip_tags($environment['paragraph_2'] ?? ''); ?>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-8">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-2xl">school</span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($environment['feature_1_title'] ?? 'Academic Excellence'); ?></h4>
                                <p class="text-xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($environment['feature_1_description'] ?? 'Quality programs'); ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400 text-2xl">science</span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($environment['feature_2_title'] ?? 'Research Focus'); ?></h4>
                                <p class="text-xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($environment['feature_2_description'] ?? 'Research opportunities'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <!-- Bottom padding is deliberately lighter than the top: the cards are the
         last element, so symmetric py-48 left a large empty band of blue. -->
    <section class="relative pt-32 sm:pt-48 pb-20 sm:pb-24 overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
        <div class="absolute top-0 right-0 w-[40rem] h-[40rem] bg-yellow-400 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-[40rem] h-[40rem] bg-blue-400 rounded-full opacity-10 blur-3xl"></div>

        <div class="relative container">
            <div class="text-center">
                <h2 class="text-white text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-tight mb-8 drop-shadow-2xl">
                    <?php echo htmlspecialchars($cta['heading'] ?? 'Join Our Community of Excellence'); ?>
                </h2>
                <?php $cta_subtitle = $cta['subtitle'] ?? 'Discover how Valley View University can help you achieve holistic development and prepare for meaningful service to God and humanity.'; ?>
                <?php if (trim($cta_subtitle) !== ''): ?>
                <p class="text-white/90 text-lg sm:text-xl md:text-2xl max-w-4xl mx-auto leading-relaxed mb-12 font-medium italic drop-shadow-xl">
                    <?php echo nl2br(htmlspecialchars($cta_subtitle)); ?>
                </p>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <?php $btn1_text = $cta['primary_btn_text'] ?? 'Learn More About VVU'; ?>
                    <?php if (trim($btn1_text) !== ''): ?>
                    <a href="<?php echo htmlspecialchars($cta['primary_btn_link'] ?? 'about_us.php'); ?>" class="inline-flex items-center justify-center px-10 py-5 text-xl font-black text-blue-900 bg-yellow-400 rounded-[1.5rem] hover:bg-yellow-300 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-3xl">
                        <span class="material-symbols-outlined mr-3 text-2xl"><?php echo htmlspecialchars($cta['primary_btn_icon'] ?? 'info'); ?></span>
                        <?php echo htmlspecialchars($btn1_text); ?>
                    </a>
                    <?php endif; ?>

                    <?php $btn2_text = $cta['secondary_btn_text'] ?? 'Apply Now'; ?>
                    <?php if (trim($btn2_text) !== ''): ?>
                    <a href="<?php echo htmlspecialchars($cta['secondary_btn_link'] ?? 'apply.php'); ?>" class="inline-flex items-center justify-center px-10 py-5 text-xl font-black text-white bg-white/20 backdrop-blur-sm border-2 border-white rounded-[1.5rem] hover:bg-white hover:text-blue-900 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-3xl">
                        <span class="material-symbols-outlined mr-3 text-2xl"><?php echo htmlspecialchars($cta['secondary_btn_icon'] ?? 'how_to_reg'); ?></span>
                        <?php echo htmlspecialchars($btn2_text); ?>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Quick Links -->
                <div class="mt-20 pt-16 border-t border-white/20">
                    <?php $eyebrow = $cta['links_eyebrow'] ?? 'Explore More'; ?>
                    <?php if (trim($eyebrow) !== ''): ?>
                    <p class="text-yellow-400 text-base sm:text-lg font-black uppercase tracking-[0.3em] mb-12">
                        <?php echo htmlspecialchars($eyebrow); ?>
                    </p>
                    <?php endif; ?>

                    <?php
                    // Columns adapt to however many cards the admin has activated
                    $cta_col_class = (count($cta_links) % 3 === 0 || count($cta_links) > 4)
                        ? 'sm:grid-cols-3'
                        : 'sm:grid-cols-2';
                    ?>
                    <div class="grid grid-cols-1 <?php echo $cta_col_class; ?> gap-8">
                        <?php foreach ($cta_links as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['link_url']); ?>"
                           class="group flex h-full flex-col items-center gap-5 p-10 rounded-[2rem] bg-white/10 backdrop-blur-sm border border-white/15 shadow-xl transition-all duration-300 hover:bg-white/20 hover:border-yellow-400/60 hover:-translate-y-2 hover:shadow-2xl">
                            <span class="flex items-center justify-center w-28 h-28 rounded-[1.5rem] bg-white/15 border border-white/25 transition-all duration-300 group-hover:bg-yellow-400 group-hover:border-yellow-400 group-hover:scale-110">
                                <span class="material-symbols-outlined text-white text-6xl transition-colors duration-300 group-hover:text-blue-900"><?php echo htmlspecialchars($link['icon']); ?></span>
                            </span>
                            <span class="text-white text-2xl font-black uppercase tracking-[0.12em] leading-snug">
                                <?php echo htmlspecialchars($link['title']); ?>
                            </span>
                            <?php if (trim($link['description'] ?? '') !== ''): ?>
                            <span class="text-white/75 text-lg leading-relaxed max-w-xs">
                                <?php echo htmlspecialchars($link['description']); ?>
                            </span>
                            <?php endif; ?>
                            <span class="mt-auto pt-4 inline-flex items-center gap-2 text-yellow-400 text-base font-black uppercase tracking-widest">
                                Explore
                                <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:translate-x-1">arrow_forward</span>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>