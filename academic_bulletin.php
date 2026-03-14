<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

$page_slug = 'academic_bulletin';
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
$downloads = $adminContent->getSectionFields($page_id, 'downloads');

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
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    
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
    
    .bulletin-card { transition: all 0.4s ease; }
    .bulletin-card:hover { 
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
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe'); ?>" 
                 alt="Academic Bulletin" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-7xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['badge_text'] ?? 'Academic Resources'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['title_1'] ?? 'Academic'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['title_2'] ?? 'Bulletin'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['description'] ?? '"Your definitive guide to academic excellence, institutional policies, and program requirements at Valley View University."'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Downloads Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6">
                        <?php echo strip_tags($downloads['title_1'] ?? 'Download'); ?> <span class="text-blue-600"><?php echo strip_tags($downloads['title_2'] ?? 'Bulletins'); ?></span>
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium max-w-4xl mx-auto">
                        <?php echo strip_tags($downloads['subtitle'] ?? 'Access the latest versions of our academic bulletins.'); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <?php for($i=1; $i<=4; $i++): 
                        $title = $downloads["item{$i}_title"] ?? '';
                        $link = $downloads["item{$i}_link"] ?? '#';
                        if(empty($title)) continue;
                    ?>
                    <div class="glass p-10 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-800 flex flex-col justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($title); ?></h3>
                        </div>
                        <a href="<?php echo strip_tags($link); ?>" download class="download-btn inline-flex items-center justify-center gap-4 px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white text-xl font-black rounded-2xl shadow-xl">
                            <span class="material-symbols-outlined text-3xl">picture_as_pdf</span>
                            <?php echo strip_tags($downloads['button_text'] ?? 'Download PDF'); ?>
                        </a>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
