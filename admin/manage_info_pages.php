<?php
/**
 * Admin - Manage Student Info Pages
 * Manages content for: freshmen_info.php, new_to_vvu.php, take_a_tour.php, download-forms.php
 */

require_once __DIR__ . '/../includes/admin_auth.php';
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Student Info Pages";
$current_page = 'manage_info_pages.php';

// Define the pages we manage
$managed_pages = [
    'freshmen_info' => [
        'title' => 'Freshmen Information',
        'icon' => 'fa-user-graduate',
        'file' => 'freshmen_info.php',
        'description' => 'Manage requirements, checklists, and support for new students.'
    ],
    'new_to_vvu' => [
        'title' => 'New to VVU',
        'icon' => 'fa-door-open',
        'file' => 'new_to_vvu.php',
        'description' => 'Manage content for prospective students exploring VVU.'
    ],
    'take_a_tour' => [
        'title' => 'Take a Tour',
        'icon' => 'fa-camera-retro',
        'file' => 'take_a_tour.php',
        'description' => 'Manage campus highlights and virtual tour content.'
    ],
    'download_forms' => [
        'title' => 'Download Forms',
        'icon' => 'fa-download',
        'file' => 'download-forms.php',
        'description' => 'Manage all downloadable application and medical forms.'
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
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'info');
            if ($uploaded) $hero_image = $uploaded;

            $stmt = $pdo->prepare("
                UPDATE academic_pages_content SET
                    hero_badge = ?,
                    hero_title = ?,
                    hero_subtitle = ?,
                    hero_description = ?,
                    hero_image = ?,
                    hero_video = ?,
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
                $_POST['hero_video'] ?? '',
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
            $uploaded = isset($_FILES['item_image_file']) ? handleAdminFileUpload($_FILES['item_image_file'], 'info') : null;
            if ($uploaded) $item_image = $uploaded;

            // Downloadable document. Separate from the image above: a PDF put
            // through the image box uploads fine but lands in item_image, so
            // the public page has no link to offer and the card stays dead.
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
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['item_id']
            ]);
            $message = "Entry updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'add_item') {
            $item_image = $_POST['item_image'] ?? '';
            $uploaded = isset($_FILES['item_image_file']) ? handleAdminFileUpload($_FILES['item_image_file'], 'info') : null;
            if ($uploaded) $item_image = $uploaded;

            $item_link = vvu_resource_item_link($_POST['page_key'] ?? '');

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
            $message = "Entry added successfully!";
            $message_type = "success";
            
        } elseif ($action === 'delete_item') {
            $stmt = $pdo->prepare("DELETE FROM academic_pages_items WHERE id = ?");
            $stmt->execute([$_POST['item_id']]);
            $message = "Entry deleted successfully!";
            $message_type = "success";
            
        } elseif ($action === 'update_section') {
            $section_image = $_POST['section_image'] ?? '';
            $uploaded = isset($_FILES['section_image_file']) ? handleAdminFileUpload($_FILES['section_image_file'], 'info') : null;
            if ($uploaded) $section_image = $uploaded;

            $stmt = $pdo->prepare("UPDATE academic_pages_sections SET section_title = ?, section_subtitle = ?, section_description = ?, section_image = ? WHERE id = ?");
            $stmt->execute([
                $_POST['section_title'], 
                $_POST['section_subtitle'] ?? '', 
                $_POST['section_description'] ?? '', 
                $section_image,
                $_POST['section_id']
            ]);
            $message = "Section updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'update_stat') {
            $stmt = $pdo->prepare("UPDATE academic_pages_stats SET stat_value = ?, stat_label = ?, stat_icon = ? WHERE id = ?");
            $stmt->execute([$_POST['stat_value'], $_POST['stat_label'], $_POST['stat_icon'] ?? '', $_POST['stat_id']]);
            $message = "Stat updated successfully!";
            $message_type = "success";
        }
        
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Get current page selection
$current_page_key = $_GET['page'] ?? 'freshmen_info';
if (!isset($managed_pages[$current_page_key])) {
    $current_page_key = 'freshmen_info';
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

    // Stats
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = ? ORDER BY display_order");
    $stmt->execute([$current_page_key]);
    $page_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
            <div class="page-header-content">
                <h1><i class="fas fa-info-circle"></i> Student Info Pages CMS</h1>
                <p class="page-description">Manage content for Freshmen Info, Prospectus, Campus Tours and Downloads.</p>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 25px; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 10px; background: <?php echo $message_type === 'success' ? '#ecfdf5' : '#fef2f2'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#10b981' : '#ef4444'; ?>; color: <?php echo $message_type === 'success' ? '#065f46' : '#991b1b'; ?>;">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <div><strong><?php echo $message_type === 'success' ? 'Success!' : 'Error:'; ?></strong> <?php echo htmlspecialchars($message); ?></div>
        </div>
        <?php endif; ?>

        <!-- Modern Page Tabs -->
        <div class="page-tabs-container" style="background: #fff; padding: 10px; border-radius: 16px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; gap: 10px; overflow-x: auto;">
            <?php foreach ($managed_pages as $key => $info): ?>
            <a href="?page=<?php echo $key; ?>" 
               class="modern-tab <?php echo $current_page_key === $key ? 'active' : ''; ?>"
               style="display: flex; align-items: center; gap: 12px; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; white-space: nowrap;
               background: <?php echo $current_page_key === $key ? 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)' : 'transparent'; ?>;
               color: <?php echo $current_page_key === $key ? '#fff' : '#64748b'; ?>;
               box-shadow: <?php echo $current_page_key === $key ? '0 8px 20px rgba(79, 70, 229, 0.3)' : 'none'; ?>;">
                <i class="fas <?php echo $info['icon']; ?>"></i>
                <?php echo $info['title']; ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Page Preview Header -->
        <div style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 30px; border-radius: 20px; color: #fff; margin-bottom: 35px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
            <div style="position: absolute; right: -20px; top: -20px; font-size: 15rem; opacity: 0.05; transform: rotate(-15deg);">
                <i class="fas <?php echo $managed_pages[$current_page_key]['icon']; ?>"></i>
            </div>
            <div style="position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <span style="background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(255,255,255,0.2);">Active Page Context</span>
                    <h2 style="margin: 10px 0 5px 0; font-size: 2rem; font-weight: 900; letter-spacing: -0.5px;"><?php echo $managed_pages[$current_page_key]['title']; ?></h2>
                    <p style="margin: 0; opacity: 0.8; max-width: 500px;"><?php echo $managed_pages[$current_page_key]['description']; ?></p>
                </div>
                <a href="../<?php echo $managed_pages[$current_page_key]['file']; ?>" target="_blank" style="padding: 12px 25px; background: #fff; color: #1e293b; border-radius: 12px; text-decoration: none; font-weight: 800; display: flex; align-items: center; gap: 10px; transition: transform 0.2s;">
                    <i class="fas fa-external-link-alt"></i> Live View
                </a>
            </div>
        </div>

        <!-- Hero Content Section -->
        <div class="content-card" style="background: #fff; border-radius: 20px; padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 25px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background: #e0e7ff; color: #4338ca; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-pager"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #1e293b;">Hero & Global Content</h3>
                    <p style="margin: 0; color: #64748b;">Configure the top section and main branding of the page.</p>
                </div>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_page_content">
                <input type="hidden" name="page_key" value="<?php echo $current_page_key; ?>">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #334155;">Hero Badge Text</label>
                        <input type="text" name="hero_badge" value="<?php echo htmlspecialchars(strip_tags($page_content['hero_badge'] ?? '')); ?>" style="width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s;" placeholder="e.g. Welcome Freshmen">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #334155;">Hero Title</label>
                        <input type="text" name="hero_title" value="<?php echo htmlspecialchars(strip_tags($page_content['hero_title'] ?? '')); ?>" style="width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem;" placeholder="Main Heading">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #334155;">Hero Subtitle (Colored)</label>
                        <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars(strip_tags($page_content['hero_subtitle'] ?? '')); ?>" style="width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem;" placeholder="Secondary Heading">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #334155;">Hero Description / Quote</label>
                    <textarea name="hero_description" rows="3" style="width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; resize: vertical;"><?php echo htmlspecialchars(strip_tags($page_content['hero_description'] ?? '')); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 25px; background: #f8fafc; padding: 25px; border-radius: 16px; border: 1px dashed #cbd5e1;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #334155;">Hero Image URL</label>
                        <input type="text" name="hero_image" value="<?php echo htmlspecialchars(strip_tags($page_content['hero_image'] ?? '')); ?>" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #334155;">Hero Video URL (Optional)</label>
                        <input type="text" name="hero_video" value="<?php echo htmlspecialchars(strip_tags($page_content['hero_video'] ?? '')); ?>" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px;" placeholder="e.g. uploads/tour.mp4">
                    </div>
                </div>

                <div style="margin-top: 25px; background: #f8fafc; padding: 25px; border-radius: 16px; border: 1px dashed #cbd5e1;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 10px; font-weight: 700; color: #334155;">Upload New Asset (Image)</label>
                        <input type="file" name="hero_image_file" style="width: 100%; padding: 10px; background: #fff; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                </div>

                <div style="margin: 40px 0; height: 1px; background: #f1f5f9;"></div>

                <div style="background: #eff6ff; padding: 25px; border-radius: 16px; border: 1px solid #dbeafe;">
                    <h4 style="margin: 0 0 20px 0; color: #1e40af; font-size: 1.1rem; font-weight: 800;">Bottom Call to Action (CTA)</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #1e40af; font-size: 0.9rem;">CTA Title</label>
                            <input type="text" name="cta_title" value="<?php echo htmlspecialchars(strip_tags($page_content['cta_title'] ?? '')); ?>" style="width: 100%; padding: 12px; border: 1px solid #bfdbfe; border-radius: 10px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #1e40af; font-size: 0.9rem;">CTA Description</label>
                            <input type="text" name="cta_subtitle" value="<?php echo htmlspecialchars(strip_tags($page_content['cta_subtitle'] ?? '')); ?>" style="width: 100%; padding: 12px; border: 1px solid #bfdbfe; border-radius: 10px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #1e40af; font-size: 0.9rem;">Button Text</label>
                            <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars(strip_tags($page_content['cta_button_text'] ?? '')); ?>" style="width: 100%; padding: 12px; border: 1px solid #bfdbfe; border-radius: 10px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #1e40af; font-size: 0.9rem;">Button Link</label>
                            <input type="text" name="cta_button_link" value="<?php echo htmlspecialchars(strip_tags($page_content['cta_button_link'] ?? '')); ?>" style="width: 100%; padding: 12px; border: 1px solid #bfdbfe; border-radius: 10px;">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px; text-align: right;">
                    <button type="submit" style="padding: 16px 40px; background: #4f46e5; color: #fff; border: none; border-radius: 14px; font-size: 1.1rem; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);">
                        <i class="fas fa-save"></i> Save Page Branding
                    </button>
                </div>
            </form>
        </div>

        <!-- Sections & Entries Manager -->
        <div style="margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between;">
            <h2 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">Page Sections & Content</h2>
            <div style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Total Sections: <?php echo count($page_sections); ?></div>
        </div>

        <?php if (empty($page_sections)): ?>
            <div style="background: #f8fafc; border: 2px dashed #e2e8f0; padding: 60px; border-radius: 20px; text-align: center;">
                <i class="fas fa-folder-open" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
                <h4 style="color: #64748b; font-size: 1.2rem; margin-bottom: 10px;">No Sections Configured</h4>
                <p style="color: #94a3b8; max-width: 400px; margin: 0 auto;">Use the migration tools or database to define sections for this page key.</p>
            </div>
        <?php else: ?>
            <?php foreach ($page_sections as $section): ?>
            <div class="section-card" style="background: #fff; border-radius: 20px; margin-bottom: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden;">
                <!-- Section Header -->
                <div style="background: #fafafa; padding: 25px 35px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 5px;">
                            <span style="background: #1e293b; color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 900; text-transform: uppercase;"><?php echo $section['section_key']; ?></span>
                            <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: #1e293b;"><?php echo htmlspecialchars(strip_tags($section['section_title'])); ?></h3>
                        </div>
                        <p style="margin: 0; color: #64748b; font-size: 0.95rem; font-style: italic;"><?php echo htmlspecialchars(strip_tags($section['section_subtitle'] ?? 'No subtitle defined')); ?></p>
                        <?php if ($section['section_image']): ?>
                        <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                            <img src="../<?php echo htmlspecialchars(strip_tags($section['section_image'])); ?>" style="height: 40px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <span style="font-size: 0.75rem; color: #94a3b8; font-family: monospace;"><?php echo htmlspecialchars(strip_tags($section['section_image'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button onclick="editSection(<?php echo $section['id']; ?>, '<?php echo addslashes($section['section_title']); ?>', '<?php echo addslashes($section['section_subtitle'] ?? ''); ?>', '<?php echo addslashes($section['section_image'] ?? ''); ?>')" style="padding: 10px 18px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; color: #475569; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-edit"></i> Edit Header
                        </button>
                        <button onclick="addItem('<?php echo $current_page_key; ?>', '<?php echo $section['section_key']; ?>')" style="padding: 10px 18px; border-radius: 10px; background: #10b981; border: none; color: #fff; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                            <i class="fas fa-plus"></i> Add Entry
                        </button>
                    </div>
                </div>

                <!-- Section Items List -->
                <div style="padding: 30px;">
                    <?php if (empty($page_items[$section['section_key']])): ?>
                        <div style="text-align: center; padding: 40px; color: #94a3b8; font-weight: 500;">
                            <i class="fas fa-ghost" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                            This section is currently empty.
                        </div>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px;">
                            <?php foreach ($page_items[$section['section_key']] as $item): ?>
                            <div class="item-card" style="background: <?php echo $item['is_active'] ? '#fff' : '#f8fafc'; ?>; border: 1px solid <?php echo $item['is_active'] ? '#e2e8f0' : '#cbd5e1'; ?>; padding: 25px; border-radius: 16px; position: relative; opacity: <?php echo $item['is_active'] ? '1' : '0.8'; ?>;">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_item">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="page_key" value="<?php echo htmlspecialchars($current_page_key); ?>">
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                        <div style="display: flex; align-items: center; gap: 10px; flex-grow: 1;">
                                            <input type="text" name="item_title" value="<?php echo htmlspecialchars(strip_tags($item['item_title'])); ?>" style="font-weight: 800; font-size: 1.1rem; color: #1e293b; border: none; border-bottom: 2px solid #f1f5f9; padding: 5px; width: 80%; background: transparent;" placeholder="Title">
                                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 800; color: #64748b; cursor: pointer;">
                                                <input type="checkbox" name="is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?> style="width: 16px; height: 16px;">
                                                Active
                                            </label>
                                        </div>
                                        <button type="button" onclick="deleteItem(<?php echo $item['id']; ?>)" style="background: #fee2e2; color: #ef4444; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
                                            <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </div>

                                    <div style="margin-bottom: 15px;">
                                        <textarea name="item_description" rows="3" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 0.9rem; color: #475569; background: <?php echo $item['is_active'] ? '#fff' : '#f1f5f9'; ?>;" placeholder="Description / Body Content"><?php echo htmlspecialchars(strip_tags($item['item_description'])); ?></textarea>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Subtitle / Tag</label>
                                            <input type="text" name="item_subtitle" value="<?php echo htmlspecialchars(strip_tags($item['item_subtitle'] ?? '')); ?>" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.85rem;">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Stat / Badge Value</label>
                                            <input type="text" name="item_stat_value" value="<?php echo htmlspecialchars(strip_tags($item['item_stat_value'] ?? '')); ?>" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.85rem;" placeholder="e.g. 100%">
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Icon / Color</label>
                                            <div style="display: flex; gap: 8px;">
                                                <input type="text" name="item_icon" value="<?php echo htmlspecialchars(strip_tags($item['item_icon'] ?? '')); ?>" style="width: 60%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.85rem;" placeholder="icon name">
                                                <input type="text" name="item_color" value="<?php echo htmlspecialchars(strip_tags($item['item_color'] ?? 'blue-600')); ?>" style="width: 40%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.85rem;" placeholder="hex/class">
                                            </div>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Link / Action URL</label>
                                            <input type="text" name="item_link" value="<?php echo htmlspecialchars(strip_tags($item['item_link'] ?? '')); ?>" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.85rem;" placeholder="https://...">
                                        </div>
                                    </div>

                                    <div style="margin-top: 15px; background: #fafafa; padding: 12px; border-radius: 12px; border: 1px solid #f1f5f9;">
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">
                                            <i class="fas fa-image"></i> Asset Management
                                        </label>
                                        <div style="display: flex; gap: 10px;">
                                            <input type="text" name="item_image" value="<?php echo htmlspecialchars(strip_tags($item['item_image'] ?? '')); ?>" style="flex-grow: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; font-size: 0.8rem;" placeholder="Direct URL (image only)">
                                            <input type="file" name="item_image_file" accept="image/*" style="width: 100px; font-size: 0.7rem;">
                                        </div>
                                        <p style="margin: 8px 0 0; font-size: 0.7rem; color: #94a3b8;">Images only. A PDF belongs in the box below, or it will not be downloadable.</p>
                                    </div>

                                    <div style="margin-top: 12px; background: #fafafa; padding: 12px; border-radius: 12px; border: 1px solid #f1f5f9;">
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">
                                            <i class="fas fa-file-arrow-down"></i> Downloadable Document
                                        </label>
                                        <input type="file" name="item_file" accept=".pdf,.doc,.docx,.xls,.xlsx" style="width: 100%; font-size: 0.75rem;">
                                        <p style="margin: 8px 0 0; font-size: 0.7rem; color: #94a3b8;">
                                            PDF, Word or Excel. Fills in the Link / Action URL above.
                                            <?php if (!empty($item['item_link']) && preg_match('~\.(pdf|docx?|xlsx?)$~i', $item['item_link'])): ?>
                                                Current: <a href="../<?php echo implode('/', array_map('rawurlencode', explode('/', $item['item_link']))); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars(basename($item['item_link'])); ?></a>
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <div style="margin-top: 20px;">
                                        <button type="submit" style="width: 100%; padding: 12px; background: #1e293b; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: background 0.2s;">
                                            <i class="fas fa-check-double" style="margin-right: 8px;"></i> Save This Entry
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Stats Section (Optional for some pages) -->
        <?php if (!empty($page_stats)): ?>
        <div style="background: #fff; border-radius: 20px; padding: 35px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
             <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background: #fff7ed; color: #ea580c; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #1e293b;">Quick Statistics</h3>
                    <p style="margin: 0; color: #64748b;">Counters and highlights displayed prominently.</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <?php foreach ($page_stats as $stat): ?>
                <div style="background: #fafafa; border: 1px solid #e2e8f0; padding: 20px; border-radius: 15px;">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_stat">
                        <input type="hidden" name="stat_id" value="<?php echo $stat['id']; ?>">
                        <div style="margin-bottom: 12px;">
                            <input type="text" name="stat_value" value="<?php echo htmlspecialchars(strip_tags($stat['stat_value'])); ?>" style="width: 100%; border: none; border-bottom: 2px solid #e2e8f0; background: transparent; font-size: 1.4rem; font-weight: 900; color: #ea580c; padding: 5px;" placeholder="Value">
                        </div>
                        <div style="margin-bottom: 12px;">
                            <input type="text" name="stat_label" value="<?php echo htmlspecialchars(strip_tags($stat['stat_label'])); ?>" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; font-size: 0.9rem;" placeholder="Label">
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="text" name="stat_icon" value="<?php echo htmlspecialchars(strip_tags($stat['stat_icon'] ?? '')); ?>" style="flex-grow: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; font-size: 0.85rem;" placeholder="Icon">
                            <button type="submit" style="background: #ea580c; color: #fff; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer;"><i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<!-- Modals -->
