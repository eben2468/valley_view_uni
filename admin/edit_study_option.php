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
        $stmt = $pdo->prepare("DELETE FROM homepage_study_options WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: manage_homepage_content.php?tab=study");
        exit();
    } catch (Exception $e) {
        $error = "Error deleting: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $btn1_text = $_POST['btn1_text'];
        $btn1_link = $_POST['btn1_link'];
        $btn1_style = $_POST['btn1_style'];
        $btn2_text = $_POST['btn2_text'];
        $btn2_link = $_POST['btn2_link'];
        $btn2_style = $_POST['btn2_style'];
        $accent_color = $_POST['accent_color'];
        $display_order = $_POST['display_order'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if ($action === 'add') {
            $sql = "INSERT INTO homepage_study_options 
                    (title, description, btn1_text, btn1_link, btn1_style, btn2_text, btn2_link, btn2_style, accent_color, display_order, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $btn1_text, $btn1_link, $btn1_style, $btn2_text, $btn2_link, $btn2_style, $accent_color, $display_order, $is_active]);
        } else {
            $sql = "UPDATE homepage_study_options SET 
                    title=?, description=?, btn1_text=?, btn1_link=?, btn1_style=?, btn2_text=?, btn2_link=?, btn2_style=?, accent_color=?, display_order=?, is_active=? 
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $btn1_text, $btn1_link, $btn1_style, $btn2_text, $btn2_link, $btn2_style, $accent_color, $display_order, $is_active, $id]);
        }
        
        header("Location: manage_homepage_content.php?tab=study");
        exit();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$option = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM homepage_study_options WHERE id = ?");
    $stmt->execute([$id]);
    $option = $stmt->fetch();
}

include 'header.php';
include 'sidebar.php';
?>

        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Study Option Card</h4>
                <a href="manage_homepage_content.php?tab=study" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">Title *</label>
                                        <input type="text" name="title" class="form-control" 
                                               value="<?php echo htmlspecialchars($option['title'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Accent Color</label>
                                        <input type="color" name="accent_color" class="form-control form-control-color w-100" 
                                               value="<?php echo htmlspecialchars($option['accent_color'] ?? '#006B3F'); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($option['description'] ?? ''); ?></textarea>
                                </div>
                                
                                <hr class="my-4">
                                <h6>Button 1 (Outline)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Button 1 Text</label>
                                        <input type="text" name="btn1_text" class="form-control" 
                                               value="<?php echo htmlspecialchars($option['btn1_text'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Button 1 Link</label>
                                        <input type="text" name="btn1_link" class="form-control" 
                                               value="<?php echo htmlspecialchars($option['btn1_link'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Button 1 Style</label>
                                    <select name="btn1_style" class="form-select">
                                        <option value="outline" <?php echo ($option['btn1_style'] ?? 'outline') == 'outline' ? 'selected' : ''; ?>>Outline</option>
                                        <option value="filled" <?php echo ($option['btn1_style'] ?? 'outline') == 'filled' ? 'selected' : ''; ?>>Filled</option>
                                    </select>
                                </div>

                                <hr class="my-4">
                                <h6>Button 2 (Filled)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Button 2 Text</label>
                                        <input type="text" name="btn2_text" class="form-control" 
                                               value="<?php echo htmlspecialchars($option['btn2_text'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Button 2 Link</label>
                                        <input type="text" name="btn2_link" class="form-control" 
                                               value="<?php echo htmlspecialchars($option['btn2_link'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Button 2 Style</label>
                                    <select name="btn2_style" class="form-select">
                                        <option value="filled" <?php echo ($option['btn2_style'] ?? 'filled') == 'filled' ? 'selected' : ''; ?>>Filled</option>
                                        <option value="outline" <?php echo ($option['btn2_style'] ?? 'filled') == 'outline' ? 'selected' : ''; ?>>Outline</option>
                                    </select>
                                </div>
                                
                                <hr class="my-4">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Display Order</label>
                                        <input type="number" name="display_order" class="form-control" 
                                               value="<?php echo htmlspecialchars($option['display_order'] ?? '0'); ?>" required>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center pt-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" 
                                                   <?php echo ($option['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Card Visible on Homepage</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save"></i> <?php echo $action === 'add' ? 'Add Card' : 'Save Changes'; ?>
                                    </button>
                                    <?php if ($action === 'edit' && $id): ?>
                                    <button type="button" class="btn btn-outline-danger ms-auto" 
                                            onclick="if(confirm('Delete this study option?')) window.location.href='edit_study_option.php?delete=<?php echo $id; ?>'">
                                        <i class="fas fa-trash"></i> Delete Card
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
