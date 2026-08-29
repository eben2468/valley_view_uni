<?php
/**
 * Admin - Manage Application & Resources Pages
 * Manages content for: fees-structure.php, why_choose_vvu.php, 
 * download-forms.php, mature-entrance.php, degree_and_diploma_in_music.php
 */

require_once __DIR__ . '/../includes/admin_auth.php';
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Application & Resources";
$current_page = 'manage_resources_pages.php';

// Define the pages we manage
$managed_pages = [
    'fees_structure' => [
        'title' => 'Fee Structure',
        'icon' => 'fa-file-invoice-dollar',
        'file' => 'fees-structure.php',
        'description' => 'Manage tuition fees, payment methods, and financial policies.'
    ],
    'why_choose_vvu' => [
        'title' => 'Why Choose VVU',
        'icon' => 'fa-star',
        'file' => 'why_choose_vvu.php',
        'description' => 'Manage mission, achievements, and unique campus features.'
    ],
    'download_forms' => [
        'title' => 'Download Forms',
        'icon' => 'fa-file-download',
        'file' => 'download-forms.php',
        'description' => 'Manage all downloadable application forms by category.'
    ],
    'mature_entrance' => [
        'title' => 'Mature Entrance',
        'icon' => 'fa-user-graduate',
        'file' => 'mature-entrance.php',
        'description' => 'Manage mature entrance programs, sessions, and requirements.'
    ],
    'music_programs' => [
        'title' => 'Music Programs',
        'icon' => 'fa-music',
        'file' => 'degree_and_diploma_in_music.php',
        'description' => 'Manage music degree and diploma program details and careers.'
    ],
    'provisional_admission_list' => [
        'title' => 'Admission List',
        'icon' => 'fa-list-check',
        'file' => 'provisional_admission_list.php',
        'description' => 'Manage the admission list page including hero, PDF lists, and guidance sections.'
    ],
    'scholarships' => [
        'title' => 'Scholarships & Aid',
        'icon' => 'fa-hand-holding-dollar',
        'file' => 'scholarships.php',
        'description' => 'Manage scholarship categories, process steps, and success stories.'
    ]
];

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'update_page_content') {
            // Update main page content
            $page_key = $_POST['page_key'];
            
            // Handle image upload
            $hero_image = $_POST['hero_image'];
            if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'resources');
                if ($uploaded) $hero_image = $uploaded;
            }

            $stmt = $pdo->prepare("
                UPDATE academic_pages_content SET
                    hero_badge = ?,
                    hero_title = ?,
                    hero_subtitle = ?,
                    hero_description = ?,
                    hero_image = ?,
                    cta_title = ?,
                    cta_subtitle = ?,
                    cta_button_text = ?,
                    cta_button_link = ?
                WHERE page_key = ?
            ");
            $stmt->execute([
                $_POST['hero_badge'] ?? '',
                $_POST['hero_title'] ?? '',
                $_POST['hero_subtitle'] ?? '',
                $_POST['hero_description'] ?? '',
                $hero_image,
                $_POST['cta_title'] ?? '',
                $_POST['cta_subtitle'] ?? '',
                $_POST['cta_button_text'] ?? '',
                $_POST['cta_button_link'] ?? '',
                $page_key
            ]);
            
            $message = "Page content updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'update_item') {
            // Update an item
            $item_image = $_POST['item_image'] ?? '';
            if (isset($_FILES['item_image_file']) && $_FILES['item_image_file']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['item_image_file'], 'resources');
                if ($uploaded) $item_image = $uploaded;
            }

            $item_link = vvu_resource_item_link($_POST['page_key'] ?? $_GET['page'] ?? '');

            $stmt = $pdo->prepare("
                UPDATE academic_pages_items SET
                    item_title = ?,
                    item_subtitle = ?,
                    item_description = ?,
                    item_icon = ?,
                    item_color = ?,
                    item_image = ?,
                    item_link = ?,
                    item_stat_value = ?,
                    extra_data = ?,
                    is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['item_title'] ?? '',
                $_POST['item_subtitle'] ?? '',
                $_POST['item_description'] ?? '',
                $_POST['item_icon'] ?? '',
                $_POST['item_color'] ?? 'blue-600',
                $item_image,
                $item_link,
                $_POST['item_stat_value'] ?? '',
                $_POST['item_extra_data'] ?? null,
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['item_id']
            ]);
            $message = "Item updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'add_item') {
            $item_image = $_POST['item_image'] ?? '';
            if (isset($_FILES['item_image_file']) && $_FILES['item_image_file']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['item_image_file'], 'resources');
                if ($uploaded) $item_image = $uploaded;
            }

            $item_link = vvu_resource_item_link($_POST['page_key'] ?? '');

            $stmt = $pdo->prepare("
                INSERT INTO academic_pages_items (page_key, section_key, item_title, item_subtitle, item_description, item_icon, item_color, item_image, item_link, item_stat_value, extra_data, display_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM academic_pages_items i2 WHERE i2.page_key = ? AND i2.section_key = ?))
            ");
            $stmt->execute([
                $_POST['page_key'],
                $_POST['section_key'],
                $_POST['item_title'] ?? '',
                $_POST['item_subtitle'] ?? '',
                $_POST['item_description'] ?? '',
                $_POST['item_icon'] ?? '',
                $_POST['item_color'] ?? 'blue-600',
                $item_image,
                $item_link,
                $_POST['item_stat_value'] ?? '',
                $_POST['item_extra_data'] ?? null,
                $_POST['page_key'],
                $_POST['section_key']
            ]);
            $message = "Item added successfully!";
            $message_type = "success";
            
        } elseif ($action === 'delete_item') {
            $stmt = $pdo->prepare("DELETE FROM academic_pages_items WHERE id = ?");
            $stmt->execute([$_POST['item_id']]);
            $message = "Item deleted successfully!";
            $message_type = "success";
            
        } elseif ($action === 'update_section') {
            $section_image = $_POST['section_image'] ?? '';
            if (isset($_FILES['section_image_file']) && $_FILES['section_image_file']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['section_image_file'], 'resources');
                if ($uploaded) $section_image = $uploaded;
            }

            $section_image_2 = $_POST['section_image_2'] ?? '';
            if (isset($_FILES['section_image_file_2']) && $_FILES['section_image_file_2']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['section_image_file_2'], 'resources');
                if ($uploaded) $section_image_2 = $uploaded;
            }

            $stmt = $pdo->prepare("UPDATE academic_pages_sections SET section_title = ?, section_subtitle = ?, section_description = ?, section_image = ?, section_image_2 = ? WHERE id = ?");
            $stmt->execute([$_POST['section_title'], $_POST['section_subtitle'] ?? '', $_POST['section_description'] ?? '', $section_image, $section_image_2, $_POST['section_id']]);
            $message = "Section updated successfully!";
            $message_type = "success";
        }
        
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Get current page selection
$current_page_key = $_GET['page'] ?? 'fees_structure';
if (!isset($managed_pages[$current_page_key])) {
    $current_page_key = 'fees_structure';
}

