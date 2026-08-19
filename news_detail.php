<?php
/**
 * Valley View University - News Article Detail Page
 * Displays individual news articles with full content
 */

require_once('includes/db_connect.php');
require_once('includes/news_helpers.php');

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
$meta_description = !empty($article['meta_description']) ? $article['meta_description'] : vvu_html_to_text($article['excerpt']);
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


// Fetch additional gallery images shown at the end of the article
$article_gallery = [];
try {
    $gallery_stmt = $pdo->prepare("
        SELECT image_path, caption
        FROM news_article_images
        WHERE article_id = ?
        ORDER BY display_order ASC, id ASC
    ");
    $gallery_stmt->execute([$article['id']]);
    $article_gallery = $gallery_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $article_gallery = []; // Table may not exist yet
}

// Parse tags
$tags = !empty($article['tags']) ? array_map('trim', explode(',', $article['tags'])) : [];

// Calculate read time
$word_count = str_word_count(strip_tags($article['content']));
$read_time = ceil($word_count / 200); // Average 200 words per minute
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
<meta property="article:author" content="<?php echo strip_tags($article['author']); ?>">

<!-- Article Detail Page -->
<main class="news-detail-page ed-article">
    
    <!-- ============ ARTICLE HEAD (type on paper, never on the photo) ============ -->
    <header class="ed-article-head">
        <div class="container">
            <div class="ed-crumbs" role="navigation" aria-label="Breadcrumb">
                <a href="index.php">Home</a>
                <i class="fa fa-angle-right sep"></i>
                <a href="news_&amp;_events.php">Newsroom</a>
                <i class="fa fa-angle-right sep"></i>
                <a href="news_&amp;_events.php?category=<?php echo urlencode($article['category']); ?>"><?php echo strip_tags($category_labels[$article['category']] ?? $article['category']); ?></a>
                <i class="fa fa-angle-right sep"></i>
                <span class="current"><?php echo strip_tags($article['title']); ?></span>
            </div>

            <div class="ed-kicker">
                <span class="dot" style="background:<?php echo vvu_kicker_tone($article['category']); ?>"></span>
                <strong><?php echo strip_tags($category_labels[$article['category']] ?? $article['category']); ?></strong>
                <span class="sep">/</span>
                <?php echo vvu_relative_date($article['publish_date']); ?>
            </div>

            <h1 class="ed-headline"><?php echo strip_tags($article['title']); ?></h1>

            <?php if (!empty($article['excerpt'])): ?>
            <p class="ed-standfirst"><?php echo vvu_html_to_text($article['excerpt']); ?></p>
            <?php endif; ?>

            <div class="ed-article-byline">
                <div class="ed-byline">
                    <span class="ed-avatar" style="background:<?php echo vvu_avatar_tone($article['author']); ?>">
                        <?php if (!empty($article['author_image'])): ?>
                        <img src="<?php echo strip_tags($article['author_image']); ?>" alt="">
                        <?php else: ?>
                        <?php echo vvu_initials($article['author']); ?>
                        <?php endif; ?>
                    </span>
                    <span class="ed-byline-text">
                        <span class="ed-byline-name">By <?php echo strip_tags($article['author']); ?></span>
                        <span class="ed-byline-meta"><?php echo date('j F Y', strtotime($article['publish_date'])); ?></span>
                    </span>
                </div>
                <div class="ed-stats">
                    <span><i class="fa fa-clock-o"></i> <?php echo $read_time; ?> min read</span>
                    <span><i class="fa fa-eye"></i> <?php echo number_format($article['views_count']); ?> views</span>
                </div>
            </div>

            <?php if (!empty($article['event_date']) || !empty($article['event_location'])): ?>
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
        </div>
    </header>

    <!-- Lead image, framed as a figure rather than used as a text background -->
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
                    
                    <!-- Article Gallery -->
                    <?php if (!empty($article_gallery)): ?>
                    <section class="article-gallery">
                        <h3 class="article-gallery-title">
                            <i class="fa fa-picture-o"></i> Photo Gallery
                            <span class="article-gallery-count"><?php echo count($article_gallery); ?> photos</span>
                        </h3>
                        <div class="article-gallery-grid">
                            <?php foreach ($article_gallery as $g_index => $g_img): ?>
                            <figure class="article-gallery-item"
                                    data-index="<?php echo (int)$g_index; ?>"
                                    data-full="<?php echo htmlspecialchars($g_img['image_path'], ENT_QUOTES); ?>"
                                    data-caption="<?php echo htmlspecialchars($g_img['caption'] ?? '', ENT_QUOTES); ?>">
                                <img src="<?php echo htmlspecialchars($g_img['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($g_img['caption'] ?: $article['title'], ENT_QUOTES); ?>"
                                     loading="lazy">
                                <span class="article-gallery-zoom"><i class="fa fa-search-plus"></i></span>
                                <?php if (!empty($g_img['caption'])): ?>
                                <figcaption><?php echo strip_tags($g_img['caption']); ?></figcaption>
                                <?php endif; ?>
                            </figure>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Gallery Lightbox -->
                    <div class="article-lightbox" id="articleLightbox" aria-hidden="true">
                        <button class="lightbox-close" type="button" aria-label="Close gallery">&times;</button>
                        <button class="lightbox-nav lightbox-prev" type="button" aria-label="Previous image">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <figure class="lightbox-figure">
                            <img src="" alt="" id="lightboxImage">
                            <figcaption id="lightboxCaption"></figcaption>
                        </figure>
                        <button class="lightbox-nav lightbox-next" type="button" aria-label="Next image">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>

                    <style>
                    .article-gallery { margin: 45px 0 10px; }
                    .article-gallery-title {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        font-size: 22px;
                        font-weight: 700;
                        color: #002147;
                        margin: 0 0 20px;
                        padding-bottom: 12px;
                        border-bottom: 2px solid #f0f2f5;
                    }
                    .article-gallery-title i { color: #f26838; }
                    .article-gallery-count {
                        margin-left: auto;
                        font-size: 12px;
                        font-weight: 600;
                        color: #6c757d;
                        background: #f0f2f5;
                        padding: 4px 12px;
                        border-radius: 12px;
                    }
                    .article-gallery-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                        gap: 14px;
                    }
                    .article-gallery-item {
                        position: relative;
                        margin: 0;
                        border-radius: 12px;
                        overflow: hidden;
                        cursor: pointer;
                        background: #0b1c3a;
                        box-shadow: 0 6px 18px rgba(12, 26, 60, 0.10);
                        transition: transform .35s cubic-bezier(.2,.7,.3,1), box-shadow .35s ease;
                    }
                    .article-gallery-item img {
                        width: 100%;
                        height: 170px;
                        object-fit: cover;
                        display: block;
                        transition: transform .6s cubic-bezier(.2,.7,.3,1), opacity .3s ease;
                    }
                    .article-gallery-item:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(12,26,60,.20); }
                    .article-gallery-item:hover img { transform: scale(1.08); opacity: .85; }
                    .article-gallery-zoom {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%) scale(.6);
                        width: 44px;
                        height: 44px;
                        border-radius: 50%;
                        background: rgba(242, 104, 56, .95);
                        color: #fff;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        opacity: 0;
                        transition: opacity .3s ease, transform .35s cubic-bezier(.2,.7,.3,1);
                        pointer-events: none;
                    }
                    .article-gallery-item:hover .article-gallery-zoom { opacity: 1; transform: translate(-50%, -50%) scale(1); }
                    .article-gallery-item figcaption {
                        position: absolute;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        padding: 22px 12px 10px;
                        background: linear-gradient(180deg, rgba(4,16,40,0) 0%, rgba(4,16,40,.85) 100%);
                        color: #fff;
                        font-size: 12.5px;
                        line-height: 1.4;
                    }

                    /* Lightbox */
                    .article-lightbox {
                        position: fixed;
                        inset: 0;
                        z-index: 9999;
                        background: rgba(3, 10, 26, .94);
                        display: none;
                        align-items: center;
                        justify-content: center;
                        padding: 40px 70px;
                    }
                    .article-lightbox.is-open { display: flex; animation: vvuLbFade .25s ease; }
                    @keyframes vvuLbFade { from { opacity: 0; } to { opacity: 1; } }
                    .lightbox-figure { margin: 0; max-width: 100%; max-height: 100%; text-align: center; }
                    .lightbox-figure img {
                        max-width: 100%;
                        max-height: 78vh;
                        border-radius: 10px;
                        box-shadow: 0 20px 50px rgba(0,0,0,.5);
                    }
                    .lightbox-figure figcaption {
                        color: #e6ebf5;
                        font-size: 14px;
                        margin-top: 16px;
                        max-width: 700px;
                        margin-left: auto;
                        margin-right: auto;
                    }
                    .lightbox-close {
                        position: absolute;
                        top: 20px;
                        right: 26px;
                        background: none;
                        border: none;
                        color: #fff;
                        font-size: 40px;
                        line-height: 1;
                        cursor: pointer;
                        opacity: .8;
                    }
                    .lightbox-close:hover { opacity: 1; }
                    .lightbox-nav {
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        border: none;
                        background: rgba(255,255,255,.12);
                        color: #fff;
                        font-size: 18px;
                        cursor: pointer;
                        transition: background .25s ease;
                    }
                    .lightbox-nav:hover { background: #f26838; }
                    .lightbox-prev { left: 20px; }
                    .lightbox-next { right: 20px; }

                    @media (max-width: 600px) {
                        .article-gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
                        .article-gallery-item img { height: 120px; }
                        .article-lightbox { padding: 20px 12px; }
                        .lightbox-nav { width: 38px; height: 38px; }
                        .lightbox-prev { left: 6px; }
                        .lightbox-next { right: 6px; }
                    }
                    </style>

                    <script>
                    (function () {
                        var items = Array.prototype.slice.call(document.querySelectorAll('.article-gallery-item'));
                        var lightbox = document.getElementById('articleLightbox');
                        if (!items.length || !lightbox) return;

                        var img = document.getElementById('lightboxImage');
                        var caption = document.getElementById('lightboxCaption');
                        var current = 0;

                        function show(index) {
                            current = (index + items.length) % items.length;
                            var item = items[current];
                            img.src = item.getAttribute('data-full');
                            img.alt = item.getAttribute('data-caption') || '';
                            caption.textContent = item.getAttribute('data-caption') || '';
                            caption.style.display = caption.textContent ? 'block' : 'none';
                        }

                        function open(index) {
                            show(index);
                            lightbox.classList.add('is-open');
                            lightbox.setAttribute('aria-hidden', 'false');
                            document.body.style.overflow = 'hidden';
                        }

                        function close() {
                            lightbox.classList.remove('is-open');
                            lightbox.setAttribute('aria-hidden', 'true');
                            document.body.style.overflow = '';
                        }

                        items.forEach(function (item, index) {
                            item.addEventListener('click', function () { open(index); });
                        });

                        lightbox.querySelector('.lightbox-close').addEventListener('click', close);
                        lightbox.querySelector('.lightbox-prev').addEventListener('click', function (e) {
                            e.stopPropagation();
                            show(current - 1);
                        });
                        lightbox.querySelector('.lightbox-next').addEventListener('click', function (e) {
                            e.stopPropagation();
                            show(current + 1);
                        });
                        lightbox.addEventListener('click', function (e) {
                            if (e.target === lightbox || e.target.classList.contains('lightbox-figure')) close();
                        });
                        document.addEventListener('keydown', function (e) {
                            if (!lightbox.classList.contains('is-open')) return;
                            if (e.key === 'Escape') close();
                            if (e.key === 'ArrowLeft') show(current - 1);
                            if (e.key === 'ArrowRight') show(current + 1);
                        });
                    })();
                    </script>
                    <?php endif; ?>

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
