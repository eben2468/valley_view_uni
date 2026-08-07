<?php
$page_title = "Valley View University";
$active_page = "home";
require_once 'includes/db_connect.php';
require_once 'includes/slider_settings.php';

// Hero slider timing (admin-controlled, see Manage Homepage → Hero Sliders)
$slider_timing = vvu_slider_settings($pdo);
$slider_default_ms = $slider_timing['interval_seconds'] * 1000;

// Fetch CMS homepage content
$cms_content = [];
try {
    $stmt = $pdo->query("SELECT section, content, image FROM homepage_content");
    while ($row = $stmt->fetch()) {
        $cms_content[$row['section']] = $row;
    }
} catch (Exception $e) {
    // Table might not exist yet
}

// Helper function to get CMS content
function getCMSContent($section, $default = '') {
    global $cms_content;
    return isset($cms_content[$section]) ? $cms_content[$section]['content'] : $default;
}

function getCMSImage($section, $default = '') {
    global $cms_content;
    return isset($cms_content[$section]) ? $cms_content[$section]['image'] : $default;
}

// Fetch homepage content from database
$sliders = $pdo->query("SELECT * FROM homepage_sliders WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$discover_cards = $pdo->query("SELECT * FROM homepage_discover_cards WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$programs = $pdo->query("SELECT * FROM homepage_programs WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$gallery = $pdo->query("SELECT * FROM homepage_gallery WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$news = $pdo->query("SELECT * FROM homepage_news WHERE is_active=1 ORDER BY display_order ASC LIMIT 4")->fetchAll();
$video = $pdo->query("SELECT * FROM homepage_video WHERE is_active=1 LIMIT 1")->fetch();

// Stats Banner & Study Options
$stats_banner = $pdo->query("SELECT * FROM homepage_stats_banner WHERE is_active=1 LIMIT 1")->fetch();
$stats_items = $pdo->query("SELECT * FROM homepage_stats_items WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$study_options = $pdo->query("SELECT * FROM homepage_study_options WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();

// Fetch section titles
$sections_data = $pdo->query("SELECT * FROM homepage_sections WHERE is_active=1")->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_data as $sec) {
    $sections[$sec['section_key']] = $sec;
}

include 'includes/header.php';
?>

<!-- SLIDER -->
<section class="home-slider-section">
    <div id="myCarousel" class="carousel" data-ride="carousel"
         data-interval="<?php echo $slider_timing['autoplay'] ? (int)$slider_default_ms : 'false'; ?>"
         data-pause="<?php echo $slider_timing['pause_on_hover'] ? 'hover' : 'false'; ?>"
         data-wrap="true">
        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <?php 
            $first = true;
            foreach ($sliders as $slider): 
                // Check if this slider has content (title, description, or buttons)
                $hasTitle = !empty(trim($slider['title']));
                $hasDescription = !empty(trim($slider['description']));
                $hasButton1 = !empty($slider['button1_text']); // Changed: Don't require link for visibility
                $hasButton2 = !empty($slider['button2_text']); 
                $hasButton3 = !empty($slider['button3_text']);
                $hasContent = $hasTitle || $hasDescription || $hasButton1 || $hasButton2 || $hasButton3;
            ?>
            <div class="item <?php echo $first ? 'active' : ''; ?><?php echo !$hasContent ? ' no-overlay' : ''; ?> pos-<?php echo strip_tags(!empty($slider['content_position']) ? $slider['content_position'] : 'middle-center'); ?>"
                 <?php if ($slider_timing['autoplay'] && !empty($slider['slide_interval'])): ?>data-interval="<?php echo (int)$slider['slide_interval'] * 1000; ?>"<?php endif; ?>>
                <img src="<?php echo strip_tags($slider['image_url']); ?>" alt="">
                <?php if ($hasContent): ?>
                <div class="carousel-caption slider-con">
                    <?php if ($hasTitle): ?>
                    <h2><?php echo strip_tags($slider['title']); ?> <?php if ($slider['highlight_text']): ?><span><?php echo strip_tags($slider['highlight_text']); ?></span><?php endif; ?></h2>
                    <?php endif; ?>
                    
                    <?php if ($hasDescription): ?>
                    <p><?php echo $slider['description']; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($hasButton1 || $hasButton2 || $hasButton3): ?>
                    <div class="slider-btn-group">
                        <?php if ($hasButton1): ?>
                            <a href="<?php echo strip_tags(!empty($slider['button1_link']) ? $slider['button1_link'] : '#'); ?>" class="bann-btn-1"><?php echo strip_tags($slider['button1_text']); ?></a>
                        <?php endif; ?>
                        <?php if ($hasButton2): ?>
                            <a href="<?php echo strip_tags(!empty($slider['button2_link']) ? $slider['button2_link'] : '#'); ?>" class="bann-btn-2"><?php echo strip_tags($slider['button2_text']); ?></a>
                        <?php endif; ?>
                        <?php if ($hasButton3): ?>
                            <a href="<?php echo strip_tags(!empty($slider['button3_link']) ? $slider['button3_link'] : '#'); ?>" class="bann-btn-3"><?php echo strip_tags($slider['button3_text']); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php 
            $first = false;
            endforeach; 
            ?>
        </div>

        <!-- Left and right controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <i class="fa fa-chevron-left slider-arr"></i>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <i class="fa fa-chevron-right slider-arr"></i>
        </a>
    </div>
</section>

<!-- DISCOVER MORE -->
<?php
// Pick a contextual icon for each discover card based on its title
function vvuDiscoverIcon($title) {
    $map = [
        'admission'  => 'fa-graduation-cap',
        'academic'   => 'fa-book',
        'student'    => 'fa-users',
        'research'   => 'fa-flask',
        'faculty'    => 'fa-user-circle',
        'library'    => 'fa-university',
        'campus'     => 'fa-map-marker',
        'facilit'    => 'fa-map-marker',
        'event'      => 'fa-calendar',
        'news'       => 'fa-calendar',
        'sport'      => 'fa-trophy',
        'alumni'     => 'fa-handshake-o',
        'contact'    => 'fa-envelope',
    ];
    $t = strtolower($title);
    foreach ($map as $needle => $icon) {
        if (strpos($t, $needle) !== false) return $icon;
    }
    return 'fa-compass';
}
?>
<section class="vvu-discover">
    <div class="container com-sp pad-bot-70">
        <div class="row">
            <div class="con-title">
                <h2><?php echo isset($sections['discover_more']) ? $sections['discover_more']['section_title'] : 'Discover <span>More</span>'; ?></h2>
                <p><?php echo isset($sections['discover_more']) ? strip_tags($sections['discover_more']['section_subtitle']) : 'Explore Valley View University\'s comprehensive academic programs, vibrant student life, and cutting-edge research opportunities.'; ?></p>
            </div>
        </div>
        <div class="vvu-discover-grid">
            <?php $d_i = 0; foreach ($discover_cards as $card): $d_i++; ?>
            <a class="vvu-dcard" href="<?php echo strip_tags($card['link_url']); ?>" style="--d:<?php echo ($d_i % 4) * 90 + intval(($d_i - 1) / 4) * 60; ?>ms">
                <div class="vvu-dcard-media">
                    <img src="<?php echo strip_tags($card['image_url']); ?>" alt="<?php echo htmlspecialchars(strip_tags($card['title']), ENT_QUOTES); ?>" loading="lazy">
                    <span class="vvu-dcard-scrim"></span>
                    <span class="vvu-dcard-sheen"></span>
                </div>
                <div class="vvu-dcard-body">
                    <span class="vvu-dcard-icon"><i class="fa <?php echo vvuDiscoverIcon($card['title']); ?>"></i></span>
                    <h3 class="vvu-dcard-title"><?php echo strip_tags($card['title']); ?></h3>
                    <span class="vvu-dcard-cta">Explore <i class="fa fa-long-arrow-right"></i></span>
                </div>
                <span class="vvu-dcard-ring"></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
/* ── Discover More (modern cards) ── */
.vvu-discover {
    background: linear-gradient(180deg, #ffffff 0%, #f5f7fb 55%, #ffffff 100%);
}
.vvu-discover .con-title { margin-bottom: 45px; }

.vvu-discover-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 24px;
}

.vvu-dcard {
    position: relative;
    display: block;
    height: 260px;
    border-radius: 18px;
    overflow: hidden;
    background: #0b1c3a;
    text-decoration: none;
    box-shadow: 0 10px 30px rgba(12, 26, 60, 0.10);
    transform: translateY(28px);
    opacity: 0;
    transition: transform .55s cubic-bezier(.2,.7,.3,1),
                opacity .55s ease,
                box-shadow .45s ease;
    will-change: transform, opacity;
}
.vvu-dcard:hover,
.vvu-dcard:focus { text-decoration: none; }

/* Scroll reveal */
.vvu-dcard.is-visible {
    opacity: 1;
    transform: translateY(0);
    transition-delay: var(--d, 0ms);
}

.vvu-dcard-media {
    position: absolute;
    inset: 0;
}
.vvu-dcard-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transform: scale(1.02);
    transition: transform 1.1s cubic-bezier(.2,.7,.3,1), filter .5s ease;
}
.vvu-dcard-scrim {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, rgba(4, 16, 40, 0) 30%, rgba(4, 16, 40, .55) 62%, rgba(3, 12, 32, .92) 100%),
        linear-gradient(135deg, rgba(31, 44, 115, .45) 0%, rgba(31, 44, 115, 0) 60%);
    transition: opacity .4s ease;
}
/* Diagonal light sweep on hover */
.vvu-dcard-sheen {
    position: absolute;
    top: -60%;
    left: -75%;
    width: 45%;
    height: 220%;
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.28) 50%, rgba(255,255,255,0) 100%);
    transform: rotate(18deg);
    opacity: 0;
    pointer-events: none;
}
.vvu-dcard:hover .vvu-dcard-sheen {
    opacity: 1;
    animation: vvuSheen .9s ease forwards;
}
@keyframes vvuSheen {
    from { left: -75%; }
    to   { left: 130%; }
}

