<?php
/**
 * Admin - Manage Departmental Resources Pages
 * Manages content for: mobile_money_fee_payment.php, policies.php, 
 * faculty_and_staff_forms.php, employment_opportunity.php, and elearning_materials.php
 */

session_start();
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Departmental Resources";
$current_page = 'manage_departmental_resources.php';

// Define the pages we manage
$managed_pages = [
    'mobile_money_payment' => [
        'title' => 'MoMo Fee Payment',
        'icon' => 'fa-mobile-alt',
        'file' => 'mobile_money_fee_payment.php',
        'description' => 'Manage USSD codes, payment steps, and mobile network info.'
    ],
    'policies' => [
        'title' => 'University Policies',
        'icon' => 'fa-gavel',
        'file' => 'policies.php',
        'description' => 'Manage institutional policies, bylaws, and archived documents.'
    ],
    'faculty_staff_forms' => [
        'title' => 'Faculty & Staff Forms',
        'icon' => 'fa-file-signature',
        'file' => 'faculty_and_staff_forms.php',
        'description' => 'Manage downloadable administrative forms and submission guidelines.'
    ],
    'employment_opportunities' => [
        'title' => 'Careers & Jobs',
        'icon' => 'fa-briefcase',
        'file' => 'employment_opportunity.php',
        'description' => 'Manage job openings, hiring process, and career benefits.'
    ],
    'elearning_materials' => [
        'title' => 'E-Learning Hub',
        'icon' => 'fa-laptop-code',
        'file' => 'elearning_materials.php',
        'description' => 'Manage digital manuals, email activation guides, and video tutorials.'
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
            
            $message = "Core page content update was successful!";
            $message_type = "success";
            
        } elseif ($action === 'update_item') {
            // Update an item
            $item_image = $_POST['item_image'] ?? '';
            if (isset($_FILES['item_image_file']) && $_FILES['item_image_file']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['item_image_file'], 'resources');
                if ($uploaded) $item_image = $uploaded;
            }

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
                $_POST['item_link'] ?? '',
                $_POST['item_stat_value'] ?? '',
                $_POST['item_extra_data'] ?? null,
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['item_id']
            ]);
            $message = "Content entry updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'add_item') {
            $item_image = $_POST['item_image'] ?? '';
            if (isset($_FILES['item_image_file']) && $_FILES['item_image_file']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['item_image_file'], 'resources');
                if ($uploaded) $item_image = $uploaded;
            }

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
                $_POST['item_link'] ?? '',
                $_POST['item_stat_value'] ?? '',
                $_POST['item_extra_data'] ?? null,
                $_POST['page_key'],
                $_POST['section_key']
            ]);
            $message = "New content entry added successfully!";
            $message_type = "success";
            
        } elseif ($action === 'delete_item') {
            $stmt = $pdo->prepare("DELETE FROM academic_pages_items WHERE id = ?");
            $stmt->execute([$_POST['item_id']]);
            $message = "Entry has been removed.";
            $message_type = "success";
            
        } elseif ($action === 'update_section') {
            $section_image = $_POST['section_image'] ?? '';
            if (isset($_FILES['section_image_file']) && $_FILES['section_image_file']['error'] === 0) {
                $uploaded = handleAdminFileUpload($_FILES['section_image_file'], 'resources');
                if ($uploaded) $section_image = $uploaded;
            }

            $stmt = $pdo->prepare("UPDATE academic_pages_sections SET section_title = ?, section_subtitle = ?, section_description = ?, section_image = ? WHERE id = ?");
            $stmt->execute([$_POST['section_title'], $_POST['section_subtitle'] ?? '', $_POST['section_description'] ?? '', $section_image, $_POST['section_id']]);
            $message = "Section header updated successfully!";
            $message_type = "success";
        } elseif ($action === 'update_stat') {
            $stmt = $pdo->prepare("UPDATE academic_pages_stats SET stat_value = ?, stat_label = ?, stat_icon = ? WHERE id = ?");
            $stmt->execute([$_POST['stat_value'], $_POST['stat_label'], $_POST['stat_icon'] ?? '', $_POST['stat_id']]);
            $message = "Stat card updated!";
            $message_type = "success";
        }
        
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Get current page selection
$current_page_key = $_GET['page'] ?? 'mobile_money_payment';
if (!isset($managed_pages[$current_page_key])) {
    $current_page_key = 'mobile_money_payment';
}

