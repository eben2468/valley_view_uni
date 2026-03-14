<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

$page_slug = 'pdf_books';
$adminContent = new AdministrationContent($pdo);
$page_data = $adminContent->getPageBySlug($page_slug);

if (!$page_data) {
    include '404.php';
    exit;
}

$page_id = $page_data['id'];
$page_title = "PDF Books Collection - Valley View University";
$active_page = "academics";

// Fetch sections
$hero = $adminContent->getSectionFields($page_id, 'hero_section');
$intro = $adminContent->getSectionFields($page_id, 'intro_section');
$accounting = $adminContent->getSectionFields($page_id, 'accounting_books');
$nursing = $adminContent->getSectionFields($page_id, 'nursing_books');

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
        min-height: 650px;
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
        padding: 100px 20px;
        max-width: 1200px;
    }

    /* Category Blocks */
    .resource-section {
        padding: 120px 0;
    }
    .category-header {
        margin-bottom: 70px;
        text-align: center;
    }
    .category-title {
        font-size: clamp(3.5rem, 8vw, 5.5rem);
        font-weight: 900;
        color: #1e293b;
        margin-bottom: 40px;
        position: relative;
        display: inline-block;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .dark .category-title { color: #f1f5f9; }
    .category-title::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 120px;
        height: 8px;
        background: #ef4444;
        border-radius: 8px;
    }
    
    .nursing-section .category-title::after {
        background: #10b981;
    }

    /* Grid layout from HTML content */
    .resource-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 40px;
    }
    .res-item {
        background: #fff;
        padding: 30px;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    .dark .res-item { background: #1e293b; border-color: rgba(255,255,255,0.08); }
    .res-item:hover {
        transform: translateY(-12px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.12);
        border-color: #ef444440;
    }
    .nursing-section .res-item:hover {
        border-color: #10b98140;
    }
    
    .book-cover {
        width: 100%;
        aspect-ratio: 3/4;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 25px;
        background: #f8fafc;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        position: relative;
    }
    .book-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
        background: #f1f5f9;
        display: block;
    }
    .res-item:hover .book-cover img {
        transform: scale(1.1);
    }
    
    /* Placeholder for broken images */
    .book-cover.broken::after {
        content: '\e80d'; /* auto_stories icon */
        font-family: 'Material Symbols Outlined';
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        background: #f1f5f9;
        color: #cbd5e1;
        z-index: 1;
    }
    
    .res-item h4 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 20px;
        line-height: 1.3;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .dark .res-item h4 { color: #f1f5f9; }
    
    .res-item a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        color: #ef4444;
        font-weight: 800;
        text-decoration: none;
        font-size: 1.4rem;
        transition: all 0.3s ease;
        padding: 12px 24px;
        background: #fef2f2;
        border-radius: 12px;
    }
    .dark .res-item a { background: #451a1a; color: #fecaca; }
    
    .nursing-section .res-item a {
        color: #10b981;
        background: #ecfdf5;
    }
    .dark .nursing-section .res-item a { background: #064e3b; color: #a7f3d0; }

    .res-item a:hover { 
        transform: translateX(5px);
        background: #fee2e2;
    }
    .nursing-section .res-item a:hover {
        background: #d1fae5;
    }
    
    .res-item a::after {
        content: '\e2c4'; /* download icon */
        font-family: 'Material Symbols Outlined';
        font-size: 1.8rem;
    }

    /* Navigation */
    .back-nav {
        background: #fff;
        border-bottom: 1px solid #e2e8e0;
        padding: 20px 0;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .dark .back-nav { background: #0f172a; border-color: #1e293b; }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #64748b;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.4rem;
        transition: color 0.3s;
    }
    .back-link:hover { color: #ef4444; }

    .intro-section {
        padding: 100px 0 60px;
        text-align: center;
    }
    .badge-label {
        display: inline-block;
        padding: 10px 24px;
        background: #fff1f2;
        color: #ef4444;
        border-radius: 100px;
        font-weight: 900;
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 30px;
    }
    .dark .badge-label { background: #451a1a; color: #fecaca; }

    @media (max-width: 768px) {
        .category-title { font-size: 2.8rem; }
        .res-item { padding: 30px; }
        .resource-grid { grid-template-columns: 1fr; }
        .on-hero { min-height: 450px; }
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="on-hero">
        <div class="on-hero-bg animate-slow-zoom" style="background-image: url('<?php echo strip_tags($hero['bg_image'] ?? 'uploads/Library/library-banner.jpg'); ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/40 to-slate-900/90"></div>
        <div class="on-hero-content animate-fadeInUp">
            <div class="inline-flex items-center gap-3 bg-red-500/20 backdrop-blur-md px-8 py-4 rounded-full mb-10 border border-red-500/40">
                <span class="material-symbols-outlined text-red-400 text-3xl">picture_as_pdf</span>
                <span class="text-red-100 font-black text-sm uppercase tracking-[0.2em]">Digital Library Archive</span>
            </div>
            <h1 class="text-5xl md:text-8xl font-black text-white mb-8 leading-[1.1] tracking-tighter">
                <?php echo strip_tags($hero['title'] ?? 'PDF Books Collection'); ?>
            </h1>
            <div class="text-xl md:text-3xl text-slate-200 font-bold max-w-4xl mx-auto leading-relaxed opacity-90">
                <?php echo strip_tags($hero['subtitle'] ?? ''); ?>
            </div>
        </div>
    </section>

    <!-- Navigation Bar -->
    <nav class="back-nav shadow-sm">
        <div class="container">
            <div class="max-w-7xl mx-auto flex justify-between items-center px-4">
                <div class="flex items-center gap-6">
                    <a href="digital_books.php" class="back-link">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Back to Digital Library
                    </a>
                    <div class="hidden md:flex items-center gap-6 border-l border-gray-200 dark:border-gray-800 pl-6">
                        <a href="#accounting" class="text-lg font-bold text-gray-500 hover:text-red-500 transition-colors">Accounting</a>
                        <a href="#nursing" class="text-lg font-bold text-gray-500 hover:text-green-500 transition-colors">Nursing</a>
                    </div>
                </div>
                
                <?php if ($is_admin): ?>
                <a href="admin/edit_administration_page.php?id=<?php echo $page_id; ?>" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl font-bold text-sm hover:from-red-500 hover:to-rose-500 transition-all shadow-lg hover:shadow-xl"
                   target="_blank">
                    <span class="material-symbols-outlined" style="font-size: 1.1rem;">edit</span>
                    Edit Page Content
                </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Intro Section -->
    <section class="intro-section bg-white dark:bg-slate-900">
        <div class="container">
            <div class="max-w-5xl mx-auto px-4">
                <div class="badge-label"><?php echo strip_tags($intro['badge_text'] ?? 'E-Books'); ?></div>
                <h2 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-white mb-8">
                    <?php echo strip_tags($intro['main_title'] ?? 'Browse Knowledge'); ?>
                </h2>
                <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                    <?php echo strip_tags($intro['description'] ?? ''); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Accounting Books -->
    <section id="accounting" class="resource-section bg-slate-50 dark:bg-slate-950">
        <div class="container">
            <div class="max-w-7xl mx-auto px-4">
                <div class="category-header">
                    <h2 class="category-title"><?php echo strip_tags($accounting['category_name'] ?? 'Accounting E-Books'); ?></h2>
                </div>
                <div class="animate-fadeInUp">
                    <?php echo $accounting['content'] ?? ''; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Nursing Books -->
    <section id="nursing" class="resource-section nursing-section bg-white dark:bg-slate-900">
        <div class="container">
            <div class="max-w-7xl mx-auto px-4">
                <div class="category-header">
                    <h2 class="category-title"><?php echo strip_tags($nursing['category_name'] ?? 'Nursing E-Books'); ?></h2>
                </div>
                <div class="animate-fadeInUp">
                    <?php echo $nursing['content'] ?? ''; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 bg-gradient-to-br from-slate-900 to-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ef4444 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="container text-center relative z-10">
            <h3 class="text-4xl md:text-6xl font-black text-white mb-8">Need more resources?</h3>
            <p class="text-xl md:text-2xl text-slate-400 mb-12 max-w-2xl mx-auto font-medium">Explore our full digital library for thousands of journals, research papers and online databases.</p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="online_resources.php" class="inline-flex items-center gap-3 bg-red-600 text-white px-10 py-5 rounded-2xl font-black text-xl hover:bg-red-500 transition-all shadow-xl hover:shadow-red-500/20">
                    <span class="material-symbols-outlined">language</span>
                    Online Resources
                </a>
                <a href="contact_us.php" class="inline-flex items-center gap-3 bg-slate-700 text-white px-10 py-5 rounded-2xl font-black text-xl hover:bg-slate-600 transition-all border border-slate-600">
                    <span class="material-symbols-outlined">support_agent</span>
                    Contact Librarian
                </a>
            </div>
        </div>
    </section>
</main>

<script>
document.querySelectorAll('.book-cover img').forEach(img => {
    img.onerror = function() {
        this.parentElement.classList.add('broken');
        this.style.display = 'none';
    };
});
</script>

<?php include 'includes/footer.php'; ?>
