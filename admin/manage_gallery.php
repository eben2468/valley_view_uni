<?php
/**
 * Photo Gallery CMS — page copy, categories, albums and stat tiles.
 *
 * The photos themselves live one level down, in edit_gallery_album.php, so an
 * album with 70 images does not have to render inside this list.
 */
require_once(__DIR__ . '/../includes/db_connect.php');
require_once(__DIR__ . '/../includes/admin_auth.php');
require_once(__DIR__ . '/../includes/upload_helper.php');

$success = '';
$error   = '';

/**
 * Which gallery page is being edited. One set of tables serves several
 * gallery pages, each identified by this key.
 */
$GALLERIES = [
    'main' => ['label' => 'Main Photo Gallery', 'page' => 'gallery.php',     'icon' => 'fa-images'],
    'src'  => ['label' => 'SRC Gallery',        'page' => 'src_gallery.php', 'icon' => 'fa-user-group'],
];

$gallery_key = $_POST['gallery_key'] ?? $_GET['gallery'] ?? 'main';
if (!isset($GALLERIES[$gallery_key])) {
    $gallery_key = 'main';
}
$gallery_meta = $GALLERIES[$gallery_key];

/** Builds a URL-safe slug, unique within one gallery's rows in a table. */
function gallery_unique_slug(PDO $pdo, $table, $text, $galleryKey, $ignoreId = null)
{
    $slug = strtolower(trim((string) $text));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim((string) $slug, '-');
    if ($slug === '') {
        $slug = 'item';
    }
    $slug = substr($slug, 0, 200);

    $base = $slug;
    $n    = 2;
    while (true) {
        $sql  = "SELECT COUNT(*) FROM `$table` WHERE slug = ? AND gallery_key = ?" . ($ignoreId ? " AND id <> ?" : "");
        $args = $ignoreId ? [$slug, $galleryKey, $ignoreId] : [$slug, $galleryKey];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        if (!$stmt->fetchColumn()) {
            return $slug;
        }
        $slug = $base . '-' . $n++;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    vvu_require_csrf();
    $action = $_POST['action'] ?? '';

    try {
        // ------------------------------------------------ page copy
        if ($action === 'save_content') {
            $fields = [
                'intro_heading', 'intro_text',
                'albums_heading', 'albums_subheading', 'all_filter_label',
                'search_placeholder', 'empty_state_text',
                'stats_heading', 'stats_text',
                'cta_heading', 'cta_text',
                'cta_1_text', 'cta_1_link', 'cta_1_icon',
                'cta_2_text', 'cta_2_link', 'cta_2_icon',
                'detail_back_label', 'detail_photos_label',
                'status',
            ];
            $set    = implode(', ', array_map(function ($f) { return "`$f` = ?"; }, $fields));
            $values = array_map(function ($f) { return $_POST[$f] ?? ''; }, $fields);
            $values[] = $gallery_key;

            $pdo->prepare("UPDATE gallery_page_content SET $set WHERE page_key = ?")->execute($values);
            $success = 'Gallery page content saved.';
        }

        // ------------------------------------------------ categories
        elseif ($action === 'save_category') {
            $id   = $_POST['id'] ?? '';
            $name = trim((string) ($_POST['name'] ?? ''));
            if ($name === '') {
                throw new RuntimeException('A category needs a name.');
            }
            $icon  = trim((string) ($_POST['icon'] ?? '')) ?: 'photo_library';
            $order = (int) ($_POST['display_order'] ?? 0);
            $stat  = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

            if ($id) {
                $slug = gallery_unique_slug($pdo, 'gallery_categories', $_POST['slug'] ?: $name, $gallery_key, (int) $id);
                $pdo->prepare("UPDATE gallery_categories SET name=?, slug=?, icon=?, display_order=?, status=? WHERE id=? AND gallery_key=?")
                    ->execute([$name, $slug, $icon, $order, $stat, $id, $gallery_key]);
                $success = 'Category updated.';
            } else {
                $slug = gallery_unique_slug($pdo, 'gallery_categories', $name, $gallery_key);
                $pdo->prepare("INSERT INTO gallery_categories (gallery_key, name, slug, icon, display_order, status) VALUES (?,?,?,?,?,?)")
                    ->execute([$gallery_key, $name, $slug, $icon, $order, $stat]);
                $success = 'Category added.';
            }
        }
        elseif ($action === 'delete_category') {
            // Albums keep existing — they just lose their category badge.
            $pdo->prepare("UPDATE gallery_albums SET category_id = NULL WHERE category_id = ? AND gallery_key = ?")
                ->execute([$_POST['id'], $gallery_key]);
            $pdo->prepare("DELETE FROM gallery_categories WHERE id = ? AND gallery_key = ?")
                ->execute([$_POST['id'], $gallery_key]);
            $success = 'Category deleted. Its albums were kept and are now uncategorised.';
        }

        // ------------------------------------------------ albums
        elseif ($action === 'add_album') {
            $title = trim((string) ($_POST['title'] ?? ''));
            if ($title === '') {
                throw new RuntimeException('An album needs a title.');
            }
            $slug  = gallery_unique_slug($pdo, 'gallery_albums', $title, $gallery_key);
            $cover = trim((string) ($_POST['cover_image'] ?? ''));
            if (!empty($_FILES['cover_upload']['name'])) {
                $uploaded = handleAdminFileUpload($_FILES['cover_upload'], 'gallery/covers', 'cover_');
                if ($uploaded) {
                    $cover = $uploaded;
                }
            }
            $catId = $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
            $date  = trim((string) ($_POST['event_date'] ?? '')) ?: null;

            $stmt = $pdo->prepare("SELECT COALESCE(MAX(display_order), 0) FROM gallery_albums WHERE gallery_key = ?");
            $stmt->execute([$gallery_key]);
            $maxOrder = (int) $stmt->fetchColumn();

            $pdo->prepare(
                "INSERT INTO gallery_albums (gallery_key, title, slug, description, event_date, cover_image, category_id, is_featured, display_order, status)
                 VALUES (?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $gallery_key, $title, $slug, $_POST['description'] ?? '', $date, $cover, $catId,
                isset($_POST['is_featured']) ? 1 : 0, $maxOrder + 1,
                ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            ]);

            header('Location: edit_gallery_album.php?id=' . $pdo->lastInsertId() . '&created=1');
            exit;
        }
        elseif ($action === 'delete_album') {
            // gallery_album_images has ON DELETE CASCADE, so the photo rows go
            // with it. The uploaded files are deliberately left on disk.
            $pdo->prepare("DELETE FROM gallery_albums WHERE id = ? AND gallery_key = ?")
                ->execute([$_POST['id'], $gallery_key]);
            $success = 'Album deleted.';
        }
        elseif ($action === 'toggle_album') {
            $pdo->prepare("UPDATE gallery_albums SET status = IF(status='active','inactive','active') WHERE id = ? AND gallery_key = ?")
                ->execute([$_POST['id'], $gallery_key]);
            $success = 'Album visibility updated.';
        }
        elseif ($action === 'reorder_albums') {
            $orders = $_POST['order'] ?? [];
            $stmt   = $pdo->prepare("UPDATE gallery_albums SET display_order = ? WHERE id = ? AND gallery_key = ?");
            foreach ($orders as $id => $order) {
                $stmt->execute([(int) $order, (int) $id, $gallery_key]);
            }
            $success = 'Album order saved.';
        }

        // ------------------------------------------------ stats
        elseif ($action === 'save_stat') {
            $id     = $_POST['id'] ?? '';
            $label  = trim((string) ($_POST['label'] ?? ''));
            if ($label === '') {
                throw new RuntimeException('A stat needs a label.');
            }
            $icon   = trim((string) ($_POST['icon'] ?? '')) ?: 'photo_library';
            $value  = trim((string) ($_POST['value_text'] ?? ''));
            $auto   = in_array($_POST['auto_source'] ?? 'none', ['none', 'photos', 'albums'], true)
                    ? $_POST['auto_source'] : 'none';
            $order  = (int) ($_POST['display_order'] ?? 0);
            $stat   = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

            if ($id) {
                $pdo->prepare("UPDATE gallery_stats SET icon=?, value_text=?, label=?, auto_source=?, display_order=?, status=? WHERE id=? AND gallery_key=?")
                    ->execute([$icon, $value, $label, $auto, $order, $stat, $id, $gallery_key]);
                $success = 'Stat updated.';
            } else {
                $pdo->prepare("INSERT INTO gallery_stats (gallery_key, icon, value_text, label, auto_source, display_order, status) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$gallery_key, $icon, $value, $label, $auto, $order, $stat]);
                $success = 'Stat added.';
            }
        }
        elseif ($action === 'delete_stat') {
            $pdo->prepare("DELETE FROM gallery_stats WHERE id = ? AND gallery_key = ?")
                ->execute([$_POST['id'], $gallery_key]);
            $success = 'Stat deleted.';
        }
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log('Gallery admin save failed: ' . $e->getMessage());
        $error = 'Save failed. If this keeps happening, run sql/gallery_page_schema.sql on this server and try again.';
    }
}

// ---------------------------------------------------------------- load
$schema_ready = true;
try {
    $get = function ($sql) use ($pdo, $gallery_key) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$gallery_key]);
        return $stmt;
    };

    $content    = $get("SELECT * FROM gallery_page_content WHERE page_key = ?")->fetch(PDO::FETCH_ASSOC) ?: [];
    $categories = $get("SELECT * FROM gallery_categories WHERE gallery_key = ? ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $stats      = $get("SELECT * FROM gallery_stats WHERE gallery_key = ? ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $albums     = $get(
        "SELECT a.*, c.name AS category_name,
                (SELECT COUNT(*) FROM gallery_album_images i WHERE i.album_id = a.id) AS photo_count
           FROM gallery_albums a
           LEFT JOIN gallery_categories c ON c.id = a.category_id
          WHERE a.gallery_key = ?
       ORDER BY a.display_order ASC, a.id ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
    $total_photos = (int) $get(
        "SELECT COUNT(*) FROM gallery_album_images i
           JOIN gallery_albums a ON a.id = i.album_id
          WHERE a.gallery_key = ?"
    )->fetchColumn();
} catch (Throwable $e) {
    $schema_ready = false;
    $content = []; $categories = []; $stats = []; $albums = []; $total_photos = 0;
}

$tab = $_GET['tab'] ?? 'albums';

/** CSRF token plus the gallery being edited — every form needs both. */
function gallery_form_fields($galleryKey)
{
    return vvu_csrf_field()
        . '<input type="hidden" name="gallery_key" value="' . htmlspecialchars($galleryKey, ENT_QUOTES) . '">';
}

/** Resolves a stored path for an <img> preview inside admin/. */
function gallery_admin_preview($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }
    return preg_match('#^(https?:)?//#i', $path) ? $path : '../' . ltrim($path, '/');
}

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1><i class="fas <?php echo htmlspecialchars($gallery_meta['icon']); ?>"></i> <?php echo htmlspecialchars($gallery_meta['label']); ?></h1>
            <p class="text-muted">
                Everything on <code><?php echo htmlspecialchars($gallery_meta['page']); ?></code> below the hero —
                albums, photos, filters, copy and counters.
            </p>
        </div>
        <div>
            <a href="../<?php echo htmlspecialchars($gallery_meta['page']); ?>" target="_blank" class="btn btn-outline-secondary">
                <i class="fas fa-external-link-alt"></i> View page
            </a>
        </div>
    </div>

    <!-- Which gallery page am I editing? -->
    <div class="dashboard-card mb-4"><div class="card-body">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="text-muted fw-bold me-2"><i class="fas fa-layer-group"></i> Gallery page:</span>
            <?php foreach ($GALLERIES as $key => $meta): ?>
                <a href="?gallery=<?php echo urlencode($key); ?>&tab=<?php echo urlencode($_GET['tab'] ?? 'albums'); ?>"
                   class="btn <?php echo $gallery_key === $key ? 'btn-dark' : 'btn-outline-dark'; ?>">
                    <i class="fas <?php echo htmlspecialchars($meta['icon']); ?>"></i>
                    <?php echo htmlspecialchars($meta['label']); ?>
                </a>
            <?php endforeach; ?>
            <span class="text-muted small ms-auto">
                Each gallery keeps its own albums, categories, copy and counters.
            </span>
        </div>
    </div></div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!$schema_ready): ?>
        <div class="alert alert-warning">
            <strong>The gallery tables are missing.</strong>
            Import <code>sql/gallery_page_schema.sql</code> and then <code>sql/gallery_page_data.sql</code>
            into this server's database, then reload this page.
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="dashboard-card h-100"><div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Albums</div>
                <div class="h2 mb-0"><?php echo count($albums); ?></div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dashboard-card h-100"><div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Photos</div>
                <div class="h2 mb-0"><?php echo $total_photos; ?></div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dashboard-card h-100"><div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Categories</div>
                <div class="h2 mb-0"><?php echo count($categories); ?></div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dashboard-card h-100"><div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Published</div>
                <div class="h2 mb-0"><?php
                    echo count(array_filter($albums, function ($a) { return $a['status'] === 'active'; }));
                ?></div>
            </div></div>
        </div>
    </div>

    <div class="dashboard-card mb-4"><div class="card-body">
        <div class="btn-group flex-wrap" role="group">
            <?php foreach (['albums' => 'Albums & Photos', 'content' => 'Page Content', 'categories' => 'Categories', 'stats' => 'Stat Counters'] as $key => $label): ?>
                <a href="?gallery=<?php echo urlencode($gallery_key); ?>&tab=<?php echo $key; ?>" class="btn <?php echo $tab === $key ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div></div>

