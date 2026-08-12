<?php
/**
 * Gallery album editor — album details plus the photos inside it.
 *
 * Photos are managed here rather than in manage_gallery.php because a single
 * album can hold 70+ images, which would swamp the album list.
 */
require_once(__DIR__ . '/../includes/db_connect.php');
require_once(__DIR__ . '/../includes/admin_auth.php');
require_once(__DIR__ . '/../includes/upload_helper.php');

$album_id = (int) ($_GET['id'] ?? 0);
if ($album_id <= 0) {
    header('Location: manage_gallery.php?tab=albums');
    exit;
}

$success = $_GET['created'] ?? '' ? 'Album created. Add its photos below.' : '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    vvu_require_csrf();
    $action = $_POST['action'] ?? '';

    try {
        // ------------------------------------------------ album details
        if ($action === 'save_album') {
            $title = trim((string) ($_POST['title'] ?? ''));
            if ($title === '') {
                throw new RuntimeException('An album needs a title.');
            }

            $cover = trim((string) ($_POST['cover_image'] ?? ''));
            if (!empty($_FILES['cover_upload']['name'])) {
                $uploaded = handleAdminFileUpload($_FILES['cover_upload'], 'gallery/covers', 'cover_');
                if ($uploaded) {
                    $cover = $uploaded;
                }
            }

            $pdo->prepare(
                "UPDATE gallery_albums
                    SET title = ?, description = ?, event_date = ?, cover_image = ?,
                        category_id = ?, is_featured = ?, display_order = ?, status = ?
                  WHERE id = ?"
            )->execute([
                $title,
                $_POST['description'] ?? '',
                trim((string) ($_POST['event_date'] ?? '')) ?: null,
                $cover,
                $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null,
                isset($_POST['is_featured']) ? 1 : 0,
                (int) ($_POST['display_order'] ?? 0),
                ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
                $album_id,
            ]);
            $success = 'Album details saved.';
        }

        // ------------------------------------------------ add photos
        elseif ($action === 'add_photos') {
            $next = (int) $pdo->query(
                "SELECT COALESCE(MAX(display_order), 0) FROM gallery_album_images WHERE album_id = " . (int) $album_id
            )->fetchColumn();

            $insert = $pdo->prepare(
                "INSERT INTO gallery_album_images (album_id, image_path, thumb_path, title, description, display_order, status)
                 VALUES (?,?,?,?,?,?,'active')"
            );

            $added   = 0;
            $skipped = 0;

            // Multiple file upload
            if (!empty($_FILES['photo_files']['name'][0])) {
                $files = $_FILES['photo_files'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    $one = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    $path = handleAdminFileUpload($one, 'gallery/albums/' . $album_id, 'photo_');
                    if ($path) {
                        $insert->execute([$album_id, $path, '', '', '', ++$next]);
                        $added++;
                    } else {
                        $skipped++;
                    }
                }
            }

            // One path/URL per line
            $lines = array_filter(array_map('trim', preg_split('/\R/', (string) ($_POST['photo_urls'] ?? ''))));
            foreach ($lines as $line) {
                $insert->execute([$album_id, $line, '', '', '', ++$next]);
                $added++;
            }

            if ($added === 0 && $skipped === 0) {
                $error = 'Nothing to add — choose some image files or paste at least one path.';
            } else {
                $success = $added . ' photo' . ($added === 1 ? '' : 's') . ' added.'
                         . ($skipped ? ' ' . $skipped . ' file(s) were rejected — check the type and size.' : '');
            }
        }

        // ------------------------------------------------ photo edits
        elseif ($action === 'save_photos') {
            $stmt = $pdo->prepare(
                "UPDATE gallery_album_images
                    SET title = ?, description = ?, image_path = ?, thumb_path = ?, display_order = ?, status = ?
                  WHERE id = ? AND album_id = ?"
            );
            foreach (($_POST['photo'] ?? []) as $pid => $row) {
                $stmt->execute([
                    $row['title'] ?? '',
                    $row['description'] ?? '',
                    $row['image_path'] ?? '',
                    $row['thumb_path'] ?? '',
                    (int) ($row['display_order'] ?? 0),
                    ($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
                    (int) $pid,
                    $album_id,
                ]);
            }
            $success = 'Photo changes saved.';
        }
        elseif ($action === 'delete_photo') {
            $pdo->prepare("DELETE FROM gallery_album_images WHERE id = ? AND album_id = ?")
                ->execute([(int) $_POST['photo_id'], $album_id]);
            $success = 'Photo removed.';
        }
        elseif ($action === 'bulk_photos') {
            $ids = array_map('intval', $_POST['selected'] ?? []);
            if (!$ids) {
                throw new RuntimeException('No photos were selected.');
            }
            $in = implode(',', array_fill(0, count($ids), '?'));
            $op = $_POST['bulk_op'] ?? '';

            if ($op === 'delete') {
                $pdo->prepare("DELETE FROM gallery_album_images WHERE album_id = ? AND id IN ($in)")
                    ->execute(array_merge([$album_id], $ids));
                $success = count($ids) . ' photo(s) deleted.';
            } elseif ($op === 'hide' || $op === 'show') {
                $pdo->prepare("UPDATE gallery_album_images SET status = ? WHERE album_id = ? AND id IN ($in)")
                    ->execute(array_merge([$op === 'hide' ? 'inactive' : 'active', $album_id], $ids));
                $success = count($ids) . ' photo(s) updated.';
            } elseif ($op === 'make_cover') {
                $first = $pdo->prepare("SELECT image_path FROM gallery_album_images WHERE id = ? AND album_id = ?");
                $first->execute([$ids[0], $album_id]);
                if ($path = $first->fetchColumn()) {
                    $pdo->prepare("UPDATE gallery_albums SET cover_image = ? WHERE id = ?")->execute([$path, $album_id]);
                    $success = 'Album cover updated.';
                }
            } else {
                throw new RuntimeException('Unknown bulk action.');
            }
        }
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log('Gallery album save failed: ' . $e->getMessage());
        $error = 'Save failed. The changes were not stored — check the server error log.';
    }

    if (function_exists('vvu_take_upload_error') && ($u = vvu_take_upload_error())) {
        $error = trim($error . ' ' . $u);
    }
}

