<?php
/**
 * Valley View University - Caution to Applicants Page
 * Fetching content dynamically from academic_pages_* tables
 */
require_once 'includes/db_connect.php';

$page_key = 'caution_to_applicants';

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

$page_title = ($page_data['page_title'] ?? 'Caution to Applicants') . " - Valley View University";
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
    
    .caution-card { transition: all 0.4s ease; }
    .caution-card:hover { 
        transform: translateY(-15px);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
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
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="Caution to Applicants" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Important Notice'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Caution to'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Applicants'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Protecting your future starts with a secure application process.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <?php foreach ($page_sections as $section): ?>
                <?php if ($section['section_key'] === 'warning'): ?>
                    <div class="text-center mb-20 animate-fadeInUp">
                        <div class="inline-flex items-center gap-4 px-8 py-4 mb-8 rounded-3xl bg-red-600 shadow-2xl">
                            <span class="material-symbols-outlined text-5xl text-white">warning</span>
                            <span class="text-3xl md:text-4xl font-black uppercase tracking-wider text-white"><?php echo strip_tags($section['section_title']); ?></span>
                        </div>
                        <div class="glass p-10 md:p-14 rounded-[3rem] shadow-2xl border-t-[12px] border-red-600">
                            <p class="text-3xl md:text-4xl text-gray-900 dark:text-white font-black leading-tight max-w-5xl mx-auto">
                                <?php echo nl2br(strip_tags($section['section_subtitle'])); ?>
                            </p>
                        </div>
                    </div>
                <?php elseif ($section['section_key'] === 'verify'): ?>
                    <div class="animate-fadeInUp">
                        <div class="text-center mb-16">
                            <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                            <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-3xl mx-auto"><?php echo strip_tags($section['section_subtitle']); ?></p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 mb-24">
                            <?php if (isset($items_map['verify'])): ?>
                                <?php foreach ($items_map['verify'] as $item): ?>
                                <div class="glass p-10 md:p-14 rounded-[2.5rem] text-center border-b-[8px] border-blue-600 shadow-xl">
                                    <span class="material-symbols-outlined text-6xl text-blue-600 mb-6"><?php echo strip_tags($item['item_icon'] ?? 'call'); ?></span>
                                    <h4 class="text-3xl md:text-4xl font-black mb-4 text-gray-900 dark:text-white"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <p class="text-xl md:text-2xl lg:text-3xl font-bold text-blue-600 break-all"><?php echo strip_tags($item['item_stat_value']); ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mb-24 animate-fadeInUp">
                        <div class="text-center mb-16">
                            <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                            <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-3xl mx-auto"><?php echo strip_tags($section['section_subtitle']); ?></p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?php echo ($section['section_key'] === 'red_flags') ? '3' : '2'; ?> gap-10 md:gap-14">
                            <?php if (isset($items_map[$section['section_key']])): ?>
                                <?php foreach ($items_map[$section['section_key']] as $item): ?>
                                <div class="caution-card glass p-10 md:p-14 rounded-[3rem] shadow-xl border-t-[12px] border-<?php echo $item['item_color'] ?? 'blue-600'; ?>">
                                    <?php if ($item['item_icon']): ?>
                                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl md:rounded-3xl bg-<?php echo explode('-', $item['item_color'])[0]; ?>-600 flex items-center justify-center text-white shadow-lg mb-8 md:mb-10">
                                        <span class="material-symbols-outlined text-4xl md:text-5xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium leading-[1.8]">
                                        <?php echo strip_tags($item['item_description']); ?>
                                    </p>
                                    <?php if ($item['item_link']): ?>
                                    <a href="<?php echo strip_tags($item['item_link']); ?>" class="mt-10 inline-flex items-center gap-3 text-xl md:text-2xl text-blue-600 font-black hover:gap-5 transition-all uppercase tracking-wider">
                                        Learn More <span class="material-symbols-outlined text-3xl">arrow_forward</span>
                                    </a>
                                    <?php endif; ?>
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

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-32 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-8 leading-tight">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-2xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-blue-900">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Directly'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-white">support_agent</span>
                        Contact Admissions
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