.vvu-dcard-body {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 22px 20px 20px;
    z-index: 2;
}
.vvu-dcard-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    margin-bottom: 12px;
    border-radius: 12px;
    background: rgba(255, 255, 255, .14);
    border: 1px solid rgba(255, 255, 255, .28);
    -webkit-backdrop-filter: blur(6px);
    backdrop-filter: blur(6px);
    color: #fff;
    font-size: 17px;
    transform: translateY(6px);
    opacity: .92;
    transition: background .35s ease, transform .45s cubic-bezier(.2,.7,.3,1), box-shadow .35s ease;
}
.vvu-dcard-title {
    margin: 0;
    font-size: 18px;
    line-height: 1.3;
    font-weight: 700;
    color: #fff;
    letter-spacing: .3px;
    text-shadow: 0 2px 12px rgba(0, 0, 0, .35);
}
.vvu-dcard-cta {
    display: block;
    margin-top: 6px;
    font-size: 12.5px;
    font-weight: 600;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    color: #ffb692;
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transform: translateY(8px);
    transition: max-height .4s ease, opacity .35s ease, transform .45s cubic-bezier(.2,.7,.3,1);
}
.vvu-dcard-cta .fa { margin-left: 6px; transition: transform .35s ease; }

/* Animated border ring */
.vvu-dcard-ring {
    position: absolute;
    inset: 0;
    border-radius: 18px;
    border: 2px solid transparent;
    pointer-events: none;
    z-index: 3;
    transition: border-color .4s ease, box-shadow .4s ease;
}

