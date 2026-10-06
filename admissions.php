<?php
/**
 * Valley View University - Admissions Page
 * Modern, responsive admissions page with database integration
 */

require_once('includes/db_connect.php');
require_once('includes/news_helpers.php');

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

// Plain text from admin-entered content, safe for HTML output
function ad_t($s) {
    return htmlspecialchars(strip_tags((string) $s), ENT_QUOTES, 'UTF-8');
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="css/vvu-modern.css?v=1.0">
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

    /* ==== Admissions page (below the hero) — builds on css/vvu-modern.css ==== */

    /* Stats strip overlapping the hero */
    .ad-stats {
        display: grid; grid-template-columns: repeat(var(--ad-cols, 4), minmax(0, 1fr));
        max-width: 1040px; margin: 0 auto; overflow: hidden;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 18px;
        box-shadow: 0 28px 50px -30px rgba(15, 23, 42, .45);
    }
    .ad-stat {
        display: flex; flex-direction: column; align-items: center; text-align: center;
        padding: 24px 14px 22px; border-left: 1px solid #eef1f5;
    }
    .ad-stat:first-child { border-left: 0; }
    .ad-stat-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 42px; height: 42px; margin-bottom: 10px; border-radius: 50%;
        background: rgba(30, 58, 138, .08);
    }
    .ad-stat-icon .material-symbols-outlined { font-size: 22px; color: #1e3a8a; }
    .ad-stat-value { color: #1e3a8a; font-size: 30px; font-weight: 700; line-height: 1.1; }
    .ad-stat-label {
        margin-top: 6px; color: #6b7280; font-size: 12px; font-weight: 700;
        letter-spacing: .14em; text-transform: uppercase;
    }
    .dark .ad-stats { background: #1f2937; border-color: #374151; }
    .dark .ad-stat { border-color: #374151; }
    .dark .ad-stat-value { color: #fff; }

    /* Get in touch + notice board */
    .ad-touch { display: grid; grid-template-columns: minmax(0, 5fr) minmax(0, 7fr); gap: 32px; align-items: start; }
    .ad-contact {
        position: relative; overflow: hidden; padding: 34px 32px; border-radius: 20px;
        background: linear-gradient(140deg, #1e3a8a 0%, #172554 100%); color: #fff;
    }
    .ad-contact::after {
        content: ""; position: absolute; width: 260px; height: 260px; right: -110px; top: -110px;
        border-radius: 50%; background: rgba(255, 255, 255, .06); pointer-events: none;
    }
    .ad-contact > * { position: relative; z-index: 1; }
    .ad-contact .vm-kicker { color: #fbbf24; margin-bottom: 10px; }
    .ad-contact-title {
        color: #fff; font-family: var(--vvu-title-font); font-weight: var(--vvu-title-weight);
        font-size: 30px; line-height: 1.2;
    }
    .ad-contact-sub { margin: 6px 0 0; color: rgba(255, 255, 255, .7); font-size: 15px; font-weight: 400; }
    .ad-contact-text { margin: 18px 0 0; color: rgba(255, 255, 255, .88); font-size: 16px; font-weight: 400; line-height: 1.65; }
    .ad-contact-rows { display: grid; gap: 10px; margin-top: 22px; }
    .ad-contact-row {
        display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 12px;
        background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .14);
        text-decoration: none !important; transition: background .25s ease;
    }
    .ad-contact-row:hover { background: rgba(255, 255, 255, .14); }
    .ad-contact-row .material-symbols-outlined { font-size: 20px; color: #fbbf24; }
    .ad-contact-val { color: #fff; font-size: 15px; font-weight: 600; overflow-wrap: anywhere; }
    .ad-contact-btn { margin-top: 22px; width: 100%; justify-content: center; }

    .ad-notices-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
    .ad-notices-head .vm-kicker { margin-bottom: 6px; }
    .ad-notices-title {
        color: #1e3a8a; font-family: var(--vvu-title-font); font-weight: var(--vvu-title-weight);
        font-size: 30px; line-height: 1.2;
    }
    .ad-notice {
        display: flex; align-items: center; gap: 18px; margin-bottom: 12px; padding: 16px 18px;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        text-decoration: none !important; transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }
    .ad-notice:hover { border-color: #1e3a8a; transform: translateX(4px); box-shadow: 0 14px 30px -24px rgba(15, 23, 42, .5); }
    .ad-date {
        flex: 0 0 64px; display: flex; flex-direction: column; align-items: center; justify-content: center;
        height: 64px; border-radius: 12px; background: #1e3a8a;
    }
    .ad-date-day { color: #fbbf24; font-size: 22px; font-weight: 700; line-height: 1; }
    .ad-date-mon { margin-top: 4px; color: rgba(255, 255, 255, .85); font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .ad-notice-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
    .ad-notice-title {
        color: #172554; font-size: 16px; font-weight: 600; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ad-notice-text {
        color: #6b7280; font-size: 14px; font-weight: 400; line-height: 1.55;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ad-notice-go { flex: 0 0 auto; font-size: 24px; color: #b45309; transition: transform .25s ease; }
    .ad-notice:hover .ad-notice-go { transform: translateX(3px); }
    .ad-notice--empty { color: #6b7280; }
    .ad-notice--empty:hover { transform: none; border-color: #e5e7eb; box-shadow: none; }
    .ad-notice--empty .material-symbols-outlined { font-size: 26px; color: #9ca3af; }
    .dark .ad-notices-title { color: #fff; }
    .dark .ad-notice { background: #1f2937; border-color: #374151; }
    .dark .ad-notice-title { color: #f3f4f6; }
    .dark .ad-notice-text { color: #9ca3af; }

    /* Why choose: compact corner cards */
    .ad-why .vm-card--corner { padding: 26px 24px 96px; }
    .ad-why .vm-card-title { font-size: 19px; font-weight: 600; }
    .ad-why .vm-card-text { font-size: 15.5px; }
    .ad-why .vm-card-corner { width: 84px; height: 84px; padding: 0 16px 16px 0; }
    .ad-why .vm-card-corner .material-symbols-outlined { font-size: 32px; width: 32px; height: 32px; }

    /* Featured programmes */
    .ad-prog {
        display: flex; flex-direction: column; overflow: hidden;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 20px;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        text-decoration: none !important; transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
    }
    .ad-prog:hover { transform: translateY(-6px); border-color: #c7d2fe; box-shadow: 0 30px 54px -30px rgba(15, 23, 42, .55); }
    .ad-prog-media { position: relative; display: block; height: 210px; overflow: hidden; background: #e5e7eb; }
    .ad-prog-media img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .7s ease; }
    .ad-prog:hover .ad-prog-media img { transform: scale(1.07); }
    /* Fade at the bottom so the faculty line reads over any photo */
    .ad-prog-media::after {
        content: ""; position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, .78) 0%, rgba(15, 23, 42, .1) 50%, rgba(15, 23, 42, 0) 70%);
    }
    .ad-prog-chip {
        position: absolute; z-index: 1; top: 14px; left: 14px; padding: 5px 12px; border-radius: 999px;
        background: rgba(255, 255, 255, .94); color: #1e3a8a; font-size: 11.5px; font-weight: 700;
        letter-spacing: .08em; text-transform: uppercase; box-shadow: 0 6px 14px -8px rgba(15, 23, 42, .6);
    }
    .ad-prog-faculty {
        position: absolute; z-index: 1; left: 18px; right: 84px; bottom: 14px;
        display: flex; align-items: center; gap: 6px;
        color: #fff; font-size: 13px; font-weight: 600; line-height: 1.35;
    }
    .ad-prog-faculty .material-symbols-outlined { font-size: 17px; color: #fbbf24; flex: 0 0 auto; }
    .ad-prog-body { position: relative; flex: 1; display: flex; flex-direction: column; padding: 22px 22px 20px; }
    /* Icon badge sitting on the photo's lower edge */
    .ad-prog-badge {
        position: absolute; top: -28px; right: 20px; display: flex; align-items: center; justify-content: center;
        width: 56px; height: 56px; border-radius: 50%; background: #1e3a8a;
        border: 4px solid #fff; box-shadow: 0 10px 20px -10px rgba(15, 23, 42, .6);
        transition: background .3s ease, transform .3s ease;
    }
    .ad-prog-badge .material-symbols-outlined { font-size: 24px; color: #fbbf24; transition: color .3s ease; }
    .ad-prog:hover .ad-prog-badge { background: #fbbf24; transform: rotate(-8deg); }
    .ad-prog:hover .ad-prog-badge .material-symbols-outlined { color: #172554; }
    .ad-prog-title { padding-right: 56px; color: #172554; font-size: 19px; font-weight: 600; line-height: 1.3; }
    .ad-prog-text {
        margin: 10px 0 18px; color: #6b7280; font-size: 15px; font-weight: 400; line-height: 1.6;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ad-prog-foot {
        margin-top: auto; padding-top: 16px; display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid #eef1f5;
    }
    .ad-prog-cta { color: #1e3a8a; font-size: 14.5px; font-weight: 700; }
    .ad-prog-go {
        display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%;
        background: rgba(30, 58, 138, .08); transition: background .3s ease, transform .3s ease;
    }
    .ad-prog-go .material-symbols-outlined { font-size: 19px; color: #1e3a8a; transition: color .3s ease; }
    .ad-prog:hover .ad-prog-go { background: #1e3a8a; transform: translateX(3px); }
    .ad-prog:hover .ad-prog-go .material-symbols-outlined { color: #fff; }
    .dark .ad-prog { background: #1f2937; border-color: #374151; }
    .dark .ad-prog-badge { border-color: #1f2937; }
    .dark .ad-prog-title { color: #f3f4f6; }
    .dark .ad-prog-text { color: #9ca3af; }
    .dark .ad-prog-foot { border-color: #374151; }
    .dark .ad-prog-cta { color: #93c5fd; }

    /* Requirements: navy header + checklist */
    .ad-req {
        display: flex; flex-direction: column; overflow: hidden;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 20px;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .ad-req:hover { transform: translateY(-6px); box-shadow: 0 30px 54px -30px rgba(15, 23, 42, .55); }
    .ad-req-head {
        position: relative; overflow: hidden; display: flex; align-items: center; gap: 14px;
        padding: 22px 22px; background: linear-gradient(135deg, #1e3a8a, #172554);
    }
    .ad-req-head::after {
        content: ""; position: absolute; right: -40px; top: -60px; width: 150px; height: 150px;
        border-radius: 50%; background: rgba(255, 255, 255, .06);
    }
    .ad-req-icon {
        position: relative; z-index: 1; flex: 0 0 auto; display: flex; align-items: center; justify-content: center;
        width: 48px; height: 48px; border-radius: 14px; background: #fbbf24;
    }
    .ad-req-icon .material-symbols-outlined { font-size: 26px; color: #172554; }
    .ad-req-head > div { position: relative; z-index: 1; }
    .ad-req-title { color: #fff; font-size: 20px; font-weight: 600; line-height: 1.25; }
    .ad-req-sub { display: block; margin-top: 3px; color: #fcd34d; font-size: 13px; font-weight: 600; }
    .ad-req-list { list-style: none; margin: 0; padding: 20px 22px 6px; display: grid; gap: 12px; }
    .ad-req-list li { display: flex; align-items: flex-start; gap: 10px; margin: 0; }
    .ad-req-list .material-symbols-outlined {
        flex: 0 0 auto; margin-top: 1px; font-size: 20px; color: #b45309;
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .ad-req-point { color: #4b5563; font-size: 15px; font-weight: 400; line-height: 1.6; }
    .ad-req-foot {
        margin: auto 22px 0; padding: 14px 0 18px; border-top: 1px solid #eef1f5;
        display: inline-flex; align-items: center; gap: 6px;
        color: #1e3a8a !important; font-size: 14.5px; font-weight: 700; text-decoration: none !important;
    }
    .ad-req-foot .material-symbols-outlined { font-size: 19px; color: inherit; transition: transform .3s ease; }
    .ad-req-foot:hover .material-symbols-outlined { transform: translateX(4px); }
    .dark .ad-req { background: #1f2937; border-color: #374151; }
    .dark .ad-req-point { color: #d1d5db; }
    .dark .ad-req-foot { color: #93c5fd !important; border-color: #374151; }

    /* Admission process steps on the navy band */
    .ad-process { padding: 72px 0 68px; }
    .ad-steps {
        position: relative; list-style: none; margin: 44px auto 0; padding: 0; max-width: 1160px;
        display: grid; grid-template-columns: repeat(var(--ad-cols, 4), minmax(0, 1fr)); gap: 22px;
    }
    /* Connector through the number circles */
    .ad-steps::before {
        content: ""; position: absolute; top: 24px; left: 12%; right: 12%; height: 2px;
        background: repeating-linear-gradient(90deg, rgba(251, 191, 36, .7) 0 8px, transparent 8px 16px);
    }
    .ad-step { position: relative; margin: 0; display: flex; flex-direction: column; align-items: center; }
    .ad-step-num {
        position: relative; z-index: 1; display: flex; align-items: center; justify-content: center;
        width: 50px; height: 50px; border-radius: 50%; background: #fbbf24; color: #172554;
        font-size: 18px; font-weight: 700; box-shadow: 0 0 0 6px rgba(251, 191, 36, .18);
    }
    .ad-step-card {
        flex: 1; width: 100%; margin-top: 18px; padding: 22px 20px; text-align: center;
        background: rgba(255, 255, 255, .07); border: 1px solid rgba(255, 255, 255, .14); border-radius: 16px;
        transition: background .3s ease, transform .3s ease;
    }
    .ad-step:hover .ad-step-card { background: rgba(255, 255, 255, .11); transform: translateY(-4px); }
    .ad-step-icon { font-size: 28px; color: #fbbf24; }
    .ad-step-title { margin-top: 8px; color: #fff; font-size: 18px; font-weight: 600; line-height: 1.3; }
    .ad-step-text { margin: 8px 0 0; color: rgba(255, 255, 255, .78); font-size: 14.5px; font-weight: 400; line-height: 1.6; }

    /* Latest news: featured story + compact list */
    .ad-news { display: grid; grid-template-columns: minmax(0, 6fr) minmax(0, 5fr); gap: 24px; align-items: stretch; }
    .ad-news--single { grid-template-columns: minmax(0, 1fr); max-width: 760px; }
    .ad-lead {
        position: relative; display: flex; align-items: flex-end; min-height: 440px; overflow: hidden;
        border-radius: 22px; background: #172554; text-decoration: none !important;
        box-shadow: 0 26px 50px -30px rgba(15, 23, 42, .6);
    }
    .ad-lead img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .8s ease; }
    .ad-lead::after {
        content: ""; position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, .92) 0%, rgba(15, 23, 42, .55) 45%, rgba(15, 23, 42, .05) 80%);
    }
    .ad-lead:hover img { transform: scale(1.05); }
    .ad-lead-body { position: relative; z-index: 1; display: flex; flex-direction: column; padding: 28px 28px 26px; }
    .ad-lead-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 12px; }
    .ad-lead .ad-news-chip { margin: 0; background: #fbbf24; color: #172554; }
    .ad-lead-date { display: inline-flex; align-items: center; gap: 6px; color: rgba(255, 255, 255, .8); font-size: 13px; font-weight: 500; }
    .ad-lead-date .material-symbols-outlined { font-size: 16px; color: #fbbf24; }
    .ad-lead-title {
        color: #fff; font-size: 24px; font-weight: 600; line-height: 1.3;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ad-lead-text {
        margin-top: 10px; color: rgba(255, 255, 255, .82); font-size: 15px; font-weight: 400; line-height: 1.6;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ad-lead-more { margin-top: 16px; display: inline-flex; align-items: center; gap: 6px; color: #fbbf24; font-size: 14.5px; font-weight: 700; }
    .ad-lead-more .material-symbols-outlined { font-size: 19px; color: inherit; transition: transform .3s ease; }
    .ad-lead:hover .ad-lead-more .material-symbols-outlined { transform: translateX(4px); }

    .ad-news-chip {
        align-self: flex-start; padding: 4px 11px; border-radius: 999px;
        background: rgba(30, 58, 138, .08); color: #1e3a8a; font-size: 11.5px; font-weight: 700;
        letter-spacing: .08em; text-transform: uppercase;
    }

    .ad-more { display: flex; flex-direction: column; gap: 14px; }
    .ad-row {
        flex: 1; display: flex; align-items: center; gap: 16px; padding: 14px;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 18px;
        text-decoration: none !important; transition: border-color .3s ease, transform .3s ease, box-shadow .3s ease;
    }
    .ad-row:hover { border-color: #1e3a8a; transform: translateX(4px); box-shadow: 0 18px 36px -28px rgba(15, 23, 42, .5); }
    .ad-row-media { flex: 0 0 116px; height: 100px; position: relative; overflow: hidden; border-radius: 12px; background: #e5e7eb; }
    .ad-row-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .6s ease; }
    .ad-row:hover .ad-row-media img { transform: scale(1.08); }
    .ad-row-body { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .ad-row-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
    .ad-row-chip { color: #b45309; font-size: 11.5px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .ad-row-date { color: #9ca3af; font-size: 12.5px; }
    .ad-row-chip + .ad-row-date::before { content: "•"; margin-right: 10px; color: #d1d5db; }
    .ad-row-title {
        color: #172554; font-size: 16px; font-weight: 600; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ad-row-text {
        margin-top: 4px; color: #6b7280; font-size: 13.5px; font-weight: 400; line-height: 1.5;
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ad-row-go { flex: 0 0 auto; font-size: 20px; color: #1e3a8a; transition: transform .3s ease; }
    .ad-row:hover .ad-row-go { transform: translateX(3px); }
    .dark .ad-row { background: #1f2937; border-color: #374151; }
    .dark .ad-row-title { color: #f3f4f6; }
    .dark .ad-row-text { color: #9ca3af; }
    .dark .ad-row-go { color: #93c5fd; }

    @media (max-width: 1023px) {
        .ad-touch { grid-template-columns: 1fr; }
        .ad-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 30px; }
        .ad-steps::before { display: none; }
        .ad-news { grid-template-columns: 1fr; }
    }
    @media (max-width: 767px) {
        .ad-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ad-stat:nth-child(odd) { border-left: 0; }
        .ad-stat:nth-child(n+3) { border-top: 1px solid #eef1f5; }
        .ad-stat { padding: 18px 10px; }
        .ad-stat-value { font-size: 24px; }
        .ad-contact { padding: 28px 22px; }
        .ad-contact-title, .ad-notices-title { font-size: 25px; }
        .ad-notices-head { flex-direction: column; align-items: flex-start; }
        .ad-notice { gap: 14px; padding: 14px; }
        .ad-date { flex-basis: 56px; height: 56px; }
        .ad-process { padding: 52px 0 48px; }
    }
    @media (max-width: 560px) {
        .ad-steps { grid-template-columns: 1fr; }
        .ad-lead { min-height: 360px; }
        .ad-lead-title { font-size: 20px; }
        .ad-row-media { flex-basis: 92px; height: 84px; }
        .ad-row-go { display: none; }
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

    <!-- Quick Stats -->
    <?php if (!empty($page_stats)): ?>
    <section class="relative z-20 -mt-16 pb-4">
        <div class="container">
            <div class="ad-stats" style="--ad-cols: <?php echo min(4, count($page_stats)); ?>;">
                <?php foreach ($page_stats as $stat): ?>
                <div class="ad-stat">
                    <span class="ad-stat-icon"><span class="material-symbols-outlined"><?php echo ad_t($stat['stat_icon'] ?: 'star'); ?></span></span>
                    <span class="ad-stat-value"><?php echo ad_t($stat['stat_value']); ?></span>
                    <span class="ad-stat-label"><?php echo ad_t($stat['stat_label']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Get in Touch & Notice Board -->
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
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="ad-touch vm-wrap">
                <!-- Contact panel -->
                <div class="ad-contact">
                    <span class="vm-kicker">Admissions Office</span>
                    <div class="ad-contact-title" role="heading" aria-level="2"><?php echo ad_t($contact_section['section_title']); ?></div>
                    <p class="ad-contact-sub"><?php echo ad_t($contact_section['section_subtitle']); ?></p>
                    <?php if ($contact_desc !== ''): ?>
                    <p class="ad-contact-text"><?php echo ad_t($contact_desc); ?></p>
                    <?php endif; ?>
                    <div class="ad-contact-rows">
                        <a href="<?php echo ad_t($contact_phone_link); ?>" class="ad-contact-row">
                            <span class="material-symbols-outlined">call</span>
                            <span class="ad-contact-val"><?php echo ad_t($contact_phone); ?></span>
                        </a>
                        <a href="<?php echo ad_t($contact_email_link); ?>" class="ad-contact-row">
                            <span class="material-symbols-outlined">mail</span>
                            <span class="ad-contact-val"><?php echo ad_t($contact_email); ?></span>
                        </a>
                    </div>
                    <a href="<?php echo ad_t($contact_btn_link); ?>" class="vm-btn vm-btn--gold ad-contact-btn">
                        <span class="material-symbols-outlined">send</span><?php echo ad_t(ucwords(strtolower($contact_btn_text))); ?>
                    </a>
                </div>

                <!-- Notice board -->
                <div class="ad-notices">
                    <div class="ad-notices-head">
                        <div>
                            <span class="vm-kicker">Stay Informed</span>
                            <div class="ad-notices-title" role="heading" aria-level="2">Notice Board</div>
                        </div>
                        <a href="notices.php" class="vm-link">See All Notices <span class="material-symbols-outlined">arrow_forward</span></a>
                    </div>

                    <?php if (empty($notices)): ?>
                        <div class="ad-notice ad-notice--empty">
                            <span class="material-symbols-outlined">notifications_off</span>
                            <span class="ad-notice-text">No notices available at this time.</span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notices as $notice): $ts = strtotime($notice['publish_date']); ?>
                        <a href="notices_detail.php?slug=<?php echo urlencode($notice['slug']); ?>" class="ad-notice">
                            <span class="ad-date">
                                <span class="ad-date-day"><?php echo date('d', $ts); ?></span>
                                <span class="ad-date-mon"><?php echo date('M Y', $ts); ?></span>
                            </span>
                            <span class="ad-notice-body">
                                <span class="ad-notice-title"><?php echo ad_t($notice['title']); ?></span>
                                <span class="ad-notice-text"><?php echo vvu_excerpt($notice['excerpt'], '', 140) ?: 'Click to read more about this notice.'; ?></span>
                            </span>
                            <span class="material-symbols-outlined ad-notice-go">chevron_right</span>
                        </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose VVU -->
    <?php $why_items = $items_map['why_choose'] ?? []; ?>
    <section class="vm-section vm-section--tint">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Our Unique Value</span>
                <div class="vm-heading" role="heading" aria-level="2"><?php echo ad_t($sections_map['why_choose']['section_title'] ?? 'Why Choose VVU?'); ?></div>
                <?php if (!empty($sections_map['why_choose']['section_subtitle'])): ?>
                <p class="vm-lead"><?php echo ad_t($sections_map['why_choose']['section_subtitle']); ?></p>
                <?php endif; ?>
            </div>

            <div class="vm-grid vm-wrap ad-why">
                <?php foreach ($why_items as $item): ?>
                <div class="vm-card vm-card--corner">
                    <div class="vm-card-title" role="heading" aria-level="3"><?php echo ad_t($item['item_title']); ?></div>
                    <p class="vm-card-text"><?php echo ad_t($item['item_description']); ?></p>
                    <span class="vm-card-corner"><span class="material-symbols-outlined"><?php echo ad_t($item['item_icon'] ?: 'star'); ?></span></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Programs -->
    <?php
    $fp_section = $sections_map['programs'] ?? ['section_title' => 'Featured Programs', 'section_subtitle' => 'Discover our most popular and impactful degree programs designed for your success.'];
    $program_items = $items_map['programs'] ?? [];
    ?>
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Study at VVU</span>
                <div class="vm-heading" role="heading" aria-level="2"><?php echo ad_t($fp_section['section_title']); ?></div>
                <?php if (!empty($fp_section['section_subtitle'])): ?>
                <p class="vm-lead"><?php echo ad_t($fp_section['section_subtitle']); ?></p>
                <?php endif; ?>
            </div>

            <div class="vm-grid vm-wrap">
                <?php foreach ($program_items as $program): ?>
                <a href="<?php echo ad_t($program['item_link'] ?: 'academic_programs_overview.php'); ?>" class="ad-prog">
                    <span class="ad-prog-media">
                        <img src="<?php echo ad_t($program['item_image'] ?: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&q=80'); ?>"
                             alt="<?php echo ad_t($program['item_title']); ?>" loading="lazy">
                        <span class="ad-prog-chip"><?php echo ad_t($program['item_stat_value'] ?: 'Program'); ?></span>
                        <?php if (!empty($program['item_subtitle'])): ?>
                        <span class="ad-prog-faculty"><span class="material-symbols-outlined">account_balance</span><?php echo ad_t($program['item_subtitle']); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="ad-prog-body">
                        <span class="ad-prog-badge"><span class="material-symbols-outlined"><?php echo ad_t($program['item_icon'] ?: 'school'); ?></span></span>
                        <span class="ad-prog-title" role="heading" aria-level="3"><?php echo ad_t($program['item_title']); ?></span>
                        <span class="ad-prog-text"><?php echo ad_t($program['item_description'] ?: 'Explore this exciting program at Valley View University.'); ?></span>
                        <span class="ad-prog-foot">
                            <span class="ad-prog-cta">View Course Details</span>
                            <span class="ad-prog-go"><span class="material-symbols-outlined">arrow_forward</span></span>
                        </span>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>

            <div class="vm-actions">
                <a href="academic_programs_overview.php" class="vm-btn vm-btn--navy"><span class="material-symbols-outlined">school</span>View All Programs</a>
            </div>
        </div>
    </section>

    <!-- Admission Requirements -->
    <?php $req_items = $items_map['requirements'] ?? []; ?>
    <section class="vm-section vm-section--tint">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Requirements</span>
                <div class="vm-heading" role="heading" aria-level="2"><?php echo ad_t($sections_map['requirements']['section_title'] ?? 'Admission Requirements'); ?></div>
                <p class="vm-lead"><?php echo ad_t($sections_map['requirements']['section_subtitle'] ?? 'What you need to apply for admission to Valley View University.'); ?></p>
            </div>

            <div class="vm-grid vm-wrap">
                <?php foreach ($req_items as $item):
                    // One checklist line per sentence of the admin text.
                    $req_points = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', trim(strip_tags((string) $item['item_description'])), -1, PREG_SPLIT_NO_EMPTY); ?>
                <div class="ad-req">
                    <div class="ad-req-head">
                        <span class="ad-req-icon"><span class="material-symbols-outlined"><?php echo ad_t($item['item_icon'] ?: 'school'); ?></span></span>
                        <div>
                            <div class="ad-req-title" role="heading" aria-level="3"><?php echo ad_t($item['item_title']); ?></div>
                            <?php if (!empty($item['item_subtitle'])): ?>
                            <span class="ad-req-sub"><?php echo ad_t($item['item_subtitle']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <ul class="ad-req-list">
                        <?php foreach ($req_points as $pt): ?>
                        <li><span class="material-symbols-outlined">check_circle</span><span class="ad-req-point"><?php echo ad_t($pt); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="apply.php" class="ad-req-foot">Start Application <span class="material-symbols-outlined">arrow_forward</span></a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Admission Process -->
    <?php $process_items = $items_map['process'] ?? []; ?>
    <section id="process" class="vm-cta ad-process">
        <div class="container">
            <div class="vm-cta-head">
                <span class="vm-kicker">How to Apply</span>
                <div class="vm-cta-heading" role="heading" aria-level="2"><?php echo ad_t($sections_map['process']['section_title'] ?? 'Admission Process'); ?></div>
                <p class="vm-cta-lead"><?php echo ad_t($sections_map['process']['section_subtitle'] ?? 'Follow these simple steps to join our vibrant academic community.'); ?></p>
            </div>

            <ol class="ad-steps" style="--ad-cols: <?php echo max(1, min(4, count($process_items))); ?>;">
                <?php foreach ($process_items as $i => $step):
                    $num = trim((string) ($step['item_stat_value'] ?? ''));
                    if ($num === '') $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?>
                <li class="ad-step">
                    <span class="ad-step-num"><?php echo ad_t($num); ?></span>
                    <div class="ad-step-card">
                        <?php if (!empty($step['item_icon'])): ?>
                        <span class="material-symbols-outlined ad-step-icon"><?php echo ad_t($step['item_icon']); ?></span>
                        <?php endif; ?>
                        <div class="ad-step-title" role="heading" aria-level="3"><?php echo ad_t($step['item_title']); ?></div>
                        <p class="ad-step-text"><?php echo ad_t($step['item_description']); ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- Latest News -->
    <?php
    $ln_section = $sections_map['latest_news'] ?? ['section_title' => 'Latest from Campus', 'section_subtitle' => 'Stay updated with the latest events and stories from our community.'];
    $news_cards = [];
    if (empty($latest_news)) {
        // Fallback static content if no news
        $news_cards[] = ['img' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=400', 'chip' => '', 'title' => 'Business Plan Bootcamp and Seminar', 'text' => 'Inspiring young entrepreneurs through transformative business planning sessions.', 'date' => '', 'link' => 'news_&_events.php'];
        $news_cards[] = ['img' => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&q=80&w=400', 'chip' => '', 'title' => 'Ellen Gould White Residence Hall Week', 'text' => 'Celebrating community and culture at Valley View University residence halls.', 'date' => '', 'link' => 'news_&_events.php'];
    } else {
        foreach ($latest_news as $news_item) {
            $news_cards[] = [
                'img'   => getAdmissionImage($news_item['featured_image'], 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&q=80'),
                'chip'  => ucfirst($news_item['category']),
                'title' => $news_item['title'],
                'text'  => vvu_excerpt($news_item['excerpt'], '', 140) ?: 'Click to read more about this update.',
                'date'  => formatAdmissionDate($news_item['publish_date']),
                'link'  => ($news_item['category'] === 'events' ? 'event_detail.php' : 'news_detail.php') . '?slug=' . urlencode($news_item['slug']),
                'raw'   => true,
            ];
        }
    }
    ?>
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">News &amp; Events</span>
                <div class="vm-heading" role="heading" aria-level="2"><?php echo ad_t($ln_section['section_title']); ?></div>
                <?php if (!empty($ln_section['section_subtitle'])): ?>
                <p class="vm-lead"><?php echo ad_t($ln_section['section_subtitle']); ?></p>
                <?php endif; ?>
            </div>

            <?php $lead_story = array_shift($news_cards); ?>
            <div class="ad-news vm-wrap<?php echo empty($news_cards) ? ' ad-news--single' : ''; ?>">
                <!-- Featured story -->
                <a href="<?php echo ad_t($lead_story['link']); ?>" class="ad-lead">
                    <img src="<?php echo ad_t($lead_story['img']); ?>" alt="<?php echo ad_t($lead_story['title']); ?>" loading="lazy">
                    <span class="ad-lead-body">
                        <span class="ad-lead-meta">
                            <?php if ($lead_story['chip'] !== ''): ?><span class="ad-news-chip"><?php echo ad_t($lead_story['chip']); ?></span><?php endif; ?>
                            <?php if ($lead_story['date'] !== ''): ?>
                            <span class="ad-lead-date"><span class="material-symbols-outlined">calendar_today</span><?php echo ad_t($lead_story['date']); ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="ad-lead-title"><?php echo ad_t($lead_story['title']); ?></span>
                        <span class="ad-lead-text"><?php echo !empty($lead_story['raw']) ? $lead_story['text'] : ad_t($lead_story['text']); ?></span>
                        <span class="ad-lead-more">Read Story <span class="material-symbols-outlined">arrow_forward</span></span>
                    </span>
                </a>

                <?php if (!empty($news_cards)): ?>
                <!-- More stories -->
                <div class="ad-more">
                    <?php foreach ($news_cards as $nc): ?>
                    <a href="<?php echo ad_t($nc['link']); ?>" class="ad-row">
                        <span class="ad-row-media">
                            <img src="<?php echo ad_t($nc['img']); ?>" alt="<?php echo ad_t($nc['title']); ?>" loading="lazy">
                        </span>
                        <span class="ad-row-body">
                            <span class="ad-row-meta">
                                <?php if ($nc['chip'] !== ''): ?><span class="ad-row-chip"><?php echo ad_t($nc['chip']); ?></span><?php endif; ?>
                                <?php if ($nc['date'] !== ''): ?><span class="ad-row-date"><?php echo ad_t($nc['date']); ?></span><?php endif; ?>
                            </span>
                            <span class="ad-row-title"><?php echo ad_t($nc['title']); ?></span>
                            <span class="ad-row-text"><?php echo !empty($nc['raw']) ? $nc['text'] : ad_t($nc['text']); ?></span>
                        </span>
                        <span class="material-symbols-outlined ad-row-go">arrow_forward</span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="vm-actions">
                <a href="news_&_events.php" class="vm-btn vm-btn--outline">View All News &amp; Events<span class="material-symbols-outlined">arrow_forward</span></a>
            </div>
        </div>
    </section>

    <!-- Closing call to action -->
    <?php $cta_extra = $items_map['cta_extra'] ?? []; ?>
    <section class="vm-cta">
        <div class="container">
            <div class="vm-cta-head">
                <span class="vm-kicker">Take the Next Step</span>
                <div class="vm-cta-heading" role="heading" aria-level="2"><?php echo ad_t($page_data['cta_title'] ?? 'Ready to start your journey?'); ?></div>
                <p class="vm-cta-lead"><?php echo ad_t($page_data['cta_subtitle'] ?? 'Join thousands of students who have chosen Valley View University for a life-changing educational experience.'); ?></p>
                <div class="vm-actions">
                    <a href="<?php echo ad_t($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="vm-btn vm-btn--gold">
                        <span class="material-symbols-outlined">rocket_launch</span><?php echo ad_t($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="<?php echo ad_t($cta_extra[0]['item_link'] ?? 'contact_us.php'); ?>" class="vm-btn vm-btn--ghost">
                        <span class="material-symbols-outlined"><?php echo ad_t($cta_extra[0]['item_icon'] ?? 'chat'); ?></span><?php echo ad_t($cta_extra[0]['item_title'] ?? 'Talk to an Advisor'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>