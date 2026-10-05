<?php
$page_title = "The VVU - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch Page Content
try {
    $page_stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'the_campus'");
    $page_stmt->execute();
    $page_data = $page_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $page_data = [];
}

// Fetch sections
try {
    $sections_stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'the_campus' ORDER BY display_order");
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
    $items_stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'the_campus' AND is_active = 1 ORDER BY display_order");
    $items_stmt->execute();
    $all_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    $items_map = [];
    foreach ($all_items as $item) {
        $items_map[$item['section_key']][] = $item;
    }
} catch (PDOException $e) {
    $items_map = [];
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
    /* Highlight bands (same design as the Mission & Vision and Core Values pages).
       Titles here are full phrases, so they are smaller than the one-word
       titles on those pages and get a fixed column width. */
    .cp-bands { display: flex; flex-direction: column; gap: 20px; }
    .cp-band { position: relative; overflow: hidden; padding: 56px 0; }
    .cp-band--dark { background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%); }
    .cp-band--dark::after {
        content: ""; position: absolute; right: -120px; top: -120px;
        width: 360px; height: 360px; border-radius: 50%;
        background: rgba(255, 255, 255, .06); pointer-events: none;
    }
    .cp-band--light { background: linear-gradient(120deg, #e0e7f1 0%, #cfd9e8 100%); }
    .dark .cp-band--light { background: linear-gradient(120deg, #1f2937 0%, #111827 100%); }

    .cp-band-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; justify-content: center;
        gap: 48px; max-width: 1100px; margin: 0 auto;
    }
    .cp-band-inner--flip { flex-direction: row-reverse; }

    .cp-band-copy { flex: 1 1 0; max-width: 640px; }
    .cp-band-text { margin: 0; font-size: 1.4rem; line-height: 1.65; font-weight: 500; }
    .cp-band-features {
        list-style: none; margin: 22px 0 0; padding: 0;
        display: flex; flex-wrap: wrap; gap: 10px;
    }
    .cp-band-features li {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 999px;
        font-size: 1.15rem; font-weight: 600;
    }
    .cp-band-features .material-symbols-outlined { font-size: 1.35rem; }
    .cp-band-quote {
        margin: 22px 0 0; padding-left: 16px;
        font-size: 1.3rem; line-height: 1.55; font-style: italic;
        border-left: 3px solid;
    }

    .cp-band--dark .cp-band-text { color: rgba(255, 255, 255, .92); }
    .cp-band--dark .cp-band-features li { background: rgba(255, 255, 255, .12); color: #fff; }
    .cp-band--dark .cp-band-features .material-symbols-outlined { color: #fbbf24; }
    .cp-band--dark .cp-band-quote { color: rgba(255, 255, 255, .8); border-color: #fbbf24; }

    .cp-band--light .cp-band-text { color: #1f2937; }
    .cp-band--light .cp-band-features li { background: rgba(30, 58, 138, .1); color: #1e3a8a; }
    .cp-band--light .cp-band-quote { color: #374151; border-color: #1e3a8a; }
    .dark .cp-band--light .cp-band-text { color: #e5e7eb; }
    .dark .cp-band--light .cp-band-features li { background: rgba(147, 197, 253, .12); color: #bfdbfe; }
    .dark .cp-band--light .cp-band-quote { color: #d1d5db; border-color: #93c5fd; }

    .cp-band-title { flex: 0 1 420px; min-width: 0; margin: 0; line-height: 1.2; }
    .cp-band-eyebrow {
        display: block; font-size: 1.1rem; font-weight: 800;
        letter-spacing: .3em; text-transform: uppercase; margin-bottom: 10px;
    }
    .cp-band--dark .cp-band-eyebrow { color: rgba(255, 255, 255, .7); }
    .cp-band--light .cp-band-eyebrow { color: #1e3a8a; }
    .dark .cp-band--light .cp-band-eyebrow { color: #93c5fd; }
    .cp-band-word {
        display: block; font-size: clamp(2.25rem, 3.8vw, 3.25rem);
        font-weight: 600; letter-spacing: -.02em; line-height: 1.2;
        padding: 0 .06em .06em 0;
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .cp-band--dark .cp-band-word { background-image: linear-gradient(90deg, #fff7d6 0%, #fbbf24 55%, #f59e0b 100%); }
    .cp-band--light .cp-band-word { background-image: linear-gradient(90deg, #93a9cf 0%, #1e3a8a 70%); }
    .dark .cp-band--light .cp-band-word { background-image: linear-gradient(90deg, #93c5fd 0%, #3b82f6 100%); }

    @media (max-width: 767px) {
        .cp-bands { gap: 14px; }
        .cp-band { padding: 40px 0; }
        .cp-band-inner, .cp-band-inner--flip {
            flex-direction: column-reverse; align-items: flex-start; gap: 16px;
        }
        .cp-band-copy { max-width: none; }
        .cp-band-text { font-size: 1.15rem; }
        .cp-band-title { flex-basis: auto; }
        .cp-band-eyebrow { font-size: .95rem; }
        .cp-band-features li { font-size: 1rem; }
        .cp-band-quote { font-size: 1.1rem; }
    }

    /* ============================================================
       Section styles shared with the Mission & Vision and Core Values pages.
       Palette: navy #1e3a8a / #172554, gold #fbbf24 / #f59e0b, greys.
       Headings are divs with role="heading"; the ones below are given
       the site's title font (Cinzel) explicitly. The universal `*` rule
       in custom-fixes.css sets the body font on every element.
       ============================================================ */
    main .cp-heading, main .cp-cta-heading {
        font-family: var(--vvu-title-font);
        font-weight: var(--vvu-title-weight);
        letter-spacing: normal;
    }

    .cp-section { padding: 88px 0; }
    .cp-section--white { background: #fff; }
    .dark .cp-section--white { background: #111827; }

    .cp-head { max-width: 820px; margin: 0 auto 56px; text-align: center; }
    .cp-kicker {
        display: inline-block; margin-bottom: 14px;
        font-size: 1rem; font-weight: 700; letter-spacing: .28em;
        text-transform: uppercase; color: #b45309;
    }
    .dark .cp-kicker { color: #fbbf24; }
    .cp-heading {
        color: #1e3a8a; font-size: clamp(2.25rem, 4.5vw, 3.5rem);
        line-height: 1.15;
    }
    .dark .cp-heading { color: #fff; }
    .cp-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .cp-lead {
        margin: 22px 0 0; color: #4b5563;
        font-size: 1.4rem; line-height: 1.6; font-weight: 400;
    }
    .dark .cp-lead { color: #9ca3af; }

    /* Campus facilities: white cards with a navy quarter-circle in the
       bottom-right corner holding a thin line icon (same as the M&V Four
       Pillars cards). The bottom padding keeps the text clear of it. */
    .cp-cards {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px; max-width: 1200px; margin: 0 auto;
    }
    .cp-card {
        position: relative; overflow: hidden;
        display: flex; flex-direction: column;
        padding: 32px 28px 112px; border-radius: 22px;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .cp-card:hover { transform: translateY(-6px); box-shadow: 0 28px 50px -28px rgba(15, 23, 42, .5); }
    .dark .cp-card { background: #1f2937; border-color: #374151; }
    .cp-card-title {
        color: #1e3a8a; font-size: 1.75rem; font-weight: 700;
        line-height: 1.2; letter-spacing: -.01em; margin-bottom: 16px;
    }
    .cp-card-title::after {
        content: ""; display: block; width: 40px; height: 3px;
        border-radius: 3px; background: #fbbf24; margin-top: 12px;
    }
    .dark .cp-card-title { color: #bfdbfe; }
    .cp-card-text {
        margin: 0; color: #4b5563;
        font-size: 1.35rem; line-height: 1.6; font-weight: 500;
    }
    .dark .cp-card-text { color: #d1d5db; }
    .cp-card-corner {
        position: absolute; right: 0; bottom: 0;
        width: 96px; height: 96px;
        background: #1e3a8a; border-top-left-radius: 100%;
        display: flex; align-items: flex-end; justify-content: flex-end;
        padding: 0 18px 18px 0;
    }
    .cp-card-corner .material-symbols-outlined {
        font-size: 38px; width: 38px; height: 38px; line-height: 1; color: #fff;
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        transition: transform .35s ease;
    }
    .cp-card:hover .cp-card-corner .material-symbols-outlined { transform: scale(1.08) rotate(-4deg); }
    .dark .cp-card-corner { background: #3b82f6; }

    /* Call to action: Vision-band gradient with soft circles */
    .cp-cta {
        position: relative; overflow: hidden; padding: 64px 0 56px;
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .cp-cta::before, .cp-cta::after {
        content: ""; position: absolute; border-radius: 50%; pointer-events: none;
        background: rgba(255, 255, 255, .05);
    }
    .cp-cta::before { width: 520px; height: 520px; right: -180px; top: -200px; }
    .cp-cta::after { width: 360px; height: 360px; left: -140px; bottom: -180px; }
    .cp-cta .container { position: relative; z-index: 1; }
    .cp-cta-head { max-width: 820px; margin: 0 auto; text-align: center; }
    .cp-cta-heading { color: #fff; font-size: clamp(2.25rem, 4.5vw, 3.5rem); line-height: 1.15; }
    .cp-cta-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .cp-cta-lead {
        margin: 22px 0 0; color: rgba(255, 255, 255, .85);
        font-size: 1.35rem; line-height: 1.6; font-weight: 400;
    }
    .cp-cta-actions {
        display: flex; flex-wrap: wrap; gap: 14px; justify-content: center;
        margin-top: 34px;
    }
    .cp-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 28px; border-radius: 999px;
        font-size: 1.1rem; font-weight: 700; text-decoration: none;
        transition: background-color .25s ease, color .25s ease, transform .25s ease;
    }
    .cp-btn .material-symbols-outlined { font-size: 22px; }
    .cp-btn:hover { transform: translateY(-2px); }
    .cp-btn--gold { background: #fbbf24; color: #172554; box-shadow: 0 12px 24px -14px rgba(251, 191, 36, .9); }
    .cp-btn--gold:hover { background: #fcd34d; color: #172554; }
    .cp-btn--ghost { color: #fff; border: 1.5px solid rgba(255, 255, 255, .6); }
    .cp-btn--ghost:hover { background: #fff; color: #1e3a8a; }


    @media (max-width: 1023px) {
        .cp-cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .cp-section { padding: 60px 0; }
        .cp-head { margin-bottom: 36px; }
        .cp-kicker { font-size: .85rem; }
        .cp-lead, .cp-cta-lead { font-size: 1.15rem; }
        .cp-cta { padding: 48px 0 44px; }
        .cp-cta-actions .cp-btn { width: 100%; justify-content: center; }
    }
    @media (max-width: 639px) {
        .cp-cards { grid-template-columns: 1fr; gap: 14px; }
        /* Single column is wide enough to keep text beside the corner */
        .cp-card { padding: 26px 96px 30px 24px; }
        .cp-card-title { font-size: 1.5rem; }
        .cp-card-text { font-size: 1.2rem; }
        .cp-card-corner { width: 80px; height: 80px; padding: 0 14px 14px 0; }
        .cp-card-corner .material-symbols-outlined { font-size: 32px; width: 32px; height: 32px; }
    }

</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCwMQREF1DNTiVX8Mt0yT_NXwihbW7HzEPMJWSNgBQCilTtI-Pyqwx0uf9UU1yMrmyCrXnx6GTxjWDSvbYKs1wCTGuYSJMd2wgD6bECQqPP84Ec0-M-7ROpYFQ7abu2FYSfGFlKV67C1vCRZkwCpYOR8wyyFr2Hn4inae6smuiwWtZUcdoGjyb4hX0aZBacOylHmMC6mBzEJy-CcMqb-ACqd8gK33jYhXbzNUejTEVIO-hLydTXEXEKoFBlnayg56kMq5_r5-6juVQr'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-10 animate-fadeInUp">
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <span class="text-white text-lg font-bold uppercase tracking-wider"><?php echo strip_tags($page_data['hero_badge'] ?? 'Our Environment'); ?></span>
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_data['hero_title'] ?? 'The Valley View'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($page_data['hero_subtitle'] ?? 'Experience'); ?></span>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 max-w-4xl mx-auto leading-relaxed animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($page_data['hero_description'] ?? 'Explore our beautiful campuses, state-of-the-art facilities, and the vibrant community that makes Valley View University a home away from home.'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Campus Highlights Section -->
    <section class="cp-section">
        <div class="container">
            <div class="cp-head">
                <span class="cp-kicker">Our Distinction</span>
                <div class="cp-heading" role="heading" aria-level="2"><?php echo strip_tags($sections_map['highlights']['section_title'] ?? 'Why Choose VVU?'); ?></div>
                <p class="cp-lead"><?php echo strip_tags($sections_map['highlights']['section_subtitle'] ?? 'Experience a unique blend of academic rigor, international culture, and spiritual growth.'); ?></p>
            </div>
        </div>

        <!-- Full-width bands that alternate dark/light; the copy and the
             highlight title swap sides on each band. -->
        <div class="cp-bands">
            <?php
            $highlights = $items_map['highlights'] ?? [];
            foreach ($highlights as $index => $highlight):
                $is_dark = ($index % 2 === 0);
            ?>
            <div class="cp-band <?php echo $is_dark ? 'cp-band--dark' : 'cp-band--light'; ?>">
                <div class="container">
                    <div class="cp-band-inner <?php echo $is_dark ? '' : 'cp-band-inner--flip'; ?>">
                        <div class="cp-band-copy">
                            <p class="cp-band-text"><?php echo nl2br(strip_tags($highlight['item_description'])); ?></p>
                            <?php if (!empty($highlight['item_subtitle'])): ?>
                            <p class="cp-band-quote"><?php echo strip_tags($highlight['item_subtitle']); ?></p>
                            <?php endif; ?>
                        </div>
                        <!-- A div rather than h3 so the site-wide heading rules
                             (Cinzel at weight 500, !important) don't apply. -->
                        <div class="cp-band-title" role="heading" aria-level="3">
                            <span class="cp-band-eyebrow">Highlight <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                            <span class="cp-band-word"><?php echo strip_tags($highlight['item_title']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Campus Features Section -->
    <section class="cp-section cp-section--white">
        <div class="container">
            <div class="cp-head">
                <span class="cp-kicker">Facilities</span>
                <div class="cp-heading" role="heading" aria-level="2"><?php echo strip_tags($sections_map['features']['section_title'] ?? 'Life on Campus'); ?></div>
                <p class="cp-lead"><?php echo strip_tags($sections_map['features']['section_subtitle'] ?? 'Discover the facilities and standards that make VVU a leader in private education.'); ?></p>
            </div>

            <div class="cp-cards">
                <?php
                $features = $items_map['features'] ?? [];
                foreach ($features as $feature):
                ?>
                <div class="cp-card">
                    <div class="cp-card-title" role="heading" aria-level="3"><?php echo strip_tags($feature['item_title']); ?></div>
                    <p class="cp-card-text"><?php echo nl2br(strip_tags($feature['item_description'])); ?></p>
                    <span class="cp-card-corner" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($feature['item_icon'] ?? 'school'); ?></span>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section: same dark-blue gradient as the dark bands -->
    <section class="cp-cta">
        <div class="container">
            <div class="cp-cta-head">
                <div class="cp-cta-heading" role="heading" aria-level="2"><?php echo strip_tags($page_data['cta_title'] ?? 'Experience the Campus, Start Your Journey'); ?></div>
                <p class="cp-cta-lead"><?php echo strip_tags($page_data['cta_subtitle'] ?? 'Join a university that values your future as much as you do. Explore our programs and apply today.'); ?></p>
                <div class="cp-cta-actions">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="cp-btn cp-btn--gold">
                        <span class="material-symbols-outlined">how_to_reg</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="admissions.php" class="cp-btn cp-btn--ghost">
                        <span class="material-symbols-outlined">info</span>
                        Admission Info
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
