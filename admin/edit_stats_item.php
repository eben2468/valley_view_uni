<?php
require_once('../includes/db_connect.php');
require_once __DIR__ . '/../includes/admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'edit';
$id = $_GET['id'] ?? null;

if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM homepage_stats_items WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: manage_homepage_content.php?tab=stats");
        exit();
    } catch (Exception $e) {
        $error = "Error deleting: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $label = $_POST['label'];
        $value = $_POST['value'];
        $display_order = $_POST['display_order'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO homepage_stats_items (label, value, display_order, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$label, $value, $display_order, $is_active]);
        } else {
            $stmt = $pdo->prepare("UPDATE homepage_stats_items SET label=?, value=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$label, $value, $display_order, $is_active, $id]);
        }
        
        header("Location: manage_homepage_content.php?tab=stats");
        exit();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$item = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM homepage_stats_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
}

include 'header.php';
include 'sidebar.php';
?>

        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Stats Item</h4>
                <a href="manage_homepage_content.php?tab=stats" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Stat Label *</label>
                                    <input type="text" name="label" class="form-control" 
                                           value="<?php echo htmlspecialchars($item['label'] ?? ''); ?>" placeholder="e.g. Graduates" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Stat Value *</label>
                                    <input type="text" name="value" class="form-control" 
                                           value="<?php echo htmlspecialchars($item['value'] ?? ''); ?>" placeholder="e.g. 14098" required>
                                    <small class="text-muted">The numeric value to be displayed (animated).</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="display_order" class="form-control" 
                                           value="<?php echo htmlspecialchars($item['display_order'] ?? '0'); ?>" required>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               <?php echo ($item['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?php echo $action === 'add' ? 'Add Item' : 'Save Changes'; ?>
                                    </button>
                                    <?php if ($action === 'edit' && $id): ?>
                                    <button type="button" class="btn btn-danger ms-auto" 
                                            onclick="if(confirm('Delete this stats item?')) window.location.href='edit_stats_item.php?delete=<?php echo $id; ?>'">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

<?php include 'footer.php'; ?>
