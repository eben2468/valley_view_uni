<?php
require_once('../includes/db_connect.php');
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/image_helper.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'edit'; $id = $_GET['id'] ?? null;
if (isset($_GET['delete'])) { $pdo->prepare("DELETE FROM homepage_gallery WHERE id = ?")->execute([$_GET['delete']]); header("Location: manage_homepage_content.php?tab=gallery"); exit(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A pasted Google Photos link is turned into its image URL; uploading a
    // file still works and wins over the link.
    $image_url = vvu_resolve_image_link($_POST['image_url'] ?? '', $link_error);
    $form_error = '';
    
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

    // Adding: extra Google Photos links (one per line) each become a photo
    // with the same caption, so a batch can be added in one go.
    $extra_links = [];
    if ($action === 'add') {
        $extra_links = array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/', (string) ($_POST['extra_links'] ?? ''))))));
        if (count($extra_links) > 5) {
            @set_time_limit(30 + 15 * count($extra_links));
        }
    }

    if ($image_url === null) {
        $form_error = 'Image link: ' . $link_error;
    } elseif ($image_url === '' && !$extra_links) {
        $form_error = 'Add an image: paste a Google Photos link or upload a file.';
    }

    if ($form_error === '') {
        $order = (int) $_POST['display_order'];
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($action === 'add') {
            $insert = $pdo->prepare("INSERT INTO homepage_gallery (image_url, caption, display_order, is_active) VALUES (?,?,?,?)");
            if ($image_url !== '') {
                $insert->execute([$image_url, $_POST['caption'], $order++, $active]);
            }
            $bad = [];
            foreach ($extra_links as $n => $link) {
                $resolved = vvu_resolve_image_link($link, $link_error);
                if ($resolved === null || $resolved === '') {
                    $bad[] = 'Link ' . ($n + 1) . ': ' . $link_error;
                    continue;
                }
                $insert->execute([$resolved, $_POST['caption'], $order++, $active]);
            }
            if ($bad) {
                $form_error = count($bad) . ' link(s) could not be added (the others were saved). ' . implode(' ', array_slice($bad, 0, 5));
            }
        } else {
            $pdo->prepare("UPDATE homepage_gallery SET image_url=?, caption=?, display_order=?, is_active=? WHERE id=?")
                ->execute([$image_url, $_POST['caption'], $order, $active, $id]);
        }
        if ($form_error === '') {
            header("Location: manage_homepage_content.php?tab=gallery"); exit();
        }
    }
}
$item = null; if ($id) { $stmt = $pdo->prepare("SELECT * FROM homepage_gallery WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch(); }

include 'header.php'; include 'sidebar.php';
?>
<main class="main-content"><h4><?= $action === 'add' ? 'Add' : 'Edit' ?> Gallery Image</h4>
<div class="row mt-4"><div class="col-lg-8"><div class="dashboard-card"><div class="card-body">
<?php if (!empty($form_error)): ?>
<div class="alert alert-danger"><i class="fas fa-triangle-exclamation me-1"></i><?= htmlspecialchars($form_error) ?></div>
<?php endif; ?>
<form method="POST" enctype="multipart/form-data">
<div class="mb-3">
    <label>Image — Google Photos link or image URL</label>
    <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($_POST['image_url'] ?? ($item['image_url'] ?? '')) ?>" placeholder="https://photos.app.goo.gl/…  or  https://lh3.googleusercontent.com/…">
    <?php include __DIR__ . '/includes/google_photos_help.php'; ?>
</div>
<?php if ($action === 'add'): ?>
<div class="mb-3">
    <label>Adding several photos? Paste more Google Photos links, one per line (optional)</label>
    <textarea name="extra_links" class="form-control" rows="4" placeholder="https://photos.app.goo.gl/AbC123…&#10;https://photos.app.goo.gl/XyZ789…"><?= htmlspecialchars($_POST['extra_links'] ?? '') ?></textarea>
    <small class="text-muted">Each link becomes its own photo in the homepage gallery, with the caption, order and status below.</small>
</div>
<?php endif; ?>
<div class="mb-3">
    <label>Or Upload Image</label>
    <input type="file" name="image_file" class="form-control" accept="image/*" data-crop="1/1">
    <small class="text-muted">Uploading a file will override the link above.</small>
</div>

<?php if ($item && $item['image_url']): 
    $preview_url = vvu_admin_img_preview($item['image_url']);
?>
<div class="mb-3">
    <label>Current Image</label><br>
    <img src="<?= htmlspecialchars($preview_url) ?>" alt="Preview" style="max-width: 200px; max-height: 150px;" class="img-thumbnail" onerror="this.style.display='none'">
</div>
<?php endif; ?>

<div class="mb-3"><label>Caption *</label><input type="text" name="caption" class="form-control" value="<?= htmlspecialchars($_POST['caption'] ?? ($item['caption'] ?? '')) ?>" required></div>
<div class="mb-3"><label>Display Order</label><input type="number" name="display_order" class="form-control" value="<?= $item['display_order'] ?? 1 ?>" required></div>
<div class="mb-3"><input type="checkbox" name="is_active" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>> Active</div>
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
<a href="manage_homepage_content.php?tab=gallery" class="btn btn-secondary">Cancel</a>
<?php if ($id): ?><button type="button" class="btn btn-danger" onclick="if(confirm('Delete?')) location.href='?delete=<?= $id ?>'"><i class="fas fa-trash"></i> Delete</button><?php endif; ?>
</form></div></div></div></div></main></div>
<?php include 'footer.php'; ?>
