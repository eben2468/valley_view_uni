<?php
/**
 * Valley View University - Provisional Admission List Page
 * Modern, responsive page to display admission lists (PDFs)
 */

require_once('includes/db_connect.php');

$page_key = 'provisional_admission_list';

// Fetch page content from academic_pages_content
try {
    $page_stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ?");
    $page_stmt->execute([$page_key]);
    $page_data = $page_stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    
    // Fetch sections
    $sections_stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
    $sections_stmt->execute([$page_key]);
    $page_sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);
    $sections_map = [];
    foreach ($page_sections as $s) {
        $sections_map[$s['section_key']] = $s;
    }

    // Fetch items grouped by section
    $items_stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY display_order");
    $items_stmt->execute([$page_key]);
    $all_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    $items_map = [];
    foreach ($all_items as $item) {
        $items_map[$item['section_key']][] = $item;
    }
} catch (PDOException $e) {
    $page_data = [];
    $sections_map = [];
    $items_map = [];
}

$page_title = ($page_data['page_title'] ?? "Provisional Admission List") . " - Valley View University";
$active_page = "admissions";

include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
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

    .list-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .list-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .pdf-icon-bg {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'images/pro-bg.jpg'); ?>" 
                 alt="Admissions List" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400">
                        <?php echo strip_tags($page_data['hero_badge'] ?? 'Admissions'); ?>
                    </span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Provisional'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3">
                        <?php echo strip_tags($page_data['hero_subtitle'] ?? 'Admission List'); ?>
                    </span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Check your status and join the family of excellence.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <?php foreach ($page_sections as $section): ?>
    <?php if ($section['section_key'] === 'official_lists'): ?>
        <section class="py-24 relative z-20 -mt-16">
            <div class="container">
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl overflow-hidden p-10 md:p-16 border border-gray-100 dark:border-gray-700">
                    <div class="max-w-4xl mx-auto text-center mb-16 px-4">
                        <div class="inline-flex items-center gap-4 px-6 py-2.5 mb-8 rounded-2xl bg-gradient-to-r from-blue-700 to-blue-500 shadow-xl text-white mx-auto">
                            <span class="material-symbols-outlined text-2xl text-white">description</span>
                            <span class="text-base font-black uppercase tracking-[0.2em] text-white"><?php echo strip_tags($section['section_title']); ?></span>
                        </div>
                        <h2 class="text-4xl md:text-6xl font-black text-gray-900 dark:text-white mb-8 tracking-tight">
                            <?php echo strip_tags($section['section_subtitle'] ?? 'Admission Documents'); ?>
                        </h2>
                        <div class="h-2 w-24 bg-blue-600 mx-auto rounded-full mb-8"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                        <?php 
                        $list_items = $items_map['official_lists'] ?? [];
                        if (empty($list_items)):
                        ?>
                            <div class="col-span-full text-center py-20 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                                <span class="material-symbols-outlined text-7xl text-gray-300 dark:text-gray-600 mb-6">description</span>
                                <h3 class="text-2xl font-bold text-gray-500 dark:text-gray-400"><?php echo strip_tags($page_data['empty_list_message'] ?: 'No admission lists published yet.'); ?></h3>
                            </div>
                        <?php else: ?>
                            <?php foreach ($list_items as $item): ?>
                                <div class="list-card group bg-gray-50 dark:bg-gray-900 p-8 rounded-[2rem] border border-gray-100 dark:border-gray-800">
                                    <div class="flex flex-col h-full">
                                        <div class="w-16 h-16 rounded-2xl pdf-icon-bg flex items-center justify-center text-white mb-6 shadow-lg group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined text-3xl text-white" style="color: white !important;">picture_as_pdf</span>
                                        </div>
                                        <div class="mb-4">
                                            <span class="px-3 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm font-bold uppercase tracking-wider mb-3 inline-block">
                                                <?php echo strip_tags($item['item_stat_value'] ?? 'PDF'); ?>
                                            </span>
                                            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                                                <?php echo strip_tags($item['item_title']); ?>
                                            </h3>
                                            <p class="text-lg text-gray-600 dark:text-gray-400 font-medium leading-relaxed line-clamp-3">
                                                <?php echo strip_tags($item['item_description']); ?>
                                            </p>
                                        </div>
                                        <div class="mt-auto pt-6 border-t border-gray-200 dark:border-gray-800">
                                            <a href="<?php echo strip_tags($item['item_link']); ?>" target="_blank" class="flex items-center justify-between w-full group/btn">
                                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">View List</span>
                                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white group-hover/btn:translate-x-2 transition-transform shadow-md">
                                                    <span class="material-symbols-outlined text-xl">arrow_forward</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php elseif ($section['section_key'] === 'guidance'): ?>
        <section class="py-24 bg-gray-50 dark:bg-gray-950">
            <div class="container">
                <div class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div>
                            <span class="inline-block px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold text-sm mb-6 uppercase tracking-widest">Next Steps</span>
                            <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($section['section_title']); ?></h2>
                            <div class="space-y-8">
                                <?php if (isset($items_map['guidance'])): ?>
                                    <?php foreach ($items_map['guidance'] as $item): ?>
                                    <div class="flex gap-6 animate-fadeInUp">
                                        <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black shrink-0 shadow-lg">
                                            <?php echo strip_tags($item['item_stat_value'] ?? '!'); ?>
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($item['item_title']); ?></h4>
                                            <p class="text-lg text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-blue-600 to-yellow-400 rounded-[3rem] blur-2xl opacity-20"></div>
                            <img src="<?php echo strip_tags($section['section_image'] ?: 'Education-Website-and-AdminPanel/images/h-adm.jpg'); ?>" alt="Student Success" class="relative w-full h-[500px] object-cover rounded-[3rem] shadow-2xl">
                            <div class="absolute -bottom-8 -right-8 glass p-8 rounded-[2rem] shadow-2xl max-w-xs animate-float">
                                <h5 class="text-xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($page_data['help_title'] ?: 'Need Help?'); ?></h5>
                                <p class="text-gray-600 dark:text-gray-400 font-bold mb-4"><?php echo strip_tags($page_data['help_description'] ?: 'Our admission officers are ready to assist you.'); ?></p>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $page_data['help_phone'] ?: '+233302230990'); ?>" class="flex items-center gap-3 text-blue-600 font-black">
                                    <span class="material-symbols-outlined">call</span>
                                    <?php echo strip_tags($page_data['help_phone'] ?: '+233 302 230 990'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php endforeach; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-yellow-400 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        </div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-black text-white mb-6">
                    <?php echo strip_tags($page_data['cta_title']); ?>
                </h2>
                <p class="text-lg text-blue-100 mb-10 font-medium leading-relaxed">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'contact_us.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">chat</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Contact Admissions'); ?>
                    </a>
                    <?php if ($page_data['cta_button_text_2']): ?>
                    <a href="<?php echo strip_tags($page_data['cta_button_link_2'] ?: 'admissions.php'); ?>" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4 backdrop-blur-md">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        <?php echo strip_tags($page_data['cta_button_text_2']); ?>
                    </a>
                    <?php else: ?>
                    <a href="admissions.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4 backdrop-blur-md">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        Admission Requirements
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
