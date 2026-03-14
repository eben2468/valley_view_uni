<?php
$page_title = "Valley View University";
$active_page = "home";
require_once 'includes/db_connect.php';

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
    <div id="myCarousel" class="carousel" data-ride="carousel">
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
            <div class="item <?php echo $first ? 'active' : ''; ?><?php echo !$hasContent ? ' no-overlay' : ''; ?> pos-<?php echo strip_tags(!empty($slider['content_position']) ? $slider['content_position'] : 'middle-center'); ?>">
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
<section>
    <div class="container com-sp pad-bot-70">
        <div class="row">
            <div class="con-title">
                <h2><?php echo isset($sections['discover_more']) ? $sections['discover_more']['section_title'] : 'Discover <span>More</span>'; ?></h2>
                <p><?php echo isset($sections['discover_more']) ? strip_tags($sections['discover_more']['section_subtitle']) : 'Explore Valley View University\'s comprehensive academic programs, vibrant student life, and cutting-edge research opportunities.'; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="ed-course">
                <?php foreach ($discover_cards as $card): ?>
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <div class="ed-course-in">
                        <a class="course-overlay" href="<?php echo strip_tags($card['link_url']); ?>">
                            <img src="<?php echo strip_tags($card['image_url']); ?>" alt="">
                            <span><?php echo strip_tags($card['title']); ?></span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

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
                        <div class="col-md-3"> <img src="<?php echo strip_tags($prog['image_url']); ?>" alt=""> </div>
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
                        <div class="col-md-3"> <img src="<?php echo strip_tags($prog['image_url']); ?>" alt=""> </div>
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
<section class="modern-media-section">
    <div class="container">
        <div class="media-container">
            <!-- PHOTO GALLERY -->
            <div class="modern-gallery-box">
                <h4>Campus Photo Gallery</h4>
                <div class="modern-gallery-grid">
                    <?php foreach ($gallery as $img): ?>
                        <div class="modern-gallery-item">
                            <img class="materialboxed" data-caption="<?php echo strip_tags($img['caption']); ?>" src="<?php echo strip_tags($img['image_url']); ?>" alt="">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- CAMPUS VIDEO -->
            <div class="modern-video-box">
                <h4>Latest Campus Video</h4>
                <div class="modern-video-wrapper">
                    <?php if ($video): ?>
                        <iframe src="<?php echo strip_tags($video['video_url']); ?>" width="100%" height="315" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <div class="video-info">
                            <h5><?php echo strip_tags($video['title']); ?></h5>
                            <p><?php echo nl2br(strip_tags($video['description'])); ?></p>
                        </div>
                    <?php else: ?>
                        <p>No video available at this time.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
