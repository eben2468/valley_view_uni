<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

/**
 * Generic function to handle list item updates
 */
function handleListItemUpdate($pdo, $table, $fields, $id) {
    $set_parts = [];
    $values = [];
    foreach ($fields as $field) {
        $set_parts[] = "$field = ?";
        
        // Handle file upload if the field is 'image'
        if ($field === 'image' && isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = handleAdminFileUpload($_FILES['image_upload'], 'campus_life');
            if ($uploadedPath) {
                $_POST[$field] = $uploadedPath;
            }
        }
        
        $values[] = $_POST[$field] ?? '';
    }
    $values[] = $id;
    
    $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($values);
}

/**
 * Generic function to handle list item insertions
 */
function handleListItemAdd($pdo, $table, $fields) {
    $field_names = implode(', ', $fields);
    $placeholders = implode(', ', array_fill(0, count($fields), '?'));
    $values = [];
    foreach ($fields as $field) {
        // Handle file upload if the field is 'image'
        if ($field === 'image' && isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = handleAdminFileUpload($_FILES['image_upload'], 'campus_life');
            if ($uploadedPath) {
                $_POST[$field] = $uploadedPath;
            }
        }
        
        $values[] = $_POST[$field] ?? '';
    }
    
    $sql = "INSERT INTO $table ($field_names) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($values);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $table = $_POST['table'] ?? '';
    $id = $_POST['id'] ?? null;
    
    try {
        if ($action === 'delete_item') {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Item deleted successfully!";
        } 
        elseif ($action === 'add_item' || $action === 'update_item') {
            $fields = [];
            switch ($table) {
                case 'philosophy_dress_principles':
                    $fields = ['title', 'description', 'icon', 'border_color', 'display_order', 'status'];
                    break;
                case 'philosophy_dress_benefits':
                    $fields = ['title', 'description', 'icon', 'gradient_start', 'gradient_end', 'display_order'];
                    break;
                case 'accommodation_features':
                    $fields = ['title', 'description', 'icon', 'display_order', 'status'];
                    break;
                case 'accommodation_halls':
                    $fields = ['type', 'title', 'description', 'halls_list', 'image', 'gradient_start', 'gradient_end', 'icon', 'display_order'];
                    // Special handling for hall image if needed
                    break;
                case 'food_services_features':
                    $fields = ['title', 'description', 'icon', 'color', 'display_order', 'status'];
                    break;
                case 'work_study_benefits':
                    $fields = ['title', 'description', 'icon', 'color', 'display_order', 'status'];
                    break;
                case 'work_study_opportunities':
                    $fields = ['category', 'opportunity_name', 'display_order', 'status'];
                    break;
                case 'work_study_steps':
                    $fields = ['step_number', 'title', 'description', 'color'];
                    break;
                case 'sld_services':
                    $fields = ['title', 'description', 'icon', 'color', 'display_order', 'status'];
                    break;
                case 'sld_staff':
                    $fields = ['name', 'position', 'campus', 'icon_color', 'display_order', 'status'];
                    break;
                case 'sld_locations':
                    $fields = ['title', 'description', 'icon', 'display_order'];
                    break;
                case 'radio_programs':
                    $fields = ['title', 'schedule', 'description', 'icon', 'border_color', 'icon_bg_color', 'display_order', 'status'];
                    break;
                case 'radio_features':
                    $fields = ['title', 'icon', 'color_class', 'display_order'];
                    break;
            }
            
            if ($action === 'add_item') {
                handleListItemAdd($pdo, $table, $fields);
                $success = "New item added successfully!";
            } else {
                handleListItemUpdate($pdo, $table, $fields, $id);
                $success = "Item updated successfully!";
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch all data
$principles = $pdo->query("SELECT * FROM philosophy_dress_principles ORDER BY display_order ASC")->fetchAll();
$phil_benefits = $pdo->query("SELECT * FROM philosophy_dress_benefits ORDER BY display_order ASC")->fetchAll();
$acc_features = $pdo->query("SELECT * FROM accommodation_features ORDER BY display_order ASC")->fetchAll();
$acc_halls = $pdo->query("SELECT * FROM accommodation_halls ORDER BY type, display_order ASC")->fetchAll();
$food_features = $pdo->query("SELECT * FROM food_services_features ORDER BY display_order ASC")->fetchAll();
$ws_benefits = $pdo->query("SELECT * FROM work_study_benefits ORDER BY display_order ASC")->fetchAll();
$ws_opportunities = $pdo->query("SELECT * FROM work_study_opportunities ORDER BY category, display_order ASC")->fetchAll();
$ws_steps = $pdo->query("SELECT * FROM work_study_steps ORDER BY step_number ASC")->fetchAll();
$sld_services = $pdo->query("SELECT * FROM sld_services ORDER BY display_order ASC")->fetchAll();
$sld_staff = $pdo->query("SELECT * FROM sld_staff ORDER BY display_order ASC")->fetchAll();
$sld_locations = $pdo->query("SELECT * FROM sld_locations ORDER BY display_order ASC")->fetchAll();
$radio_programs = $pdo->query("SELECT * FROM radio_programs ORDER BY display_order ASC")->fetchAll();
$radio_features = $pdo->query("SELECT * FROM radio_features ORDER BY display_order ASC")->fetchAll();

include 'header.php';
include 'sidebar.php';
?>

<style>
    :root {
        --vvu-blue: #003366;
        --vvu-gold: #ffcc00;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }
    
    .main-content {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding-bottom: 50px;
    }

    .page-header {
        background: linear-gradient(135deg, var(--vvu-blue) 0%, #004080 100%);
        padding: 40px;
        border-radius: 20px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 51, 102, 0.1);
    }

    .page-header h2 {
        font-weight: 800;
        letter-spacing: -1px;
    }

    .nav-tabs {
        border: none;
        gap: 10px;
        margin-bottom: 20px;
        padding: 10px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-tabs .nav-link:hover {
        background: #f8f9fa;
        color: var(--vvu-blue);
    }

    .nav-tabs .nav-link.active {
        background: var(--vvu-blue);
        color: white;
        box-shadow: 0 8px 20px rgba(0, 51, 102, 0.2);
    }

    .dashboard-card {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }

    .dashboard-card h4 {
        font-weight: 700;
        color: var(--vvu-blue);
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f1f1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .list-edit-item {
        background: #fbfbfb;
        border: 1px solid #eee;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .list-edit-item:hover {
        border-color: var(--vvu-blue);
        background: white;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--vvu-blue);
        border: none;
    }
</style>

<main class="main-content">
    <div class="page-header">
        <h2><i class="fas fa-list-ul me-3"></i>Campus Life Lists Manager</h2>
        <p class="opacity-75 mb-0">Manage principles, benefits, staff, and features across all campus life pages.</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="listTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-philosophy">
                <i class="fas fa-tshirt"></i> Philosophy
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-accommodation">
                <i class="fas fa-hotel"></i> Accommodation
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-food">
                <i class="fas fa-utensils"></i> Food Services
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-work">
                <i class="fas fa-briefcase"></i> Work Study
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sld">
                <i class="fas fa-church"></i> SLD
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-radio">
                <i class="fas fa-radio"></i> Radio
            </button>
        </li>
    </ul>

    <div class="tab-content pt-4" id="listTabContent">
        <!-- Philosophy Section -->
        <div class="tab-pane fade show active" id="tab-philosophy">
            <!-- Principles -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-gavel me-2"></i>Dress Principles</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPrincipleModal">
                        <i class="fas fa-plus me-1"></i>Add Principle
                    </button>
                </div>
                <?php foreach ($principles as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="philosophy_dress_principles">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Border Color</label>
                                <input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($item['border_color']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Benefits -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-star me-2"></i>Dress Benefits</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPhilBenefitModal">
                        <i class="fas fa-plus me-1"></i>Add Benefit
                    </button>
                </div>
                <?php foreach ($phil_benefits as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="philosophy_dress_benefits">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Grad Start</label>
                                <input type="text" name="gradient_start" class="form-control" value="<?php echo htmlspecialchars($item['gradient_start']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Grad End</label>
                                <input type="text" name="gradient_end" class="form-control" value="<?php echo htmlspecialchars($item['gradient_end']); ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-10">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Accommodation Section -->
        <div class="tab-pane fade" id="tab-accommodation">
            <!-- Features -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-check-circle me-2"></i>Housing Features</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAccFeatureModal">
                        <i class="fas fa-plus me-1"></i>Add Feature
                    </button>
                </div>
                <?php foreach ($acc_features as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="accommodation_features">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Halls -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-building me-2"></i>Residence Halls</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAccHallModal">
                        <i class="fas fa-plus me-1"></i>Add Hall Card
                    </button>
                </div>
                <?php foreach ($acc_halls as $item): ?>
                <div class="list-edit-item">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="accommodation_halls">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="male" <?php echo $item['type'] == 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo $item['type'] == 'female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Image Path / Upload</label>
                                <input type="text" name="image" class="form-control mb-1" value="<?php echo htmlspecialchars($item['image']); ?>">
                                <input type="file" name="image_upload" class="form-control form-control-sm" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Halls List (Comma separated)</label>
                                <input type="text" name="halls_list" class="form-control" value="<?php echo htmlspecialchars($item['halls_list']); ?>">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($item['description']); ?></textarea>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Grad Start</label>
                                <input type="text" name="gradient_start" class="form-control" value="<?php echo htmlspecialchars($item['gradient_start']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Grad End</label>
                                <input type="text" name="gradient_end" class="form-control" value="<?php echo htmlspecialchars($item['gradient_end']); ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Food Services Section -->
        <div class="tab-pane fade" id="tab-food">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-utensils me-2"></i>Food Features</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addFoodFeatureModal">
                        <i class="fas fa-plus me-1"></i>Add Feature
                    </button>
                </div>
                <?php foreach ($food_features as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="food_services_features">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Color (Tailwind)</label>
                                <input type="text" name="color" class="form-control" value="<?php echo htmlspecialchars($item['color']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Work Study Section -->
        <div class="tab-pane fade" id="tab-work">
            <!-- Benefits -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-smile me-2"></i>Work Benefits</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addWsBenefitModal">
                        <i class="fas fa-plus me-1"></i>Add Benefit
                    </button>
                </div>
                <?php foreach ($ws_benefits as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="work_study_benefits">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Color Class</label>
                                <input type="text" name="color" class="form-control" value="<?php echo htmlspecialchars($item['color']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($item['description']); ?></textarea>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Opportunities -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-briefcase me-2"></i>Job Opportunities</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addWsOppModal">
                        <i class="fas fa-plus me-1"></i>Add Opportunity
                    </button>
                </div>
                <?php foreach ($ws_opportunities as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="work_study_opportunities">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="Campus Industries" <?php echo $item['category'] == 'Campus Industries' ? 'selected' : ''; ?>>Campus Industries</option>
                                    <option value="Academic & Admin" <?php echo $item['category'] == 'Academic & Admin' ? 'selected' : ''; ?>>Academic & Admin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Opportunity Name</label>
                                <input type="text" name="opportunity_name" class="form-control" value="<?php echo htmlspecialchars($item['opportunity_name']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Steps -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-list-ol me-2"></i>Application Steps</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addWsStepModal">
                        <i class="fas fa-plus me-1"></i>Add Step
                    </button>
                </div>
                <?php foreach ($ws_steps as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="work_study_steps">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-1">
                                <label class="form-label">Step #</label>
                                <input type="number" name="step_number" class="form-control" value="<?php echo $item['step_number']; ?>">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Color Class</label>
                                <input type="text" name="color" class="form-control" value="<?php echo htmlspecialchars($item['color']); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($item['description']); ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- SLD Section -->
        <div class="tab-pane fade" id="tab-sld">
            <!-- Services -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-hands-holding me-2"></i>SLD Services</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSldServiceModal">
                        <i class="fas fa-plus me-1"></i>Add Service
                    </button>
                </div>
                <?php foreach ($sld_services as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="sld_services">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Color Class</label>
                                <input type="text" name="color" class="form-control" value="<?php echo htmlspecialchars($item['color']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($item['description']); ?></textarea>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Staff -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-users me-2"></i>SLD Staff</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSldStaffModal">
                        <i class="fas fa-plus me-1"></i>Add Staff Member
                    </button>
                </div>
                <?php foreach ($sld_staff as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="sld_staff">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($item['position']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Campus</label>
                                <input type="text" name="campus" class="form-control" value="<?php echo htmlspecialchars($item['campus']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Color Class</label>
                                <input type="text" name="icon_color" class="form-control" value="<?php echo htmlspecialchars($item['icon_color']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-8 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Locations -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-map-marker-alt me-2"></i>Campus Locations</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSldLocModal">
                        <i class="fas fa-plus me-1"></i>Add Location
                    </button>
                </div>
                <?php foreach ($sld_locations as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="sld_locations">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Radio Section -->
        <div class="tab-pane fade" id="tab-radio">
            <!-- Programs -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-microphone me-2"></i>Radio Programs</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRadioProgModal">
                        <i class="fas fa-plus me-1"></i>Add Program
                    </button>
                </div>
                <?php foreach ($radio_programs as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="radio_programs">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Schedule/Time</label>
                                <input type="text" name="schedule" class="form-control" value="<?php echo htmlspecialchars($item['schedule']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?php echo $item['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $item['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($item['description']); ?></textarea>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Icon (material symbol)</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Border Color</label>
                                <input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($item['border_color']); ?>">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Icon BG Color</label>
                                <input type="text" name="icon_bg_color" class="form-control" value="<?php echo htmlspecialchars($item['icon_bg_color']); ?>">
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Features -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-star me-2"></i>Station Features</h4>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRadioFeatModal">
                        <i class="fas fa-plus me-1"></i>Add Feature
                    </button>
                </div>
                <?php foreach ($radio_features as $item): ?>
                <div class="list-edit-item">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="table" value="radio_features">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Icon</label>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Color Class</label>
                                <input type="text" name="color_class" class="form-control" value="<?php echo htmlspecialchars($item['color_class']); ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Order</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $item['display_order']; ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Save</button>
                                <button type="submit" name="action" value="delete_item" class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modals for adding items -->
<div class="modal fade" id="addPrincipleModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="philosophy_dress_principles">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Dress Principle</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="checkroom"></div>
                    <div class="col-6 mb-3"><label class="form-label">Border Color</label><input type="text" name="border_color" class="form-control" value="purple-600"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                    <div class="col-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Principle</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addPhilBenefitModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="philosophy_dress_benefits">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Dress Benefit</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="star"></div>
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Grad Start</label><input type="text" name="gradient_start" class="form-control" value="from-blue-600"></div>
                    <div class="col-6 mb-3"><label class="form-label">Grad End</label><input type="text" name="gradient_end" class="form-control" value="to-indigo-700"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Benefit</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addAccFeatureModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="accommodation_features">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Housing Feature</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="home"></div>
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Feature</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addAccHallModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="accommodation_halls">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Residence Hall Card</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div class="col-md-8 mb-3"><label class="form-label">Card Title</label><input type="text" name="title" class="form-control" required></div>
                </div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2" required></textarea></div>
                <div class="mb-3"><label class="form-label">Halls List (Comma separated)</label><input type="text" name="halls_list" class="form-control" placeholder="Hall A, Hall B, Hall C" required></div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image Path / Upload</label>
                        <input type="text" name="image" class="form-control mb-1" value="images/accommodation_room.jpg">
                        <input type="file" name="image_upload" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="bed"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Grad Start</label><input type="text" name="gradient_start" class="form-control" value="from-blue-600/90"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Grad End</label><input type="text" name="gradient_end" class="form-control" value="to-indigo-600/90"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Hall Card</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addFoodFeatureModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="food_services_features">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Food Feature</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="restaurant"></div>
                    <div class="col-6 mb-3"><label class="form-label">Color (Tailwind)</label><input type="text" name="color" class="form-control" value="green"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Feature</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addWsBenefitModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="work_study_benefits">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add WS Benefit</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="work"></div>
                    <div class="col-6 mb-3"><label class="form-label">Color Class</label><input type="text" name="color" class="form-control" value="blue"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Benefit</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addWsOppModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="work_study_opportunities">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add WS Opportunity</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Category</label><select name="category" class="form-select"><option value="Campus Industries">Campus Industries</option><option value="Academic & Admin">Academic & Admin</option></select></div>
                <div class="mb-3"><label class="form-label">Opportunity Name</label><input type="text" name="opportunity_name" class="form-control" required></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                    <div class="col-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Opportunity</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addWsStepModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="work_study_steps">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Application Step</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-4 mb-3"><label class="form-label">Step #</label><input type="number" name="step_number" class="form-control" required></div>
                    <div class="col-8 mb-3"><label class="form-label">Step Title</label><input type="text" name="title" class="form-control" required></div>
                </div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                <div class="mb-3"><label class="form-label">Color Class</label><input type="text" name="color" class="form-control" value="blue"></div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Step</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addSldServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="sld_services">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add SLD Service</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="church"></div>
                    <div class="col-6 mb-3"><label class="form-label">Color Class</label><input type="text" name="color" class="form-control" value="blue"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Service</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addSldStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="sld_staff">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-user-plus me-2"></i>Add SLD Staff</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Position</label><input type="text" name="position" class="form-control" required></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Campus</label><input type="text" name="campus" class="form-control" placeholder="Main / Techiman"></div>
                    <div class="col-6 mb-3"><label class="form-label">Icon Color</label><input type="text" name="icon_color" class="form-control" value="blue"></div>
                </div>
                <div class="mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Staff</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addSldLocModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="sld_locations">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-map-marker-alt me-2"></i>Add Location</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Location Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2" required></textarea></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="location_on"></div>
                    <div class="col-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Location</button></div>
        </form>
    </div>
</div>


<div class="modal fade" id="addRadioProgModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="radio_programs">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Radio Program</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Program Title</label><input type="text" name="title" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Schedule/Time</label><input type="text" name="schedule" class="form-control" placeholder="Mon - Fri | 6:00 AM" required></div>
                </div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Icon (Material)</label><input type="text" name="icon" class="form-control" value="radio"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Border Color</label><input type="text" name="border_color" class="form-control" value="purple-600"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Icon BG Color</label><input type="text" name="icon_bg_color" class="form-control" value="purple-600"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Program</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="addRadioFeatModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="table" value="radio_features">
            <div class="modal-header bg-primary text-white"><h5><i class="fas fa-plus-circle me-2"></i>Add Station Feature</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label">Feature Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="school"></div>
                    <div class="col-6 mb-3"><label class="form-label">Color Class</label><input type="text" name="color_class" class="form-control" value="purple"></div>
                </div>
                <div class="mb-3"><label class="form-label">Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
            </div>
            <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary px-4 shadow-sm">Save Feature</button></div>
        </form>
    </div>
</div>

<!-- Extra modals would go here for each type. For now I'll include the main ones to be functional. -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-activate tab from URL hash
    document.addEventListener("DOMContentLoaded", function() {
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }
    });
</script>

<?php include 'footer.php'; ?>
