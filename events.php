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

require_once 'includes/news_helpers.php';
include 'includes/header.php';
?>

<!-- Newsroom styles -->
<link rel="stylesheet" href="css/news-editorial.css">
<script src="js/news-modern.js" defer></script>

<main class="ed-news">

    <!-- ============ MASTHEAD ============ -->
    <section class="ed-masthead">
        <div class="container">
            <div class="ed-masthead-inner">
                <div>
                    <span class="ed-eyebrow">What&rsquo;s On</span>
                    <h1>Campus Events</h1>
                    <p>Convocations, lectures, worship, sport and everything in between.
                       Come and be part of it.</p>
                </div>
                <div class="ed-masthead-stat">
                    <strong><?php echo number_format($total_items); ?></strong>
                    <span><?php echo !empty($search_query) ? 'Matching events' : 'Events listed'; ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FEATURED EVENT ============ -->
    <?php if ($featured_event): ?>
    <?php $fe_url = 'event_detail.php?slug=' . urlencode($featured_event['slug']); ?>
    <section class="ed-featured">
        <div class="container">
            <div class="ed-featured-panel ed-rise">
                <a class="ed-featured-media" href="<?php echo $fe_url; ?>"
                   aria-label="<?php echo htmlspecialchars($featured_event['title'], ENT_QUOTES); ?>">
                    <img src="<?php echo strip_tags(getEventImage($featured_event['featured_image'])); ?>"
                         alt="<?php echo htmlspecialchars($featured_event['title'], ENT_QUOTES); ?>">
                    <?php if (!empty($featured_event['event_date'])): ?>
                    <span class="ed-datetile">
                        <span class="m"><?php echo date('M', strtotime($featured_event['event_date'])); ?></span>
                        <span class="d"><?php echo date('j', strtotime($featured_event['event_date'])); ?></span>
                    </span>
                    <?php endif; ?>
                </a>
                <div class="ed-featured-body">
                    <div class="ed-kicker">
                        <span class="dot" style="background:<?php echo vvu_kicker_tone('events'); ?>"></span>
                        <strong>Featured Event</strong>
                        <?php if (!empty($featured_event['event_date'])): ?>
                        <span class="sep">/</span>
                        <?php echo vvu_relative_date($featured_event['event_date']); ?>
                        <?php endif; ?>
                    </div>

                    <h2 class="ed-featured-title">
                        <a href="<?php echo $fe_url; ?>"><?php echo strip_tags($featured_event['title']); ?></a>
                    </h2>

                    <p class="ed-featured-excerpt">
                        <?php echo vvu_excerpt($featured_event['excerpt'], '', 240); ?>
                    </p>

                    <div class="ed-facts" style="margin-bottom:26px;">
                        <?php if (!empty($featured_event['event_date'])): ?>
                        <div class="ed-fact">
                            <span>Date</span>
                            <strong><?php echo date('l, j F Y', strtotime($featured_event['event_date'])); ?></strong>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($featured_event['event_time'])): ?>
                        <div class="ed-fact">
                            <span>Time</span>
                            <strong><?php echo date('g:i A', strtotime($featured_event['event_time'])); ?></strong>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($featured_event['event_location'])): ?>
                        <div class="ed-fact">
                            <span>Venue</span>
                            <strong><?php echo strip_tags($featured_event['event_location']); ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <a href="<?php echo $fe_url; ?>" class="ed-btn">
                            See full details <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============ SEARCH RAIL ============ -->
    <section class="ed-rail">
        <div class="container">
            <div class="ed-rail-inner">
                <div class="ed-tabs">
                    <a href="events.php" class="ed-tab is-active">All Events</a>
                    <a href="news_&_events.php" class="ed-tab">Newsroom</a>
                </div>
                <form action="" method="GET" class="ed-search">
                    <div class="ed-search-field">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" placeholder="Search events or venues"
                               value="<?php echo htmlspecialchars($search_query, ENT_QUOTES); ?>">
                        <?php if (!empty($search_query)): ?>
                        <a href="events.php" class="ed-search-clear"><i class="fa fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
    </section>

    <!-- ============ EVENTS ============ -->
    <section class="ed-section">
        <div class="container">
            <?php if (!empty($search_query)): ?>
            <p class="ed-result-note">
                <?php echo $total_items; ?> event<?php echo $total_items !== 1 ? 's' : ''; ?>
                for &ldquo;<strong><?php echo strip_tags($search_query); ?></strong>&rdquo;
            </p>
            <?php endif; ?>

            <?php if (empty($events)): ?>
            <div class="ed-empty">
                <i class="fa fa-calendar-o"></i>
                <h3>No events to show</h3>
                <p>There&rsquo;s nothing on the calendar matching that search right now.
                   Check back soon &mdash; new events are added every week.</p>
                <a href="events.php" class="ed-btn">View all events</a>
            </div>
            <?php else: ?>

            <div class="ed-section-head">
                <h2><?php echo !empty($search_query) ? 'Search Results' : 'Upcoming &amp; Recent'; ?></h2>
                <span class="ed-count">Page <?php echo $current_page; ?> of <?php echo max(1, $total_pages); ?></span>
            </div>

            <div class="ed-grid">
                <?php foreach ($events as $index => $event): ?>
                <?php
                    $e_url = 'event_detail.php?slug=' . urlencode($event['slug']);
                    $tile_date = !empty($event['event_date']) ? $event['event_date'] : $event['publish_date'];
                    $wide = (!$featured_event && $index === 0);
                ?>
                <article class="ed-card ed-rise <?php echo $wide ? 'is-wide' : ''; ?>">
                    <a class="ed-card-media" href="<?php echo $e_url; ?>" tabindex="-1" aria-hidden="true">
                        <img src="<?php echo strip_tags(getEventImage($event['featured_image'])); ?>"
                             alt="" loading="lazy">
                        <span class="ed-datetile">
                            <span class="m"><?php echo date('M', strtotime($tile_date)); ?></span>
                            <span class="d"><?php echo date('j', strtotime($tile_date)); ?></span>
                        </span>
                    </a>

                    <div class="ed-card-body">
                        <div class="ed-kicker">
                            <span class="dot" style="background:<?php echo vvu_kicker_tone('events'); ?>"></span>
                            <strong><?php echo vvu_relative_date($tile_date); ?></strong>
                            <?php if (!empty($event['event_time'])): ?>
                            <span class="sep">/</span>
                            <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                            <?php endif; ?>
                        </div>

                        <h3 class="ed-card-title">
                            <a href="<?php echo $e_url; ?>"><?php echo strip_tags($event['title']); ?></a>
                        </h3>

                        <?php if (!empty($event['event_location'])): ?>
                        <div class="ed-card-where">
                            <i class="fa fa-map-marker"></i>
                            <?php echo strip_tags($event['event_location']); ?>
                        </div>
                        <?php endif; ?>

                        <p class="ed-card-excerpt"><?php echo vvu_excerpt($event['excerpt'], '', $wide ? 220 : 130); ?></p>

                        <div class="ed-card-foot">
                            <a href="<?php echo $e_url; ?>" class="ed-btn ed-btn-ghost" style="padding:9px 18px;font-size:13px;">
                                Event details <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <?php
            $base_url = 'events.php?';
            $url_params = [];
            if (!empty($search_query)) $url_params[] = 'search=' . urlencode($search_query);
            $base_url .= implode('&', $url_params) . (count($url_params) > 0 ? '&' : '');
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            ?>
            <div class="ed-pagination">
                <?php if ($current_page > 1): ?>
                <a href="<?php echo $base_url . 'page=' . ($current_page - 1); ?>" class="ed-page-arrow">
                    <i class="fa fa-chevron-left"></i> Previous
                </a>
                <?php endif; ?>

                <?php if ($start_page > 1): ?>
                <a href="<?php echo $base_url; ?>page=1" class="ed-page">1</a>
                <?php if ($start_page > 2): ?><span class="ed-page-gap">&hellip;</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="<?php echo $base_url . 'page=' . $i; ?>" class="ed-page <?php echo $i === $current_page ? 'is-active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?><span class="ed-page-gap">&hellip;</span><?php endif; ?>
                <a href="<?php echo $base_url . 'page=' . $total_pages; ?>" class="ed-page"><?php echo $total_pages; ?></a>
                <?php endif; ?>

                <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo $base_url . 'page=' . ($current_page + 1); ?>" class="ed-page-arrow">
                    Next <i class="fa fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </section>

    <!-- ============ SUBSCRIBE ============ -->
    <section class="ed-subscribe">
        <div class="container">
            <div class="ed-subscribe-inner">
                <div>
                    <h2>Never miss what&rsquo;s happening</h2>
                    <p>Get the campus calendar in your inbox &mdash; ceremonies, workshops,
                       fixtures and services, before they fill up.</p>
                </div>
                <form action="#" method="POST">
                    <input type="email" name="email" placeholder="your.name@email.com" required>
                    <button type="submit">Subscribe</button>
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