<div id="addItemModal" class="admin-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: flex-start; justify-content: center; padding: 40px 20px; overflow-y: auto; backdrop-filter: blur(5px);">
    <div style="background: #fff; width: 90%; max-width: 600px; padding: 35px; border-radius: 25px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); margin: auto;">
        <h3 style="margin-top: 0; font-size: 1.6rem; font-weight: 900; color: #1e293b; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-plus-circle" style="color: #4f46e5;"></i>
            Create New Entry
        </h3>
        <p style="color: #64748b; margin-bottom: 30px;">Adding to section: <strong id="sectionDisplay" style="color: #1e293b;"></strong></p>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="page_key" id="addItemPageKey">
            <input type="hidden" name="section_key" id="addItemSectionKey">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 800; font-size: 0.9rem; color: #475569; margin-bottom: 8px;">Title / Heading</label>
                <input type="text" name="item_title" required style="width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 800; font-size: 0.9rem; color: #475569; margin-bottom: 8px;">Content / Description</label>
                <textarea name="item_description" rows="3" style="width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 800; font-size: 0.8rem; color: #475569; margin-bottom: 5px;">Subtitle / Tag</label>
                    <input type="text" name="item_subtitle" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 800; font-size: 0.8rem; color: #475569; margin-bottom: 5px;">Stat / Badge Value</label>
                    <input type="text" name="item_stat_value" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 800; font-size: 0.8rem; color: #475569; margin-bottom: 5px;">Icon Name</label>
                    <input type="text" name="item_icon" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px;" placeholder="e.g. school">
                </div>
                <div>
                    <label style="display: block; font-weight: 800; font-size: 0.8rem; color: #475569; margin-bottom: 5px;">Link / URL</label>
                    <input type="text" name="item_link" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px;">
                </div>
            </div>

            <div style="margin-bottom: 25px; background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px dashed #cbd5e1;">
                <label style="display: block; font-weight: 800; font-size: 0.8rem; color: #475569; margin-bottom: 10px;">Media Asset (Optional)</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="item_image" placeholder="Image URL" style="flex-grow: 1; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px;">
                    <input type="file" name="item_image_file" accept="image/*" style="width: 150px; font-size: 0.7rem;">
                </div>
                <div style="margin-top: 12px;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Downloadable Document</label>
                    <input type="file" name="item_file" accept=".pdf,.doc,.docx,.xls,.xlsx" style="width: 100%; font-size: 0.75rem;">
                    <p style="margin: 6px 0 0; font-size: 0.7rem; color: #94a3b8;">PDF, Word or Excel &mdash; fills in the Link / Action URL for you.</p>
                </div>
            </div>

            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('addItemModal').style.display='none'" style="padding: 14px 25px; background: #f1f5f9; color: #64748b; border: none; border-radius: 12px; font-weight: 800; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 14px 40px; background: #4f46e5; color: #fff; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);">Create Entry</button>
            </div>
        </form>
    </div>
