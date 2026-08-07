<?php
require_once('../includes/db_connect.php');
require_once('../includes/slider_settings.php');
require_once('../includes/video_helper.php');

// Make sure the slider timing table/column exist, then handle a save
vvu_slider_install($pdo);
$slider_timing_saved = false;
$slider_timing_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_slider_timing'])) {
    try {
        vvu_slider_save(
            $pdo,
            $_POST['interval_seconds'] ?? 5,
            isset($_POST['pause_on_hover']),
            isset($_POST['autoplay'])
        );
        $slider_timing_saved = true;
    } catch (PDOException $e) {
        $slider_timing_error = 'Could not save slider timing: ' . $e->getMessage();
    }
}

$slider_timing = vvu_slider_settings($pdo);

// ---- Campus video save ----------------------------------------------------
// Whatever YouTube link shape the editor pastes is normalised to a playable
// /embed/ URL before it hits the database.
$video_saved   = false;
$video_error   = '';
$video_warning = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_video'])) {
    try {
        $video_url = vvu_video_embed($_POST['video_url'] ?? '');

        if (trim($video_url) === '') {
            $video_error = 'Please paste a YouTube link.';
        } else {
            $stmt = $pdo->prepare(
                "UPDATE homepage_video SET video_url=?, title=?, description=?, is_active=? WHERE id=?"
            );
            $stmt->execute([
                $video_url,
                trim($_POST['video_title'] ?? ''),
                trim($_POST['video_description'] ?? ''),
                isset($_POST['video_is_active']) ? 1 : 0,
                (int) ($_POST['video_id'] ?? 1),
            ]);
            $video_saved = true;

            if (vvu_youtube_id($video_url) === null) {
                $video_warning = "That doesn't look like a YouTube link. It was saved as-is — check the homepage to confirm it plays.";
            }
        }
    } catch (PDOException $e) {
        $video_error = 'Could not save the video: ' . $e->getMessage();
    }
}

