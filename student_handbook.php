<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

$page_slug = 'student_handbook';
$adminContent = new AdministrationContent($pdo);
$page_data = $adminContent->getPageBySlug($page_slug);

if (!$page_data) {
    // Fallback if not in administration_pages yet
    include '404.php';
    exit;
}

$page_id = $page_data['id'];
$page_title = $page_data['page_name'] . " - Valley View University";
$active_page = "resources";

// Fetch sections
$hero = $adminContent->getSectionFields($page_id, 'hero');
$intro = $adminContent->getSectionFields($page_id, 'introduction');
$features = $adminContent->getSectionFields($page_id, 'features');
$download = $adminContent->getSectionFields($page_id, 'download');
$assistance = $adminContent->getSectionFields($page_id, 'assistance');
$faq = $adminContent->getSectionFields($page_id, 'faq');
$cta = $adminContent->getSectionFields($page_id, 'cta');

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
    
    .handbook-card { transition: all 0.4s ease; }
    .handbook-card:hover { 
        transform: translateY(-15px);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
    }
    
    .download-btn { transition: all 0.3s ease; }
    .download-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.6);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f'); ?>" 
                 alt="Student Handbook" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-7xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400">Student Resources</span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title_1'] ?? 'Student'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4">
                        <?php echo strip_tags($hero['hero_title_2'] ?? 'Handbook'); ?>
                    </span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? '"Your Comprehensive Guide to Success and Excellence at Valley View University"'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-blue-600 shadow-lg">
                        <span class="material-symbols-outlined text-3xl text-white">menu_book</span>
                        <span class="text-xl font-black uppercase tracking-wider text-white"><?php echo strip_tags($intro['badge'] ?? 'Essential Guide'); ?></span>
                    </div>
                    <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-8">
                        <?php echo strip_tags($intro['title_part1'] ?? 'Navigating Your'); ?> <span class="text-blue-600 text-4xl sm:text-5xl md:text-6xl font-semibold"><?php echo strip_tags($intro['title_part2'] ?? 'University Journey'); ?></span>
                    </h2>
                    <div class="h-2 w-48 bg-blue-600 mx-auto rounded-full mb-8"></div>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                        <?php echo nl2br(strip_tags($intro['description'] ?? '')); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <!-- Feature 1 -->
                    <div class="handbook-card glass p-12 rounded-[3rem] shadow-xl border-t-[12px] border-blue-600">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card1_icon'] ?? 'school'); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($features['card1_title'] ?? 'Academic Policies'); ?></h3>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($features['card1_desc'] ?? ''); ?>
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="handbook-card glass p-12 rounded-[3rem] shadow-xl border-t-[12px] border-green-600">
                        <div class="w-24 h-24 rounded-3xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card2_icon'] ?? 'gavel'); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($features['card2_title'] ?? 'Student Conduct'); ?></h3>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($features['card2_desc'] ?? ''); ?>
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="handbook-card glass p-12 rounded-[3rem] shadow-xl border-t-[12px] border-purple-600">
                        <div class="w-24 h-24 rounded-3xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card3_icon'] ?? 'diversity_3'); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($features['card3_title'] ?? 'Campus Life'); ?></h3>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($features['card3_desc'] ?? ''); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px] -mr-64 -mt-64"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] -ml-64 -mb-64"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-900 rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="p-12 md:p-16">
                            <div class="inline-flex items-center gap-3 px-6 py-2 mb-8 rounded-full bg-blue-600 shadow-md">
                                <span class="material-symbols-outlined text-2xl text-white">download</span>
                                <span class="text-lg font-black uppercase tracking-wider text-white"><?php echo strip_tags($download['badge'] ?? 'Digital Copy'); ?></span>
                            </div>
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-8 leading-tight">
                                <?php echo strip_tags($download['title_part1'] ?? 'Get Your Copy of the'); ?> <span class="text-blue-600 text-4xl sm:text-5xl md:text-6xl font-semibold"><?php echo strip_tags($download['title_part2'] ?? 'Handbook'); ?></span>
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-400 font-medium mb-12 leading-relaxed">
                                <?php echo strip_tags($download['description'] ?? ''); ?>
                            </p>
                            
                            <div class="flex flex-col sm:flex-row gap-6">
                                <a href="<?php echo strip_tags($download['pdf_link'] ?? '#'); ?>" download class="download-btn inline-flex items-center justify-center gap-4 px-12 py-6 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-black rounded-2xl shadow-xl">
                                    <span class="material-symbols-outlined text-3xl text-white">picture_as_pdf</span>
                                    Download PDF
                                </a>
                                <div class="flex items-center gap-4 px-8 py-6 bg-blue-600 text-white rounded-2xl shadow-md">
                                    <span class="material-symbols-outlined text-white text-3xl">info</span>
                                    <div>
                                        <p class="text-sm font-bold text-blue-100 uppercase tracking-widest">File Size</p>
                                        <p class="text-xl font-black text-white"><?php echo strip_tags($download['file_size'] ?? ''); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center p-16 bg-gray-50 dark:bg-gray-800/50">
                            <div class="relative">
                                <img src="<?php echo strip_tags($download['cover_image'] ?? 'uploads/student_handbook_cover.png'); ?>" 
                                     alt="VVU Student Handbook Cover" 
                                     class="w-full max-w-[500px] h-auto rounded-[3rem] shadow-2xl border-[12px] border-white dark:border-gray-700 transform hover:scale-105 transition-transform duration-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links / Contact -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($assistance['title'] ?? 'Need Further Assistance?'); ?></h2>
                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium mb-10 leading-relaxed">
                            <?php echo strip_tags($assistance['description'] ?? ''); ?>
                        </p>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-white text-2xl">location_on</span>
                                </div>
                                <span class="text-xl text-gray-700 dark:text-gray-300 font-bold"><?php echo strip_tags($assistance['address'] ?? ''); ?></span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-white text-2xl">call</span>
                                </div>
                                <span class="text-xl text-blue-600 dark:text-blue-400 font-black"><?php echo strip_tags($assistance['phone'] ?? ''); ?></span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-white text-2xl">mail</span>
                                </div>
                                <span class="text-xl text-gray-700 dark:text-gray-300 font-bold"><?php echo strip_tags($assistance['email'] ?? ''); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="glass p-12 rounded-[3rem] shadow-xl text-center">
                        <div class="w-32 h-32 mx-auto rounded-3xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white shadow-lg mb-10 animate-float">
                            <span class="material-symbols-outlined text-6xl text-white">help</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($faq['title'] ?? 'Frequently Asked Questions'); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium mb-10">
                            <?php echo strip_tags($faq['description'] ?? ''); ?>
                        </p>
                        <a href="<?php echo strip_tags($faq['btn_link'] ?? 'faqs.php'); ?>" class="inline-flex items-center gap-4 px-10 py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xl font-black rounded-2xl transition-all hover:scale-105 shadow-lg">
                            <?php echo strip_tags($faq['btn_text'] ?? 'View FAQs'); ?>
                            <span class="material-symbols-outlined text-white dark:text-gray-900 text-2xl">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-10 leading-tight">
                    <?php echo strip_tags($cta['title_part1'] ?? 'Your Success'); ?> <br><span class="text-5xl sm:text-4xl md:text-7xl lg:text-7xl text-yellow-400 font-semibold"><?php echo strip_tags($cta['title_part2'] ?? 'Starts Here'); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl text-blue-100 mb-16 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($cta['description'] ?? ''); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-8 justify-center">
                    <a href="<?php echo strip_tags($download['pdf_link'] ?? '#'); ?>" download class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-blue-900">download</span>
                        Download Handbook
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-white">support_agent</span>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
