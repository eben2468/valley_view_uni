<?php
require_once('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'edit';
$id = $_GET['id'] ?? null;

if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM homepage_sections WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: manage_homepage_content.php?tab=sections");
        exit();
    } catch (Exception $e) {
        $error = "Error deleting: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $section_key = $_POST['section_key'];
        $section_title = $_POST['section_title'];
        $section_subtitle = $_POST['section_subtitle'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO homepage_sections (section_key, section_title, section_subtitle, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$section_key, $section_title, $section_subtitle, $is_active]);
        } else {
            $stmt = $pdo->prepare("UPDATE homepage_sections SET section_key=?, section_title=?, section_subtitle=?, is_active=? WHERE id=?");
            $stmt->execute([$section_key, $section_title, $section_subtitle, $is_active, $id]);
        }
        
        header("Location: manage_homepage_content.php?tab=sections");
        exit();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$section = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM homepage_sections WHERE id = ?");
    $stmt->execute([$id]);
    $section = $stmt->fetch();
}

include 'header.php';
include 'sidebar.php';
?>

        <main class="main-content">
            <h4><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Section</h4>
            
            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Section Key *</label>
                                    <input type="text" name="section_key" class="form-control" 
                                           value="<?php echo htmlspecialchars($section['section_key'] ?? ''); ?>" required>
                                    <small class="text-muted">e.g., discover_more, popular_programs</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Section Title *</label>
                                    <input type="text" name="section_title" class="form-control" 
                                           value="<?php echo htmlspecialchars($section['section_title'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Section Subtitle *</label>
                                    <textarea name="section_subtitle" class="form-control" rows="3" required><?php echo htmlspecialchars($section['section_subtitle'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               <?php echo ($section['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                    <a href="manage_homepage_content.php?tab=sections" class="btn btn-secondary">Cancel</a>
                                    <?php if ($action === 'edit' && $id): ?>
                                    <button type="button" class="btn btn-danger ms-auto" 
                                            onclick="if(confirm('Delete this section?')) window.location.href='edit_section.php?delete=<?php echo $id; ?>'">
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
