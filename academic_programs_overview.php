<?php
$page_title = "Academic Programs Overview - Valley View University";
$active_page = "academics";
include 'includes/header.php';
require_once('includes/db_connect.php');

// Fetch Page Content from new centralized table
$content_stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'academic_programs'");
$content_stmt->execute();
$page_data = $content_stmt->fetch();

// Fetch Categories with program counts
$categories_stmt = $pdo->query("
    SELECT pc.*, (SELECT COUNT(*) FROM academic_programs ap WHERE ap.category_id = pc.id AND ap.is_active = 1) as program_count 
    FROM program_categories pc 
    ORDER BY pc.display_order ASC
");
$db_categories = $categories_stmt->fetchAll();

// Fetch All Programs
$programs_stmt = $pdo->query("
    SELECT ap.*, pc.name as category_name, pc.color_1, pc.color_2,
           pd.name as department_name, pd.icon as department_icon,
           pd.display_order as department_order
    FROM academic_programs ap
    JOIN program_categories pc ON ap.category_id = pc.id
    LEFT JOIN program_departments pd ON ap.department_id = pd.id AND pd.is_active = 1
    WHERE ap.is_active = 1
    ORDER BY ap.display_order ASC, ap.title ASC
");
$db_programs = $programs_stmt->fetchAll();

/*
 * Group the programmes as faculty/school -> department -> programmes so they are
 * listed under their department rather than in one flat grid. Units without
 * departments (e.g. the School of Graduate Studies) fall into a single
 * unnamed group that renders without a department heading.
 */
$programs_by_unit = [];
foreach ($db_programs as $program) {
    $unit = $program['category_name'];
    $department = $program['department_name'] ?: '';

    if (!isset($programs_by_unit[$unit])) {
        $programs_by_unit[$unit] = [
            'color_1'     => $program['color_1'],
            'color_2'     => $program['color_2'],
            'departments' => [],
        ];
    }
    if (!isset($programs_by_unit[$unit]['departments'][$department])) {
        $programs_by_unit[$unit]['departments'][$department] = [
            'icon'     => $program['department_icon'] ?: 'school',
            'order'    => $department === '' ? PHP_INT_MAX : (int)$program['department_order'],
            'programs' => [],
        ];
    }
    $programs_by_unit[$unit]['departments'][$department]['programs'][] = $program;
}

// Keep departments in their configured order, unnamed group last.
foreach ($programs_by_unit as &$unit_data) {
    uasort($unit_data['departments'], function ($a, $b) {
        return $a['order'] <=> $b['order'];
    });
}
unset($unit_data);

// Fetch Stats
$stats_stmt = $pdo->query("SELECT * FROM academic_programs_stats ORDER BY display_order ASC");
$db_stats = $stats_stmt->fetchAll();

// Map for quick access
$category_colors = [];
foreach ($db_categories as $cat) {
    $category_colors[$cat['name']] = [$cat['color_1'], $cat['color_2']];
}
?>


<style>
    /* ========================================
       ANIMATIONS
       ======================================== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.15); }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .animate-slow-zoom { animation: slowZoom 25s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.7s ease-out forwards; }
    .animate-fadeInDown { animation: fadeInDown 0.7s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    
    /* ========================================
       GLASSMORPHISM & EFFECTS
       ======================================== */
    .glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .dark .glass {
        background: rgba(17, 24, 39, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* ========================================
       HERO SECTION
       ======================================== */
    .hero-gradient {
        background: linear-gradient(135deg, 
            rgba(0, 33, 71, 0.9) 0%, 
            rgba(30, 64, 175, 0.7) 50%,
            rgba(17, 24, 39, 0.9) 100%);
    }
    
    .hero-h1 span {
        font-size: 0.6em;
        font-weight: bold;
        display: block;
        margin-top: 1rem;
        text-transform: none;
        letter-spacing: -0.05em;
        color: #fbbf24; /* Yellow-400 */
        background: linear-gradient(to right, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1;
    }
    
    /* ========================================
       SEARCH SECTION
       ======================================== */
    .search-container {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 2rem;
        box-shadow: 
            0 25px 50px -12px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.8) inset;
    }
    .dark .search-container {
        background: linear-gradient(145deg, #1f2937 0%, #111827 100%);
        box-shadow: 
            0 25px 50px -12px rgba(0, 0, 0, 0.4),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }
    
    .search-input {
        background: linear-gradient(145deg, #f8fafc 0%, #ffffff 100%);
        border: 2px solid #e2e8f0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }
    .dark .search-input {
        background: linear-gradient(145deg, #1f2937 0%, #0f172a 100%);
        border-color: #374151;
        color: #fff;
    }
    .dark .search-input:focus {
        border-color: #3b82f6;
    }
    
    /* ========================================
       CATEGORY CARDS
       ======================================== */
    .category-card {
        position: relative;
        border-radius: 1.5rem;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid transparent;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    .dark .category-card {
        background: linear-gradient(145deg, #1f2937 0%, #111827 100%);
    }
    .category-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1.5rem;
        padding: 2px;
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
        -webkit-mask: 
            linear-gradient(#fff 0 0) content-box, 
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .category-card:hover::before,
    .category-card.active::before {
        opacity: 1;
    }
    .category-card:hover,
    .category-card.active {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    .category-card.active {
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
    }
    .category-card.active .cat-icon,
    .category-card.active .cat-name,
    .category-card.active .cat-count {
        color: #fff !important;
    }
    
    .cat-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    .category-card:hover .cat-icon-wrapper {
        transform: scale(1.1) rotate(-5deg);
    }
    .category-card.active .cat-icon-wrapper {
        background: rgba(255, 255, 255, 0.25);
    }
    
    .cat-icon {
        font-size: 2.5rem;
        color: #fff;
    }
    
    .cat-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }
    .dark .cat-name {
        color: #f1f5f9;
    }
    
    .cat-count {
        font-size: 1.25rem;
        color: #64748b;
        font-weight: 700;
    }
    .dark .cat-count {
        color: #94a3b8;
    }
    
    /* ========================================
       FACULTY / SCHOOL AND DEPARTMENT GROUPING
       ======================================== */
    .unit-block + .unit-block {
        margin-top: 5rem;
    }
    .unit-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding-bottom: 1.25rem;
        margin-bottom: 2.5rem;
        border-bottom: 3px solid;
        border-image: linear-gradient(90deg, var(--cat-color-1), var(--cat-color-2)) 1;
    }
    .unit-name {
        font-size: clamp(1.75rem, 3vw, 2.5rem);
        font-weight: 900;
        line-height: 1.15;
        color: #0f172a;
        margin: 0;
    }
    .dark .unit-name { color: #f1f5f9; }
    .unit-count {
        flex-shrink: 0;
        margin-left: auto;
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-size: 0.95rem;
        font-weight: 800;
        white-space: nowrap;
        color: #fff;
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
    }
    .department-block + .department-block {
        margin-top: 3rem;
    }
    .department-header {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 1.5rem;
    }
    .department-icon {
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.9rem;
        font-size: 1.5rem;
        color: #fff;
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
    }
    .department-name {
        font-size: clamp(1.15rem, 2vw, 1.5rem);
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }
    .dark .department-name { color: #e2e8f0; }
    .department-count {
        flex-shrink: 0;
        min-width: 1.9rem;
        padding: 0.15rem 0.6rem;
        border-radius: 999px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 800;
        color: #475569;
        background: #f1f5f9;
    }
    .dark .department-count {
        color: #cbd5e1;
        background: rgba(255, 255, 255, 0.08);
    }
    @media (max-width: 640px) {
        .unit-header { flex-wrap: wrap; gap: 0.75rem; }
        .unit-count { margin-left: 0; }
    }

    /* ========================================
       PROGRAM CARDS
       ======================================== */
    /* ========================================
       PROGRAM CARDS (PREMIUM REDESIGN)
       ======================================== */
    .program-card {
        background: #ffffff;
        border-radius: 2rem;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        position: relative;
        z-index: 1;
    }
    .dark .program-card {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.05);
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
    }
    .program-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
        opacity: 0.03;
        border-radius: 0 0 0 100%;
        z-index: -1;
        transition: all 0.5s ease;
    }
    .program-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.12);
        border-color: var(--cat-color-1);
    }
    .program-card:hover::after {
        width: 100%;
        height: 100%;
        border-radius: 0;
        opacity: 0.05;
    }
    
    .program-card-header {
        position: relative;
        padding-bottom: 0;
    }

    .program-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.75rem;
        border-radius: 1.25rem;
        font-size: 1.1rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
        color: #fff;
        margin-bottom: 2rem;
    }
    
    .program-title {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        transition: color 0.3s ease;
        letter-spacing: -0.02em;
    }
    .dark .program-title {
        color: #f1f5f9;
    }
    
    .program-desc {
        font-size: 1.4rem;
        color: #64748b;
        line-height: 1.6;
        font-weight: 500;
        margin-bottom: 2rem;
    }
    .dark .program-desc {
        color: #94a3b8;
    }

    .program-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    .dark .program-meta {
        border-top-color: rgba(255, 255, 255, 0.05);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #475569;
    }
    .dark .meta-item {
        color: #cbd5e1;
    }
    .meta-item i {
        font-size: 1.6rem;
        color: var(--cat-color-1);
    }
    
    .btn-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        width: 100%;
        padding: 1.5rem;
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 800;
        font-size: 1.25rem;
        border-radius: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none;
        border: 1px solid transparent;
    }
    .dark .btn-view {
        background: #334155;
        color: #f8fafc;
    }
    .program-card:hover .btn-view {
        background: linear-gradient(135deg, var(--cat-color-1), var(--cat-color-2));
        color: #fff;
        transform: scale(1.02);
        box-shadow: 0 15px 30px -10px var(--cat-color-1);
    }
    .btn-view span {
        transition: transform 0.4s ease;
    }
    .btn-view:hover span {
        transform: translateX(5px);
    }
    
    /* ========================================
       SECTION HEADERS
       ======================================== */
    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.1));
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 2rem;
        margin-bottom: 1.5rem;
    }
    .section-badge span {
        font-size: 1rem;
        font-weight: 700;
        color: #2563eb;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .dark .section-badge {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.15));
    }
    .dark .section-badge span {
        color: #60a5fa;
    }
    
    .section-title {
        font-size: 4rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 1.5rem;
    }
    .dark .section-title {
        color: #f1f5f9;
    }
    
    .section-subtitle {
        font-size: 1.8rem;
        color: #64748b;
        font-weight: 500;
        max-width: 800px;
    }
    .dark .section-subtitle {
        color: #94a3b8;
    }
    
    /* ========================================
       RESPONSIVE ADJUSTMENTS
       ======================================== */
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.75rem;
        }
        .category-card {
            padding: 1rem;
        }
        .cat-icon-wrapper {
            width: 48px;
            height: 48px;
        }
        .cat-icon {
            font-size: 1.5rem;
        }
        .cat-name {
            font-size: 0.85rem;
        }
        .program-title {
            font-size: 1.1rem;
        }
    }
    
    /* View All Button */
    .btn-view-all {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        background: transparent;
        border: 2px solid #2563eb;
        color: #2563eb;
        font-weight: 700;
        font-size: 1.15rem;
        border-radius: 3rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .btn-view-all:hover {
        background: #2563eb;
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
    }
    .dark .btn-view-all {
        border-color: #60a5fa;
        color: #60a5fa;
    }
    .dark .btn-view-all:hover {
        background: #3b82f6;
        color: #fff;
    }
    
    /* Stats Section */
    .stat-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.4s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .dark .stat-card {
        background: linear-gradient(145deg, #1f2937 0%, #111827 100%);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    .stat-number {
        font-size: 5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .stat-label {
        font-size: 1.5rem;
        color: #64748b;
        font-weight: 700;
        margin-top: 1rem;
    }
    .dark .stat-label {
        color: #94a3b8;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[75vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?: 'images/home-2.jpg'); ?>" 
                 alt="VVU Academic Programs" class="w-full h-full object-cover animate-slow-zoom opacity-70">
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        
        <!-- Floating Shapes -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-10 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/3 right-20 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/3 w-24 h-24 bg-yellow-500/10 rounded-full blur-2xl animate-float" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge'] ?: 'Academic Excellence'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl hero-h1" style="animation-delay: 0.1s;">
                    <?php echo $page_data['hero_title']; ?>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_subtitle']); ?>"
                </p>
            </div>
        </div>
    </section>



    <!-- Popular Categories Section -->
    <section class="py-16 px-4">
        <div class="container max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <div class="section-badge mx-auto inline-flex">
                    <span class="material-symbols-outlined text-blue-500 text-lg">category</span>
                    <span>Popular Categories</span>
                </div>
                <h2 class="section-title">Browse By Category</h2>
                <p class="section-subtitle mx-auto">Click on any category to explore programs of your choice</p>
            </div>
            
            <div id="categoryGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <!-- All Categories Card -->
                <div class="category-card active" data-category="" 
                     style="--cat-color-1: #2563eb; --cat-color-2: #1d4ed8;">
                    <div class="cat-icon-wrapper">
                        <span class="material-symbols-outlined cat-icon">apps</span>
                    </div>
                    <h3 class="cat-name">All Programs</h3>
                    <p class="cat-count"><?php echo count($db_programs); ?> Programs</p>
                </div>
                
                <?php foreach ($db_categories as $cat): ?>
                <div class="category-card" data-category="<?php echo strip_tags($cat['name']); ?>"
                     style="--cat-color-1: <?php echo $cat['color_1']; ?>; --cat-color-2: <?php echo $cat['color_2']; ?>;">
                    <div class="cat-icon-wrapper">
                        <span class="material-symbols-outlined cat-icon"><?php echo $cat['icon']; ?></span>
                    </div>
                    <h3 class="cat-name"><?php echo strip_tags($cat['name']); ?></h3>
                    <p class="cat-count"><?php echo $cat['program_count']; ?> Programs</p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 px-4 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800">
        <div class="container max-w-6xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($db_stats as $stat): ?>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2"><?php echo strip_tags($stat['stat_value']); ?></div>
                    <div class="text-lg text-blue-200 font-semibold"><?php echo strip_tags($stat['stat_label']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Programs Grid Section -->
    <section class="py-16 px-4">
        <div class="container max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <div>
                    <div class="section-badge inline-flex">
                        <span class="material-symbols-outlined text-blue-500 text-lg">school</span>
                        <span>Our Programs</span>
                    </div>
                    <h2 class="section-title" id="programsTitle">All Programs</h2>
                    <p class="section-subtitle" id="programsCount">Showing <?php echo count($db_programs); ?> programs</p>
                </div>
            </div>
            
            <div id="programsGrid">
                <?php foreach ($programs_by_unit as $unit_name => $unit_data): ?>
                <?php
                    $unit_total = 0;
                    foreach ($unit_data['departments'] as $d) {
                        $unit_total += count($d['programs']);
                    }
                ?>
                <section class="unit-block" data-category="<?php echo strip_tags($unit_name); ?>"
                         style="--cat-color-1: <?php echo $unit_data['color_1']; ?>; --cat-color-2: <?php echo $unit_data['color_2']; ?>;">
                    <div class="unit-header">
                        <h3 class="unit-name"><?php echo strip_tags($unit_name); ?></h3>
                        <span class="unit-count"><?php echo $unit_total; ?> Programs</span>
                    </div>

                    <?php foreach ($unit_data['departments'] as $dept_name => $dept_data): ?>
                    <div class="department-block">
                        <?php if ($dept_name !== ''): ?>
                        <div class="department-header">
                            <span class="material-symbols-outlined department-icon"><?php echo strip_tags($dept_data['icon']); ?></span>
                            <h4 class="department-name"><?php echo strip_tags($dept_name); ?></h4>
                            <span class="department-count"><?php echo count($dept_data['programs']); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php foreach ($dept_data['programs'] as $program): ?>
                            <div class="program-card flex flex-col h-full"
                                 data-title="<?php echo strtolower(strip_tags($program['title'])); ?>"
                                 data-category="<?php echo strip_tags($program['category_name']); ?>"
                                 style="--cat-color-1: <?php echo $program['color_1']; ?>; --cat-color-2: <?php echo $program['color_2']; ?>;">
                                <div class="p-8 flex-grow">
                                    <div class="program-card-header">
                                        <span class="program-badge">
                                            <span class="material-symbols-outlined" style="font-size: 1rem;">school</span>
                                            <?php echo strip_tags($program['department_name'] ?: $program['category_name']); ?>
                                        </span>
                                    </div>
                                    <h3 class="program-title"><?php echo strip_tags($program['title']); ?></h3>
                                    <p class="program-desc line-clamp-3"><?php echo strip_tags($program['description']); ?></p>

                                    <div class="program-meta">
                                        <div class="meta-item">
                                            <i class="material-symbols-outlined">schedule</i>
                                            <?php echo strip_tags($program['duration']); ?>
                                        </div>
                                        <div class="meta-item">
                                            <i class="material-symbols-outlined">layers</i>
                                            <?php echo strip_tags($program['level']); ?>
                                        </div>
                                        <div class="meta-item">
                                            <i class="material-symbols-outlined">location_on</i>
                                            <?php echo strip_tags(explode(',', $program['campus'])[0]); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-8 pt-0">
                                    <a href="course_details.php?id=<?php echo $program['id']; ?>" class="btn-view">
                                        Explore Program
                                        <span class="material-symbols-outlined">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </section>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden py-20 text-center">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-5xl text-gray-400">search_off</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No programs found</h3>
                <p class="text-xl text-gray-500 font-medium mb-6">Try adjusting your search or filter criteria.</p>
                <button onclick="resetAllFilters()" class="btn-view-all">
                    <span class="material-symbols-outlined">restart_alt</span>
                    Reset Filters
                </button>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container max-w-4xl mx-auto relative z-10 text-center">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-white mb-6">
                <?php echo strip_tags($page_data['cta_title'] ?? 'Ready to Shape Your Future?'); ?>
            </h2>
            <p class="text-xl text-blue-200 mb-10 max-w-2xl mx-auto">
                <?php echo strip_tags($page_data['cta_subtitle'] ?? 'Join thousands of successful graduates who started their journey at Valley View University.'); ?>
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'admissions.php'); ?>" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 font-bold rounded-full transition-all hover:transform hover:-translate-y-1 hover:shadow-lg hover:shadow-yellow-500/30">
                    <span class="material-symbols-outlined">edit_note</span>
                    <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                </a>
                <a href="contact.php" class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-full border border-white/30 transition-all hover:bg-white/20 hover:transform hover:-translate-y-1">
                    <span class="material-symbols-outlined">mail</span>
                    Contact Us
                </a>
            </div>
        </div>
    </section>
</main>

<script>
    const categoryCards = document.querySelectorAll('.category-card');
    const programsGrid = document.getElementById('programsGrid');
    const programCards = programsGrid.querySelectorAll('.program-card');
    const noResults = document.getElementById('noResults');
    const programsTitle = document.getElementById('programsTitle');
    const programsCount = document.getElementById('programsCount');

    let selectedCategory = '';

    const unitBlocks = programsGrid.querySelectorAll('.unit-block');

    function filterPrograms() {
        let visibleCount = 0;

        programCards.forEach(card => {
            const category = card.getAttribute('data-category');
            const matchesCategory = selectedCategory === '' || category === selectedCategory;

            if (matchesCategory) {
                card.style.display = 'flex';
                card.style.animation = 'fadeInUp 0.5s ease forwards';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Hide any department group or faculty/school heading left with no cards.
        unitBlocks.forEach(unit => {
            let unitVisible = 0;

            unit.querySelectorAll('.department-block').forEach(group => {
                const shown = group.querySelectorAll('.program-card:not([style*="display: none"])').length;
                group.style.display = shown === 0 ? 'none' : '';
                unitVisible += shown;
            });

            unit.style.display = unitVisible === 0 ? 'none' : '';
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        programsGrid.style.display = visibleCount === 0 ? 'none' : '';

        // Update title and count
        if (selectedCategory) {
            programsTitle.textContent = selectedCategory;
        } else {
            programsTitle.textContent = 'All Programs';
        }
        programsCount.textContent = `Showing ${visibleCount} program${visibleCount !== 1 ? 's' : ''}`;
    }

    function resetAllFilters() {
        selectedCategory = '';
        categoryCards.forEach(card => {
            card.classList.remove('active');
            if (card.getAttribute('data-category') === '') {
                card.classList.add('active');
            }
        });
        filterPrograms();
    }

    // Category card click handler
    categoryCards.forEach(card => {
        card.addEventListener('click', () => {
            categoryCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            selectedCategory = card.getAttribute('data-category');
            filterPrograms();
            
            // Smooth scroll to programs section
            document.getElementById('programsGrid').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        });
    });


</script>

<?php
include 'includes/footer.php';
?>