<?php if ($tab === 'albums'): ?>
    <!-- ==================== ALBUMS ==================== -->
    <div class="dashboard-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-folder-plus"></i> Add a new album</h5>
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#addAlbumForm">
                Show / hide
            </button>
        </div>
        <div class="collapse" id="addAlbumForm"><div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php echo gallery_form_fields($gallery_key); ?>
                <input type="hidden" name="action" value="add_album">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Album title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Event date</label>
                        <input type="date" name="event_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">— Uncategorised —</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cover image (upload)</label>
                        <input type="file" name="cover_upload" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">…or cover image path / URL</label>
                        <input type="text" name="cover_image" class="form-control" placeholder="uploads/gallery/...">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Published</option>
                            <option value="inactive">Hidden</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="newFeatured">
                            <label class="form-check-label" for="newFeatured">Featured album</label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-3"><i class="fas fa-save"></i> Create album &amp; add photos</button>
            </form>
        </div></div>
    </div>

    <form method="POST">
        <?php echo gallery_form_fields($gallery_key); ?>
        <input type="hidden" name="action" value="reorder_albums">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="fas fa-images"></i> Albums (<?php echo count($albums); ?>)</h5>
                <div class="d-flex gap-2 align-items-center">
                    <input type="search" id="albumSearch" class="form-control form-control-sm" placeholder="Filter albums…" style="min-width:200px">
                    <button class="btn btn-sm btn-success"><i class="fas fa-sort-numeric-down"></i> Save order</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:90px">Order</th>
                                <th style="width:90px">Cover</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th class="text-center">Photos</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="albumRows">
                        <?php if (!$albums): ?>
                            <tr><td colspan="8" class="text-center text-muted py-5">No albums yet. Use “Add a new album” above.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($albums as $a):
                            $preview = gallery_admin_preview($a['cover_image']);
                        ?>
                            <tr data-search="<?php echo htmlspecialchars(strtolower($a['title'] . ' ' . ($a['category_name'] ?? ''))); ?>">
                                <td>
                                    <input type="number" name="order[<?php echo $a['id']; ?>]"
                                           value="<?php echo (int) $a['display_order']; ?>"
                                           class="form-control form-control-sm" style="width:75px">
                                </td>
                                <td>
                                    <?php if ($preview): ?>
                                        <img src="<?php echo htmlspecialchars($preview); ?>" alt=""
                                             style="width:70px;height:52px;object-fit:cover;border-radius:6px"
                                             onerror="this.style.opacity=.25">
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($a['title']); ?></div>
                                    <small class="text-muted">
                                        <?php if ($a['is_featured']): ?><span class="badge bg-warning text-dark me-1">Featured</span><?php endif; ?>
                                        /gallery.php?album=<?php echo htmlspecialchars($a['slug']); ?>
                                    </small>
                                </td>
                                <td><?php echo $a['category_name'] ? htmlspecialchars($a['category_name']) : '<span class="text-muted">—</span>'; ?></td>
                                <td><?php echo $a['event_date'] ? htmlspecialchars(date('j M Y', strtotime($a['event_date']))) : '<span class="text-muted">—</span>'; ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?php echo (int) $a['photo_count']; ?></span></td>
                                <td>
                                    <span class="badge bg-<?php echo $a['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo $a['status'] === 'active' ? 'Published' : 'Hidden'; ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="edit_gallery_album.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary js-album-action"
                                            data-action="toggle_album" data-id="<?php echo $a['id']; ?>"
                                            title="<?php echo $a['status'] === 'active' ? 'Hide from the site' : 'Publish'; ?>">
                                        <i class="fas fa-<?php echo $a['status'] === 'active' ? 'eye' : 'eye-slash'; ?>"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-album-action"
                                            data-action="delete_album" data-id="<?php echo $a['id']; ?>"
                                            data-confirm="Delete &quot;<?php echo htmlspecialchars($a['title'], ENT_QUOTES); ?>&quot; and all <?php echo (int) $a['photo_count']; ?> of its photos?"
                                            title="Delete album">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>

    <!-- Single out-of-band form so the row buttons above don't nest forms -->
    <form method="POST" id="albumAction" class="d-none">
        <?php echo gallery_form_fields($gallery_key); ?>
        <input type="hidden" name="action" id="aaAction" value="">
        <input type="hidden" name="id" id="aaId" value="">
    </form>

    <script>
        (function () {
            var box = document.getElementById('albumSearch');
            if (box) {
                box.addEventListener('input', function () {
                    var q = box.value.trim().toLowerCase();
                    document.querySelectorAll('#albumRows tr[data-search]').forEach(function (tr) {
                        tr.style.display = (q === '' || tr.getAttribute('data-search').indexOf(q) !== -1) ? '' : 'none';
                    });
                });
            }

            // Row actions post through one hidden form, so the buttons can sit
            // inside the reorder form without nesting a second <form>.
            var af = document.getElementById('albumAction');
            document.querySelectorAll('.js-album-action').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var msg = btn.getAttribute('data-confirm');
                    if (msg && !confirm(msg)) return;
                    document.getElementById('aaAction').value = btn.getAttribute('data-action');
                    document.getElementById('aaId').value = btn.getAttribute('data-id');
                    af.submit();
                });
            });
        })();
    </script>

