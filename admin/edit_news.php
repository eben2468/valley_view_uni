<?php
require_once('../includes/db_connect.php');
require_once __DIR__ . '/../includes/admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'edit'; $id = $_GET['id'] ?? null;
if (isset($_GET['delete'])) { $pdo->prepare("DELETE FROM homepage_news WHERE id = ?")->execute([$_GET['delete']]); header("Location: manage_homepage_content.php?tab=news"); exit(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [$_POST['title'], $_POST['description'], $_POST['category'], $_POST['event_date'], $_POST['link_url'], $_POST['display_order'], isset($_POST['is_active']) ? 1 : 0];
    if ($action === 'add') $pdo->prepare("INSERT INTO homepage_news (title, description, category, event_date, link_url, display_order, is_active) VALUES (?,?,?,?,?,?,?)")->execute($data);
    else $pdo->prepare("UPDATE homepage_news SET title=?, description=?, category=?, event_date=?, link_url=?, display_order=?, is_active=? WHERE id=?")->execute([...$data, $id]);
    header("Location: manage_homepage_content.php?tab=news"); exit();
}
$item = null; if ($id) { $stmt = $pdo->prepare("SELECT * FROM homepage_news WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch(); }

include 'header.php'; include 'sidebar.php';
?>
<main class="main-content"><h4><?= $action === 'add' ? 'Add' : 'Edit' ?> News/Event</h4>
<div class="row mt-4"><div class="col-lg-8"><div class="dashboard-card"><div class="card-body"><form method="POST">
<div class="mb-3"><label>Title *</label><input type="text" name="title" class="form-control" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required></div>
<div class="mb-3"><label>Description *</label><textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($item['description'] ?? '') ?></textarea></div>
<div class="row"><div class="col-md-6 mb-3"><label>Category *</label><input type="text" name="category" class="form-control" value="<?= htmlspecialchars($item['category'] ?? '') ?>" required></div>
<div class="col-md-6 mb-3"><label>Event Date *</label><input type="date" name="event_date" class="form-control" value="<?= $item['event_date'] ?? date('Y-m-d') ?>" required></div></div>
<div class="mb-3"><label>Link URL *</label><input type="text" name="link_url" class="form-control" value="<?= htmlspecialchars($item['link_url'] ?? '') ?>" required></div>
<div class="mb-3"><label>Display Order</label><input type="number" name="display_order" class="form-control" value="<?= $item['display_order'] ?? 1 ?>" required></div>
<div class="mb-3"><input type="checkbox" name="is_active" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>> Active</div>
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
<a href="manage_homepage_content.php?tab=news" class="btn btn-secondary">Cancel</a>
<?php if ($id): ?><button type="button" class="btn btn-danger" onclick="if(confirm('Delete?')) location.href='?delete=<?= $id ?>'"><i class="fas fa-trash"></i> Delete</button><?php endif; ?>
</form></div></div></div></div></main></div>
<?php include 'footer.php'; ?>
