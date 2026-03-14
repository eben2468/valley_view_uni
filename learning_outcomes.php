<?php
$page_title = "Learning Outcomes - Valley View University";
$active_page = "academics";
require_once 'includes/db_connect.php';

// Fetch Page Content
try {
    $page_stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'learning_outcomes'");
    $page_stmt->execute();
    $page_data = $page_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $page_data = [];
}

// Fetch sections
try {
    $sections_stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'learning_outcomes' ORDER BY display_order");
    $sections_stmt->execute();
    $page_sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);
    $sections_map = [];
    foreach ($page_sections as $s) {
        $sections_map[$s['section_key']] = $s;
    }
} catch (PDOException $e) {
    $page_sections = [];
    $sections_map = [];
}

// Fetch items grouped by section
try {
    $items_stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'learning_outcomes' AND is_active = 1 ORDER BY display_order");
    $items_stmt->execute();
    $all_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    $items_map = [];
    foreach ($all_items as $item) {
        $items_map[$item['section_key']][] = $item;
    }
} catch (PDOException $e) {
    $items_map = [];
}

// Fetch stats
try {
    $stats_stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = 'learning_outcomes' AND is_active = 1 ORDER BY display_order");
    $stats_stmt->execute();
    $page_stats = $stats_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $page_stats = [];
}

