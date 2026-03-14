<?php
$page_title = "The VVU - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch Page Content
try {
    $page_stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'the_campus'");
    $page_stmt->execute();
    $page_data = $page_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $page_data = [];
}

// Fetch sections
try {
    $sections_stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'the_campus' ORDER BY display_order");
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
    $items_stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'the_campus' AND is_active = 1 ORDER BY display_order");
    $items_stmt->execute();
    $all_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    $items_map = [];
    foreach ($all_items as $item) {
        $items_map[$item['section_key']][] = $item;
    }
} catch (PDOException $e) {
    $items_map = [];
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
    .feature-card {
        transition: all 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-10px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCwMQREF1DNTiVX8Mt0yT_NXwihbW7HzEPMJWSNgBQCilTtI-Pyqwx0uf9UU1yMrmyCrXnx6GTxjWDSvbYKs1wCTGuYSJMd2wgD6bECQqPP84Ec0-M-7ROpYFQ7abu2FYSfGFlKV67C1vCRZkwCpYOR8wyyFr2Hn4inae6smuiwWtZUcdoGjyb4hX0aZBacOylHmMC6mBzEJy-CcMqb-ACqd8gK33jYhXbzNUejTEVIO-hLydTXEXEKoFBlnayg56kMq5_r5-6juVQr'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-10 animate-fadeInUp">
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <span class="text-white text-lg font-bold uppercase tracking-wider"><?php echo strip_tags($page_data['hero_badge'] ?? 'Our Environment'); ?></span>
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'The Valley View'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Experience'); ?></span>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 max-w-4xl mx-auto leading-relaxed animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($page_data['hero_description'] ?? 'Explore our beautiful campuses, state-of-the-art facilities, and the vibrant community that makes Valley View University a home away from home.'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Campus Highlights Section -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($sections_map['highlights']['section_title'] ?? 'Why Choose VVU?'); ?></h2>
                <div class="h-1.5 w-32 bg-blue-600 mx-auto rounded-full mb-6"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sections_map['highlights']['section_subtitle'] ?? 'Experience a unique blend of academic rigor, international culture, and spiritual growth.'); ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                <?php 
                $highlights = $items_map['highlights'] ?? [];
                foreach ($highlights as $highlight): 
                    $border_color = $highlight['item_color'] ?? 'blue-600';
                ?>
                <div class="feature-card relative group">
                    <div class="relative h-full glass p-8 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($border_color); ?> flex flex-col">
                        <div class="w-16 h-16 rounded-2xl bg-<?php echo strip_tags($border_color); ?> flex items-center justify-center text-white shadow-lg mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($highlight['item_icon'] ?? 'verified'); ?></span>
                        </div>
                        <?php if (!empty($highlight['item_subtitle'])): ?>
                        <p class="text-xl text-blue-600 dark:text-blue-400 font-bold italic mb-3"><?php echo strip_tags($highlight['item_subtitle']); ?></p>
                        <?php endif; ?>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($highlight['item_title']); ?></h3>
                        <p class="text-2xl text-gray-700 dark:text-gray-300 mb-6 flex-grow leading-relaxed">
                            <?php echo nl2br(strip_tags($highlight['item_description'])); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Campus Features Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($sections_map['features']['section_title'] ?? 'Life on Campus'); ?></h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sections_map['features']['section_subtitle'] ?? 'Discover the facilities and standards that make VVU a leader in private education.'); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                $features = $items_map['features'] ?? [];
                foreach ($features as $feature): 
                ?>
                <div class="group p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-14 h-14 rounded-2xl bg-<?php echo strip_tags($feature['item_color'] ?? 'blue-600'); ?> flex items-center justify-center text-white shadow-lg mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl text-white"><?php echo strip_tags($feature['item_icon'] ?? 'school'); ?></span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($feature['item_title']); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo nl2br(strip_tags($feature['item_description'])); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-yellow-500/10 rounded-full blur-[120px] -mr-60 -mt-60"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[120px] -ml-60 -mb-60"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($page_data['cta_title'] ?? 'Experience the Campus, Start Your Journey'); ?>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['cta_subtitle'] ?? 'Join a university that values your future as much as you do. Explore our programs and apply today.'); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-8 py-4 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="admissions.php" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl">info</span>
                        Admission Info
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>