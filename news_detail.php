<?php
/**
 * Valley View University - News Article Detail Page
 * Displays individual news articles with full content
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

// If article not found, redirect to news page
if (!$article) {
    header("Location: news_&_events.php");
    exit();
}

// Set page title and meta
$page_title = strip_tags($article['title']) . " - Valley View University";
$meta_description = !empty($article['meta_description']) ? $article['meta_description'] : $article['excerpt'];
$active_page = "news";

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
    if (!empty($image) && file_exists($image)) {
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

// Fetch related articles
try {
    $related_stmt = $pdo->prepare("
        SELECT id, title, slug, excerpt, featured_image, category, publish_date
        FROM news_articles 
        WHERE status = 'published' 
        AND id != ? 
        AND (category = ? OR is_featured = 1)
        ORDER BY 
            CASE WHEN category = ? THEN 0 ELSE 1 END,
            publish_date DESC 
        LIMIT 3
    ");
    $related_stmt->execute([$article['id'], $article['category'], $article['category']]);
    $related_articles = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent news for sidebar
    $recent_stmt = $pdo->prepare("
        SELECT id, title, slug, featured_image, category, publish_date
        FROM news_articles 
        WHERE status = 'published' AND id != ?
        ORDER BY publish_date DESC 
        LIMIT 4
    ");
    $recent_stmt->execute([$article['id']]);
    $recent_articles = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $related_articles = [];
    $recent_articles = [];
}


// Parse tags
$tags = !empty($article['tags']) ? array_map('trim', explode(',', $article['tags'])) : [];

// Calculate read time
$word_count = str_word_count(strip_tags($article['content']));
$read_time = ceil($word_count / 200); // Average 200 words per minute
if ($read_time < 1) $read_time = 1;


include 'includes/header.php';
?>

<!-- News Portal CSS -->
<link rel="stylesheet" href="css/news-portal.css">

<!-- SEO Meta Tags -->
<meta name="description" content="<?php echo strip_tags($meta_description); ?>">
<meta property="og:title" content="<?php echo strip_tags($article['title']); ?>">
<meta property="og:description" content="<?php echo strip_tags($meta_description); ?>">
<meta property="og:image" content="<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>">
<meta property="og:type" content="article">
<meta property="article:published_time" content="<?php echo date('c', strtotime($article['publish_date'])); ?>">
<meta property="article:author" content="<?php echo strip_tags($article['author']); ?>">

<!-- Article Detail Page -->
<main class="news-detail-page">
    
    <!-- Article Header -->
    <header class="article-header">
        <div class="article-header-bg" style="background-image: url('<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>');"></div>
        <div class="article-header-overlay"></div>
        <div class="article-header-content">
            <div class="container">

                <span class="article-category <?php echo getCategoryColor($article['category']); ?>">
                    <?php echo strip_tags($category_labels[$article['category']] ?? $article['category']); ?>
                </span>
                <h1 class="article-title"><?php echo strip_tags($article['title']); ?></h1>
                <div class="article-meta">
                    <div class="meta-item author">
                        <div class="author-avatar">
                            <?php if (!empty($article['author_image'])): ?>
                            <img src="<?php echo strip_tags($article['author_image']); ?>" alt="<?php echo strip_tags($article['author']); ?>">
                            <?php else: ?>
                            <i class="fa fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <span>By <?php echo strip_tags($article['author']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-calendar"></i>
                        <span><?php echo formatDate($article['publish_date']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-eye"></i>
                        <span><?php echo number_format($article['views_count']); ?> views</span>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-clock-o"></i>
                        <span><?php echo $read_time; ?> min read</span>
                    </div>

                    <?php if (!empty($article['event_date'])): ?>
                    <div class="meta-item event-date">
                        <i class="fa fa-calendar-check-o"></i>
                        <span>Event: <?php echo formatDate($article['event_date']); ?>
                            <?php if (!empty($article['event_time'])): ?>
                            at <?php echo date('g:i A', strtotime($article['event_time'])); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($article['event_location'])): ?>
                    <div class="meta-item">
                        <i class="fa fa-map-marker"></i>
                        <span><?php echo strip_tags($article['event_location']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <section class="article-content-section">
        <div class="container">
            <div class="article-wrapper">
                <!-- Main Content -->
                <article class="article-main">
                    <!-- Featured Image -->
                    <figure class="article-featured-image">
                        <img src="<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>" 
                             alt="<?php echo strip_tags($article['title']); ?>">
                    </figure>
                    
                    <!-- Excerpt/Lead -->
                    <?php if (!empty($article['excerpt'])): ?>
                    <div class="article-lead">
                        <?php echo strip_tags($article['excerpt']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Article Body -->
                    <div class="article-body">
                        <?php echo $article['content']; ?>
                    </div>
                    
                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                    <div class="article-tags">
                        <span class="tags-label"><i class="fa fa-tags"></i> Tags:</span>
                        <?php foreach ($tags as $tag): ?>
                        <a href="news_&_events.php?search=<?php echo urlencode($tag); ?>" class="tag-item">
                            <?php echo strip_tags($tag); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Social Share -->
                    <div class="article-share">
                        <span class="share-label">Share this article:</span>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" 
                               class="share-btn share-facebook"
                               title="Share on Facebook">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>" 
                               target="_blank" 
                               class="share-btn share-twitter"
                               title="Share on Twitter">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($article['title']); ?>" 
                               target="_blank" 
                               class="share-btn share-linkedin"
                               title="Share on LinkedIn">
                                <i class="fa fa-linkedin"></i>
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' - ' . 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" 
                               class="share-btn share-whatsapp"
                               title="Share on WhatsApp">
                                <i class="fa fa-whatsapp"></i>
                            </a>
                            <button class="share-btn share-copy" onclick="copyLink()" title="Copy Link">
                                <i class="fa fa-link"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="article-navigation">
                        <a href="news_&_events.php" class="nav-back">
                            <i class="fa fa-arrow-left"></i>
                            <span>Back to News</span>
                        </a>
                        <a href="news_&_events.php?category=<?php echo urlencode($article['category']); ?>" class="nav-category">
                            <span>More <?php echo strip_tags($category_labels[$article['category']] ?? $article['category']); ?></span>
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
                
                <!-- Sidebar -->
                <aside class="article-sidebar">
                    <!-- Quick Info Card -->
                    <?php if ($article['category'] === 'events' && !empty($article['event_date'])): ?>
                    <div class="sidebar-card event-info-card">
                        <h4><i class="fa fa-calendar"></i> Event Details</h4>
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
                        <a href="#" class="btn-event-register">
                            <i class="fa fa-ticket"></i> Register for Event
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Recent News Widget -->
                    <?php if (!empty($recent_articles)): ?>
                    <div class="sidebar-card recent-posts-card">
                        <h4><i class="fa fa-history"></i> Recent News</h4>
                        <div class="recent-posts-list">
                            <?php foreach ($recent_articles as $recent): ?>
                            <a href="news_detail.php?slug=<?php echo urlencode($recent['slug']); ?>" class="recent-post-item">
                                <div class="recent-post-image">
                                    <img src="<?php echo strip_tags(getArticleImage($recent['featured_image'], $recent['category'])); ?>" alt="<?php echo strip_tags($recent['title']); ?>">
                                </div>
                                <div class="recent-post-info">
                                    <h5><?php echo strip_tags($recent['title']); ?></h5>
                                    <span class="recent-post-date"><?php echo date('M j, Y', strtotime($recent['publish_date'])); ?></span>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="sidebar-card categories-card">
                        <h4><i class="fa fa-folder-open"></i> Categories</h4>
                        <ul class="categories-list">
                            <li><a href="news_&_events.php?category=news"><i class="fa fa-newspaper-o"></i> News</a></li>
                            <li><a href="news_&_events.php?category=events"><i class="fa fa-calendar"></i> Events</a></li>
                            <li><a href="news_&_events.php?category=announcements"><i class="fa fa-bullhorn"></i> Announcements</a></li>
                            <li><a href="news_&_events.php?category=press_releases"><i class="fa fa-building"></i> Press Releases</a></li>
                            <li><a href="news_&_events.php?category=academic"><i class="fa fa-graduation-cap"></i> Academic</a></li>
                        </ul>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="sidebar-card quick-links-card">
                        <h4><i class="fa fa-link"></i> Quick Links</h4>
                        <ul class="quick-links-list">
                            <li><a href="admissions.php"><i class="fa fa-user-plus"></i> Apply Now</a></li>
                            <li><a href="academic_calendar.php"><i class="fa fa-calendar"></i> Academic Calendar</a></li>
                            <li><a href="contact_us.php"><i class="fa fa-envelope"></i> Contact Us</a></li>
                            <li><a href="gallery.php"><i class="fa fa-image"></i> Photo Gallery</a></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    
    <!-- Related Articles -->
    <?php if (!empty($related_articles)): ?>
    <section class="related-articles-section">
        <div class="container">
            <div class="section-header">
                <h2><i class="fa fa-bookmark"></i> Related Articles</h2>
                <a href="news_&_events.php?category=<?php echo urlencode($article['category']); ?>" class="view-all-link">
                    View All <i class="fa fa-arrow-right"></i>
                </a>
            </div>
            <div class="related-articles-grid">
                <?php foreach ($related_articles as $related): ?>
                <article class="related-article-card">
                    <a href="news_detail.php?slug=<?php echo urlencode($related['slug']); ?>">
                        <div class="related-image">
                            <img src="<?php echo strip_tags(getArticleImage($related['featured_image'], $related['category'])); ?>" 
                                 alt="<?php echo strip_tags($related['title']); ?>"
                                 loading="lazy">
                        </div>
                        <div class="related-content">
                            <span class="related-category <?php echo getCategoryColor($related['category']); ?>">
                                <?php echo strip_tags($category_labels[$related['category']] ?? $related['category']); ?>
                            </span>
                            <h3><?php echo strip_tags($related['title']); ?></h3>
                            <span class="related-date">
                                <i class="fa fa-calendar-o"></i>
                                <?php echo formatDate($related['publish_date']); ?>
                            </span>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
</main>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" title="Go to top">
    <i class="fa fa-arrow-up"></i>
</button>

<script>
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function() {
        const btn = document.querySelector('.share-copy');
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-check"></i>';
        btn.classList.add('copied');
        setTimeout(function() {
            btn.innerHTML = originalIcon;
            btn.classList.remove('copied');
        }, 2000);
    }, function(err) {
        console.error('Could not copy link: ', err);
    });
}

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
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>


<?php
include 'includes/footer.php';
?>
