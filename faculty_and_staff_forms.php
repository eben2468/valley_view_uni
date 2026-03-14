<?php
$page_title = "Faculty and Staff Forms - Valley View University";
$active_page = "resources";
require_once 'includes/db_connect.php';

// Fetch data from database
$page_key = 'faculty_staff_forms';
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
    .text-gradient {
        background: linear-gradient(to right, #2563eb, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .form-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .form-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    .icon-container {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
    }
    .icon-container span {
        color: white !important;
        font-size: 40px;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'images/faculty_of_science_hero.png'); ?>" 
                 alt="Faculty and Staff" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'Resources for Faculty & Staff'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Official Forms'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? '& Documentation'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? '"Streamlining administrative processes for our dedicated faculty and staff members."'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Forms Section -->
    <?php 
    $forms_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'downloadable_forms'))[0] ?? null;
    if ($forms_section): 
    ?>
    <section class="py-24 bg-white dark:bg-gray-900 relative z-20 -mt-20 mx-auto max-w-[95%] rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
        <div class="w-full px-8 md:px-16 text-center">
            <div class="max-w-5xl mx-auto mb-20">
                <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($forms_section['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags($forms_section['section_subtitle']); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 text-center">
                <?php foreach ($grouped_items['downloadable_forms'] ?? [] as $form): ?>
                <div class="form-card group p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 flex flex-col items-center">
                    <div class="icon-container bg-gradient-to-br from-<?php echo str_replace('blue-600', 'blue-600', $form['item_color']); ?> to-<?php echo str_replace('blue-600', 'blue-800', $form['item_color']); ?>">
                        <span class="material-symbols-outlined text-white"><?php echo strip_tags($form['item_icon']); ?></span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($form['item_title']); ?></h3>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 mb-10 flex-grow leading-relaxed font-medium">
                        <?php echo strip_tags($form['item_description']); ?>
                    </p>
                    <a href="<?php echo strip_tags($form['item_link']); ?>" target="_blank" class="w-full py-6 bg-<?php echo strip_tags($form['item_color']); ?> text-white text-2xl font-bold rounded-2xl hover:brightness-110 transition-all shadow-lg flex items-center justify-center gap-3" style="background-color: <?php echo str_contains($form['item_color'], '#') ? $form['item_color'] : ''; ?>">
                        Download <?php echo strtoupper(pathinfo($form['item_link'], PATHINFO_EXTENSION) ?: 'File'); ?>
                        <span class="material-symbols-outlined text-white">download</span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Instructions Section -->
    <?php 
    $guides_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'guidelines'))[0] ?? null;
    if ($guides_section): 
    ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-8 md:px-16 text-center">
            <div class="max-w-5xl mx-auto mb-20 text-center">
                <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($guides_section['section_title']); ?></h2>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags($guides_section['section_subtitle']); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-left">
                <?php foreach ($grouped_items['guidelines'] ?? [] as $guide): ?>
                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-<?php echo strip_tags($guide['item_color'] ?? 'blue-600'); ?> flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($guide['item_icon']); ?></span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($guide['item_title']); ?></h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($guide['item_description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Support Section -->
    <section class="py-24 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        
        <div class="relative z-10 w-full max-w-[95%] mx-auto px-8 md:px-16 text-center">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-6xl sm:text-7xl md:text-8xl font-black text-white mb-8"><?php echo strip_tags($hero['cta_title'] ?? 'Need Assistance?'); ?></h2>
                <p class="text-3xl sm:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($hero['cta_subtitle'] ?? 'If you have questions regarding any of these forms, please contact the Registry or Human Resources office.'); ?>
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 max-w-6xl mx-auto text-left">
                    <div class="p-12 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20">
                        <h4 class="text-4xl font-black text-yellow-400 mb-6">Registry Office</h4>
                        <p class="text-3xl text-white font-bold">+233 307011832</p>
                        <p class="text-2xl text-blue-200 mt-4 font-medium">Location: Administration Block, Room 102</p>
                    </div>
                    <div class="p-12 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20">
                        <h4 class="text-4xl font-black text-yellow-400 mb-6">HR Department</h4>
                        <p class="text-3xl text-white font-bold"><?php echo strip_tags($hero['cta_button_text'] ?? 'hr@vvu.edu.gh'); ?> </p>
                        <p class="text-2xl text-blue-200 mt-4 font-medium">Available Mon-Fri, 8am - 5pm</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
