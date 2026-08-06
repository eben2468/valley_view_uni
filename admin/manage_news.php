<?php
/**
 * Valley View University Admin Panel
 * News & Events Management Page
 */

require_once('../includes/db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $delete_id = intval($_GET['delete']);
        
        // Get image path before deleting
        $img_stmt = $pdo->prepare("SELECT featured_image FROM news_articles WHERE id = ?");
        $img_stmt->execute([$delete_id]);
        $article = $img_stmt->fetch();
        
        // Get gallery image paths before deleting (rows cascade with the article)
        $gallery_files = [];
        try {
            $g_stmt = $pdo->prepare("SELECT image_path FROM news_article_images WHERE article_id = ?");
            $g_stmt->execute([$delete_id]);
            $gallery_files = $g_stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            // Gallery table may not exist yet
        }

        // Delete the article
        $stmt = $pdo->prepare("DELETE FROM news_articles WHERE id = ?");
        $stmt->execute([$delete_id]);

        // Delete associated gallery images from disk
        foreach ($gallery_files as $gallery_file) {
            if (!empty($gallery_file) && file_exists('../' . $gallery_file)) {
                unlink('../' . $gallery_file);
            }
        }

        // Delete associated image if it exists
        if ($article && !empty($article['featured_image']) && file_exists('../' . $article['featured_image'])) {
            unlink('../' . $article['featured_image']);
        }
        
        $success_message = "Article deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Error deleting article: " . $e->getMessage();
    }
}

// Handle status toggle
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    try {
        $toggle_id = intval($_GET['toggle_status']);
        $stmt = $pdo->prepare("UPDATE news_articles SET status = CASE WHEN status = 'published' THEN 'draft' ELSE 'published' END WHERE id = ?");
        $stmt->execute([$toggle_id]);
        $success_message = "Article status updated!";
    } catch (PDOException $e) {
        $error_message = "Error updating status: " . $e->getMessage();
    }
}

// Handle featured toggle
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    try {
        $toggle_id = intval($_GET['toggle_featured']);
        $stmt = $pdo->prepare("UPDATE news_articles SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$toggle_id]);
        $success_message = "Featured status updated!";
    } catch (PDOException $e) {
        $error_message = "Error updating featured status: " . $e->getMessage();
    }
}

// Pagination and filtering
$items_per_page = 15;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

$filter_category = isset($_GET['category']) ? $_GET['category'] : 'all';
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_clauses = [];
$params = [];

if ($filter_category !== 'all') {
    $where_clauses[] = "category = ?";
    $params[] = $filter_category;
}

if ($filter_status !== 'all') {
    $where_clauses[] = "status = ?";
    $params[] = $filter_status;
}