</div>

<div id="editSectionModal" class="admin-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: flex-start; justify-content: center; padding: 40px 20px; overflow-y: auto; backdrop-filter: blur(5px);">
    <div style="background: #fff; width: 90%; max-width: 500px; padding: 35px; border-radius: 25px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); margin: auto;">
        <h3 style="margin-top: 0; font-size: 1.6rem; font-weight: 900; color: #1e293b; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-layer-group" style="color: #4f46e5;"></i>
            Edit Section Header
        </h3>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_section">
            <input type="hidden" name="section_id" id="editSectionId">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 800; font-size: 0.9rem; color: #475569; margin-bottom: 8px;">Main Title</label>
                <input type="text" name="section_title" id="editSectionTitle" required style="width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 800; font-size: 0.9rem; color: #475569; margin-bottom: 8px;">Section Subtitle</label>
                <input type="text" name="section_subtitle" id="editSectionSubtitle" style="width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px;">
            </div>

            <div style="margin-bottom: 25px; background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px dashed #cbd5e1;">
                <label style="display: block; font-weight: 800; font-size: 0.8rem; color: #475569; margin-bottom: 10px;">Section Image (Optional)</label>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <input type="text" name="section_image" id="editSectionImage" placeholder="Image URL" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">OR UPLOAD:</span>
                        <input type="file" name="section_image_file" style="font-size: 0.7rem;">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('editSectionModal').style.display='none'" style="padding: 14px 25px; background: #f1f5f9; color: #64748b; border: none; border-radius: 12px; font-weight: 800; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 14px 40px; background: #4f46e5; color: #fff; border: none; border-radius: 12px; font-weight: 800; cursor: pointer;">Save Changes</button>
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
    if (confirm('Are you certain you want to remove this content? This action cannot be reversed.')) {
        document.getElementById('deleteItemId').value = id;
        document.getElementById('deleteItemForm').submit();
    }
}

