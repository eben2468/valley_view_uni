<?php
$page_title = "Employment Opportunities - Valley View University";
$active_page = "resources";
require_once 'includes/db_connect.php';

// Fetch data from database
$page_key = 'employment_opportunities';
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
        $item['meta'] = json_decode($item['extra_data'], true) ?: [];
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
    .job-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .job-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
    }
    .icon-container {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.3);
    }
    .icon-container span {
        color: white !important;
        font-size: 32px;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&q=80&w=1920'); ?>" 
                 alt="VVU Team" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'Careers at VVU'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Join Our'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Academic Family'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? '"Be part of a community dedicated to excellence in education, research, and service. Discover your next professional milestone."'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <div class="w-full max-w-[95%] mx-auto px-8 md:px-16 relative z-20 -mt-20">
        <!-- Search and Filter Bar -->
        <section class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-2xl mb-16 border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-6">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-3xl">search</span>
                        <input type="text" placeholder="Search by job title, department, or keywords..." 
                               class="w-full pl-14 pr-6 py-5 bg-gray-50 dark:bg-gray-900 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 text-xl font-medium">
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <select class="w-full px-6 py-5 bg-gray-50 dark:bg-gray-900 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 text-xl font-medium appearance-none">
                        <option>All Categories</option>
                        <option>Faculty</option>
                        <option>Staff</option>
                        <option>Administration</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <select class="w-full px-6 py-5 bg-gray-50 dark:bg-gray-900 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 text-xl font-medium appearance-none">
                        <option>All Locations</option>
                        <option>Oyibi Campus</option>
                        <option>Techiman Campus</option>
                        <option>Kumasi Campus</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <button class="w-full py-5 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg flex items-center justify-center gap-3">
                        Find Jobs
                        <span class="material-symbols-outlined text-white">search</span>
                    </button>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-16">
            <!-- Main Content: Job Listings -->
            <div class="xl:col-span-2 space-y-12">
                <!-- Featured Openings -->
                <?php 
                $featured_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'featured_openings'))[0] ?? null;
                if ($featured_section): 
                ?>
                <section>
                    <div class="flex items-center gap-6 mb-10">
                        <div class="icon-container">
                            <span class="material-symbols-outlined">star</span>
                        </div>
                        <h2 class="text-5xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($featured_section['section_title']); ?></h2>
                    </div>

                    <div class="space-y-8">
                        <?php foreach ($grouped_items['featured_openings'] ?? [] as $job): ?>
                        <div class="job-card bg-white dark:bg-gray-800 p-10 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="flex flex-col md:flex-row justify-between gap-8">
                                <div class="flex-grow">
                                    <div class="flex flex-wrap gap-3 mb-4">
                                        <span class="px-4 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-lg font-bold">Full-time</span>
                                        <span class="px-4 py-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full text-lg font-bold">Faculty</span>
                                    </div>
                                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($job['item_title']); ?></h3>
                                    <p class="text-2xl text-gray-500 dark:text-gray-400 font-bold mb-6"><?php echo strip_tags($job['item_subtitle']); ?></p>
                                    <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                        <?php echo strip_tags($job['item_description']); ?>
                                    </p>
                                </div>
                                <div class="shrink-0 flex flex-col justify-center">
                                    <a href="<?php echo strip_tags($job['item_link']); ?>" class="px-10 py-5 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg flex items-center justify-center gap-3">
                                        View & Apply
                                        <span class="material-symbols-outlined text-white">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- All Openings -->
                <?php 
                $all_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'all_openings'))[0] ?? null;
                if ($all_section): 
                ?>
                <section>
                    <div class="flex items-center gap-6 mb-10">
                        <div class="icon-container bg-gradient-to-br from-gray-600 to-gray-800">
                            <span class="material-symbols-outlined">work</span>
                        </div>
                        <h2 class="text-5xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($all_section['section_title']); ?> (<?php echo count($grouped_items['all_openings'] ?? []); ?>)</h2>
                    </div>

                    <div class="space-y-8">
                        <?php foreach ($grouped_items['all_openings'] ?? [] as $job): ?>
                        <div class="job-card bg-white dark:bg-gray-800 p-10 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="flex flex-col md:flex-row justify-between gap-8">
                                <div class="flex-grow">
                                    <div class="flex flex-wrap gap-3 mb-4">
                                        <span class="px-4 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-lg font-bold">Full-time</span>
                                        <span class="px-4 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full text-lg font-bold">Staff</span>
                                    </div>
                                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($job['item_title']); ?></h3>
                                    <p class="text-2xl text-gray-500 dark:text-gray-400 font-bold mb-6"><?php echo strip_tags($job['item_subtitle']); ?></p>
                                    <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                        <?php echo strip_tags($job['item_description']); ?>
                                    </p>
                                </div>
                                <div class="shrink-0 flex flex-col justify-center">
                                    <a href="<?php echo strip_tags($job['item_link']); ?>" class="px-10 py-5 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg flex items-center justify-center gap-3">
                                        View & Apply
                                        <span class="material-symbols-outlined text-white">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center items-center gap-4 mt-16">
                        <button class="w-16 h-16 flex items-center justify-center rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-400 cursor-not-allowed">
                            <span class="material-symbols-outlined text-3xl">chevron_left</span>
                        </button>
                        <button class="w-16 h-16 flex items-center justify-center rounded-2xl bg-blue-600 text-white text-2xl font-black shadow-lg">1</button>
                        <button class="w-16 h-16 flex items-center justify-center rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-2xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">2</button>
                        <button class="w-16 h-16 flex items-center justify-center rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-2xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">3</button>
                        <button class="w-16 h-16 flex items-center justify-center rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            <span class="material-symbols-outlined text-3xl">chevron_right</span>
                        </button>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="xl:col-span-1 space-y-12">
                <!-- Why Work at VVU -->
                <?php 
                $why_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'why_join'))[0] ?? null;
                if ($why_section): 
                ?>
                <div class="bg-white dark:bg-gray-800 p-10 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-xl">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-400 flex items-center justify-center text-blue-900 shadow-lg">
                            <span class="material-symbols-outlined text-3xl text-white">favorite</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($why_section['section_title']); ?></h3>
                    </div>
                    <ul class="space-y-6">
                        <?php foreach ($grouped_items['why_join'] ?? [] as $reason): ?>
                        <li class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-<?php echo strip_tags($reason['item_color'] ?? 'blue-600'); ?> text-3xl mt-1"><?php echo strip_tags($reason['item_icon'] ?? 'check_circle'); ?></span>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($reason['item_description']); ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- How to Apply -->
                <?php 
                $how_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'how_apply'))[0] ?? null;
                if ($how_section): 
                ?>
                <div class="bg-blue-900 p-10 rounded-[2.5rem] shadow-xl relative overflow-hidden text-center">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/10 rounded-full -mr-16 -mt-16"></div>
                    <h3 class="text-3xl font-black text-white mb-6"><?php echo strip_tags($how_section['section_title']); ?></h3>
                    <?php foreach ($grouped_items['how_apply'] ?? [] as $info): ?>
                    <p class="text-2xl text-blue-100 leading-relaxed font-medium mb-8">
                        <?php echo strip_tags($info['item_description']); ?>
                    </p>
                    <a href="<?php echo strip_tags($info['item_link']); ?>" download class="w-full py-5 bg-yellow-400 text-blue-900 text-xl font-bold rounded-2xl hover:bg-yellow-300 transition-all shadow-lg flex items-center justify-center gap-3">
                        Download Application Form
                        <span class="material-symbols-outlined">download</span>
                    </a>
                    <?php endforeach; ?>
                    <a href="contact_us.php" class="mt-6 flex items-center justify-center gap-3 text-xl font-bold text-white hover:text-yellow-400 transition-all">
                        Contact HR Support
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
                <?php endif; ?>

                <!-- Hiring Process -->
                <?php 
                $process_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'hiring_process'))[0] ?? null;
                if ($process_section): 
                ?>
                <div class="bg-white dark:bg-gray-800 p-10 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-xl">
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($process_section['section_title']); ?></h3>
                    <div class="space-y-8">
                        <?php foreach ($grouped_items['hiring_process'] ?? [] as $index => $step): ?>
                        <div class="flex gap-6">
                            <div class="shrink-0 w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-black"><?php echo ($index + 1); ?></div>
                            <div>
                                <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo strip_tags($step['item_title']); ?></h4>
                                <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($step['item_description']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>

    <!-- CTA Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 mt-24">
        <div class="w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-[3rem] p-16 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-8 relative z-10"><?php echo strip_tags($hero['cta_title'] ?? 'Start Your Journey With Us'); ?></h2>
                <p class="text-2xl sm:text-3xl text-blue-100 mb-12 max-w-4xl mx-auto relative z-10 font-medium">
                    <?php echo strip_tags($hero['cta_subtitle'] ?? ''); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center relative z-10">
                    <a href="<?php echo strip_tags($hero['cta_button_link'] ?? 'contact_us.php'); ?>" class="px-12 py-6 bg-white text-blue-900 text-2xl font-bold rounded-2xl hover:bg-gray-100 transition-all shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Submit General Interest'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>