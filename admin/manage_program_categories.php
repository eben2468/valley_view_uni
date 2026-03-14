<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');

$message = '';

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM program_categories WHERE id = ?");
    $stmt->execute([$delete_id]);
    $message = '<div class="alert alert-success">Category deleted successfully!</div>';
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $icon = $_POST['icon'];
    $color_1 = $_POST['color_1'];
    $color_2 = $_POST['color_2'];
    $display_order = (int)$_POST['display_order'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE program_categories SET name = ?, icon = ?, color_1 = ?, color_2 = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$name, $icon, $color_1, $color_2, $display_order, $id]);
        $message = '<div class="alert alert-success">Category updated successfully!</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO program_categories (name, icon, color_1, color_2, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $icon, $color_1, $color_2, $display_order]);
        $message = '<div class="alert alert-success">Category added successfully!</div>';
    }
}

// Fetch all categories
$categories = $pdo->query("SELECT pc.*, (SELECT COUNT(*) FROM academic_programs ap WHERE ap.category_id = pc.id) as program_count FROM program_categories pc ORDER BY display_order ASC")->fetchAll();

// Get specific category for editing if requested
$edit_cat = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM program_categories WHERE id = ?");
    $stmt->execute([(int)$_GET['edit_id']]);
    $edit_cat = $stmt->fetch();
}
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Program Categories</h4>
            <p class="text-muted mb-0">Manage faculties, schools, and departments</p>
        </div>
        <a href="manage_programs.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Programs
        </a>
    </div>

    <?php echo $message; ?>

    <div class="row g-4">
        <!-- Category Form -->
        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $edit_cat ? 'Edit' : 'Add New'; ?> Category</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit_cat): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_cat['name'] ?? ''); ?>" required placeholder="e.g. School of Business">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Material Icon Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($edit_cat['icon'] ?? 'school'); ?>" placeholder="e.g. business_center">
                            </div>
                            <small class="text-muted">Use <a href="https://fonts.google.com/icons" target="_blank">Google Material Icons</a></small>
                        </div>
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Color 1 (Start)</label>
                                <input type="color" name="color_1" class="form-control form-control-color w-100" value="<?php echo htmlspecialchars($edit_cat['color_1'] ?? '#3b82f6'); ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Color 2 (End)</label>
                                <input type="color" name="color_2" class="form-control form-control-color w-100" value="<?php echo htmlspecialchars($edit_cat['color_2'] ?? '#2563eb'); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo $edit_cat['display_order'] ?? 0; ?>">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> <?php echo $edit_cat ? 'Update' : 'Save'; ?> Category
                            </button>
                            <?php if ($edit_cat): ?>
                                <a href="manage_program_categories.php" class="btn btn-light">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category List -->
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Existing Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Icon</th>
                                    <th>Name</th>
                                    <th>Programs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo $cat['display_order']; ?></td>
                                    <td>
                                        <div class="cat-preview" style="background: linear-gradient(135deg, <?php echo $cat['color_1']; ?>, <?php echo $cat['color_2']; ?>);">
                                            <span class="material-symbols-outlined text-white" style="font-size: 20px;"><?php echo $cat['icon']; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold"><?php echo htmlspecialchars($cat['name']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill"><?php echo $cat['program_count']; ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="?edit_id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-icon btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-icon btn-danger" onclick="if(confirm('Delete this category? Associated programs will become uncategorized.')) window.location.href='?delete_id=<?php echo $cat['id']; ?>'">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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
</main>

<!-- Load Material Icons for preview -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

<style>
.cat-preview {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-icon { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
.form-control-color { height: 45px; }
</style>

<?php include 'footer.php'; ?>