/* Hover / focus state */
.vvu-dcard:hover,
.vvu-dcard:focus-visible {
    transform: translateY(-10px);
    box-shadow: 0 22px 46px rgba(12, 26, 60, .28);
}
.vvu-dcard:hover .vvu-dcard-media img,
.vvu-dcard:focus-visible .vvu-dcard-media img { transform: scale(1.12); }
.vvu-dcard:hover .vvu-dcard-scrim,
.vvu-dcard:focus-visible .vvu-dcard-scrim { opacity: .95; }
.vvu-dcard:hover .vvu-dcard-icon,
.vvu-dcard:focus-visible .vvu-dcard-icon {
    background: #f26838;
    border-color: #f26838;
    transform: translateY(0);
    box-shadow: 0 8px 20px rgba(242, 104, 56, .45);
}
.vvu-dcard:hover .vvu-dcard-cta,
.vvu-dcard:focus-visible .vvu-dcard-cta {
    max-height: 30px;
    opacity: 1;
    transform: translateY(0);
}
.vvu-dcard:hover .vvu-dcard-cta .fa { transform: translateX(5px); }
.vvu-dcard:hover .vvu-dcard-ring,
.vvu-dcard:focus-visible .vvu-dcard-ring {
    border-color: rgba(242, 104, 56, .85);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .12);
}
.vvu-dcard:focus-visible { outline: none; }