// Fetch page data
try {
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ?");
    $stmt->execute([$current_page_key]);
    $page_content = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
    $stmt->execute([$current_page_key]);
    $page_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? ORDER BY section_key, display_order");
    $stmt->execute([$current_page_key]);
    $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_items = [];
    foreach ($all_items as $item) {
        $page_items[$item['section_key']][] = $item;
    }

    $stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = ? ORDER BY display_order");
    $stmt->execute([$current_page_key]);
    $page_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Fatal Database Error: " . $e->getMessage());
}

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
    <div class="content-wrapper">
        <!-- Modern Header -->
        <div class="resource-manage-header" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 40px; border-radius: 30px; color: #fff; margin-bottom: 40px; box-shadow: 0 15px 35px rgba(37, 99, 235, 0.2); position: relative; overflow: hidden;">
            <div style="position: absolute; right: -50px; bottom: -50px; font-size: 15rem; opacity: 0.1; transform: rotate(-15deg);">
                <i class="fas <?php echo $managed_pages[$current_page_key]['icon']; ?>"></i>
            </div>
            <div style="position: relative; z-index: 1;">
                <h1 style="margin: 0; font-size: 2.5rem; font-weight: 900; letter-spacing: -1px; display: flex; align-items: center; gap: 20px;">
                   <i class="fas <?php echo $managed_pages[$current_page_key]['icon']; ?>" style="background: rgba(255,255,255,0.2); width: 70px; height: 70px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;"></i>
                   <?php echo $managed_pages[$current_page_key]['title']; ?> CMS
                </h1>
                <p style="margin: 15px 0 0 0; opacity: 0.9; font-size: 1.2rem; max-width: 700px;"><?php echo $managed_pages[$current_page_key]['description']; ?></p>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert-modern" style="background: <?php echo $message_type === 'success' ? '#ecfdf5' : '#fef2f2'; ?>; border-left: 5px solid <?php echo $message_type === 'success' ? '#10b981' : '#ef4444'; ?>; padding: 20px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; animation: slideIn 0.4s ease;">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>" style="color: <?php echo $message_type === 'success' ? '#10b981' : '#ef4444'; ?>; font-size: 1.5rem;"></i>
            <span style="font-weight: 700; color: <?php echo $message_type === 'success' ? '#065f46' : '#991b1b'; ?>;"><?php echo htmlspecialchars($message); ?></span>
        </div>
        <?php endif; ?>

        <!-- Horizontal Page Switcher -->
        <div class="resource-nav-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 40px;">
            <?php foreach ($managed_pages as $key => $info): ?>
            <a href="?page=<?php echo $key; ?>" 
               class="resource-nav-card <?php echo $current_page_key === $key ? 'active' : ''; ?>"
               style="background: <?php echo $current_page_key === $key ? '#fff' : 'rgba(255,255,255,0.5)'; ?>; padding: 20px; border-radius: 20px; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 10px; border: 2px solid <?php echo $current_page_key === $key ? '#3b82f6' : 'transparent'; ?>; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: <?php echo $current_page_key === $key ? '0 10px 25px rgba(0,0,0,0.05)' : 'none'; ?>;">
                <i class="fas <?php echo $info['icon']; ?>" style="font-size: 1.5rem; color: <?php echo $current_page_key === $key ? '#3b82f6' : '#94a3b8'; ?>;"></i>
                <span style="font-weight: 800; text-align: center; color: <?php echo $current_page_key === $key ? '#1e293b' : '#64748b'; ?>; font-size: 0.9rem;"><?php echo $info['title']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px; align-items: start;">
            <!-- Left Side: Section Management -->
            <div class="management-left">
                <!-- Branding & Global Content Card -->
                <div class="modern-card" style="background: #fff; border-radius: 25px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 40px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
                        <h3 style="margin: 0; font-size: 1.4rem; font-weight: 900; color: #1e293b; display: flex; align-items: center; gap: 12px;">
                            <span style="width: 10px; height: 30px; background: #3b82f6; border-radius: 5px;"></span>
                            Hero & Global Branding
                        </h3>
                        <a href="../<?php echo $managed_pages[$current_page_key]['file']; ?>" target="_blank" style="padding: 10px 20px; background: #f8fafc; color: #3b82f6; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 0.85rem;">Preview Live Page</a>
                    </div>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_page_content">
                        <input type="hidden" name="page_key" value="<?php echo $current_page_key; ?>">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                            <div class="form-field">
                                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Hero Badge</label>
                                <input type="text" name="hero_badge" value="<?php echo htmlspecialchars($page_content['hero_badge'] ?? ''); ?>" style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc; font-weight: 600;">
                            </div>
                            <div class="form-field">
                                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Hero Title (Normal)</label>
                                <input type="text" name="hero_title" value="<?php echo htmlspecialchars($page_content['hero_title'] ?? ''); ?>" style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc; font-weight: 600;">
                            </div>
                        </div>

                        <div class="form-field" style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Hero Subtitle (Gradient/Highlight)</label>
                            <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($page_content['hero_subtitle'] ?? ''); ?>" style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc; font-weight: 600;">
                        </div>

                        <div class="form-field" style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Hero Mission/Quote Description</label>
                            <textarea name="hero_description" rows="3" style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc; font-weight: 600; resize: vertical;"><?php echo htmlspecialchars($page_content['hero_description'] ?? ''); ?></textarea>
                        </div>

                        <div style="background: #fdfaf2; padding: 25px; border-radius: 20px; border: 1px dashed #fcd34d; margin-bottom: 30px;">
                           <div style="display: flex; gap: 20px; align-items: end;">
                                <div style="flex-grow: 1;">
                                    <label style="display: block; font-weight: 800; color: #92400e; font-size: 0.85rem; margin-bottom: 10px;">Hero Background URL</label>
                                    <input type="text" name="hero_image" value="<?php echo htmlspecialchars($page_content['hero_image'] ?? ''); ?>" style="width: 100%; padding: 12px; border: 2px solid #fef3c7; border-radius: 10px;">
                                </div>
                                <div style="width: 200px;">
                                    <label style="display: block; font-weight: 800; color: #92400e; font-size: 0.85rem; margin-bottom: 10px;">Upload New</label>
                                    <input type="file" name="hero_image_file" style="font-size: 0.7rem;">
                                </div>
                           </div>
                        </div>

                        <div style="height: 2px; background: #f1f5f9; margin: 40px 0;"></div>

                        <h4 style="margin: 0 0 25px 0; color: #1e293b; font-weight: 900; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Bottom Call to Action (CTA)</h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                            <div class="form-field">
                                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">CTA Main Heading</label>
                                <input type="text" name="cta_title" value="<?php echo htmlspecialchars($page_content['cta_title'] ?? ''); ?>" style="width: 100%; padding: 12px; border: 2px solid #f1f5f9; border-radius: 12px;">
                            </div>
                            <div class="form-field">
                                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">CTA Sub-Heading</label>
                                <input type="text" name="cta_subtitle" value="<?php echo htmlspecialchars($page_content['cta_subtitle'] ?? ''); ?>" style="width: 100%; padding: 12px; border: 2px solid #f1f5f9; border-radius: 12px;">
                            </div>
                            <div class="form-field">
                                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Button Label</label>
                                <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars($page_content['cta_button_text'] ?? ''); ?>" style="width: 100%; padding: 12px; border: 2px solid #f1f5f9; border-radius: 12px;">
                            </div>
                            <div class="form-field">
                                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Button Action (Link/Email)</label>
                                <input type="text" name="cta_button_link" value="<?php echo htmlspecialchars($page_content['cta_button_link'] ?? ''); ?>" style="width: 100%; padding: 12px; border: 2px solid #f1f5f9; border-radius: 12px;">
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <button type="submit" style="padding: 15px 45px; background: #1e293b; color: #fff; border: none; border-radius: 14px; font-weight: 900; cursor: pointer; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: transform 0.2s;">
                                <i class="fas fa-save" style="margin-right: 10px;"></i> Update Global branding
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Page Content Sections -->
                <div style="margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
                    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #1e293b;">Structured Page Sections</h2>
                    <span style="padding: 6px 15px; background: #e0f2fe; color: #075985; border-radius: 20px; font-weight: 900; font-size: 0.8rem;">Active Sections: <?php echo count($page_sections); ?></span>
                </div>

                <?php if (empty($page_sections)): ?>
                    <div style="background: #fff; padding: 80px; text-align: center; border-radius: 25px; border: 2px dashed #e2e8f0;">
                        <i class="fas fa-cubes" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
                        <h4 style="color: #64748b;">No distinct sections found for this page key.</h4>
                    </div>
                <?php else: ?>
                    <?php foreach ($page_sections as $section): ?>
                    <div class="dynamic-section-container" style="background: #fff; border-radius: 25px; margin-bottom: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; overflow: hidden;">
                        <div style="background: #fafafa; padding: 30px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                            <div>
                                <span style="font-size: 0.75rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;"><?php echo $section['section_key']; ?></span>
                                <h4 style="margin: 5px 0 0 0; font-size: 1.3rem; font-weight: 900; color: #1e293b;"><?php echo htmlspecialchars($section['section_title']); ?></h4>
                                <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #64748b; font-weight: 600;"><?php echo htmlspecialchars($section['section_subtitle'] ?? ''); ?></p>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button onclick="editSection(<?php echo $section['id']; ?>, '<?php echo addslashes($section['section_title']); ?>', '<?php echo addslashes($section['section_subtitle'] ?? ''); ?>', '<?php echo addslashes($section['section_image'] ?? ''); ?>')" style="padding: 10px 18px; border-radius: 12px; background: #fff; border: 2px solid #f1f5f9; color: #1e293b; font-weight: 800; font-size: 0.85rem; cursor: pointer;">
                                    <i class="fas fa-edit"></i> Edit Header
                                </button>
                                <button onclick="addItem('<?php echo $current_page_key; ?>', '<?php echo $section['section_key']; ?>')" style="padding: 10px 18px; border-radius: 12px; background: #3b82f6; color: #fff; border: none; font-weight: 800; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
                                    <i class="fas fa-plus"></i> Add Entry
                                </button>
                            </div>
                        </div>

                        <div style="padding: 30px;">
                            <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                                <?php if (empty($page_items[$section['section_key']])): ?>
                                    <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 20px; color: #94a3b8; font-weight: 700;">Section Workspace is Empty</div>
                                <?php else: ?>
                                    <?php foreach ($page_items[$section['section_key']] as $item): ?>
                                    <div class="modern-item-editor" style="background: <?php echo $item['is_active'] ? '#fff' : '#f8fafc'; ?>; border: 2px solid #f1f5f9; padding: 25px; border-radius: 20px; position: relative;">
                                        <form method="POST" action="" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update_item">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">

                                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                                                <div style="flex-grow: 1; display: flex; align-items: center; gap: 15px;">
                                                    <input type="text" name="item_title" value="<?php echo htmlspecialchars($item['item_title']); ?>" style="font-size: 1.1rem; font-weight: 900; color: #1e293b; border: none; border-bottom: 2px solid transparent; background: transparent; padding: 5px; width: 70%;" placeholder="Entry Title" onfocus="this.style.borderBottomColor='#3b82f6'" onblur="this.style.borderBottomColor='transparent'">
                                                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 800; color: #64748b; cursor: pointer;">
                                                        <input type="checkbox" name="is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?> style="width: 18px; height: 18px; accent-color: #10b981;"> Active
                                                    </label>
                                                </div>
                                                <button type="button" onclick="deleteItem(<?php echo $item['id']; ?>)" style="width: 35px; height: 35px; border-radius: 10px; background: #fee2e2; color: #ef4444; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fas fa-trash-alt"></i></button>
                                            </div>

                                            <div style="margin-bottom: 20px;">
                                                <textarea name="item_description" placeholder="Body Content / Description" rows="3" style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 12px; background: #fcfcfc; font-weight: 600;"><?php echo htmlspecialchars($item['item_description'] ?? ''); ?></textarea>
                                            </div>

                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                                <div class="mini-field">
                                                    <label style="display: block; font-size: 0.75rem; font-weight: 900; color: #94a3b8; margin-bottom: 5px; text-transform: uppercase;">Tag / Subtitle</label>
                                                    <input type="text" name="item_subtitle" value="<?php echo htmlspecialchars($item['item_subtitle'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                                </div>
                                                <div class="mini-field">
                                                    <label style="display: block; font-size: 0.75rem; font-weight: 900; color: #94a3b8; margin-bottom: 5px; text-transform: uppercase;">Stat / Badge Value</label>
                                                    <input type="text" name="item_stat_value" value="<?php echo htmlspecialchars($item['item_stat_value'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                                </div>
                                                <div class="mini-field">
                                                    <label style="display: block; font-size: 0.75rem; font-weight: 900; color: #94a3b8; margin-bottom: 5px; text-transform: uppercase;">Icon / Color</label>
                                                    <div style="display: flex; gap: 5px;">
                                                        <input type="text" name="item_icon" value="<?php echo htmlspecialchars($item['item_icon'] ?? ''); ?>" style="flex-grow: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                                        <input type="text" name="item_color" value="<?php echo htmlspecialchars($item['item_color'] ?? 'blue-600'); ?>" style="width: 50%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                                    </div>
                                                </div>
                                                <div class="mini-field">
                                                    <label style="display: block; font-size: 0.75rem; font-weight: 900; color: #94a3b8; margin-bottom: 5px; text-transform: uppercase;">Link / Asset Path</label>
                                                    <input type="text" name="item_link" value="<?php echo htmlspecialchars($item['item_link'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                                </div>
                                            </div>

                                            <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
                                                <button type="submit" style="padding: 10px 25px; background: #e2e8f0; color: #475569; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; transition: all 0.2s;">Save Item</button>
                                            </div>
                                        </form>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Right Side: Sidebar Tools -->
            <div class="management-right">
                <!-- Stats Editor (If applicable) -->
                <?php if (!empty($page_stats)): ?>
                <div class="modern-card" style="background: #fff; border-radius: 25px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 30px;">
                     <h3 style="margin: 0 0 25px 0; font-size: 1.2rem; font-weight: 900; color: #1e293b; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-chart-bar" style="color: #f97316;"></i>
                        Counter Stats
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($page_stats as $stat): ?>
                        <div style="background: #fffaf0; padding: 20px; border-radius: 18px; border: 1px solid #ffedd5;">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="update_stat">
                                <input type="hidden" name="stat_id" value="<?php echo $stat['id']; ?>">
                                <input type="text" name="stat_value" value="<?php echo htmlspecialchars($stat['stat_value']); ?>" style="width: 100%; border: none; background: transparent; font-size: 1.4rem; font-weight: 900; color: #ea580c; margin-bottom: 5px;">
                                <input type="text" name="stat_label" value="<?php echo htmlspecialchars($stat['stat_label']); ?>" style="width: 100%; border: none; background: transparent; font-size: 0.85rem; font-weight: 800; color: #9a3412; margin-bottom: 12px;">
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" name="stat_icon" value="<?php echo htmlspecialchars($stat['stat_icon'] ?? ''); ?>" style="flex-grow: 1; padding: 8px; border: 1px solid #fed7aa; border-radius: 8px; font-size: 0.75rem;">
                                    <button type="submit" style="background: #ea580c; color: #fff; border: none; padding: 5px 12px; border-radius: 8px; cursor: pointer;"><i class="fas fa-check"></i></button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="modern-card" style="background: #1e293b; border-radius: 25px; padding: 30px; color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <h4 style="margin: 0 0 20px 0; font-size: 1.1rem; font-weight: 900;">Admin Guidelines</h4>
                    <p style="font-size: 0.85rem; line-height: 1.6; opacity: 0.8;">
                        • Use high-quality imagery for hero banners.<br>
                        • Icon names refer to Google Material Symbols (e.g., 'school', 'bolt').<br>
                        • Color classes follow the Tailwind pattern (e.g., 'blue-600').<br>
                        • Changes are reflected instantly on the live site.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modals -->
