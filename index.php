<?php
$page_title = "Valley View University";
$active_page = "home";
require_once 'includes/db_connect.php';
require_once 'includes/slider_settings.php';
require_once 'includes/slider_buttons.php';

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
                $buttons = vvu_slide_button_config($slider);
                $hasButtons = !empty($buttons['buttons']);
                // Buttons only belong in the caption when the editor asked for
                // them to sit with the text; every other placement puts them in
                // their own layer over the slide.
                $buttonsInCaption = $hasButtons && $buttons['position'] === 'inherit';
                $hasCaption = $hasTitle || $hasDescription || $buttonsInCaption;
                $hasContent = $hasTitle || $hasDescription || $hasButtons;
            ?>
            <div class="item <?php echo $first ? 'active' : ''; ?><?php echo !$hasContent ? ' no-overlay' : ''; ?> pos-<?php echo strip_tags(!empty($slider['content_position']) ? $slider['content_position'] : 'middle-center'); ?>"
                 <?php if ($slider_timing['autoplay'] && !empty($slider['slide_interval'])): ?>data-interval="<?php echo (int)$slider['slide_interval'] * 1000; ?>"<?php endif; ?>>
                <img src="<?php echo strip_tags($slider['image_url']); ?>" alt="">
                <?php if ($hasCaption): ?>
                <div class="carousel-caption slider-con">
                    <?php if ($hasTitle): ?>
                    <h2><?php echo strip_tags($slider['title']); ?> <?php if ($slider['highlight_text']): ?><span><?php echo strip_tags($slider['highlight_text']); ?></span><?php endif; ?></h2>
                    <?php endif; ?>
                    
                    <?php if ($hasDescription): ?>
                    <p><?php echo $slider['description']; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($buttonsInCaption) echo vvu_slide_buttons_group_html($buttons); ?>
                </div>
                <?php endif; ?>

                <?php if ($hasButtons && !$buttonsInCaption) echo vvu_slide_buttons_html($buttons); ?>
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
// Fallback line under each discover card title, picked by keyword. Only used
// until discover_card_descriptions.sql adds the editable description column.
function vvuDiscoverTagline($title) {
    $map = [
        'admission' => 'Entry requirements and how to apply',
        'academic'  => 'Schools, faculties and programmes',
        'student'   => 'Clubs, halls and campus community',
        'research'  => 'Projects, publications and units',
        'faculty'   => 'Find lecturers and staff contacts',
        'library'   => 'Books, journals and e-resources',
        'campus'    => 'Our grounds, halls and facilities',
        'facilit'   => 'Our grounds, halls and facilities',
        'event'     => "What's happening around VVU",
        'news'      => "What's happening around VVU",
        'sport'     => 'Teams, fitness and recreation',
        'alumni'    => 'Stay connected with VVU',
        'contact'   => 'Get in touch with the university',
    ];
    $t = strtolower($title);
    foreach ($map as $needle => $text) {
        if (strpos($t, $needle) !== false) return $text;
    }
    return '';
}

// Section titles are stored with markup ("Discover <span>More</span>");
// the new headings show them as plain text in one style.
function vvuSectionTitle($sections, $key, $default) {
    return isset($sections[$key]) ? trim(strip_tags($sections[$key]['section_title'])) : $default;
}
function vvuSectionSubtitle($sections, $key, $default) {
    return isset($sections[$key]) ? trim(strip_tags($sections[$key]['section_subtitle'])) : $default;
}
?>

<style>
/* ==========================================================================
   Homepage sections, in the style of the About pages (Mission & Vision etc.)
   Palette: navy #1e3a8a / #172554, gold #fbbf24 / #f59e0b, greys.
   Headings are divs with role="heading" given the site's title font
   (Cinzel). The universal `*` rule in custom-fixes.css sets the body font
   on every element, and the legacy theme paints every <span> grey, so
   spans get explicit colours below.
   ========================================================================== */
