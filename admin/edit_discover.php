<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
require_once __DIR__ . '/../includes/admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'edit';
$id = $_GET['id'] ?? null;

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM homepage_discover_cards WHERE id = ?")->execute([$_GET['delete']]);
    header("Location: manage_homepage_content.php?tab=discover");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_url = $_POST['image_url'];
    
    // Handle file upload
    $uploaded_path = handleAdminFileUpload($_FILES['image_file'], 'discover');
    if ($uploaded_path) {
        $image_url = $uploaded_path;
    }

    $data = [$image_url, $_POST['title'], $_POST['link_url'], $_POST['display_order'], isset($_POST['is_active']) ? 1 : 0];
    if ($action === 'add') {
        $pdo->prepare("INSERT INTO homepage_discover_cards (image_url, title, link_url, display_order, is_active) VALUES (?, ?, ?, ?, ?)")->execute($data);
    } else {
        $pdo->prepare("UPDATE homepage_discover_cards SET image_url=?, title=?, link_url=?, display_order=?, is_active=? WHERE id=?")->execute([...$data, $id]);
    }
    header("Location: manage_homepage_content.php?tab=discover");
    exit();
}

$item = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM homepage_discover_cards WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
}

include 'header.php';
include 'sidebar.php';
?>
<main class="main-content">
    <h4><?= $action === 'add' ? 'Add' : 'Edit' ?> Discover Card</h4>
    <div class="row mt-4"><div class="col-lg-8"><div class="dashboard-card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3"><label>Title *</label><input type="text" name="title" class="form-control" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required></div>
            <div class="mb-3">
                <label>Image URL</label>
                <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($item['image_url'] ?? '') ?>" placeholder="Enter image URL">
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

            <div class="mb-3"><label>Link URL *</label><input type="text" name="link_url" class="form-control" value="<?= htmlspecialchars($item['link_url'] ?? '') ?>" required></div>
            <div class="mb-3"><label>Display Order *</label><input type="number" name="display_order" class="form-control" value="<?= $item['display_order'] ?? 1 ?>" required></div>
            <div class="mb-3"><input type="checkbox" name="is_active" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>> Active</div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            <a href="manage_homepage_content.php?tab=discover" class="btn btn-secondary">Cancel</a>
            <?php if ($id): ?><button type="button" class="btn btn-danger" onclick="if(confirm('Delete?')) location.href='?delete=<?= $id ?>'"><i class="fas fa-trash"></i> Delete</button><?php endif; ?>
        </form>
    </div></div></div></div>
</main></div>
<?php include 'footer.php'; ?>
