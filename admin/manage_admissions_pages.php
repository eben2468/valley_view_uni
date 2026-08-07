<?php
/**
 * Admin - Manage Admissions Info Pages
 * Manages content for: provisional_admission_list.php, entry-requirement.php, 
 * caution-to-applicants.php, scholarships.php, scholarships-forms.php
 */

session_start();
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Admissions Pages";
$current_page = 'manage_admissions_pages.php';

// Define the pages we manage
$managed_pages = [
    'admissions' => [
        'title' => 'Admissions (Main Page)',
        'icon' => 'fa-graduation-cap',
        'file' => 'admissions.php',
        'description' => 'Manage the main admissions page: Featured Programs, Why Choose VVU, Requirements, Process, Contact and more.'
    ],
    'provisional_admission_list' => [
        'title' => 'Provisional Admission List',
        'icon' => 'fa-list-check',
        'file' => 'provisional_admission_list.php',
        'description' => 'Manage the admission list page including hero, PDF lists, and guidance sections.'
    ],
    'entry_requirements' => [
        'title' => 'Entry Requirements',
        'icon' => 'fa-clipboard-list',
        'file' => 'entry-requirement.php',
        'description' => 'Manage postgraduate, undergraduate, and special entry requirements.'
    ],
    'caution_to_applicants' => [
        'title' => 'Caution to Applicants',
        'icon' => 'fa-triangle-exclamation',
        'file' => 'caution-to-applicants.php',
        'description' => 'Manage official warnings, red flags, and verification channels.'
    ],
    'scholarships' => [
        'title' => 'Scholarships & Aid',
        'icon' => 'fa-hand-holding-dollar',
        'file' => 'scholarships.php',
        'description' => 'Manage scholarship categories, process steps, and success stories.'
    ],
    'scholarships_forms' => [
        'title' => 'Scholarship Forms',
        'icon' => 'fa-file-invoice-dollar',
        'file' => 'scholarships-forms.php',
        'description' => 'Manage downloadable scholarship forms and application tips.'
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
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'admissions');
            if ($uploaded) $hero_image = $uploaded;

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
                    cta_button_link = ?,
                    cta_button_text_2 = ?,
                    cta_button_link_2 = ?,
                    help_title = ?,
                    help_description = ?,
                    help_phone = ?,
                    empty_list_message = ?
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
                $_POST['cta_button_text_2'] ?? '',
                $_POST['cta_button_link_2'] ?? '',
                $_POST['help_title'] ?? '',
                $_POST['help_description'] ?? '',
                $_POST['help_phone'] ?? '',
                $_POST['empty_list_message'] ?? '',
                $page_key
            ]);
            
            $message = "Page content updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'update_item') {
            // Update an item
            $item_image = $_POST['item_image'] ?? '';
            $uploaded = handleAdminFileUpload($_FILES['item_image_file'] ?? null, 'admissions');
            if ($uploaded) $item_image = $uploaded;

            $item_link = $_POST['item_link'] ?? '';
            $uploaded_link = handleAdminFileUpload($_FILES['item_link_file'] ?? null, 'admissions');
            if ($uploaded_link) $item_link = $uploaded_link;

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
                    display_order = ?,
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
                (int) ($_POST['display_order'] ?? 0),
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['item_id']
            ]);
            $message = "Item updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'add_item') {
            $item_image = $_POST['item_image'] ?? '';
            $uploaded = handleAdminFileUpload($_FILES['item_image_file'] ?? null, 'admissions');
            if ($uploaded) $item_image = $uploaded;

            $item_link = $_POST['item_link'] ?? '';
            $uploaded_link = handleAdminFileUpload($_FILES['item_link_file'] ?? null, 'admissions');
            if ($uploaded_link) $item_link = $uploaded_link;

            $stmt = $pdo->prepare("
                INSERT INTO academic_pages_items (page_key, section_key, item_title, item_subtitle, item_description, item_icon, item_color, item_image, item_link, item_stat_value, display_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM academic_pages_items i2 WHERE i2.page_key = ? AND i2.section_key = ?))
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
            $uploaded = handleAdminFileUpload($_FILES['section_image_file'] ?? null, 'admissions');
            if ($uploaded) $section_image = $uploaded;

            $stmt = $pdo->prepare("UPDATE academic_pages_sections SET section_title = ?, section_subtitle = ?, section_description = ?, section_image = ? WHERE id = ?");
            $stmt->execute([$_POST['section_title'], $_POST['section_subtitle'] ?? '', $_POST['section_description'] ?? '', $section_image, $_POST['section_id']]);
            $message = "Section updated successfully!";
            $message_type = "success";
        }
        
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Get current page selection
$current_page_key = $_GET['page'] ?? 'provisional_admission_list';
if (!isset($managed_pages[$current_page_key])) {
    $current_page_key = 'provisional_admission_list';
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
            <h1><i class="fas fa-user-shield"></i> Manage Admissions Info Pages</h1>
            <p class="page-description">Update content for admission lists, requirements, cautions, and scholarships.</p>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 25px;">
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
                        <label class="form-label fw-bold">Hero Title</label>
                        <input type="text" name="hero_title" value="<?php echo htmlspecialchars($page_content['hero_title'] ?? ''); ?>" class="form-control form-control-lg">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Hero Subtitle colored</label>
                        <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($page_content['hero_subtitle'] ?? ''); ?>" class="form-control form-control-lg">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Hero Description</label>
                        <textarea name="hero_description" rows="3" class="form-control form-control-lg"><?php echo htmlspecialchars($page_content['hero_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hero Background Image</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                            <input type="text" name="hero_image" value="<?php echo htmlspecialchars($page_content['hero_image'] ?? ''); ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Update Background Image</label>
                        <input type="file" name="hero_image_file" class="form-control">
                    </div>
                </div>

                <div style="margin: 35px 0; height: 2px; background: #f1f5f9;"></div>

                <h4 style="margin: 0 0 20px 0; color: #64748b; font-weight: 700;">Bottom Call-to-Action</h4>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CTA Main Title</label>
                        <input type="text" name="cta_title" value="<?php echo htmlspecialchars($page_content['cta_title'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CTA Description</label>
                        <input type="text" name="cta_subtitle" value="<?php echo htmlspecialchars($page_content['cta_subtitle'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Button 1 Text</label>
                        <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars($page_content['cta_button_text'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Button 1 Link</label>
                        <input type="text" name="cta_button_link" value="<?php echo htmlspecialchars($page_content['cta_button_link'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Button 2 Text</label>
                        <input type="text" name="cta_button_text_2" value="<?php echo htmlspecialchars($page_content['cta_button_text_2'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Button 2 Link</label>
                        <input type="text" name="cta_button_link_2" value="<?php echo htmlspecialchars($page_content['cta_button_link_2'] ?? ''); ?>" class="form-control">
                    </div>
                </div>

                <div style="margin: 35px 0; height: 2px; background: #f1f5f9;"></div>

                <h4 style="margin: 0 0 20px 0; color: #64748b; font-weight: 700;">Additional Page Settings</h4>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Help Card Title</label>
                        <input type="text" name="help_title" value="<?php echo htmlspecialchars($page_content['help_title'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Help Description</label>
                        <input type="text" name="help_description" value="<?php echo htmlspecialchars($page_content['help_description'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Help Phone</label>
                        <input type="text" name="help_phone" value="<?php echo htmlspecialchars($page_content['help_phone'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Empty List Message</label>
                        <input type="text" name="empty_list_message" value="<?php echo htmlspecialchars($page_content['empty_list_message'] ?? ''); ?>" class="form-control" placeholder="Message shown when no items are found">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                        <i class="fas fa-save me-2"></i> Save Page Branding
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
                    <?php if (!empty($section['section_description'])): ?>
                    <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 0.9em; max-width: 600px;"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars(strip_tags($section['section_description'])); ?></p>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="editSection(this)" 
                            data-id="<?php echo $section['id']; ?>"
                            data-title="<?php echo htmlspecialchars($section['section_title']); ?>"
                            data-subtitle="<?php echo htmlspecialchars($section['section_subtitle'] ?? ''); ?>"
                            data-description="<?php echo htmlspecialchars($section['section_description'] ?? ''); ?>"
                            data-image="<?php echo htmlspecialchars($section['section_image'] ?? ''); ?>"
                            class="btn btn-outline-secondary">
                        <i class="fas fa-edit me-1"></i> Edit Header
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
                                
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label small fw-bold">Title</label>
                                        <input type="text" name="item_title" value="<?php echo htmlspecialchars($item['item_title']); ?>" class="form-control fw-bold border-2">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold" title="Lower numbers appear first">Order</label>
                                        <input type="number" name="display_order" value="<?php echo (int) ($item['display_order'] ?? 0); ?>" class="form-control" min="0">
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
                                        <label class="form-label small fw-bold">Badge / Tag</label>
                                        <input type="text" name="item_stat_value" value="<?php echo htmlspecialchars($item['item_stat_value'] ?? ''); ?>" class="form-control" placeholder="e.g. Business, Health, PDF, Batch 1">
                                        <div class="form-text small">Shown as the coloured pill on the card image (Featured Programs).</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Icon</label>
                                        <input type="text" name="item_icon" value="<?php echo htmlspecialchars($item['item_icon'] ?? ''); ?>" class="form-control" placeholder="description">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Color Class</label>
                                        <input type="text" name="item_color" value="<?php echo htmlspecialchars($item['item_color'] ?? 'blue-600'); ?>" class="form-control" placeholder="blue-600">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Image URL or Upload Image</label>
                                        <div class="input-group input-group-sm mb-0">
                                            <input type="text" name="item_image" value="<?php echo htmlspecialchars($item['item_image'] ?? ''); ?>" class="form-control" placeholder="Image URL">
                                            <input type="file" name="item_image_file" class="form-control" style="max-width:130px;" title="Upload Image">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Link URL or Upload File</label>
                                        <div class="input-group input-group-sm mb-0">
                                            <input type="text" name="item_link" value="<?php echo htmlspecialchars($item['item_link'] ?? ''); ?>" class="form-control" placeholder="URL">
                                            <input type="file" name="item_link_file" class="form-control" style="max-width:130px;" title="Upload File">
                                        </div>
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
                        <p class="text-muted">No entries found in this section. Get started by adding one!</p>
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
                <div class="col-md-3">
                    <label class="form-label fw-bold">Icon</label>
                    <input type="text" name="item_icon" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Color</label>
                    <input type="text" name="item_color" value="blue-600" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Image URL OR Upload Image</label>
                    <div class="input-group">
                        <input type="text" name="item_image" class="form-control" placeholder="Image URL">
                        <input type="file" name="item_image_file" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Link URL OR Upload File</label>
                    <div class="input-group">
                        <input type="text" name="item_link" class="form-control" placeholder="https://...">
                        <input type="file" name="item_link_file" class="form-control">
                    </div>
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
                <textarea name="section_description" id="editSectionDescription" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Image</label>
                <div class="input-group">
                    <input type="text" name="section_image" id="editSectionImage" class="form-control" placeholder="URL">
                    <input type="file" name="section_image_file" class="form-control">
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

function editSection(btn) {
    document.getElementById('editSectionId').value = btn.dataset.id;
    document.getElementById('editSectionTitle').value = btn.dataset.title;
    document.getElementById('editSectionSubtitle').value = btn.dataset.subtitle;
    document.getElementById('editSectionDescription').value = btn.dataset.description;
    document.getElementById('editSectionImage').value = btn.dataset.image || '';
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
</style>

<?php include 'footer.php'; ?>