<?php elseif ($tab === 'content'): ?>
    <!-- ==================== PAGE CONTENT ==================== -->
    <div class="dashboard-card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-pen-to-square"></i> Gallery page content</h5></div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-circle-info me-1"></i>
                The hero banner at the top of gallery.php is the shared default hero used on every page and is
                intentionally not editable here.
            </div>
            <form method="POST">
                <?php echo gallery_form_fields($gallery_key); ?>
                <input type="hidden" name="action" value="save_content">

                <h6 class="border-bottom pb-2 mb-3">Introduction section</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Heading</label>
                        <input type="text" name="intro_heading" class="form-control" value="<?php echo htmlspecialchars($content['intro_heading'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Paragraph</label>
                        <textarea name="intro_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['intro_text'] ?? ''); ?></textarea>
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Album browser</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Heading</label>
                        <input type="text" name="albums_heading" class="form-control" value="<?php echo htmlspecialchars($content['albums_heading'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">“All” filter label</label>
                        <input type="text" name="all_filter_label" class="form-control" value="<?php echo htmlspecialchars($content['all_filter_label'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Sub-heading</label>
                        <textarea name="albums_subheading" class="form-control" rows="2"><?php echo htmlspecialchars($content['albums_subheading'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Search box placeholder</label>
                        <input type="text" name="search_placeholder" class="form-control" value="<?php echo htmlspecialchars($content['search_placeholder'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">“Nothing found” message</label>
                        <input type="text" name="empty_state_text" class="form-control" value="<?php echo htmlspecialchars($content['empty_state_text'] ?? ''); ?>">
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Album detail view</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">“Back” link text</label>
                        <input type="text" name="detail_back_label" class="form-control" value="<?php echo htmlspecialchars($content['detail_back_label'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo-count wording</label>
                        <input type="text" name="detail_photos_label" class="form-control" value="<?php echo htmlspecialchars($content['detail_photos_label'] ?? ''); ?>">
                        <small class="text-muted">Shown after the number, e.g. “24 photos in this album”.</small>
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Highlights band</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Heading</label>
                        <input type="text" name="stats_heading" class="form-control" value="<?php echo htmlspecialchars($content['stats_heading'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Paragraph</label>
                        <textarea name="stats_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['stats_text'] ?? ''); ?></textarea>
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Closing call to action</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Heading</label>
                        <input type="text" name="cta_heading" class="form-control" value="<?php echo htmlspecialchars($content['cta_heading'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Paragraph</label>
                        <textarea name="cta_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['cta_text'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Button 1 text</label>
                        <input type="text" name="cta_1_text" class="form-control" value="<?php echo htmlspecialchars($content['cta_1_text'] ?? ''); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Button 1 link</label>
                        <input type="text" name="cta_1_link" class="form-control" value="<?php echo htmlspecialchars($content['cta_1_link'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Button 1 icon</label>
                        <input type="text" name="cta_1_icon" class="form-control" value="<?php echo htmlspecialchars($content['cta_1_icon'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Button 2 text</label>
                        <input type="text" name="cta_2_text" class="form-control" value="<?php echo htmlspecialchars($content['cta_2_text'] ?? ''); ?>">
                        <small class="text-muted">Leave blank to hide this button.</small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Button 2 link</label>
                        <input type="text" name="cta_2_link" class="form-control" value="<?php echo htmlspecialchars($content['cta_2_link'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Button 2 icon</label>
                        <input type="text" name="cta_2_icon" class="form-control" value="<?php echo htmlspecialchars($content['cta_2_icon'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Page status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo ($content['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($content['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <p class="text-muted small">
                    Icon names come from
                    <a href="https://fonts.google.com/icons" target="_blank" rel="noopener">Material Symbols</a>
                    (e.g. <code>event</code>, <code>newspaper</code>, <code>photo_library</code>).
                </p>

                <button class="btn btn-primary"><i class="fas fa-save"></i> Save page content</button>
            </form>
        </div>
    </div>

<?php elseif ($tab === 'categories'): ?>
    <!-- ==================== CATEGORIES ==================== -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="dashboard-card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-plus"></i> Add category</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo gallery_form_fields($gallery_key); ?>
                        <input type="hidden" name="action" value="save_category">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Material Symbols icon</label>
                            <input type="text" name="icon" class="form-control" value="photo_library">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo count($categories) + 1; ?>">
                        </div>
                        <input type="hidden" name="status" value="active">
                        <button class="btn btn-primary"><i class="fas fa-save"></i> Add category</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="dashboard-card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-tags"></i> Categories (<?php echo count($categories); ?>)</h5></div>
                <div class="card-body p-0">
                    <?php if (!$categories): ?>
                        <p class="text-muted text-center py-5 mb-0">No categories yet.</p>
                    <?php endif; ?>
                    <?php foreach ($categories as $c): ?>
                        <div class="border-bottom p-3">
                            <form method="POST" class="row g-2 align-items-end">
                                <?php echo gallery_form_fields($gallery_key); ?>
                                <input type="hidden" name="action" value="save_category">
                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                <input type="hidden" name="slug" value="<?php echo htmlspecialchars($c['slug']); ?>">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Name</label>
                                    <input type="text" name="name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c['name']); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Icon</label>
                                    <input type="text" name="icon" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c['icon']); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-muted mb-1">Order</label>
                                    <input type="number" name="display_order" class="form-control form-control-sm" value="<?php echo (int) $c['display_order']; ?>">
                                </div>
                                <div class="col-md-3 d-flex gap-1">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="active" <?php echo $c['status'] === 'active' ? 'selected' : ''; ?>>On</option>
                                        <option value="inactive" <?php echo $c['status'] === 'inactive' ? 'selected' : ''; ?>>Off</option>
                                    </select>
                                    <button class="btn btn-sm btn-success" title="Save"><i class="fas fa-check"></i></button>
                                </div>
                            </form>
                            <form method="POST" class="mt-2"
                                  onsubmit="return confirm('Delete this category? Its albums stay but become uncategorised.');">
                                <?php echo gallery_form_fields($gallery_key); ?>
                                <input type="hidden" name="action" value="delete_category">
                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
                                <small class="text-muted ms-2">slug: <code><?php echo htmlspecialchars($c['slug']); ?></code></small>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ==================== STATS ==================== -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="dashboard-card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-plus"></i> Add stat tile</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo gallery_form_fields($gallery_key); ?>
                        <input type="hidden" name="action" value="save_stat">
                        <div class="mb-3">
                            <label class="form-label">Label *</label>
                            <input type="text" name="label" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value</label>
                            <input type="text" name="value_text" class="form-control" placeholder="e.g. 50K+">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Count automatically</label>
                            <select name="auto_source" class="form-select">
                                <option value="none">No — use the value above</option>
                                <option value="photos">Yes — total published photos</option>
                                <option value="albums">Yes — total published albums</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Material Symbols icon</label>
                            <input type="text" name="icon" class="form-control" value="photo_library">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo count($stats) + 1; ?>">
                        </div>
                        <input type="hidden" name="status" value="active">
                        <button class="btn btn-primary"><i class="fas fa-save"></i> Add stat</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="dashboard-card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-simple"></i> Stat tiles (<?php echo count($stats); ?>)</h5></div>
                <div class="card-body p-0">
                    <?php if (!$stats): ?>
                        <p class="text-muted text-center py-5 mb-0">No stat tiles yet — the highlights band will be hidden.</p>
                    <?php endif; ?>
                    <?php foreach ($stats as $s): ?>
                        <div class="border-bottom p-3">
                            <form method="POST" class="row g-2 align-items-end">
                                <?php echo gallery_form_fields($gallery_key); ?>
                                <input type="hidden" name="action" value="save_stat">
                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Label</label>
                                    <input type="text" name="label" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['label']); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-muted mb-1">Value</label>
                                    <input type="text" name="value_text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['value_text']); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Auto count</label>
                                    <select name="auto_source" class="form-select form-select-sm">
                                        <option value="none"   <?php echo $s['auto_source'] === 'none' ? 'selected' : ''; ?>>Manual</option>
                                        <option value="photos" <?php echo $s['auto_source'] === 'photos' ? 'selected' : ''; ?>>Photos</option>
                                        <option value="albums" <?php echo $s['auto_source'] === 'albums' ? 'selected' : ''; ?>>Albums</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-muted mb-1">Icon</label>
                                    <input type="text" name="icon" class="form-control form-control-sm" value="<?php echo htmlspecialchars($s['icon']); ?>">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label small text-muted mb-1">Ord</label>
                                    <input type="number" name="display_order" class="form-control form-control-sm" value="<?php echo (int) $s['display_order']; ?>">
                                </div>
                                <div class="col-12 d-flex gap-2 mt-2">
                                    <select name="status" class="form-select form-select-sm" style="max-width:110px">
                                        <option value="active"   <?php echo $s['status'] === 'active' ? 'selected' : ''; ?>>Shown</option>
                                        <option value="inactive" <?php echo $s['status'] === 'inactive' ? 'selected' : ''; ?>>Hidden</option>
                                    </select>
                                    <button class="btn btn-sm btn-success"><i class="fas fa-check"></i> Save</button>
                                </div>
                            </form>
                            <form method="POST" class="mt-2" onsubmit="return confirm('Delete this stat tile?');">
                                <?php echo gallery_form_fields($gallery_key); ?>
                                <input type="hidden" name="action" value="delete_stat">
                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

</main>
</div>
<?php include 'footer.php'; ?>
