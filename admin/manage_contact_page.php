<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
require_once __DIR__ . '/../includes/admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        // ========================================
        // HERO ACTIONS
        // ========================================
        if ($action === 'update_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'contact');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE contact_hero SET badge_text=?, title_1=?, title_2=?, description=?, image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['badge_text'], $_POST['title_1'], $_POST['title_2'], $_POST['description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Hero section updated successfully!";
        }
        
        // ========================================
        // QUICK CARDS ACTIONS
        // ========================================
        elseif ($action === 'update_quick_card') {
            $stmt = $pdo->prepare("UPDATE contact_quick_cards SET icon=?, title=?, description=?, bg_gradient=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['bg_gradient'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Quick card updated successfully!";
        }
        
        // ========================================
        // POSTAL ADDRESS ACTIONS
        // ========================================
        elseif ($action === 'update_postal') {
            $stmt = $pdo->prepare("UPDATE contact_postal_addresses SET icon=?, title=?, description=?, icon_bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['icon_bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Postal address updated successfully!";
        }
        
        // ========================================
        // SOCIAL LINKS ACTIONS
        // ========================================
        elseif ($action === 'update_social') {
            $stmt = $pdo->prepare("UPDATE contact_social_links SET platform=?, icon=?, url=?, color_class=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['platform'], $_POST['icon'], $_POST['url'], $_POST['color_class'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Social link updated successfully!";
        }
        
        // ========================================
        // EMERGENCY/USSD ACTIONS
        // ========================================
        elseif ($action === 'update_emergency_ussd') {
            $stmt = $pdo->prepare("UPDATE contact_emergency_ussd SET title=?, description=?, main_value=?, btn_text=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['main_value'], $_POST['btn_text'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Emergency/USSD section updated successfully!";
        }
        
        // ========================================
        // DEPARTMENTS ACTIONS
        // ========================================
        elseif ($action === 'update_dept_header') {
            $stmt = $pdo->prepare("UPDATE contact_departments_header SET badge_text=?, title=?, description=? WHERE id=?");
            $stmt->execute([$_POST['badge_text'], $_POST['title'], $_POST['description'], $_POST['id']]);
            $success = "Department header updated successfully!";
        }
        elseif ($action === 'update_dept') {
            $stmt = $pdo->prepare("UPDATE contact_departments SET icon=?, name=?, phone_1=?, phone_2=?, email=?, icon_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['name'], $_POST['phone_1'], $_POST['phone_2'], $_POST['email'], $_POST['icon_color'], $_POST['display_order'] ?? 0, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Department updated successfully!";
        }
        
        // ========================================
        // FAQ ACTIONS
        // ========================================
        elseif ($action === 'update_faq_header') {
            $stmt = $pdo->prepare("UPDATE contact_faq_header SET badge_text=?, title=?, description=? WHERE id=?");
            $stmt->execute([$_POST['badge_text'], $_POST['title'], $_POST['description'], $_POST['id']]);
            $success = "FAQ header updated successfully!";
        }
        elseif ($action === 'update_faq') {
            $stmt = $pdo->prepare("UPDATE contact_faqs SET question=?, answer=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['question'], $_POST['answer'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "FAQ updated successfully!";
        }
        
        // ========================================
        // MAP OVERLAY ACTIONS
        // ========================================
        elseif ($action === 'update_map') {
            $stmt = $pdo->prepare("UPDATE contact_map_overlay SET title=?, description=?, link_text=?, link_url=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['link_text'], $_POST['link_url'], $_POST['id']]);
            $success = "Map overlay updated successfully!";
        }
        
        // ========================================
        // CTA ACTIONS
        // ========================================
        elseif ($action === 'update_cta') {
            $stmt = $pdo->prepare("UPDATE contact_cta SET title=?, subtitle=?, description=?, btn1_text=?, btn1_url=?, btn2_text=?, btn2_url=?, stat1_value=?, stat1_label=?, stat2_value=?, stat2_label=?, stat3_value=?, stat3_label=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['subtitle'], $_POST['description'], $_POST['btn1_text'], $_POST['btn1_url'], $_POST['btn2_text'], $_POST['btn2_url'], $_POST['stat1_value'], $_POST['stat1_label'], $_POST['stat2_value'], $_POST['stat2_label'], $_POST['stat3_value'], $_POST['stat3_label'], $_POST['id']]);
            $success = "CTA section updated successfully!";
        }
        
        // ========================================
        // MAIN INFO ACTIONS
        // ========================================
        elseif ($action === 'update_main_info') {
            $stmt = $pdo->prepare("UPDATE contact_main_info SET title=?, address_1=?, address_2=?, address_3=?, telephone=?, email=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['address_1'], $_POST['address_2'], $_POST['address_3'], $_POST['telephone'], $_POST['email'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Main contact information updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch all data
$hero = $pdo->query("SELECT * FROM contact_hero LIMIT 1")->fetch();
$quick_cards = $pdo->query("SELECT * FROM contact_quick_cards ORDER BY display_order ASC")->fetchAll();
$postal = $pdo->query("SELECT * FROM contact_postal_addresses ORDER BY display_order ASC")->fetchAll();
$socials = $pdo->query("SELECT * FROM contact_social_links ORDER BY display_order ASC")->fetchAll();
$emerg_ussd = $pdo->query("SELECT * FROM contact_emergency_ussd")->fetchAll();
$dept_header = $pdo->query("SELECT * FROM contact_departments_header LIMIT 1")->fetch();
$depts = $pdo->query("SELECT * FROM contact_departments ORDER BY display_order ASC")->fetchAll();
$faq_header = $pdo->query("SELECT * FROM contact_faq_header LIMIT 1")->fetch();
$faqs = $pdo->query("SELECT * FROM contact_faqs ORDER BY display_order ASC")->fetchAll();
$map_overlay = $pdo->query("SELECT * FROM contact_map_overlay LIMIT 1")->fetch();
$cta = $pdo->query("SELECT * FROM contact_cta LIMIT 1")->fetch();
$main_info = $pdo->query("SELECT * FROM contact_main_info LIMIT 1")->fetch();

include 'header.php';
include 'sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h2>Manage Contact Us Page</h2>
        <p>Control every aspect of the contact page content, from the hero section to departments and FAQs.</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fa fa-check-circle" style="margin-right: 8px;"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle" style="margin-right: 8px;"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="contactTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main_info" type="button" role="tab">Main Contact Info</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">Hero & Cards</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="sidebar-tab" data-bs-toggle="tab" data-bs-target="#sidebar_content" type="button" role="tab">Sidebar Content</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="depts-tab" data-bs-toggle="tab" data-bs-target="#depts" type="button" role="tab">Departments</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="faqs-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab">FAQs</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer_cta" type="button" role="tab">Map & CTA</button>
        </li>
    </ul>

    <div class="tab-content" id="contactTabContent">
        <!-- Main Contact Info Tab -->
        <div class="tab-pane fade show active" id="main_info" role="tabpanel">
            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-university me-2"></i>University Main Contacts</h4>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_main_info">
                    <input type="hidden" name="id" value="<?php echo $main_info['id']; ?>">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Section Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($main_info['title']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Address Line 1</label>
                            <input type="text" name="address_1" class="form-control" value="<?php echo htmlspecialchars($main_info['address_1']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Address Line 2 (Optional)</label>
                            <input type="text" name="address_2" class="form-control" value="<?php echo htmlspecialchars($main_info['address_2']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Address Line 3 (Optional)</label>
                            <input type="text" name="address_3" class="form-control" value="<?php echo htmlspecialchars($main_info['address_3']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Telephone Number(s)</label>
                            <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($main_info['telephone']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address(es)</label>
                            <input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($main_info['email']); ?>" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Main Contact Info</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hero Section Tab -->
        <div class="tab-pane fade" id="hero" role="tabpanel">
            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-image me-2"></i>Hero Section</h4>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_hero">
                    <input type="hidden" name="id" value="<?php echo $hero['id']; ?>">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Badge Text</label>
                            <input type="text" name="badge_text" class="form-control" value="<?php echo htmlspecialchars($hero['badge_text']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hero Title Part 1</label>
                            <input type="text" name="title_1" class="form-control" value="<?php echo htmlspecialchars($hero['title_1']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hero Title Part 2</label>
                            <input type="text" name="title_2" class="form-control" value="<?php echo htmlspecialchars($hero['title_2']); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Hero Description (Quote)</label>
                            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($hero['description']); ?></textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Background Image URL</label>
                            <input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($hero['image_url']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Or Upload File</label>
                            <input type="file" name="hero_image_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Hero Section</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-th-large me-2"></i>Quick Contact Cards (Top 4)</h4>
                </div>
                <?php foreach ($quick_cards as $card): ?>
                <form method="POST" class="mb-4 p-3 border rounded">
                    <input type="hidden" name="action" value="update_quick_card">
                    <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small">Icon</label>
                            <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($card['icon']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($card['title']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Description</label>
                            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($card['description']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Gradient Class</label>
                            <input type="text" name="bg_gradient" class="form-control" value="<?php echo htmlspecialchars($card['bg_gradient']); ?>">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-info w-100">Save</button>
                        </div>
                    </div>
                </form>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sidebar Content Tab -->
        <div class="tab-pane fade" id="sidebar_content" role="tabpanel">
            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-envelope me-2"></i>Postal & Academic Addresses</h4>
                </div>
                <?php foreach ($postal as $item): ?>
                <form method="POST" class="mb-3 p-3 border rounded">
                    <input type="hidden" name="action" value="update_postal">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small">Icon</label>
                            <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Header</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Details</label>
                            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Color Class</label>
                            <input type="text" name="icon_bg_color" class="form-control" value="<?php echo htmlspecialchars($item['icon_bg_color']); ?>">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-info w-100">Save</button>
                        </div>
                    </div>
                </form>
                <?php endforeach; ?>
            </div>

            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-share-alt me-2"></i>Social Media Links</h4>
                </div>
                <div class="row">
                    <?php foreach ($socials as $social): ?>
                    <div class="col-md-6 mb-3">
                        <form method="POST" class="p-3 border rounded">
                            <input type="hidden" name="action" value="update_social">
                            <input type="hidden" name="id" value="<?php echo $social['id']; ?>">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Platform</label>
                                    <input type="text" name="platform" class="form-control" value="<?php echo htmlspecialchars($social['platform']); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Icon (FA)</label>
                                    <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($social['icon']); ?>">
                                </div>
                                <div class="col-10">
                                    <label class="form-label small">Link URL</label>
                                    <input type="text" name="url" class="form-control" value="<?php echo htmlspecialchars($social['url']); ?>">
                                </div>
                                <div class="col-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-sm btn-info w-100">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-warning me-2"></i>Emergency & USSD Sections</h4>
                </div>
                <?php foreach ($emerg_ussd as $sec): ?>
                <form method="POST" class="mb-4 p-3 border rounded bg-light">
                    <input type="hidden" name="action" value="update_emergency_ussd">
                    <input type="hidden" name="id" value="<?php echo $sec['id']; ?>">
                    <h6 class="text-secondary mb-3 fw-bold"><?php echo strtoupper($sec['section_type']); ?> SECTION</h6>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($sec['title']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($sec['description']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Primary Value (Code or Number)</label>
                            <input type="text" name="main_value" class="form-control" value="<?php echo htmlspecialchars($sec['main_value']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Button Text (if applicable)</label>
                            <input type="text" name="btn_text" class="form-control" value="<?php echo htmlspecialchars($sec['btn_text']); ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-primary">Update Section</button>
                        </div>
                    </div>
                </form>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Departments Tab -->
        <div class="tab-pane fade" id="depts" role="tabpanel">
            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3 mb-4 border-start border-primary border-4">
                <div class="inn-title mb-4">
                    <h4 class="mb-0 text-primary">Department List Header</h4>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_dept_header">
                    <input type="hidden" name="id" value="<?php echo $dept_header['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Badge Text</label>
                            <input type="text" name="badge_text" class="form-control" value="<?php echo htmlspecialchars($dept_header['badge_text']); ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Heading</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($dept_header['title']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Subtext</label>
                            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($dept_header['description']); ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save Header</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary">Manage Departments</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Icon</th>
                                <th style="width: 200px;">Department Name</th>
                                <th>Contact Info</th>
                                <th style="width: 80px;">Order</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($depts as $dept): ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_dept">
                                    <input type="hidden" name="id" value="<?php echo $dept['id']; ?>">
                                    <td>
                                        <input type="text" name="icon" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dept['icon']); ?>" title="Material Icon Name">
                                    </td>
                                    <td>
                                        <input type="text" name="name" class="form-control form-control-sm mb-1 fw-bold" value="<?php echo htmlspecialchars($dept['name']); ?>">
                                        <input type="text" name="icon_color" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dept['icon_color']); ?>" placeholder="Tailwind Color">
                                    </td>
                                    <td>
                                        <div class="row g-1">
                                            <div class="col-sm-6"><input type="text" name="phone_1" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dept['phone_1']); ?>" placeholder="Phone 1"></div>
                                            <div class="col-sm-6"><input type="text" name="phone_2" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dept['phone_2']); ?>" placeholder="Phone 2"></div>
                                            <div class="col-sm-12"><input type="text" name="email" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dept['email']); ?>" placeholder="Email"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" name="display_order" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dept['display_order']); ?>">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-success w-100">Save</button>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FAQ Tab -->
        <div class="tab-pane fade" id="faq" role="tabpanel">
            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary">FAQ Section Header</h4>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_faq_header">
                    <input type="hidden" name="id" value="<?php echo $faq_header['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge_text" class="form-control" value="<?php echo htmlspecialchars($faq_header['badge_text']); ?>">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($faq_header['title']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($faq_header['description']); ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save Header</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary">Frequently Asked Questions</h4>
                </div>
                <?php foreach ($faqs as $f): ?>
                <form method="POST" class="mb-4 pb-4 border-bottom">
                    <input type="hidden" name="action" value="update_faq">
                    <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                    <div class="mb-2">
                        <label class="form-label fw-bold">Question</label>
                        <input type="text" name="question" class="form-control" value="<?php echo htmlspecialchars($f['question']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Answer (HTML allowed)</label>
                        <textarea name="answer" class="form-control" rows="2" required><?php echo htmlspecialchars($f['answer']); ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <label class="small text-muted mb-0">Order: <input type="number" name="display_order" class="form-control form-control-sm d-inline-block w-auto" value="<?php echo $f['display_order']; ?>"></label>
                        </div>
                        <button type="submit" class="btn btn-sm btn-info px-4">Save Changes</button>
                    </div>
                </form>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Footer/Map Tab -->
        <div class="tab-pane fade" id="footer_cta" role="tabpanel">
            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-map-marker me-2"></i>Map Overlay Content</h4>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_map">
                    <input type="hidden" name="id" value="<?php echo $map_overlay['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Card Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($map_overlay['title']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($map_overlay['description']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Button/Link Text</label>
                            <input type="text" name="link_text" class="form-control" value="<?php echo htmlspecialchars($map_overlay['link_text']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Link URL</label>
                            <input type="text" name="link_url" class="form-control" value="<?php echo htmlspecialchars($map_overlay['link_url']); ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Map Overlay</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="dashboard-card shadow-sm p-4 bg-white rounded-3">
                <div class="inn-title mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary"><i class="fa fa-bullhorn me-2"></i>CTA Section Content</h4>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_cta">
                    <input type="hidden" name="id" value="<?php echo $cta['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title Line 1</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($cta['title']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Yellow Title Line</label>
                            <input type="text" name="subtitle" class="form-control" value="<?php echo htmlspecialchars($cta['subtitle']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($cta['description']); ?></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Btn 1 Text</label>
                            <input type="text" name="btn1_text" class="form-control" value="<?php echo htmlspecialchars($cta['btn1_text']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Btn 1 URL</label>
                            <input type="text" name="btn1_url" class="form-control" value="<?php echo htmlspecialchars($cta['btn1_url']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Btn 2 Text</label>
                            <input type="text" name="btn2_text" class="form-control" value="<?php echo htmlspecialchars($cta['btn2_text']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Btn 2 URL</label>
                            <input type="text" name="btn2_url" class="form-control" value="<?php echo htmlspecialchars($cta['btn2_url']); ?>">
                        </div>
                        
                        <!-- Stats -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Stat 1 Val</label>
                            <input type="text" name="stat1_value" class="form-control" value="<?php echo htmlspecialchars($cta['stat1_value']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Stat 1 Lbl</label>
                            <input type="text" name="stat1_label" class="form-control" value="<?php echo htmlspecialchars($cta['stat1_label']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Stat 2 Val</label>
                            <input type="text" name="stat2_value" class="form-control" value="<?php echo htmlspecialchars($cta['stat2_value']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Stat 2 Lbl</label>
                            <input type="text" name="stat2_label" class="form-control" value="<?php echo htmlspecialchars($cta['stat2_label']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Stat 3 Val</label>
                            <input type="text" name="stat3_value" class="form-control" value="<?php echo htmlspecialchars($cta['stat3_value']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Stat 3 Lbl</label>
                            <input type="text" name="stat3_label" class="form-control" value="<?php echo htmlspecialchars($cta['stat3_label']); ?>">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-5 btn-lg">Update CTA Section</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
    .nav-tabs .nav-link {
        color: #4b5563;
        font-weight: 600;
        padding: 12px 20px;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        color: #2563eb;
        background: transparent;
        border-bottom-color: #2563eb;
    }
    .dashboard-card {
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }
    .table th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>

<?php include 'footer.php'; ?>
