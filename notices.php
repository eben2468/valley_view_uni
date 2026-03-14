<?php
/**
 * Valley View University - Notices & Announcements Portal
 * Modern, responsive notices page with database integration
 */

require_once('includes/db_connect.php');

$page_title = "Notices & Announcements - Valley View University";
$active_page = "notices";

// Pagination settings
$items_per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Search settings
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query focusing on 'announcements' category (Notices)
$where_clauses = ["status = 'published'", "category = 'announcements'"];
$params = [];

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

// Fetch notices
try {
    $sql = "
        SELECT id, title, slug, excerpt, featured_image, category, author, publish_date, is_featured
        FROM news_articles 
        WHERE $where_sql 
        ORDER BY is_featured DESC, publish_date DESC 
        LIMIT " . intval($items_per_page) . " OFFSET " . intval($offset);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $notices = [];
}

// Get featured notice for top section (if any)
$featured_notice = null;
if ($current_page === 1 && empty($search_query)) {
    try {
        $featured_stmt = $pdo->prepare("
            SELECT id, title, slug, excerpt, featured_image, publish_date
            FROM news_articles 
            WHERE status = 'published' AND category = 'announcements'
            ORDER BY is_featured DESC, publish_date DESC 
            LIMIT 1
        ");
        $featured_stmt->execute();
        $featured_notice = $featured_stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $featured_notice = null;
    }
}

// Helper function to format date
function formatNoticeDate($date) {
    if (!$date) return '';
    return date('M j, Y', strtotime($date));
}

// Get default image for notices
function getNoticeImage($image) {
    if (!empty($image) && (file_exists($image) || strpos($image, 'http') === 0)) {
        return $image;
    }
    return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&q=80'; // Notice-themed default
}

include 'includes/header.php';
?>

<!-- News Portal CSS -->
<link rel="stylesheet" href="css/news-portal.css">

<main class="news-portal">
    
    <?php if ($featured_notice): ?>
    <!-- Notice Hero Section -->
    <section class="news-hero" style="min-height: 450px;">
        <div class="news-hero-bg" style="background-image: url('<?php echo strip_tags(getNoticeImage($featured_notice['featured_image'])); ?>');"></div>
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content">
            <div class="container">
                <div class="hero-badge" style="background: #e74c3c; color: white !important;">
                    <span class="badge-icon text-white" style="color: white !important;">
                        <i class="fa fa-bullhorn text-white" style="color: white !important;"></i>
                    </span>
                    <span class="text-white" style="color: white !important;">Urgent Announcement</span>
                </div>
                <h1 class="hero-title"><?php echo strip_tags($featured_notice['title']); ?></h1>
                <p class="hero-excerpt"><?php echo strip_tags($featured_notice['excerpt']); ?></p>
                <div class="hero-meta text-white" style="color: white !important;">
                    <span class="hero-date text-white" style="color: white !important;">
                        <i class="fa fa-calendar text-white" style="color: white !important;"></i>
                        Posted on <?php echo formatNoticeDate($featured_notice['publish_date']); ?>
                    </span>
                </div>
                <a href="notices_detail.php?slug=<?php echo urlencode($featured_notice['slug']); ?>" class="hero-btn text-white" style="color: white !important;">
                    <span class="text-white" style="color: white !important;">Read Full Notice</span>
                    <i class="fa fa-arrow-right text-white" style="color: white !important;"></i>
                </a>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- Alternative Header for Notices -->
    <section class="news-hero news-hero-default">
        <div class="news-hero-bg" style="background-image: url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1600&q=80');"></div>
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content">
            <div class="container">
                <h1 class="hero-title">Notices & Announcements</h1>
                <p class="hero-excerpt">Stay informed with official updates, policy changes, and important declarations from the University Administration.</p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Search Section -->
    <section class="news-filters">
        <div class="container">
            <div class="filters-wrapper" style="justify-content: center;">
                <div class="filters-search" style="width: 100%; max-width: 600px;">
                    <form action="" method="GET" class="search-form">
                        <div class="search-input-wrapper" style="flex: 1;">
                            <i class="fa fa-search"></i>
                            <input type="text" name="search" placeholder="Search notices by keyword..." value="<?php echo strip_tags($search_query); ?>" style="width: 100%;">
                            <?php if (!empty($search_query)): ?>
                            <a href="notices.php" class="search-clear">
                                <i class="fa fa-times"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="search-btn">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Notices Grid -->
    <section class="news-grid-section">
        <div class="container">
            <?php if (empty($notices)): ?>
            <div class="no-articles">
                <div class="no-articles-icon">
                    <i class="fa fa-bell-slash-o"></i>
                </div>
                <h3>No Notices Found</h3>
                <p>There are currently no announcements matching your search or criteria.</p>
                <a href="notices.php" class="btn-primary">View All Notices</a>
            </div>
            <?php else: ?>
            <div class="news-grid">
                <?php foreach ($notices as $notice): ?>
                <article class="news-card">
                    <a href="notices_detail.php?slug=<?php echo urlencode($notice['slug']); ?>" class="news-card-link">
                        <div class="news-card-image">
                            <img src="<?php echo strip_tags(getNoticeImage($notice['featured_image'])); ?>" 
                                 alt="<?php echo strip_tags($notice['title']); ?>"
                                 loading="lazy">
                            <div class="news-card-overlay"></div>
                            <?php if ($notice['is_featured']): ?>
                            <span class="featured-badge" style="background: #e74c3c;">
                                <i class="fa fa-bolt"></i> Important
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="news-card-content">
                            <h3 class="news-card-title"><?php echo strip_tags($notice['title']); ?></h3>
                            <p class="news-card-excerpt"><?php echo strip_tags($notice['excerpt']); ?></p>
                            <div class="news-card-meta">
                                <span class="news-card-date">
                                    <i class="fa fa-calendar-o"></i>
                                    <?php echo formatNoticeDate($notice['publish_date']); ?>
                                </span>
                                <span class="news-card-author">
                                    <i class="fa fa-user-circle"></i>
                                    <?php echo strip_tags($notice['author']); ?>
                                </span>
                            </div>
                            <span class="read-more">
                                View Notice <i class="fa fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <!-- Pagination -->
            <nav class="news-pagination">
                <?php
                $base_url = 'notices.php?';
                if (!empty($search_query)) $base_url .= 'search=' . urlencode($search_query) . '&';
                ?>
                
                <?php if ($current_page > 1): ?>
                <a href="<?php echo $base_url . 'page=' . ($current_page - 1); ?>" class="pagination-btn pagination-prev">
                    <i class="fa fa-chevron-left"></i>
                    <span>Previous</span>
                </a>
                <?php endif; ?>
                
                <div class="pagination-numbers">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo $base_url . 'page=' . $i; ?>" class="pagination-num <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
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

</main>

<?php
include 'includes/footer.php';
?>
