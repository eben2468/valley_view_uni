<?php
$page_title = "University Policies - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch data from database
$page_key = 'policies';
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ? AND is_active = 1");
$stmt->execute([$page_key]);
$hero = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
$stmt->execute([$page_key]);
$sections = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute([$page_key]);
$all_items = $stmt->fetchAll();

$grouped_items = [];
foreach ($all_items as $item) {
    if ($item['extra_data']) {
        $item['documents'] = json_decode($item['extra_data'], true) ?: [];
    }
    $grouped_items[$item['section_key']][] = $item;
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
    .policy-card {
        transition: all 0.3s ease;
    }
    .policy-card:hover {
        transform: translateY(-10px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'uploads/strategy/img_1770600004_69893644a6dec.jpg'); ?>" 
                 alt="University Policies" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'Governance & Standards'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'University'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Policies'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? '"A comprehensive guide to the principles, regulations, and procedures that govern Valley View University. We ensure transparency and fairness in all our operations."'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Policy Categories Section -->
    <?php 
    $framework_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'framework'))[0] ?? null;
    if ($framework_section): 
    ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container text-center">
            <div class="max-w-4xl mx-auto mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($framework_section['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($framework_section['section_subtitle']); ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <?php foreach ($grouped_items['framework'] ?? [] as $category): ?>
                <div class="policy-card relative group">
                    <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($category['item_color']); ?> flex flex-col text-left">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo strip_tags($category['item_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($category['item_icon']); ?></span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($category['item_title']); ?></h3>
                        <p class="text-3xl text-gray-700 dark:text-gray-300 mb-8 flex-grow leading-relaxed">
                            <?php echo strip_tags($category['item_description']); ?>
                        </p>
                        <div class="space-y-4">
                            <?php if (!empty($category['documents'])): ?>
                                <?php foreach ($category['documents'] as $doc): ?>
                                <a href="<?php echo strip_tags($doc['url']); ?>" download class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group/link">
                                    <span class="material-symbols-outlined text-<?php echo strip_tags($doc['color'] ?? 'blue-600'); ?> text-4xl"><?php echo strip_tags($doc['icon'] ?? 'picture_as_pdf'); ?></span>
                                    <span class="text-2xl text-gray-700 dark:text-gray-300 font-bold"><?php echo strip_tags($doc['title']); ?></span>
                                    <span class="ml-auto text-sm bg-<?php echo strip_tags($category['item_color']); ?> text-white px-3 py-1 rounded-full opacity-0 group-hover/link:opacity-100 transition-opacity">Download PDF</span>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Search & Quick Access Section -->
    <?php 
    $links_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'quick_links'))[0] ?? null;
    if ($links_section): 
    ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container text-center">
            <div class="max-w-4xl mx-auto mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($links_section['section_title']); ?></h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($links_section['section_subtitle']); ?></p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-yellow-500 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative flex items-center bg-white dark:bg-gray-900 rounded-full p-2 shadow-2xl">
                        <div class="pl-6 text-gray-400">
                            <span class="material-symbols-outlined text-4xl">search</span>
                        </div>
                        <input type="text" placeholder="Search for policies (e.g., Admissions, Conduct, Finance)..." 
                               class="w-full bg-transparent border-none focus:ring-0 text-2xl py-6 px-6 text-gray-900 dark:text-white placeholder-gray-400">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-5 rounded-full text-2xl font-bold transition-all transform hover:scale-105 shadow-lg">
                            Search
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-20 text-left">
                <?php foreach ($grouped_items['quick_links'] ?? [] as $link): ?>
                <div class="group p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-16 h-16 rounded-2xl bg-<?php echo strip_tags($link['item_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($link['item_icon']); ?></span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($link['item_title']); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-6">
                        <?php echo strip_tags($link['item_description']); ?>
                    </p>
                    <a href="<?php echo strip_tags($link['item_link']); ?>" class="text-<?php echo strip_tags($link['item_color']); ?> font-bold text-xl flex items-center gap-2 hover:gap-4 transition-all">
                        <?php echo strip_tags($link['item_subtitle']); ?> <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
                <?php endforeach; ?>
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
        
        <div class="container relative z-10 text-center">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($hero['cta_title'] ?? 'Committed to'); ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo strip_tags($hero['cta_subtitle'] ?? 'Integrity & Transparency'); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Our policies are designed to protect and empower every member of the Valley View University family.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="mission_and_vision.php" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">visibility</span>
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Our Mission'); ?>
                    </a>
                    <a href="core_values.php" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">verified</span>
                        Our Values
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>