/* Make the first card a wide feature tile on large screens */
@media (min-width: 1200px) {
    .vvu-dcard:first-child { grid-column: span 2; }
    .vvu-dcard:first-child .vvu-dcard-title { font-size: 24px; }
    .vvu-dcard:first-child .vvu-dcard-icon { width: 48px; height: 48px; font-size: 19px; }
}

@media (max-width: 1199px) {
    .vvu-discover-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 900px) {
    .vvu-discover-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .vvu-dcard { height: 210px; }
}
@media (max-width: 480px) {
    .vvu-discover-grid { gap: 14px; }
    .vvu-dcard { height: 170px; border-radius: 14px; }
    .vvu-dcard-body { padding: 14px 13px 13px; }
    .vvu-dcard-icon { width: 34px; height: 34px; font-size: 14px; margin-bottom: 8px; border-radius: 10px; }
    .vvu-dcard-title { font-size: 14px; }
    .vvu-dcard-cta { display: none; }
}

@media (prefers-reduced-motion: reduce) {
    .vvu-dcard,
    .vvu-dcard-media img,
    .vvu-dcard-icon,
    .vvu-dcard-cta { transition: none !important; }
    .vvu-dcard { opacity: 1; transform: none; }
    .vvu-dcard:hover { transform: none; }
    .vvu-dcard-sheen { display: none; }
}
</style>

<script>
// Staggered scroll reveal for the Discover More cards
document.addEventListener('DOMContentLoaded', function () {
    var cards = document.querySelectorAll('.vvu-dcard');
    if (!cards.length) return;

    if (!('IntersectionObserver' in window)) {
        cards.forEach(function (c) { c.classList.add('is-visible'); });
        return;
    }
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    cards.forEach(function (c) { io.observe(c); });
});
</script>

<!-- STATS BANNER -->
<?php if ($stats_banner): ?>
<section class="vvu-stats-banner" <?php if (!empty($stats_banner['bg_image'])): ?>style="background-image:url('<?php echo strip_tags($stats_banner['bg_image']); ?>')"<?php endif; ?>>
    <div class="vvu-stats-overlay"></div>
    <div class="container">
        <div class="vvu-stats-inner">
            <p class="vvu-stats-text"><?php echo strip_tags($stats_banner['banner_text']); ?></p>
            <div class="vvu-stats-grid">
                <?php foreach ($stats_items as $stat): ?>
                <div class="vvu-stat-card">
                    <span class="vvu-stat-label"><?php echo strip_tags($stat['label']); ?></span>
                    <span class="vvu-stat-value" data-target="<?php echo strip_tags($stat['value']); ?>"><?php echo strip_tags($stat['value']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- STUDY OPTIONS -->
<?php if (!empty($study_options)): ?>
<section class="vvu-study-options">
    <div class="container">
        <div class="vvu-study-grid">
            <?php foreach ($study_options as $opt): ?>
            <div class="vvu-study-card">
                <h3 class="vvu-study-title" style="color:<?php echo strip_tags($opt['accent_color']); ?>"><?php echo strip_tags($opt['title']); ?></h3>
                <p class="vvu-study-desc"><?php echo strip_tags($opt['description']); ?></p>
                <div class="vvu-study-btns">
                    <?php if (!empty($opt['btn1_text'])): ?>
                    <a href="<?php echo strip_tags($opt['btn1_link']); ?>" class="vvu-study-btn vvu-study-btn-outline" style="border-color:<?php echo strip_tags($opt['accent_color']); ?>;color:<?php echo strip_tags($opt['accent_color']); ?>"><?php echo strip_tags($opt['btn1_text']); ?></a>
                    <?php endif; ?>
                    <?php if (!empty($opt['btn2_text'])): ?>
                    <a href="<?php echo strip_tags($opt['btn2_link']); ?>" class="vvu-study-btn vvu-study-btn-filled" style="background:<?php echo strip_tags($opt['accent_color']); ?>;border-color:<?php echo strip_tags($opt['accent_color']); ?>"><?php echo strip_tags($opt['btn2_text']); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
/* ── Stats Banner ── */
.vvu-stats-banner {
    position: relative;
    background: #2c3e8c;
    background-size: cover;
    background-position: center;
    padding: 60px 0 50px;
    overflow: hidden;
}
.vvu-stats-overlay {
    position: absolute;
    inset: 0;
    background: rgba(30, 40, 100, 0.85);
}
.vvu-stats-inner {
    position: relative;
    z-index: 2;
}
.vvu-stats-text {
    color: #fff;
    font-size: 1.8rem;
    line-height: 1.5;
    text-align: center;
    max-width: 1050px;
    margin: 0 auto 50px;
    font-weight: 500;
}
.vvu-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    max-width: 1100px;
    margin: 0 auto;
}
.vvu-stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 22px 24px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: transform 0.3s;
}
.vvu-stat-card:hover {
    transform: translateY(-4px);
}
.vvu-stat-label {
    font-size: 1.2rem;
    font-weight: 600;
    color: #555;
    text-transform: capitalize;
}
.vvu-stat-value {
    font-size: 4rem;
    font-weight: 900;
    color: #1a1a2e;
    line-height: 1;
}

