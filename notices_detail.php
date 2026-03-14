<?php
/**
 * Valley View University - Notice Detail Page
 * Displays individual individual announcements with full content
 */

require_once('includes/db_connect.php');

// Get notice by slug or ID
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

// If notice not found, redirect to notices page
if (!$article) {
    header("Location: notices.php");
    exit();
}

// Set page title and meta
$page_title = strip_tags($article['title']) . " - Notices & Announcements - Valley View University";
$meta_description = !empty($article['meta_description']) ? $article['meta_description'] : $article['excerpt'];
$active_page = "notices";

// Category labels
$category_labels = [
    'news' => 'News',
    'events' => 'Events',
    'announcements' => 'Announcement',
    'press_releases' => 'Press Release',
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
    return 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1200&q=80';
}

// Fetch related notices
try {
    $related_stmt = $pdo->prepare("
        SELECT id, title, slug, excerpt, featured_image, category, publish_date
        FROM news_articles 
        WHERE status = 'published' 
        AND id != ? 
        AND category = 'announcements'
        ORDER BY publish_date DESC 
        LIMIT 3
    ");
    $related_stmt->execute([$article['id']]);
    $related_notices = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent notices for sidebar
    $recent_stmt = $pdo->prepare("
        SELECT id, title, slug, featured_image, category, publish_date
        FROM news_articles 
        WHERE status = 'published' AND id != ? AND category = 'announcements'
        ORDER BY publish_date DESC 
        LIMIT 4
    ");
    $recent_stmt->execute([$article['id']]);
    $recent_notices = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $related_notices = [];
    $recent_notices = [];
}

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

<main class="news-detail-page">
    
    <!-- Notice Header -->
    <header class="article-header">
        <div class="article-header-bg" style="background-image: url('<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>');"></div>
        <div class="article-header-overlay"></div>
        <div class="article-header-content">
            <div class="container">
                <span class="article-category <?php echo getCategoryColor($article['category']); ?>" style="background: #e74c3c;">
                    <?php echo strip_tags($category_labels[$article['category']] ?? $article['category']); ?>
                </span>
                <h1 class="article-title"><?php echo strip_tags($article['title']); ?></h1>
                <div class="article-meta">
                    <div class="meta-item author">
                        <i class="fa fa-user-circle"></i>
                        <span>By <?php echo strip_tags($article['author']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-calendar"></i>
                        <span>Posted on <?php echo formatDate($article['publish_date']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-eye"></i>
                        <span><?php echo number_format($article['views_count']); ?> views</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Notice Content -->
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
                    
                    <!-- Social Share -->
                    <div class="article-share">
                        <span class="share-label">Share this notice:</span>
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
                        <a href="notices.php" class="nav-back">
                            <i class="fa fa-arrow-left"></i>
                            <span>Back to All Notices</span>
                        </a>
                    </div>
                </article>
                
                <!-- Sidebar -->
                <aside class="article-sidebar">
                    <!-- Quick Info Card -->
                    <div class="sidebar-card event-info-card" style="border-top-color: #e74c3c;">
                        <h4><i class="fa fa-info-circle"></i> Official Information</h4>
                        <p style="font-size: 14px; color: #666; margin-bottom: 15px;">This is an official announcement from Valley View University. Please ensure you read the details carefully as it may contain critical information regarding your studies or campus life.</p>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 10px; font-size: 14px;"><strong>Posted By:</strong> <?php echo strip_tags($article['author']); ?></li>
                            <li style="margin-bottom: 10px; font-size: 14px;"><strong>Date:</strong> <?php echo formatDate($article['publish_date']); ?></li>
                        </ul>
                        <button onclick="window.print()" class="btn-event-register" style="display: block; width: 100%; text-align: center; padding: 12px; background: #34495e; color: white; border-radius: 8px; text-decoration: none; font-weight: 700; margin-top: 20px; border: none; cursor: pointer;">
                            <i class="fa fa-print"></i> Print Notice
                        </button>
                    </div>
                    
                    <!-- Other Notices Widget -->
                    <?php if (!empty($recent_notices)): ?>
                    <div class="sidebar-card recent-posts-card">
                        <h4><i class="fa fa-bullhorn"></i> Recent Notices</h4>
                        <div class="recent-posts-list">
                            <?php foreach ($recent_notices as $recent): ?>
                            <a href="notices_detail.php?slug=<?php echo urlencode($recent['slug']); ?>" class="recent-post-item">
                                <div class="recent-post-image">
                                    <img src="<?php echo strip_tags(getArticleImage($recent['featured_image'], $recent['category'])); ?>" alt="<?php echo strip_tags($recent['title']); ?>">
                                </div>
                                <div class="recent-post-info">
                                    <h5><?php echo strip_tags($recent['title']); ?></h5>
                                    <span class="recent-post-date"><?php echo formatDate($recent['publish_date']); ?></span>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Categories -->
                    <div class="sidebar-card categories-card">
                        <h4><i class="fa fa-link"></i> Quick Navigation</h4>
                        <ul class="categories-list">
                            <li><a href="events.php"><i class="fa fa-calendar"></i> Campus Events</a></li>
                            <li><a href="news_&_events.php"><i class="fa fa-newspaper-o"></i> Latest News</a></li>
                            <li><a href="admissions.php"><i class="fa fa-graduation-cap"></i> Admissions</a></li>
                            <li><a href="contact_us.php"><i class="fa fa-envelope"></i> Contact Us</a></li>
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
