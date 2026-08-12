<?php
/**
 * Valley View University - Scholarships & Financial Aid Page
 * Fetching content dynamically from academic_pages_* tables
 */
require_once 'includes/db_connect.php';

$page_key = 'scholarships';

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

$page_title = ($page_data['page_title'] ?? 'Scholarships & Financial Aid') . " - Valley View University";
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
    
    .scholarship-card { transition: all 0.4s ease; }
    .scholarship-card:hover { 
        transform: translateY(-15px);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
    }
    
    .text-gradient {
        background: linear-gradient(to right, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5rem;
        height: 5rem;
        border-radius: 9999px;
        background-color: #2563eb;
        color: #ffffff;
        font-size: 1.875rem;
        font-weight: 900;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="Scholarships" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Financial Support'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Scholarships'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($page_data['hero_subtitle'] ?? '& Awards'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Empowering excellence through financial aid.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction and Categories Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <?php foreach ($page_sections as $section): ?>
                <?php if ($section['section_key'] === 'intro'): ?>
                    <div class="text-center mb-24 animate-fadeInUp px-4">
                        <div class="inline-flex items-center gap-4 px-6 py-2.5 mb-8 rounded-2xl bg-gradient-to-r from-blue-700 to-blue-500 shadow-xl text-white mx-auto">
                            <span class="material-symbols-outlined text-2xl text-white">workspace_premium</span>
                            <span class="text-xl font-black uppercase tracking-[0.2em] text-white"><?php echo strip_tags($section['section_title']); ?></span>
                        </div>
                        <h2 class="text-4xl md:text-6xl font-black text-gray-900 dark:text-white mb-10 tracking-tight max-w-4xl mx-auto">
                            <?php echo !empty($section['section_description']) ? htmlspecialchars_decode(strip_tags($section['section_description'], '<span><br><i><b><strong><em>')) : 'Investing in Your <span class="text-blue-600">Future Success</span>'; ?>
                        </h2>
                        <div class="h-2 w-24 bg-blue-600 mx-auto rounded-full mb-12"></div>
                        <div class="inline-flex items-center justify-center bg-white dark:bg-gray-800 px-10 py-5 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 dark:border-gray-800 transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
                            <p class="text-2xl md:text-3xl text-slate-600 dark:text-slate-300 font-medium m-0">
                                <?php echo nl2br(strip_tags($section['section_subtitle'])); ?>
                            </p>
                        </div>
                    </div>
                <?php elseif ($section['section_key'] === 'categories'): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-24 animate-fadeInUp">
                        <?php if (isset($items_map['categories'])): ?>
                            <?php foreach ($items_map['categories'] as $item): ?>
                            <div class="scholarship-card glass p-10 rounded-[3rem] shadow-xl border-t-[12px] border-<?php echo $item['item_color'] ?? 'blue-600'; ?>">
                                <div class="w-16 h-16 rounded-2xl bg-<?php echo explode('-', $item['item_color'])[0]; ?>-600 flex items-center justify-center text-white shadow-lg mb-8">
                                    <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?? 'workspace_premium'); ?></span>
                                </div>
                                <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                    <?php echo strip_tags($item['item_description']); ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php elseif ($section['section_key'] === 'process'): ?>
                    <div class="mb-24 animate-fadeInUp">
                        <div class="text-center mb-16">
                            <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-2xl mx-auto"><?php echo strip_tags($section['section_subtitle']); ?></p>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                            <?php if (isset($items_map['process'])): ?>
                                <?php foreach ($items_map['process'] as $item): ?>
                                <div class="glass p-12 rounded-[3rem] relative overflow-hidden group">
                                    <div class="step-number"><?php echo strip_tags($item['item_stat_value']); ?></div>
                                    <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed font-bold"><?php echo strip_tags($item['item_description']); ?></p>
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

    <!-- Success Stories Section -->
    <?php 
    $success_section = null;
    foreach ($page_sections as $s) if ($s['section_key'] === 'success') $success_section = $s;
    if ($success_section && isset($items_map['success'])): 
    ?>
    <section class="py-24 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-4xl md:text-5xl font-black text-white mb-4 leading-tight"><?php echo strip_tags($success_section['section_title'] ?? 'How Awards Are Decided'); ?></h2>
                        <div class="h-2 w-28 bg-yellow-400 rounded-full mb-10"></div>

                        <ul class="space-y-8">
                            <?php foreach ($items_map['success'] as $item): ?>
                            <li class="flex items-start gap-5">
                                <div class="w-14 h-14 shrink-0 rounded-2xl bg-yellow-400 flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-3xl text-blue-900"><?php echo strip_tags($item['item_icon'] ?: 'check_circle'); ?></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-white mb-1 leading-tight"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <p class="text-xl text-blue-100 leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="relative">
                        <?php
                        /*
                         * The slot is 4:5 rather than a square: the uploaded photos are
                         * landscape camera originals, and forcing them into a square made
                         * object-cover discard most of the frame and zoom hard into what
                         * was left, which is what made the picture look soft.
                         *
                         * object-top keeps faces in shot when the crop does bite, and the
                         * width is capped in CSS pixels so the browser is never asked to
                         * paint a 6000px original into a small box.
                         */
                        $success_image = !empty($success_section['section_image'])
                            ? strip_tags($success_section['section_image'])
                            : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
                        ?>
                        <div class="aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl rotate-3 group hover:rotate-0 transition-transform duration-700 max-w-md mx-auto">
                            <img src="<?php echo htmlspecialchars($success_image); ?>"
                                 alt="Student Success"
                                 width="800" height="1000" loading="lazy" decoding="async"
                                 class="w-full h-full object-cover object-top">
                        </div>
                        <div class="absolute -bottom-6 -left-6 glass p-8 rounded-2xl shadow-2xl animate-float">
                            <h4 class="text-3xl font-black text-blue-900 dark:text-white"><?php echo !empty($success_section['section_subtitle']) ? strip_tags($success_section['section_subtitle']) : 'Merit &amp; Need'; ?></h4>
                            <p class="text-xl font-bold text-gray-600 dark:text-gray-400"><?php echo !empty($success_section['section_description']) ? strip_tags($success_section['section_description']) : 'The two bases for every award'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Resources Section -->
    <?php 
    $resource_section = null;
    foreach ($page_sections as $s) if ($s['section_key'] === 'resources') $resource_section = $s;
    if ($resource_section && isset($items_map['resources'])): 
    ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($resource_section['section_title']); ?></h2>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-2xl mx-auto"><?php echo strip_tags($resource_section['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($items_map['resources'] as $item): ?>
                    <?php if ($item['item_link']): ?>
                        <a href="<?php echo strip_tags($item['item_link']); ?>" class="glass p-10 rounded-[3rem] hover:bg-blue-600 group transition-all duration-500 shadow-xl">
                            <div class="w-16 h-16 rounded-2xl bg-blue-600 group-hover:bg-white flex items-center justify-center mb-8 transition-colors">
                                <span class="material-symbols-outlined text-3xl text-white group-hover:text-blue-600"><?php echo strip_tags($item['item_icon'] ?? 'description'); ?></span>
                            </div>
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white group-hover:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 group-hover:text-blue-50 font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                        </a>
                    <?php else: ?>
                        <div class="glass p-10 rounded-[3rem] shadow-xl border-l-[12px] border-yellow-500">
                            <div class="w-16 h-16 rounded-2xl bg-yellow-500 flex items-center justify-center mb-8">
                                <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?? 'contact_support'); ?></span>
                            </div>
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium mb-6 font-bold"><?php echo nl2br(strip_tags($item['item_description'])); ?></p>
                        </div>
                    <?php endif; ?>
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
                <h2 class="text-3xl sm:text-4xl font-black text-white mb-6 leading-tight">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-2xl md:text-3xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed font-normal">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-blue-900">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Now'); ?>
                    </a>
                    <a href="<?php echo strip_tags($page_data['cta_button_link_2'] ?: 'contact_us.php'); ?>" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-white">support_agent</span>
                        <?php echo strip_tags($page_data['cta_button_text_2'] ?: 'Contact Admissions'); ?>
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