/* ── Study Options ── */
.vvu-study-options {
    padding: 60px 0;
    background: #fff;
    border-top: 1px solid #eee;
}
.vvu-study-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 50px;
}
.vvu-study-card {
    padding: 5px 0;
}
.vvu-study-title {
    font-size: 2.2rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    margin-bottom: 18px;
}
.vvu-study-desc {
    font-size: 1.35rem;
    color: #444;
    line-height: 1.7;
    margin-bottom: 35px;
}
.vvu-study-btns {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}
.vvu-study-btn {
    padding: 16px 36px;
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s;
    display: inline-block;
}
.vvu-study-btn-outline {
    background: transparent;
    border: 2px solid;
}
.vvu-study-btn-outline:hover {
    background: currentColor;
    color: #fff !important;
}
.vvu-study-btn-filled {
    color: #fff;
    border: 2px solid transparent;
}
.vvu-study-btn-filled:hover {
    opacity: 0.85;
    transform: translateY(-2px);
    color: #fff;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .vvu-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .vvu-study-grid { grid-template-columns: 1fr; gap: 35px; }
    .vvu-stats-text { font-size: 1.05rem; padding: 0 15px; }
    .vvu-stat-value { font-size: 2rem; }
}
@media (max-width: 480px) {
    .vvu-stats-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
    .vvu-study-btns { flex-direction: column; }
    .vvu-study-btn { text-align: center; }
}
</style>

<script>
// Animate stat numbers on scroll
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('.vvu-stat-value').forEach(el => {
                    const target = parseInt(el.getAttribute('data-target').replace(/,/g, ''));
                    if (isNaN(target)) return;
                    let current = 0;
                    const step = Math.ceil(target / 60);
                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) { current = target; clearInterval(timer); }
                        el.textContent = current.toLocaleString();
                    }, 25);
                });
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });
    const banner = document.querySelector('.vvu-stats-grid');
    if (banner) observer.observe(banner);
});
</script>

