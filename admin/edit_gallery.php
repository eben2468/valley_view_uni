<?php
require_once('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'edit'; $id = $_GET['id'] ?? null;
if (isset($_GET['delete'])) { $pdo->prepare("DELETE FROM homepage_gallery WHERE id = ?")->execute([$_GET['delete']]); header("Location: manage_homepage_content.php?tab=gallery"); exit(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_url = $_POST['image_url'];
    
    // Handle file upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/gallery/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_extensions)) {
            $new_filename = 'gallery_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/gallery/' . $new_filename;
            }
        }
    }

    $data = [$image_url, $_POST['caption'], $_POST['display_order'], isset($_POST['is_active']) ? 1 : 0];
    if ($action === 'add') $pdo->prepare("INSERT INTO homepage_gallery (image_url, caption, display_order, is_active) VALUES (?,?,?,?)")->execute($data);
    else $pdo->prepare("UPDATE homepage_gallery SET image_url=?, caption=?, display_order=?, is_active=? WHERE id=?")->execute([...$data, $id]);
    header("Location: manage_homepage_content.php?tab=gallery"); exit();
}
$item = null; if ($id) { $stmt = $pdo->prepare("SELECT * FROM homepage_gallery WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch(); }

include 'header.php'; include 'sidebar.php';
?>
<main class="main-content"><h4><?= $action === 'add' ? 'Add' : 'Edit' ?> Gallery Image</h4>
<div class="row mt-4"><div class="col-lg-8"><div class="dashboard-card"><div class="card-body">
<form method="POST" enctype="multipart/form-data">
<div class="mb-3">
    <label>Image URL</label>
    <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($item['image_url'] ?? '') ?>" placeholder="Enter external image URL">
</div>
<div class="mb-3">
    <label>Or Upload Image</label>
    <input type="file" name="image_file" class="form-control" accept="image/*">
    <small class="text-muted">Uploading a file will override the URL above.</small>
</div>

<?php if ($item && $item['image_url']): 
    $preview_url = $item['image_url'];
    if (!filter_var($preview_url, FILTER_VALIDATE_URL) && !empty($preview_url)) {
        $preview_url = '../' . $preview_url;
    }
?>
<div class="mb-3">
    <label>Current Image</label><br>
    <img src="<?= htmlspecialchars($preview_url) ?>" alt="Preview" style="max-width: 200px; max-height: 150px;" class="img-thumbnail" onerror="this.style.display='none'">
</div>
<?php endif; ?>

<div class="mb-3"><label>Caption *</label><input type="text" name="caption" class="form-control" value="<?= htmlspecialchars($item['caption'] ?? '') ?>" required></div>
<div class="mb-3"><label>Display Order</label><input type="number" name="display_order" class="form-control" value="<?= $item['display_order'] ?? 1 ?>" required></div>
<div class="mb-3"><input type="checkbox" name="is_active" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>> Active</div>
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
<a href="manage_homepage_content.php?tab=gallery" class="btn btn-secondary">Cancel</a>
<?php if ($id): ?><button type="button" class="btn btn-danger" onclick="if(confirm('Delete?')) location.href='?delete=<?= $id ?>'"><i class="fas fa-trash"></i> Delete</button><?php endif; ?>
</form></div></div></div></div></main></div>
<?php include 'footer.php'; ?>
