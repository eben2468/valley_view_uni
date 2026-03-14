<?php
$page_title = "Mature Entrance Examination - Valley View University";
$active_page = "admissions";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'mature_entrance'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'mature_entrance' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_raw as $s) {
    $sections[$s['section_key']] = $s;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'mature_entrance' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($items_raw as $i) {
    $items[$i['section_key']][] = $i;
}
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
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(251, 191, 36, 0.3); }
        50% { box-shadow: 0 0 40px rgba(251, 191, 36, 0.5); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
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
    .program-card {
        transition: all 0.3s ease;
    }
    .program-card:hover {
        transform: translateY(-10px);
    }
    .session-card {
        transition: all 0.3s ease;
    }
    .session-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); ?>" 
                 alt="Mature Students" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <?php if ($page_data['hero_badge']): ?>
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge']); ?></span>
                </div>
                <?php endif; ?>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $page_data['hero_title']; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo $page_data['hero_subtitle']; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description']); ?>"
                </p>

                <div class="mt-12 flex flex-col sm:flex-row gap-6 justify-center animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="uploads/Download Forms/mature-admissions-form.pdf" download class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4 animate-pulse-glow">
                        <span class="material-symbols-outlined text-3xl">download</span>
                        Download Form
                    </a>
                    <a href="#programs" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        View Programs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <?php if (isset($sections['intro'])): ?>
    <section class="py-28 bg-white dark:bg-gray-800 relative z-20 -mt-10 mx-auto max-w-7xl rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <!-- Left Column -->
                <div class="p-12">
                    <div class="flex items-center gap-8 mb-12">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white">diversity_3</span>
                        </div>
                        <div>
                            <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($sections['intro']['section_title']); ?></h2>
                        </div>
                    </div>
                    <div class="text-3xl text-gray-600 dark:text-gray-400 mb-10 leading-relaxed">
                        <?php echo $sections['intro']['section_description']; ?>
                    </div>
                    <div class="flex flex-wrap gap-5">
                        <div class="px-8 py-4 bg-blue-100 dark:bg-blue-900/30 rounded-2xl">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">Age: 25+</span>
                        </div>
                        <div class="px-8 py-4 bg-green-100 dark:bg-green-900/30 rounded-2xl">
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">No WASSCE Required</span>
                        </div>
                        <div class="px-8 py-4 bg-yellow-100 dark:bg-yellow-900/30 rounded-2xl">
                            <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">Regular Intakes</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Why Choose (Side Version) -->
                <?php if (isset($sections['why_choose'])): ?>
                <div class="p-12 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 rounded-[2.5rem]">
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-10"><?php echo strip_tags($sections['why_choose']['section_title']); ?></h3>
                    <ul class="space-y-8">
                        <?php if (isset($items['why_choose'])): ?>
                        <?php foreach ($items['why_choose'] as $item): ?>
                        <li class="flex items-start gap-6">
                            <span class="w-16 h-16 rounded-2xl bg-<?php echo str_replace('blue-600', 'blue', $item['item_color']); ?>-600 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                            </span>
                            <div>
                                <h4 class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags($item['item_title']); ?></h4>
                                <p class="text-2xl text-gray-600 dark:text-gray-400 mt-2"><?php echo strip_tags($item['item_description']); ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Available Programs Section -->
    <?php if (isset($sections['programs'])): ?>
    <section id="programs" class="py-28 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-[140rem] mx-auto px-6">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($sections['programs']['section_title']); ?></h2>
                    <div class="h-2 w-48 bg-blue-600 mx-auto rounded-full mb-10"></div>
                    <p class="text-3xl sm:text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-5xl mx-auto"><?php echo strip_tags($sections['programs']['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                    <?php if (isset($items['programs'])): ?>
                    <?php foreach ($items['programs'] as $item): ?>
                    <div class="program-card bg-white dark:bg-gray-900 rounded-[2.5rem] p-10 shadow-xl border border-gray-100 dark:border-gray-800">
                        <div class="w-20 h-20 rounded-2xl bg-<?php echo str_replace('-600', '', $item['item_color']); ?>-600 flex items-center justify-center mb-8">
                            <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-5"><?php echo strip_tags($item['item_title']); ?></h3>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 mb-8 leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                        <?php if ($item['item_subtitle']): ?>
                        <span class="inline-block px-5 py-3 bg-<?php echo str_replace('-600', '', $item['item_color']); ?>-100 dark:bg-<?php echo str_replace('-600', '', $item['item_color']); ?>-900/30 text-<?php echo str_replace('-600', '', $item['item_color']); ?>-600 dark:text-<?php echo str_replace('-600', '', $item['item_color']); ?>-400 rounded-full text-xl font-bold"><?php echo strip_tags($item['item_subtitle']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Enrollment Sessions Section -->
    <section class="py-28 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8">Enrollment Sessions</h2>
                    <p class="text-3xl text-blue-100 font-medium leading-relaxed max-w-5xl mx-auto">Choose a session that fits your schedule. We offer flexible class options throughout the year.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- Session 1 & 2 -->
                    <?php if (isset($sections['sessions_1_2'])): ?>
                    <div class="session-card bg-white/10 backdrop-blur-md rounded-[3rem] p-12 border border-white/10">
                        <div class="flex items-center gap-5 mb-10">
                            <div class="w-20 h-20 rounded-2xl bg-yellow-400 flex items-center justify-center">
                                <span class="text-blue-900 text-4xl font-black">1-2</span>
                            </div>
                            <h3 class="text-4xl font-black text-white"><?php echo strip_tags($sections['sessions_1_2']['section_title']); ?></h3>
                        </div>
                        <div class="space-y-6">
                            <?php if (isset($items['sessions_1_2'])): ?>
                            <?php foreach ($items['sessions_1_2'] as $item): ?>
                            <div class="flex items-start gap-5">
                                <span class="material-symbols-outlined text-white text-3xl"><?php echo strip_tags($item['item_icon']); ?></span>
                                <div>
                                    <p class="text-2xl text-white font-bold"><?php echo strip_tags($item['item_title']); ?></p>
                                    <div class="text-xl text-blue-200 mt-1"><?php echo $item['item_description']; ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Session 3 & 4 -->
                    <?php if (isset($sections['sessions_3_4'])): ?>
                    <div class="session-card bg-white/10 backdrop-blur-md rounded-[3rem] p-12 border border-white/10">
                        <div class="flex items-center gap-5 mb-10">
                            <div class="w-20 h-20 rounded-2xl bg-yellow-400 flex items-center justify-center">
                                <span class="text-blue-900 text-4xl font-black">3-4</span>
                            </div>
                            <h3 class="text-4xl font-black text-white"><?php echo strip_tags($sections['sessions_3_4']['section_title']); ?></h3>
                        </div>
                        <div class="space-y-6">
                            <?php if (isset($items['sessions_3_4'])): ?>
                            <?php foreach ($items['sessions_3_4'] as $item): ?>
                            <div class="flex items-start gap-5">
                                <span class="material-symbols-outlined text-white text-3xl"><?php echo strip_tags($item['item_icon']); ?></span>
                                <div>
                                    <p class="text-2xl text-white font-bold"><?php echo strip_tags($item['item_title']); ?></p>
                                    <div class="text-xl text-blue-200 mt-1"><?php echo $item['item_description']; ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Apply Section -->
    <?php if (isset($sections['how_to_apply'])): ?>
    <section class="py-28 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($sections['how_to_apply']['section_title']); ?></h2>
                    <div class="h-2 w-48 bg-blue-600 mx-auto rounded-full mb-10"></div>
                    <p class="text-3xl sm:text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-5xl mx-auto"><?php echo strip_tags($sections['how_to_apply']['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <?php if (isset($items['how_to_apply'])): ?>
                    <?php foreach ($items['how_to_apply'] as $index => $item): ?>
                    <div class="relative p-12 bg-gray-50 dark:bg-gray-800 rounded-[3rem] text-center group hover:shadow-2xl transition-all">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo str_replace('-600', '', $item['item_color']); ?>-600 flex items-center justify-center text-white text-5xl font-black mx-auto mb-10 group-hover:scale-110 transition-transform"><?php echo $index + 1; ?></div>
                        <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-5"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Download Form Section -->
    <?php if (isset($sections['form_download'])): ?>
    <section class="py-28 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-950 dark:to-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-900 rounded-[3.5rem] shadow-2xl overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                        <div class="p-20 flex flex-col justify-center">
                            <div class="w-28 h-28 rounded-3xl bg-red-600 flex items-center justify-center mb-12 shadow-xl">
                                <span class="material-symbols-outlined text-6xl text-white">description</span>
                            </div>
                            <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($sections['form_download']['section_title']); ?></h2>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 mb-12 leading-relaxed">
                                <?php echo strip_tags($sections['form_download']['section_description']); ?>
                            </p>
                            <a href="uploads/Download Forms/mature-admissions-form.pdf" download class="inline-flex items-center gap-5 px-14 py-7 bg-blue-600 text-white text-2xl font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-xl hover:scale-105 transform w-fit">
                                <span class="material-symbols-outlined text-4xl text-white">download</span>
                                Download Form (PDF)
                            </a>
                        </div>
                        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-20 flex flex-col justify-center">
                            <h3 class="text-4xl font-black text-white mb-10">Form Available at:</h3>
                            <ul class="space-y-8">
                                <?php if (isset($items['form_locations'])): ?>
                                <?php foreach ($items['form_locations'] as $item): ?>
                                <li class="flex items-center gap-5 text-2xl text-white">
                                    <span class="material-symbols-outlined text-white text-4xl"><?php echo strip_tags($item['item_icon']); ?></span>
                                    <?php echo strip_tags($item['item_title']); ?>
                                </li>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Contact Section -->
    <?php if (isset($sections['contact'])): ?>
    <section class="py-28 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($sections['contact']['section_title']); ?></h2>
                    <p class="text-3xl sm:text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-5xl mx-auto"><?php echo strip_tags($sections['contact']['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                    <?php if (isset($items['contact_info'])): ?>
                    <?php foreach ($items['contact_info'] as $item): ?>
                    <div class="p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] text-center hover:shadow-xl transition-all">
                        <div class="w-20 h-20 rounded-2xl bg-<?php echo str_replace('-600', '', $item['item_color']); ?>-600 flex items-center justify-center mx-auto mb-8">
                            <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-3"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mt-20 p-12 bg-blue-50 dark:bg-blue-900/20 rounded-[3rem] flex flex-col md:flex-row items-center justify-between gap-10">
                    <div class="flex items-center gap-8">
                        <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-white">location_on</span>
                        </div>
                        <div>
                            <h4 class="text-3xl font-bold text-gray-900 dark:text-white">Campus Address</h4>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 mt-2">Mile 19 Off the Adenta-Dodowa Road, P.O. Box AF 595 Adentan</p>
                        </div>
                    </div>
                    <a href="contact_us.php" class="px-12 py-6 bg-blue-600 text-white text-2xl font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg flex items-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-white">email</span>
                        Contact Us
                    </a>
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
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo $page_data['cta_title']; ?> <br><span class="text-lg sm:text-xl md:text-2xl lg:text-4xl text-yellow-400 block mt-2 font-medium"><?php echo $page_data['cta_subtitle']; ?></span>
                </h2>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="uploads/Download Forms/mature-admissions-form.pdf" download class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">download</span>
                        Download Form
                    </a>
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">edit_square</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Online'); ?>
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">15+</div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black">Programs Available</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">4</div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black">Sessions Per Year</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">25+</div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black">Age Requirement</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
