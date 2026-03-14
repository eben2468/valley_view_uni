<?php
$page_title = "Ecological Stewardship - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM ecology_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$philosophy = $pdo->query("SELECT * FROM ecology_philosophy WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$initiatives = $pdo->query("SELECT * FROM ecology_initiatives WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$stats = $pdo->query("SELECT * FROM ecology_stats WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$cta = $pdo->query("SELECT * FROM ecology_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

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
    .pillar-card {
        transition: all 0.3s ease;
    }
    .pillar-card:hover {
        transform: translateY(-10px);
    }
    .text-gradient-green {
        background: linear-gradient(to right, #4ade80, #facc15);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuAmDxsoRYwbAdA-K6FnHtGy5wBKf5vqZyCFrV-HUs0bGBbSYDDD3Wneaa4B3Mghrt-m8pX84m8r7qCgwcfDWVTgZ50_6SQnuA8eFAgja8xXsyydOyiQerdpRe8ByyUddDBpqrZiEkjhGqS2kqGy0E8GeQPOwbB-ubqUVSYHeioclUPe1rVhk9B5n7d1x91PPmJdcrant8ajJ6wr62nzNnnytxiWlIHbUtB4rcls1XQWOj-_Fb4eja9I6pobhorje4VNZvJg6liAcbOK'); ?>" 
                 alt="Lush Green VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-green-900/80 via-green-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-green-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'Ecological Stewardship'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-tight tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Harmony with'); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-green-400 via-green-200 to-yellow-400 block mt-3"><?php echo strip_tags($hero['hero_subtitle'] ?? "God's Creation"); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'At Valley View University, we believe that caring for the environment is a sacred responsibility. Our campus is a living laboratory for sustainable development and ecological preservation.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Our Ecological Philosophy Section -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-4">Our Philosophy</h2>
                <div class="h-1.5 w-32 bg-green-600 mx-auto rounded-full mb-6"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">We integrate environmental stewardship into our curriculum, campus operations, and community outreach.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                <?php foreach ($philosophy as $item): ?>
                <div class="pillar-card relative group">
                    <div class="relative h-full glass p-8 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($item['border_color'] ?? 'green-600'); ?> flex flex-col">
                        <div class="w-20 h-20 rounded-2xl bg-<?php echo strip_tags($item['border_color'] ?? 'green-600'); ?> flex items-center justify-center text-white shadow-lg mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['icon'] ?? 'nature_people'); ?></span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['title']); ?></h3>
                        <p class="text-2xl text-gray-700 dark:text-gray-300 mb-6 flex-grow leading-relaxed">
                            <?php echo nl2br(strip_tags($item['description'])); ?>
                        </p>
                        <?php if (!empty($item['feature_1']) || !empty($item['feature_2'])): ?>
                        <ul class="space-y-3 mb-6">
                            <?php if (!empty($item['feature_1'])): ?>
                            <li class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-<?php echo strip_tags($item['border_color'] ?? 'green-600'); ?> text-4xl">check_circle</span>
                                <span class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($item['feature_1']); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($item['feature_2'])): ?>
                            <li class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-<?php echo strip_tags($item['border_color'] ?? 'green-600'); ?> text-4xl">check_circle</span>
                                <span class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($item['feature_2']); ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>
                        <?php if (!empty($item['quote'])): ?>
                        <div class="p-6 bg-<?php echo str_replace('-600', '-50', strip_tags($item['border_color'] ?? 'green-50')); ?> dark:bg-<?php echo str_replace('-600', '-900/20', strip_tags($item['border_color'] ?? 'green-900/20')); ?> rounded-2xl italic text-gray-700 dark:text-gray-300 border-l-4 border-<?php echo strip_tags($item['border_color'] ?? 'green-600'); ?> text-2xl font-medium">
                            "<?php echo strip_tags($item['quote']); ?>"
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Green Initiatives in Action -->
    <section class="py-20 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-12">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-4">Initiatives in Action</h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Our commitment to the environment is visible in every corner of our campus.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($initiatives as $initiative): ?>
                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl bg-<?php echo strip_tags($initiative['icon_bg_color'] ?? 'green-600'); ?> flex items-center justify-center text-white shadow-lg mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($initiative['icon'] ?? 'potted_plant'); ?></span>
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($initiative['title']); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo nl2br(strip_tags($initiative['description'])); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Impact Stats Section -->
    <section class="py-20 bg-green-900 text-white overflow-hidden relative">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black mb-12">Our Ecological Impact</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-10">
                    <?php foreach ($stats as $index => $stat): ?>
                    <div class="animate-fadeInUp" style="animation-delay: <?php echo (0.1 * ($index + 1)); ?>s;">
                        <div class="text-6xl md:text-7xl font-black text-green-400 mb-2"><?php echo strip_tags($stat['stat_value']); ?></div>
                        <div class="text-xl md:text-2xl uppercase tracking-widest font-black text-green-100"><?php echo strip_tags($stat['stat_label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gray-900"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-green-500/10 rounded-full blur-[120px] -mr-60 -mt-60"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-yellow-500/10 rounded-full blur-[120px] -ml-60 -mb-60"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white mb-6 leading-tight tracking-tight">
                    <?php echo strip_tags($cta['title_white'] ?? 'Join Our Green Revolution,'); ?> <br><span class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-medium text-green-400 block mt-2"><?php echo strip_tags($cta['title_green'] ?? 'Protect Our Future'); ?></span>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-green-100 mb-10 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($cta['description'] ?? 'Be part of a community that values the earth as much as education. Discover how you can contribute to our ecological mission.'); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($cta['button_1_link'] ?? 'student_life.php'); ?>" class="px-8 py-4 bg-green-500 hover:bg-green-400 text-white text-lg font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl"><?php echo strip_tags($cta['button_1_icon'] ?? 'eco'); ?></span>
                        <?php echo strip_tags($cta['button_1_text'] ?? 'Get Involved'); ?>
                    </a>
                    <a href="<?php echo strip_tags($cta['button_2_link'] ?? 'contact_us.php'); ?>" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl"><?php echo strip_tags($cta['button_2_icon'] ?? 'mail'); ?></span>
                        <?php echo strip_tags($cta['button_2_text'] ?? 'Contact Eco-Office'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>