if (!empty($search_query)) {
    $where_clauses[] = "(title LIKE ? OR excerpt LIKE ?)";
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total count
try {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM news_articles $where_sql");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
} catch (PDOException $e) {
    $total_items = 0;
    $total_pages = 1;
}

// Fetch articles
try {
    $sql = "
        SELECT id, title, slug, category, author, status, is_featured, views_count, publish_date, created_at
        FROM news_articles 
        $where_sql 
        ORDER BY created_at DESC 
        LIMIT " . intval($items_per_page) . " OFFSET " . intval($offset);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $articles = [];
    $error_message = "Error loading articles: " . $e->getMessage();
}


// Get stats
try {
    $stats = [
        'total' => $pdo->query("SELECT COUNT(*) FROM news_articles")->fetchColumn(),
        'published' => $pdo->query("SELECT COUNT(*) FROM news_articles WHERE status = 'published'")->fetchColumn(),
        'draft' => $pdo->query("SELECT COUNT(*) FROM news_articles WHERE status = 'draft'")->fetchColumn(),
        'featured' => $pdo->query("SELECT COUNT(*) FROM news_articles WHERE is_featured = 1")->fetchColumn(),
    ];
} catch (PDOException $e) {
    $stats = ['total' => 0, 'published' => 0, 'draft' => 0, 'featured' => 0];
}

$category_labels = [
    'news' => 'News',
    'events' => 'Events',
    'announcements' => 'Notices',
    'press_releases' => 'Press Releases',
    'academic' => 'Academic'
];

$page_title = "Manage News & Events";
include 'header.php';
include 'sidebar.php';
?>

<style>
/* News Management Specific Styles Refined */
.news-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 24px 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column; /* Vertical Layout */
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 12px;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.02);
    min-height: 160px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-icon.total { background: rgba(0,33,71,0.08); color: #002147; }
.stat-icon.published { background: rgba(46, 216, 182, 0.1); color: #2ed8b6; }
.stat-icon.draft { background: rgba(255,193,7,0.1); color: #e5a500; }
.stat-icon.featured { background: rgba(242,104,56,0.1); color: #f26838; }

.stat-content h4 {
    font-size: 28px;
    font-weight: 800;
    color: #002147;
    margin: 0;
    line-height: 1;
}

.stat-content p {
    color: #6c757d;
    margin: 6px 0 0 0;
    font-size: 13px;
    font-weight: 500;
}

.news-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

.toolbar-left {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.toolbar-right {
    display: flex;
    gap: 12px;
    align-items: center;
}

.filter-select {
    padding: 10px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    min-width: 150px;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #f26838;
}

.search-box {
    display: flex;
    align-items: center;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    background: white;
}

.search-box input {
    border: none;
    padding: 10px 16px;
    font-size: 14px;
    min-width: 250px;
}

.search-box input:focus {
    outline: none;
}

.search-box button {
    padding: 10px 16px;
    background: #f26838;
    color: white;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-box button:hover {
    background: #002147;
}

.btn-add-new {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #f26838 0%, #e85a2a 100%);
    color: white !important;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(242,104,56,0.3);
}

.btn-add-new:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(242,104,56,0.4);
    text-decoration: none;
}

.news-table-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.news-table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
}


.news-table th {
    background: #f8f9fa;
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    color: #002147;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
}

.news-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.news-table tr:hover {
    background: #f8f9fa;
}

.article-title-cell {
    max-width: 300px;
}

.article-title {
    font-weight: 600;
    color: #002147;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-slug {
    color: #6c757d;
    font-size: 12px;
}

.category-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.category-news { background: rgba(0,160,210,0.15); color: #00a0d2; }
.category-events { background: rgba(156,39,176,0.15); color: #9c27b0; }
.category-announcements { background: rgba(255,152,0,0.15); color: #ff9800; }
.category-press_releases { background: rgba(0,33,71,0.15); color: #002147; }
.category-academic { background: rgba(40,167,69,0.15); color: #28a745; }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.status-published {
    background: rgba(40,167,69,0.15);
    color: #28a745;
}

.status-draft {
    background: rgba(255,193,7,0.15);
    color: #e5a500;
}

.status-archived {
    background: rgba(108,117,125,0.15);
    color: #6c757d;
}

.featured-star {
    color: #ffc107;
    font-size: 18px;
}

.featured-star.inactive {
    color: #dee2e6;
}

.views-count {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6c757d;
}

.views-count i {
    color: #00a0d2;
}

.actions-cell {
    white-space: nowrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    margin-right: 6px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.action-btn.edit {
    background: rgba(0,160,210,0.1);
    color: #00a0d2;
}

.action-btn.view {
    background: rgba(40,167,69,0.1);
    color: #28a745;
}

.action-btn.delete {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
}

.action-btn.toggle {
    background: rgba(255,193,7,0.1);
    color: #e5a500;
}

.action-btn:hover {
    transform: scale(1.1);
}

.action-btn.edit:hover { background: #00a0d2; color: white; }
.action-btn.view:hover { background: #28a745; color: white; }
.action-btn.delete:hover { background: #dc3545; color: white; }
.action-btn.toggle:hover { background: #e5a500; color: white; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 60px;
    color: #dee2e6;
    margin-bottom: 20px;
}

.empty-state h4 {
    color: #002147;
    margin-bottom: 10px;
}

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.pagination-info {
    color: #6c757d;
    font-size: 14px;
}

.pagination {
    display: flex;
    gap: 6px;
}

.pagination a,
.pagination span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination a {
    background: white;
    color: #002147;
    border: 1px solid #e9ecef;
}

.pagination a:hover {
    background: #f26838;
    color: white;
    border-color: #f26838;
}

.pagination a.active {
    background: #002147;
    color: white;
    border-color: #002147;
}

.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: rgba(40,167,69,0.1);
    color: #28a745;
    border: 1px solid rgba(40,167,69,0.2);
}

.alert-danger {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
    border: 1px solid rgba(220,53,69,0.2);
}

@media screen and (max-width: 992px) {
    .news-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .news-toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .toolbar-left,
    .toolbar-right {
        flex-wrap: wrap;
    }
    
    .search-box input {
        min-width: 180px;
    }
}

@media screen and (max-width: 768px) {
    .news-stats {
        grid-template-columns: 1fr;
    }
    
    .news-table-wrapper {
        overflow-x: auto;
    }
    
    .news-table {
        min-width: 900px;
    }
}
</style>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h4><i class="fas fa-newspaper"></i> News & Events Management</h4>
        <p class="text-muted">Create, edit, and manage news articles and events</p>
    </div>

    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success_message); ?>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="news-stats">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo number_format($stats['total']); ?></h4>
                <p>Total Articles</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon published">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo number_format($stats['published']); ?></h4>
                <p>Published</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon draft">
                <i class="fas fa-edit"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo number_format($stats['draft']); ?></h4>
                <p>Drafts</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon featured">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo number_format($stats['featured']); ?></h4>
                <p>Featured</p>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="news-toolbar">
        <div class="toolbar-left">
            <form method="GET" class="d-flex gap-2">
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter_category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($category_labels as $key => $label): ?>
                    <option value="<?php echo $key; ?>" <?php echo $filter_category === $key ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="published" <?php echo $filter_status === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo $filter_status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="archived" <?php echo $filter_status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
                <?php if (!empty($search_query)): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <?php endif; ?>
            </form>
        </div>
        <div class="toolbar-right">
            <form method="GET" class="search-box">
                <?php if ($filter_category !== 'all'): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($filter_category); ?>">
                <?php endif; ?>
                <?php if ($filter_status !== 'all'): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="Search articles..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <a href="edit_news_article.php?action=add" class="btn-add-new">
                <i class="fas fa-plus"></i> Add New Article
            </a>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="news-table-wrapper">
        <?php if (empty($articles)): ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <h4>No Articles Found</h4>
            <p>No articles match your current filters. Try adjusting your search or <a href="edit_news_article.php?action=add">create a new article</a>.</p>
        </div>
        <?php else: ?>
        <table class="news-table">
            <thead>
                <tr>
                    <th width="40">ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th width="50">Featured</th>
                    <th>Views</th>
                    <th>Date</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                <tr>
                    <td><strong>#<?php echo $article['id']; ?></strong></td>
                    <td class="article-title-cell">
                        <div class="article-title"><?php echo htmlspecialchars($article['title']); ?></div>
                        <div class="article-slug">/<?php echo htmlspecialchars($article['slug']); ?></div>
                    </td>
                    <td>
                        <span class="category-badge category-<?php echo $article['category']; ?>">
                            <?php echo $category_labels[$article['category']] ?? $article['category']; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($article['author']); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $article['status']; ?>">
                            <i class="fas fa-<?php echo $article['status'] === 'published' ? 'check' : ($article['status'] === 'draft' ? 'edit' : 'archive'); ?>"></i>
                            <?php echo ucfirst($article['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="?toggle_featured=<?php echo $article['id']; ?>&<?php echo http_build_query(array_filter(['category' => $filter_category !== 'all' ? $filter_category : null, 'status' => $filter_status !== 'all' ? $filter_status : null, 'search' => $search_query, 'page' => $current_page])); ?>" class="featured-star <?php echo $article['is_featured'] ? '' : 'inactive'; ?>" title="Toggle Featured">
                            <i class="fas fa-star"></i>
                        </a>
                    </td>
                    <td>
                        <span class="views-count">
                            <i class="fas fa-eye"></i>
                            <?php echo number_format($article['views_count']); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo date('M j, Y', strtotime($article['publish_date'])); ?>
                        <br>
                        <small class="text-muted"><?php echo date('g:i A', strtotime($article['publish_date'])); ?></small>
                    </td>
                    <td class="actions-cell">
                        <a href="edit_news_article.php?id=<?php echo $article['id']; ?>" class="action-btn edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="../news_detail.php?slug=<?php echo urlencode($article['slug']); ?>" target="_blank" class="action-btn view" title="View">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="?toggle_status=<?php echo $article['id']; ?>&<?php echo http_build_query(array_filter(['category' => $filter_category !== 'all' ? $filter_category : null, 'status' => $filter_status !== 'all' ? $filter_status : null, 'search' => $search_query, 'page' => $current_page])); ?>" class="action-btn toggle" title="Toggle Status">
                            <i class="fas fa-<?php echo $article['status'] === 'published' ? 'eye-slash' : 'eye'; ?>"></i>
                        </a>
                        <a href="?delete=<?php echo $article['id']; ?>&<?php echo http_build_query(array_filter(['category' => $filter_category !== 'all' ? $filter_category : null, 'status' => $filter_status !== 'all' ? $filter_status : null, 'search' => $search_query, 'page' => $current_page])); ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this article? This action cannot be undone.')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing <?php echo $offset + 1; ?> - <?php echo min($offset + $items_per_page, $total_items); ?> of <?php echo $total_items; ?> articles
            </div>
            <div class="pagination">
                <?php
                $base_url = 'manage_news.php?';
                $url_params = [];
                if ($filter_category !== 'all') $url_params[] = 'category=' . urlencode($filter_category);
                if ($filter_status !== 'all') $url_params[] = 'status=' . urlencode($filter_status);
                if (!empty($search_query)) $url_params[] = 'search=' . urlencode($search_query);
                $base_url .= implode('&', $url_params) . (count($url_params) > 0 ? '&' : '');
                ?>
                
                <?php if ($current_page > 1): ?>
                <a href="<?php echo $base_url . 'page=' . ($current_page - 1); ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if ($start_page > 1): ?>
                <a href="<?php echo $base_url . 'page=1'; ?>">1</a>
                <?php if ($start_page > 2): ?>
                <span>...</span>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="<?php echo $base_url . 'page=' . $i; ?>" class="<?php echo $i === $current_page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?>
                <span>...</span>
                <?php endif; ?>
                <a href="<?php echo $base_url . 'page=' . $total_pages; ?>"><?php echo $total_pages; ?></a>
                <?php endif; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo $base_url . 'page=' . ($current_page + 1); ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
</div>

<?php include 'footer.php'; ?>