// ---------------------------------------------------------------- load
try {
    $stmt = $pdo->prepare("SELECT * FROM gallery_albums WHERE id = ?");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$album) {
        header('Location: manage_gallery.php?tab=albums');
        exit;
    }

    // The album carries its own gallery, so this editor works for any of them
    // without needing the key in the URL.
    $gallery_key = $album['gallery_key'] ?? 'main';
    $GALLERIES = [
        'main' => ['label' => 'Main Photo Gallery', 'page' => 'gallery.php'],
        'src'  => ['label' => 'SRC Gallery',        'page' => 'src_gallery.php'],
    ];
    $gallery_meta = $GALLERIES[$gallery_key] ?? $GALLERIES['main'];

    // Only this gallery's categories may be assigned to this album.
    $cstmt = $pdo->prepare("SELECT * FROM gallery_categories WHERE gallery_key = ? ORDER BY display_order ASC, name ASC");
    $cstmt->execute([$gallery_key]);
    $categories = $cstmt->fetchAll(PDO::FETCH_ASSOC);

    $pstmt = $pdo->prepare("SELECT * FROM gallery_album_images WHERE album_id = ? ORDER BY display_order ASC, id ASC");
    $pstmt->execute([$album_id]);
    $photos = $pstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Gallery album load failed: ' . $e->getMessage());
    header('Location: manage_gallery.php?tab=albums');
    exit;
}

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
            <h1><i class="fas fa-folder-open"></i> <?php echo htmlspecialchars($album['title']); ?></h1>
            <p class="text-muted">
                <?php echo count($photos); ?> photo(s) &middot;
                <span class="badge bg-dark"><?php echo htmlspecialchars($gallery_meta['label']); ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="manage_gallery.php?gallery=<?php echo urlencode($gallery_key); ?>&tab=albums" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> All albums
            </a>
            <a href="../<?php echo htmlspecialchars($gallery_meta['page']); ?>?album=<?php echo urlencode($album['slug']); ?>" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-external-link-alt"></i> View
            </a>
        </div>
    </div>

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

    <!-- ==================== ALBUM DETAILS ==================== -->
    <div class="dashboard-card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-circle-info"></i> Album details</h5></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php echo vvu_csrf_field(); ?>
                <input type="hidden" name="action" value="save_album">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($album['title']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Event date</label>
                        <input type="date" name="event_date" class="form-control" value="<?php echo htmlspecialchars($album['event_date'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Optional — shown above the photo grid."><?php echo htmlspecialchars($album['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">— Uncategorised —</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo (int) $album['category_id'] === (int) $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Display order</label>
                        <input type="number" name="display_order" class="form-control" value="<?php echo (int) $album['display_order']; ?>">
                        <small class="text-muted">Lower numbers appear first on gallery.php.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active"   <?php echo $album['status'] === 'active' ? 'selected' : ''; ?>>Published</option>
                            <option value="inactive" <?php echo $album['status'] === 'inactive' ? 'selected' : ''; ?>>Hidden</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Cover image path / URL</label>
                        <input type="text" name="cover_image" class="form-control mb-2" value="<?php echo htmlspecialchars($album['cover_image'] ?? ''); ?>">
                        <input type="file" name="cover_upload" class="form-control" accept="image/*">
                        <small class="text-muted">Uploading a file replaces the path above. Or tick a photo below and use “Set as album cover”.</small>
                    </div>
                    <div class="col-md-4">
                        <?php if ($p = gallery_admin_preview($album['cover_image'])): ?>
                            <label class="form-label">Current cover</label><br>
                            <img src="<?php echo htmlspecialchars($p); ?>" alt=""
                                 style="max-width:100%;max-height:150px;border-radius:8px;object-fit:cover"
                                 onerror="this.style.opacity=.2">
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" <?php echo $album['is_featured'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="isFeatured">Featured album</label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-3"><i class="fas fa-save"></i> Save album details</button>
            </form>
        </div>
    </div>

    <!-- ==================== ADD PHOTOS ==================== -->
    <div class="dashboard-card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-cloud-arrow-up"></i> Add photos</h5></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php echo vvu_csrf_field(); ?>
                <input type="hidden" name="action" value="add_photos">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Upload image files (select many at once)</label>
                        <input type="file" name="photo_files[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">
                            Saved to <code>uploads/gallery/albums/<?php echo $album_id; ?>/</code>.
                            Very large batches can exceed the server's upload limit — add them in groups if a batch fails.
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">…or paste image paths / URLs, one per line</label>
                        <textarea name="photo_urls" class="form-control" rows="4"
                                  placeholder="uploads/gallery/albums/3/images/games-10.jpg&#10;https://example.com/photo.jpg"></textarea>
                    </div>
                </div>
                <button class="btn btn-success mt-3"><i class="fas fa-plus"></i> Add photos</button>
            </form>
        </div>
    </div>

    <!-- ==================== PHOTOS ==================== -->
    <form method="POST" id="photosForm">
        <?php echo vvu_csrf_field(); ?>
        <input type="hidden" name="action" id="photosAction" value="save_photos">
        <input type="hidden" name="bulk_op" id="bulkOp" value="">

        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="fas fa-images"></i> Photos (<?php echo count($photos); ?>)</h5>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAll">Select all</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="selectNone">Clear</button>
                    <button type="button" class="btn btn-sm btn-outline-primary js-bulk" data-op="make_cover">Set as album cover</button>
                    <button type="button" class="btn btn-sm btn-outline-warning js-bulk" data-op="hide">Hide</button>
                    <button type="button" class="btn btn-sm btn-outline-success js-bulk" data-op="show">Show</button>
                    <button type="button" class="btn btn-sm btn-outline-danger js-bulk" data-op="delete"
                            data-confirm="Delete the selected photos? This cannot be undone.">Delete</button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Save all changes</button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!$photos): ?>
                    <p class="text-muted text-center py-5 mb-0">No photos yet. Use “Add photos” above.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($photos as $ph):
                            $prev = gallery_admin_preview($ph['thumb_path'] ?: $ph['image_path']);
                        ?>
                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="border rounded p-2 h-100 <?php echo $ph['status'] === 'inactive' ? 'bg-light opacity-75' : ''; ?>">
                                    <div class="position-relative mb-2">
                                        <a href="<?php echo htmlspecialchars(gallery_admin_preview($ph['image_path'])); ?>" target="_blank" rel="noopener">
                                            <img src="<?php echo htmlspecialchars($prev); ?>" alt=""
                                                 style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:6px;background:#eee"
                                                 loading="lazy" onerror="this.style.opacity=.2">
                                        </a>
                                        <div class="form-check position-absolute top-0 start-0 m-2 p-1 bg-white rounded shadow-sm">
                                            <input class="form-check-input m-0 js-photo-check" type="checkbox"
                                                   name="selected[]" value="<?php echo $ph['id']; ?>"
                                                   aria-label="Select photo <?php echo $ph['id']; ?>">
                                        </div>
                                        <?php if ($ph['status'] === 'inactive'): ?>
                                            <span class="badge bg-secondary position-absolute top-0 end-0 m-2">Hidden</span>
                                        <?php endif; ?>
                                    </div>

                                    <input type="text" class="form-control form-control-sm mb-1"
                                           name="photo[<?php echo $ph['id']; ?>][title]"
                                           value="<?php echo htmlspecialchars($ph['title'] ?? ''); ?>" placeholder="Caption (optional)">
                                    <input type="text" class="form-control form-control-sm mb-1"
                                           name="photo[<?php echo $ph['id']; ?>][image_path]"
                                           value="<?php echo htmlspecialchars($ph['image_path']); ?>" placeholder="Full-size path">
                                    <input type="text" class="form-control form-control-sm mb-1"
                                           name="photo[<?php echo $ph['id']; ?>][thumb_path]"
                                           value="<?php echo htmlspecialchars($ph['thumb_path'] ?? ''); ?>" placeholder="Thumbnail path (optional)">
                                    <textarea class="form-control form-control-sm mb-1" rows="1"
                                              name="photo[<?php echo $ph['id']; ?>][description]"
                                              placeholder="Description (optional)"><?php echo htmlspecialchars($ph['description'] ?? ''); ?></textarea>

                                    <div class="d-flex gap-1 align-items-center">
                                        <input type="number" class="form-control form-control-sm"
                                               name="photo[<?php echo $ph['id']; ?>][display_order]"
                                               value="<?php echo (int) $ph['display_order']; ?>" style="max-width:75px" title="Order">
                                        <select class="form-select form-select-sm" name="photo[<?php echo $ph['id']; ?>][status]">
                                            <option value="active"   <?php echo $ph['status'] === 'active' ? 'selected' : ''; ?>>Shown</option>
                                            <option value="inactive" <?php echo $ph['status'] === 'inactive' ? 'selected' : ''; ?>>Hidden</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-danger js-del-photo"
                                                data-id="<?php echo $ph['id']; ?>" title="Delete this photo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <form method="POST" id="delPhotoForm" class="d-none">
        <?php echo vvu_csrf_field(); ?>
        <input type="hidden" name="action" value="delete_photo">
        <input type="hidden" name="photo_id" id="delPhotoId" value="">
    </form>
</main>
</div>

<script>
(function () {
    var form = document.getElementById('photosForm');
    if (!form) return;

    var checks = Array.prototype.slice.call(document.querySelectorAll('.js-photo-check'));

    var all = document.getElementById('selectAll');
    if (all) all.addEventListener('click', function () {
        checks.forEach(function (c) { c.checked = true; });
    });

    var none = document.getElementById('selectNone');
    if (none) none.addEventListener('click', function () {
        checks.forEach(function (c) { c.checked = false; });
    });

    document.querySelectorAll('.js-bulk').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!checks.some(function (c) { return c.checked; })) {
                alert('Tick at least one photo first.');
                return;
            }
            var msg = btn.getAttribute('data-confirm');
            if (msg && !confirm(msg)) return;
            document.getElementById('photosAction').value = 'bulk_photos';
            document.getElementById('bulkOp').value = btn.getAttribute('data-op');
            form.submit();
        });
    });

    document.querySelectorAll('.js-del-photo').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Delete this photo?')) return;
            document.getElementById('delPhotoId').value = btn.getAttribute('data-id');
            document.getElementById('delPhotoForm').submit();
        });
    });
})();
</script>

<?php include 'footer.php'; ?>