<!-- POPULAR PROGRAMS -->
<section class="pop-cour">
    <div class="container com-sp pad-bot-70">
        <div class="row">
            <div class="con-title">
                <h2><?php echo isset($sections['popular_programs']) ? $sections['popular_programs']['section_title'] : 'Popular <span>Programs</span>'; ?></h2>
                <p><?php echo isset($sections['popular_programs']) ? strip_tags($sections['popular_programs']['section_subtitle']) : 'Explore our most sought-after academic programs designed to prepare you for success in your chosen field.'; ?></p>
            </div>
        </div>
        <div class="row">
            <?php
            require_once 'includes/image_helper.php';
            $half = ceil(count($programs) / 2);
            $first_column = array_slice($programs, 0, $half);
            $second_column = array_slice($programs, $half);
            ?>
            <div class="col-md-6">
                <div>
                    <?php foreach ($first_column as $prog): ?>
                    <!--POPULAR PROGRAMS-->
                    <div class="home-top-cour">
                        <!--POPULAR PROGRAMS IMAGE-->
                        <div class="col-md-3"> <img src="<?php echo htmlspecialchars(vvu_thumb(strip_tags($prog['image_url']), 400, 450)); ?>" width="400" height="450" loading="lazy" decoding="async" alt="<?php echo htmlspecialchars(strip_tags($prog['title'])); ?>"> </div>
                        <!--POPULAR PROGRAMS: CONTENT-->
                        <div class="col-md-9 home-top-cour-desc">
                            <a href="<?php echo strip_tags($prog['link_url']); ?>">
                                <h3><?php echo strip_tags($prog['title']); ?></h3>
                            </a>
                            <h4><?php echo strip_tags($prog['category']); ?></h4>
                            <p><?php echo strip_tags($prog['description']); ?></p> <span class="home-top-cour-rat"><?php echo $prog['rating']; ?></span>
                            <div class="hom-list-share">
                                <ul>
                                    <li><a href="<?php echo strip_tags($prog['button1_link']); ?>"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo strip_tags($prog['button1_text']); ?></a> </li>
                                    <li><a href="<?php echo strip_tags($prog['button2_link']); ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo strip_tags($prog['button2_text']); ?></a> </li>
                                    <li><a href="<?php echo strip_tags($prog['button3_link']); ?>"><i class="fa fa-share-alt" aria-hidden="true"></i> <?php echo strip_tags($prog['button3_text']); ?></a> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div>
                    <?php foreach ($second_column as $prog): ?>
                    <!--POPULAR PROGRAMS-->
                    <div class="home-top-cour">
                        <!--POPULAR PROGRAMS IMAGE-->
                        <div class="col-md-3"> <img src="<?php echo htmlspecialchars(vvu_thumb(strip_tags($prog['image_url']), 400, 450)); ?>" width="400" height="450" loading="lazy" decoding="async" alt="<?php echo htmlspecialchars(strip_tags($prog['title'])); ?>"> </div>
                        <!--POPULAR PROGRAMS: CONTENT-->
                        <div class="col-md-9 home-top-cour-desc">
                            <a href="<?php echo strip_tags($prog['link_url']); ?>">
                                <h3><?php echo strip_tags($prog['title']); ?></h3>
                            </a>
                            <h4><?php echo strip_tags($prog['category']); ?></h4>
                            <p><?php echo strip_tags($prog['description']); ?></p> <span class="home-top-cour-rat"><?php echo $prog['rating']; ?></span>
                            <div class="hom-list-share">
                                <ul>
                                    <li><a href="<?php echo strip_tags($prog['button1_link']); ?>"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo strip_tags($prog['button1_text']); ?></a> </li>
                                    <li><a href="<?php echo strip_tags($prog['button2_link']); ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo strip_tags($prog['button2_text']); ?></a> </li>
                                    <li><a href="<?php echo strip_tags($prog['button3_link']); ?>"><i class="fa fa-share-alt" aria-hidden="true"></i> <?php echo strip_tags($prog['button3_text']); ?></a> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Fetch latest news, events, and notices for the modern cards
try {
    // Latest News (3 items)
    $stmt_news = $pdo->query("SELECT * FROM news_articles WHERE status='published' AND category='news' ORDER BY publish_date DESC LIMIT 3");
    $home_news = $stmt_news->fetchAll();

    // Upcoming Events (3 items)
    $stmt_events = $pdo->query("SELECT * FROM news_articles WHERE status='published' AND category='events' ORDER BY publish_date DESC LIMIT 3");
    $home_events = $stmt_events->fetchAll();

    // Latest Notices (3 items)
    $stmt_notices = $pdo->query("SELECT * FROM news_articles WHERE status='published' AND category='announcements' ORDER BY publish_date DESC LIMIT 3");
    $home_notices = $stmt_notices->fetchAll();
} catch (Exception $e) {
    $home_news = $home_events = $home_notices = [];
}

// Helper to get image path
function getHImg($path, $cat) {
    if (!empty($path)) return $path;
    $defaults = [
        'news' => 'Education-Website-and-AdminPanel/images/h-res1.jpg',
        'events' => 'Education-Website-and-AdminPanel/images/h-cam.jpg',
        'announcements' => 'Education-Website-and-AdminPanel/images/h-about2.jpg'
    ];
    return $defaults[$cat] ?? 'Education-Website-and-AdminPanel/images/h-res1.jpg';
}
?>

