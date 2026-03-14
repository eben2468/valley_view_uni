<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');

// Fetch all existing homepage content
$sliders = $pdo->query("SELECT * FROM homepage_sliders ORDER BY display_order ASC")->fetchAll();
$sections = $pdo->query("SELECT * FROM homepage_sections ORDER BY section_key ASC")->fetchAll();
$discover_cards = $pdo->query("SELECT * FROM homepage_discover_cards ORDER BY display_order ASC")->fetchAll();
$programs = $pdo->query("SELECT * FROM homepage_programs ORDER BY display_order ASC")->fetchAll();
$gallery = $pdo->query("SELECT * FROM homepage_gallery ORDER BY display_order ASC")->fetchAll();
$news = $pdo->query("SELECT * FROM homepage_news ORDER BY display_order ASC")->fetchAll();
$video = $pdo->query("SELECT * FROM homepage_video LIMIT 1")->fetch();

$stats_banner = $pdo->query("SELECT * FROM homepage_stats_banner WHERE id=1")->fetch();
$stats_items = $pdo->query("SELECT * FROM homepage_stats_items ORDER BY display_order ASC")->fetchAll();
$study_options = $pdo->query("SELECT * FROM homepage_study_options ORDER BY display_order ASC")->fetchAll();

// Get statistics
$stats = [
    'sliders' => count($sliders),
    'discover_cards' => count($discover_cards),
    'programs' => count($programs),
    'gallery' => count($gallery),
    'news' => count($news),
    'stats' => count($stats_items),
    'study_options' => count($study_options)
];
?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Stats Overview -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stat-card stat-card-orange">
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['sliders']; ?></div>
                            <div class="stat-label">Hero Sliders</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-images" style="font-size: 40px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-green">
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['programs']; ?></div>
                            <div class="stat-label">Programs</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap" style="font-size: 40px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-pink">
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['stats']; ?></div>
                            <div class="stat-label">Stats Items</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line" style="font-size: 40px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-cyan">
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['study_options']; ?></div>
                            <div class="stat-label">Study Options</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-book-open" style="font-size: 40px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Sections -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5>Manage Homepage Content</h5>
                            <p class="text-muted">Edit all sections and content displayed on the homepage</p>
                        </div>
                        <div class="card-body">
                            <div class="nav nav-tabs" id="contentTabs" role="tablist">
                                <button class="nav-link active" id="sliders-tab" data-bs-toggle="tab" data-bs-target="#sliders" type="button">
                                    <i class="fas fa-images"></i> Hero Sliders
                                </button>
                                <button class="nav-link" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections-content" type="button">
                                    <i class="fas fa-heading"></i> Section Titles
                                </button>
                                <button class="nav-link" id="discover-tab" data-bs-toggle="tab" data-bs-target="#discover" type="button">
                                    <i class="fas fa-th-large"></i> Discover Cards
                                </button>
                                <button class="nav-link" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs-content" type="button">
                                    <i class="fas fa-graduation-cap"></i> Programs
                                </button>
                                <button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#news-content" type="button">
                                    <i class="fas fa-newspaper"></i> News & Events
                                </button>
                                <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery-content" type="button">
                                    <i class="fas fa-image"></i> Gallery
                                </button>
                                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats-content" type="button">
                                    <i class="fas fa-chart-line"></i> Stats Banner
                                </button>
                                <button class="nav-link" id="study-tab" data-bs-toggle="tab" data-bs-target="#study-content" type="button">
                                    <i class="fas fa-book-open"></i> Study Options
                                </button>
                            </div>
                            
                            <div class="tab-content mt-4" id="contentTabContent">
                                <!-- HERO SLIDERS TAB -->
                                <div class="tab-pane fade show active" id="sliders" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Hero Sliders</h6>
                                        <a href="edit_slider.php?action=add" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Slider
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Position</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($sliders as $slider): ?>
                                                <tr>
                                                    <td><?php echo $slider['display_order']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($slider['title']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars(substr($slider['description'], 0, 50)) . '...'; ?></td>
                                                    <td><span class="badge" style="background: #6f42c1;"><?php echo htmlspecialchars($slider['content_position'] ?? 'middle-center'); ?></span></td>
                                                    <td>
                                                        <?php if ($slider['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge" style="background: #999;">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_slider.php?id=<?php echo $slider['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- SECTION TITLES TAB -->
                                <div class="tab-pane fade" id="sections-content" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Section Titles & Subtitles</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Section Key</th>
                                                    <th>Title</th>
                                                    <th>Subtitle</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($sections as $section): ?>
                                                <tr>
                                                    <td><code><?php echo htmlspecialchars($section['section_key']); ?></code></td>
                                                    <td><strong><?php echo htmlspecialchars($section['section_title']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars(substr($section['section_subtitle'], 0, 50)) . '...'; ?></td>
                                                    <td>
                                                        <?php if ($section['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge" style="background: #999;">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- DISCOVER CARDS TAB -->
                                <div class="tab-pane fade" id="discover" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Discover More Cards</h6>
                                        <a href="edit_discover.php?action=add" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Card
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Title</th>
                                                    <th>Link</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($discover_cards as $card): ?>
                                                <tr>
                                                    <td><?php echo $card['display_order']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($card['title']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($card['link_url']); ?></td>
                                                    <td>
                                                        <?php if ($card['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge" style="background: #999;">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_discover.php?id=<?php echo $card['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- PROGRAMS TAB -->
                                <div class="tab-pane fade" id="programs-content" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Popular Programs</h6>
                                        <a href="edit_program.php?action=add" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Program
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Rating</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($programs as $program): ?>
                                                <tr>
                                                    <td><?php echo $program['display_order']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($program['title']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($program['category']); ?></td>
                                                    <td><?php echo $program['rating']; ?></td>
                                                    <td>
                                                        <?php if ($program['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge" style="background: #999;">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_program.php?id=<?php echo $program['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- NEWS & EVENTS TAB -->
                                <div class="tab-pane fade" id="news-content" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Latest News & Events</h6>
                                        <a href="edit_news.php?action=add" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Item
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Event Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($news as $item): ?>
                                                <tr>
                                                    <td><?php echo $item['display_order']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($item['event_date'])); ?></td>
                                                    <td>
                                                        <?php if ($item['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge" style="background: #999;">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_news.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- GALLERY TAB -->
                                <div class="tab-pane fade" id="gallery-content" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Photo Gallery</h6>
                                        <a href="edit_gallery.php?action=add" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Image
                                        </a>
                                    </div>
                                    <div class="row">
                                        <?php foreach ($gallery as $image): ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="gallery-item">
                                                <img src="../<?php echo htmlspecialchars($image['image_url']); ?>" alt="<?php echo htmlspecialchars($image['caption']); ?>" class="img-fluid" style="border-radius: 8px;">
                                                <div class="mt-2">
                                                    <small class="text-muted"><?php echo htmlspecialchars($image['caption']); ?></small>
                                                    <div class="mt-1">
                                                        <a href="edit_gallery.php?id=<?php echo $image['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- STATS BANNER TAB -->
                                <div class="tab-pane fade" id="stats-content" role="tabpanel">
                                    <div class="mb-5 p-4 border rounded bg-light shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Banner Configuration</h6>
                                            <a href="edit_stats_banner.php" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit Banner Text & Image
                                            </a>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center">
                                                <?php if (!empty($stats_banner['bg_image'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($stats_banner['bg_image']); ?>" class="img-fluid rounded border" style="max-height: 120px;">
                                                <?php else: ?>
                                                    <div class="p-4 bg-secondary text-white rounded"><i class="fas fa-image fa-2x"></i></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-9">
                                                <p><strong>Banner Text:</strong></p>
                                                <p class="text-muted border-start ps-3 py-1"><?php echo nl2br(htmlspecialchars($stats_banner['banner_text'] ?? '')); ?></p>
                                                <div>
                                                    <span class="badge <?php echo ($stats_banner['is_active'] ?? 1) ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo ($stats_banner['is_active'] ?? 1) ? 'Visible' : 'Hidden'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6><i class="fas fa-list me-2"></i>Display Statistics</h6>
                                        <a href="edit_stats_item.php?action=add" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Stat
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Label</th>
                                                    <th>Value</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($stats_items as $item): ?>
                                                <tr>
                                                    <td><?php echo $item['display_order']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($item['label']); ?></strong></td>
                                                    <td><span class="badge" style="background: #20c997;"><?php echo htmlspecialchars($item['value']); ?></span></td>
                                                    <td>
                                                        <?php if ($item['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge" style="background: #999;">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_stats_item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- STUDY OPTIONS TAB -->
                                <div class="tab-pane fade" id="study-content" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6>Homepage Study Cards</h6>
                                        <a href="edit_study_option.php?action=add" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Card
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Title</th>
                                                    <th>Accent</th>
                                                    <th>Buttons</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($study_options as $option): ?>
                                                <tr>
                                                    <td><?php echo $option['display_order']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($option['title']); ?></strong></td>
                                                    <td>
                                                        <span class="d-inline-block rounded-circle" style="width:15px; height:15px; background:<?php echo $option['accent_color']; ?>; border:1px solid #ccc;"></span>
                                                        <small class="ms-1"><?php echo $option['accent_color']; ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <small class="text-truncate" style="max-width:150px;">1: <?php echo htmlspecialchars($option['btn1_text']); ?></small>
                                                            <small class="text-truncate" style="max-width:150px;">2: <?php echo htmlspecialchars($option['btn2_text']); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($option['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge" style="background: #999;">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_study_option.php?id=<?php echo $option['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
    .nav-tabs {
        border-bottom: 2px solid #e0e0e0;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #666;
        padding: 12px 20px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .nav-tabs .nav-link:hover {
        color: #4680ff;
        background: #f8f9fa;
    }
    .nav-tabs .nav-link.active {
        color: #4680ff;
        border-bottom: 3px solid #4680ff;
        background: transparent;
    }
    .gallery-item {
        border: 1px solid #e0e0e0;
        padding: 10px;
        border-radius: 8px;
        transition: box-shadow 0.2s;
    }
    .gallery-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    code {
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        color: #4680ff;
        font-size: 12px;
    }
    </style>

    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        
        if (activeTab) {
            const tabEl = document.querySelector(`#${activeTab}-tab`);
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
    });
    </script>

<?php include 'footer.php'; ?>
