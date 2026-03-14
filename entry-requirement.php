<?php
/**
 * Valley View University - Entry Requirements Page
 * Fetching content dynamically from academic_pages_* tables
 */
require_once 'includes/db_connect.php';

$page_key = 'entry_requirements';

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

$page_title = ($page_data['page_title'] ?? 'Entry Requirements') . " - Valley View University";
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
    @keyframes glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); }
        50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.4); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 6s ease-in-out infinite; }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
    }
    .dark .glass-card {
        background: rgba(17, 24, 39, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.3);
    }
    
    .requirement-card {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .requirement-card:hover { 
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.15);
        border-color: currentColor;
    }
    .dark .requirement-card:hover {
        box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.6);
    }

    .requirement-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle at top right, currentColor, transparent 70%);
        opacity: 0.05;
        z-index: 0;
        transition: opacity 0.5s ease;
    }
    .requirement-card:hover::before {
        opacity: 0.15;
    }
    
    .text-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .section-header-pill {
        background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);
        box-shadow: 0 10px 25px -5px rgba(30, 64, 175, 0.3);
    }

    .icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        border-radius: 20px;
        margin-bottom: 24px;
        background: white;
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .requirement-card:hover .icon-wrapper {
        transform: rotate(10deg) scale(1.1);
    }
    .dark .icon-wrapper {
        background: #1f2937;
    }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }
</style>