<!-- MODERN NEWS, EVENTS & NOTICES SECTION -->
<section class="modern-news-section">
    <div class="container com-sp">
        <div class="row">
            <div class="con-title">
                <h2><?php echo isset($sections['news_events']) ? $sections['news_events']['section_title'] : 'Explore <span>Latest Updates</span>'; ?></h2>
                <p>Stay informed with the most recent news, upcoming institutional events, and official announcements from Valley View University.</p>
            </div>
        </div>
        <div class="row">
            <!-- COLUMN 1: LATEST NEWS -->
            <div class="col-md-4 modern-news-column">
                <h4>Latest News</h4>
                <div class="modern-card-list">
                    <?php if (empty($home_news)): ?>
                        <p>No news articles available.</p>
                    <?php else: foreach ($home_news as $item): ?>
                        <a href="news_detail.php?slug=<?php echo urlencode($item['slug']); ?>" class="modern-news-card">
                            <div class="card-img-box">
                                <img src="<?php echo strip_tags(getHImg($item['featured_image'], 'news')); ?>" alt="">
                            </div>
                            <div class="card-body-box">
                                <span class="card-category-badge">University News</span>
                                <h5 class="card-title-text"><?php echo strip_tags($item['title']); ?></h5>
                                <div class="card-meta-info">
                                    <span><i class="fa fa-calendar"></i> <?php echo date('M d, Y', strtotime($item['publish_date'])); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- COLUMN 2: UPCOMING EVENTS -->
            <div class="col-md-4 modern-news-column">
                <h4>Upcoming Events</h4>
                <div class="modern-card-list">
                    <?php if (empty($home_events)): ?>
                        <p>No upcoming events.</p>
                    <?php else: foreach ($home_events as $item): ?>
                        <a href="event_detail.php?slug=<?php echo urlencode($item['slug']); ?>" class="modern-news-card">
                            <div class="event-date-box">
                                <span class="event-day"><?php echo date('d', strtotime($item['event_date'] ?? $item['publish_date'])); ?></span>
                                <span class="event-month"><?php echo date('M', strtotime($item['event_date'] ?? $item['publish_date'])); ?></span>
                            </div>
                            <div class="card-body-box">
                                <span class="card-category-badge">Events</span>
                                <h5 class="card-title-text"><?php echo strip_tags($item['title']); ?></h5>
                                <div class="card-meta-info">
                                    <span><i class="fa fa-map-marker"></i> <?php echo strip_tags($item['event_location'] ?: 'VVU Campus'); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- COLUMN 3: OFFICIAL NOTICES -->
            <div class="col-md-4 modern-news-column">
                <h4>Notices</h4>
                <div class="modern-card-list">
                    <?php if (empty($home_notices)): ?>
                        <p>No official notices.</p>
                    <?php else: foreach ($home_notices as $item): ?>
                        <a href="notices_detail.php?slug=<?php echo urlencode($item['slug']); ?>" class="modern-news-card">
                            <div class="card-body-box" style="padding-left: 20px;">
                                <span class="card-category-badge" style="color: #002147;">Announcement</span>
                                <h5 class="card-title-text"><?php echo strip_tags($item['title']); ?></h5>
                                <div class="card-meta-info">
                                    <span><i class="fa fa-clock-o"></i> Posted on <?php echo date('M d, Y', strtotime($item['publish_date'])); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODERN MEDIA SECTION (GALLERY & VIDEO) -->
<?php
require_once 'includes/image_helper.php';
require_once 'includes/video_helper.php';
?>
<section class="modern-media-section">
    <div class="container">
        <div class="media-container">
            <!-- PHOTO GALLERY -->
            <div class="modern-gallery-box">
                <div class="media-head">
                    <h4>Campus Photo Gallery</h4>
                    <p>Moments from life on the Oyibi campus</p>
                </div>
                <div class="modern-gallery-grid" id="vvuGallery">
                    <?php foreach ($gallery as $i => $img):
                        $full    = strip_tags($img['image_url']);
                        $caption = strip_tags($img['caption']);
                    ?>
                        <button type="button" class="modern-gallery-item"
                                data-index="<?php echo $i; ?>"
                                data-full="<?php echo htmlspecialchars($full); ?>"
                                data-caption="<?php echo htmlspecialchars($caption); ?>"
                                aria-label="View photo: <?php echo htmlspecialchars($caption); ?>">
                            <img src="<?php echo htmlspecialchars(vvu_thumb($full, 500, 500)); ?>"
                                 alt="<?php echo htmlspecialchars($caption); ?>"
                                 width="500" height="500" loading="lazy" decoding="async">
                            <span class="gallery-item-overlay">
                                <i class="fa fa-search-plus" aria-hidden="true"></i>
                                <span class="gallery-item-caption"><?php echo htmlspecialchars($caption); ?></span>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CAMPUS VIDEO -->
            <div class="modern-video-box">
                <div class="media-head">
                    <h4>Latest Campus Video</h4>
                    <p>See the university through our lens</p>
                </div>
                <?php if ($video): ?>
                    <div class="modern-video-wrapper">
                        <div class="video-frame">
                            <iframe src="<?php echo htmlspecialchars(vvu_video_embed(strip_tags($video['video_url']))); ?>"
                                    title="<?php echo htmlspecialchars(strip_tags($video['title'])); ?>"
                                    frameborder="0" loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                        <div class="video-info">
                            <h5><?php echo strip_tags($video['title']); ?></h5>
                            <p><?php echo nl2br(strip_tags($video['description'])); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="modern-video-wrapper media-empty">
                        <i class="fa fa-video-camera" aria-hidden="true"></i>
                        <p>No video available at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- LIGHTBOX -->
    <div class="vvu-lightbox" id="vvuLightbox" role="dialog" aria-modal="true" aria-label="Photo viewer" hidden>
        <button type="button" class="vvu-lb-btn vvu-lb-close" data-lb="close" aria-label="Close viewer">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
        <button type="button" class="vvu-lb-btn vvu-lb-nav vvu-lb-prev" data-lb="prev" aria-label="Previous photo">
            <i class="fa fa-angle-left" aria-hidden="true"></i>
        </button>
        <figure class="vvu-lb-stage">
            <div class="vvu-lb-imgwrap">
                <div class="vvu-lb-spinner" aria-hidden="true"></div>
                <img id="vvuLbImg" src="" alt="">
            </div>
            <figcaption>
                <span id="vvuLbCaption"></span>
                <span id="vvuLbCounter" class="vvu-lb-counter"></span>
            </figcaption>
        </figure>
        <button type="button" class="vvu-lb-btn vvu-lb-nav vvu-lb-next" data-lb="next" aria-label="Next photo">
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </button>
    </div>
