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
        SELECT id, title, slug, excerpt, featured_image, category, author, author_image, publish_date, event_date, event_time, event_location, is_featured
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
            SELECT id, title, slug, excerpt, content, featured_image, category, author, author_image, publish_date
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

require_once 'includes/news_helpers.php';
include 'includes/header.php';
?>

<!-- Newsroom styles -->
<link rel="stylesheet" href="css/news-editorial.css">
<script src="js/news-modern.js" defer></script>

<!-- Main Content -->
<main class="ed-news">

    <!-- ============ MASTHEAD ============ -->
    <section class="ed-masthead">
        <div class="container">
            <div class="ed-masthead-inner">
                <div>
                    <span class="ed-eyebrow">VVU Newsroom</span>
                    <h1>News &amp; Events</h1>
                    <p>Stories from across the Valley View community &mdash; what we are learning,
                       celebrating and building together.</p>
                </div>
                <div class="ed-masthead-stat">
                    <strong><?php echo number_format($total_items); ?></strong>
                    <span><?php echo ($category_filter !== 'all' || !empty($search_query)) ? 'Matching stories' : 'Stories published'; ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FEATURED STORY ============ -->
    <?php if ($featured_article): ?>
    <?php
        $f_url = 'news_detail.php?slug=' . urlencode($featured_article['slug']);
        $f_author = $featured_article['author'] ?: 'VVU Communications';
    ?>
    <section class="ed-featured">
        <div class="container">
            <div class="ed-featured-panel ed-rise">
                <a class="ed-featured-media" href="<?php echo $f_url; ?>"
                   aria-label="<?php echo htmlspecialchars($featured_article['title'], ENT_QUOTES); ?>">
                    <img src="<?php echo strip_tags(getArticleImage($featured_article['featured_image'], $featured_article['category'])); ?>"
                         alt="<?php echo htmlspecialchars($featured_article['title'], ENT_QUOTES); ?>">
                    <span class="ed-flag"><i class="fa fa-star"></i> Featured</span>
                </a>
                <div class="ed-featured-body">
                    <div class="ed-kicker">
                        <span class="dot" style="background:<?php echo vvu_kicker_tone($featured_article['category']); ?>"></span>
                        <strong><?php echo strip_tags($category_labels[$featured_article['category']] ?? $featured_article['category']); ?></strong>
                        <span class="sep">/</span>
                        <?php echo vvu_relative_date($featured_article['publish_date']); ?>
                    </div>

                    <h2 class="ed-featured-title">
                        <a href="<?php echo $f_url; ?>"><?php echo strip_tags($featured_article['title']); ?></a>
                    </h2>

                    <p class="ed-featured-excerpt">
                        <?php echo vvu_excerpt($featured_article['excerpt'], $featured_article['content'], 240); ?>
                    </p>

                    <div class="ed-featured-foot">
                        <div class="ed-byline">
                            <span class="ed-avatar" style="background:<?php echo vvu_avatar_tone($f_author); ?>">
                                <?php if (!empty($featured_article['author_image'])): ?>
                                <img src="<?php echo strip_tags($featured_article['author_image']); ?>" alt="">
                                <?php else: ?>
                                <?php echo vvu_initials($f_author); ?>
                                <?php endif; ?>
                            </span>
                            <span class="ed-byline-text">
                                <span class="ed-byline-name"><?php echo strip_tags($f_author); ?></span>
                                <span class="ed-byline-meta">
                                    <?php echo date('j F Y', strtotime($featured_article['publish_date'])); ?>
                                    <span class="sep">&middot;</span>
                                    <?php echo vvu_read_time($featured_article['content']); ?> min read
                                </span>
                            </span>
                        </div>
                        <a href="<?php echo $f_url; ?>" class="ed-btn">
                            Read the full story <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============ FILTER RAIL ============ -->
    <section class="ed-rail">
        <div class="container">
            <div class="ed-rail-inner">
                <div class="ed-tabs">
                    <?php
                    $tabs = ['all' => 'All Stories'] + $category_labels;
                    foreach ($tabs as $key => $label):
                        $href = $key === 'all' ? 'news_&_events.php' : '?category=' . urlencode($key);
                    ?>
                    <a href="<?php echo $href; ?>" class="ed-tab <?php echo $category_filter === $key ? 'is-active' : ''; ?>">
                        <?php echo $label; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <form action="" method="GET" class="ed-search">
                    <?php if ($category_filter !== 'all'): ?>
                    <input type="hidden" name="category" value="<?php echo strip_tags($category_filter); ?>">
                    <?php endif; ?>
                    <div class="ed-search-field">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" placeholder="Search stories"
                               value="<?php echo htmlspecialchars($search_query, ENT_QUOTES); ?>">
                        <?php if (!empty($search_query)): ?>
                        <a href="?<?php echo $category_filter !== 'all' ? 'category=' . urlencode($category_filter) : ''; ?>" class="ed-search-clear">
                            <i class="fa fa-times"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
    </section>

    <!-- ============ STORIES ============ -->
    <section class="ed-section">
        <div class="container">
            <?php if (!empty($search_query)): ?>
            <p class="ed-result-note">
                <?php echo $total_items; ?> result<?php echo $total_items !== 1 ? 's' : ''; ?>
                for &ldquo;<strong><?php echo strip_tags($search_query); ?></strong>&rdquo;
            </p>
            <?php endif; ?>

            <?php if (empty($articles)): ?>
            <div class="ed-empty">
                <i class="fa fa-newspaper-o"></i>
                <h3>Nothing here yet</h3>
                <p>We couldn&rsquo;t find any stories matching what you&rsquo;re looking for.
                   Try a different category, or browse everything we&rsquo;ve published.</p>
                <a href="news_&_events.php" class="ed-btn">Browse all stories</a>
            </div>
            <?php else: ?>

            <div class="ed-section-head">
                <h2><?php
                    if (!empty($search_query)) {
                        echo 'Search Results';
                    } elseif ($category_filter !== 'all') {
                        echo strip_tags($category_labels[$category_filter] ?? 'Stories');
                    } else {
                        echo 'Latest Stories';
                    }
                ?></h2>
                <span class="ed-count">Page <?php echo $current_page; ?> of <?php echo max(1, $total_pages); ?></span>
            </div>

            <div class="ed-grid">
                <?php foreach ($articles as $index => $article): ?>
                <?php
                    $a_url = 'news_detail.php?slug=' . urlencode($article['slug']);
                    $a_author = $article['author'] ?: 'VVU Communications';
                    $is_event = ($article['category'] === 'events' || !empty($article['event_date']));
                    $tile_date = !empty($article['event_date']) ? $article['event_date'] : $article['publish_date'];
                    // First card runs full width when there is no featured panel above
                    $wide = (!$featured_article && $index === 0);
                ?>
                <article class="ed-card ed-rise <?php echo $wide ? 'is-wide' : ''; ?>">
                    <a class="ed-card-media" href="<?php echo $a_url; ?>" tabindex="-1" aria-hidden="true">
                        <img src="<?php echo strip_tags(getArticleImage($article['featured_image'], $article['category'])); ?>"
                             alt="" loading="lazy">
                        <?php if ($is_event): ?>
                        <span class="ed-datetile">
                            <span class="m"><?php echo date('M', strtotime($tile_date)); ?></span>
                            <span class="d"><?php echo date('j', strtotime($tile_date)); ?></span>
                        </span>
                        <?php elseif ($article['is_featured']): ?>
                        <span class="ed-flag"><i class="fa fa-star"></i> Featured</span>
                        <?php endif; ?>
                    </a>

                    <div class="ed-card-body">
                        <div class="ed-kicker">
                            <span class="dot" style="background:<?php echo vvu_kicker_tone($article['category']); ?>"></span>
                            <strong><?php echo strip_tags($category_labels[$article['category']] ?? $article['category']); ?></strong>
                            <span class="sep">/</span>
                            <?php echo vvu_relative_date($article['publish_date']); ?>
                        </div>

                        <h3 class="ed-card-title">
                            <a href="<?php echo $a_url; ?>"><?php echo strip_tags($article['title']); ?></a>
                        </h3>

                        <?php if ($is_event && !empty($article['event_location'])): ?>
                        <div class="ed-card-where">
                            <i class="fa fa-map-marker"></i>
                            <?php echo strip_tags($article['event_location']); ?>
                            <?php if (!empty($article['event_time'])): ?>
                            <span class="sep">&middot;</span> <?php echo date('g:i A', strtotime($article['event_time'])); ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <p class="ed-card-excerpt"><?php echo vvu_excerpt($article['excerpt'], '', $wide ? 220 : 130); ?></p>

                        <div class="ed-card-foot">
                            <span class="ed-avatar sm" style="background:<?php echo vvu_avatar_tone($a_author); ?>">
                                <?php if (!empty($article['author_image'])): ?>
                                <img src="<?php echo strip_tags($article['author_image']); ?>" alt="">
                                <?php else: ?>
                                <?php echo vvu_initials($a_author); ?>
                                <?php endif; ?>
                            </span>
                            <span class="ed-byline-meta"><?php echo strip_tags($a_author); ?></span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <?php
            $base_url = 'news_&_events.php?';
            $url_params = [];
            if ($category_filter !== 'all') $url_params[] = 'category=' . urlencode($category_filter);
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
                    <h2>Get the newsroom in your inbox</h2>
                    <p>A short note each month with the stories, events and milestones
                       from around campus. No noise &mdash; just what matters.</p>
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
