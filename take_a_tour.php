<?php
$page_title = "Take a Tour - Valley View University";
$active_page = "resources";
require_once 'includes/db_connect.php';

// Fetch page content
$page_key = 'take_a_tour';
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ?");
$stmt->execute([$page_key]);
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
$stmt->execute([$page_key]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute([$page_key]);
$all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$items_by_section = [];
foreach ($all_items as $item) {
    $items_by_section[$item['section_key']][] = $item;
}

include 'includes/header.php';

// Default values
$hero_badge = $page_data['hero_badge'] ?? 'Explore Our Campus';
$hero_title = $page_data['hero_title'] ?? 'Take a Tour';
$hero_subtitle = $page_data['hero_subtitle'] ?? 'Discover Your Future Home';
$hero_description = $page_data['hero_description'] ?? '"Experience the beauty, serenity, and state-of-the-art facilities that make Valley View University a world-class institution."';
$hero_video = $page_data['hero_video'] ?? 'uploads/AERIAL VIEW OF VALLEY VIEW UNIVERSITY BY OPAREDAWURO.mp4.mp4';
$hero_image = $page_data['hero_image'] ?? 'images/new_to_vvu_hero_bg.png';
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
    .tour-card {
        transition: all 0.3s ease;
    }
    .tour-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .material-symbols-outlined {
        /* color: white !important; */
    }
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        height: 0;
        overflow: hidden;
    }
    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Video Background -->
        <div class="absolute inset-0 z-0">
            <?php if (!empty($hero_video)): ?>
                <video autoplay muted loop playsinline class="w-full h-full object-cover opacity-60">
                    <source src="<?php echo strip_tags($hero_video); ?>" type="video/mp4">
                    <!-- Fallback Image -->
                    <img src="<?php echo strip_tags($hero_image); ?>" alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom">
                </video>
            <?php else: ?>
                <img src="<?php echo strip_tags($hero_image); ?>" alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero_badge); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $hero_title; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero_subtitle); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo $hero_description; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Dynamic Sections -->
    <?php foreach ($sections as $section): ?>
        <?php 
        $section_key = $section['section_key'];
        $section_items = $items_by_section[$section_key] ?? [];
        if (empty($section_items)) continue;
        ?>

        <?php if ($section_key === 'overview'): ?>
            <!-- Campus Overview Section -->
            <section class="py-24 bg-white dark:bg-gray-900" id="overview">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-20">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($section['section_subtitle']); ?>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div class="space-y-12">
                            <?php foreach ($section_items as $item): ?>
                            <div class="flex gap-10 p-12 glass rounded-[3rem] border-l-[12px] border-<?php echo str_replace('-600', '', $item['item_color'] ?: 'blue'); ?>-600 shadow-2xl">
                                <div class="w-16 h-16 rounded-2xl bg-<?php echo $item['item_color'] ?: 'blue-600'; ?> flex items-center justify-center shadow-lg mb-6 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'location_on'); ?></span>
                                </div>
                                <div>
                                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <div class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                        <?php echo $item['item_description']; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="relative">
                            <div class="absolute -inset-4 bg-blue-500 rounded-[3rem] blur-2xl opacity-20"></div>
                            <img src="<?php echo strip_tags($section['section_image'] ?: 'images/new_to_vvu_hero_bg.png'); ?>" alt="Campus View" class="relative rounded-[2.5rem] shadow-2xl w-full h-[500px] object-cover">
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ($section_key === 'landmarks'): ?>
            <!-- Key Landmarks Section -->
            <section class="py-24 bg-gray-50 dark:bg-gray-950" id="landmarks">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-20">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($section['section_subtitle']); ?></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                        <?php foreach ($section_items as $item): ?>
                        <div class="tour-card group bg-white dark:bg-gray-900 rounded-[2.5rem] overflow-hidden shadow-lg border border-gray-100 dark:border-gray-800">
                            <div class="h-72 overflow-hidden relative">
                                <img src="<?php echo strip_tags($item['item_image'] ?: 'images/excellence.png'); ?>" alt="<?php echo strip_tags($item['item_title']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute top-6 right-6 w-16 h-16 rounded-2xl bg-<?php echo $item['item_color'] ?: 'blue-600'; ?> flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'account_balance'); ?></span>
                                </div>
                            </div>
                            <div class="p-10">
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                                <div class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                    <?php echo $item['item_description']; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($section_key === 'aerial_tour'): ?>
            <!-- Aerial Tour Section -->
            <section class="py-24 bg-white dark:bg-gray-900" id="aerial_tour">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-16">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($section['section_subtitle']); ?></p>
                    </div>

                    <?php foreach ($section_items as $item): ?>
                    <div class="max-w-6xl mx-auto rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white dark:border-gray-800">
                        <div class="video-container">
                            <video controls class="w-full h-full object-cover">
                                <source src="<?php echo strip_tags($item['item_link'] ?: 'uploads/AERIAL VIEW OF VALLEY VIEW UNIVERSITY BY OPAREDAWURO.mp4.mp4'); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    
                    <div class="mt-16 text-center">
                        <p class="text-2xl text-gray-500 dark:text-gray-400 font-bold italic">
                            <?php echo $item['item_description'] ?: '"A bird\'s eye view of excellence, innovation, and beauty."'; ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- CTA Section -->
    <?php if (!empty($page_data['cta_title'])): ?>
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo $page_data['cta_title']; ?>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'apply.php'); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-3xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-4xl">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Now'); ?>
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
