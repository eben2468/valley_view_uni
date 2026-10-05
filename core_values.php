<?php
$page_title = "Core Values - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM core_values_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$pillars = $pdo->query("SELECT * FROM core_values_pillars WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$actions = $pdo->query("SELECT * FROM core_values_actions WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();

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
    /* ============================================================
       Section styles shared with the Mission & Vision page.
       Palette: navy #1e3a8a / #172554, gold #fbbf24 / #f59e0b, greys.
       Headings are divs with role="heading"; the ones below are given
       the site's title font (Cinzel) explicitly. The universal `*` rule
       in custom-fixes.css sets the body font on every element.
       ============================================================ */
    main .cv-heading, main .cv-cta-heading {
        font-family: var(--vvu-title-font);
        font-weight: var(--vvu-title-weight);
        letter-spacing: normal;
    }

    .cv-section { padding: 88px 0; }
    .cv-section--white { background: #fff; }
    .dark .cv-section--white { background: #111827; }

    .cv-head { max-width: 820px; margin: 0 auto 56px; text-align: center; }
    .cv-kicker {
        display: inline-block; margin-bottom: 14px;
        font-size: 1rem; font-weight: 700; letter-spacing: .28em;
        text-transform: uppercase; color: #b45309;
    }
    .dark .cv-kicker { color: #fbbf24; }
    .cv-heading {
        color: #1e3a8a; font-size: clamp(2.25rem, 4.5vw, 3.5rem);
        line-height: 1.15;
    }
    .dark .cv-heading { color: #fff; }
    .cv-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .cv-lead {
        margin: 22px 0 0; color: #4b5563;
        font-size: 1.4rem; line-height: 1.6; font-weight: 400;
    }
    .dark .cv-lead { color: #9ca3af; }

    /* Living Our Values: white cards with a navy quarter-circle in the
       bottom-right corner holding a thin line icon (same as the Four
       Pillars cards). The bottom padding keeps the text clear of it. */
    .cv-cards {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px; max-width: 1200px; margin: 0 auto;
    }
    .cv-card {
        position: relative; overflow: hidden;
        display: flex; flex-direction: column;
        padding: 32px 28px 112px; border-radius: 22px;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .cv-card:hover { transform: translateY(-6px); box-shadow: 0 28px 50px -28px rgba(15, 23, 42, .5); }
    .dark .cv-card { background: #1f2937; border-color: #374151; }
    .cv-card-title {
        color: #1e3a8a; font-size: 1.75rem; font-weight: 700;
        line-height: 1.2; letter-spacing: -.01em; margin-bottom: 16px;
    }
    .cv-card-title::after {
        content: ""; display: block; width: 40px; height: 3px;
        border-radius: 3px; background: #fbbf24; margin-top: 12px;
    }
    .dark .cv-card-title { color: #bfdbfe; }
    .cv-card-text {
        margin: 0; color: #4b5563;
        font-size: 1.35rem; line-height: 1.6; font-weight: 500;
    }
    .dark .cv-card-text { color: #d1d5db; }
    .cv-card-corner {
        position: absolute; right: 0; bottom: 0;
        width: 96px; height: 96px;
        background: #1e3a8a; border-top-left-radius: 100%;
        display: flex; align-items: flex-end; justify-content: flex-end;
        padding: 0 18px 18px 0;
    }
    .cv-card-corner .material-symbols-outlined {
        font-size: 38px; width: 38px; height: 38px; line-height: 1; color: #fff;
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        transition: transform .35s ease;
    }
    .cv-card:hover .cv-card-corner .material-symbols-outlined { transform: scale(1.08) rotate(-4deg); }
    .dark .cv-card-corner { background: #3b82f6; }

    /* Call to action: Vision-band gradient with soft circles */
    .cv-cta {
        position: relative; overflow: hidden; padding: 64px 0 56px;
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .cv-cta::before, .cv-cta::after {
        content: ""; position: absolute; border-radius: 50%; pointer-events: none;
        background: rgba(255, 255, 255, .05);
    }
    .cv-cta::before { width: 520px; height: 520px; right: -180px; top: -200px; }
    .cv-cta::after { width: 360px; height: 360px; left: -140px; bottom: -180px; }
    .cv-cta .container { position: relative; z-index: 1; }
    .cv-cta-head { max-width: 820px; margin: 0 auto; text-align: center; }
    .cv-cta-heading { color: #fff; font-size: clamp(2.25rem, 4.5vw, 3.5rem); line-height: 1.15; }
    .cv-cta-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .cv-cta-lead {
        margin: 22px 0 0; color: rgba(255, 255, 255, .85);
        font-size: 1.35rem; line-height: 1.6; font-weight: 400;
    }
    .cv-cta-actions {
        display: flex; flex-wrap: wrap; gap: 14px; justify-content: center;
        margin-top: 34px;
    }
    .cv-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 28px; border-radius: 999px;
        font-size: 1.1rem; font-weight: 700; text-decoration: none;
        transition: background-color .25s ease, color .25s ease, transform .25s ease;
    }
    .cv-btn .material-symbols-outlined { font-size: 22px; }
    .cv-btn:hover { transform: translateY(-2px); }
    .cv-btn--gold { background: #fbbf24; color: #172554; box-shadow: 0 12px 24px -14px rgba(251, 191, 36, .9); }
    .cv-btn--gold:hover { background: #fcd34d; color: #172554; }
    .cv-btn--ghost { color: #fff; border: 1.5px solid rgba(255, 255, 255, .6); }
    .cv-btn--ghost:hover { background: #fff; color: #1e3a8a; }

    /* Stats: open columns under a thin rule, like the M&V quick links */
    .cv-stats {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px 36px; max-width: 1000px; margin: 48px auto 0;
    }
    .cv-stat {
        padding: 20px 4px 4px; text-align: center;
        border-top: 1px solid rgba(255, 255, 255, .25);
    }
    .cv-stat-value {
        display: block; color: #fbbf24;
        font-size: clamp(1.75rem, 3.5vw, 2.75rem); font-weight: 700; line-height: 1.1;
    }
    .cv-stat-label {
        display: block; margin-top: 8px; color: rgba(255, 255, 255, .75);
        font-size: .95rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
    }

    @media (max-width: 1023px) {
        .cv-cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .cv-section { padding: 60px 0; }
        .cv-head { margin-bottom: 36px; }
        .cv-kicker { font-size: .85rem; }
        .cv-lead, .cv-cta-lead { font-size: 1.15rem; }
        .cv-cta { padding: 48px 0 44px; }
        .cv-cta-actions .cv-btn { width: 100%; justify-content: center; }
        .cv-stats { gap: 12px; margin-top: 36px; }
        .cv-stat-label { font-size: .7rem; letter-spacing: .08em; }
    }
    @media (max-width: 639px) {
        .cv-cards { grid-template-columns: 1fr; gap: 14px; }
        /* Single column is wide enough to keep text beside the corner */
        .cv-card { padding: 26px 96px 30px 24px; }
        .cv-card-title { font-size: 1.5rem; }
        .cv-card-text { font-size: 1.2rem; }
        .cv-card-corner { width: 80px; height: 80px; padding: 0 14px 14px 0; }
        .cv-card-corner .material-symbols-outlined { font-size: 32px; width: 32px; height: 32px; }
    }

    .text-gradient {
        background: linear-gradient(to right, #2563eb, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    /* Core value bands (same design as the Mission & Vision page) */
    .cv-bands { display: flex; flex-direction: column; gap: 20px; }
    .cv-band { position: relative; overflow: hidden; padding: 56px 0; }
    .cv-band--dark { background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%); }
    .cv-band--dark::after {
        content: ""; position: absolute; right: -120px; top: -120px;
        width: 360px; height: 360px; border-radius: 50%;
        background: rgba(255, 255, 255, .06); pointer-events: none;
    }
    .cv-band--light { background: linear-gradient(120deg, #e0e7f1 0%, #cfd9e8 100%); }
    .dark .cv-band--light { background: linear-gradient(120deg, #1f2937 0%, #111827 100%); }

    .cv-band-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; justify-content: center;
        gap: 48px; max-width: 1100px; margin: 0 auto;
    }
    .cv-band-inner--flip { flex-direction: row-reverse; }

    .cv-band-copy { flex: 1 1 0; max-width: 640px; }
    .cv-band-text { margin: 0; font-size: 1.6rem; line-height: 1.6; font-weight: 600; }
    .cv-band-features {
        list-style: none; margin: 22px 0 0; padding: 0;
        display: flex; flex-wrap: wrap; gap: 10px;
    }
    .cv-band-features li {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 999px;
        font-size: 1.15rem; font-weight: 600;
    }
    .cv-band-features .material-symbols-outlined { font-size: 1.35rem; }
    .cv-band-quote {
        margin: 22px 0 0; padding-left: 16px;
        font-size: 1.3rem; line-height: 1.55; font-style: italic;
        border-left: 3px solid;
    }

    .cv-band--dark .cv-band-text { color: rgba(255, 255, 255, .92); }
    .cv-band--dark .cv-band-features li { background: rgba(255, 255, 255, .12); color: #fff; }
    .cv-band--dark .cv-band-features .material-symbols-outlined { color: #fbbf24; }
    .cv-band--dark .cv-band-quote { color: rgba(255, 255, 255, .8); border-color: #fbbf24; }

    .cv-band--light .cv-band-text { color: #1f2937; }
    .cv-band--light .cv-band-features li { background: rgba(30, 58, 138, .1); color: #1e3a8a; }
    .cv-band--light .cv-band-quote { color: #374151; border-color: #1e3a8a; }
    .dark .cv-band--light .cv-band-text { color: #e5e7eb; }
    .dark .cv-band--light .cv-band-features li { background: rgba(147, 197, 253, .12); color: #bfdbfe; }
    .dark .cv-band--light .cv-band-quote { color: #d1d5db; border-color: #93c5fd; }

    .cv-band-title { flex: 0 0 auto; margin: 0; line-height: 1.2; }
    .cv-band-eyebrow {
        display: block; font-size: 1.35rem; font-weight: 800;
        letter-spacing: .3em; text-transform: uppercase; margin-bottom: 2px;
    }
    .cv-band--dark .cv-band-eyebrow { color: rgba(255, 255, 255, .7); }
    .cv-band--light .cv-band-eyebrow { color: #1e3a8a; }
    .dark .cv-band--light .cv-band-eyebrow { color: #93c5fd; }
    .cv-band-word {
        display: block; font-size: clamp(3.5rem, 8vw, 6.5rem);
        font-weight: 600; letter-spacing: -.03em; line-height: 1.2;
        padding: 0 .06em .06em 0;
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .cv-band--dark .cv-band-word { background-image: linear-gradient(90deg, #fff7d6 0%, #fbbf24 55%, #f59e0b 100%); }
    .cv-band--light .cv-band-word { background-image: linear-gradient(90deg, #93a9cf 0%, #1e3a8a 70%); }
    .dark .cv-band--light .cv-band-word { background-image: linear-gradient(90deg, #93c5fd 0%, #3b82f6 100%); }

    @media (max-width: 767px) {
        .cv-bands { gap: 14px; }
        .cv-band { padding: 40px 0; }
        .cv-band-inner, .cv-band-inner--flip {
            flex-direction: column-reverse; align-items: flex-start; gap: 16px;
        }
        .cv-band-copy { max-width: none; }
        .cv-band-text { font-size: 1.25rem; }
        .cv-band-eyebrow { font-size: 1.1rem; }
        .cv-band-features li { font-size: 1rem; }
        .cv-band-quote { font-size: 1.1rem; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'Our Foundation'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Core Values'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'That Define Us'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'At Valley View University, our core values are the guiding principles that shape our culture.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- The Three Pillars Section -->
    <section class="cv-section">
        <div class="container">
            <div class="cv-head">
                <span class="cv-kicker">What Defines Us</span>
                <div class="cv-heading" role="heading" aria-level="2">The Three Pillars</div>
                <p class="cv-lead">These fundamental values form the cornerstone of our identity and guide every aspect of university life.</p>
            </div>
        </div>

        <!-- Full-width bands that alternate dark/light; the copy and the big
             value word swap sides on each band. -->
        <div class="cv-bands">
            <?php foreach ($pillars as $index => $pillar):
                $is_dark = ($index % 2 === 0);
            ?>
            <div class="cv-band <?php echo $is_dark ? 'cv-band--dark' : 'cv-band--light'; ?>">
                <div class="container">
                    <div class="cv-band-inner <?php echo $is_dark ? '' : 'cv-band-inner--flip'; ?>">
                        <div class="cv-band-copy">
                            <p class="cv-band-text"><?php echo nl2br(strip_tags($pillar['description'])); ?></p>
                            <?php if (!empty($pillar['feature_1']) || !empty($pillar['feature_2'])): ?>
                            <ul class="cv-band-features">
                                <?php foreach (['feature_1', 'feature_2'] as $f): if (empty($pillar[$f])) continue; ?>
                                <li>
                                    <span class="material-symbols-outlined">check_circle</span>
                                    <?php echo strip_tags($pillar[$f]); ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                            <?php if (!empty($pillar['quote'])): ?>
                            <p class="cv-band-quote">"<?php echo strip_tags($pillar['quote']); ?>"</p>
                            <?php endif; ?>
                        </div>
                        <!-- A div rather than h3 so the site-wide heading rules
                             (Cinzel at weight 500, !important) don't apply. -->
                        <div class="cv-band-title" role="heading" aria-level="3">
                            <span class="cv-band-eyebrow">Value <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                            <span class="cv-band-word"><?php echo strip_tags($pillar['title']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Values in Action Section -->
    <section class="cv-section cv-section--white">
        <div class="container">
            <div class="cv-head">
                <span class="cv-kicker">Values in Action</span>
                <div class="cv-heading" role="heading" aria-level="2">Living Our Values</div>
                <p class="cv-lead">Our core values aren't just words on a page—they're the principles we live by in every aspect of university life.</p>
            </div>

            <div class="cv-cards">
                <?php foreach ($actions as $action): ?>
                <div class="cv-card">
                    <div class="cv-card-title" role="heading" aria-level="3"><?php echo strip_tags($action['title']); ?></div>
                    <p class="cv-card-text"><?php echo strip_tags($action['description']); ?></p>
                    <span class="cv-card-corner" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($action['icon'] ?? 'school'); ?></span>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section: same dark-blue gradient as the Excellence band -->
    <section class="cv-cta">
        <div class="container">
            <div class="cv-cta-head">
                <div class="cv-cta-heading" role="heading" aria-level="2">Embrace Our Values, Join Our Community</div>
                <p class="cv-cta-lead">Be part of a university that stands for excellence, integrity, and service. Discover how our core values can shape your future.</p>
                <div class="cv-cta-actions">
                    <a href="about_us.php" class="cv-btn cv-btn--gold">
                        <span class="material-symbols-outlined">info</span>
                        Learn More About VVU
                    </a>
                    <a href="apply.php" class="cv-btn cv-btn--ghost">
                        <span class="material-symbols-outlined">how_to_reg</span>
                        Apply Now
                    </a>
                </div>
            </div>

            <div class="cv-stats">
                <div class="cv-stat">
                    <span class="cv-stat-value">100%</span>
                    <span class="cv-stat-label">Commitment</span>
                </div>
                <div class="cv-stat">
                    <span class="cv-stat-value">Values</span>
                    <span class="cv-stat-label">Driven Culture</span>
                </div>
                <div class="cv-stat">
                    <span class="cv-stat-value">24/7</span>
                    <span class="cv-stat-label">Living Principles</span>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
