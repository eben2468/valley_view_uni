<?php
/**
 * Valley View University - Admissions Page
 * Modern, responsive admissions page with database integration
 */

require_once('includes/db_connect.php');

$page_title = "Admissions - Valley View University";
$active_page = "admissions";

// Fetch latest notices (announcements) from the database
try {
    $notices_stmt = $pdo->prepare("
        SELECT id, title, slug, excerpt, featured_image, publish_date 
        FROM news_articles 
        WHERE status = 'published' AND category = 'announcements'
        ORDER BY publish_date DESC 
        LIMIT 3
    ");
    $notices_stmt->execute();
    $notices = $notices_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $notices = [];
}

// Fetch latest news from the database
try {
    $news_stmt = $pdo->prepare("
        SELECT id, title, slug, excerpt, featured_image, category, publish_date 
        FROM news_articles 
        WHERE status = 'published' AND category IN ('news', 'events')
        ORDER BY publish_date DESC 
        LIMIT 4
    ");
    $news_stmt->execute();
    $latest_news = $news_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latest_news = [];
}

// Fetch featured academic programs from the database
try {
    $programs_stmt = $pdo->prepare("
        SELECT ap.id, ap.title, ap.description, ap.image_url, pc.name as category_name, pc.icon as category_icon
        FROM academic_programs ap
        LEFT JOIN program_categories pc ON ap.category_id = pc.id
        WHERE ap.is_active = 1
        ORDER BY RAND()
        LIMIT 6
    ");
    $programs_stmt->execute();
    $featured_programs = $programs_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_programs = [];
}

// Fetch page content from academic_pages_content
try {
    $page_stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'admissions'");
    $page_stmt->execute();
    $page_data = $page_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $page_data = [];
}

// Fetch stats
try {
    $stats_stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = 'admissions' ORDER BY display_order");
    $stats_stmt->execute();
    $page_stats = $stats_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $page_stats = [];
}

// Fetch sections
try {
    $sections_stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'admissions' ORDER BY display_order");
    $sections_stmt->execute();
    $page_sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);
    $sections_map = [];
    foreach ($page_sections as $s) {
        $sections_map[$s['section_key']] = $s;
    }
} catch (PDOException $e) {
    $page_sections = [];
    $sections_map = [];
}

// Fetch items grouped by section
try {
    $items_stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'admissions' AND is_active = 1 ORDER BY display_order");
    $items_stmt->execute();
    $all_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    $items_map = [];
    foreach ($all_items as $item) {
        $items_map[$item['section_key']][] = $item;
    }
} catch (PDOException $e) {
    $items_map = [];
}

// Helper functions
function formatAdmissionDate($date) {
    return date('M j, Y', strtotime($date));
}

function getAdmissionImage($image, $default = '') {
    if (!empty($image) && (file_exists($image) || strpos($image, 'http') === 0)) {
        return $image;
    }
    return !empty($default) ? $default : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&q=80';
}

function getProgramCategoryColor($category) {
    $colors = [
        'School of Business' => 'from-blue-500 to-blue-700',
        'Faculty of Arts & Social Science' => 'from-purple-500 to-purple-700',
        'Faculty of Arts & Social Sciences' => 'from-purple-500 to-purple-700',
        'Department of Teacher Education' => 'from-yellow-500 to-yellow-700',
        'Faculty of Science' => 'from-green-500 to-green-700',
        'School of IT & Computing' => 'from-indigo-500 to-indigo-700',
        'School of Theology & Missions' => 'from-red-500 to-red-700',
    ];
    return $colors[$category] ?? 'from-blue-500 to-blue-700';
}

function getProgramBadgeColor($category) {
    $colors = [
        'School of Business' => 'bg-blue-600',
        'Faculty of Arts & Social Science' => 'bg-purple-600',
        'Faculty of Arts & Social Sciences' => 'bg-purple-600',
        'Department of Teacher Education' => 'bg-yellow-600',
        'Faculty of Science' => 'bg-green-600',
        'School of IT & Computing' => 'bg-indigo-600',
        'School of Theology & Missions' => 'bg-red-600',
    ];
    return $colors[$category] ?? 'bg-blue-600';
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
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
        50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.8); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
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
    .admission-card {
        transition: all 0.3s ease;
    }
    .admission-card:hover {
        transform: translateY(-10px);
    }
    .notice-item {
        transition: all 0.3s ease;
    }
    .notice-item:hover {
        transform: translateX(5px);
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: scale(1.05);
    }
    .requirement-card {
        transition: all 0.3s ease;
    }
    .requirement-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 0;
        width: 20px;
        height: 20px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        left: -21px;
        top: 20px;
        width: 2px;
        height: calc(100% + 20px);
        background: linear-gradient(to bottom, #3b82f6, #dbeafe);
    }
    .timeline-item:last-child::after {
        display: none;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'vvu_admissions_hero_1766876689316.png'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?? 'Admissions 2024/2025'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'Are You Ready'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'To Apply?'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description'] ?? 'Join Ghana\'s first chartered private university and embark on a journey of holistic education and excellence.'); ?>"
                </p>

                <?php $hero_btns = $items_map['hero_buttons'] ?? []; ?>
                <div class="mt-10 flex flex-col sm:flex-row gap-5 justify-center animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="<?php echo strip_tags($hero_btns[0]['item_link'] ?? 'apply.php'); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-3 animate-pulse-glow">
                        <span class="material-symbols-outlined text-2xl"><?php echo strip_tags($hero_btns[0]['item_icon'] ?? 'edit_square'); ?></span>
                        <?php echo strip_tags($hero_btns[0]['item_title'] ?? 'Apply Online Now'); ?>
                    </a>
                    <a href="<?php echo strip_tags($hero_btns[1]['item_link'] ?? '#process'); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl"><?php echo strip_tags($hero_btns[1]['item_icon'] ?? 'info'); ?></span>
                        <?php echo strip_tags($hero_btns[1]['item_title'] ?? 'Admission Process'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
            <span class="material-symbols-outlined text-white/60 text-4xl">expand_more</span>
        </div>
    </section>

    <!-- Quick Stats Section -->
    <section class="relative z-20 -mt-16 pb-12">
        <div class="container">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                <?php 
                $stat_colors = ['blue', 'green', 'yellow', 'purple'];
                $stat_index = 0;
                foreach ($page_stats as $stat): 
                    $color = $stat_colors[$stat_index % 4];
                    $stat_index++;
                ?>
                <div class="stat-card text-center p-8 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-<?php echo $color; ?>-500 to-<?php echo $color; ?>-700 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-3xl"><?php echo strip_tags($stat['stat_icon'] ?? 'star'); ?></span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($stat['stat_value']); ?></h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($stat['stat_label']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Quick Contact & Notices -->
    <?php
    $contact_section = $sections_map['contact'] ?? ['section_title' => 'Get in Touch', 'section_subtitle' => 'Your first point of contact'];
    $contact_items = $items_map['contact'] ?? [];
    $contact_desc = '';
    $contact_phone = '+233 302 230 990';
    $contact_phone_link = 'tel:+233302230990';
    $contact_email = 'admissions@vvu.edu.gh';
    $contact_email_link = 'mailto:admissions@vvu.edu.gh';
    $contact_btn_text = 'REQUEST INFORMATION';
    $contact_btn_link = 'contact_us.php';
    foreach ($contact_items as $ci) {
        if ($ci['item_title'] === 'Contact Description') $contact_desc = $ci['item_description'];
        elseif ($ci['item_title'] === 'Phone') { $contact_phone = $ci['item_description']; $contact_phone_link = $ci['item_link']; }
        elseif ($ci['item_title'] === 'Email') { $contact_email = $ci['item_description']; $contact_email_link = $ci['item_link']; }
        elseif ($ci['item_title'] === 'Button Text') { $contact_btn_text = $ci['item_description']; $contact_btn_link = $ci['item_link']; }
    }
    ?>
    <section class="py-24 bg-white dark:bg-gray-800 relative z-20 mx-auto max-w-7xl rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Contact Form Area -->
                <div class="p-10 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined text-3xl">contact_support</span>
                        </div>
                        <div>
                            <h2 class="text-4xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($contact_section['section_title']); ?></h2>
                            <p class="text-xl text-gray-500 dark:text-gray-400 font-medium"><?php echo strip_tags($contact_section['section_subtitle']); ?></p>
                        </div>
                    </div>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 mb-10 leading-relaxed">
                        <?php echo strip_tags($contact_desc); ?>
                    </p>
                    <div class="space-y-6">
                        <a href="<?php echo strip_tags($contact_btn_link); ?>" class="inline-flex items-center gap-4 px-10 py-5 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg w-full justify-center">
                            <?php echo strip_tags($contact_btn_text); ?>
                            <span class="material-symbols-outlined">send</span>
                        </a>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="<?php echo strip_tags($contact_phone_link); ?>" class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-all">
                                <span class="material-symbols-outlined text-blue-600">call</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium"><?php echo strip_tags($contact_phone); ?></span>
                            </a>
                            <a href="<?php echo strip_tags($contact_email_link); ?>" class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-all">
                                <span class="material-symbols-outlined text-blue-600">mail</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium truncate"><?php echo strip_tags($contact_email); ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Notice Board - Dynamic -->
                <div class="p-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-500 flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined text-3xl">campaign</span>
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white">Notice Board</h2>
                    </div>
                    <div class="space-y-6">
                        <?php if (empty($notices)): ?>
                            <p class="text-2xl text-gray-500 dark:text-gray-400 italic">No notices available at this time.</p>
                        <?php else: ?>
                            <?php foreach ($notices as $notice): ?>
                            <a href="notices_detail.php?slug=<?php echo urlencode($notice['slug']); ?>" class="notice-item block group">
                                <div class="flex gap-6 items-start p-6 rounded-3xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all border border-transparent hover:border-gray-100 dark:hover:border-gray-800">
                                    <div class="text-blue-600 dark:text-blue-400 flex-shrink-0">
                                        <span class="material-symbols-outlined text-4xl">event_note</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-2xl font-bold text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors line-clamp-2">
                                            <?php echo strip_tags($notice['title']); ?>
                                        </h4>
                                        <p class="text-xl text-gray-500 mt-2 line-clamp-2">
                                            <?php echo strip_tags($notice['excerpt'] ?: 'Click to read more about this notice.'); ?>
                                        </p>
                                        <span class="text-base text-gray-400 mt-2 inline-block">
                                            <span class="material-symbols-outlined text-base align-middle">schedule</span>
                                            <?php echo formatAdmissionDate($notice['publish_date']); ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <a href="notices.php" class="inline-flex items-center gap-2 text-xl font-bold text-blue-600 hover:gap-4 transition-all mt-4">
                            See All Notices <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose VVU Section -->
    <section class="py-24 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 relative overflow-hidden">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20 px-4">
                <div class="inline-flex items-center gap-4 px-6 py-2.5 mb-8 rounded-2xl bg-gradient-to-r from-blue-700 to-blue-500 shadow-xl text-white mx-auto">
                    <span class="material-symbols-outlined text-2xl text-white">verified</span>
                    <span class="text-base font-black uppercase tracking-[0.2em] text-white"><?php echo strip_tags($sections_map['why_choose']['section_title'] ?? 'Why Choose VVU?'); ?></span>
                </div>
                <!-- Sized just under the page's other section headings (37.5px/900).
                     This one is a full sentence, not a short label, so it reads
                     better a little smaller and lighter. -->
                <h2 class="text-[20px] md:text-[30px] font-bold text-gray-900 dark:text-white mb-8 tracking-tight leading-snug">
                    <?php echo strip_tags($sections_map['why_choose']['section_subtitle'] ?? 'Our Unique Value'); ?>
                </h2>
                <div class="h-2 w-24 bg-blue-600 mx-auto rounded-full mb-8"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php 
                $benefit_colors = ['blue', 'green', 'indigo', 'purple', 'red', 'teal'];
                $benefit_index = 0;
                $why_items = $items_map['why_choose'] ?? [];
                foreach ($why_items as $item): 
                    $color = $benefit_colors[$benefit_index % 6];
                    $benefit_index++;
                ?>
                <div class="group p-10 bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-all hover:-translate-y-3">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-<?php echo $color; ?>-500 to-<?php echo $color; ?>-700 flex items-center justify-center mb-8 group-hover:rotate-6 transition-transform shadow-lg">
                        <span class="material-symbols-outlined text-white text-4xl"><?php echo strip_tags($item['item_icon'] ?? 'star'); ?></span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-5 transition-colors group-hover:text-blue-600"><?php echo strip_tags($item['item_title']); ?></h3>
                    <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Programs - Dynamic from Admin -->
    <?php 
    $fp_section = $sections_map['programs'] ?? ['section_title' => 'Featured Programs', 'section_subtitle' => 'Discover our most popular and impactful degree programs designed for your success.'];
    $program_items = $items_map['programs'] ?? [];
    $badge_colors = [
        'Business' => 'bg-blue-600', 'Health' => 'bg-green-600', 'Technology' => 'bg-purple-600',
        'Education' => 'bg-yellow-600', 'Theology' => 'bg-red-600', 'Arts' => 'bg-indigo-600',
    ];
    ?>
    <section class="py-24">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($fp_section['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($fp_section['section_subtitle'] ?? ''); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <?php foreach ($program_items as $program):
                    $badge = $program['item_stat_value'] ?: 'Program';
                    // Admin-set colour wins; otherwise fall back to the known
                    // badge map, so a new badge name isn't stuck on blue.
                    $badge_color = !empty($program['item_color'])
                        ? 'bg-' . preg_replace('/[^a-z0-9\-]/i', '', $program['item_color'])
                        : ($badge_colors[$badge] ?? 'bg-blue-600');
                ?>
                <div class="admission-card group bg-white dark:bg-gray-800 rounded-[2.5rem] overflow-hidden shadow-xl border border-gray-100 dark:border-gray-800">
                    <div class="relative h-72 overflow-hidden">
                        <img src="<?php echo strip_tags($program['item_image'] ?: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&q=80'); ?>" 
                             alt="<?php echo strip_tags($program['item_title']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-6 right-6 px-4 py-2 <?php echo $badge_color; ?> text-white rounded-full font-bold text-lg shadow-lg">
                            <?php echo strip_tags($badge); ?>
                        </div>
                    </div>
                    <div class="p-10">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4 line-clamp-2"><?php echo strip_tags($program['item_title']); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 leading-relaxed line-clamp-3">
                            <?php echo strip_tags($program['item_description'] ?: 'Explore this exciting program at Valley View University.'); ?>
                        </p>
                        <a href="<?php echo strip_tags($program['item_link'] ?: 'academic_programs_overview.php'); ?>" class="inline-flex items-center gap-3 text-xl font-bold text-blue-600 hover:gap-5 transition-all">
                            View Course Details <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-16">
                <a href="academic_programs_overview.php" class="inline-flex items-center gap-4 px-12 py-6 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                    <span class="material-symbols-outlined text-3xl">school</span>
                    View All Programs
                </a>
            </div>
        </div>
    </section>

    <!-- Admission Requirements Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <span class="inline-block px-6 py-2 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 font-bold text-lg mb-6">REQUIREMENTS</span>
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sections_map['requirements']['section_title'] ?? 'Admission Requirements'); ?></h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sections_map['requirements']['section_subtitle'] ?? 'What you need to apply for admission to Valley View University.'); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 max-w-6xl mx-auto">
                <?php 
                $req_colors = ['blue', 'green', 'purple'];
                $req_index = 0;
                $req_items = $items_map['requirements'] ?? [];
                foreach ($req_items as $item): 
                    $color = $req_colors[$req_index % 3];
                    $req_index++;
                ?>
                <div class="requirement-card bg-white dark:bg-gray-800 rounded-[2rem] p-10 shadow-xl border border-gray-100 dark:border-gray-700">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-<?php echo $color; ?>-500 to-<?php echo $color; ?>-700 flex items-center justify-center mb-8">
                        <span class="material-symbols-outlined text-white text-3xl"><?php echo strip_tags($item['item_icon'] ?? 'school'); ?></span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($item['item_title']); ?></h3>
                    <?php if (!empty($item['item_subtitle'])): ?>
                    <p class="text-xl text-blue-600 dark:text-blue-400 font-semibold mb-4"><?php echo strip_tags($item['item_subtitle']); ?></p>
                    <?php endif; ?>
                    <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Admission Process Section -->
    <section id="process" class="py-24 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-6"><?php echo strip_tags($sections_map['process']['section_title'] ?? 'Admission Process'); ?></h2>
                <p class="text-2xl text-blue-100 font-medium leading-relaxed"><?php echo strip_tags($sections_map['process']['section_subtitle'] ?? 'Follow these simple steps to join our vibrant academic community.'); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php 
                $process_items = $items_map['process'] ?? [];
                $step_num = 1;
                foreach ($process_items as $step): 
                ?>
                <div class="relative p-10 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/10 text-center group">
                    <div class="w-20 h-20 rounded-3xl bg-yellow-400 flex items-center justify-center text-blue-900 text-4xl font-black mx-auto mb-8 group-hover:scale-110 transition-transform"><?php echo strip_tags($step['item_stat_value'] ?? $step_num); ?></div>
                    <h4 class="text-3xl font-black text-white mb-4"><?php echo strip_tags($step['item_title']); ?></h4>
                    <p class="text-2xl text-blue-100 leading-relaxed"><?php echo strip_tags($step['item_description']); ?></p>
                </div>
                <?php 
                $step_num++;
                endforeach; 
                ?>
            </div>
        </div>
    </section>

    <!-- Latest News - Dynamic -->
    <?php $ln_section = $sections_map['latest_news'] ?? ['section_title' => 'Latest from Campus', 'section_subtitle' => 'Stay updated with the latest events and stories from our community.']; ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($ln_section['section_title']); ?></h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($ln_section['section_subtitle'] ?? ''); ?></p>
            </div>

            <?php if (empty($latest_news)): ?>
            <!-- Fallback static content if no news -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="flex flex-col md:flex-row gap-8 bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800 group">
                    <div class="md:w-1/3 h-48 rounded-3xl overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=400" alt="News" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="md:w-2/3">
                        <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-4 group-hover:text-blue-600 transition-colors">Business Plan Bootcamp and Seminar</h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">Inspiring young entrepreneurs through transformative business planning sessions.</p>
                        <a href="news_&_events.php" class="text-blue-600 font-bold text-xl flex items-center gap-2">Read More <span class="material-symbols-outlined">chevron_right</span></a>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-8 bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800 group">
                    <div class="md:w-1/3 h-48 rounded-3xl overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&q=80&w=400" alt="News" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="md:w-2/3">
                        <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-4 group-hover:text-blue-600 transition-colors">Ellen Gould White Residence Hall Week</h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">Celebrating community and culture at Valley View University residence halls.</p>
                        <a href="news_&_events.php" class="text-blue-600 font-bold text-xl flex items-center gap-2">Read More <span class="material-symbols-outlined">chevron_right</span></a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <?php foreach ($latest_news as $news_item): ?>
                <div class="flex flex-col md:flex-row gap-8 bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800 group">
                    <div class="md:w-1/3 h-48 rounded-3xl overflow-hidden">
                        <img src="<?php echo strip_tags(getAdmissionImage($news_item['featured_image'], 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&q=80')); ?>" 
                             alt="<?php echo strip_tags($news_item['title']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="md:w-2/3">
                        <span class="inline-block px-3 py-1 rounded-full text-base font-bold mb-3 <?php echo $news_item['category'] === 'events' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'; ?>">
                            <?php echo ucfirst($news_item['category']); ?>
                        </span>
                        <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-4 group-hover:text-blue-600 transition-colors line-clamp-2">
                            <?php echo strip_tags($news_item['title']); ?>
                        </h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400 mb-6 leading-relaxed line-clamp-2">
                            <?php echo strip_tags($news_item['excerpt'] ?: 'Click to read more about this update.'); ?>
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-base text-gray-500">
                                <span class="material-symbols-outlined text-base align-middle">schedule</span>
                                <?php echo formatAdmissionDate($news_item['publish_date']); ?>
                            </span>
                            <a href="<?php echo $news_item['category'] === 'events' ? 'event_detail.php' : 'news_detail.php'; ?>?slug=<?php echo urlencode($news_item['slug']); ?>" 
                               class="text-blue-600 font-bold text-xl flex items-center gap-2">
                                Read More <span class="material-symbols-outlined">chevron_right</span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="text-center mt-16">
                <a href="news_&_events.php" class="inline-flex items-center gap-4 px-10 py-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-2xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-all shadow-lg">
                    View All News & Events <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-yellow-400 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        </div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-8"><?php echo strip_tags($page_data['cta_title'] ?? 'Ready to start your journey?'); ?></h2>
                <p class="text-2xl text-blue-100 mb-12 font-medium leading-relaxed">
                    <?php echo strip_tags($page_data['cta_subtitle'] ?? 'Join thousands of students who have chosen Valley View University for a life-changing educational experience.'); ?>
                </p>
                <?php $cta_extra = $items_map['cta_extra'] ?? []; ?>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">rocket_launch</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="<?php echo strip_tags($cta_extra[0]['item_link'] ?? 'contact_us.php'); ?>" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4 backdrop-blur-md">
                        <span class="material-symbols-outlined text-3xl"><?php echo strip_tags($cta_extra[0]['item_icon'] ?? 'chat'); ?></span>
                        <?php echo strip_tags($cta_extra[0]['item_title'] ?? 'Talk to an Advisor'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>