.hp-heading, .hp-glance-title {
    font-family: var(--vvu-title-font);
    font-weight: var(--vvu-title-weight);
    letter-spacing: normal;
}
.hp-section { padding: 88px 0; }
.hp-section--white { background: #fff; }
.hp-section--tint { background: #f5f7fb; }

.hp-head { max-width: 820px; margin: 0 auto 52px; text-align: center; }
.hp-kicker {
    display: inline-flex; align-items: center; gap: 8px; margin-bottom: 14px;
    font-size: 14px; font-weight: 700; letter-spacing: .28em;
    text-transform: uppercase; color: #b45309;
}
.hp-kicker .fa { letter-spacing: 0; color: inherit; }
.hp-heading { color: #1e3a8a; font-size: clamp(28px, 3.5vw, 42px); line-height: 1.15; }
.hp-heading::after {
    content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
    background: #fbbf24; margin: 20px auto 0;
}
.hp-lead { margin: 20px 0 0; color: #4b5563; font-size: 19px; line-height: 1.65; font-weight: 400; }


/* ── Discover More: white cards with the photo full-bleed on top, a bold
   title and one small grey meta row (description left, "Explore" right) ── */
.vvu-discover { background: linear-gradient(180deg, #ffffff 0%, #f5f7fb 55%, #ffffff 100%); }
.vvu-discover-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 24px;
}
.vvu-dcard {
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 12px;
    background: #fff;
    text-decoration: none !important;
    box-shadow: 0 6px 22px rgba(15, 23, 42, .09);
    transform: translateY(28px);
    opacity: 0;
    transition: transform .55s cubic-bezier(.2,.7,.3,1),
                opacity .55s ease,
                box-shadow .35s ease;
    will-change: transform, opacity;
}
/* Scroll reveal */
.vvu-dcard.is-visible {
    opacity: 1;
    transform: translateY(0);
    transition-delay: var(--d, 0ms);
}
.vvu-dcard-media {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16 / 10;
    background: #e5e7eb;
}
.vvu-dcard-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .8s cubic-bezier(.2,.7,.3,1);
}
.vvu-dcard-body {
    display: flex; flex-direction: column; flex: 1 1 auto;
    padding: 14px 16px 16px;
}
/* A div, not h3, so the site-wide heading font rule doesn't apply */
.vvu-dcard-title {
    color: #111827;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
}
.vvu-dcard-meta {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    margin-top: auto; padding-top: 10px;
}
.vvu-dcard-text { min-width: 0; color: #8a94a6; font-size: 12.5px; line-height: 1.4; }
.vvu-dcard-more {
    flex: 0 0 auto; display: inline-flex; align-items: center; gap: 5px;
    color: #8a94a6; font-size: 12.5px; white-space: nowrap;
    transition: color .3s ease;
}
.vvu-dcard-more .fa { color: inherit; font-size: 11px; transition: transform .3s ease; }

/* Hover / focus */
.vvu-dcard:hover,
.vvu-dcard:focus-visible {
    transform: translateY(-6px);
    box-shadow: 0 18px 36px rgba(15, 23, 42, .16);
    outline: none;
}
.vvu-dcard:hover .vvu-dcard-media img,
.vvu-dcard:focus-visible .vvu-dcard-media img { transform: scale(1.06); }
.vvu-dcard:hover .vvu-dcard-title { color: #1e3a8a; }
.vvu-dcard:hover .vvu-dcard-more { color: #b45309; }
.vvu-dcard:hover .vvu-dcard-more .fa { transform: translateX(3px); }
.vvu-dcard:focus-visible { box-shadow: 0 0 0 3px #fbbf24, 0 18px 36px rgba(15, 23, 42, .16); }

/* ── VVU at a glance (stats): original centred layout with white stat
   cards, on the navy gradient ── */
.vvu-stats-banner {
    position: relative;
    background-color: #1e3a8a;
    background-size: cover;
    background-position: center;
    padding: 60px 0 50px;
    overflow: hidden;
}
.vvu-stats-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, rgba(29, 78, 216, .92) 0%, rgba(30, 58, 138, .94) 55%, rgba(23, 37, 84, .97) 100%);
}
.vvu-stats-inner {
    position: relative;
    z-index: 2;
    text-align: center;
}
.vvu-stats-banner .hp-kicker { color: #fbbf24; }
.hp-glance-title { color: #fff; font-size: clamp(26px, 3vw, 38px); line-height: 1.2; }
.hp-glance-title::after {
    content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
    background: #fbbf24; margin: 20px auto 0;
}
.vvu-stats-text {
    color: rgba(255, 255, 255, .86);
    font-size: 1.8rem;
    line-height: 1.5;
    text-align: center;
    max-width: 1050px;
    margin: 22px auto 50px;
    font-weight: 500;
}
.vvu-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    max-width: 1100px;
    margin: 0 auto;
    text-align: left;
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
    color: #1e3a8a;
    line-height: 1;
}

/* ── Study options: a coloured back panel shows along the left and bottom
   of a white card that sits over it, with its top-left corner cut on a
   slant. ── */
.vvu-study-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 32px;
    max-width: 1200px;
    margin: 0 auto;
}
.vvu-study-card {
    position: relative;
    padding: 0 0 18px 44px;
}
.vvu-study-back {
    position: absolute;
    left: 0; right: 18px; top: 34px; bottom: 0;
    border-radius: 24px;
    background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 60%, #172554 100%);
    box-shadow: 0 22px 40px -26px rgba(23, 37, 84, .7);
}
.vvu-study-card:nth-child(even) .vvu-study-back {
    background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 45%, #f59e0b 100%);
    box-shadow: 0 22px 40px -26px rgba(180, 83, 9, .6);
}
/* drop-shadow (not box-shadow) so the shadow follows the slanted corner */
.vvu-study-front {
    position: relative; z-index: 2;
    padding: 34px 40px 32px;
    text-align: center;
    background: #fff;
    border-radius: 24px;
    clip-path: polygon(64px 0, 100% 0, 100% 100%, 0 100%, 0 64px);
}
.vvu-study-card { filter: drop-shadow(0 18px 26px rgba(15, 23, 42, .14)); }

.vvu-study-title {
    margin: 0 0 16px;
    color: #1e3a8a;
    font-size: 1.9rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    line-height: 1.2;
}
.vvu-study-desc {
    font-size: 1.35rem;
    color: #444;
    line-height: 1.7;
    margin: 0 0 24px;
}
.vvu-study-btns {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
}
.vvu-study-btn {
    padding: 12px 24px;
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-decoration: none;
    border-radius: 999px;
    transition: all 0.3s;
    display: inline-block;
}
.vvu-study-btn-outline {
    background: transparent;
    border: 2px solid #1e3a8a;
    color: #1e3a8a;
}
.vvu-study-btn-outline:hover {
    background: #1e3a8a;
    color: #fff !important;
}
.vvu-study-btn-filled {
    background: #1e3a8a;
    color: #fff;
    border: 2px solid #1e3a8a;
}
.vvu-study-btn-filled:hover {
    opacity: 0.85;
    transform: translateY(-2px);
    color: #fff;
}

/* ── Popular programs: compact split cards of one fixed size. The photo
   panel (about 8:9, the cropper's programme shape) always fills its space,
   so the card never grows; it carries a small level chip. On the right:
   title + rating pill, a one-line description, the faculty, and the
   Learn More / View Details / Apply buttons. ── */
.hp-progs {
    display: grid;
    grid-template-columns: minmax(0, 480px) minmax(0, 1fr) minmax(0, 480px);
    gap: 18px 24px;
}
.hp-prog--left  { grid-column: 1; grid-row: var(--row); }
.hp-prog--right { grid-column: 3; grid-row: var(--row); }

/* Centre column: line, a dot per row, and the sticky programme-count hub */
.hp-progs-mid { grid-column: 2; position: relative; display: flex; justify-content: center; min-width: 0; }
.hp-progs-line {
    position: absolute; top: 0; bottom: 0; left: 50%; width: 2px; transform: translateX(-50%);
    border-radius: 2px; background: linear-gradient(180deg, rgba(30, 58, 138, .15) 0%, #1e3a8a 30%, #f59e0b 100%);
}
.hp-progs-dots {
    position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: space-around;
    align-items: center; pointer-events: none;
}
.hp-progs-dots span {
    width: 14px; height: 14px; border-radius: 50%; background: #fbbf24;
    border: 3px solid #fff; box-shadow: 0 0 0 2px #1e3a8a;
}
/* "Not sure what to study?" help card, sticky in the centre column */
.hp-progs-hub {
    position: sticky; top: 140px; align-self: flex-start; z-index: 1; margin-top: 40px;
    width: 100%; max-width: 210px; padding: 22px 18px 18px; text-align: center;
    display: flex; flex-direction: column; align-items: center;
    background: #fff; border: 1px solid #e8edf5; border-radius: 18px;
    box-shadow: 0 18px 36px -22px rgba(23, 37, 84, .45);
}
/* Navy cap along the top edge */
.hp-progs-hub::before {
    content: ""; position: absolute; left: 18px; right: 18px; top: -1px; height: 4px;
    border-radius: 0 0 4px 4px; background: #1e3a8a;
}
.hp-progs-hub-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 48px; height: 48px; border-radius: 50%; background: #1e3a8a;
}
.hp-progs-hub-icon .material-symbols-outlined {
    font-size: 26px; color: #fbbf24;
    font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
}
.hp-progs-hub-title { margin-top: 12px; color: #1e3a8a; font-size: 15px; font-weight: 700; line-height: 1.3; }
.hp-progs-hub-text { margin: 6px 0 0; color: #6b7280; font-size: 12.5px; line-height: 1.5; }
.hp-progs-hub-btn {
    display: block; width: 100%; margin-top: 14px; padding: 9px 12px; border-radius: 10px;
    background: #1e3a8a; color: #fff !important; font-size: 12.5px; font-weight: 700;
    text-decoration: none !important; transition: background-color .25s ease;
}
.hp-progs-hub-btn:hover { background: #172554; }
.hp-progs-hub-link {
    display: inline-flex; align-items: center; gap: 5px; margin-top: 10px;
    color: #b45309 !important; font-size: 12px; font-weight: 700; text-decoration: none !important;
}
.hp-progs-hub-link .fa { color: inherit; transition: transform .25s ease; }
.hp-progs-hub-link:hover .fa { transform: translateX(3px); }
/* The hub needs room; on narrower desktops keep just the line and dots */
@media (max-width: 1279px) {
    .hp-progs-hub { display: none; }
}
.hp-prog {
    position: relative; display: flex; height: 168px; overflow: hidden; border-radius: 16px;
    background: #fff; border: 1px solid #e8edf5;
    box-shadow: 0 10px 26px -20px rgba(15, 23, 42, .4);
    transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
}
.hp-prog:hover {
    transform: translateY(-4px); border-color: #c7d2e6;
    box-shadow: 0 20px 38px -24px rgba(30, 58, 138, .45);
}
.hp-prog-media {
    flex: 0 0 150px; position: relative; display: block; overflow: hidden; background: #eef1f6;
}
.hp-prog-media img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .8s ease; }
.hp-prog:hover .hp-prog-media img { transform: scale(1.07); }
/* Soft shade at the bottom of the photo so the level chip reads */
.hp-prog-media::after {
    content: ""; position: absolute; inset: 0; pointer-events: none;
    background: linear-gradient(to top, rgba(23, 37, 84, .45) 0%, transparent 45%);
}
.hp-prog-level {
    position: absolute; left: 8px; bottom: 8px; z-index: 1;
    padding: 3px 9px; border-radius: 999px; background: rgba(255, 255, 255, .92);
    color: #1e3a8a; font-size: 10.5px; font-weight: 700; letter-spacing: .04em;
}
.hp-prog-body {
    flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column;
    padding: 13px 15px 13px 16px;
}
.hp-prog-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
.hp-prog-title {
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    color: #111827 !important; font-size: 15px; font-weight: 700; line-height: 1.3;
    text-decoration: none !important; transition: color .25s ease;
}
.hp-prog:hover .hp-prog-title { color: #1e3a8a !important; }
.hp-prog-rating {
    flex: 0 0 auto; display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 8px; border-radius: 999px; background: #fef3c7;
    color: #92400e; font-size: 11.5px; font-weight: 700; line-height: 1.3;
}
.hp-prog-rating .fa { color: #f59e0b; font-size: 10px; }
.hp-prog-desc {
    margin: 3px 0 0; color: #6b7280; font-size: 12.5px; line-height: 1.4;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.hp-prog-spec { display: flex; align-items: center; gap: 8px; min-width: 0; margin-top: 9px; }
.hp-prog-spec-icon {
    flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px; border-radius: 50%; background: rgba(30, 58, 138, .08);
}
.hp-prog-spec-icon .material-symbols-outlined {
    font-size: 15px; color: #1e3a8a;
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
}
.hp-prog-spec-value {
    min-width: 0; color: #374151; font-size: 12px; font-weight: 600; line-height: 1.35;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.hp-prog-foot {
    margin-top: auto; padding-top: 10px; border-top: 1px solid #f0f3f8;
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
}
.hp-prog-more {
    display: inline-flex; align-items: center; gap: 4px;
    color: #1e3a8a !important; font-size: 12px; font-weight: 700; text-decoration: none !important;
    white-space: nowrap;
}
.hp-prog-more .fa { color: inherit; font-size: 11px; transition: transform .25s ease; }
.hp-prog-more:hover .fa { transform: translateX(3px); }
.hp-prog-btns { display: flex; gap: 6px; }
.hp-prog-btn {
    display: inline-flex; align-items: center; padding: 6px 11px; border-radius: 8px;
    font-size: 11.5px; font-weight: 700; text-decoration: none !important; white-space: nowrap;
    transition: background-color .25s ease, color .25s ease;
}
.hp-prog-btn--light { background: #f1f4f9; color: #1f2937 !important; }
.hp-prog-btn--light:hover { background: #e2e8f0; }
.hp-prog-btn--main { background: #1e3a8a; color: #fff !important; }
.hp-prog-btn--main:hover { background: #172554; }


/* ── Campus life: original gallery and video cards, new labels ── */
.hp-media-label {
    display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
    color: #1e3a8a; font-size: 20px; font-weight: 700;
}
.hp-media-label .fa {
    display: inline-flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 50%; background: #1e3a8a; color: #fff; font-size: 16px;
}
/* Photo gallery panel: mosaic grid (styles over css/custom-fixes.css) */
.hp-gal-panel {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 20px; overflow: hidden;
    box-shadow: 0 22px 44px -34px rgba(15, 23, 42, .5);
}
.hp-gal-titles { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.hp-gal-count { color: #6b7280; font-size: 12.5px; font-weight: 500; }
.hp-gal-all { border: 0; background: none; padding: 0; cursor: pointer; font-family: inherit; }
.hp-gal { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; padding: 14px; }
.hp-gal .modern-gallery-item { border-radius: 12px; box-shadow: none; }
.hp-gal .modern-gallery-item:hover { transform: none; }
.hp-gal .hp-gal-item--lead { grid-column: span 2; grid-row: span 2; aspect-ratio: auto; }
.hp-gal .hp-gal-item--extra { display: none !important; }

.hp-gal .gallery-item-overlay {
    justify-content: flex-end; align-items: flex-start; text-align: left; padding: 10px 12px;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0) 40%, rgba(15, 23, 42, .82) 100%);
}
.hp-gal-zoom {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(.85);
    width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    background: rgba(255, 255, 255, .92); color: #1e3a8a; font-size: 22px; transition: transform .3s ease;
}
.hp-gal .modern-gallery-item:hover .hp-gal-zoom { transform: translate(-50%, -50%) scale(1); }
.hp-gal .gallery-item-caption {
    color: #fff; font-size: 12px; font-weight: 600; line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
/* Lead photo: caption always shown, a little larger */
.hp-gal .hp-gal-item--lead .gallery-item-overlay { opacity: 1; padding: 16px 18px; }
.hp-gal .hp-gal-item--lead .gallery-item-caption { font-size: 15px; }
.hp-gal .hp-gal-item--lead .hp-gal-zoom { opacity: 0; transition: opacity .3s ease, transform .3s ease; }
.hp-gal .hp-gal-item--lead:hover .hp-gal-zoom { opacity: 1; }

/* "+N more photos" tile */
.hp-gal-more {
    position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;
    background: rgba(23, 37, 84, .72); transition: background .3s ease;
}
.hp-gal-item--more:hover .hp-gal-more { background: rgba(23, 37, 84, .82); }
.hp-gal-more-num { color: #fff; font-size: 26px; font-weight: 700; line-height: 1; }
.hp-gal-more-text { margin-top: 4px; color: #fbbf24; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }

.dark .hp-gal-panel { background: #1f2937; border-color: #374151; }

@media (max-width: 600px) {
    .hp-gal { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; padding: 10px; }
    .hp-gal .gallery-item-caption { display: none; }
    .hp-gal .hp-gal-item--lead .gallery-item-caption { display: -webkit-box; font-size: 13px; }
}

.media-container .video-info h5 { color: #1e3a8a; }
.media-container .video-info p { color: #4b5563; }

/* ── Responsive ── */
@media (max-width: 1100px) {
    .hp-progs { grid-template-columns: minmax(0, 560px); justify-content: center; }
    .hp-progs-mid { display: none; }
    .hp-prog--left, .hp-prog--right { grid-column: 1; grid-row: auto; }
}
@media (max-width: 1199px) {
    .vvu-discover-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 900px) {
    .vvu-discover-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
}
@media (max-width: 768px) {
    .vvu-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .vvu-study-grid { grid-template-columns: 1fr; gap: 28px; }

    .vvu-stats-text { font-size: 1.05rem; padding: 0 15px; }
    .vvu-stat-value { font-size: 2rem; }
}
@media (max-width: 767px) {
    .hp-section { padding: 60px 0; }
    .hp-head { margin-bottom: 34px; }
    .hp-kicker { font-size: 12px; }
    .hp-lead { font-size: 16px; }
}
@media (max-width: 480px) {
    .vvu-discover-grid { gap: 14px; }
    .vvu-dcard-body { padding: 10px 11px 12px; }
    .vvu-dcard-title { font-size: 14px; }
    .vvu-dcard-text { display: none; }
    .vvu-stats-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
    .vvu-study-btns { flex-direction: column; }
    .vvu-study-btn { text-align: center; }
    .vvu-study-card { padding-left: 28px; }
    .vvu-study-front { padding: 28px 24px 24px; clip-path: polygon(44px 0, 100% 0, 100% 100%, 0 100%, 0 44px); }
    .vvu-study-title { font-size: 1.5rem; }
    .vvu-study-desc { font-size: 1.2rem; }
    .hp-prog { height: auto; min-height: 156px; }
    .hp-prog-media { flex: 0 0 116px; }
    .hp-prog-body { padding: 12px 12px 12px 13px; }
    .hp-prog-title { font-size: 14px; }
    .hp-prog-foot { flex-wrap: wrap; row-gap: 8px; }
    .hp-prog-btns { width: 100%; }
    .hp-prog-btn { flex: 1 1 0; justify-content: center; }
}


/* ---- News, events & notices: three matching panels ---------------------- */
.hp-upd {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px;
    max-width: 1240px; margin: 0 auto; align-items: start;
}
.hp-upd-panel {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 20px; overflow: hidden;
    box-shadow: 0 22px 44px -34px rgba(15, 23, 42, .5);
}
.hp-upd-head {
    display: flex; align-items: center; gap: 12px; padding: 16px 18px;
    border-bottom: 1px solid #eef1f5; background: linear-gradient(180deg, #f8fafc, #fff);
}
.hp-upd-icon {
    flex: 0 0 auto; display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 12px; background: #1e3a8a;
}
.hp-upd-icon .material-symbols-outlined { font-size: 21px; color: #fff; }
.hp-upd-title {
    flex: 1; min-width: 0; color: #1e3a8a; font-family: var(--vvu-title-font); font-weight: var(--vvu-title-weight);
    font-size: 19px; line-height: 1.2; letter-spacing: normal;
}
.hp-upd-all {
    flex: 0 0 auto; display: inline-flex; align-items: center; gap: 3px;
    color: #b45309 !important; font-size: 13px; font-weight: 700; text-decoration: none !important;
}
.hp-upd-all .material-symbols-outlined { font-size: 17px; color: inherit; transition: transform .25s ease; }
.hp-upd-all:hover .material-symbols-outlined { transform: translateX(3px); }

.hp-upd-list { display: flex; flex-direction: column; padding: 8px; }

/* Shared text pieces */
.hp-upd-name {
    color: #172554; font-size: 15px; font-weight: 600; line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    transition: color .25s ease;
}
.hp-upd-name--lg { font-size: 17px; }
.hp-upd-label { color: #b45309; font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
.hp-upd-date { display: inline-flex; align-items: center; gap: 5px; color: #6b7280; font-size: 13px; font-weight: 400; }
.hp-upd-date .material-symbols-outlined { font-size: 15px; color: #9ca3af; }

/* Featured news story */
.hp-upd-feature {
    display: block; margin-bottom: 4px; border-radius: 14px; overflow: hidden;
    text-decoration: none !important; transition: background .25s ease;
}
.hp-upd-feature-media { position: relative; display: block; aspect-ratio: 16 / 9; overflow: hidden; border-radius: 14px; background: #e5e7eb; }
.hp-upd-feature-media img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .6s ease; }
.hp-upd-feature:hover .hp-upd-feature-media img { transform: scale(1.05); }
.hp-upd-chip {
    position: absolute; left: 12px; top: 12px; padding: 4px 10px; border-radius: 999px;
    background: #fbbf24; color: #172554; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
}
.hp-upd-feature-body { display: flex; flex-direction: column; gap: 6px; padding: 14px 10px 12px; }
.hp-upd-feature:hover .hp-upd-name { color: #1d4ed8; }

/* Compact rows */
.hp-upd-row {
    display: flex; align-items: center; gap: 14px; padding: 12px 10px; border-radius: 14px;
    text-decoration: none !important; transition: background .25s ease;
}
.hp-upd-row + .hp-upd-row, .hp-upd-feature + .hp-upd-row { border-top: 1px solid #f1f4f8; border-radius: 0 0 14px 14px; }
.hp-upd-row:hover { background: #f5f7fb; border-radius: 14px; }
.hp-upd-row:hover .hp-upd-name { color: #1d4ed8; }
.hp-upd-row-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
.hp-upd-thumb { flex: 0 0 76px; height: 64px; border-radius: 10px; overflow: hidden; background: #e5e7eb; }
.hp-upd-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s ease; }
.hp-upd-row:hover .hp-upd-thumb img { transform: scale(1.08); }

/* Event date tile */
.hp-upd-cal {
    flex: 0 0 64px; height: 68px; display: flex; flex-direction: column; align-items: center; justify-content: center;
    border-radius: 14px; background: linear-gradient(150deg, #1e3a8a, #172554);
    box-shadow: 0 10px 20px -14px rgba(15, 23, 42, .8);
}
.hp-upd-cal-day { color: #fff; font-size: 24px; font-weight: 700; line-height: 1; }
.hp-upd-cal-mon { margin-top: 4px; color: #fbbf24; font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }

/* Notice pin */
.hp-upd-pin {
    flex: 0 0 auto; display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 50%; background: rgba(251, 191, 36, .16);
    align-self: flex-start; margin-top: 2px;
}
.hp-upd-pin .material-symbols-outlined { font-size: 19px; color: #b45309; }
.hp-upd-go { flex: 0 0 auto; font-size: 22px; color: #9ca3af; transition: transform .25s ease, color .25s ease; }
.hp-upd-row:hover .hp-upd-go { color: #1e3a8a; transform: translateX(3px); }

.hp-upd-empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 34px 16px; text-align: center; }
.hp-upd-empty .material-symbols-outlined { font-size: 36px; color: #cbd5e1; }
.hp-upd-empty-text { color: #6b7280; font-size: 14px; }

.dark .hp-upd-panel { background: #1f2937; border-color: #374151; }
.dark .hp-upd-head { background: #1f2937; border-color: #374151; }
.dark .hp-upd-title { color: #fff; }
.dark .hp-upd-name { color: #f3f4f6; }
.dark .hp-upd-row + .hp-upd-row, .dark .hp-upd-feature + .hp-upd-row { border-color: #374151; }
.dark .hp-upd-row:hover { background: #111827; }

@media (max-width: 1100px) {
    .hp-upd { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .hp-upd-panel:first-child { grid-row: span 2; }
}
@media (max-width: 767px) {
    .hp-upd { grid-template-columns: minmax(0, 1fr); gap: 18px; }
    .hp-upd-panel:first-child { grid-row: auto; }
}
@media (prefers-reduced-motion: reduce) {
    .vvu-dcard,
    .vvu-dcard-media img,
    .vvu-dcard-more { transition: none !important; }
    .vvu-dcard { opacity: 1; transform: none; }
    .vvu-dcard:hover { transform: none; }
}
</style>

<section class="vvu-discover hp-section">
    <div class="container">
        <div class="hp-head">
            <span class="hp-kicker">Explore VVU</span>
            <div class="hp-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(vvuSectionTitle($sections, 'discover_more', 'Discover More')); ?></div>
            <p class="hp-lead"><?php echo htmlspecialchars(vvuSectionSubtitle($sections, 'discover_more', "Explore Valley View University's comprehensive academic programs, vibrant student life, and cutting-edge research opportunities.")); ?></p>
        </div>
        <div class="vvu-discover-grid">
            <?php $d_i = 0; foreach ($discover_cards as $card): $d_i++; $d_title = strip_tags($card['title']);
                // Editable in the admin panel (discover_card_descriptions.sql adds the
                // column). Before that migration runs, fall back to the built-in line.
                $d_text = array_key_exists('description', $card) ? trim(strip_tags((string) $card['description'])) : vvuDiscoverTagline($d_title);
            ?>
            <a class="vvu-dcard" href="<?php echo strip_tags($card['link_url']); ?>" style="--d:<?php echo ($d_i % 4) * 90 + intval(($d_i - 1) / 4) * 60; ?>ms">
                <div class="vvu-dcard-media">
                    <img src="<?php echo strip_tags($card['image_url']); ?>" alt="<?php echo htmlspecialchars($d_title, ENT_QUOTES); ?>" loading="lazy">
                </div>
                <div class="vvu-dcard-body">
                    <div class="vvu-dcard-title" role="heading" aria-level="3"><?php echo htmlspecialchars($d_title); ?></div>
                    <span class="vvu-dcard-meta">
                        <span class="vvu-dcard-text"><?php echo htmlspecialchars($d_text); ?></span>
                        <span class="vvu-dcard-more">Explore <i class="fa fa-long-arrow-right"></i></span>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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

<!-- VVU AT A GLANCE (STATS BANNER) -->
<?php if ($stats_banner): ?>
<section class="vvu-stats-banner" <?php if (!empty($stats_banner['bg_image'])): ?>style="background-image:url('<?php echo htmlspecialchars(strip_tags($stats_banner['bg_image'])); ?>')"<?php endif; ?>>
    <div class="vvu-stats-overlay"></div>
    <div class="container">
        <div class="vvu-stats-inner">
            <span class="hp-kicker">VVU at a Glance</span>
            <div class="hp-glance-title" role="heading" aria-level="2">A Community That Shapes the World</div>
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

<script>
// Count the stat numbers up when they scroll into view
document.addEventListener('DOMContentLoaded', function() {
    const grid = document.querySelector('.vvu-stats-grid');
    if (!grid || !('IntersectionObserver' in window)) return;
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
    observer.observe(grid);
});
</script>

<!-- STUDY OPTIONS -->
<?php if (!empty($study_options)): ?>
<section class="hp-section hp-section--white">
    <div class="container">
        <div class="hp-head">
            <span class="hp-kicker">Study With Us</span>
            <div class="hp-heading" role="heading" aria-level="2">Choose Your Path</div>
        </div>
        <!-- Each path: a coloured back panel with a white card laid over it
             whose top-left corner is cut on a slant -->
        <div class="vvu-study-grid">
            <?php foreach ($study_options as $i => $opt):
                // A mis-encoded em dash is stored as " ù " in some descriptions
                $desc = str_replace(' ù ', ' — ', strip_tags($opt['description']));
            ?>
            <div class="vvu-study-card">
                <span class="vvu-study-back" aria-hidden="true"></span>
                <div class="vvu-study-front">
                    <h3 class="vvu-study-title"><?php echo strip_tags($opt['title']); ?></h3>
                    <p class="vvu-study-desc"><?php echo htmlspecialchars($desc); ?></p>
                    <div class="vvu-study-btns">
                        <?php if (!empty($opt['btn1_text'])): ?>
                        <a href="<?php echo strip_tags($opt['btn1_link']); ?>" class="vvu-study-btn vvu-study-btn-outline"><?php echo htmlspecialchars(ucwords(strtolower(strip_tags($opt['btn1_text'])))); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($opt['btn2_text'])): ?>
                        <a href="<?php echo strip_tags($opt['btn2_link']); ?>" class="vvu-study-btn vvu-study-btn-filled"><?php echo htmlspecialchars(ucwords(strtolower(strip_tags($opt['btn2_text'])))); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- POPULAR PROGRAMS -->
<?php require_once 'includes/image_helper.php'; ?>
<section class="hp-section hp-section--tint">
    <div class="container">
        <div class="hp-head">
            <span class="hp-kicker">Academics</span>
            <div class="hp-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(vvuSectionTitle($sections, 'popular_programs', 'Popular Programs')); ?></div>
            <p class="hp-lead"><?php echo htmlspecialchars(vvuSectionSubtitle($sections, 'popular_programs', 'Explore our most sought-after academic programs designed to prepare you for success in your chosen field.')); ?></p>
        </div>
        <?php $prog_rows = max(1, (int) ceil(count($programs) / 2)); ?>
        <div class="hp-progs">
            <!-- Centre column (wide screens only): a navy-to-gold line with a
                 dot level with each row, and a hub showing the programme count -->
            <div class="hp-progs-mid" style="grid-row: 1 / span <?php echo $prog_rows; ?>;">
                <span class="hp-progs-line" aria-hidden="true"></span>
                <div class="hp-progs-dots" aria-hidden="true">
                    <?php for ($r = 0; $r < $prog_rows; $r++): ?><span></span><?php endfor; ?>
                </div>
                <div class="hp-progs-hub">
                    <span class="hp-progs-hub-icon" aria-hidden="true"><span class="material-symbols-outlined">support_agent</span></span>
                    <div class="hp-progs-hub-title" role="heading" aria-level="3">Not sure what to study?</div>
                    <p class="hp-progs-hub-text">Our admissions team will help you choose the right programme.</p>
                    <a class="hp-progs-hub-btn" href="contact_us.php">Talk to Admissions</a>
                    <a class="hp-progs-hub-link" href="academic_programs_overview.php">View all programmes <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
            <?php foreach ($programs as $prog_i => $prog):
                $p_title = strip_tags($prog['title']);
                $p_desc  = trim(strip_tags($prog['description']));
                // Skip descriptions that just repeat the title or are a stray label
                $show_desc = $p_desc !== '' && strcasecmp($p_desc, $p_title) !== 0 && strcasecmp($p_desc, 'Apply Now') !== 0;
                $learn   = strip_tags($prog['button1_link'] ?: '#');
                $details = strip_tags($prog['button2_link'] ?: $learn);
                // Study level, read from the programme name and faculty
                // Whole words, case-sensitive, so "Mathematics" is not read as "MA"
                $level = preg_match('/\b(MBA|MSc|MA|MPhil|PhD|Masters?|Doctor(ate)?|Graduate)\b/', $p_title . ' ' . $prog['category'])
                       ? 'Postgraduate' : 'Undergraduate';
            ?>
            <article class="hp-prog <?php echo $prog_i % 2 === 0 ? 'hp-prog--left' : 'hp-prog--right'; ?>" style="--row: <?php echo intdiv($prog_i, 2) + 1; ?>;">
                <a class="hp-prog-media" href="<?php echo htmlspecialchars($details); ?>" tabindex="-1" aria-hidden="true">
                    <img src="<?php echo htmlspecialchars(vvu_thumb(strip_tags($prog['image_url']), 300, 336)); ?>" width="300" height="336" loading="lazy" decoding="async" alt="">
                    <span class="hp-prog-level"><?php echo $level; ?></span>
                </a>
                <div class="hp-prog-body">
                    <div class="hp-prog-top">
                        <a class="hp-prog-title" href="<?php echo htmlspecialchars($learn); ?>"><?php echo htmlspecialchars($p_title); ?></a>
                        <?php if (trim((string) $prog['rating']) !== ''): ?>
                        <span class="hp-prog-rating" title="Rating"><i class="fa fa-star" aria-hidden="true"></i><?php echo htmlspecialchars(strip_tags($prog['rating'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($show_desc): ?>
                    <p class="hp-prog-desc" title="<?php echo htmlspecialchars($p_desc); ?>"><?php echo htmlspecialchars($p_desc); ?></p>
                    <?php endif; ?>

                    <div class="hp-prog-spec" title="<?php echo htmlspecialchars(strip_tags($prog['category'])); ?>">
                        <span class="hp-prog-spec-icon"><span class="material-symbols-outlined" aria-hidden="true">account_balance</span></span>
                        <span class="hp-prog-spec-value"><?php echo htmlspecialchars(strip_tags($prog['category'])); ?></span>
                    </div>

                    <div class="hp-prog-foot">
                        <a class="hp-prog-more" href="<?php echo htmlspecialchars($learn); ?>"><?php echo htmlspecialchars(strip_tags($prog['button1_text'] ?: 'Learn More')); ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                        <div class="hp-prog-btns">
                            <a class="hp-prog-btn hp-prog-btn--light" href="<?php echo htmlspecialchars($details); ?>"><?php echo htmlspecialchars(strip_tags($prog['button2_text'] ?: 'View Details')); ?></a>
                            <?php if (!empty($prog['button3_text'])): ?>
                            <a class="hp-prog-btn hp-prog-btn--main" href="<?php echo htmlspecialchars(strip_tags($prog['button3_link'])); ?>"><?php echo htmlspecialchars(strip_tags($prog['button3_text'])); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Fetch latest news, events, and notices for the update columns
try {
    $stmt_news = $pdo->query("SELECT * FROM news_articles WHERE status='published' AND category='news' ORDER BY publish_date DESC LIMIT 3");
    $home_news = $stmt_news->fetchAll();

    $stmt_events = $pdo->query("SELECT * FROM news_articles WHERE status='published' AND category='events' ORDER BY publish_date DESC LIMIT 3");
    $home_events = $stmt_events->fetchAll();

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
        <div class="hp-head">
            <span class="hp-kicker">What's Happening</span>
            <div class="hp-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(vvuSectionTitle($sections, 'news_events', 'Latest News & Events')); ?></div>
            <p class="hp-lead"><?php echo htmlspecialchars(vvuSectionSubtitle($sections, 'news_events', 'Stay informed with the most recent news, upcoming institutional events, and official announcements from Valley View University.')); ?></p>
        </div>
        <div class="hp-upd">
            <!-- COLUMN 1: LATEST NEWS -->
            <div class="hp-upd-panel">
                <div class="hp-upd-head">
                    <span class="hp-upd-icon"><span class="material-symbols-outlined">newspaper</span></span>
                    <div class="hp-upd-title" role="heading" aria-level="3">Latest News</div>
                    <a href="news_&_events.php" class="hp-upd-all">View all <span class="material-symbols-outlined">arrow_forward</span></a>
                </div>
                <div class="hp-upd-list">
                    <?php if (empty($home_news)): ?>
                        <div class="hp-upd-empty"><span class="material-symbols-outlined">article</span><span class="hp-upd-empty-text">No news articles available.</span></div>
                    <?php else: foreach ($home_news as $i => $item):
                        $n_img = strip_tags(getHImg($item['featured_image'], 'news'));
                        $n_title = strip_tags($item['title']);
                        $n_date = date('M d, Y', strtotime($item['publish_date']));
                        if ($i === 0): ?>
                        <a href="news_detail.php?slug=<?php echo urlencode($item['slug']); ?>" class="hp-upd-feature">
                            <span class="hp-upd-feature-media">
                                <img src="<?php echo htmlspecialchars(vvu_thumb($n_img, 640, 360)); ?>" width="640" height="360"
                                     loading="lazy" decoding="async" alt="<?php echo htmlspecialchars($n_title); ?>">
                                <span class="hp-upd-chip">University News</span>
                            </span>
                            <span class="hp-upd-feature-body">
                                <span class="hp-upd-date"><span class="material-symbols-outlined">calendar_today</span><?php echo $n_date; ?></span>
                                <span class="hp-upd-name hp-upd-name--lg"><?php echo htmlspecialchars($n_title); ?></span>
                            </span>
                        </a>
                        <?php else: ?>
                        <a href="news_detail.php?slug=<?php echo urlencode($item['slug']); ?>" class="hp-upd-row">
                            <span class="hp-upd-thumb">
                                <img src="<?php echo htmlspecialchars(vvu_thumb($n_img, 300, 300)); ?>" width="300" height="300"
                                     loading="lazy" decoding="async" alt="<?php echo htmlspecialchars($n_title); ?>">
                            </span>
                            <span class="hp-upd-row-body">
                                <span class="hp-upd-name"><?php echo htmlspecialchars($n_title); ?></span>
                                <span class="hp-upd-date"><span class="material-symbols-outlined">calendar_today</span><?php echo $n_date; ?></span>
                            </span>
                        </a>
                        <?php endif; ?>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- COLUMN 2: UPCOMING EVENTS -->
            <div class="hp-upd-panel">
                <div class="hp-upd-head">
                    <span class="hp-upd-icon"><span class="material-symbols-outlined">event</span></span>
                    <div class="hp-upd-title" role="heading" aria-level="3">Upcoming Events</div>
                    <a href="events.php" class="hp-upd-all">View all <span class="material-symbols-outlined">arrow_forward</span></a>
                </div>
                <div class="hp-upd-list">
                    <?php if (empty($home_events)): ?>
                        <div class="hp-upd-empty"><span class="material-symbols-outlined">event_busy</span><span class="hp-upd-empty-text">No upcoming events.</span></div>
                    <?php else: foreach ($home_events as $item):
                        $e_ts = strtotime($item['event_date'] ?? $item['publish_date']); ?>
                        <a href="event_detail.php?slug=<?php echo urlencode($item['slug']); ?>" class="hp-upd-row hp-upd-row--event">
                            <span class="hp-upd-cal">
                                <span class="hp-upd-cal-day"><?php echo date('d', $e_ts); ?></span>
                                <span class="hp-upd-cal-mon"><?php echo date('M', $e_ts); ?></span>
                            </span>
                            <span class="hp-upd-row-body">
                                <span class="hp-upd-label">Event</span>
                                <span class="hp-upd-name"><?php echo htmlspecialchars(strip_tags($item['title'])); ?></span>
                                <span class="hp-upd-date"><span class="material-symbols-outlined">location_on</span><?php echo htmlspecialchars(strip_tags($item['event_location'] ?: 'VVU Campus')); ?></span>
                            </span>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- COLUMN 3: OFFICIAL NOTICES -->
            <div class="hp-upd-panel">
                <div class="hp-upd-head">
                    <span class="hp-upd-icon"><span class="material-symbols-outlined">campaign</span></span>
                    <div class="hp-upd-title" role="heading" aria-level="3">Notices</div>
                    <a href="notices.php" class="hp-upd-all">View all <span class="material-symbols-outlined">arrow_forward</span></a>
                </div>
                <div class="hp-upd-list">
                    <?php if (empty($home_notices)): ?>
                        <div class="hp-upd-empty"><span class="material-symbols-outlined">notifications_off</span><span class="hp-upd-empty-text">No official notices.</span></div>
                    <?php else: foreach ($home_notices as $item): ?>
                        <a href="notices_detail.php?slug=<?php echo urlencode($item['slug']); ?>" class="hp-upd-row hp-upd-row--notice">
                            <span class="hp-upd-pin"><span class="material-symbols-outlined">push_pin</span></span>
                            <span class="hp-upd-row-body">
                                <span class="hp-upd-label">Announcement</span>
                                <span class="hp-upd-name"><?php echo htmlspecialchars(strip_tags($item['title'])); ?></span>
                                <span class="hp-upd-date"><span class="material-symbols-outlined">schedule</span>Posted <?php echo date('M d, Y', strtotime($item['publish_date'])); ?></span>
                            </span>
                            <span class="material-symbols-outlined hp-upd-go">chevron_right</span>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CAMPUS LIFE: GALLERY & VIDEO -->
<?php
require_once 'includes/video_helper.php';

// The video box plays either an uploaded file (uploads/videos/...) or a
// YouTube embed, whichever the admin chose. A row with neither — an upload
// whose file was deleted and no link to fall back on — shows the empty state
// rather than a broken player.
$video_is_upload = $video ? vvu_video_is_upload($video) : false;
$video_embed     = ($video && !$video_is_upload) ? vvu_video_embed(strip_tags($video['video_url'] ?? '')) : '';
$video_playable  = $video && ($video_is_upload || $video_embed !== '');
?>
<section class="modern-media-section">
    <div class="container">
        <div class="hp-head">
            <span class="hp-kicker">Campus Life</span>
            <div class="hp-heading" role="heading" aria-level="2">Life at VVU</div>
            <p class="hp-lead">Moments from the Oyibi campus, and the university through our lens.</p>
        </div>
        <div class="media-container">
            <!-- PHOTO GALLERY -->
            <div class="modern-gallery-box hp-gal-panel">
                <?php
                // Mosaic: one large tile + 8 small ones fill a 4 x 3 grid. Photos
                // beyond that stay in the DOM (hidden) so the lightbox still pages
                // through all of them; the last visible tile says how many remain.
                $gal_total   = count($gallery);
                $gal_visible = 9;
                $gal_extra   = max(0, $gal_total - $gal_visible);
                ?>
                <div class="hp-upd-head hp-gal-head">
                    <span class="hp-upd-icon"><span class="material-symbols-outlined">photo_camera</span></span>
                    <div class="hp-gal-titles">
                        <div class="hp-upd-title" role="heading" aria-level="3">Campus Photo Gallery</div>
                        <?php if ($gal_total): ?><span class="hp-gal-count"><?php echo $gal_total; ?> photo<?php echo $gal_total === 1 ? '' : 's'; ?></span><?php endif; ?>
                    </div>
                    <?php if ($gal_total): ?>
                    <button type="button" class="hp-upd-all hp-gal-all" id="vvuGalleryAll">View all <span class="material-symbols-outlined">arrow_forward</span></button>
                    <?php endif; ?>
                </div>
                <div class="modern-gallery-grid hp-gal" id="vvuGallery">
                    <?php foreach ($gallery as $i => $img):
                        $full    = strip_tags($img['image_url']);
                        $caption = strip_tags($img['caption']);
                        $is_more  = ($gal_extra > 0 && $i === $gal_visible - 1);
                        $is_extra = ($i >= $gal_visible);
                        $cls = 'modern-gallery-item' . ($i === 0 ? ' hp-gal-item--lead' : '') . ($is_more ? ' hp-gal-item--more' : '') . ($is_extra ? ' hp-gal-item--extra' : '');
                    ?>
                        <button type="button" class="<?php echo $cls; ?>"
                                data-index="<?php echo $i; ?>"
                                data-full="<?php echo htmlspecialchars($full); ?>"
                                data-caption="<?php echo htmlspecialchars($caption); ?>"
                                aria-label="View photo: <?php echo htmlspecialchars($caption); ?>">
                            <?php if (!$is_extra): ?>
                            <img src="<?php echo htmlspecialchars(vvu_thumb($full, $i === 0 ? 800 : 500, $i === 0 ? 800 : 500)); ?>"
                                 alt="<?php echo htmlspecialchars($caption); ?>"
                                 width="500" height="500" loading="lazy" decoding="async">
                            <?php endif; ?>
                            <?php if ($is_more): ?>
                            <span class="hp-gal-more"><span class="hp-gal-more-num">+<?php echo $gal_extra; ?></span><span class="hp-gal-more-text">more photos</span></span>
                            <?php elseif (!$is_extra): ?>
                            <span class="gallery-item-overlay">
                                <span class="material-symbols-outlined hp-gal-zoom" aria-hidden="true">zoom_in</span>
                                <?php if ($caption !== ''): ?><span class="gallery-item-caption"><?php echo htmlspecialchars($caption); ?></span><?php endif; ?>
                            </span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CAMPUS VIDEO -->
            <div class="modern-video-box">
                <div class="hp-media-label"><i class="fa fa-play"></i>Latest Campus Video</div>
                <?php if ($video_playable): ?>
                    <div class="modern-video-wrapper">
                        <div class="video-frame">
                            <?php if ($video_is_upload):
                                $video_file = strip_tags($video['video_file']); ?>
                                <video controls preload="metadata" playsinline
                                       title="<?php echo htmlspecialchars(strip_tags($video['title'])); ?>">
                                    <source src="<?php echo htmlspecialchars($video_file); ?>"
                                            type="<?php echo htmlspecialchars(vvu_video_mime($video_file)); ?>">
                                    Your browser cannot play this video.
                                    <a href="<?php echo htmlspecialchars($video_file); ?>">Download it instead</a>.
                                </video>
                            <?php else: ?>
                                <iframe src="<?php echo htmlspecialchars($video_embed); ?>"
                                        title="<?php echo htmlspecialchars(strip_tags($video['title'])); ?>"
                                        frameborder="0" loading="lazy"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                            <?php endif; ?>
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

    var viewAll = document.getElementById('vvuGalleryAll');
    if (viewAll) viewAll.addEventListener('click', function () { open(0); });

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