// Fetch page content
try {
    // Main content
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ?");
    $stmt->execute([$current_page_key]);
    $page_content = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    
    // Sections
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
    $stmt->execute([$current_page_key]);
    $page_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Items by section
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? ORDER BY section_key, display_order");
    $stmt->execute([$current_page_key]);
    $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_items = [];
    foreach ($all_items as $item) {
        $page_items[$item['section_key']][] = $item;
    }
    
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $message_type = "error";
}

// Include admin header
include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
    <div class="content-wrapper">
        <div class="page-header">
            <h1><i class="fas fa-file-invoice"></i> Manage Application & Resources</h1>
            <p class="page-description">Update content for fee structures, download forms, and more.</p>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 25px; padding: 15px; border-radius: 10px;">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Page Tabs -->
        <div class="page-tabs" style="display: flex; gap: 10px; margin-bottom: 30px; overflow-x: auto; padding-bottom: 10px;">
            <?php foreach ($managed_pages as $key => $info): ?>
            <a href="?page=<?php echo $key; ?>" 
               class="page-tab <?php echo $current_page_key === $key ? 'active' : ''; ?>"
               style="display: flex; align-items: center; gap: 10px; padding: 15px 25px; background: <?php echo $current_page_key === $key ? 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)' : '#fff'; ?>; color: <?php echo $current_page_key === $key ? '#fff' : '#333'; ?>; border-radius: 14px; text-decoration: none; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <i class="fas <?php echo $info['icon']; ?>"></i>
                <?php echo $info['title']; ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 25px 30px; border-radius: 20px; color: #fff; margin-bottom: 35px; box-shadow: 0 10px 25px rgba(30, 58, 138, 0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0 0 5px 0; font-size: 1.6rem;"><i class="fas <?php echo $managed_pages[$current_page_key]['icon']; ?> me-2"></i> <?php echo $managed_pages[$current_page_key]['title']; ?></h2>
                    <p style="margin: 0; opacity: 0.9; font-size: 1.05rem;"><?php echo $managed_pages[$current_page_key]['description']; ?></p>
                </div>
                <a href="../<?php echo $managed_pages[$current_page_key]['file']; ?>" target="_blank" style="padding: 10px 20px; background: rgba(255,255,255,0.2); color: #fff; border-radius: 10px; text-decoration: none; font-weight: 600; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-external-link-alt me-2"></i> Live Preview
                </a>
            </div>
        </div>

        <!-- Hero & CTA Section -->
        <div class="content-card" style="background: #fff; border-radius: 20px; padding: 30px; margin-bottom: 35px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 25px 0; color: #1e3a8a; display: flex; align-items: center; gap: 12px; font-weight: 800;">
                <span style="width: 45px; height: 45px; background: #e0f2fe; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #0284c7;">
                    <i class="fas fa-paint-brush"></i>
                </span>
                Hero & CTA Appearance
            </h3>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_page_content">
                <input type="hidden" name="page_key" value="<?php echo $current_page_key; ?>">
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Hero Badge</label>
                        <input type="text" name="hero_badge" value="<?php echo htmlspecialchars($page_content['hero_badge'] ?? ''); ?>" class="form-control form-control-lg">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Hero Title (Normal)</label>
                        <input type="text" name="hero_title" value="<?php echo htmlspecialchars($page_content['hero_title'] ?? ''); ?>" class="form-control form-control-lg">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Hero Subtitle (Gradient)</label>
                        <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($page_content['hero_subtitle'] ?? ''); ?>" class="form-control form-control-lg">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Hero Description</label>
                        <textarea name="hero_description" rows="3" class="form-control form-control-lg"><?php echo htmlspecialchars($page_content['hero_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hero Background Image URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                            <input type="text" name="hero_image" value="<?php echo htmlspecialchars($page_content['hero_image'] ?? ''); ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Upload New Background</label>
                        <input type="file" name="hero_image_file" class="form-control">
                    </div>
                </div>

                <div style="margin: 35px 0; height: 2px; background: #f1f5f9;"></div>

                <h4 style="margin: 0 0 20px 0; color: #64748b; font-weight: 700;">Bottom Call-to-Action</h4>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CTA Title (Main)</label>
                        <input type="text" name="cta_title" value="<?php echo htmlspecialchars($page_content['cta_title'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CTA Title (Yellow)</label>
                        <input type="text" name="cta_subtitle" value="<?php echo htmlspecialchars($page_content['cta_subtitle'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Button Text</label>
                        <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars($page_content['cta_button_text'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Button Link</label>
                        <input type="text" name="cta_button_link" value="<?php echo htmlspecialchars($page_content['cta_button_link'] ?? ''); ?>" class="form-control">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Sections Editor -->
        <?php foreach ($page_sections as $section): ?>
        <div class="content-card" style="background: #fff; border-radius: 20px; padding: 30px; margin-bottom: 35px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #1e3a8a; display: flex; align-items: center; gap: 12px; font-weight: 800;">
                        <span style="width: 45px; height: 45px; background: #fff1f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #e11d48;">
                            <i class="fas fa-layer-group"></i>
                        </span>
                        Section: <?php echo htmlspecialchars($section['section_title'] ?: ucwords(str_replace('_', ' ', $section['section_key']))); ?>
                    </h3>
                    <p style="margin: 0; color: #64748b; font-weight: 500;"><?php echo htmlspecialchars($section['section_subtitle'] ?? ''); ?></p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="editSection(<?php echo $section['id']; ?>, '<?php echo addslashes($section['section_title']); ?>', '<?php echo addslashes($section['section_subtitle'] ?? ''); ?>', '<?php echo addslashes($section['section_description'] ?? ''); ?>', '<?php echo addslashes($section['section_image'] ?? ''); ?>', '<?php echo addslashes($section['section_image_2'] ?? ''); ?>')" class="btn btn-outline-secondary">
                        <i class="fas fa-edit me-1"></i> Header
                    </button>
                    <button onclick="addItem('<?php echo $current_page_key; ?>', '<?php echo $section['section_key']; ?>')" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Add Entry
                    </button>
                </div>
            </div>
            
            <div class="row g-4">
                <?php if (!empty($page_items[$section['section_key']])): ?>
                    <?php foreach ($page_items[$section['section_key']] as $item): ?>
                    <div class="col-xl-6">
                        <div style="background: <?php echo $item['is_active'] ? '#f8fafc' : '#f1f5f9'; ?>; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; height: 100%; position: relative; transition: all 0.3s ease;">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_item">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="page_key" value="<?php echo htmlspecialchars($current_page_key); ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-9">
                                        <label class="form-label small fw-bold">Title</label>
                                        <input type="text" name="item_title" value="<?php echo htmlspecialchars($item['item_title']); ?>" class="form-control fw-bold border-2">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Status</label>
                                        <div class="form-check form-switch pt-1">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?>>
                                            <span class="ms-2 small"><?php echo $item['is_active'] ? 'Active' : 'Hidden'; ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">Description / Supporting Text</label>
                                        <textarea name="item_description" rows="2" class="form-control"><?php echo htmlspecialchars($item['item_description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Subtitle / Small Label</label>
                                        <input type="text" name="item_subtitle" value="<?php echo htmlspecialchars($item['item_subtitle'] ?? ''); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Badge / Special Value (e.g. PDF)</label>
                                        <input type="text" name="item_stat_value" value="<?php echo htmlspecialchars($item['item_stat_value'] ?? ''); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Icon (Material Sym)</label>
                                        <input type="text" name="item_icon" value="<?php echo htmlspecialchars($item['item_icon'] ?? ''); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Color Class</label>
                                        <input type="text" name="item_color" value="<?php echo htmlspecialchars($item['item_color'] ?? 'blue-600'); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Link URL / File Path</label>
                                        <input type="text" name="item_link" value="<?php echo htmlspecialchars($item['item_link'] ?? ''); ?>" class="form-control">
                                        <small class="text-muted d-block mt-1">A page on this site (<code>admissions.php</code>), a full address (<code>https://&hellip;</code>), or leave blank and upload a file instead. A link opens the page; a file downloads.</small>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold">
                                            Upload Document <span class="text-muted fw-normal">(PDF, Word or Excel &mdash; replaces the path above)</span>
                                        </label>
                                        <input type="file" name="item_file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="form-control">
                                        <?php if (!empty($item['item_link']) && preg_match('~\.(pdf|docx?|xlsx?)$~i', $item['item_link'])): ?>
                                            <small class="text-muted d-block mt-1">
                                                Current file:
                                                <a href="../<?php echo implode('/', array_map('rawurlencode', explode('/', $item['item_link']))); ?>" target="_blank" rel="noopener">
                                                    <?php echo htmlspecialchars(basename($item['item_link'])); ?>
                                                </a>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-primary">Advanced Data (JSON Format)</label>
                                        <textarea name="item_extra_data" rows="2" class="form-control" style="font-family: monospace; font-size: 0.85rem;" placeholder='{"key": "value"}'><?php echo htmlspecialchars($item['extra_data'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Image URL</label>
                                        <input type="text" name="item_image" value="<?php echo htmlspecialchars($item['item_image'] ?? ''); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Upload Image</label>
                                        <input type="file" name="item_image_file" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-top d-flex justify-content-between">
                                    <button type="button" onclick="deleteItem(<?php echo $item['id']; ?>)" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm px-4">
                                        <i class="fas fa-save me-1"></i> Save Entry
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                        <p class="text-muted">No entries found in this section.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</main>

<!-- Modals -->
<div id="addItemModal" class="admin-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 650px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div style="padding: 25px 30px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e293b;"><i class="fas fa-plus-circle me-2 text-primary"></i> Add New Entry</h3>
            <button onclick="document.getElementById('addItemModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" style="padding: 30px;">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="page_key" id="addItemPageKey">
            <input type="hidden" name="section_key" id="addItemSectionKey">
            
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="item_title" required class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="item_description" rows="3" class="form-control"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Subtitle</label>
                    <input type="text" name="item_subtitle" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Icon (e.g. school)</label>
                    <input type="text" name="item_icon" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Link / File Path</label>
                    <input type="text" name="item_link" class="form-control">
                    <small class="text-muted d-block mt-1">A page on this site (<code>admissions.php</code>), a full address (<code>https://&hellip;</code>), or leave blank and upload a file instead. A link opens the page; a file downloads.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        Upload Document <span class="text-muted fw-normal small">(PDF, Word or Excel)</span>
                    </label>
                    <input type="file" name="item_file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="form-control">
                    <small class="text-muted d-block mt-1">Choosing a file here fills in the path above for you.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Color Class</label>
                    <input type="text" name="item_color" value="blue-600" class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold text-primary">Advanced Data (JSON Format)</label>
                    <textarea name="item_extra_data" rows="2" class="form-control" style="font-family: monospace;" placeholder='{"key": "value"}'></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Item Image</label>
                    <input type="file" name="item_image_file" class="form-control">
                </div>
            </div>
            
            <div class="mt-4 pt-3 d-flex justify-content-end gap-2">
                <button type="button" onclick="document.getElementById('addItemModal').style.display='none'" class="btn btn-light px-4">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Create Entry</button>
            </div>
        </form>
    </div>
</div>

<div id="editSectionModal" class="admin-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div style="padding: 25px 30px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e293b;"><i class="fas fa-edit me-2 text-primary"></i> Edit Section Info</h3>
            <button onclick="document.getElementById('editSectionModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" style="padding: 30px;">
            <input type="hidden" name="action" value="update_section">
            <input type="hidden" name="section_id" id="editSectionId">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Section Title</label>
                <input type="text" name="section_title" id="editSectionTitle" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Subtitle</label>
                <input type="text" name="section_subtitle" id="editSectionSubtitle" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Description</label>
                <textarea name="section_description" id="editSectionDescription" rows="3" class="form-control"></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6 border-end">
                    <h5 class="small fw-bold text-primary mb-3">Primary Image</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Image URL</label>
                        <input type="text" name="section_image" id="editSectionImage" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Upload New</label>
                        <input type="file" name="section_image_file" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="small fw-bold text-success mb-3">Secondary Image (Optional)</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Image URL</label>
                        <input type="text" name="section_image_2" id="editSectionImage2" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Upload New</label>
                        <input type="file" name="section_image_file_2" class="form-control">
                    </div>
                </div>
            </div>
            
            <div class="mt-4 pt-3 d-flex justify-content-end gap-2">
                <button type="button" onclick="document.getElementById('editSectionModal').style.display='none'" class="btn btn-light px-4">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteItemForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete_item">
    <input type="hidden" name="item_id" id="deleteItemId">
</form>

<script>
function deleteItem(id) {
    if (confirm('Permanently remove this entry? This cannot be undone.')) {
        document.getElementById('deleteItemId').value = id;
        document.getElementById('deleteItemForm').submit();
    }
}

function addItem(pageKey, sectionKey) {
    document.getElementById('addItemPageKey').value = pageKey;
    document.getElementById('addItemSectionKey').value = sectionKey;
    document.getElementById('addItemModal').style.display = 'flex';
}

function editSection(id, title, subtitle, description, image, image2) {
    document.getElementById('editSectionId').value = id;
    document.getElementById('editSectionTitle').value = title;
    document.getElementById('editSectionSubtitle').value = subtitle;
    document.getElementById('editSectionDescription').value = description;
    document.getElementById('editSectionImage').value = image;
    document.getElementById('editSectionImage2').value = image2;
    document.getElementById('editSectionModal').style.display = 'flex';
}

window.onclick = function(event) {
    if (event.target.classList.contains('admin-modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<style>
.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}
.btn-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    transform: translateY(-1px);
}
.main-content {
    margin-left: 280px;
    padding: 30px;
}
@media (max-width: 992px) {
    .main-content {
        margin-left: 0;
    }
}
</style>

<?php include 'footer.php'; ?>