function addItem(pageKey, sectionKey) {
    document.getElementById('addItemPageKey').value = pageKey;
    document.getElementById('addItemSectionKey').value = sectionKey;
    document.getElementById('sectionDisplay').innerText = sectionKey.replace('_', ' ').toUpperCase();
    document.getElementById('addItemModal').style.display = 'flex';
}

function editSection(id, title, subtitle, image) {
    document.getElementById('editSectionId').value = id;
    document.getElementById('editSectionTitle').value = title;
    document.getElementById('editSectionSubtitle').value = subtitle;
    document.getElementById('editSectionImage').value = image || '';
    document.getElementById('editSectionModal').style.display = 'flex';
}

window.onclick = function(event) {
    if (event.target.classList.contains('admin-modal')) {
        event.target.style.display = 'none';
    }
}

// Interactivity for item cards
document.querySelectorAll('.item-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-5px)';
        card.style.boxShadow = '0 10px 30px rgba(0,0,0,0.08)';
        card.style.transition = 'all 0.3s ease';
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = 'none';
    });
});
</script>

<style>
/* Smooth scroll for tabs */
.page-tabs-container::-webkit-scrollbar {
    height: 4px;
}
.page-tabs-container::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.modern-tab.active i {
    animation: bounce 1s ease infinite;
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}
input:focus, textarea:focus {
    outline: none;
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}
</style>

<?php include 'footer.php'; ?>
