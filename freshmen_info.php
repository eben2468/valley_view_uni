<?php
$page_title = "Freshmen Information - Valley View University";
$active_page = "resources";
require_once 'includes/db_connect.php';

// Fetch page content
$page_key = 'freshmen_info';
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ?");
$stmt->execute([$page_key]);
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
$stmt->execute([$page_key]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute([$page_key]);
$all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$items_by_section = [];
foreach ($all_items as $item) {
    $items_by_section[$item['section_key']][] = $item;
}

include 'includes/header.php';

// Default values
$hero_badge = $page_data['hero_badge'] ?? 'Welcome Freshmen';
$hero_title = $page_data['hero_title'] ?? 'Freshmen Info';
$hero_subtitle = $page_data['hero_subtitle'] ?? 'Your Journey Starts Here';
$hero_description = $page_data['hero_description'] ?? '"Welcome to Valley View University - where your academic dreams come to life and your future begins."';
$hero_image = $page_data['hero_image'] ?? 'images/freshmen_hero_bg.png';
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
    .info-card {
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .material-symbols-outlined {
        /* color: white !important; */
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[50vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero_image); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero_badge); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $hero_title; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero_subtitle); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo $hero_description; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Dynamic Sections -->
    <?php foreach ($sections as $section): ?>
        <?php 
        $section_key = $section['section_key'];
        $section_items = $items_by_section[$section_key] ?? [];
        if (empty($section_items) && $section_key !== 'grading') continue;
        ?>

        <?php if ($section_key === 'requirements'): ?>
            <!-- Entry Requirements Section -->
            <section class="py-24 bg-white dark:bg-gray-900" id="requirements">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-20">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($section['section_subtitle']); ?></p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                        <?php foreach ($section_items as $item): ?>
                        <div class="info-card relative group">
                            <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo str_replace('bg-', '', $item['item_color'] ?: 'blue-600'); ?> flex flex-col bg-gradient-to-br from-<?php echo str_replace('-600', '', $item['item_color'] ?: 'blue'); ?>-50 to-white dark:from-<?php echo str_replace('-600', '', $item['item_color'] ?: 'blue'); ?>-900/20 dark:to-gray-900">
                                <div class="w-24 h-24 rounded-3xl bg-<?php echo $item['item_color'] ?: 'blue-600'; ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'school'); ?></span>
                                </div>
                                <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                                <div class="text-2xl text-gray-700 dark:text-gray-300 mb-8 flex-grow leading-relaxed space-y-4">
                                    <?php echo $item['item_description']; ?>
                                </div>
                                <?php if ($item['item_subtitle']): ?>
                                <ul class="space-y-4 mb-8">
                                    <li class="flex items-center gap-4">
                                        <span class="material-symbols-outlined text-<?php echo $item['item_color'] ?: 'blue-600'; ?> text-4xl">check_circle</span>
                                        <span class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags($item['item_subtitle']); ?></span>
                                    </li>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($section_key === 'checklist'): ?>
            <!-- Freshmen Checklist Section -->
            <section class="py-24 bg-gray-50 dark:bg-gray-950" id="checklist">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-16">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($section['section_subtitle']); ?></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($section_items as $item): ?>
                        <div class="group p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                            <div class="w-16 h-16 rounded-2xl bg-<?php echo $item['item_color'] ?: 'blue-600'; ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'task_alt'); ?></span>
                            </div>
                            <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                            <div class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                <?php echo $item['item_description']; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($section_key === 'grading'): ?>
            <!-- Grading System Section -->
            <section class="py-24 bg-white dark:bg-gray-900" id="grading">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-20">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($section['section_subtitle']); ?></p>
                    </div>

                    <div class="max-w-5xl mx-auto overflow-hidden rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-blue-600 text-white">
                                    <th class="p-8 text-3xl text-white font-black uppercase tracking-wider">WASSCE</th>
                                    <th class="p-8 text-3xl text-white font-black uppercase tracking-wider">SSSCE</th>
                                    <th class="p-8 text-3xl text-white font-black uppercase tracking-wider">GBCE</th>
                                    <th class="p-8 text-3xl text-white font-black uppercase tracking-wider">Interpretation</th>
                                </tr>
                            </thead>
                            <tbody class="text-2xl font-bold bg-white dark:bg-gray-900">
                                <?php if (!empty($section_items)): ?>
                                    <?php foreach ($section_items as $index => $item): ?>
                                    <tr class="<?php echo $index % 2 == 0 ? 'bg-gray-50 dark:bg-gray-800/50' : ''; ?> border-b border-gray-100 dark:border-gray-700">
                                        <td class="p-8 text-blue-600 dark:text-blue-400"><?php echo strip_tags($item['item_title']); ?></td>
                                        <td class="p-8 dark:text-white"><?php echo strip_tags($item['item_subtitle']); ?></td>
                                        <td class="p-8 dark:text-white"><?php echo strip_tags($item['item_stat_value']); ?></td>
                                        <td class="p-8 text-gray-600 dark:text-gray-400"><?php echo $item['item_description']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="p-10 text-center text-gray-400 italic">No grading data found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        <?php elseif ($section_key === 'important'): ?>
            <!-- Important Information Section -->
            <section class="py-24 bg-gray-50 dark:bg-gray-950" id="important">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-16">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($section['section_subtitle']); ?></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <?php foreach ($section_items as $item): ?>
                        <div class="glass p-10 rounded-3xl border-l-8 border-<?php echo str_replace('bg-', '', $item['item_color'] ?: 'blue-600'); ?>">
                            <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-4">
                                <span class="material-symbols-outlined text-<?php echo str_replace('bg-', '', $item['item_color'] ?: 'blue-600'); ?>"><?php echo strip_tags($item['item_icon'] ?: 'info'); ?></span>
                                <?php echo strip_tags($item['item_title']); ?>
                            </h4>
                            <div class="text-2xl text-gray-700 dark:text-gray-300 leading-relaxed">
                                <?php echo $item['item_description']; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($section_key === 'support'): ?>
            <!-- Support Services Section -->
            <section class="py-24 bg-white dark:bg-gray-900" id="support">
                <div class="container">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div>
                            <div class="inline-flex items-center gap-3 px-6 py-2 mb-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-black text-3xl uppercase tracking-wider">
                                <?php echo strip_tags($section['section_subtitle'] ?: 'Student Success'); ?>
                            </div>
                            <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($section['section_title']); ?></h2>
                            <div class="space-y-8">
                                <?php foreach ($section_items as $item): ?>
                                <div class="flex gap-6">
                                    <div class="w-14 h-14 rounded-xl bg-<?php echo $item['item_color'] ?: 'blue-600'; ?> flex items-center justify-center text-white shadow-lg shrink-0">
                                        <span class="material-symbols-outlined text-2xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'support'); ?></span>
                                    </div>
                                    <div>
                                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-2"><?php echo strip_tags($item['item_title']); ?></h4>
                                        <div class="text-2xl text-gray-600 dark:text-gray-400"><?php echo $item['item_description']; ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="absolute -inset-4 bg-blue-500 rounded-[3rem] blur-2xl opacity-20"></div>
                            <img src="<?php echo strip_tags($section['section_image'] ?: 'images/happy_students.png'); ?>" 
                                 alt="Support Services" class="relative rounded-[2.5rem] shadow-2xl w-full h-[500px] object-cover">
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ($section_key === 'resources'): ?>
            <!-- Quick Resources Section -->
            <section class="py-24 bg-gray-50 dark:bg-gray-950" id="resources">
                <div class="container">
                    <div class="max-w-4xl mx-auto text-center mb-16">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($section['section_title']); ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($section['section_subtitle']); ?></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        <?php foreach ($section_items as $item): ?>
                        <a href="<?php echo strip_tags($item['item_link'] ?: '#'); ?>" class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-sm hover:shadow-2xl transition-all duration-500 text-center border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                            <div class="w-20 h-20 mx-auto rounded-2xl bg-<?php echo $item['item_color'] ?: 'blue-600'; ?> flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                                <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'download'); ?></span>
                            </div>
                            <h5 class="text-3xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($item['item_title']); ?></h5>
                            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400 font-medium"><?php echo strip_tags($item['item_subtitle'] ?: 'Access resource'); ?></p>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- CTA Section -->
    <?php if (!empty($page_data['cta_title'])): ?>
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo $page_data['cta_title']; ?>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?: 'apply.php'); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-3xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-4xl">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?: 'Apply Now'); ?>
                    </a>
                </div>
                
                <!-- Stats row if exists -->
                <?php
                $stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = ? ORDER BY display_order");
                $stmt->execute([$page_key]);
                $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if (!empty($stats)): ?>
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <?php foreach ($stats as $stat): ?>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2"><?php echo strip_tags($stat['stat_value']); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags($stat['stat_label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>