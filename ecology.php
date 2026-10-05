<?php
$page_title = "Ecological Stewardship - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM ecology_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$philosophy = $pdo->query("SELECT * FROM ecology_philosophy WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$initiatives = $pdo->query("SELECT * FROM ecology_initiatives WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$stats = $pdo->query("SELECT * FROM ecology_stats WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$cta = $pdo->query("SELECT * FROM ecology_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

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
    /* Philosophy bands (same design as Mission & Vision, in this page's greens) */
    .eco-bands { display: flex; flex-direction: column; gap: 20px; }
    .eco-band { position: relative; overflow: hidden; padding: 56px 0; }
    .eco-band--dark { background: linear-gradient(120deg, #15803d 0%, #166534 50%, #14532d 100%); }
    .eco-band--dark::after {
        content: ""; position: absolute; right: -120px; top: -120px;
        width: 360px; height: 360px; border-radius: 50%;
        background: rgba(255, 255, 255, .06); pointer-events: none;
    }
    .eco-band--light { background: linear-gradient(120deg, #e3efe6 0%, #cfe2d4 100%); }
    .dark .eco-band--light { background: linear-gradient(120deg, #1f2937 0%, #111827 100%); }

    .eco-band-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; justify-content: center;
        gap: 48px; max-width: 1100px; margin: 0 auto;
    }
    .eco-band-inner--flip { flex-direction: row-reverse; }

    .eco-band-copy { flex: 1 1 0; max-width: 620px; }
    .eco-band-text { margin: 0; font-size: 1.6rem; line-height: 1.6; font-weight: 600; }
    .eco-band-features {
        list-style: none; margin: 22px 0 0; padding: 0;
        display: flex; flex-wrap: wrap; gap: 10px;
    }
    .eco-band-features li {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 999px;
        font-size: 1.15rem; font-weight: 600;
    }
    .eco-band-features .material-symbols-outlined { font-size: 1.35rem; }
    .eco-band-quote {
        margin: 22px 0 0; padding-left: 16px;
        font-size: 1.3rem; line-height: 1.55; font-style: italic;
        border-left: 3px solid;
    }

    .eco-band--dark .eco-band-text { color: rgba(255, 255, 255, .92); }
    .eco-band--dark .eco-band-features li { background: rgba(255, 255, 255, .12); color: #fff; }
    .eco-band--dark .eco-band-features .material-symbols-outlined { color: #facc15; }
    .eco-band--dark .eco-band-quote { color: rgba(255, 255, 255, .8); border-color: #facc15; }

    .eco-band--light .eco-band-text { color: #1f2937; }
    .eco-band--light .eco-band-features li { background: rgba(22, 101, 52, .1); color: #166534; }
    .eco-band--light .eco-band-quote { color: #374151; border-color: #16a34a; }
    .dark .eco-band--light .eco-band-text { color: #e5e7eb; }
    .dark .eco-band--light .eco-band-features li { background: rgba(134, 239, 172, .12); color: #bbf7d0; }
    .dark .eco-band--light .eco-band-quote { color: #d1d5db; border-color: #4ade80; }

    .eco-band-title { flex: 0 1 auto; min-width: 0; margin: 0; line-height: 1.2; }
    .eco-band-eyebrow {
        display: block; font-size: 1.35rem; font-weight: 800;
        letter-spacing: .3em; text-transform: uppercase; margin-bottom: 2px;
    }
    .eco-band--dark .eco-band-eyebrow { color: rgba(255, 255, 255, .7); }
    .eco-band--light .eco-band-eyebrow { color: #166534; }
    .dark .eco-band--light .eco-band-eyebrow { color: #86efac; }
    /* Smaller than on Mission & Vision: titles here are longer
       ("Sustainability", "Green Campus"). */
    .eco-band-word {
        display: block; font-size: clamp(2.75rem, 5.5vw, 4.75rem);
        font-weight: 600; letter-spacing: -.03em; line-height: 1.2;
        padding: 0 .06em .06em 0; overflow-wrap: anywhere;
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; color: transparent;
    }
    .eco-band--dark .eco-band-word { background-image: linear-gradient(90deg, #4ade80 0%, #bef264 50%, #facc15 100%); }
    .eco-band--light .eco-band-word { background-image: linear-gradient(90deg, #86b896 0%, #166534 70%); }
    .dark .eco-band--light .eco-band-word { background-image: linear-gradient(90deg, #86efac 0%, #22c55e 100%); }

    @media (max-width: 767px) {
        .eco-bands { gap: 14px; }
        .eco-band { padding: 40px 0; }
        .eco-band-inner, .eco-band-inner--flip {
            flex-direction: column-reverse; align-items: flex-start; gap: 16px;
        }
        .eco-band-copy { max-width: none; }
        .eco-band-text { font-size: 1.25rem; }
        .eco-band-eyebrow { font-size: 1.1rem; }
        .eco-band-word { font-size: 2.6rem; }
        .eco-band-features li { font-size: 1rem; }
        .eco-band-quote { font-size: 1.1rem; }
    }
    /* ============================================================
       Section styles shared with the Mission & Vision and Core Values pages, in this page's greens.
       Palette: green #166534 / #14532d, yellow #facc15, greys.
       Headings are divs with role="heading"; the ones below are given
       the site's title font (Cinzel) explicitly. The universal `*` rule
       in custom-fixes.css sets the body font on every element.
       ============================================================ */
    main .eco-heading, main .eco-cta-heading {
        font-family: var(--vvu-title-font);
        font-weight: var(--vvu-title-weight);
        letter-spacing: normal;
    }

    .eco-section { padding: 88px 0; }
    .eco-section--white { background: #fff; }
    .dark .eco-section--white { background: #111827; }

    .eco-head { max-width: 820px; margin: 0 auto 56px; text-align: center; }
    .eco-kicker {
        display: inline-block; margin-bottom: 14px;
        font-size: 1rem; font-weight: 700; letter-spacing: .28em;
        text-transform: uppercase; color: #15803d;
    }
    .dark .eco-kicker { color: #4ade80; }
    .eco-heading {
        color: #166534; font-size: clamp(2.25rem, 4.5vw, 3.5rem);
        line-height: 1.15;
    }
    .dark .eco-heading { color: #fff; }
    .eco-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #facc15; margin: 20px auto 0;
    }
    .eco-lead {
        margin: 22px 0 0; color: #4b5563;
        font-size: 1.4rem; line-height: 1.6; font-weight: 400;
    }
    .dark .eco-lead { color: #9ca3af; }

    /* Initiatives: white cards with a green quarter-circle in the
       bottom-right corner holding a thin line icon (same as the M&V Four
       Pillars cards). The bottom padding keeps the text clear of it. */
    .eco-cards {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px; max-width: 1200px; margin: 0 auto;
    }
    .eco-card {
        position: relative; overflow: hidden;
        display: flex; flex-direction: column;
        padding: 32px 28px 112px; border-radius: 22px;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .eco-card:hover { transform: translateY(-6px); box-shadow: 0 28px 50px -28px rgba(15, 23, 42, .5); }
    .dark .eco-card { background: #1f2937; border-color: #374151; }
    .eco-card-title {
        color: #166534; font-size: 1.75rem; font-weight: 700;
        line-height: 1.2; letter-spacing: -.01em; margin-bottom: 16px;
    }
    .eco-card-title::after {
        content: ""; display: block; width: 40px; height: 3px;
        border-radius: 3px; background: #facc15; margin-top: 12px;
    }
    .dark .eco-card-title { color: #bbf7d0; }
    .eco-card-text {
        margin: 0; color: #4b5563;
        font-size: 1.35rem; line-height: 1.6; font-weight: 500;
    }
    .dark .eco-card-text { color: #d1d5db; }
    .eco-card-corner {
        position: absolute; right: 0; bottom: 0;
        width: 96px; height: 96px;
        background: #166534; border-top-left-radius: 100%;
        display: flex; align-items: flex-end; justify-content: flex-end;
        padding: 0 18px 18px 0;
    }
    .eco-card-corner .material-symbols-outlined {
        font-size: 38px; width: 38px; height: 38px; line-height: 1; color: #fff;
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        transition: transform .35s ease;
    }
    .eco-card:hover .eco-card-corner .material-symbols-outlined { transform: scale(1.08) rotate(-4deg); }
    .dark .eco-card-corner { background: #22c55e; }

    /* Call to action: same green gradient as the dark bands */
    .eco-cta {
        position: relative; overflow: hidden; padding: 64px 0 56px;
        background: linear-gradient(120deg, #15803d 0%, #166534 50%, #14532d 100%);
    }
    .eco-cta::before, .eco-cta::after {
        content: ""; position: absolute; border-radius: 50%; pointer-events: none;
        background: rgba(255, 255, 255, .05);
    }
    .eco-cta::before { width: 520px; height: 520px; right: -180px; top: -200px; }
    .eco-cta::after { width: 360px; height: 360px; left: -140px; bottom: -180px; }
    .eco-cta .container { position: relative; z-index: 1; }
    .eco-cta-head { max-width: 820px; margin: 0 auto; text-align: center; }
    .eco-cta-heading { color: #fff; font-size: clamp(2.25rem, 4.5vw, 3.5rem); line-height: 1.15; }
    .eco-cta-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #facc15; margin: 20px auto 0;
    }
    .eco-cta-lead {
        margin: 22px 0 0; color: rgba(255, 255, 255, .85);
        font-size: 1.35rem; line-height: 1.6; font-weight: 400;
    }
    .eco-cta-actions {
        display: flex; flex-wrap: wrap; gap: 14px; justify-content: center;
        margin-top: 34px;
    }
    .eco-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 28px; border-radius: 999px;
        font-size: 1.1rem; font-weight: 700; text-decoration: none;
        transition: background-color .25s ease, color .25s ease, transform .25s ease;
    }
    .eco-btn .material-symbols-outlined { font-size: 22px; }
    .eco-btn:hover { transform: translateY(-2px); }
    .eco-btn--gold { background: #facc15; color: #14532d; box-shadow: 0 12px 24px -14px rgba(250, 204, 21, .9); }
    .eco-btn--gold:hover { background: #fde047; color: #14532d; }
    .eco-btn--ghost { color: #fff; border: 1.5px solid rgba(255, 255, 255, .6); }
    .eco-btn--ghost:hover { background: #fff; color: #166534; }

    /* Impact stats: open columns under a thin rule */
    .eco-stats {
        display: grid; grid-template-columns: repeat(var(--eco-stat-cols, 3), minmax(0, 1fr));
        gap: 12px 36px; max-width: 1000px; margin: 0 auto;
    }
    .eco-stat {
        padding: 20px 4px 4px; text-align: center;
        border-top: 1px solid rgba(255, 255, 255, .25);
    }
    .eco-stat-value {
        display: block; color: #facc15;
        font-size: clamp(1.75rem, 3.5vw, 2.75rem); font-weight: 700; line-height: 1.1;
    }
    .eco-stat-label {
        display: block; margin-top: 8px; color: rgba(255, 255, 255, .75);
        font-size: .95rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
    }

    .eco-cta-kicker { display: block; text-align: center; color: #facc15; margin: 44px auto 18px; }

    @media (max-width: 1023px) {
        .eco-cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .eco-section { padding: 60px 0; }
        .eco-head { margin-bottom: 36px; }
        .eco-kicker { font-size: .85rem; }
        .eco-lead, .eco-cta-lead { font-size: 1.15rem; }
        .eco-cta { padding: 48px 0 44px; }
        .eco-cta-actions .eco-btn { width: 100%; justify-content: center; }
        .eco-stats { gap: 12px; }
        .eco-cta-kicker { margin-top: 32px; }
        .eco-stat-label { font-size: .7rem; letter-spacing: .08em; }
    }
    @media (max-width: 639px) {
        .eco-cards { grid-template-columns: 1fr; gap: 14px; }
        /* Single column is wide enough to keep text beside the corner */
        .eco-card { padding: 26px 96px 30px 24px; }
        .eco-card-title { font-size: 1.5rem; }
        .eco-card-text { font-size: 1.2rem; }
        .eco-card-corner { width: 80px; height: 80px; padding: 0 14px 14px 0; }
        .eco-card-corner .material-symbols-outlined { font-size: 32px; width: 32px; height: 32px; }
    }

    /* Hero subtitle ("God's Creation"). A solid bright yellow rather than
       Tailwind gradient classes: vvu-brand-icons.css recolours green
       gradient text to VVU blue, which vanished against the green photo. */
    .eco-hero-sub {
        color: #fde047 !important;
        -webkit-text-fill-color: #fde047 !important;
        background: none !important;
        text-shadow: 0 2px 12px rgba(0, 0, 0, .55);
    }

    .text-gradient-green {
        background: linear-gradient(to right, #4ade80, #facc15);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuAmDxsoRYwbAdA-K6FnHtGy5wBKf5vqZyCFrV-HUs0bGBbSYDDD3Wneaa4B3Mghrt-m8pX84m8r7qCgwcfDWVTgZ50_6SQnuA8eFAgja8xXsyydOyiQerdpRe8ByyUddDBpqrZiEkjhGqS2kqGy0E8GeQPOwbB-ubqUVSYHeioclUPe1rVhk9B5n7d1x91PPmJdcrant8ajJ6wr62nzNnnytxiWlIHbUtB4rcls1XQWOj-_Fb4eja9I6pobhorje4VNZvJg6liAcbOK'); ?>" 
                 alt="Lush Green VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-green-900/80 via-green-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-green-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'Ecological Stewardship'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-tight tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Harmony with'); ?> <br>
                    <span class="eco-hero-sub text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-semibold block mt-3"><?php echo strip_tags($hero['hero_subtitle'] ?? "God's Creation"); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'At Valley View University, we believe that caring for the environment is a sacred responsibility. Our campus is a living laboratory for sustainable development and ecological preservation.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Our Ecological Philosophy Section -->
    <section class="eco-section">
        <div class="container">
            <div class="eco-head">
                <span class="eco-kicker">How We Think</span>
                <div class="eco-heading" role="heading" aria-level="2">Our Philosophy</div>
                <p class="eco-lead">We integrate environmental stewardship into our curriculum, campus operations, and community outreach.</p>
            </div>

        </div>

        <!-- Full-width bands that alternate dark/light; the copy and the big
             title word swap sides on each band. -->
        <div class="eco-bands">
            <?php foreach ($philosophy as $index => $item):
                $is_dark = ($index % 2 === 0);
            ?>
            <div class="eco-band <?php echo $is_dark ? 'eco-band--dark' : 'eco-band--light'; ?>">
                <div class="container">
                    <div class="eco-band-inner <?php echo $is_dark ? '' : 'eco-band-inner--flip'; ?>">
                        <div class="eco-band-copy">
                            <p class="eco-band-text"><?php echo nl2br(strip_tags($item['description'])); ?></p>
                            <?php if (!empty($item['feature_1']) || !empty($item['feature_2'])): ?>
                            <ul class="eco-band-features">
                                <?php foreach (['feature_1', 'feature_2'] as $f): if (empty($item[$f])) continue; ?>
                                <li>
                                    <span class="material-symbols-outlined">check_circle</span>
                                    <?php echo strip_tags($item[$f]); ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                            <?php if (!empty($item['quote'])): ?>
                            <p class="eco-band-quote">"<?php echo strip_tags($item['quote']); ?>"</p>
                            <?php endif; ?>
                        </div>
                        <!-- A div rather than h3 so the site-wide heading rules
                             (Cinzel at weight 500, !important) don't apply. -->
                        <div class="eco-band-title" role="heading" aria-level="3">
                            <span class="eco-band-eyebrow">Principle <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                            <span class="eco-band-word"><?php echo strip_tags($item['title']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Green Initiatives in Action -->
    <section class="eco-section eco-section--white">
        <div class="container">
            <div class="eco-head">
                <span class="eco-kicker">On Our Campus</span>
                <div class="eco-heading" role="heading" aria-level="2">Initiatives in Action</div>
                <p class="eco-lead">Our commitment to the environment is visible in every corner of our campus.</p>
            </div>

            <div class="eco-cards">
                <?php foreach ($initiatives as $initiative): ?>
                <div class="eco-card">
                    <div class="eco-card-title" role="heading" aria-level="3"><?php echo strip_tags($initiative['title']); ?></div>
                    <p class="eco-card-text"><?php echo nl2br(strip_tags($initiative['description'])); ?></p>
                    <span class="eco-card-corner" aria-hidden="true">
                        <span class="material-symbols-outlined"><?php echo strip_tags($initiative['icon'] ?? 'potted_plant'); ?></span>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section with the impact stats: same green gradient as the dark bands -->
    <section class="eco-cta">
        <div class="container">
            <div class="eco-cta-head">
                <div class="eco-cta-heading" role="heading" aria-level="2">
                    <?php echo strip_tags(trim(($cta['title_white'] ?? 'Join Our Green Revolution,') . ' ' . ($cta['title_green'] ?? 'Protect Our Future'))); ?>
                </div>
                <p class="eco-cta-lead"><?php echo strip_tags($cta['description'] ?? 'Be part of a community that values the earth as much as education. Discover how you can contribute to our ecological mission.'); ?></p>
                <div class="eco-cta-actions">
                    <a href="<?php echo strip_tags($cta['button_1_link'] ?? 'student_life.php'); ?>" class="eco-btn eco-btn--gold">
                        <span class="material-symbols-outlined"><?php echo strip_tags($cta['button_1_icon'] ?? 'eco'); ?></span>
                        <?php echo strip_tags($cta['button_1_text'] ?? 'Get Involved'); ?>
                    </a>
                    <a href="<?php echo strip_tags($cta['button_2_link'] ?? 'contact_us.php'); ?>" class="eco-btn eco-btn--ghost">
                        <span class="material-symbols-outlined"><?php echo strip_tags($cta['button_2_icon'] ?? 'mail'); ?></span>
                        <?php echo strip_tags($cta['button_2_text'] ?? 'Contact Eco-Office'); ?>
                    </a>
                </div>
            </div>

            <?php if ($stats): ?>
            <span class="eco-kicker eco-cta-kicker">Our Ecological Impact</span>
            <div class="eco-stats" style="--eco-stat-cols: <?php echo max(1, min(count($stats), 4)); ?>;">
                <?php foreach ($stats as $stat): ?>
                <div class="eco-stat">
                    <span class="eco-stat-value"><?php echo strip_tags($stat['stat_value']); ?></span>
                    <span class="eco-stat-label"><?php echo strip_tags($stat['stat_label']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
