<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';
require_once 'includes/ebook_access.php';
if (session_status() == PHP_SESSION_NONE) session_start();

// Digital Books content is stored under the Library Resources page (id=53)
$page_slug = 'library_resources';
$adminContent = new AdministrationContent($pdo);
$page_data = $adminContent->getPageBySlug($page_slug);

if (!$page_data) {
    include '404.php';
    exit;
}

$page_id = $page_data['id'];
$page_title = "Digital Books & Online Resources - Valley View University";
$active_page = "academics";

// Fetch digital books sections (db_ prefixed keys under page 53)
$hero = $adminContent->getSectionFields($page_id, 'db_hero');
$qrEbooks = $adminContent->getSectionFields($page_id, 'db_qr_ebooks');
$onlineResources = $adminContent->getSectionFields($page_id, 'db_online_resources');
$partnerLibraries = $adminContent->getSectionFields($page_id, 'db_partner_libraries');
$quickLinks = $adminContent->getSectionFields($page_id, 'db_quick_links');

$is_admin = isset($_SESSION['admin_id']);

// The e-books collection is shared with the student Google Workspace domain, so
// Google performs the sign-in check. Both the button and the QR code point at
// ebooks_access.php, which explains which account to use before handing over.
$ebooks_gate_url = vvu_ebook_gate_url('ebooks');
$ebooks_domain   = vvu_ebook_student_domain();

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
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59,130,246,0.15); }
        50% { box-shadow: 0 0 40px rgba(59,130,246,0.3); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }

    /* Hero Section */
    .db-hero {
        position: relative;
        min-height: 450px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0c4a6e 100%);
    }
    .db-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="p" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect fill="url(%23p)" width="100" height="100"/></svg>');
        opacity: 0.6;
    }
    .db-hero-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0.2;
    }
    .db-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 60px 20px;
        max-width: 1100px;
    }

    /* QR Section */
    .qr-section {
        position: relative;
        overflow: hidden;
    }
    .qr-card {
        background: linear-gradient(135deg, #fff 0%, #f0f9ff 100%);
        border-radius: 40px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0,0,0,0.1);
        border: 1px solid #e0f2fe;
    }
    .dark .qr-card {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        border-color: rgba(255,255,255,0.1);
    }
    .qr-image-container {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: #fff;
        border-radius: 32px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        animation: pulse-glow 3s ease-in-out infinite;
    }
    .dark .qr-image-container {
        background: #1e293b;
    }
    .qr-image-container img {
        max-width: 200px;
        width: 100%;
        height: auto;
        border-radius: 12px;
    }

    /* E-Books access status */
    .ebooks-status {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 22px;
        border-radius: 20px;
        border: 1px solid transparent;
        line-height: 1.6;
    }
    .ebooks-status .material-symbols-outlined { font-size: 1.8rem; flex-shrink: 0; }
    .ebooks-status strong {
        display: block;
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .ebooks-status span.ebooks-note, .ebooks-status div > span { font-size: 1.2rem; }
    .ebooks-status code {
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 700;
        background: rgba(59,130,246,0.12);
    }
    .ebooks-status a { font-weight: 800; text-decoration: underline; }
    .ebooks-status-lock {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1e40af;
    }
    .dark .ebooks-status-lock {
        background: rgba(59,130,246,0.12);
        border-color: rgba(59,130,246,0.3);
        color: #bfdbfe;
    }
    .ebooks-status-ok {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }
    .dark .ebooks-status-ok {
        background: rgba(16,185,129,0.12);
        border-color: rgba(16,185,129,0.3);
        color: #a7f3d0;
    }
    .ebooks-status-ok code { background: rgba(16,185,129,0.15); }

    /* Resource Cards */
    .resource-card {
        background: #fff;
        border-radius: 32px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .dark .resource-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .resource-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        border-radius: 24px 24px 0 0;
        transition: height 0.3s ease;
    }
    .resource-card.card-blue::before { background: linear-gradient(90deg, #3b82f6, #6366f1); }
    .resource-card.card-red::before { background: linear-gradient(90deg, #ef4444, #f97316); }
    .resource-card.card-green::before { background: linear-gradient(90deg, #10b981, #06b6d4); }
    .resource-card.card-purple::before { background: linear-gradient(90deg, #8b5cf6, #a855f7); }
    .resource-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }
    .resource-card:hover::before {
        height: 12px;
    }
    .resource-icon {
        width: 60px;
        height: 60px;
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        font-size: 2.6rem;
        flex-shrink: 0;
    }
    .resource-card h3 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 16px;
        line-height: 1.2;
    }
    .dark .resource-card h3 {
        color: #f1f5f9;
    }
    .resource-card p, .resource-card .prose {
        font-size: 1.65rem;
        line-height: 1.85;
        color: #475569;
        margin-bottom: 25px;
        flex-grow: 1;
    }
    .dark .resource-card p, .dark .resource-card .prose {
        color: #94a3b8;
    }
    .resource-btn {
        display: inline-flex;
        align-items: center;
        gap: 16px;
        padding: 16px 32px;
        border-radius: 20px;
        font-weight: 800;
        font-size: 1.25rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .resource-btn.btn-blue {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .resource-btn.btn-blue:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .resource-btn.btn-red {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .resource-btn.btn-red:hover {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
    }
    .resource-btn.btn-green {
        background: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }
    .resource-btn.btn-green:hover {
        background: #059669;
        color: #fff;
        border-color: #059669;
    }
    .resource-btn.btn-purple {
        background: #f5f3ff;
        color: #7c3aed;
        border-color: #c4b5fd;
    }
    .resource-btn.btn-purple:hover {
        background: #7c3aed;
        color: #fff;
        border-color: #7c3aed;
    }

    /* Partner Cards */
    .partner-card {
        background: #fff;
        border-radius: 32px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .dark .partner-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .partner-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.1);
    }
    .partner-card h4 {
        font-size: 2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 20px;
    }
    .dark .partner-card h4 {
        color: #f1f5f9;
    }
    .partner-card p, .partner-card .text-xl {
        font-size: 1.6rem !important;
        line-height: 1.8;
        color: #475569;
        flex-grow: 1;
        margin-bottom: 25px;
    }
    .dark .partner-card p, .dark .partner-card .text-xl {
        color: #94a3b8;
    }

    /* Quick Links Grid */
    .quick-link-card {
        background: #fff;
        border-radius: 24px;
        padding: 30px 20px;
        box-shadow: 0 6px 30px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 20px;
    }
    .dark .quick-link-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .quick-link-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        border-color: #3b82f640;
    }
    .quick-link-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 2rem;
    }
    .quick-link-card h5 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .dark .quick-link-card h5 {
        color: #f1f5f9;
    }
    .quick-link-card span.ql-desc {
        font-size: 1.25rem;
        color: #64748b;
        line-height: 1.6;
    }
    .dark .quick-link-card span.ql-desc {
        color: #94a3b8;
    }

    /* Section Headers */
    .db-section-badge {
        display: inline-flex;
        align-items: center;
        gap: 16px;
        padding: 12px 28px;
        border-radius: 60px;
        font-weight: 800;
        font-size: 1.15rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 16px;
    }
    .db-section-title {
        font-size: clamp(2.5rem, 5vw, 3.8rem);
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 16px;
    }
    .db-section-desc {
        font-size: 1.8rem;
        line-height: 1.8;
        max-width: 950px;
        margin: 0 auto;
        font-weight: 500;
    }

    /* CTA Button */
    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 16px;
        padding: 16px 36px;
        border-radius: 20px;
        font-weight: 900;
        font-size: 1.25rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .cta-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.2);
    }

    /* Back link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        color: #475569;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.5rem;
        transition: color 0.3s;
        padding: 15px 0;
    }
    .back-link:hover {
        color: #3b82f6;
    }
    .dark .back-link {
        color: #cbd5e1;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .resource-card { padding: 30px; }
        .resource-icon { width: 60px; height: 60px; font-size: 1.8rem; }
        .resource-card h3 { font-size: 1.6rem; }
        .resource-card p { font-size: 1.3rem; }
        .qr-image-container { padding: 30px; }
        .qr-image-container img { max-width: 140px; }
        .partner-card { padding: 28px; }
        .partner-card h4 { font-size: 1.7rem; }
        .partner-card p { font-size: 1.25rem; }
        .quick-link-card { padding: 22px 26px; }
        .quick-link-card h5 { font-size: 1.3rem; }
        .quick-link-card span.ql-desc { font-size: 1.1rem; }
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="db-hero">
        <div class="db-hero-bg animate-slow-zoom" 
             style="background-image: url('<?php echo strip_tags($hero['bg_image'] ?? 'uploads/Library/library-banner.jpg'); ?>');">
        </div>
        <div class="db-hero-content">
            <div class="animate-fadeInUp" style="animation-delay: 0.1s;">
                <div class="inline-flex items-center gap-4 bg-white/10 backdrop-blur-md px-8 py-4 rounded-full mb-10 border border-white/20">
                    <span class="material-symbols-outlined text-yellow-400 text-3xl">auto_stories</span>
                    <span class="text-white font-bold tracking-widest text-lg uppercase">VVU Digital Library</span>
                </div>
            </div>
            <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white leading-tight mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.2s;">
                <?php echo strip_tags($hero['title'] ?? 'Digital Books & Online Resources'); ?>
            </h1>
            <div class="text-lg sm:text-xl md:text-2xl text-white/90 max-w-5xl mx-auto animate-fadeInUp font-semibold leading-relaxed" style="animation-delay: 0.3s;">
                <?php echo strip_tags($hero['subtitle'] ?? ''); ?>
            </div>
            <div class="mt-14 animate-fadeInUp" style="animation-delay: 0.4s;">
                <a href="#ebooks" class="cta-btn bg-gradient-to-r from-yellow-400 to-amber-500 text-gray-900 hover:from-yellow-300 hover:to-amber-400" style="font-size: 1.3rem; padding: 16px 36px;">
                    <span class="material-symbols-outlined" style="font-size: 1.6rem;">download</span>
                    Get Started Now
                </a>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="container">
            <div class="max-w-7xl mx-auto py-4 px-4 flex items-center justify-between flex-wrap gap-3">
                <a href="library_resources.php" class="back-link">
                    <span class="material-symbols-outlined text-xl">arrow_back</span>
                    Back to Library Resources
                </a>
                <?php ?>
            </div>
        </div>
    </div>

    <!-- QR Code & E-Books Section -->
    <section id="ebooks" class="py-16 bg-white dark:bg-gray-900 qr-section">
        <div class="container">
            <div class="max-w-[1440px] mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-fadeInUp">
                    <div class="db-section-badge bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                        <span class="material-symbols-outlined">qr_code_2</span>
                        Mobile Access
                    </div>
                    <h2 class="db-section-title text-gray-900 dark:text-white">
                        <?php echo strip_tags($qrEbooks['section_title'] ?? 'VVU Library E-Books'); ?>
                    </h2>
                    <div class="db-section-desc text-gray-600 dark:text-gray-400">
                        <?php echo strip_tags($qrEbooks['section_description'] ?? ''); ?>
                    </div>
                </div>

                <!-- QR Card -->
                <div class="qr-card">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                        <!-- QR Code Side -->
                        <div class="flex items-center justify-center p-8 md:p-12 lg:p-16 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30">
                            <div class="qr-image-container animate-float">
                                <img src="ebooks_qr.php?r=ebooks"
                                     alt="Scan to open the E-Books Collection with your VVU student account">
                            </div>
                        </div>
                        <!-- Info Side -->
                        <div class="flex flex-col justify-center p-8 md:p-12 lg:p-16">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600" style="font-size: 2rem;">library_books</span>
                                </div>
                                <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">E-Books Collection</h3>
                            </div>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                                Scan the QR code with your mobile device camera, or use the button below. Google will ask you to sign in with your student account before the collection opens.
                            </p>
                            <div class="flex items-center gap-4 text-lg text-gray-500 dark:text-gray-500 mb-6">
                                <span class="material-symbols-outlined text-green-500" style="font-size: 1.25rem;">verified</span>
                                <span>Hosted on Google Drive • VVU Students Only • Updated Regularly</span>
                            </div>

                            <div class="ebooks-status ebooks-status-lock mb-8">
                                <span class="material-symbols-outlined">shield_person</span>
                                <div>
                                    <strong>Student sign-in required</strong>
                                    <span>Access is granted by Google, not by this website. Sign in with your
                                        <code>@<?php echo htmlspecialchars($ebooks_domain); ?></code> student account &mdash; a personal Gmail will be turned away.</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-4">
                                <a href="<?php echo htmlspecialchars($ebooks_gate_url); ?>"
                                   class="cta-btn bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-500 hover:to-indigo-500"
                                   style="font-size: 1.1rem; padding: 14px 28px;">
                                    <span class="material-symbols-outlined">open_in_new</span>
                                    <?php echo strip_tags($qrEbooks['button_text'] ?? 'Access E-Books Collection'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Online Resources & PDF Books -->
    <section class="py-16 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-[1440px] mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-fadeInUp">
                    <div class="db-section-badge bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                        <span class="material-symbols-outlined">cloud</span>
                        Digital Collections
                    </div>
                    <h2 class="db-section-title text-gray-900 dark:text-white">
                        Browse Our Digital Library
                    </h2>
                    <p class="db-section-desc text-gray-600 dark:text-gray-400">
                        Explore our comprehensive online resources and downloadable materials.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Online Resources Card -->
                    <div class="resource-card card-blue animate-fadeInUp" style="animation-delay: 0.1s;">
                        <div class="resource-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600">
                            <span class="material-symbols-outlined"><?php echo strip_tags($onlineResources['resource_1_icon'] ?? 'language'); ?></span>
                        </div>
                        <h3><?php echo strip_tags($onlineResources['resource_1_title'] ?? 'Online Resources'); ?></h3>
                        <div class="mb-8 prose prose-slate dark:prose-invert max-w-none">
                            <?php echo strip_tags($onlineResources['resource_1_description'] ?? ''); ?>
                        </div>
                        <div>
                            <a href="<?php echo strip_tags($onlineResources['resource_1_url'] ?? '#'); ?>" 
                               target="_blank" class="resource-btn btn-blue">
                                <span class="material-symbols-outlined">arrow_forward</span>
                                <?php echo strip_tags($onlineResources['resource_1_button_text'] ?? 'Explore Resources'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- PDF Books Card -->
                    <div class="resource-card card-red animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="resource-icon bg-red-100 dark:bg-red-900/40 text-red-600">
                            <span class="material-symbols-outlined"><?php echo strip_tags($onlineResources['resource_2_icon'] ?? 'picture_as_pdf'); ?></span>
                        </div>
                        <h3><?php echo strip_tags($onlineResources['resource_2_title'] ?? 'PDF Books'); ?></h3>
                        <div class="mb-8 prose prose-slate dark:prose-invert max-w-none">
                            <?php echo strip_tags($onlineResources['resource_2_description'] ?? ''); ?>
                        </div>
                        <div>
                            <a href="<?php echo strip_tags($onlineResources['resource_2_url'] ?? '#'); ?>" 
                               target="_blank" class="resource-btn btn-red">
                                <span class="material-symbols-outlined">arrow_forward</span>
                                <?php echo strip_tags($onlineResources['resource_2_button_text'] ?? 'Browse PDF Books'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Libraries -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-[1440px] mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-fadeInUp">
                    <div class="db-section-badge bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                        <span class="material-symbols-outlined"><?php echo strip_tags($partnerLibraries['section_icon'] ?? 'handshake'); ?></span>
                        <?php echo strip_tags($partnerLibraries['section_badge'] ?? 'Partner Institutions'); ?>
                    </div>
                    <h2 class="db-section-title text-gray-900 dark:text-white">
                        <?php echo strip_tags($partnerLibraries['section_title'] ?? 'Partner Libraries'); ?>
                    </h2>
                    <div class="db-section-desc text-gray-600 dark:text-gray-400">
                        <?php echo strip_tags($partnerLibraries['section_description'] ?? ''); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Babcock -->
                    <div class="partner-card animate-fadeInUp" style="animation-delay: 0.1s;">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-16 h-16 rounded-2xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-green-600" style="font-size: 1.6rem;"><?php echo strip_tags($partnerLibraries['partner_1_icon'] ?? 'school'); ?></span>
                            </div>
                            <h4><?php echo strip_tags($partnerLibraries['partner_1_title'] ?? 'Babcock University Library'); ?></h4>
                        </div>
                        <div class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                            <?php echo strip_tags($partnerLibraries['partner_1_description'] ?? ''); ?>
                        </div>
                        <a href="<?php echo strip_tags($partnerLibraries['partner_1_url'] ?? '#'); ?>" 
                           target="_blank" class="resource-btn btn-green" style="align-self: flex-start;">
                            <span class="material-symbols-outlined">open_in_new</span>
                            Visit Library
                        </a>
                    </div>

                    <!-- Leslie Hardinge -->
                    <div class="partner-card animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 1.6rem;"><?php echo strip_tags($partnerLibraries['partner_2_icon'] ?? 'local_library'); ?></span>
                            </div>
                            <h4><?php echo strip_tags($partnerLibraries['partner_2_title'] ?? 'Leslie Hardinge Library'); ?></h4>
                        </div>
                        <div class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                            <?php echo strip_tags($partnerLibraries['partner_2_description'] ?? ''); ?>
                        </div>
                        <a href="<?php echo strip_tags($partnerLibraries['partner_2_url'] ?? '#'); ?>" 
                           target="_blank" class="resource-btn btn-purple" style="align-self: flex-start;">
                            <span class="material-symbols-outlined">open_in_new</span>
                            Visit Library
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="py-16 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-[1440px] mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-12 animate-fadeInUp">
                    <div class="db-section-badge bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                        <span class="material-symbols-outlined"><?php echo strip_tags($quickLinks['section_icon'] ?? 'link'); ?></span>
                        <?php echo strip_tags($quickLinks['section_badge'] ?? 'Quick Access'); ?>
                    </div>
                    <h2 class="db-section-title text-gray-900 dark:text-white">
                        <?php echo strip_tags($quickLinks['section_title'] ?? 'Useful Links'); ?>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php 
                        $title = $quickLinks["link_{$i}_title"] ?? '';
                        $url = $quickLinks["link_{$i}_url"] ?? '#';
                        $icon = $quickLinks["link_{$i}_icon"] ?? 'link';
                        $desc = $quickLinks["link_{$i}_description"] ?? '';
                        if (empty($title)) continue;
                        $colors = ['bg-blue-100 dark:bg-blue-900/40 text-blue-600', 'bg-green-100 dark:bg-green-900/40 text-green-600', 'bg-purple-100 dark:bg-purple-900/40 text-purple-600', 'bg-amber-100 dark:bg-amber-900/40 text-amber-600'];
                        $color = $colors[$i - 1];
                        $target = (strpos($url, 'http') === 0) ? ' target="_blank"' : '';
                    ?>
                    <a href="<?php echo strip_tags($url); ?>"<?php echo $target; ?> class="quick-link-card animate-fadeInUp" style="animation-delay: <?php echo ($i * 0.1); ?>s;">
                        <div class="quick-link-icon <?php echo $color; ?>">
                            <span class="material-symbols-outlined"><?php echo strip_tags($icon); ?></span>
                        </div>
                        <div>
                            <h5><?php echo strip_tags($title); ?></h5>
                            <span class="ql-desc"><?php echo strip_tags($desc); ?></span>
                        </div>
                    </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
