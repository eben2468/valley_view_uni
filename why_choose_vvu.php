<?php
$page_title = "Why Choose Valley View University";
$active_page = "admissions";
require_once 'includes/db_connect.php';
include 'includes/header.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'why_choose_vvu'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'why_choose_vvu' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_raw as $s) {
    $sections[$s['section_key']] = $s;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'why_choose_vvu' AND is_active = 1 ORDER BY display_order");
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
    
    .feature-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .feature-card:hover { 
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
    }
    
    .text-gradient {
        background: linear-gradient(to right, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-spacing {
        padding-top: 10rem;
        padding-bottom: 10rem;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'Education-Website-and-AdminPanel/images/pro-bg.jpg'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
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

                <div class="mt-12 flex flex-wrap justify-center gap-6 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="apply.php" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-black rounded-xl transition-all transform hover:scale-105 shadow-2xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-blue-900">how_to_reg</span>
                        Apply Now
                    </a>
                    <a href="#discover" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-lg font-black rounded-xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-2xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">explore</span>
                        Explore More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <?php if (isset($sections['intro'])): ?>
    <section id="discover" class="section-spacing bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                    <div class="animate-fadeInUp">
                        <div class="inline-flex items-center gap-5 px-10 py-4 mb-10 rounded-full bg-blue-600/10 text-blue-600 dark:text-blue-400 font-black uppercase tracking-widest text-xl">
                            <span class="material-symbols-outlined text-3xl">school</span>
                            <?php echo strip_tags($sections['intro']['section_title']); ?>
                        </div>
                        <?php if ($sections['intro']['section_subtitle']): ?>
                        <div class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-10 leading-tight">
                            <?php echo $sections['intro']['section_subtitle']; ?>
                        </div>
                        <?php endif; ?>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-12">
                            <?php echo strip_tags($sections['intro']['section_description']); ?>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <?php if (isset($items['intro'])): ?>
                            <?php foreach ($items['intro'] as $item): ?>
                            <div class="flex items-start gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center shrink-0 shadow-lg">
                                    <span class="material-symbols-outlined text-white text-3xl"><?php echo strip_tags($item['item_icon']); ?></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="relative animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="absolute -inset-6 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-[4rem] blur-3xl opacity-20"></div>
                        <img src="<?php echo strip_tags($sections['intro']['section_image'] ?? 'Education-Website-and-AdminPanel/images/h-about.jpg'); ?>" 
                             alt="Students Collaborating" class="relative rounded-[3rem] shadow-2xl w-full h-[600px] object-cover">
                        <div class="absolute -bottom-12 -left-12 glass p-10 rounded-[2.5rem] shadow-2xl max-w-sm animate-float">
                            <div class="text-5xl font-black text-blue-600 mb-2">97%</div>
                            <p class="text-xl font-bold text-gray-700 dark:text-gray-300">Nursing Licensure Exam Pass Rate (2015)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Achievements Grid -->
    <?php if (isset($sections['achievements'])): ?>
    <section class="section-spacing bg-gray-50 dark:bg-gray-950 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-blue-600/5 rounded-full blur-[150px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[800px] h-[800px] bg-yellow-400/5 rounded-full blur-[150px] -ml-96 -mb-96"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-6xl md:text-7xl lg:text-8xl font-black text-gray-900 dark:text-white mb-8">
                        <?php echo $sections['achievements']['section_title']; ?>
                    </h2>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-4xl mx-auto">
                        <?php echo strip_tags($sections['achievements']['section_subtitle']); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <?php if (isset($items['achievements'])): ?>
                    <?php foreach ($items['achievements'] as $item): ?>
                    <div class="feature-card glass p-12 rounded-[3rem] shadow-xl border-t-[12px] border-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?>">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($item['item_description']); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Eco-Friendly Section -->
    <?php if (isset($sections['eco_friendly'])): ?>
    <section class="section-spacing bg-white dark:bg-gray-900 overflow-hidden">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="bg-blue-900 rounded-[4rem] p-16 lg:p-24 relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                        <div>
                            <div class="inline-flex items-center gap-4 px-8 py-3 mb-10 rounded-full bg-green-500/20 text-green-400 font-black uppercase tracking-widest text-xl">
                                <span class="material-symbols-outlined">eco</span>
                                <?php echo strip_tags($sections['eco_friendly']['section_subtitle']); ?>
                            </div>
                            <h2 class="text-6xl md:text-7xl font-black text-white mb-10 leading-tight">
                                <?php echo $sections['eco_friendly']['section_title']; ?>
                            </h2>
                            <p class="text-3xl text-blue-100 font-medium leading-relaxed mb-12">
                                <?php echo strip_tags($sections['eco_friendly']['section_description']); ?>
                            </p>
                            <ul class="space-y-8">
                                <?php if (isset($items['eco_friendly'])): ?>
                                <?php foreach ($items['eco_friendly'] as $item): ?>
                                <li class="flex items-center gap-6">
                                    <div class="w-14 h-14 rounded-xl bg-green-500/20 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-white text-3xl"><?php echo strip_tags($item['item_icon']); ?></span>
                                    </div>
                                    <span class="text-2xl text-white font-bold"><?php echo strip_tags($item['item_title']); ?></span>
                                </li>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="relative">
                            <img src="<?php echo strip_tags($sections['eco_friendly']['section_image'] ?? 'Education-Website-and-AdminPanel/images/h-cam1.jpg'); ?>" 
                                 alt="Eco Campus" class="rounded-[3rem] shadow-2xl w-full h-[500px] object-cover">
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-yellow-400 rounded-full flex items-center justify-center shadow-2xl animate-float">
                                <span class="material-symbols-outlined text-white text-6xl">energy_savings_leaf</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Facilities & Services -->
    <?php if (isset($sections['facilities'])): ?>
    <section class="section-spacing bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8">
                        <?php echo $sections['facilities']['section_title']; ?>
                    </h2>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-4xl mx-auto">
                        <?php echo strip_tags($sections['facilities']['section_subtitle']); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                    <?php if (isset($items['facilities'])): ?>
                    <?php foreach ($items['facilities'] as $item): ?>
                    <div class="p-10 bg-white dark:bg-gray-900 rounded-[3rem] shadow-sm hover:shadow-xl transition-all border border-gray-100 dark:border-gray-800 text-center">
                        <div class="w-20 h-20 rounded-2xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center mx-auto mb-8">
                            <span class="material-symbols-outlined text-white text-4xl"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Student Life Section -->
    <?php if (isset($sections['student_life'])): ?>
    <section class="section-spacing bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                    <div class="order-2 lg:order-1">
                        <div class="grid grid-cols-2 gap-6">
                            <img src="<?php echo strip_tags($sections['student_life']['section_image'] ?? 'Education-Website-and-AdminPanel/images/h-cam.jpg'); ?>" alt="Campus Life" class="rounded-3xl shadow-xl h-80 w-full object-cover">
                            <img src="<?php echo strip_tags($sections['student_life']['section_image_2'] ?? 'Education-Website-and-AdminPanel/images/h-about1.jpg'); ?>" alt="Students" class="rounded-3xl shadow-xl h-80 w-full object-cover mt-12">
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <h2 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-10 leading-tight">
                            <?php echo $sections['student_life']['section_title']; ?>
                        </h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-12">
                            <?php echo strip_tags($sections['student_life']['section_description']); ?>
                        </p>
                        <div class="space-y-6">
                            <?php if (isset($items['student_life'])): ?>
                            <?php foreach ($items['student_life'] as $item): ?>
                            <div class="flex items-center gap-6 p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl">
                                <div class="w-14 h-14 rounded-xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-white text-3xl"><?php echo strip_tags($item['item_icon']); ?></span>
                                </div>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300"><?php echo strip_tags($item['item_title']); ?></span>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
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
                <p class="text-lg sm:text-xl md:text-2xl text-white mb-20 max-w-5xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($page_data['hero_description'] ?? 'Join the thousands of successful graduates who chose Valley View University for their future.'); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-8 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-blue-900">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-white">support_agent</span>
                        Talk to an Advisor
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