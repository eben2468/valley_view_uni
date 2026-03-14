<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

$page_slug = 'sandwich_calendar';
$adminContent = new AdministrationContent($pdo);
$page_data = $adminContent->getPageBySlug($page_slug);

if (!$page_data) {
    include '404.php';
    exit;
}

$page_id = $page_id = $page_data['id'];
$page_title = $page_data['page_name'] . " - Valley View University";
$active_page = "academics";

// Fetch sections
$hero = $adminContent->getSectionFields($page_id, 'hero');
$intro = $adminContent->getSectionFields($page_id, 'introduction');
$features = $adminContent->getSectionFields($page_id, 'features');
$document = $adminContent->getSectionFields($page_id, 'document');
$support = $adminContent->getSectionFields($page_id, 'support');
$assistance = $adminContent->getSectionFields($page_id, 'assistance');
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
    
    .calendar-card { transition: all 0.4s ease; }
    .calendar-card:hover { 
        transform: translateY(-10px);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.2);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[50vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['image'] ?? 'https://images.unsplash.com/photo-1506784365847-bbad939e9335'); ?>" 
                 alt="Sandwich Calendar" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-emerald-900/80 via-emerald-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-7xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['badge'] ?? 'Sandwich Program'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['title_part1'] ?? 'Sandwich Academic'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-[6.5rem] font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-6"><?php echo strip_tags($hero['title_part2'] ?? 'Calendar'); ?></span>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-5xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['description'] ?? ''); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-10">
                        <?php echo strip_tags($intro['title'] ?? ''); ?>
                    </h2>
                    <div class="h-3 w-64 bg-emerald-600 mx-auto rounded-full mb-10"></div>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-6xl mx-auto">
                        <?php echo nl2br(strip_tags($intro['description'] ?? '')); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="calendar-card glass p-12 rounded-[3rem] shadow-xl border-t-[12px] border-emerald-600">
                        <div class="w-24 h-24 rounded-3xl bg-emerald-600 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card1_icon'] ?? 'app_registration'); ?></span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($features['card1_title'] ?? 'Registration'); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($features['card1_desc'] ?? ''); ?>
                        </p>
                    </div>

                    <div class="calendar-card glass p-12 rounded-[3rem] shadow-xl border-t-[12px] border-yellow-500">
                        <div class="w-24 h-24 rounded-3xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card2_icon'] ?? 'history_edu'); ?></span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($features['card2_title'] ?? 'Lectures & Exams'); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($features['card2_desc'] ?? ''); ?>
                        </p>
                    </div>

                    <div class="calendar-card glass p-12 rounded-[3rem] shadow-xl border-t-[12px] border-purple-600">
                        <div class="w-24 h-24 rounded-3xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($features['card3_icon'] ?? 'celebration'); ?></span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($features['card3_title'] ?? 'Holidays & Events'); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags($features['card3_desc'] ?? ''); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Calendar Section -->
    <section class="py-40 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-[1400px] mx-auto">
                <!-- Digital Document Container -->
                <div class="bg-white dark:bg-gray-900 shadow-[0_0_100px_rgba(0,0,0,0.1)] dark:shadow-[0_0_100px_rgba(0,0,0,0.3)] rounded-[1.5rem] overflow-hidden border border-gray-200 dark:border-gray-800 relative">
                    <!-- Letterhead / Header -->
                    <div class="p-16 border-b-4 border-emerald-600 bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-8">
                            <img src="vvu_logo.jpg" alt="VVU Logo" class="w-32 h-auto">
                            <div>
                                <h2 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Valley View University</h2>
                                <p class="text-xl font-bold text-emerald-600 tracking-widest uppercase mt-2">Registry - Academic Affairs (Sandwich)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div class="p-10 md:p-20">
                        <?php if (!empty($document['calendar_pdf'])): ?>
                            <div class="mb-10 text-center">
                                <a href="<?php echo strip_tags($document['calendar_pdf']); ?>" target="_blank" class="inline-flex items-center gap-4 px-10 py-5 bg-emerald-600 text-white rounded-2xl font-black shadow-xl hover:bg-emerald-700 transition-all transform hover:scale-105">
                                    <span class="material-symbols-outlined text-3xl">download</span>
                                    Download Sandwich Calendar
                                </a>
                            </div>
                            <div class="rounded-[2rem] overflow-hidden border-8 border-gray-100 dark:border-gray-800 shadow-2xl bg-white" style="height: 1100px;">
                                <iframe src="<?php echo strip_tags($document['calendar_pdf']); ?>#toolbar=0" width="100%" height="100%" frameborder="0">
                                    <p>Your browser does not support iframes. <a href="<?php echo strip_tags($document['calendar_pdf']); ?>">Click here to download the PDF.</a></p>
                                </iframe>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <?php echo $document['table_html'] ?? '<!-- Paste calendar table HTML here -->'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-10 leading-tight"><?php echo strip_tags($support['title'] ?? ''); ?></h2>
                        <p class="text-xl text-gray-600 dark:text-gray-400 font-medium mb-10 leading-relaxed">
                            <?php echo strip_tags($support['description'] ?? ''); ?>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <?php for($i=1; $i<=4; $i++): 
                                $s_title = $support["item{$i}_title"] ?? '';
                                $s_desc = $support["item{$i}_desc"] ?? '';
                                $s_icon = $support["item{$i}_icon"] ?? 'school';
                                if(empty($s_title)) continue;
                            ?>
                            <div class="p-8 bg-white dark:bg-gray-900 rounded-3xl shadow-sm">
                                <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-emerald-600 text-3xl"><?php echo strip_tags($s_icon); ?></span>
                                </div>
                                <h4 class="text-xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($s_title); ?></h4>
                                <p class="text-lg text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($s_desc); ?></p>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-[4rem] blur-2xl opacity-20"></div>
                        <div class="relative glass p-12 rounded-[4rem] shadow-2xl text-center">
                            <div class="w-32 h-32 mx-auto rounded-[2.5rem] bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-white shadow-lg mb-10 animate-float">
                                <span class="material-symbols-outlined text-6xl text-white">contact_support</span>
                            </div>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($assistance['title'] ?? ''); ?></h3>
                            <p class="text-xl text-gray-600 dark:text-gray-400 font-medium mb-10">
                                <?php echo strip_tags($assistance['description'] ?? ''); ?>
                            </p>
                            <a href="contact_us.php" class="inline-flex items-center gap-5 px-12 py-6 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-2xl font-black rounded-[2rem] transition-all hover:scale-105 shadow-lg">
                                Contact Registry
                                <span class="material-symbols-outlined text-3xl">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-emerald-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-12 leading-tight">
                    <?php echo strip_tags($cta['title_part1'] ?? ''); ?> <br><span class="text-4xl sm:text-5xl md:text-6xl text-yellow-400"><?php echo strip_tags($cta['title_part2'] ?? ''); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl text-emerald-100 mb-20 max-w-5xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($cta['description'] ?? ''); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-10 justify-center">
                    <?php if (!empty($document['calendar_pdf'])): ?>
                    <a href="<?php echo strip_tags($document['calendar_pdf']); ?>" download class="px-16 py-8 bg-yellow-400 hover:bg-yellow-300 text-emerald-900 text-3xl font-bold rounded-[3rem] transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-5">
                        <span class="material-symbols-outlined text-4xl text-emerald-900">picture_as_pdf</span>
                        Download PDF
                    </a>
                    <?php endif; ?>
                    <a href="index.php" class="px-16 py-8 bg-white/10 hover:bg-white/20 text-white text-3xl font-bold rounded-[3rem] transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-5">
                        <span class="material-symbols-outlined text-4xl text-white">home</span>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