</section>

<script>
(function () {
    var grid = document.getElementById('vvuGallery');
    var box  = document.getElementById('vvuLightbox');
    if (!grid || !box) return;

    var items    = [].slice.call(grid.querySelectorAll('.modern-gallery-item'));
    var img      = document.getElementById('vvuLbImg');
    var caption  = document.getElementById('vvuLbCaption');
    var counter  = document.getElementById('vvuLbCounter');
    var wrap     = box.querySelector('.vvu-lb-imgwrap');
    var current  = 0;
    var lastFocus = null;

    function show(i) {
        current = (i + items.length) % items.length;
        var el = items[current];
        wrap.classList.add('is-loading');
        img.src = el.getAttribute('data-full');
        img.alt = el.getAttribute('data-caption') || '';
        caption.textContent = el.getAttribute('data-caption') || '';
        counter.textContent = (current + 1) + ' / ' + items.length;
    }

    img.addEventListener('load', function () { wrap.classList.remove('is-loading'); });
    img.addEventListener('error', function () { wrap.classList.remove('is-loading'); });

    function open(i) {
        lastFocus = document.activeElement;
        box.hidden = false;
        document.body.classList.add('vvu-lb-open');
        show(i);
        // let the [hidden] removal paint before transitioning in
        requestAnimationFrame(function () { box.classList.add('is-open'); });
        box.querySelector('.vvu-lb-close').focus();
    }

    function close() {
        box.classList.remove('is-open');
        document.body.classList.remove('vvu-lb-open');
        window.setTimeout(function () {
            box.hidden = true;
            img.src = '';
        }, 200);
        if (lastFocus) lastFocus.focus();
    }

    items.forEach(function (el, i) {
        el.addEventListener('click', function () { open(i); });
    });

    box.addEventListener('click', function (e) {
        var action = e.target.closest('[data-lb]');
        if (action) {
            var what = action.getAttribute('data-lb');
            if (what === 'close') close();
            if (what === 'prev')  show(current - 1);
            if (what === 'next')  show(current + 1);
            return;
        }
        // click on the backdrop (not the photo itself) closes
        if (!e.target.closest('.vvu-lb-imgwrap')) close();
    });

    document.addEventListener('keydown', function (e) {
        if (box.hidden) return;
        if (e.key === 'Escape')     close();
        if (e.key === 'ArrowLeft')  show(current - 1);
        if (e.key === 'ArrowRight') show(current + 1);
    });

    // Swipe between photos on touch devices
    var startX = null;
    box.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    box.addEventListener('touchend', function (e) {
        if (startX === null) return;
        var dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 50) show(current + (dx < 0 ? 1 : -1));
        startX = null;
    }, { passive: true });
})();
</script>

<?php
include 'includes/footer.php';
?>