include 'header.php';
include 'sidebar.php';

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
                                <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video-content" type="button">
                                    <i class="fas fa-video"></i> Campus Video
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

                                    <!-- ===== Slide timing ===== -->
                                    <?php if ($slider_timing_saved): ?>
                                    <div class="alert alert-success py-2">
                                        <i class="fas fa-check-circle"></i> Slider timing saved &mdash; the homepage now changes slide every
                                        <strong><?php echo (int)$slider_timing['interval_seconds']; ?> second<?php echo $slider_timing['interval_seconds'] == 1 ? '' : 's'; ?></strong>.
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($slider_timing_error): ?>
                                    <div class="alert alert-danger py-2"><?php echo htmlspecialchars($slider_timing_error); ?></div>
                                    <?php endif; ?>

                                    <div class="slider-timing-panel mb-4">
                                        <form method="POST" class="slider-timing-form">
                                            <input type="hidden" name="save_slider_timing" value="1">

                                            <div class="timing-head">
                                                <div>
                                                    <h6 class="mb-1"><i class="fas fa-stopwatch"></i> Slide Timing</h6>
                                                    <p class="text-muted mb-0" style="font-size:13px;">
                                                        How long each hero image stays on screen before the next one slides in.
                                                    </p>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-save"></i> Save timing
                                                </button>
                                            </div>

                                            <div class="timing-body">
                                                <div class="timing-control">
                                                    <label for="intervalRange">Time per slide</label>
                                                    <div class="timing-slider">
                                                        <input type="range" id="intervalRange" min="2" max="30" step="1"
                                                               value="<?php echo (int)$slider_timing['interval_seconds']; ?>">
                                                        <div class="timing-value">
                                                            <input type="number" name="interval_seconds" id="intervalNumber"
                                                                   min="1" max="60" step="1"
                                                                   value="<?php echo (int)$slider_timing['interval_seconds']; ?>">
                                                            <span>seconds</span>
                                                        </div>
                                                    </div>
                                                    <div class="timing-presets">
                                                        <span>Quick set:</span>
                                                        <button type="button" class="preset" data-seconds="3">3s</button>
                                                        <button type="button" class="preset" data-seconds="5">5s</button>
                                                        <button type="button" class="preset" data-seconds="8">8s</button>
                                                        <button type="button" class="preset" data-seconds="12">12s</button>
                                                    </div>
                                                </div>

                                                <div class="timing-toggles">
                                                    <label class="timing-check">
                                                        <input type="checkbox" name="autoplay" id="autoplayToggle"
                                                               <?php echo $slider_timing['autoplay'] ? 'checked' : ''; ?>>
                                                        <span>
                                                            <strong>Change slides automatically</strong>
                                                            <small>Turn off to let visitors move through the slides themselves.</small>
                                                        </span>
                                                    </label>
                                                    <label class="timing-check">
                                                        <input type="checkbox" name="pause_on_hover"
                                                               <?php echo $slider_timing['pause_on_hover'] ? 'checked' : ''; ?>>
                                                        <span>
                                                            <strong>Pause while the mouse is over the slider</strong>
                                                            <small>Gives people time to read a slide they stopped on.</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <p class="timing-note">
                                                <i class="fas fa-info-circle"></i>
                                                This is the default for every slide. To give one slide its own timing,
                                                set <strong>Display time</strong> when editing that slide.
                                            </p>
                                        </form>
                                    </div>

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
                                                    <th>Display time</th>
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
                                                        <?php $own = isset($slider['slide_interval']) ? (int)$slider['slide_interval'] : 0; ?>
                                                        <?php if ($own > 0): ?>
                                                            <span class="badge" style="background:#0d6efd;"><?php echo $own; ?>s</span>
                                                        <?php else: ?>
                                                            <span class="text-muted" style="font-size:12px;"><?php echo (int)$slider_timing['interval_seconds']; ?>s (default)</span>
                                                        <?php endif; ?>
                                                    </td>
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

                                <!-- CAMPUS VIDEO TAB -->
                                <div class="tab-pane fade" id="video-content" role="tabpanel">
                                    <?php if ($video_saved): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-1"></i> Campus video updated.
                                            <?php if ($video_warning): ?>
                                                <div class="mt-1"><small><?php echo htmlspecialchars($video_warning); ?></small></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($video_error): ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <?php echo htmlspecialchars($video_error); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0"><i class="fas fa-video me-2"></i>Latest Campus Video</h6>
                                        <span class="badge <?php echo ($video['is_active'] ?? 1) ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ($video['is_active'] ?? 1) ? 'Visible on homepage' : 'Hidden'; ?>
                                        </span>
                                    </div>

                                    <!-- ?tab=video so the existing tab-restore script reopens this
                                         panel after the POST, keeping the result message visible -->
                                    <form method="POST" action="manage_homepage_content.php?tab=video">
                                        <input type="hidden" name="save_video" value="1">
                                        <input type="hidden" name="video_id" value="<?php echo (int) ($video['id'] ?? 1); ?>">

                                        <div class="row">
                                            <div class="col-lg-7">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" for="video_url">YouTube Link</label>
                                                    <input type="text" class="form-control" id="video_url" name="video_url"
                                                           value="<?php echo htmlspecialchars($video['video_url'] ?? ''); ?>"
                                                           placeholder="https://youtu.be/..." required>
                                                    <div class="form-text">
                                                        Paste any YouTube link &mdash; the Share link (<code>youtu.be/…</code>),
                                                        the browser address bar (<code>youtube.com/watch?v=…</code>), a Short,
                                                        or an embed URL. It is converted automatically when you save.
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" for="video_title">Title</label>
                                                    <input type="text" class="form-control" id="video_title" name="video_title"
                                                           value="<?php echo htmlspecialchars($video['title'] ?? ''); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" for="video_description">Description</label>
                                                    <textarea class="form-control" id="video_description" name="video_description"
                                                              rows="5"><?php echo htmlspecialchars($video['description'] ?? ''); ?></textarea>
                                                </div>

                                                <div class="form-check form-switch mb-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                           id="video_is_active" name="video_is_active" value="1"
                                                           <?php echo ($video['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="video_is_active">
                                                        Show this video on the homepage
                                                    </label>
                                                </div>

                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> Save Video
                                                </button>
                                            </div>

                                            <!-- Live preview: confirms the link plays before saving -->
                                            <div class="col-lg-5">
                                                <label class="form-label fw-semibold">Preview</label>
                                                <div style="position: relative; width: 100%; aspect-ratio: 16/9; background: #000; border-radius: 10px; overflow: hidden;">
                                                    <iframe id="video_preview" src="" allowfullscreen
                                                            style="position: absolute; inset: 0; width: 100%; height: 100%; border: 0;"></iframe>
                                                </div>
                                                <p id="video_preview_msg" class="text-danger small mt-2 mb-0"></p>
                                                <p class="text-muted small mt-2 mb-0">
                                                    Press play here to make sure the video works before saving.
                                                    If it says the video is unavailable, the link is wrong or the
                                                    YouTube account was removed.
                                                </p>
                                            </div>
                                        </div>
                                    </form>
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
    /* ===== Slide timing panel ===== */
    .slider-timing-panel {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        border: 1px solid #e3e9f0;
        border-radius: 12px;
        padding: 20px 22px;
    }

    .timing-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 16px;
        margin-bottom: 18px;
        border-bottom: 1px solid #e3e9f0;
    }

    .timing-head h6 {
        font-weight: 700;
        color: #002147;
    }

    .timing-head h6 i {
        color: #f26838;
        margin-right: 6px;
    }

    .timing-body {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 26px;
    }

    .timing-control > label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        color: #002147;
        margin-bottom: 10px;
    }

    .timing-slider {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .timing-slider input[type="range"] {
        flex: 1;
        min-width: 0;
        accent-color: #f26838;
        height: 6px;
        cursor: pointer;
    }

    .timing-value {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid #d8e0ea;
        border-radius: 8px;
        padding: 6px 10px;
        white-space: nowrap;
    }

    .timing-value input {
        width: 52px;
        border: none;
        outline: none;
        font-size: 18px;
        font-weight: 700;
        color: #002147;
        text-align: center;
    }

    .timing-value span {
        font-size: 12px;
        color: #6c757d;
    }

    .timing-presets {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        font-size: 12px;
        color: #6c757d;
    }

    .timing-presets .preset {
        border: 1px solid #d8e0ea;
        background: #fff;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #002147;
        cursor: pointer;
        transition: all .2s ease;
    }

    .timing-presets .preset:hover,
    .timing-presets .preset.is-current {
        background: #f26838;
        border-color: #f26838;
        color: #fff;
    }

    .timing-toggles {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .timing-check {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #fff;
        border: 1px solid #e3e9f0;
        border-radius: 8px;
        padding: 12px 14px;
        margin: 0;
        cursor: pointer;
    }

    .timing-check input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        accent-color: #f26838;
        cursor: pointer;
        flex-shrink: 0;
    }

    .timing-check strong {
        display: block;
        font-size: 13.5px;
        color: #002147;
        font-weight: 600;
    }

    .timing-check small {
        display: block;
        font-size: 12px;
        color: #6c757d;
        line-height: 1.5;
    }

    .timing-note {
        margin: 16px 0 0;
        padding-top: 14px;
        border-top: 1px dashed #dbe3ec;
        font-size: 12.5px;
        color: #6c757d;
    }

    .timing-note i {
        color: #0d6efd;
    }

    .slider-timing-panel.is-off .timing-control {
        opacity: .45;
        pointer-events: none;
    }

    @media (max-width: 992px) {
        .timing-body {
            grid-template-columns: 1fr;
            gap: 18px;
        }
    }

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
    // ===== Slide timing: keep range, number box and presets in sync =====
    document.addEventListener('DOMContentLoaded', function () {
        var range = document.getElementById('intervalRange');
        var number = document.getElementById('intervalNumber');
        var autoplay = document.getElementById('autoplayToggle');
        var panel = document.querySelector('.slider-timing-panel');
        if (!range || !number) return;

        function markPreset(value) {
            document.querySelectorAll('.timing-presets .preset').forEach(function (b) {
                b.classList.toggle('is-current', parseInt(b.dataset.seconds, 10) === value);
            });
        }

        function apply(value, from) {
            value = parseInt(value, 10);
            if (isNaN(value)) return;
            value = Math.min(60, Math.max(1, value));
            if (from !== 'number') number.value = value;
            // The range only covers 2–30; keep it at the nearest end otherwise
            if (from !== 'range') range.value = Math.min(30, Math.max(2, value));
            markPreset(value);
        }

        range.addEventListener('input', function () { apply(this.value, 'range'); });
        number.addEventListener('input', function () { apply(this.value, 'number'); });

        document.querySelectorAll('.timing-presets .preset').forEach(function (btn) {
            btn.addEventListener('click', function () { apply(this.dataset.seconds, 'preset'); });
        });

        if (autoplay && panel) {
            var reflectAutoplay = function () {
                panel.classList.toggle('is-off', !autoplay.checked);
            };
            autoplay.addEventListener('change', reflectAutoplay);
            reflectAutoplay();
        }

        markPreset(parseInt(number.value, 10));
    });

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

    /* ===== Campus video: live preview =====
       Mirrors vvu_youtube_id() in includes/video_helper.php so the editor can
       confirm the link plays before saving. */
    (function () {
        const input   = document.getElementById('video_url');
        const frame   = document.getElementById('video_preview');
        const message = document.getElementById('video_preview_msg');
        if (!input || !frame) return;

        const patterns = [
            /youtube\.com\/watch\?(?:.*&)?v=([A-Za-z0-9_-]{11})/i,
            /youtu\.be\/([A-Za-z0-9_-]{11})/i,
            /youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i,
            /youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/i,
            /youtube\.com\/live\/([A-Za-z0-9_-]{11})/i,
            /youtube\.com\/v\/([A-Za-z0-9_-]{11})/i
        ];

        function youtubeId(url) {
            url = (url || '').trim();
            if (/^[A-Za-z0-9_-]{11}$/.test(url)) return url;
            for (const pattern of patterns) {
                const match = url.match(pattern);
                if (match) return match[1];
            }
            return null;
        }

        function refresh() {
            const id = youtubeId(input.value);
            if (id) {
                const next = 'https://www.youtube.com/embed/' + id;
                if (frame.src !== next) frame.src = next;   // don't reload while typing
                message.textContent = '';
            } else {
                frame.removeAttribute('src');
                message.textContent = input.value.trim()
                    ? "Couldn't read a YouTube video ID from that link. Check it before saving."
                    : '';
            }
        }

        input.addEventListener('input', refresh);
        input.addEventListener('change', refresh);
        refresh();
    })();
    </script>

<?php include 'footer.php'; ?>
