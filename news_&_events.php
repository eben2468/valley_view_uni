<?php
/**
 * Valley View University - News & Events Portal
 * Modern, responsive news page with database integration
 */

require_once('includes/db_connect.php');

$page_title = "News & Events - Valley View University";
$active_page = "news";

// Pagination settings
$items_per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Filter settings
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query with filters
$where_clauses = ["status = 'published'"];
$params = [];

if ($category_filter !== 'all') {
    $where_clauses[] = "category = ?";
    $params[] = $category_filter;
}

if (!empty($search_query)) {
    $where_clauses[] = "(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_sql = implode(' AND ', $where_clauses);

// Get total count for pagination
try {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM news_articles WHERE $where_sql");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
} catch (PDOException $e) {
    $total_items = 0;
    $total_pages = 1;
}

// Fetch news articles
try {
    $sql = "
        SELECT id, title, slug, excerpt, featured_image, category, author, publish_date, event_date, event_time, event_location, is_featured
        FROM news_articles 
        WHERE $where_sql 
        ORDER BY is_featured DESC, publish_date DESC 
        LIMIT " . intval($items_per_page) . " OFFSET " . intval($offset);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $articles = [];
}


// Get featured article for hero section (only on first page with no filters)
$featured_article = null;
if ($current_page === 1 && $category_filter === 'all' && empty($search_query)) {
    try {
        $featured_stmt = $pdo->prepare("
            SELECT id, title, slug, excerpt, featured_image, category, publish_date
            FROM news_articles 
            WHERE status = 'published' AND is_featured = 1
            ORDER BY publish_date DESC 
            LIMIT 1
        ");
        $featured_stmt->execute();
        $featured_article = $featured_stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $featured_article = null;
    }
}

// Category labels
$category_labels = [
    'news' => 'News',
    'events' => 'Events',
    'announcements' => 'Announcements',
    'press_releases' => 'Press Releases',
    'academic' => 'Academic'
];

// Helper function to format date
function formatNewsDate($date, $event_time = null) {
    $timestamp = strtotime($date);
    $formatted = date('F j, Y', $timestamp);
    if ($event_time) {
        $formatted .= ' | ' . date('g:i A', strtotime($event_time));
    }
    return $formatted;
}

// Function to get category color
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

// Get default image for articles without images
function getArticleImage($image, $category) {
    if (!empty($image) && file_exists($image)) {
        return $image;
    }
    // Default placeholder images by category
    $defaults = [
        'news' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80',
        'events' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80',
        'announcements' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&q=80',
        'press_releases' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&q=80',
        'academic' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&q=80'
    ];
    return $defaults[$category] ?? $defaults['news'];
}

include 'includes/header.php';
?>

<!-- News Portal CSS -->
<link rel="stylesheet" href="css/news-portal.css">

<!-- Main Content -->
<main class="news-portal">
    
    <?php if ($featured_article): ?>
    <!-- Hero Section with Featured Article -->
    <section class="news-hero">
        <div class="news-hero-bg" style="background-image: url('<?php echo strip_tags(getArticleImage($featured_article['featured_image'], $featured_article['category'])); ?>');"></div>
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content">
            <div class="container">
                <div class="hero-badge" style="color: white !important;">
                    <span class="badge-icon text-white" style="color: white !important;">
                        <i class="fa fa-star text-white" style="color: white !important;"></i>
                    </span>
                    <span class="text-white" style="color: white !important;">Featured Story</span>
                </div>
                <span class="hero-category <?php echo getCategoryColor($featured_article['category']); ?>">
                    <?php echo strip_tags($category_labels[$featured_article['category']] ?? $featured_article['category']); ?>
                </span>
                <h1 class="hero-title"><?php echo strip_tags($featured_article['title']); ?></h1>
                <p class="hero-excerpt"><?php echo strip_tags($featured_article['excerpt']); ?></p>
                <div class="hero-meta text-white" style="color: white !important;">
                    <span class="hero-date text-white" style="color: white !important;">
                        <i class="fa fa-calendar" style="color: white !important;"></i>
                        <?php echo formatNewsDate($featured_article['publish_date']); ?>
                    </span>
                </div>
                <a href="news_detail.php?slug=<?php echo urlencode($featured_article['slug']); ?>" class="hero-btn text-white" style="color: white !important;">
                    <span class="text-white" style="color: white !important;">Read Full Story</span>
                    <i class="fa fa-arrow-right text-white" style="color: white !important;"></i>
                </a>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- Alternative Hero when no featured article -->
    <section class="news-hero news-hero-default">
        <div class="news-hero-bg" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600&q=80');"></div>
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content">
            <div class="container">
                <h1 class="hero-title">News & Events</h1>
                <p class="hero-excerpt">Stay updated with the latest happenings at Valley View University. Discover news, events, and announcements from across our campus community.</p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Filters Section -->
    <section class="news-filters">
        <div class="container">
            <div class="filters-wrapper">
                <div class="filters-categories">
                    <a href="news_&_events.php" class="filter-btn <?php echo $category_filter === 'all' ? 'active' : ''; ?>">
                        <i class="fa fa-th-large"></i>
                        <span>All</span>
                    </a>
                    <a href="?category=news" class="filter-btn <?php echo $category_filter === 'news' ? 'active' : ''; ?>">
                        <i class="fa fa-newspaper-o"></i>
                        <span>News</span>
                    </a>
                    <a href="?category=events" class="filter-btn <?php echo $category_filter === 'events' ? 'active' : ''; ?>">
                        <i class="fa fa-calendar"></i>
                        <span>Events</span>
                    </a>
                    <a href="?category=announcements" class="filter-btn <?php echo $category_filter === 'announcements' ? 'active' : ''; ?>">
                        <i class="fa fa-bullhorn"></i>
                        <span>Announcements</span>
                    </a>
                    <a href="?category=press_releases" class="filter-btn <?php echo $category_filter === 'press_releases' ? 'active' : ''; ?>">
                        <i class="fa fa-building"></i>
                        <span>Press</span>
                    </a>
                    <a href="?category=academic" class="filter-btn <?php echo $category_filter === 'academic' ? 'active' : ''; ?>">
                        <i class="fa fa-graduation-cap"></i>
                        <span>Academic</span>
                    </a>
                </div>
                <div class="filters-search">
                    <form action="" method="GET" class="search-form">
                        <?php if ($category_filter !== 'all'): ?>
                        <input type="hidden" name="category" value="<?php echo strip_tags($category_filter); ?>">
                        <?php endif; ?>
                        <div class="search-input-wrapper">
                            <i class="fa fa-search"></i>
                            <input type="text" name="search" placeholder="Search articles..." value="<?php echo strip_tags($search_query); ?>">
                            <?php if (!empty($search_query)): ?>
                            <a href="?<?php echo $category_filter !== 'all' ? 'category=' . urlencode($category_filter) : ''; ?>" class="search-clear">
                                <i class="fa fa-times"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="search-btn">Search</button>
                    </form>
                </div>
            </div>
            
            <?php if (!empty($search_query)): ?>
            <div class="search-results-info">
                <p>Showing results for "<strong><?php echo strip_tags($search_query); ?></strong>" — <?php echo $total_items; ?> article<?php echo $total_items !== 1 ? 's' : ''; ?> found</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- News Grid -->
    <section class="news-grid-section">
        <div class="container">
            <?php if (empty($articles)): ?>
            <div class="no-articles">
                <div class="no-articles-icon">
                    <i class="fa fa-newspaper-o"></i>
                </div>
                <h3>No Articles Found</h3>
                <p>We couldn't find any articles matching your criteria. Try adjusting your filters or search terms.</p>
                <a href="news_&_events.php" class="btn-primary">View All Articles</a>
            </div>
            <?php else: ?>
            <div class="news-grid">
                <?php foreach ($articles as $article): ?>
                <article class="news-card">
                    <a href="news_detail.php?slug=<?php echo urlencode($article['slug']); ?>" class="news-card-link">
                        <div class="news-card-image">
                            <img src="<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>" 
                                 alt="<?php echo strip_tags($article['title']); ?>"
                                 loading="lazy">
                            <div class="news-card-overlay"></div>
                            <?php if ($article['is_featured']): ?>
                            <span class="featured-badge">
                                <i class="fa fa-star"></i> Featured
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="news-card-content">
                            <span class="news-card-category <?php echo getCategoryColor($article['category']); ?>">
                                <?php echo strip_tags($category_labels[$article['category']] ?? $article['category']); ?>
                            </span>
                            <h3 class="news-card-title"><?php echo strip_tags($article['title']); ?></h3>
                            <p class="news-card-excerpt"><?php echo strip_tags($article['excerpt']); ?></p>
                            <div class="news-card-meta">
                                <span class="news-card-date">
                                    <i class="fa fa-calendar-o"></i>
                                    <?php echo formatNewsDate($article['event_date'] ?? $article['publish_date'], $article['event_time']); ?>
                                </span>
                                <?php if (!empty($article['event_location'])): ?>
                                <span class="news-card-location">
                                    <i class="fa fa-map-marker"></i>
                                    <?php echo strip_tags($article['event_location']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <span class="read-more">
                                Read More <i class="fa fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <!-- Pagination -->
            <nav class="news-pagination" aria-label="News pagination">
                <?php
                $base_url = 'news_&_events.php?';
                $url_params = [];
                if ($category_filter !== 'all') $url_params[] = 'category=' . urlencode($category_filter);
                if (!empty($search_query)) $url_params[] = 'search=' . urlencode($search_query);
                $base_url .= implode('&', $url_params) . (count($url_params) > 0 ? '&' : '');
                ?>
                
                <?php if ($current_page > 1): ?>
                <a href="<?php echo $base_url . 'page=' . ($current_page - 1); ?>" class="pagination-btn pagination-prev">
                    <i class="fa fa-chevron-left"></i>
                    <span>Previous</span>
                </a>
                <?php endif; ?>
                
                <div class="pagination-numbers">
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1): ?>
                    <a href="<?php echo $base_url . 'page=1'; ?>" class="pagination-num">1</a>
                    <?php if ($start_page > 2): ?>
                    <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="<?php echo $base_url . 'page=' . $i; ?>" class="pagination-num <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                    <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                    <a href="<?php echo $base_url . 'page=' . $total_pages; ?>" class="pagination-num"><?php echo $total_pages; ?></a>
                    <?php endif; ?>
                </div>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo $base_url . 'page=' . ($current_page + 1); ?>" class="pagination-btn pagination-next">
                    <span>Next</span>
                    <i class="fa fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="news-newsletter">
        <div class="container">
            <div class="newsletter-wrapper">
                <div class="newsletter-content">
                    <div class="newsletter-icon">
                        <i class="fa fa-envelope-open"></i>
                    </div>
                    <h2>Stay Informed</h2>
                    <p>Subscribe to our newsletter to receive the latest news and updates from Valley View University directly in your inbox.</p>
                </div>
                <form class="newsletter-form" action="#" method="POST">
                    <input type="email" name="email" placeholder="Enter your email address" required>
                    <button type="submit">
                        <span>Subscribe</span>
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
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
