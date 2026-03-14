<?php
$page_title = "Fee Structure - Valley View University";
$active_page = "admissions";
require_once 'includes/db_connect.php';
include 'includes/header.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'fees_structure'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'fees_structure' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_raw as $s) {
    $sections[$s['section_key']] = $s;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'fees_structure' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($items_raw as $i) {
    $items[$i['section_key']][] = $i;
}
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
    
    .fee-card { transition: all 0.4s ease; }
    .fee-card:hover { 
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
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); ?>" 
                 alt="Fee Structure" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-7xl mx-auto text-center">
                <?php if ($page_data['hero_badge']): ?>
                <div class="inline-flex items-center gap-4 px-12 py-5 mb-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-4 h-4 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge']); ?></span>
                </div>
                <?php endif; ?>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-12 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $page_data['hero_title']; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-6"><?php echo $page_data['hero_subtitle']; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-5xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description']); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Tuition & Fees Section -->
    <?php if (isset($sections['tuition_fees'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-4 px-8 py-3.5 mb-8 rounded-full bg-blue-600 shadow-lg">
                        <span class="material-symbols-outlined text-3xl text-white">payments</span>
                        <span class="text-xl font-black uppercase tracking-wider text-white"><?php echo strip_tags($sections['tuition_fees']['section_title']); ?></span>
                    </div>
                    <?php if ($sections['tuition_fees']['section_subtitle']): ?>
                    <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-8">
                        <?php echo $sections['tuition_fees']['section_subtitle']; ?>
                    </h2>
                    <?php endif; ?>
                    <div class="h-2 w-48 bg-blue-600 mx-auto rounded-full mb-8"></div>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-5xl mx-auto">
                        <?php echo strip_tags($sections['tuition_fees']['section_description']); ?>
                    </p>
                </div>

                <!-- Fee Schedules Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-16 mb-40">
                    <?php if (isset($items['tuition_fees'])): ?>
                    <?php foreach ($items['tuition_fees'] as $item): ?>
                    <div class="fee-card glass p-16 rounded-[4rem] shadow-xl border-t-[16px] border-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex flex-col justify-between">
                        <div>
                            <div class="w-24 h-24 rounded-3xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center text-white shadow-lg mb-10">
                                <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                            </div>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-10">
                                <?php echo strip_tags($item['item_description']); ?>
                            </p>
                        </div>
                        <a href="<?php echo strip_tags($item['item_link']); ?>" download class="download-btn inline-flex items-center justify-center gap-4 px-12 py-6 bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> <?php echo strpos($item['item_color'], 'yellow') !== false ? 'text-blue-900' : 'text-white'; ?> text-2xl font-black rounded-3xl hover:opacity-90 transition-all">
                            <span class="material-symbols-outlined text-4xl <?php echo strpos($item['item_color'], 'yellow') !== false ? 'text-blue-900' : 'text-white'; ?>">download</span>
                            Download PDF
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Payment Methods -->
                <?php if (isset($sections['payment_methods'])): ?>
                <div class="mb-40">
                    <div class="text-center mb-24">
                        <h2 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo $sections['payment_methods']['section_title']; ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-4xl mx-auto"><?php echo strip_tags($sections['payment_methods']['section_subtitle']); ?></p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <?php if (isset($items['payment_methods'])): ?>
                        <?php foreach ($items['payment_methods'] as $item): ?>
                        <div class="glass p-12 rounded-[3rem] border-l-[16px] border-blue-600 shadow-xl">
                            <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white mb-8">
                                <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                            </div>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h4>
                            <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Financial Policies -->
                <?php if (isset($sections['financial_policies'])): ?>
                <div class="bg-blue-900 rounded-[4rem] p-20 text-white shadow-2xl relative overflow-hidden mb-40">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48"></div>
                    <div class="relative z-10">
                        <h2 class="text-6xl font-black mb-12"><?php echo strip_tags($sections['financial_policies']['section_title']); ?></h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <?php if (isset($items['financial_policies'])): ?>
                            <?php foreach ($items['financial_policies'] as $item): ?>
                            <div class="flex items-start gap-8">
                                <div class="w-20 h-20 rounded-2xl bg-white/10 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                                </div>
                                <div>
                                    <h4 class="text-3xl font-bold mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <p class="text-xl text-blue-100"><?php echo strip_tags($item['item_description']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Support Section -->
    <?php if (isset($sections['support'])): ?>
    <section class="py-40 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-12">
                    <?php echo $sections['support']['section_title']; ?>
                </h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium mb-20 max-w-4xl mx-auto leading-relaxed">
                    <?php echo strip_tags($sections['support']['section_description']); ?>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <?php if (isset($items['support_info'])): ?>
                    <?php foreach ($items['support_info'] as $item): ?>
                    <div class="glass p-12 rounded-[3rem] card-hover bg-blue-600/10">
                        <span class="material-symbols-outlined text-6xl text-white mb-8"><?php echo strip_tags($item['item_icon']); ?></span>
                        <h4 class="text-3xl font-black mb-4 text-gray-900 dark:text-white"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-10 leading-tight">
                    <?php echo $page_data['cta_title']; ?> <br><span class="text-lg sm:text-xl md:text-2xl lg:text-4xl text-white font-medium"><?php echo $page_data['cta_subtitle']; ?></span>
                </h2>
                <div class="flex flex-col sm:flex-row gap-10 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-3xl font-bold rounded-full transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-5xl text-blue-900">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="scholarships.php" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-3xl font-bold rounded-full transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-5xl text-white">info</span>
                        Scholarship Info
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
