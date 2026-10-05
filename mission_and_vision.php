<?php
$page_title = "Mission and Vision - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM mission_vision_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$cards = $pdo->query("SELECT * FROM mission_vision_cards WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$pillars = $pdo->query("SELECT * FROM mission_vision_pillars WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$environment = $pdo->query("SELECT * FROM mission_vision_environment WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

// Bottom call-to-action block. Falls back to the previous hard-coded copy if
// install_mission_vision_cta.php has not been run yet.
try {
    $cta = $pdo->query("SELECT * FROM mission_vision_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
    $cta_links = $pdo->query("SELECT * FROM mission_vision_cta_links WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
} catch (PDOException $e) {
    $cta = false;
    $cta_links = [];
}

if (!$cta_links) {
    $cta_links = [
        ['icon' => 'star',          'title' => 'Our Core Values',    'description' => 'The beliefs that shape how we teach, serve and live.',       'link_url' => 'core_values.php'],
        ['icon' => 'menu_book',     'title' => 'Academic Programs',  'description' => 'Undergraduate, graduate and professional courses.',          'link_url' => 'academic_programs_overview.php'],
        ['icon' => 'location_city', 'title' => 'Visit Our Campus',   'description' => 'See Oyibi for yourself — facilities, halls and green space.', 'link_url' => 'the_campus.php'],
    ];
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
    /* Vision / Mission bands */
    .mv-bands { padding: 64px 0; display: flex; flex-direction: column; gap: 20px; }
    .mv-band { position: relative; overflow: hidden; padding: 56px 0; }
    .mv-band--dark {
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .mv-band--dark::after {
        content: ""; position: absolute; right: -120px; top: -120px;
        width: 360px; height: 360px; border-radius: 50%;
        background: rgba(255, 255, 255, .06); pointer-events: none;
    }
    .mv-band--light { background: linear-gradient(120deg, #e0e7f1 0%, #cfd9e8 100%); }
    .dark .mv-band--light { background: linear-gradient(120deg, #1f2937 0%, #111827 100%); }

    .mv-band-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; justify-content: center;
        gap: 48px; max-width: 1100px; margin: 0 auto;
    }
    .mv-band-inner--flip { flex-direction: row-reverse; }

    .mv-band-text {
        flex: 1 1 0; max-width: 640px; margin: 0;
        font-size: 1.6rem; line-height: 1.6; font-weight: 600;
    }
    .mv-band--dark .mv-band-text { color: rgba(255, 255, 255, .92); }
    .mv-band--light .mv-band-text { color: #1f2937; }
    .dark .mv-band--light .mv-band-text { color: #e5e7eb; }

    /* The title is a div with role="heading" so the site-wide h2 rules
       (Cinzel at weight 500, !important) don't override the bold word. */
    .mv-band-title { flex: 0 0 auto; margin: 0; line-height: 1.2; overflow: visible; }
    .mv-band-eyebrow {
        display: block; font-size: 1.35rem; font-weight: 800;
        letter-spacing: .3em; text-transform: uppercase; margin-bottom: 2px;
    }
    .mv-band--dark .mv-band-eyebrow { color: rgba(255, 255, 255, .7); }
    .mv-band--light .mv-band-eyebrow { color: #1e3a8a; }
    .dark .mv-band--light .mv-band-eyebrow { color: #93c5fd; }
    .mv-band-word {
        display: block; font-size: clamp(3.5rem, 9vw, 7rem);
        font-weight: 600; letter-spacing: -.03em; line-height: 1.2;
        padding: 0 .06em .06em 0;
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .mv-band--dark .mv-band-word { background-image: linear-gradient(90deg, #fff7d6 0%, #fbbf24 55%, #f59e0b 100%); }
    .mv-band--light .mv-band-word { background-image: linear-gradient(90deg, #93a9cf 0%, #1e3a8a 70%); }
    .dark .mv-band--light .mv-band-word { background-image: linear-gradient(90deg, #93c5fd 0%, #3b82f6 100%); }

    @media (max-width: 767px) {
        .mv-bands { padding: 40px 0; gap: 14px; }
        .mv-band { padding: 40px 0; }
        .mv-band-inner, .mv-band-inner--flip {
            flex-direction: column-reverse; align-items: flex-start; gap: 16px;
        }
        .mv-band-text { font-size: 1.25rem; max-width: none; }
        .mv-band-eyebrow { font-size: 1.1rem; }
    }

    /* Four Pillars: one row of white cards with a navy quarter-circle in
       the bottom-right corner holding a thin line icon. The bottom padding
       keeps the text clear of the corner, so the text can use the full width. */
    .mv-pillars {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px; max-width: 1280px; margin: 0 auto;
    }
    .mv-pillar {
        position: relative; overflow: hidden;
        display: flex; flex-direction: column;
        padding: 32px 28px 112px; border-radius: 22px;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .mv-pillar:hover { transform: translateY(-6px); box-shadow: 0 28px 50px -28px rgba(15, 23, 42, .5); }
    .dark .mv-pillar { background: #1f2937; border-color: #374151; }

    .mv-pillar-title {
        color: #1e3a8a; font-size: 1.75rem; font-weight: 700;
        line-height: 1.2; letter-spacing: -.01em; margin-bottom: 16px;
    }
    .mv-pillar-title::after {
        content: ""; display: block; width: 40px; height: 3px;
        border-radius: 3px; background: #fbbf24; margin-top: 12px;
    }
    .dark .mv-pillar-title { color: #bfdbfe; }
    .mv-pillar-text {
        margin: 0; color: #4b5563;
        font-size: 1.35rem; line-height: 1.6; font-weight: 500;
    }
    .dark .mv-pillar-text { color: #d1d5db; }

    .mv-pillar-corner {
        position: absolute; right: 0; bottom: 0;
        width: 96px; height: 96px;
        background: #1e3a8a; border-top-left-radius: 100%;
        display: flex; align-items: flex-end; justify-content: flex-end;
        padding: 0 18px 18px 0;
        transition: background-color .35s ease;
    }
    .mv-pillar-corner .material-symbols-outlined {
        font-size: 38px; width: 38px; height: 38px; line-height: 1; color: #fff;
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        transition: transform .35s ease;
    }
    .mv-pillar:hover .mv-pillar-corner .material-symbols-outlined { transform: scale(1.08) rotate(-4deg); }
    .dark .mv-pillar-corner { background: #3b82f6; }

    @media (max-width: 1099px) {
        .mv-pillars { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 639px) {
        .mv-pillars { grid-template-columns: 1fr; gap: 14px; }
        /* Single column is wide enough to keep text beside the corner */
        .mv-pillar { padding: 26px 96px 30px 24px; }
        .mv-pillar-title { font-size: 1.5rem; }
        .mv-pillar-text { font-size: 1.2rem; }
        .mv-pillar-corner { width: 80px; height: 80px; padding: 0 14px 14px 0; }
        .mv-pillar-corner .material-symbols-outlined { font-size: 32px; width: 32px; height: 32px; }
    }

    /* ============================================================
       Shared section styles. Headings are divs with role="heading" so
       the site-wide h1-h6 rules (Cinzel, weight 500, !important) don't
       apply, keeping the modern sans look of the Vision/Mission bands.
       Palette: navy #1e3a8a / #172554, gold #fbbf24 / #f59e0b, greys.
       ============================================================ */
    /* Section headings keep the site's title font (Cinzel). The universal
       `*` rule in custom-fixes.css sets the body font on every element, so
       nested spans need it too. */
    main .mv-heading, main .mv-cta-heading,
    main .mv-env-feature-title, main .mv-link-title {
        font-family: var(--vvu-title-font);
        font-weight: var(--vvu-title-weight);
        letter-spacing: normal;
    }

    .mv-section { padding: 88px 0; }
    .mv-section--white { background: #fff; }
    .dark .mv-section--white { background: #111827; }

    .mv-head { max-width: 820px; margin: 0 auto 56px; text-align: center; }
    .mv-kicker {
        display: inline-block; margin-bottom: 14px;
        font-size: 1rem; font-weight: 700; letter-spacing: .28em;
        text-transform: uppercase; color: #b45309;
    }
    .dark .mv-kicker { color: #fbbf24; }
    .mv-kicker--light { color: #fbbf24; }
    .mv-heading {
        color: #1e3a8a; font-size: clamp(2.25rem, 4.5vw, 3.5rem);
        font-weight: 700; line-height: 1.15; letter-spacing: -.02em;
    }
    .dark .mv-heading { color: #fff; }
    .mv-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .mv-heading--left::after { margin-left: 0; }
    .mv-lead {
        margin: 22px 0 0; color: #4b5563;
        font-size: 1.4rem; line-height: 1.6; font-weight: 400;
    }
    .dark .mv-lead { color: #9ca3af; }
    .mv-body {
        margin: 0 0 16px; color: #4b5563;
        font-size: 1.3rem; line-height: 1.7; font-weight: 400;
    }
    .dark .mv-body { color: #d1d5db; }

    /* Learning environment: image with a navy quarter-circle corner */
    .mv-env {
        display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 72px; align-items: center; max-width: 1200px; margin: 0 auto;
    }
    /* Frame: room around the image for the decorative layers behind it */
    .mv-env-frame { position: relative; padding: 0 24px 24px 0; isolation: isolate; }
    /* Navy panel offset down and right behind the image */
    .mv-env-frame::before {
        content: ""; position: absolute; z-index: -1;
        top: 24px; left: 24px; right: 0; bottom: 0;
        border-radius: 28px;
        background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);
    }
    /* Gold dot grid peeking out at the top-left */
    .mv-env-frame::after {
        content: ""; position: absolute; z-index: -1;
        top: -22px; left: -22px; width: 130px; height: 130px;
        background-image: radial-gradient(#f59e0b 2px, transparent 2.5px);
        background-size: 16px 16px; opacity: .7;
    }
    .mv-env-media {
        position: relative; overflow: hidden; border-radius: 26px;
        aspect-ratio: 4 / 3;
        border: 8px solid #fff;
        box-shadow: 0 30px 60px -30px rgba(15, 23, 42, .55);
    }
    .dark .mv-env-media { border-color: #1f2937; }
    .mv-env-media img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        border-radius: 18px; transition: transform .8s ease;
    }
    .mv-env-media:hover img { transform: scale(1.04); }
    /* Soft shade at the bottom so the caption chip reads on any photo */
    .mv-env-media::after {
        content: ""; position: absolute; inset: 0; border-radius: 18px; pointer-events: none;
        background: linear-gradient(to top, rgba(23, 37, 84, .45) 0%, transparent 35%);
    }
    .mv-env-chip {
        position: absolute; left: 18px; bottom: 18px; z-index: 1;
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 999px;
        background: rgba(255, 255, 255, .9);
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        color: #1e3a8a; font-size: .95rem; font-weight: 600;
        box-shadow: 0 8px 20px -10px rgba(15, 23, 42, .5);
    }
    .mv-env-chip-dot { width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; }
    .mv-env-copy .mv-heading { margin-bottom: 24px; }
    .mv-env-features {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px; margin-top: 28px;
    }
    .mv-env-feature {
        display: flex; gap: 14px; align-items: flex-start;
        padding: 20px; border-radius: 18px;
        background: #f8fafc; border: 1px solid #e5e7eb;
    }
    .dark .mv-env-feature { background: #1f2937; border-color: #374151; }
    .mv-env-feature-icon {
        flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center;
        width: 46px; height: 46px; border-radius: 50%; background: #1e3a8a;
    }
    .mv-env-feature-icon .material-symbols-outlined {
        font-size: 24px; color: #fff;
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
    }
    .mv-env-feature-title { color: #1e3a8a; font-size: 1.25rem; font-weight: 700; line-height: 1.3; }
    .dark .mv-env-feature-title { color: #bfdbfe; }
    .mv-env-feature-text { margin: 4px 0 0; color: #4b5563; font-size: 1.05rem; line-height: 1.5; font-weight: 400; }
    .dark .mv-env-feature-text { color: #9ca3af; }

    /* Call to action: Vision-band gradient with a soft circle */
    .mv-cta {
        position: relative; overflow: hidden; padding: 64px 0 56px;
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .mv-cta::before, .mv-cta::after {
        content: ""; position: absolute; border-radius: 50%; pointer-events: none;
        background: rgba(255, 255, 255, .05);
    }
    .mv-cta::before { width: 520px; height: 520px; right: -180px; top: -200px; }
    .mv-cta::after { width: 360px; height: 360px; left: -140px; bottom: -180px; }
    .mv-cta .container { position: relative; z-index: 1; }
    .mv-cta-head { max-width: 820px; margin: 0 auto; text-align: center; }
    .mv-cta-heading {
        color: #fff; font-size: clamp(2.25rem, 4.5vw, 3.5rem);
        font-weight: 700; line-height: 1.15; letter-spacing: -.02em;
    }
    .mv-cta-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .mv-cta-lead {
        margin: 22px 0 0; color: rgba(255, 255, 255, .85);
        font-size: 1.35rem; line-height: 1.6; font-weight: 400;
    }
    .mv-cta-actions {
        display: flex; flex-wrap: wrap; gap: 14px; justify-content: center;
        margin-top: 34px;
    }
    .mv-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 28px; border-radius: 999px;
        font-size: 1.1rem; font-weight: 700; text-decoration: none;
        transition: background-color .25s ease, color .25s ease, transform .25s ease, box-shadow .25s ease;
    }
    .mv-btn .material-symbols-outlined { font-size: 22px; }
    .mv-btn:hover { transform: translateY(-2px); }
    .mv-btn--gold { background: #fbbf24; color: #172554; box-shadow: 0 12px 24px -14px rgba(251, 191, 36, .9); }
    .mv-btn--gold:hover { background: #fcd34d; color: #172554; }
    .mv-btn--ghost { color: #fff; border: 1.5px solid rgba(255, 255, 255, .6); }
    .mv-btn--ghost:hover { background: #fff; color: #1e3a8a; }

    .mv-cta-kicker {
        display: block; text-align: center;
        margin: 44px auto 18px; max-width: 1200px;
    }
    .mv-links {
        display: grid; gap: 12px 36px; max-width: 1200px; margin: 0 auto;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .mv-links--1 { grid-template-columns: minmax(0, 420px); justify-content: center; }
    .mv-links--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .mv-links--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .mv-link {
        position: relative;
        display: flex; align-items: flex-start; gap: 16px;
        padding: 22px 4px 6px;
        border-top: 1px solid rgba(255, 255, 255, .25);
        text-decoration: none;
    }
    /* Gold rule that grows across the top border on hover */
    .mv-link::before {
        content: ""; position: absolute; left: 0; top: -1px;
        width: 0; height: 2px; background: #fbbf24;
        transition: width .4s ease;
    }
    .mv-link:hover::before { width: 100%; }
    .mv-link-num {
        flex: 0 0 auto; padding-top: 4px;
        color: #fbbf24; font-size: 1rem; font-weight: 700; letter-spacing: .08em;
    }
    .mv-link-body { flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column; }
    .mv-link-title { color: #fff; font-size: 1.35rem; line-height: 1.3; }
    .mv-link-text {
        margin-top: 6px; color: rgba(255, 255, 255, .75);
        font-size: 1.05rem; line-height: 1.5; font-weight: 400;
    }
    .mv-link-arrow {
        flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .35);
        transition: background-color .3s ease, border-color .3s ease;
    }
    .mv-link-arrow .material-symbols-outlined {
        font-size: 20px; color: #fff; transition: color .3s ease, transform .3s ease;
    }
    .mv-link:hover .mv-link-arrow { background: #fbbf24; border-color: #fbbf24; }
    .mv-link:hover .mv-link-arrow .material-symbols-outlined { color: #172554; transform: rotate(45deg); }

    @media (max-width: 1099px) {
        .mv-links--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 1023px) {
        .mv-env { grid-template-columns: 1fr; gap: 40px; }
    }
    @media (max-width: 767px) {
        .mv-section { padding: 60px 0; }
        .mv-head { margin-bottom: 36px; }
        .mv-kicker { font-size: .85rem; }
        .mv-lead, .mv-cta-lead { font-size: 1.15rem; }
        .mv-body { font-size: 1.1rem; }
        .mv-env-features { grid-template-columns: 1fr; }
        .mv-env-frame { padding: 0 14px 14px 0; }
        .mv-env-frame::before { top: 14px; left: 14px; border-radius: 22px; }
        .mv-env-frame::after { top: -14px; left: -14px; width: 90px; height: 90px; }
        .mv-env-media { border-width: 6px; border-radius: 20px; }
        .mv-env-media img, .mv-env-media::after { border-radius: 14px; }
        .mv-env-chip { left: 12px; bottom: 12px; font-size: .85rem; padding: 6px 12px; }
        .mv-cta { padding: 48px 0 44px; }
        .mv-cta-actions .mv-btn { width: 100%; justify-content: center; }
        .mv-cta-kicker { margin-top: 32px; }
        .mv-links, .mv-links--2, .mv-links--4 { grid-template-columns: 1fr; gap: 4px; }
    }

    .text-gradient {
        background: linear-gradient(to right, #2563eb, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[75vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-32">
            <div class="max-w-6xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'About Our Institution'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title_1'] ?? 'Our Mission'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_title_2'] ?? 'Vision'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'Guiding principles and aspirations'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Content Section -->
    <!-- Full-width bands that alternate dark/light; the statement and the big
         title word swap sides on each band. -->
    <section class="mv-bands">
        <?php foreach ($cards as $i => $card):
            $is_dark = ($i % 2 === 0);
            $title = strip_tags($card['title']);
            // "Our Vision" -> eyebrow "Our" + big word "Vision"
            $eyebrow = '';
            if (preg_match('/^(our)\s+(.+)$/i', $title, $m)) {
                $eyebrow = $m[1];
                $title = $m[2];
            }
        ?>
        <div class="mv-band <?php echo $is_dark ? 'mv-band--dark' : 'mv-band--light'; ?>">
            <div class="container">
                <div class="mv-band-inner <?php echo $is_dark ? '' : 'mv-band-inner--flip'; ?>">
                    <p class="mv-band-text"><?php echo strip_tags($card['content']); ?></p>
                    <div class="mv-band-title" role="heading" aria-level="2">
                        <?php if ($eyebrow !== ''): ?><span class="mv-band-eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
                        <span class="mv-band-word"><?php echo $title; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <!-- Key Pillars Section -->
    <section class="mv-section">
        <div class="container">
            <div class="mv-head">
                <span class="mv-kicker">What Shapes Us</span>
                <div class="mv-heading" role="heading" aria-level="2">Our Four Pillars of Development</div>
                <p class="mv-lead">Valley View University is committed to the holistic development of every student and staff member through four key dimensions.</p>
            </div>

            <?php
            // Thin line icons shown in each card's corner, picked by pillar
            // title. These replace the icons stored with the pillars.
            $pillar_icons = [
                'physical'     => 'directions_run',
                'intellectual' => 'lightbulb',
                'social'       => 'diversity_3',
                'spiritual'    => 'volunteer_activism',
            ];
            ?>
            <!-- One row of white cards with a navy quarter-circle in the bottom-right
                 corner holding a thin line icon. -->
            <div class="mv-pillars">
                <?php foreach ($pillars as $pillar):
                    $key = strtolower(trim(strip_tags($pillar['title'])));
                    $corner_icon = $pillar_icons[$key] ?? 'star';
                ?>
                <div class="mv-pillar">
                    <div class="mv-pillar-title" role="heading" aria-level="3"><?php echo strip_tags($pillar['title']); ?></div>
                    <p class="mv-pillar-text"><?php echo strip_tags($pillar['description']); ?></p>
                    <span class="mv-pillar-corner" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo $corner_icon; ?></span>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Learning Environment Section -->
    <section class="mv-section mv-section--white">
        <div class="container">
            <div class="mv-env">
                <!-- Image in a white mat, with an offset navy panel and a
                     gold dot grid behind it, and a small caption chip -->
                <div class="mv-env-frame">
                    <div class="mv-env-media">
                        <img src="<?php echo strip_tags($environment['image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'); ?>"
                             alt="Valley View University campus" loading="lazy">
                        <span class="mv-env-chip">
                            <span class="mv-env-chip-dot"></span>
                            Valley View University, Oyibi
                        </span>
                    </div>
                </div>

                <div class="mv-env-copy">
                    <span class="mv-kicker"><?php echo strip_tags($environment['badge_text'] ?? 'Our Commitment'); ?></span>
                    <div class="mv-heading mv-heading--left" role="heading" aria-level="2"><?php echo strip_tags($environment['section_title'] ?? 'A Well-Designed Learning Environment'); ?></div>
                    <?php foreach (['paragraph_1', 'paragraph_2'] as $p): if (trim($environment[$p] ?? '') === '') continue; ?>
                    <p class="mv-body"><?php echo strip_tags($environment[$p]); ?></p>
                    <?php endforeach; ?>

                    <div class="mv-env-features">
                        <?php
                        $env_features = [
                            ['icon' => 'school',  'title' => $environment['feature_1_title'] ?? 'Academic Excellence', 'text' => $environment['feature_1_description'] ?? 'Quality programs'],
                            ['icon' => 'science', 'title' => $environment['feature_2_title'] ?? 'Research Focus',      'text' => $environment['feature_2_description'] ?? 'Research opportunities'],
                        ];
                        foreach ($env_features as $feat): ?>
                        <div class="mv-env-feature">
                            <span class="mv-env-feature-icon"><span class="material-symbols-outlined"><?php echo $feat['icon']; ?></span></span>
                            <div>
                                <div class="mv-env-feature-title" role="heading" aria-level="3"><?php echo strip_tags($feat['title']); ?></div>
                                <p class="mv-env-feature-text"><?php echo strip_tags($feat['text']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section: same dark-blue gradient as the Vision band -->
    <section class="mv-cta">
        <div class="container">
            <div class="mv-cta-head">
                <div class="mv-cta-heading" role="heading" aria-level="2">
                    <?php echo htmlspecialchars($cta['heading'] ?? 'Join Our Community of Excellence'); ?>
                </div>
                <?php $cta_subtitle = $cta['subtitle'] ?? 'Discover how Valley View University can help you achieve holistic development and prepare for meaningful service to God and humanity.'; ?>
                <?php if (trim($cta_subtitle) !== ''): ?>
                <p class="mv-cta-lead"><?php echo nl2br(htmlspecialchars($cta_subtitle)); ?></p>
                <?php endif; ?>

                <div class="mv-cta-actions">
                    <?php $btn1_text = $cta['primary_btn_text'] ?? 'Learn More About VVU'; ?>
                    <?php if (trim($btn1_text) !== ''): ?>
                    <a href="<?php echo htmlspecialchars($cta['primary_btn_link'] ?? 'about_us.php'); ?>" class="mv-btn mv-btn--gold">
                        <span class="material-symbols-outlined"><?php echo htmlspecialchars($cta['primary_btn_icon'] ?? 'info'); ?></span>
                        <?php echo htmlspecialchars($btn1_text); ?>
                    </a>
                    <?php endif; ?>

                    <?php $btn2_text = $cta['secondary_btn_text'] ?? 'Apply Now'; ?>
                    <?php if (trim($btn2_text) !== ''): ?>
                    <a href="<?php echo htmlspecialchars($cta['secondary_btn_link'] ?? 'apply.php'); ?>" class="mv-btn mv-btn--ghost">
                        <span class="material-symbols-outlined"><?php echo htmlspecialchars($cta['secondary_btn_icon'] ?? 'how_to_reg'); ?></span>
                        <?php echo htmlspecialchars($btn2_text); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links: open numbered columns under a thin rule that
                 fills gold on hover -->

            <?php $eyebrow = $cta['links_eyebrow'] ?? 'Explore More'; ?>
            <?php if (trim($eyebrow) !== ''): ?>
            <span class="mv-kicker mv-kicker--light mv-cta-kicker"><?php echo htmlspecialchars($eyebrow); ?></span>
            <?php endif; ?>
            <div class="mv-links mv-links--<?php echo max(1, min(count($cta_links), 4)); ?>">
                <?php foreach ($cta_links as $li => $link): ?>
                <a href="<?php echo htmlspecialchars($link['link_url']); ?>" class="mv-link">
                    <span class="mv-link-num"><?php echo str_pad($li + 1, 2, '0', STR_PAD_LEFT); ?></span>
                    <span class="mv-link-body">
                        <span class="mv-link-title"><?php echo htmlspecialchars($link['title']); ?></span>
                        <?php if (trim($link['description'] ?? '') !== ''): ?>
                        <span class="mv-link-text"><?php echo htmlspecialchars($link['description']); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="mv-link-arrow" aria-hidden="true"><span class="material-symbols-outlined">arrow_outward</span></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
