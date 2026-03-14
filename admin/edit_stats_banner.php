<?php
require_once('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $banner_text = $_POST['banner_text'];
        $bg_image = $_POST['bg_image'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle file upload if provided
        if (isset($_FILES['bg_image_file']) && $_FILES['bg_image_file']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../uploads/homepage/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            
            $file_ext = pathinfo($_FILES['bg_image_file']['name'], PATHINFO_EXTENSION);
            $file_name = "stats_bg_" . time() . "." . $file_ext;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES['bg_image_file']['tmp_name'], $target_file)) {
                $bg_image = "uploads/homepage/" . $file_name;
            }
        }

        $stmt = $pdo->prepare("UPDATE homepage_stats_banner SET banner_text=?, bg_image=?, is_active=? WHERE id=1");
        $stmt->execute([$banner_text, $bg_image, $is_active]);
        
        $success_msg = "Stats banner updated successfully!";
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM homepage_stats_banner WHERE id = 1");
$banner = $stmt->fetch();

include 'header.php';
include 'sidebar.php';
?>

        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Manage Stats Banner</h4>
                <a href="manage_homepage_content.php?tab=stats" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-body">
                            <?php if ($success_msg): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $success_msg; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error_msg): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error_msg; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Banner Text *</label>
                                    <textarea name="banner_text" class="form-control" rows="3" required><?php echo htmlspecialchars($banner['banner_text'] ?? ''); ?></textarea>
                                    <small class="text-muted">This text appears at the top of the stats section.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Background Image URL</label>
                                    <input type="text" name="bg_image" id="bg_image" class="form-control" 
                                           value="<?php echo htmlspecialchars($banner['bg_image'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Or Upload New Background</label>
                                    <input type="file" name="bg_image_file" class="form-control" accept="image/*">
                                    <?php if (!empty($banner['bg_image'])): ?>
                                        <div class="mt-2">
                                            <small>Current Image:</small><br>
                                            <img src="../<?php echo htmlspecialchars($banner['bg_image']); ?>" style="max-height: 150px; border-radius: 8px; margin-top: 5px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               <?php echo ($banner['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Section Visible on Homepage</label>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Update Banner Content
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="dashboard-card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Help & Tips</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted">
                                <strong>Text Tip:</strong> Keep the banner text concise and inspiring to maintain the modern aesthetic.
                            </p>
                            <p class="small text-muted">
                                <strong>Image Tip:</strong> Use a dark or high-contrast image (at least 1920x600px) for the best background effect. The system will automatically apply an overlay.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

<?php include 'footer.php'; ?>
