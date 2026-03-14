<?php
$page_title = "Core Values - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM core_values_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$pillars = $pdo->query("SELECT * FROM core_values_pillars WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$actions = $pdo->query("SELECT * FROM core_values_actions WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();

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
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'Our Foundation'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Core Values'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'That Define Us'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'At Valley View University, our core values are the guiding principles that shape our culture.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- The Three Pillars Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">The Three Pillars</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">These fundamental values form the cornerstone of our identity and guide every aspect of university life.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <?php foreach ($pillars as $index => $pillar): ?>
                <!-- <?php echo strip_tags($pillar['title']); ?> -->
                <div class="pillar-card relative group">
                    <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($pillar['border_color'] ?? 'blue-600'); ?> flex flex-col" style="background: <?php echo strip_tags($pillar['bg_color'] ?? 'transparent'); ?>">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo strip_tags($pillar['border_color'] ?? 'blue-600'); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($pillar['icon'] ?? 'workspace_premium'); ?></span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($pillar['title']); ?></h3>
                        <p class="text-3xl text-gray-700 dark:text-gray-300 mb-8 flex-grow leading-relaxed">
                            <?php echo nl2br(strip_tags($pillar['description'])); ?>
                        </p>
                        <?php if (!empty($pillar['feature_1']) || !empty($pillar['feature_2'])): ?>
                        <ul class="space-y-4 mb-8">
                            <?php if (!empty($pillar['feature_1'])): ?>
                            <li class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-<?php echo strip_tags($pillar['border_color'] ?? 'blue-600'); ?> text-4xl">check_circle</span>
                                <span class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($pillar['feature_1']); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($pillar['feature_2'])): ?>
                            <li class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-<?php echo strip_tags($pillar['border_color'] ?? 'blue-600'); ?> text-4xl">check_circle</span>
                                <span class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($pillar['feature_2']); ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>
                        <?php if (!empty($pillar['quote'])): ?>
                        <div class="p-6 bg-<?php echo strip_tags($pillar['border_color'] ?? 'blue'); ?>-50 dark:bg-<?php echo strip_tags($pillar['border_color'] ?? 'blue'); ?>-900/20 rounded-2xl italic text-gray-700 dark:text-gray-300 border-l-4 border-<?php echo strip_tags($pillar['border_color'] ?? 'blue-600'); ?> text-2xl font-medium">
                            "<?php echo strip_tags($pillar['quote']); ?>"
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Values in Action Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Living Our Values</h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Our core values aren't just words on a page—they're the principles we live by in every aspect of university life.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($actions as $action): ?>
                <div class="group p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-16 h-16 rounded-2xl bg-<?php echo strip_tags($action['icon_bg_color'] ?? 'blue-600'); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($action['icon'] ?? 'school'); ?></span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($action['title']); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags($action['description']); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    Embrace Our Values, <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2">Join Our Community</span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Be part of a university that stands for excellence, integrity, and service. Discover how our core values can shape your future.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="about_us.php" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">info</span>
                        Learn More About VVU
                    </a>
                    <a href="apply.php" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">how_to_reg</span>
                        Apply Now
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">100%</div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black">Commitment</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">Values</div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black">Driven Culture</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">24/7</div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black">Living Principles</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>