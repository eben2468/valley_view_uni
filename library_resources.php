<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

$page_slug = 'library_resources';
$adminContent = new AdministrationContent($pdo);
$page_data = $adminContent->getPageBySlug($page_slug);

if (!$page_data) {
    include '404.php';
    exit;
}

$page_id = $page_data['id'];
$page_title = $page_data['page_name'] . " - Valley View University";
$active_page = "academics";

// Fetch all sections
$hero = $adminContent->getSectionFields($page_id, 'hero');
$director = $adminContent->getSectionFields($page_id, 'director');
$statsSection = $adminContent->getSectionFields($page_id, 'stats');
$digitalResources = $adminContent->getSectionFields($page_id, 'digital_resources');
$aboutLibraries = $adminContent->getSectionFields($page_id, 'about_libraries');
$branchLibraries = $adminContent->getSectionFields($page_id, 'branch_libraries');
$libraryPlans = $adminContent->getSectionFields($page_id, 'library_plans');

$is_admin = isset($_SESSION['admin_id']);

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
    @keyframes floatUp {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: floatUp 4s ease-in-out infinite; }
    
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
    
    .stat-card { transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); }

    /* Digital Resources Banner */
    .digital-banner {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .digital-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.88), rgba(219, 39, 119, 0.82), rgba(124, 58, 237, 0.88));
        z-index: 1;
    }
    .digital-banner img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .digital-banner-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 50px 30px;
    }
    .digital-banner-content h2 {
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        font-weight: 900;
        color: #fbbf24;
        margin-bottom: 12px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .digital-banner-content p {
        font-size: clamp(1rem, 2vw, 1.25rem);
        color: rgba(255,255,255,0.92);
        margin-bottom: 28px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }
    .digital-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 36px;
        background: linear-gradient(135deg, #1e3a5f, #1a365d);
        color: #fff;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        border: 2px solid rgba(255,255,255,0.15);
    }
    .digital-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.35);
        background: linear-gradient(135deg, #2d4a73, #234873);
    }

    /* About Libraries Section */
    .about-libraries-section {
        position: relative;
    }
    .about-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 40px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }
    .dark .about-card {
        background: #1f2937;
        border-color: rgba(255,255,255,0.08);
    }
    .about-card:hover {
        box-shadow: 0 12px 50px rgba(0,0,0,0.1);
    }
    .about-text {
        font-size: clamp(1.3rem, 2vw, 1.55rem);
        line-height: 1.9;
        color: #475569;
    }
    .dark .about-text {
        color: #94a3b8;
    }

    /* Branch Library Cards */
    .branch-card {
        background: #fff;
        border-radius: 20px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .dark .branch-card {
        background: #1f2937;
        border-color: rgba(255,255,255,0.08);
    }
    .branch-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, #3b82f6, #8b5cf6);
        border-radius: 0 4px 4px 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .branch-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.12);
    }
    .branch-card:hover::before {
        opacity: 1;
    }
    .branch-card h4 {
        font-size: 1.45rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dark .branch-card h4 {
        color: #f1f5f9;
    }
    .branch-card p {
        font-size: 1.15rem;
        line-height: 1.7;
        color: #64748b;
    }
    .dark .branch-card p {
        color: #94a3b8;
    }
    .branch-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.3rem;
    }

    /* Plans Section */
    .plans-section {
        position: relative;
    }
    .plan-block {
        background: #fff;
        border-radius: 20px;
        padding: 48px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }
    .dark .plan-block {
        background: #1f2937;
        border-color: rgba(255,255,255,0.08);
    }
    .plan-block:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .automation-list {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    .automation-list li {
        padding: 20px 28px 20px 64px;
        position: relative;
        font-size: 1.4rem;
        line-height: 1.85;
        color: #475569;
        background: #f8fafc;
        border-radius: 14px;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }
    .dark .automation-list li {
        background: #111827;
        border-color: rgba(255,255,255,0.06);
        color: #94a3b8;
    }
    .automation-list li::before {
        content: '\e86c';
        font-family: 'Material Symbols Outlined';
        position: absolute;
        left: 22px;
        top: 20px;
        font-size: 1.7rem;
        color: #22c55e;
    }
    .automation-list li:hover {
        transform: translateX(4px);
        border-color: #22c55e40;
    }

    /* Welcome Section Images */
    .welcome-gallery {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-top: 28px;
    }
    .welcome-gallery img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        transition: all 0.4s ease;
        border: 3px solid #fff;
    }
    .welcome-gallery img:hover {
        transform: scale(1.03);
        box-shadow: 0 12px 35px rgba(0,0,0,0.18);
    }

    /* Closing Statement */
    .closing-statement {
        text-align: center;
        padding: 50px 30px;
        background: linear-gradient(135deg, #fef3c7, #fde68a, #fef3c7);
        border-radius: 24px;
        border: 2px solid #f59e0b40;
    }
    .dark .closing-statement {
        background: linear-gradient(135deg, #78350f40, #92400e40, #78350f40);
        border-color: #f59e0b30;
    }
    .closing-statement h3 {
        font-size: clamp(1.7rem, 3.5vw, 2.5rem);
        font-weight: 900;
        color: #92400e;
        letter-spacing: 0.05em;
    }
    .dark .closing-statement h3 {
        color: #fbbf24;
    }

    /* Section Headers */
    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 18px;
    }
    .section-title {
        font-size: clamp(2.8rem, 5.5vw, 4.2rem);
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 28px;
    }

    /* Image gallery grid for about section */
    .about-images-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    @media (min-width: 640px) {
        .about-images-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    .about-images-grid img {
        width: 100%;
        height: 340px;
        object-fit: cover;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.4s ease;
        border: 4px solid #fff;
    }
    .dark .about-images-grid img {
        border-color: #374151;
    }
    .about-images-grid img:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Admin Edit Button -->
    <?php if ($is_admin): ?>
    <div class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container py-3 flex justify-end">
            <a href="admin/edit_administration_page.php?id=<?php echo $page_id; ?>" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-blue-500 hover:to-indigo-500 transition-all shadow-lg hover:shadow-xl"
               target="_blank">
                <i class="fas fa-edit"></i>
                Edit Library Contents
            </a>
        </div>
    </div>
    <?php endif; ?>
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1507842217343-583bb7270b66'); ?>" 
                 alt="VVU Library" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-7xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['badge_text'] ?? 'Knowledge Hub'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['title_1'] ?? 'VVU'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['title_2'] ?? 'Library'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['description'] ?? '"Your Gateway to Knowledge — Discover, Learn, Grow"'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Digital Resources Banner -->
    <section class="py-16 bg-gray-100 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="digital-banner animate-fadeInUp">
                    <img src="<?php echo strip_tags($digitalResources['banner_image'] ?? 'uploads/Library/library-banner.jpg'); ?>" 
                         alt="Digital Library Resources">
                    <div class="digital-banner-content">
                        <h2><?php echo strip_tags($digitalResources['banner_title'] ?? 'Discover new knowledge!'); ?></h2>
                        <p><?php echo strip_tags($digitalResources['banner_subtitle'] ?? ''); ?></p>
                        <a href="<?php echo strip_tags($digitalResources['banner_button_url'] ?? 'digital_books.php'); ?>" class="digital-btn">
                            <span class="material-symbols-outlined">menu_book</span>
                            <?php echo strip_tags($digitalResources['banner_button_text'] ?? 'Check Out Digital Books and Online Resources'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Welcome Message Section -->
    <section class="py-28 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-16 items-start">
                    <div class="lg:col-span-1 text-center lg:sticky lg:top-32">
                        <div class="w-64 h-64 mx-auto rounded-full overflow-hidden shadow-2xl mb-8 ring-4 ring-blue-600 ring-offset-4 ring-offset-white dark:ring-offset-gray-900">
                            <img src="<?php echo strip_tags($director['image'] ?? 'uploads/Library/library-director.jpg'); ?>" alt="University Librarian" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($director['name'] ?? ''); ?></h3>
                        <p class="text-2xl text-blue-600 dark:text-blue-400 font-bold italic"><?php echo strip_tags($director['title'] ?? 'University Librarian'); ?></p>
                        
                        <!-- Gallery Images -->
                        <?php if (!empty($aboutLibraries['about_image_1']) || !empty($aboutLibraries['about_image_2'])): ?>
                        <div class="welcome-gallery mt-10">
                            <?php if (!empty($aboutLibraries['about_image_1'])): ?>
                            <img src="<?php echo strip_tags($aboutLibraries['about_image_1']); ?>" alt="VVU Library Interior">
                            <?php endif; ?>
                            <?php if (!empty($aboutLibraries['about_image_2'])): ?>
                            <img src="<?php echo strip_tags($aboutLibraries['about_image_2']); ?>" alt="VVU Library Collection">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="inline-flex items-center gap-4 px-10 py-4 mb-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                            <span class="material-symbols-outlined text-4xl">waving_hand</span>
                            <span class="text-2xl font-black uppercase tracking-wider"><?php echo strip_tags($director['welcome_badge'] ?? 'Welcome Message'); ?></span>
                        </div>
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-10">
                            <?php echo strip_tags($director['welcome_heading_1'] ?? 'Welcome to the'); ?> <span class="text-blue-600" style="font-size: inherit;"><?php echo strip_tags($director['welcome_heading_2'] ?? 'VVU Library'); ?></span>
                        </h2>
                        <div class="space-y-8">
                            <div class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                <?php echo $director['message'] ?? ''; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- About The VVU Libraries -->
    <section class="py-28 bg-white dark:bg-gray-900 about-libraries-section">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-fadeInUp">
                    <div class="section-badge bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                        <span class="material-symbols-outlined">local_library</span>
                        <?php echo strip_tags($aboutLibraries['about_badge'] ?? 'About Our Libraries'); ?>
                    </div>
                    <h2 class="section-title text-gray-900 dark:text-white">
                        <?php echo strip_tags($aboutLibraries['about_title'] ?? 'About The VVU Libraries'); ?>
                    </h2>
                </div>

                <!-- Main About Content -->
                <div class="about-card mb-12">
                    <div class="p-8 md:p-12">
                        <p class="about-text">
                            <?php echo $aboutLibraries['about_intro'] ?? ''; ?>
                        </p>
                    </div>
                </div>

                <!-- Images Grid -->
                <?php if (!empty($aboutLibraries['about_image_1']) || !empty($aboutLibraries['about_image_2'])): ?>
                <div class="about-images-grid mb-16">
                    <?php if (!empty($aboutLibraries['about_image_1'])): ?>
                    <img src="<?php echo strip_tags($aboutLibraries['about_image_1']); ?>" alt="VVU Library">
                    <?php endif; ?>
                    <?php if (!empty($aboutLibraries['about_image_2'])): ?>
                    <img src="<?php echo strip_tags($aboutLibraries['about_image_2']); ?>" alt="VVU Library Collection">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Branch Libraries -->
                <div class="mb-16">
                    <div class="text-center mb-10">
                        <div class="section-badge bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                            <span class="material-symbols-outlined">account_balance</span>
                            <?php echo strip_tags($branchLibraries['branch_title'] ?? 'Branch Libraries'); ?>
                        </div>
                        <div class="text-3xl text-gray-600 dark:text-gray-400 font-medium mt-4">
                            <?php echo strip_tags($branchLibraries['branch_intro'] ?? ''); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Branch 1 -->
                        <div class="branch-card">
                            <h4>
                                <span class="branch-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600">
                                    <span class="material-symbols-outlined">auto_stories</span>
                                </span>
                                <?php echo strip_tags($branchLibraries['branch_1_name'] ?? ''); ?>
                            </h4>
                            <p><?php echo strip_tags($branchLibraries['branch_1_desc'] ?? ''); ?></p>
                        </div>

                        <!-- Branch 2 -->
                        <div class="branch-card">
                            <h4>
                                <span class="branch-icon bg-green-100 dark:bg-green-900/40 text-green-600">
                                    <span class="material-symbols-outlined">apartment</span>
                                </span>
                                <?php echo strip_tags($branchLibraries['branch_2_name'] ?? ''); ?>
                            </h4>
                            <p><?php echo strip_tags($branchLibraries['branch_2_desc'] ?? ''); ?></p>
                        </div>

                        <!-- Branch 3 -->
                        <div class="branch-card">
                            <h4>
                                <span class="branch-icon bg-purple-100 dark:bg-purple-900/40 text-purple-600">
                                    <span class="material-symbols-outlined">school</span>
                                </span>
                                <?php echo strip_tags($branchLibraries['branch_3_name'] ?? ''); ?>
                            </h4>
                            <p><?php echo strip_tags($branchLibraries['branch_3_desc'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Library Plans & Development -->
    <section class="py-28 bg-gray-50 dark:bg-gray-950 plans-section">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-fadeInUp">
                    <div class="section-badge bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                        <span class="material-symbols-outlined">rocket_launch</span>
                        <?php echo strip_tags($libraryPlans['plans_badge'] ?? 'Future Plans'); ?>
                    </div>
                    <h2 class="section-title text-gray-900 dark:text-white">
                        <?php echo strip_tags($libraryPlans['plans_title'] ?? 'Library Development & Plans'); ?>
                    </h2>
                </div>

                <!-- New Building Plans -->
                <div class="plan-block">
                    <div class="flex items-start gap-5 mb-5">
                        <div class="w-16 h-16 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-blue-600 text-4xl">construction</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white pt-2"><?php echo strip_tags($libraryPlans['new_building_title'] ?? 'New Library Building'); ?></h3>
                    </div>
                    <div class="about-text ml-0 md:ml-[68px]">
                        <?php echo $libraryPlans['new_building_plans'] ?? ''; ?>
                    </div>
                </div>

                <!-- Automation/Digitization -->
                <div class="plan-block">
                    <div class="flex items-start gap-5 mb-5">
                        <div class="w-16 h-16 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-purple-600 text-4xl">smart_toy</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white pt-2"><?php echo strip_tags($libraryPlans['automation_title'] ?? 'Automation & Digitization'); ?></h3>
                    </div>
                    <div class="about-text ml-0 md:ml-[68px] mb-4">
                        <?php echo $libraryPlans['automation_intro'] ?? ''; ?>
                    </div>
                    <ul class="automation-list ml-0 md:ml-[68px]">
                        <li><?php echo strip_tags($libraryPlans['automation_point_1'] ?? ''); ?></li>
                        <li><?php echo strip_tags($libraryPlans['automation_point_2'] ?? ''); ?></li>
                        <li><?php echo strip_tags($libraryPlans['automation_point_3'] ?? ''); ?></li>
                    </ul>
                </div>

                <!-- Library Operations -->
                <div class="plan-block">
                    <div class="flex items-start gap-5 mb-5">
                        <div class="w-16 h-16 rounded-xl bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-teal-600 text-4xl">settings</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white pt-2"><?php echo strip_tags($libraryPlans['operations_title'] ?? 'Library Operations'); ?></h3>
                    </div>
                    <div class="about-text ml-0 md:ml-[68px]">
                        <?php echo $libraryPlans['library_operations'] ?? ''; ?>
                    </div>
                </div>

                <!-- Technology Plan -->
                <div class="plan-block">
                    <div class="flex items-start gap-5 mb-5">
                        <div class="w-16 h-16 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-amber-600 text-4xl">memory</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white pt-2"><?php echo strip_tags($libraryPlans['technology_title'] ?? 'Technology Plan'); ?></h3>
                    </div>
                    <div class="about-text ml-0 md:ml-[68px]">
                        <?php echo $libraryPlans['technology_plan'] ?? ''; ?>
                    </div>
                </div>

                <!-- Funding Plans -->
                <div class="plan-block">
                    <div class="flex items-start gap-5 mb-5">
                        <div class="w-16 h-16 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-green-600 text-4xl">payments</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white pt-2"><?php echo strip_tags($libraryPlans['funding_title'] ?? 'Funding & Infrastructure'); ?></h3>
                    </div>
                    <div class="about-text ml-0 md:ml-[68px]">
                        <?php echo $libraryPlans['funding_plans'] ?? ''; ?>
                    </div>
                </div>

                <!-- Closing Statement -->
                <div class="closing-statement mt-12 animate-fadeInUp">
                    <span class="material-symbols-outlined text-amber-500 text-5xl mb-4 animate-float" style="display:block;">church</span>
                    <h3><?php echo strip_tags($libraryPlans['closing_statement'] ?? ''); ?></h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="py-28 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8 px-4">
                    <div class="stat-card text-center p-8 lg:p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl shadow-xl">
                        <div class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-black text-blue-600 mb-4"><?php echo strip_tags($statsSection['stat1_val'] ?? ''); ?></div>
                        <div class="text-lg sm:text-xl lg:text-2xl text-gray-700 dark:text-gray-300 font-black uppercase tracking-wide"><?php echo strip_tags($statsSection['stat1_label'] ?? ''); ?></div>
                    </div>
                    <div class="stat-card text-center p-8 lg:p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl shadow-xl">
                        <div class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-black text-green-600 mb-4"><?php echo strip_tags($statsSection['stat2_val'] ?? ''); ?></div>
                        <div class="text-lg sm:text-xl lg:text-2xl text-gray-700 dark:text-gray-300 font-black uppercase tracking-wide"><?php echo strip_tags($statsSection['stat2_label'] ?? ''); ?></div>
                    </div>
                    <div class="stat-card text-center p-8 lg:p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl shadow-xl">
                        <div class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-black text-purple-600 mb-4"><?php echo strip_tags($statsSection['stat3_val'] ?? ''); ?></div>
                        <div class="text-lg sm:text-xl lg:text-2xl text-gray-700 dark:text-gray-300 font-black uppercase tracking-wide"><?php echo strip_tags($statsSection['stat3_label'] ?? ''); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
