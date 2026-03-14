<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// Online Resources content is also stored under page 53 (Library/Digital Books)
$page_id = 53;
$adminContent = new AdministrationContent($pdo);

$page_title = "Online Academic Resources - Valley View University";
$active_page = "academics";

// Fetch sections
$hero = $adminContent->getSectionFields($page_id, 'on_hero');
$general = $adminContent->getSectionFields($page_id, 'on_general_resources');
$health = $adminContent->getSectionFields($page_id, 'on_health_resources');
$science = $adminContent->getSectionFields($page_id, 'on_science_resources');
$arts = $adminContent->getSectionFields($page_id, 'on_arts_resources');

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
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }

    /* Hero */
    .on-hero {
        position: relative;
        min-height: 750px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        overflow: hidden;
    }
    .on-hero-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0.35;
    }
    .on-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 120px 20px;
        max-width: 1200px;
    }

    /* Category Blocks */
    .resource-section {
        padding: 140px 0;
    }
    .category-header {
        margin-bottom: 80px;
        text-align: center;
    }
    .category-title {
        font-size: clamp(4rem, 10vw, 6rem);
        font-weight: 1000;
        color: #1e293b;
        margin-bottom: 50px;
        position: relative;
        display: inline-block;
        letter-spacing: -0.01em;
        line-height: 1.3;
    }
    .dark .category-title { color: #f1f5f9; }
    .category-title::after {
        content: '';
        position: absolute;
        bottom: -35px;
        left: 50%;
        transform: translateX(-50%);
        width: 160px;
        height: 14px;
        background: #3b82f6;
        border-radius: 12px;
    }

    /* Grid layout from HTML content */
    .resource-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(460px, 1fr));
        gap: 60px;
    }
    .res-item {
        background: #fff;
        padding: 70px;
        border-radius: 48px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.07);
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .dark .res-item { background: #1e293b; border-color: rgba(255,255,255,0.08); }
    .res-item:hover {
        transform: translateY(-20px) scale(1.03);
        box-shadow: 0 45px 90px rgba(0,0,0,0.15);
        border-color: #3b82f670;
    }
    .res-item h4 {
        font-size: 2.5rem;
        font-weight: 1000;
        color: #1e293b;
        margin-bottom: 30px;
        line-height: 1.15;
    }
    .dark .res-item h4 { color: #f1f5f9; }
    .res-item p {
        font-size: 1.8rem;
        color: #334155;
        line-height: 1.9;
        margin-bottom: 50px;
        flex-grow: 1;
        font-weight: 500;
    }
    .dark .res-item p { color: #cbd5e1; }
    .res-item a {
        display: inline-flex;
        align-items: center;
        gap: 18px;
        color: #2563eb;
        font-weight: 1000;
        text-decoration: none;
        font-size: 1.8rem;
        transition: all 0.3s ease;
        padding: 12px 0;
    }
    .res-item a:hover { gap: 24px; color: #1d4ed8; text-decoration: underline; }
    .res-item a::after {
        content: '\e5c8';
        font-family: 'Material Symbols Outlined';
        font-size: 2.1rem;
    }

    /* Navigation */
    .back-nav {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 30px 0;
    }
    .dark .back-nav { background: #0f172a; border-color: #1e293b; }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        color: #64748b;
        text-decoration: none;
        font-weight: 900;
        font-size: 1.6rem;
        transition: color 0.3s;
    }
    .back-link:hover { color: #3b82f6; }

    @media (max-width: 768px) {
        .category-title { font-size: 3rem; }
        .res-item { padding: 50px; }
        .resource-grid { grid-template-columns: 1fr; }
        .on-hero { min-height: 550px; }
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="on-hero">
        <div class="on-hero-bg animate-slow-zoom" style="background-image: url('<?php echo strip_tags($hero['bg_image'] ?? 'uploads/Library/library-banner.jpg'); ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/40 to-slate-900/90"></div>
        <div class="on-hero-content animate-fadeInUp">
            <div class="inline-flex items-center gap-3 bg-blue-500/20 backdrop-blur-md px-8 py-4 rounded-full mb-10 border border-blue-500/40">
                <span class="material-symbols-outlined text-blue-400 text-3xl">public</span>
                <span class="text-blue-100 font-black text-sm uppercase tracking-[0.2em]">Global Academic Access</span>
            </div>
            <h1 class="text-6xl md:text-8xl font-black text-white mb-10 leading-[1.1] tracking-tighter">
                <?php echo strip_tags($hero['title'] ?? 'Online Resources'); ?>
            </h1>
            <div class="text-2xl md:text-4xl text-slate-200 font-bold max-w-4xl mx-auto leading-relaxed opacity-90">
                <?php echo strip_tags($hero['subtitle'] ?? ''); ?>
            </div>
        </div>
    </section>

    <!-- Breadcrumbs -->
    <nav class="back-nav">
        <div class="container">
            <div class="max-w-7xl mx-auto flex justify-between items-center px-4">
                <a href="digital_books.php" class="back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Digital Library
                </a>
                <?php if ($is_admin): ?>
                <a href="admin/edit_administration_page.php?id=<?php echo $page_id; ?>" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-blue-500 hover:to-indigo-500 transition-all shadow-lg hover:shadow-xl"
                   target="_blank">
                    <span class="material-symbols-outlined" style="font-size: 1.1rem;">edit</span>
                    Edit Page Content
                </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- General Resources -->
    <section class="resource-section bg-white dark:bg-slate-900">
        <div class="container">
            <div class="max-w-7xl mx-auto px-4">
                <div class="category-header">
                    <h2 class="category-title"><?php echo strip_tags($general['category_name'] ?? 'General Resources'); ?></h2>
                </div>
                <?php echo $general['content'] ?? ''; ?>
            </div>
        </div>
    </section>

    <!-- Health Resources -->
    <section class="resource-section bg-slate-50 dark:bg-slate-950">
        <div class="container">
            <div class="max-w-7xl mx-auto px-4">
                <div class="category-header">
                    <h2 class="category-title"><?php echo strip_tags($health['category_name'] ?? 'Health & Medical'); ?></h2>
                </div>
                <?php echo $health['content'] ?? ''; ?>
            </div>
        </div>
    </section>

    <!-- Science & Tech -->
    <section class="resource-section bg-white dark:bg-slate-900">
        <div class="container">
            <div class="max-w-7xl mx-auto px-4">
                <div class="category-header">
                    <h2 class="category-title"><?php echo strip_tags($science['category_name'] ?? 'Science & Technology'); ?></h2>
                </div>
                <?php echo $science['content'] ?? ''; ?>
            </div>
        </div>
    </section>

    <!-- Arts & Humanities -->
    <section class="resource-section bg-slate-50 dark:bg-slate-950">
        <div class="container">
            <div class="max-w-7xl mx-auto px-4">
                <div class="category-header">
                    <h2 class="category-title"><?php echo strip_tags($arts['category_name'] ?? 'Humanities & Social Science'); ?></h2>
                </div>
                <?php echo $arts['content'] ?? ''; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-blue-600">
        <div class="container text-center">
            <h3 class="text-3xl md:text-4xl font-black text-white mb-6">Need assistance with your research?</h3>
            <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">Our librarians are ready to help you navigate these resources and find exactly what you need.</p>
            <a href="contact_us.php" class="inline-flex items-center gap-3 bg-white text-blue-600 px-10 py-4 rounded-2xl font-black text-xl hover:bg-blue-50 transition-colors">
                <span class="material-symbols-outlined">support_agent</span>
                Contact a Librarian
            </a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