<div id="addItemModal" class="modern-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 9999; align-items: start; justify-content: center; padding: 50px 20px; overflow-y: auto;">
    <div style="background: #fff; width: 100%; max-width: 600px; border-radius: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transition: all 0.3s ease; transform: translateY(20px);">
        <div style="padding: 30px 40px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.5rem; font-weight: 900; color: #1e293b;"><i class="fas fa-plus-circle" style="color: #3b82f6; margin-right: 15px;"></i> New Content Entry</h3>
            <button onclick="document.getElementById('addItemModal').style.display='none'" style="background: #f1f5f9; border: none; width: 35px; height: 35px; border-radius: 10px; color: #64748b; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" style="padding: 40px;">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="page_key" id="addItemPageKey">
            <input type="hidden" name="section_key" id="addItemSectionKey">
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Entry Title / Heading</label>
                <input type="text" name="item_title" required style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Description / Body</label>
                <textarea name="item_description" rows="3" style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; font-weight: 800; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Badge/Stat Value</label>
                    <input type="text" name="item_stat_value" style="width: 100%; padding: 12px; border: 2px solid #f1f5f9; border-radius: 10px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 800; color: #475569; font-size: 0.8rem; margin-bottom: 8px;">Link URL</label>
                    <input type="text" name="item_link" style="width: 100%; padding: 12px; border: 2px solid #f1f5f9; border-radius: 10px;">
                </div>
            </div>

            <div style="display: flex; gap: 20px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('addItemModal').style.display='none'" style="padding: 15px 30px; background: #f1f5f9; color: #64748b; border: none; border-radius: 14px; font-weight: 800; cursor: pointer;">Discard</button>
                <button type="submit" style="padding: 15px 40px; background: #3b82f6; color: #fff; border: none; border-radius: 14px; font-weight: 900; cursor: pointer; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);">Deploy Entry</button>
            </div>
        </form>
    </div>