include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
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
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.3); }
        50% { box-shadow: 0 0 40px rgba(37, 99, 235, 0.6); }
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
    
    .outcome-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .outcome-card:hover {
        transform: translateY(-10px) scale(1.02);
    }
    .outcome-card:hover .outcome-icon {
        transform: scale(1.15) rotate(5deg);
    }
    .outcome-icon {
        transition: all 0.4s ease;
    }
    
    .pillar-card {
        transition: all 0.3s ease;
    }
    .pillar-card:hover {
        transform: translateY(-8px);
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .number-badge {
        width: 60px;
        height: 60px;
        min-width: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.75rem;
        border-radius: 50%;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="VVU Graduates" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Graduate Excellence'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Learning'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Outcomes'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Desired Characteristics of A VVU Graduate — Shaping Leaders for Tomorrow'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                    <span class="material-symbols-outlined text-2xl">school</span>
                    <span class="text-xl font-bold uppercase tracking-wider"><?php echo strip_tags($sections_map['intro']['section_subtitle'] ?? 'Defining Excellence'); ?></span>
                </div>
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8">
                    <?php echo $sections_map['intro']['section_title'] ?? 'What Makes a <span class="text-blue-600">VVU Graduate</span> Exceptional?'; ?>
                </h2>
                <div class="h-2 w-40 bg-gradient-to-r from-blue-600 to-yellow-500 mx-auto rounded-full mb-10"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                    <?php echo strip_tags($sections_map['intro']['section_description'] ?? 'The following learning objectives, described in terms of the desired characteristics of educated graduates, are used to guide educators in their development of courses and programmes.'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Core Learning Outcomes Grid -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sections_map['pillars']['section_title'] ?? 'The Eleven Pillars'); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sections_map['pillars']['section_subtitle'] ?? 'Core characteristics that define every Valley View University graduate.'); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
                <?php 
                $pillars = $items_map['pillars'] ?? [];
                $count = 1;
                foreach ($pillars as $pillar): 
                    $border_color = $pillar['item_color'] ?? 'blue-600';
                ?>
                <div class="outcome-card glass rounded-3xl shadow-xl p-10 border-t-8 border-<?php echo strip_tags($border_color); ?>">
                    <div class="flex items-center gap-5 mb-8">
                        <div class="number-badge bg-<?php echo strip_tags($border_color); ?> text-white"><?php echo $count; ?></div>
                        <div class="outcome-icon w-20 h-20 rounded-2xl bg-<?php echo strip_tags($border_color); ?> flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-5xl"><?php echo strip_tags($pillar['item_icon'] ?? 'star'); ?></span>
                        </div>
                    </div>
                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-5"><?php echo strip_tags($pillar['item_title']); ?></h3>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags($pillar['item_description']); ?>
                    </p>
                </div>
                <?php 
                $count++;
                endforeach; 
                ?>
            </div>
        </div>
    </section>

    <!-- How We Achieve These Outcomes Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sections_map['methods']['section_title'] ?? 'How We Achieve Excellence'); ?></h2>
                <div class="h-2 w-40 bg-yellow-500 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo strip_tags($sections_map['methods']['section_subtitle'] ?? 'Our comprehensive approach to developing well-rounded graduates.'); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-5xl mx-auto">
                <?php 
                $methods = $items_map['methods'] ?? [];
                foreach ($methods as $method): 
                    $color = $method['item_color'] ?? 'blue-600';
                    $points = [];
                    if (!empty($method['extra_data'])) {
                        $extra = json_decode($method['extra_data'], true);
                        $points = $extra['points'] ?? [];
                    }
                ?>
                <div class="pillar-card bg-gradient-to-br from-<?php echo str_replace('-600', '-50', $color); ?> to-<?php echo str_replace('-600', '-100', $color); ?> dark:from-<?php echo str_replace('-600', '-900/30', $color); ?> dark:to-<?php echo str_replace('-600', '-800/30', $color); ?> p-10 rounded-3xl shadow-lg">
                    <div class="w-20 h-20 rounded-2xl bg-<?php echo strip_tags($color); ?> flex items-center justify-center shadow-lg mb-8">
                        <span class="material-symbols-outlined text-white text-4xl"><?php echo strip_tags($method['item_icon'] ?? 'school'); ?></span>
                    </div>
                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($method['item_title']); ?></h3>
                    <p class="text-xl text-gray-700 dark:text-gray-300 font-medium leading-relaxed mb-6">
                        <?php echo nl2br(strip_tags($method['item_description'])); ?>
                    </p>
                    <?php if (!empty($points)): ?>
                    <ul class="space-y-4">
                        <?php foreach ($points as $point): ?>
                        <li class="flex items-center gap-4 text-xl text-gray-600 dark:text-gray-400">
                            <span class="material-symbols-outlined text-<?php echo strip_tags($color); ?> text-2xl">check_circle</span>
                            <span class="font-semibold"><?php echo strip_tags($point); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
            </div>
        </div>
    </section>

    <!-- Graduate Stats Section -->
    <?php
    $stats = $page_stats ?? []; // Already fetched at the top if I added it
    if (!empty($stats)):
    ?>
    <section class="relative py-24 bg-blue-900 overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-6"><?php echo strip_tags($sections_map['stats']['section_title'] ?? 'Graduate Success'); ?></h2>
                    <div class="h-2 w-40 bg-yellow-400 mx-auto rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php foreach ($stats as $stat): ?>
                    <div class="stat-card text-center p-8 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20">
                        <div class="text-6xl font-black text-yellow-400 mb-4"><?php echo strip_tags($stat['stat_value']); ?><?php echo strip_tags($stat['stat_suffix'] ?? ''); ?></div>
                        <div class="text-2xl text-white font-bold uppercase tracking-wider"><?php echo strip_tags($stat['stat_label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Graduate Testimonial Quote Section -->
    <?php 
    $testimonial = $items_map['testimonial'][0] ?? null;
    if ($testimonial):
    ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center">
                <div class="mb-8">
                    <span class="material-symbols-outlined text-8xl text-blue-600/20"><?php echo strip_tags($testimonial['item_icon'] ?? 'format_quote'); ?></span>
                </div>
                <blockquote class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 dark:text-white leading-relaxed mb-10 italic">
                    "<?php echo strip_tags($testimonial['item_description']); ?>"
                </blockquote>
                <div class="flex items-center justify-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                        <?php if ($testimonial['item_image']): ?>
                            <img src="<?php echo strip_tags($testimonial['item_image']); ?>" alt="Graduate" class="w-full h-full object-cover rounded-full">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-white text-3xl">person</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-left">
                        <p class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($testimonial['item_title']); ?></p>
                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($testimonial['item_subtitle']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($page_data['cta_title'] ?? 'Begin Your Journey to Excellence'); ?>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['cta_subtitle'] ?? 'Join the university that shapes leaders and builds futures. Your path to success starts here.'); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-8 justify-center mt-12">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-12 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-black rounded-2xl transition-all shadow-xl flex items-center justify-center gap-4 group">
                        <span class="material-symbols-outlined text-3xl group-hover:rotate-12 transition-transform">rocket_launch</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-5 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-2xl transition-all border-2 border-white/30 shadow-lg flex items-center justify-center gap-4 backdrop-blur-md group">
                        <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">chat</span>
                        Contact Admissions
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">Faith</div>
                        <div class="text-blue-200 uppercase tracking-widest text-xl font-black">Centered</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">Values</div>
                        <div class="text-blue-200 uppercase tracking-widest text-xl font-black">Driven</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">Future</div>
                        <div class="text-blue-200 uppercase tracking-widest text-xl font-black">Ready</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>