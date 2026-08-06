<?php
/**
 * Valley View University - Event Detail Page
 * Displays individual event articles with full content
 */

require_once('includes/db_connect.php');

// Get article by slug or ID
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$article = null;

try {
    if (!empty($slug)) {
        $stmt = $pdo->prepare("SELECT * FROM news_articles WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
    } elseif ($article_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM news_articles WHERE id = ? AND status = 'published'");
        $stmt->execute([$article_id]);
    }
    
    if (isset($stmt)) {
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Increment view count
    if ($article) {
        $update_stmt = $pdo->prepare("UPDATE news_articles SET views_count = views_count + 1 WHERE id = ?");
        $update_stmt->execute([$article['id']]);
    }
} catch (PDOException $e) {
    $article = null;
}

// If article not found, redirect to events page
if (!$article) {
    header("Location: events.php");
    exit();
}

// Set page title and meta
$page_title = strip_tags($article['title']) . " - University Events - Valley View University";
$meta_description = !empty($article['meta_description']) ? $article['meta_description'] : $article['excerpt'];
$active_page = "events";

// Category labels
$category_labels = [
    'news' => 'News',
    'events' => 'Events',
    'announcements' => 'Announcements',
    'press_releases' => 'Press Releases',
    'academic' => 'Academic'
];

// Get category color class
function getCategoryColor($category) {
    $colors = [
        'news' => 'news-category-news',
        'events' => 'news-category-events',
        'announcements' => 'news-category-announcements',
        'press_releases' => 'news-category-press',
        'academic' => 'news-category-academic'
    ];
    return $colors[$category] ?? 'news-category-news';
}

// Format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Get article image
function getArticleImage($image, $category) {
    if (!empty($image) && (file_exists($image) || strpos($image, 'http') === 0)) {
        return $image;
    }
    $defaults = [
        'news' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=1200&q=80',
        'events' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80',
        'announcements' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1200&q=80',
        'press_releases' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80',
        'academic' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1200&q=80'
    ];
    return $defaults[$category] ?? $defaults['news'];
}

// Fetch related events
try {
    $related_stmt = $pdo->prepare("
        SELECT id, title, slug, excerpt, featured_image, category, event_date, publish_date
        FROM news_articles 
        WHERE status = 'published' 
        AND id != ? 
        AND category = 'events'
        ORDER BY event_date DESC, publish_date DESC 
        LIMIT 3
    ");
    $related_stmt->execute([$article['id']]);
    $related_events = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent events for sidebar
    $recent_stmt = $pdo->prepare("
        SELECT id, title, slug, featured_image, category, event_date, publish_date
        FROM news_articles 
        WHERE status = 'published' AND id != ? AND category = 'events'
        ORDER BY event_date DESC, publish_date DESC 
        LIMIT 4
    ");
    $recent_stmt->execute([$article['id']]);
    $recent_events = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $related_events = [];
    $recent_events = [];
}

// Parse tags
$tags = !empty($article['tags']) ? array_map('trim', explode(',', $article['tags'])) : [];

// Calculate read time
$word_count = str_word_count(strip_tags($article['content']));
$read_time = ceil($word_count / 200); 
if ($read_time < 1) $read_time = 1;

require_once 'includes/news_helpers.php';
include 'includes/header.php';
?>

<!-- News Portal CSS -->
<link rel="stylesheet" href="css/news-portal.css">
<link rel="stylesheet" href="css/news-modern.css">
<link rel="stylesheet" href="css/news-editorial.css">
<script src="js/news-modern.js" defer></script>

<!-- SEO Meta Tags -->
<meta name="description" content="<?php echo strip_tags($meta_description); ?>">
<meta property="og:title" content="<?php echo strip_tags($article['title']); ?>">
<meta property="og:description" content="<?php echo strip_tags($meta_description); ?>">
<meta property="og:image" content="<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>">
<meta property="og:type" content="article">
<meta property="article:published_time" content="<?php echo date('c', strtotime($article['publish_date'])); ?>">

<main class="news-detail-page ed-article">
    
    <!-- ============ EVENT HEAD ============ -->
    <header class="ed-article-head">
        <div class="container">
            <div class="ed-crumbs" role="navigation" aria-label="Breadcrumb">
                <a href="index.php">Home</a>
                <i class="fa fa-angle-right sep"></i>
                <a href="events.php">Events</a>
                <i class="fa fa-angle-right sep"></i>
                <span class="current"><?php echo strip_tags($article['title']); ?></span>
            </div>

            <div class="ed-kicker">
                <span class="dot" style="background:<?php echo vvu_kicker_tone('events'); ?>"></span>
                <strong>Campus Event</strong>
                <?php if (!empty($article['event_date'])): ?>
                <span class="sep">/</span>
                <?php echo vvu_relative_date($article['event_date']); ?>
                <?php endif; ?>
            </div>

            <h1 class="ed-headline"><?php echo strip_tags($article['title']); ?></h1>

            <?php if (!empty($article['excerpt'])): ?>
            <p class="ed-standfirst"><?php echo strip_tags($article['excerpt']); ?></p>
            <?php endif; ?>

            <?php if (!empty($article['event_date']) || !empty($article['event_time']) || !empty($article['event_location'])): ?>
            <div class="ed-facts">
                <?php if (!empty($article['event_date'])): ?>
                <div class="ed-fact">
                    <span>Date</span>
                    <strong><?php echo date('l, j F Y', strtotime($article['event_date'])); ?></strong>
                </div>
                <?php endif; ?>
                <?php if (!empty($article['event_time'])): ?>
                <div class="ed-fact">
                    <span>Time</span>
                    <strong><?php echo date('g:i A', strtotime($article['event_time'])); ?></strong>
                </div>
                <?php endif; ?>
                <?php if (!empty($article['event_location'])): ?>
                <div class="ed-fact">
                    <span>Venue</span>
                    <strong><?php echo strip_tags($article['event_location']); ?></strong>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="ed-article-byline">
                <div class="ed-byline">
                    <span class="ed-avatar" style="background:<?php echo vvu_avatar_tone($article['author']); ?>">
                        <?php echo vvu_initials($article['author']); ?>
                    </span>
                    <span class="ed-byline-text">
                        <span class="ed-byline-name">Posted by <?php echo strip_tags($article['author']); ?></span>
                        <span class="ed-byline-meta"><?php echo date('j F Y', strtotime($article['publish_date'])); ?></span>
                    </span>
                </div>
                <div class="ed-stats">
                    <span><i class="fa fa-eye"></i> <?php echo number_format($article['views_count']); ?> views</span>
                </div>
            </div>
        </div>
    </header>

    <figure class="ed-article-figure">
        <div class="container">
            <div class="ed-figure-frame">
                <img src="<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>"
                     alt="<?php echo htmlspecialchars($article['title'], ENT_QUOTES); ?>">
            </div>
        </div>
    </figure>

    <!-- Article Content -->
    <section class="article-content-section">
        <div class="container">
            <div class="article-wrapper">
                <!-- Main Content -->
                <article class="article-main">
                    <!-- Article Body -->
                    <div class="article-body">
                        <?php echo $article['content']; ?>
                    </div>
                    
                    <!-- Social Share -->
                    <div class="article-share">
                        <span class="share-label">Share this event:</span>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" class="share-btn share-facebook">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>" 
                               target="_blank" class="share-btn share-twitter">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' - ' . 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" class="share-btn share-whatsapp">
                                <i class="fa fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="article-navigation">
                        <a href="events.php" class="nav-back">
                            <i class="fa fa-arrow-left"></i>
                            <span>Back to All Events</span>
                        </a>
                    </div>
                </article>
                
                <!-- Sidebar -->
                <aside class="article-sidebar">
                    <!-- Quick Info Card -->
                    <?php if ($article['category'] === 'events' && !empty($article['event_date'])): ?>
                    <div class="sidebar-card event-info-card">
                        <h4><i class="fa fa-calendar"></i> Event Information</h4>
                        <div class="event-details">
                            <div class="event-detail">
                                <span class="detail-label">Date</span>
                                <span class="detail-value"><?php echo formatDate($article['event_date']); ?></span>
                            </div>
                            <?php if (!empty($article['event_time'])): ?>
                            <div class="event-detail">
                                <span class="detail-label">Time</span>
                                <span class="detail-value"><?php echo date('g:i A', strtotime($article['event_time'])); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($article['event_location'])): ?>
                            <div class="event-detail">
                                <span class="detail-label">Location</span>
                                <span class="detail-value"><?php echo strip_tags($article['event_location']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <a href="contact_us.php" class="btn-event-register" style="display: block; text-align: center; padding: 15px; background: #f26838; color: white; border-radius: 8px; text-decoration: none; font-weight: 700; margin-top: 20px;">
                            <i class="fa fa-envelope"></i> Inquire About Event
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Other Events Widget -->
                    <?php if (!empty($recent_events)): ?>
                    <div class="sidebar-card recent-posts-card">
                        <h4><i class="fa fa-calendar"></i> Other Events</h4>
                        <div class="recent-posts-list">
                            <?php foreach ($recent_events as $recent): ?>
                            <a href="event_detail.php?slug=<?php echo urlencode($recent['slug']); ?>" class="recent-post-item">
                                <div class="recent-post-image">
                                    <img src="<?php echo strip_tags(getArticleImage($recent['featured_image'], $recent['category'])); ?>" alt="<?php echo strip_tags($recent['title']); ?>">
                                </div>
                                <div class="recent-post-info">
                                    <h5><?php echo strip_tags($recent['title']); ?></h5>
                                    <span class="recent-post-date"><?php echo date('M j, Y', strtotime($recent['event_date'] ?? $recent['publish_date'])); ?></span>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quick Links -->
                    <div class="sidebar-card quick-links-card">
                        <h4><i class="fa fa-link"></i> Quick Links</h4>
                        <ul class="quick-links-list">
                            <li><a href="admissions.php"><i class="fa fa-user-plus"></i> Admissions</a></li>
                            <li><a href="academic_calendar.php"><i class="fa fa-calendar"></i> Academic Calendar</a></li>
                            <li><a href="news_&_events.php"><i class="fa fa-newspaper-o"></i> Latest News</a></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    
</main>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" title="Go to top">
    <i class="fa fa-arrow-up"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });
    backToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>

<?php
include 'includes/footer.php';
?>