</div>

<div id="editSectionModal" class="modern-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 9999; align-items: start; justify-content: center; padding: 50px 20px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
        <div style="padding: 30px 40px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.4rem; font-weight: 900; color: #1e293b;">Section Settings</h3>
            <button onclick="document.getElementById('editSectionModal').style.display='none'" style="background: #f1f5f9; border: none; width: 35px; height: 35px; border-radius: 10px; color: #64748b; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="" style="padding: 40px;">
            <input type="hidden" name="action" value="update_section">
            <input type="hidden" name="section_id" id="editSectionId">
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Section Title</label>
                <input type="text" name="section_title" id="editSectionTitle" required style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc;">
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; font-weight: 800; color: #475569; font-size: 0.85rem; margin-bottom: 10px; text-transform: uppercase;">Section Subtitle</label>
                <input type="text" name="section_subtitle" id="editSectionSubtitle" style="width: 100%; padding: 15px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc;">
            </div>

            <div style="text-align: right;">
                <button type="submit" style="padding: 15px 40px; background: #3b82f6; color: #fff; border: none; border-radius: 14px; font-weight: 900; cursor: pointer;">Save Settings</button>
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
    if (confirm('Permanently delete this entry?')) {
        document.getElementById('deleteItemId').value = id;
        document.getElementById('deleteItemForm').submit();
    }
}

function addItem(pageKey, sectionKey) {
    document.getElementById('addItemPageKey').value = pageKey;
    document.getElementById('addItemSectionKey').value = sectionKey;
    document.getElementById('addItemModal').style.display = 'flex';
}

function editSection(id, title, subtitle, image) {
    document.getElementById('editSectionId').value = id;
    document.getElementById('editSectionTitle').value = title;
    document.getElementById('editSectionSubtitle').value = subtitle;
    document.getElementById('editSectionModal').style.display = 'flex';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modern-modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<style>
.main-content { margin-left: 280px; padding: 40px; background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif; }
@media (max-width: 1024px) { .main-content { margin-left: 0; padding: 20px; } }
input:focus, textarea:focus { outline: none; border-color: #3b82f6 !important; background: #fff !important; }
.resource-nav-card:hover { transform: translateY(-5px); background: #fff; border-color: #3b82f6; }
.modern-item-editor:hover { border-color: #3b82f6; shadow: 0 10px 30px rgba(0,0,0,0.05); }
@keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<?php include 'footer.php'; ?>