<main class="flex-grow bg-[#f8fafc] dark:bg-gray-950">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1523050853063-bd80e27433fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="Entry Requirements" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Admissions'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Entry'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Requirements'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Your journey to excellence begins here.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section id="pathways" class="py-32 md:py-48 relative bg-[#F8FAFC] dark:bg-[#0F172A]">
        <div class="container relative z-10">
            <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
                <?php foreach ($page_sections as $section): ?>
                <?php if ($section['section_key'] === 'intro'): ?>
                    <div class="text-center mb-20 md:mb-32 animate-fadeInUp">
                        <span class="inline-block px-6 py-2 mb-8 text-sm md:text-base font-bold tracking-[0.2em] text-blue-700 dark:text-blue-400 uppercase bg-blue-100 dark:bg-blue-900/40 rounded-full">Overview</span>
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">
                            <?php echo strip_tags($section['section_title']); ?>
                        </h2>
                        <p class="text-lg md:text-xl lg:text-2xl text-slate-600 dark:text-slate-400 font-medium leading-relaxed max-w-4xl mx-auto">
                            <?php echo nl2br(strip_tags($section['section_subtitle'] ?? '')); ?>
                        </p>
                    </div>
                <?php elseif ($section['section_key'] === 'resources'): ?>
                    <!-- Resource Section is handled separately -->
                <?php else: ?>
                    <div class="mb-24 md:mb-32 animate-fadeInUp">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-16 px-4 border-b-2 border-slate-200 dark:border-slate-800 pb-10">
                            <div class="max-w-4xl">
                                <div class="inline-flex items-center gap-4 px-6 py-3 mb-6 rounded-2xl bg-slate-900 dark:bg-slate-800 text-white shadow-xl">
                                    <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($items_map[$section['section_key']][0]['item_icon'] ?? 'school'); ?></span>
                                    <span class="text-lg md:text-xl font-black uppercase tracking-[0.15em] text-white"><?php echo strip_tags($section['section_title']); ?></span>
                                </div>
                                <h3 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white leading-tight">
                                    <?php echo strip_tags($section['section_subtitle'] ?? 'Program Requirements'); ?>
                                </h3>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                            <?php if (isset($items_map[$section['section_key']])): ?>
                                <?php foreach ($items_map[$section['section_key']] as $item): ?>
                                <?php 
                                    // Map random item_colors to specific soft pastel colors matching the design spec
                                    $color_mappings = [
                                        'blue' => ['bg' => 'bg-[#E3F2FD]', 'pill' => 'bg-[#B3E5FC]', 'text' => 'text-[#01579B]'], // Web Design like
                                        'orange' => ['bg' => 'bg-[#FFF3E0]', 'pill' => 'bg-[#FFE0B2]', 'text' => 'text-[#E65100]'], // Graphic Design like
                                        'purple' => ['bg' => 'bg-[#F3E5F5]', 'pill' => 'bg-[#E1BEE7]', 'text' => 'text-[#4A148C]'], // Developers like
                                        'green' => ['bg' => 'bg-[#E8F5E9]', 'pill' => 'bg-[#C8E6C9]', 'text' => 'text-[#1B5E20]'], // Copywriting like
                                        'yellow' => ['bg' => 'bg-[#FFFDE7]', 'pill' => 'bg-[#FFF59D]', 'text' => 'text-[#F57F17]'],
                                        'red' => ['bg' => 'bg-[#FFEBEE]', 'pill' => 'bg-[#FFCDD2]', 'text' => 'text-[#B71C1C]'],
                                        'indigo' => ['bg' => 'bg-[#E8EAF6]', 'pill' => 'bg-[#C5CAE9]', 'text' => 'text-[#1A237E]'],
                                        'teal' => ['bg' => 'bg-[#E0F2F1]', 'pill' => 'bg-[#B2DFDB]', 'text' => 'text-[#004D40]']
                                    ];
                                    $base = explode('-', $item['item_color'] ?? 'blue-600')[0];
                                    $colors = $color_mappings[$base] ?? $color_mappings['blue']; // fallback to blue
                                ?>
                                <div class="bg-white dark:bg-[#1E293B] rounded-[2.5rem] flex flex-col group shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden h-full aspect-square">
                                    <div class="<?php echo $colors['bg']; ?> p-8 md:p-10 rounded-[2rem] flex-grow mx-3 mt-3 flex flex-col">
                                        
                                        <h4 class="text-3xl md:text-3xl lg:text-4xl font-bold text-slate-900 mb-4 leading-tight tracking-tight">
                                            <?php echo strip_tags($item['item_title']); ?>
                                        </h4>
                                        
                                        <div class="text-base md:text-lg text-slate-700 font-medium leading-[1.6] mb-6">
                                            <?php if ($item['item_subtitle']): ?>
                                                <?php echo strip_tags($item['item_subtitle']); ?>
                                            <?php else: ?>
                                                <?php // Use first line of description as subtitle if subtitle is empty, just for nice formatting
                                                    $first_point = explode("\n", $item['item_description'])[0] ?? '';
                                                    echo strip_tags(trim($first_point));
                                                ?>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex flex-wrap gap-2.5 mt-auto">
                                            <?php 
                                            $points = explode("\n", $item['item_description']);
                                            $count = 0;
                                            foreach ($points as $point): 
                                                $clean_point = strip_tags(trim($point));
                                                if ($clean_point && $count < 4): // Limit to 4 pills like the screenshot
                                                    // Skip the first point if we used it as the subtitle
                                                    if (!$item['item_subtitle'] && $count === 0) { $count++; continue; }
                                            ?>
                                            <span class="px-4 py-2 text-sm md:text-base font-semibold <?php echo $colors['pill']; ?> text-slate-800 rounded-full">
                                                <?php echo $clean_point; ?>
                                            </span>
                                            <?php 
                                                $count++;
                                                endif; 
                                            endforeach; 
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="p-6 md:p-8 flex items-center justify-between bg-white dark:bg-[#1E293B]">
                                        <span class="text-lg md:text-xl font-bold text-slate-900 dark:text-white">Explore</span>
                                        <a href="<?php echo strip_tags($item['item_link'] ?? '#'); ?>" class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center transition-colors group-hover:-rotate-45 duration-300">
                                            <span class="material-symbols-outlined text-2xl text-slate-900 dark:text-white">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Resources Section -->
    <?php 
    $resource_section = null;
    foreach ($page_sections as $s) if ($s['section_key'] === 'resources') $resource_section = $s;
    if ($resource_section && isset($items_map['resources'])): 
    ?>
    <section class="py-24 md:py-32 relative bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
        <div class="container relative z-10">
            <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 md:mb-24">
                    <span class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-[0.2em] text-sm md:text-base mb-6">
                        <span class="material-symbols-outlined text-xl">folder_open</span>
                        Tools & Guides
                    </span>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white leading-tight mb-6">
                        <?php echo strip_tags($resource_section['section_title']); ?>
                    </h2>
                    <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 font-medium leading-relaxed max-w-3xl mx-auto">
                        <?php echo strip_tags($resource_section['section_subtitle']); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                    <?php foreach ($items_map['resources'] as $item): ?>
                    <a href="<?php echo strip_tags($item['item_link'] ?? '#'); ?>" class="group p-8 md:p-10 bg-slate-50 dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700 rounded-[2.5rem] hover:bg-white dark:hover:bg-slate-800 hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col">
                        <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-8 transition-colors group-hover:bg-blue-600 group-hover:text-white border border-blue-200 dark:border-blue-800/50">
                            <span class="material-symbols-outlined text-4xl"><?php echo strip_tags($item['item_icon'] ?? 'description'); ?></span>
                        </div>
                        <h4 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-4 leading-tight">
                            <?php echo strip_tags($item['item_title']); ?>
                        </h4>
                        <p class="text-base md:text-lg text-slate-600 dark:text-slate-400 font-medium leading-relaxed mb-8 flex-grow">
                            <?php echo strip_tags($item['item_description']); ?>
                        </p>
                        <div class="mt-auto flex items-center gap-3 text-blue-600 dark:text-blue-400 font-black uppercase text-base md:text-lg tracking-[0.15em] group-hover:gap-5 transition-all duration-300">
                            Access Now
                            <span class="material-symbols-outlined text-2xl">arrow_forward</span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-40 overflow-hidden bg-blue-600">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-700 to-blue-900 opacity-90"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] animate-slow-zoom"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-5xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed font-semibold italic">
                    "<?php echo strip_tags($page_data['cta_subtitle']); ?>"
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'apply.php'); ?>" class="group px-12 py-6 bg-white text-blue-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-2xl flex items-center justify-center gap-4">
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Now'); ?>
                        <span class="material-symbols-outlined text-3xl transition-transform group-hover:translate-x-1">trending_flat</span>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-2xl transition-all backdrop-blur-xl border-2 border-white/30 transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        Contact Admissions
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Design Accents -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
