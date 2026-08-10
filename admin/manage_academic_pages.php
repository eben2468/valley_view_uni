<?php
/**
 * Admin - Manage Academic Overview Pages
 * Manages content for: admissions.php, academic_programs_overview.php, the_campus.php, learning_outcomes.php
 */

require_once __DIR__ . '/../includes/admin_auth.php';
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';

$page_title = "Manage Academic Pages";
$current_page = 'manage_academic_pages.php';

// Define the pages we manage
$managed_pages = [
    'admissions' => [
        'title' => 'Admissions',
        'icon' => 'fa-user-graduate',
        'file' => 'admissions.php',
        'description' => 'Manage the admissions page content including hero, stats, requirements, and process steps.'
    ],
    'academic_programs' => [
        'title' => 'Academic Programs Overview',
        'icon' => 'fa-graduation-cap',
        'file' => 'academic_programs_overview.php',
        'description' => 'Manage the academic programs overview page hero and CTA sections.'
    ],
    'the_campus' => [
        'title' => 'The Campus',
        'icon' => 'fa-university',
        'file' => 'the_campus.php',
        'description' => 'Manage the campus page including highlights and features.'
    ],
    'learning_outcomes' => [
        'title' => 'Learning Outcomes',
        'icon' => 'fa-award',
        'file' => 'learning_outcomes.php',
        'description' => 'Manage the learning outcomes page including the eleven pillars and methods.'
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
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'academic');
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
                    cta_button_link = ?
                WHERE page_key = ?
            ");
            $stmt->execute([
                $_POST['hero_badge'],
                $_POST['hero_title'],
                $_POST['hero_subtitle'],
                $_POST['hero_description'],
                $hero_image,
                $_POST['cta_title'],
                $_POST['cta_subtitle'],
                $_POST['cta_button_text'] ?? '',
                $_POST['cta_button_link'] ?? '',
                $page_key
            ]);
            
            $message = "Page content updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'update_stat') {
            // Update a stat
            $stmt = $pdo->prepare("UPDATE academic_pages_stats SET stat_value = ?, stat_label = ?, stat_icon = ? WHERE id = ?");
            $stmt->execute([$_POST['stat_value'], $_POST['stat_label'], $_POST['stat_icon'] ?? '', $_POST['stat_id']]);
            $message = "Stat updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'add_stat') {
            $stmt = $pdo->prepare("INSERT INTO academic_pages_stats (page_key, stat_value, stat_label, stat_icon, display_order) VALUES (?, ?, ?, ?, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM academic_pages_stats s2 WHERE s2.page_key = ?))");
            $stmt->execute([$_POST['page_key'], $_POST['stat_value'], $_POST['stat_label'], $_POST['stat_icon'] ?? '', $_POST['page_key']]);
            $message = "Stat added successfully!";
            $message_type = "success";
            
        } elseif ($action === 'delete_stat') {
            $stmt = $pdo->prepare("DELETE FROM academic_pages_stats WHERE id = ?");
            $stmt->execute([$_POST['stat_id']]);
            $message = "Stat deleted successfully!";
            $message_type = "success";
            
        } elseif ($action === 'update_item') {
            // Update an item
            // Handle image upload
            $item_image = $_POST['item_image'];
            $uploaded = handleAdminFileUpload($_FILES['item_image_file'], 'academic');
            if ($uploaded) $item_image = $uploaded;

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
                $_POST['item_title'],
                $_POST['item_subtitle'] ?? '',
                $_POST['item_description'],
                $_POST['item_icon'] ?? '',
                $_POST['item_color'] ?? 'blue-600',
                $item_image,
                $_POST['item_link'] ?? '',
                $_POST['item_stat_value'] ?? '',
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['item_id']
            ]);
            $message = "Item updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'add_item') {
            // Handle image upload
            $item_image = $_POST['item_image'];
            $uploaded = handleAdminFileUpload($_FILES['item_image_file'], 'academic');
            if ($uploaded) $item_image = $uploaded;

            $stmt = $pdo->prepare("
                INSERT INTO academic_pages_items (page_key, section_key, item_title, item_subtitle, item_description, item_icon, item_color, item_image, item_link, item_stat_value, display_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM academic_pages_items i2 WHERE i2.page_key = ? AND i2.section_key = ?))
            ");
            $stmt->execute([
                $_POST['page_key'],
                $_POST['section_key'],
                $_POST['item_title'],
                $_POST['item_subtitle'] ?? '',
                $_POST['item_description'],
                $_POST['item_icon'] ?? '',
                $_POST['item_color'] ?? 'blue-600',
                $item_image,
                $_POST['item_link'] ?? '',
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
            $stmt = $pdo->prepare("UPDATE academic_pages_sections SET section_title = ?, section_subtitle = ?, section_description = ? WHERE id = ?");
            $stmt->execute([$_POST['section_title'], $_POST['section_subtitle'] ?? '', $_POST['section_description'] ?? '', $_POST['section_id']]);
            $message = "Section updated successfully!";
            $message_type = "success";
            
        } elseif ($action === 'reorder_items') {
            $items = json_decode($_POST['items_order'], true);
            foreach ($items as $index => $item_id) {
                $stmt = $pdo->prepare("UPDATE academic_pages_items SET display_order = ? WHERE id = ?");
                $stmt->execute([$index + 1, $item_id]);
            }
            $message = "Items reordered successfully!";
            $message_type = "success";
        }
        
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Get current page selection
$current_page_key = $_GET['page'] ?? 'admissions';
if (!isset($managed_pages[$current_page_key])) {
    $current_page_key = 'admissions';
}

// Fetch page content
$page_content = [];
$page_sections = [];
$page_items = [];
$page_stats = [];

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

<!-- Main Content Area -->
<main class="main-content">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1><i class="fas fa-file-alt"></i> Manage Academic Overview Pages</h1>
                <p class="page-description">Edit content for Admissions, Academic Programs, Campus, and Learning Outcomes pages.</p>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: <?php echo $message_type === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $message_type === 'success' ? '#155724' : '#721c24'; ?>;">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Page Tabs -->
        <div class="page-tabs" style="display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;">
            <?php foreach ($managed_pages as $key => $info): ?>
            <a href="?page=<?php echo $key; ?>" 
               class="page-tab <?php echo $current_page_key === $key ? 'active' : ''; ?>"
               style="display: flex; align-items: center; gap: 10px; padding: 15px 25px; background: <?php echo $current_page_key === $key ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : '#fff'; ?>; color: <?php echo $current_page_key === $key ? '#fff' : '#333'; ?>; border-radius: 12px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                <i class="fas <?php echo $info['icon']; ?>"></i>
                <?php echo $info['title']; ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Current Page Info -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border-radius: 16px; color: #fff; margin-bottom: 30px;">
            <h2 style="margin: 0 0 5px 0; font-size: 1.5rem;"><i class="fas <?php echo $managed_pages[$current_page_key]['icon']; ?>"></i> <?php echo $managed_pages[$current_page_key]['title']; ?></h2>
            <p style="margin: 0; opacity: 0.9;"><?php echo $managed_pages[$current_page_key]['description']; ?></p>
            <a href="../<?php echo $managed_pages[$current_page_key]['file']; ?>" target="_blank" style="display: inline-block; margin-top: 10px; padding: 8px 16px; background: rgba(255,255,255,0.2); color: #fff; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-external-link-alt"></i> Preview Page
            </a>
        </div>

        <!-- Hero Section Editor -->
        <div class="content-card" style="background: #fff; border-radius: 16px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h3 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                <span style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff;">
                    <i class="fas fa-image"></i>
                </span>
                Hero Section
            </h3>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_page_content">
                <input type="hidden" name="page_key" value="<?php echo $current_page_key; ?>">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Hero Badge Text</label>
                        <input type="text" name="hero_badge" value="<?php echo htmlspecialchars($page_content['hero_badge'] ?? ''); ?>" 
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Hero Image URL</label>
                        <input type="text" name="hero_image" value="<?php echo htmlspecialchars($page_content['hero_image'] ?? ''); ?>" 
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem;">
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Or Upload Hero Image</label>
                        <input type="file" name="hero_image_file" accept="image/*" 
                               style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem;">
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Hero Title (supports HTML for line breaks)</label>
                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars($page_content['hero_title'] ?? ''); ?>" 
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem;">
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Hero Subtitle</label>
                    <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($page_content['hero_subtitle'] ?? ''); ?>" 
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem;">
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Hero Description</label>
                    <textarea name="hero_description" rows="3" 
                              style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem; resize: vertical;"><?php echo htmlspecialchars($page_content['hero_description'] ?? ''); ?></textarea>
                </div>
                
                <hr style="margin: 25px 0; border: none; border-top: 2px solid #eee;">
                
                <h4 style="margin: 0 0 20px 0; color: #555;">Call to Action Section</h4>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">CTA Title</label>
                        <input type="text" name="cta_title" value="<?php echo htmlspecialchars($page_content['cta_title'] ?? ''); ?>" 
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem;">
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">CTA Button Text</label>
                        <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars($page_content['cta_button_text'] ?? ''); ?>" 
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem;">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">CTA Subtitle</label>
                        <textarea name="cta_subtitle" rows="2" 
                                  style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem; resize: vertical;"><?php echo htmlspecialchars($page_content['cta_subtitle'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">CTA Button Link</label>
                        <input type="text" name="cta_button_link" value="<?php echo htmlspecialchars($page_content['cta_button_link'] ?? ''); ?>" 
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1rem;">
                    </div>
                </div>
                
                <div style="margin-top: 25px;">
                    <button type="submit" style="padding: 14px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                        <i class="fas fa-save"></i> Save Hero & CTA Content
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Section Editor -->
        <?php if (!empty($page_stats) || in_array($current_page_key, ['admissions', 'learning_outcomes'])): ?>
        <div class="content-card" style="background: #fff; border-radius: 16px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #333; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 40px; height: 40px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff;">
                        <i class="fas fa-chart-bar"></i>
                    </span>
                    Quick Stats
                </h3>
                <button onclick="document.getElementById('addStatModal').style.display='flex'" style="padding: 10px 20px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-plus"></i> Add Stat
                </button>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <?php foreach ($page_stats as $stat): ?>
                <div style="background: linear-gradient(145deg, #f8f9fa 0%, #fff 100%); padding: 20px; border-radius: 12px; border: 2px solid #eee;">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_stat">
                        <input type="hidden" name="stat_id" value="<?php echo $stat['id']; ?>">
                        
                        <div style="display: grid; gap: 12px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Value</label>
                                <input type="text" name="stat_value" value="<?php echo htmlspecialchars($stat['stat_value']); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1.2rem; font-weight: bold; text-align: center;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Label</label>
                                <input type="text" name="stat_label" value="<?php echo htmlspecialchars($stat['stat_label']); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Icon (Material Symbols)</label>
                                <input type="text" name="stat_icon" value="<?php echo htmlspecialchars($stat['stat_icon'] ?? ''); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button type="submit" style="flex: 1; padding: 10px; background: #667eea; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-save"></i> Save
                            </button>
                            <button type="button" onclick="deleteStat(<?php echo $stat['id']; ?>)" style="padding: 10px 15px; background: #dc3545; color: #fff; border: none; border-radius: 8px; cursor: pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Sections & Items Editor -->
        <?php foreach ($page_sections as $section): ?>
        <div class="content-card" style="background: #fff; border-radius: 16px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                        <span style="width: 40px; height: 40px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff;">
                            <i class="fas fa-layer-group"></i>
                        </span>
                        <?php echo htmlspecialchars($section['section_title'] ?: ucwords(str_replace('_', ' ', $section['section_key']))); ?>
                    </h3>
                    <p style="margin: 0; color: #777; font-size: 0.9rem;"><?php echo htmlspecialchars($section['section_subtitle'] ?? ''); ?></p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button onclick="editSection(<?php echo $section['id']; ?>, '<?php echo addslashes($section['section_title']); ?>', '<?php echo addslashes($section['section_subtitle'] ?? ''); ?>', '<?php echo addslashes($section['section_description'] ?? ''); ?>')" 
                            style="padding: 8px 15px; background: #6c757d; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                        <i class="fas fa-edit"></i> Edit Section
                    </button>
                    <button onclick="addItem('<?php echo $current_page_key; ?>', '<?php echo $section['section_key']; ?>')" 
                            style="padding: 8px 15px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>
            </div>
            
            <?php if (!empty($page_items[$section['section_key']])): ?>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($page_items[$section['section_key']] as $item): ?>
                <div style="background: <?php echo $item['is_active'] ? 'linear-gradient(145deg, #f8f9fa 0%, #fff 100%)' : '#f0f0f0'; ?>; padding: 20px; border-radius: 12px; border: 2px solid #eee; <?php echo !$item['is_active'] ? 'opacity: 0.7;' : ''; ?>">
                    <form method="POST" action="" class="item-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Title</label>
                                <input type="text" name="item_title" value="<?php echo htmlspecialchars($item['item_title']); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-weight: 600;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Subtitle</label>
                                <input type="text" name="item_subtitle" value="<?php echo htmlspecialchars($item['item_subtitle'] ?? ''); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Icon</label>
                                <input type="text" name="item_icon" value="<?php echo htmlspecialchars($item['item_icon'] ?? ''); ?>" placeholder="e.g., school, verified" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Color Class</label>
                                <input type="text" name="item_color" value="<?php echo htmlspecialchars($item['item_color'] ?? 'blue-600'); ?>" placeholder="e.g., blue-600" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Number/Badge</label>
                                <input type="text" name="item_stat_value" value="<?php echo htmlspecialchars($item['item_stat_value'] ?? ''); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Image URL</label>
                                <input type="text" name="item_image" value="<?php echo htmlspecialchars($item['item_image'] ?? ''); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Or Upload Image</label>
                                <input type="file" name="item_image_file" accept="image/*"
                                       style="width: 100%; padding: 8px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.85rem;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Link URL</label>
                                <input type="text" name="item_link" value="<?php echo htmlspecialchars($item['item_link'] ?? ''); ?>" 
                                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            </div>
                        </div>
                        
                        <div style="margin-top: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555; font-size: 0.85rem;">Description</label>
                            <textarea name="item_description" rows="2" 
                                      style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; resize: vertical;"><?php echo htmlspecialchars($item['item_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; flex-wrap: wrap; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?> style="width: 18px; height: 18px;">
                                <span style="font-weight: 600; color: #555;">Active</span>
                            </label>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" style="padding: 10px 20px; background: #667eea; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <button type="button" onclick="deleteItem(<?php echo $item['id']; ?>)" style="padding: 10px 15px; background: #dc3545; color: #fff; border: none; border-radius: 8px; cursor: pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color: #999; text-align: center; padding: 30px;">No items in this section. Click "Add Item" to create one.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

    </div>
</main>

<!-- Add Stat Modal -->
<div id="addStatModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; padding: 30px; border-radius: 16px; max-width: 400px; width: 90%;">
        <h3 style="margin: 0 0 20px 0;"><i class="fas fa-chart-bar"></i> Add New Stat</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_stat">
            <input type="hidden" name="page_key" value="<?php echo $current_page_key; ?>">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Value</label>
                <input type="text" name="stat_value" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" placeholder="e.g., 50+">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Label</label>
                <input type="text" name="stat_label" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" placeholder="e.g., Programs">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Icon (optional)</label>
                <input type="text" name="stat_icon" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" placeholder="e.g., school">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 12px; background: #667eea; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Add Stat</button>
                <button type="button" onclick="document.getElementById('addStatModal').style.display='none'" style="padding: 12px 20px; background: #6c757d; color: #fff; border: none; border-radius: 8px; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Item Modal -->
<div id="addItemModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; overflow-y: auto; padding: 20px;">
    <div style="background: #fff; padding: 30px; border-radius: 16px; max-width: 600px; width: 100%; margin: auto;">
        <h3 style="margin: 0 0 20px 0;"><i class="fas fa-plus-circle"></i> Add New Item</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="page_key" id="addItemPageKey">
            <input type="hidden" name="section_key" id="addItemSectionKey">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Title</label>
                    <input type="text" name="item_title" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Subtitle (optional)</label>
                    <input type="text" name="item_subtitle" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                </div>
            </div>
            
            <div style="margin-top: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Description</label>
                <textarea name="item_description" rows="3" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;"></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Icon</label>
                    <input type="text" name="item_icon" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" placeholder="e.g., school">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Color</label>
                    <input type="text" name="item_color" value="blue-600" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Number/Badge</label>
                    <input type="text" name="item_stat_value" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Image URL</label>
                    <input type="text" name="item_image" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" placeholder="e.g., images/grad.jpg">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Or Upload Image</label>
                    <input type="file" name="item_image_file" accept="image/*" style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                </div>
            </div>
            
            <div style="margin-top: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Link URL</label>
                <input type="text" name="item_link" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;" placeholder="e.g., details.php">
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" style="flex: 1; padding: 12px; background: #667eea; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Add Item</button>
                <button type="button" onclick="document.getElementById('addItemModal').style.display='none'" style="padding: 12px 20px; background: #6c757d; color: #fff; border: none; border-radius: 8px; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="editSectionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; padding: 30px; border-radius: 16px; max-width: 500px; width: 90%;">
        <h3 style="margin: 0 0 20px 0;"><i class="fas fa-edit"></i> Edit Section</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update_section">
            <input type="hidden" name="section_id" id="editSectionId">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Section Title</label>
                <input type="text" name="section_title" id="editSectionTitle" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Section Subtitle</label>
                <input type="text" name="section_subtitle" id="editSectionSubtitle" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Section Description (optional)</label>
                <textarea name="section_description" id="editSectionDescription" rows="3" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 12px; background: #667eea; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Save Section</button>
                <button type="button" onclick="document.getElementById('editSectionModal').style.display='none'" style="padding: 12px 20px; background: #6c757d; color: #fff; border: none; border-radius: 8px; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteStatForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete_stat">
    <input type="hidden" name="stat_id" id="deleteStatId">
</form>

<form id="deleteItemForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete_item">
    <input type="hidden" name="item_id" id="deleteItemId">
</form>

<script>
function deleteStat(id) {
    if (confirm('Are you sure you want to delete this stat?')) {
        document.getElementById('deleteStatId').value = id;
        document.getElementById('deleteStatForm').submit();
    }
}

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        document.getElementById('deleteItemId').value = id;
        document.getElementById('deleteItemForm').submit();
    }
}

function addItem(pageKey, sectionKey) {
    document.getElementById('addItemPageKey').value = pageKey;
    document.getElementById('addItemSectionKey').value = sectionKey;
    document.getElementById('addItemModal').style.display = 'flex';
}

function editSection(id, title, subtitle, description) {
    document.getElementById('editSectionId').value = id;
    document.getElementById('editSectionTitle').value = title;
    document.getElementById('editSectionSubtitle').value = subtitle;
    document.getElementById('editSectionDescription').value = description;
    document.getElementById('editSectionModal').style.display = 'flex';
}

// Close modals when clicking outside
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>

<?php include 'footer.php'; ?>
