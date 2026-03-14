<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

$page_slug = 'journals';
$adminContent = new AdministrationContent($pdo);
$page_data = $adminContent->getPageBySlug($page_slug);

if (!$page_data) {
    include '404.php';
    exit;
}

$page_id = $page_data['id'];
$page_title = $page_data['page_name'] . " - Valley View University";
$active_page = "academics";

// Fetch sections
$hero = $adminContent->getSectionFields($page_id, 'hero');
$intro = $adminContent->getSectionFields($page_id, 'introduction');
$features = $adminContent->getSectionFields($page_id, 'features');
$download = $adminContent->getSectionFields($page_id, 'download');

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
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.3); }
        50% { box-shadow: 0 0 40px rgba(37, 99, 235, 0.6); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    
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
    
    .journal-card { transition: all 0.4s ease; }
    .journal-card:hover { 
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
    }
    
    .feature-card { transition: all 0.3s ease; }
    .feature-card:hover { transform: translateY(-5px); }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570'); ?>" 
                 alt="Academic Journals" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['badge'] ?? 'Research & Publications'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['title_1'] ?? 'Academic'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['title_2'] ?? 'Journals'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo nl2br(strip_tags($hero['description'] ?? '')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-28 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-20">
                    <div class="inline-flex items-center gap-4 px-6 py-2.5 mb-8 rounded-2xl bg-gradient-to-r from-blue-700 to-blue-500 shadow-xl text-white mx-auto">
                        <span class="material-symbols-outlined text-2xl text-white">auto_stories</span>
                        <span class="text-base font-black uppercase tracking-[0.2em] text-white"><?php echo strip_tags($intro['badge'] ?? 'Our Publications'); ?></span>
                    </div>
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-10">
                        <?php echo strip_tags($intro['title_part1'] ?? 'Journal of'); ?> <?php echo strip_tags($intro['title_part2'] ?? 'Multidisciplinary Studies'); ?>
                    </h2>
                    <div class="h-3 w-56 bg-blue-600 mx-auto rounded-full mb-10"></div>
                    <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo nl2br(strip_tags($intro['description'] ?? '')); ?>
                    </p>
                </div>

                <!-- Journal Features -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
                    <div class="feature-card text-center p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl">
                        <div class="w-24 h-24 mx-auto rounded-3xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card1_icon'] ?? 'verified'); ?></span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($features['card1_title'] ?? 'Peer-Reviewed'); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($features['card1_desc'] ?? ''); ?></p>
                    </div>
                    <div class="feature-card text-center p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl">
                        <div class="w-24 h-24 mx-auto rounded-3xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card2_icon'] ?? 'public'); ?></span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($features['card2_title'] ?? 'Open Access'); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($features['card2_desc'] ?? ''); ?></p>
                    </div>
                    <div class="feature-card text-center p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl">
                        <div class="w-24 h-24 mx-auto rounded-3xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card3_icon'] ?? 'diversity_3'); ?></span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($features['card3_title'] ?? 'Multidisciplinary'); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($features['card3_desc'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Journal Download Section -->
    <section class="py-28 bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Journal Preview -->
                    <div class="journal-card relative">
                        <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden animate-pulse-glow">
                            <div class="bg-gradient-to-br from-blue-600 to-purple-700 p-10 text-center">
                                <span class="material-symbols-outlined text-white mb-6" style="font-size: 6rem;">menu_book</span>
                                <h3 class="text-3xl font-black text-white mb-2"><?php echo strip_tags($download['journal_title'] ?? 'Journal of Multidisciplinary Studies'); ?></h3>
                                <p class="text-xl text-blue-200 font-bold"><?php echo strip_tags($download['volume_info'] ?? ''); ?></p>
                            </div>
                            <div class="p-8 bg-white">
                                <div class="flex items-center gap-4 mb-6">
                                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-lg font-bold">PDF Format</span>
                                    <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-lg font-bold">Free Download</span>
                                </div>
                                <div class="flex items-center gap-3 text-gray-600">
                                    <span class="material-symbols-outlined text-2xl">calendar_month</span>
                                    <span class="text-lg font-medium">Published: <?php echo strip_tags($download['publish_year'] ?? ''); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-6 -right-6 w-20 h-20 bg-yellow-400 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                            <span class="material-symbols-outlined text-blue-900 text-4xl">star</span>
                        </div>
                    </div>

                    <!-- Download Info -->
                    <div class="text-center lg:text-left">
                        <span class="inline-block px-6 py-2 bg-yellow-400 text-blue-900 text-lg font-bold rounded-full mb-8">Featured Publication</span>
                        <h2 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white mb-8 leading-tight">
                            Download Our Latest Journal
                        </h2>
                        <a href="<?php echo strip_tags($download['pdf_link'] ?? '#'); ?>" download class="inline-flex items-center justify-center gap-4 px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-black rounded-2xl shadow-xl transition-all transform hover:scale-105">
                            <span class="material-symbols-outlined text-4xl">download</span>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
