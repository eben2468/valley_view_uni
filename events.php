<?php
/**
 * Valley View University - Events Portal
 * Modern, responsive events page with database integration
 */

require_once('includes/db_connect.php');

$page_title = "University Events - Valley View University";
$active_page = "events";

// Pagination settings
$items_per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Search settings
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query focusing on 'events' category
$where_clauses = ["status = 'published'", "category = 'events'"];
$params = [];

if (!empty($search_query)) {
    $where_clauses[] = "(title LIKE ? OR excerpt LIKE ? OR content LIKE ? OR event_location LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
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

// Fetch events
try {
    $sql = "
        SELECT id, title, slug, excerpt, featured_image, category, author, publish_date, event_date, event_time, event_location, is_featured
        FROM news_articles 
        WHERE $where_sql 
        ORDER BY is_featured DESC, event_date DESC, publish_date DESC 
        LIMIT " . intval($items_per_page) . " OFFSET " . intval($offset);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $events = [];
}

// Get featured event for hero section
$featured_event = null;
if ($current_page === 1 && empty($search_query)) {
    try {
        $featured_stmt = $pdo->prepare("
            SELECT id, title, slug, excerpt, featured_image, event_date, event_time, event_location
            FROM news_articles 
            WHERE status = 'published' AND category = 'events' AND is_featured = 1
            ORDER BY event_date DESC, publish_date DESC 
            LIMIT 1
        ");
        $featured_stmt->execute();
        $featured_event = $featured_stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $featured_event = null;
    }
}

// Helper function to format date
function formatEventDate($date) {
    if (!$date) return '';
    return date('M j, Y', strtotime($date));
}

function formatEventTime($time) {
    if (!$time) return '';
    return date('g:i A', strtotime($time));
}

// Get default image for events without images
function getEventImage($image) {
    if (!empty($image) && (file_exists($image) || strpos($image, 'http') === 0)) {
        return $image;
    }
    return 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80';
}

include 'includes/header.php';
?>

<!-- News/Events Portal CSS -->
<link rel="stylesheet" href="css/news-portal.css">

<main class="news-portal">
    
    <?php if ($featured_event): ?>
    <!-- Hero Section with Featured Event -->
    <section class="news-hero">
        <div class="news-hero-bg" style="background-image: url('<?php echo strip_tags(getEventImage($featured_event['featured_image'])); ?>');"></div>
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content">
            <div class="container">
                <div class="hero-badge" style="color: white !important;">
                    <span class="badge-icon text-white" style="color: white !important;">
                        <i class="fa fa-star text-white" style="color: white !important;"></i>
                    </span>
                    <span class="text-white" style="color: white !important;">Ongoing / Upcoming Event</span>
                </div>
                <h1 class="hero-title"><?php echo strip_tags($featured_event['title']); ?></h1>
                <p class="hero-excerpt"><?php echo strip_tags($featured_event['excerpt']); ?></p>
                <div class="hero-meta text-white" style="color: white !important;">
                    <span class="hero-date text-white" style="color: white !important;">
                        <i class="fa fa-calendar" style="color: white !important;"></i>
                        <?php echo formatEventDate($featured_event['event_date']); ?>
                    </span>
                    <?php if (!empty($featured_event['event_time'])): ?>
                    <span class="hero-time text-white" style="color: white !important;">
                        <i class="fa fa-clock-o" style="color: white !important;"></i>
                        <?php echo formatEventTime($featured_event['event_time']); ?>
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($featured_event['event_location'])): ?>
                    <span class="hero-location text-white" style="color: white !important;">
                        <i class="fa fa-map-marker" style="color: white !important;"></i>
                        <?php echo strip_tags($featured_event['event_location']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <a href="event_detail.php?slug=<?php echo urlencode($featured_event['slug']); ?>" class="hero-btn text-white" style="color: white !important;">
                    <span class="text-white" style="color: white !important;">View Event Details</span>
                    <i class="fa fa-arrow-right text-white" style="color: white !important;"></i>
                </a>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- Alternative Hero when no featured event -->
    <section class="news-hero news-hero-default">
        <div class="news-hero-bg" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600&q=80');"></div>
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content">
            <div class="container text-center" style="max-width: 100%;">
                <h1 class="hero-title">University Events</h1>
                <p class="hero-excerpt">Connect, learn, and grow through our diverse range of university events. From academic symposiums to campus festivals, there's always something happening at VVU.</p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Search Section -->
    <section class="news-filters" style="top: 80px;"> <!-- Adjusted for potential sticky header height -->
        <div class="container">
            <div class="filters-wrapper" style="justify-content: center;">
                <div class="filters-search" style="width: 100%; max-width: 600px;">
                    <form action="" method="GET" class="search-form">
                        <div class="search-input-wrapper" style="flex: 1;">
                            <i class="fa fa-search"></i>
                            <input type="text" name="search" placeholder="Search events by name, location or keyword..." value="<?php echo strip_tags($search_query); ?>" style="width: 100%;">
                            <?php if (!empty($search_query)): ?>
                            <a href="events.php" class="search-clear">
                                <i class="fa fa-times"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="search-btn">Search</button>
                    </form>
                </div>
            </div>
            
            <?php if (!empty($search_query)): ?>
            <div class="search-results-info text-center">
                <p>Showing results for "<strong><?php echo strip_tags($search_query); ?></strong>" — <?php echo $total_items; ?> event<?php echo $total_items !== 1 ? 's' : ''; ?> found</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Events Grid -->
    <section class="news-grid-section">
        <div class="container">
            <?php if (empty($events)): ?>
            <div class="no-articles">
                <div class="no-articles-icon">
                    <i class="fa fa-calendar-times-o"></i>
                </div>
                <h3>No Events Found</h3>
                <p>We couldn't find any upcoming events matching your criteria. Please check back later or try a different search.</p>
                <a href="events.php" class="btn-primary">View All Events</a>
            </div>
            <?php else: ?>
            <div class="news-grid">
                <?php foreach ($events as $event): ?>
                <article class="news-card">
                    <a href="event_detail.php?slug=<?php echo urlencode($event['slug']); ?>" class="news-card-link">
                        <div class="news-card-image">
                            <img src="<?php echo strip_tags(getEventImage($event['featured_image'])); ?>" 
                                 alt="<?php echo strip_tags($event['title']); ?>"
                                 loading="lazy">
                            <div class="news-card-overlay"></div>
                            <?php if ($event['is_featured']): ?>
                            <span class="featured-badge">
                                <i class="fa fa-star"></i> Featured
                            </span>
                            <?php endif; ?>
                            <div class="event-date-badge">
                                <span class="month"><?php echo date('M', strtotime($event['event_date'] ?? $event['publish_date'])); ?></span>
                                <span class="day"><?php echo date('d', strtotime($event['event_date'] ?? $event['publish_date'])); ?></span>
                            </div>
                        </div>
                        <div class="news-card-content">
                            <h3 class="news-card-title"><?php echo strip_tags($event['title']); ?></h3>
                            <p class="news-card-excerpt"><?php echo strip_tags($event['excerpt']); ?></p>
                            <div class="news-card-meta">
                                <?php if (!empty($event['event_time'])): ?>
                                <span class="news-card-date">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo formatEventTime($event['event_time']); ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($event['event_location'])): ?>
                                <span class="news-card-location">
                                    <i class="fa fa-map-marker"></i>
                                    <?php echo strip_tags($event['event_location']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <span class="read-more">
                                Event Details <i class="fa fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <!-- Pagination -->
            <nav class="news-pagination" aria-label="Events pagination">
                <?php
                $base_url = 'events.php?';
                $url_params = [];
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
                        <i class="fa fa-calendar-check-o"></i>
                    </div>
                    <h2>Don't Miss Out</h2>
                    <p>Subscribe to our events newsletter to receive updates about upcoming campus activities, workshops, and ceremonies directly in your inbox.</p>
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