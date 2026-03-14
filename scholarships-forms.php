<?php
/**
 * Valley View University - Scholarship Forms Page
 * Fetching content dynamically from academic_pages_* tables
 */
require_once 'includes/db_connect.php';

$page_key = 'scholarships_forms';

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

$page_title = ($page_data['page_title'] ?? 'Scholarship Forms') . " - Valley View University";
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
    
    .form-card { transition: all 0.4s ease; }
    .form-card:hover { 
        transform: translateY(-15px);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
    }
    
    .download-btn { transition: all 0.3s ease; }
    .download-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.6);
    }

    .text-gradient {
        background: linear-gradient(to right, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="Scholarship Forms" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Application Center'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Scholarship'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Forms'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Your journey to financial support begins here.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <?php foreach ($page_sections as $section): ?>
                <?php if ($section['section_key'] === 'intro'): ?>
                    <div class="text-center mb-20 animate-fadeInUp">
                        <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-blue-600 shadow-lg">
                            <span class="material-symbols-outlined text-3xl text-white">description</span>
                            <span class="text-2xl font-black uppercase tracking-wider text-white"><?php echo strip_tags($section['section_title']); ?></span>
                        </div>
                        <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6">
                            <?php echo $section['section_title']; ?>
                        </h2>
                        <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                        <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                            <?php echo nl2br(strip_tags($section['section_subtitle'])); ?>
                        </p>
                    </div>
                <?php elseif ($section['section_key'] === 'forms'): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-24 animate-fadeInUp">
                        <?php if (isset($items_map['forms'])): ?>
                            <?php foreach ($items_map['forms'] as $item): ?>
                            <div class="form-card glass p-10 rounded-[3rem] shadow-xl border-t-[12px] border-<?php echo $item['item_color'] ?? 'blue-600'; ?> flex flex-col justify-between">
                                <div>
                                    <div class="w-16 h-16 rounded-2xl bg-<?php echo explode('-', $item['item_color'])[0]; ?>-600 flex items-center justify-center text-white shadow-lg mb-8">
                                        <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?? 'description'); ?></span>
                                    </div>
                                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h3>
                                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-8">
                                        <?php echo strip_tags($item['item_description']); ?>
                                    </p>
                                </div>
                                <div class="flex flex-col gap-4">
                                    <?php if ($item['item_link']): ?>
                                    <a href="<?php echo strip_tags($item['item_link']); ?>" download class="download-btn inline-flex items-center justify-center gap-3 px-8 py-4 bg-<?php echo explode('-', $item['item_color'])[0]; ?>-600 <?php echo strpos($item['item_color'], 'yellow') !== false ? 'text-blue-900' : 'text-white'; ?> text-xl font-black rounded-2xl hover:brightness-110 transition-all">
                                        <span class="material-symbols-outlined text-3xl">download</span>
                                        Download PDF
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($item['item_stat_value']): // Using stat_value for extra link like 'Apply Online' ?>
                                    <a href="<?php echo strip_tags($item['item_stat_value']); ?>" target="_blank" class="inline-flex items-center justify-center gap-3 px-8 py-4 border-2 border-<?php echo explode('-', $item['item_color'])[0]; ?>-500 text-<?php echo explode('-', $item['item_color'])[0]; ?>-600 dark:text-<?php echo explode('-', $item['item_color'])[0]; ?>-400 text-xl font-black rounded-2xl hover:bg-<?php echo explode('-', $item['item_color'])[0]; ?>-500/10 transition-all">
                                        <span class="material-symbols-outlined text-3xl">language</span>
                                        Apply Online
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php elseif ($section['section_key'] === 'tips'): ?>
                    <div class="mb-24 animate-fadeInUp">
                        <div class="text-center mb-16">
                            <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium max-w-2xl mx-auto"><?php echo strip_tags($section['section_subtitle']); ?></p>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                            <?php if (isset($items_map['tips'])): ?>
                                <?php foreach ($items_map['tips'] as $item): ?>
                                <div class="glass p-10 rounded-[2.5rem] border-l-[12px] border-blue-600 shadow-xl">
                                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed font-bold"><?php echo strip_tags($item['item_description']); ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($section['section_key'] === 'submission'): ?>
                    <div class="bg-blue-900 rounded-[3rem] p-12 md:p-16 text-white shadow-2xl relative overflow-hidden animate-fadeInUp">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
                        <div class="relative z-10">
                            <h2 class="text-5xl md:text-6xl font-black mb-10"><?php echo strip_tags($section['section_title']); ?></h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <?php if (isset($items_map['submission'])): ?>
                                    <?php foreach ($items_map['submission'] as $item): ?>
                                    <div class="flex items-start gap-6">
                                        <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?? 'mail'); ?></span>
                                        </div>
                                        <div>
                                            <h4 class="text-3xl font-bold mb-3"><?php echo strip_tags($item['item_title']); ?></h4>
                                            <p class="text-xl text-blue-100"><?php echo $item['item_description']; ?></p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Support Section -->
    <?php 
    $support_section = null;
    foreach ($page_sections as $s) if ($s['section_key'] === 'support') $support_section = $s;
    if ($support_section && isset($items_map['support'])): 
    ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-8">
                    <?php echo strip_tags($support_section['section_title']); ?>
                </h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium mb-16 max-w-4xl mx-auto leading-relaxed">
                    <?php echo strip_tags($support_section['section_subtitle']); ?>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($items_map['support'] as $item): ?>
                    <div class="glass p-10 rounded-[2.5rem] card-hover bg-blue-600/10">
                        <span class="material-symbols-outlined text-5xl text-blue-600 mb-6"><?php echo strip_tags($item['item_icon'] ?? 'call'); ?></span>
                        <h4 class="text-3xl font-black mb-3 text-gray-900 dark:text-white"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-32 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-4xl sm:text-5xl font-black text-white mb-6 leading-tight">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed font-normal">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'scholarships.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-blue-900">info</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Scholarship Info'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-white">support_agent</span>
                        Contact Support
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
