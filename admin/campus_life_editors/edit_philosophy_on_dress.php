<?php
// Direct-access guard. This file is normally include()d by
// admin/manage_campus_life_pages.php, but it is also reachable at its own
// URL, where it would otherwise process POSTs and uploads with no login.
// The guard is idempotent, so it is harmless when included.
require_once __DIR__ . "/../../includes/admin_auth.php";
require_once __DIR__ . "/../../includes/upload_helper.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_philosophy'])) {
    $id = 1; // Single record
    
    $upload_dir = '../uploads/campus_life/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle hero image upload
    $hero_image = $_POST['hero_image'];
    if (isset($_FILES['hero_image_upload']) && $_FILES['hero_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['hero_image_upload'], 'campus_life/', 'hero_');
        if ($uploaded !== null) {
            $hero_image = $uploaded;
        }
    }
    
    // Handle intro image upload
    $intro_image = $_POST['intro_image'];
    if (isset($_FILES['intro_image_upload']) && $_FILES['intro_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['intro_image_upload'], 'campus_life/', 'intro_');
        if ($uploaded !== null) {
            $intro_image = $uploaded;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE philosophy_on_dress_content SET 
        hero_title = ?, hero_subtitle = ?, hero_image = ?,
        intro_heading = ?, intro_text = ?, intro_image = ?,
        philosophy_statement = ?, encouraged_items = ?, discouraged_items = ?,
        benefits_text = ?, cta_heading = ?, cta_text = ?, status = ?,
        principles_heading = ?, principles_text = ?, benefits_heading = ?,
        guidelines_heading = ?, guidelines_text = ?, matters_heading = ?, matters_text = ?
        WHERE id = ?");
    
    $stmt->execute([
        $_POST['hero_title'], $_POST['hero_subtitle'], $hero_image,
        $_POST['intro_heading'], $_POST['intro_text'], $intro_image,
        $_POST['philosophy_statement'], $_POST['encouraged_items'], $_POST['discouraged_items'],
        $_POST['benefits_text'], $_POST['cta_heading'], $_POST['cta_text'],
        $_POST['status'],
        $_POST['principles_heading'], $_POST['principles_text'], $_POST['benefits_heading'],
        $_POST['guidelines_heading'], $_POST['guidelines_text'], $_POST['matters_heading'], $_POST['matters_text'],
        $id
    ]);
    
    echo '<div class="alert alert-success">Content updated successfully!</div>';
}

// Fetch current content
$stmt = $pdo->query("SELECT * FROM philosophy_on_dress_content WHERE id = 1");
$content = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    echo '<div class="alert alert-warning">No content found. Please run the database setup script.</div>';
    return;
}
?>

<div class="dashboard-card">
    <div class="card-header">
        <h5><i class="fas fa-tshirt"></i> Philosophy on Dress Content</h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            
            <!-- Hero Section -->
            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-image"></i> Hero Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Hero Title</label>
                    <input type="text" name="hero_title" class="form-control" 
                           value="<?php echo htmlspecialchars($content['hero_title']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hero Image</label>
                    <input type="text" name="hero_image" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['hero_image']); ?>"
                           placeholder="Or upload new image below">
                    <input type="file" name="hero_image_upload" class="form-control" accept="image/*">
                    <small class="text-muted">Upload new image or enter path manually</small>
                    <?php if (!empty($content['hero_image'])): ?>
                        <div class="mt-2">
                            <img src="../<?php echo htmlspecialchars($content['hero_image']); ?>" 
                                 alt="Current hero image" style="max-width: 200px; max-height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Hero Subtitle</label>
                    <textarea name="hero_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($content['hero_subtitle']); ?></textarea>
                </div>
            </div>

            <!-- Introduction Section -->
            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-info-circle"></i> Introduction Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Introduction Heading</label>
                    <input type="text" name="intro_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['intro_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Introduction Image</label>
                    <input type="text" name="intro_image" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['intro_image']); ?>"
                           placeholder="Or upload new image below">
                    <input type="file" name="intro_image_upload" class="form-control" accept="image/*">
                    <small class="text-muted">Upload new image or enter path manually</small>
                    <?php if (!empty($content['intro_image'])): ?>
                        <div class="mt-2">
                            <img src="../<?php echo htmlspecialchars($content['intro_image']); ?>" 
                                 alt="Current intro image" style="max-width: 200px; max-height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Introduction Text</label>
                    <textarea name="intro_text" class="form-control" rows="4"><?php echo htmlspecialchars($content['intro_text']); ?></textarea>
                </div>
            </div>

            <!-- Philosophy Statement -->
            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-quote-left"></i> Philosophy Statement</h6>
            <div class="mb-4">
                <textarea name="philosophy_statement" class="form-control" rows="4"><?php echo htmlspecialchars($content['philosophy_statement']); ?></textarea>
            </div>

            <!-- Principles Section -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-list-check"></i> Principles Section</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Principles</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Principles Heading</label>
                    <input type="text" name="principles_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['principles_heading'] ?? ''); ?>">
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Principles Text</label>
                    <textarea name="principles_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['principles_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Dress Guidelines -->
            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-list"></i> Dress Guidelines</h6>
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Guidelines Heading</label>
                    <input type="text" name="guidelines_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['guidelines_heading'] ?? ''); ?>">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Guidelines Text</label>
                    <textarea name="guidelines_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['guidelines_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Encouraged Items (one per line)</label>
                    <textarea name="encouraged_items" class="form-control" rows="6"><?php echo htmlspecialchars($content['encouraged_items']); ?></textarea>
                    <small class="text-muted">Enter each item on a new line</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Discouraged Items (one per line)</label>
                    <textarea name="discouraged_items" class="form-control" rows="6"><?php echo htmlspecialchars($content['discouraged_items']); ?></textarea>
                    <small class="text-muted">Enter each item on a new line</small>
                </div>
            </div>

            <!-- Why It Matters -->
            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-circle-info"></i> Why It Matters</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Matters Heading</label>
                    <input type="text" name="matters_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['matters_heading'] ?? ''); ?>">
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Matters Text</label>
                    <textarea name="matters_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['matters_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Benefits -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-star"></i> Benefits Section</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Benefits</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Benefits Heading</label>
                    <input type="text" name="benefits_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['benefits_heading'] ?? ''); ?>">
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Benefits Text</label>
                    <textarea name="benefits_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['benefits_text']); ?></textarea>
                </div>
            </div>

            <!-- CTA Section -->
            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-bullhorn"></i> Call to Action</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">CTA Heading</label>
                    <input type="text" name="cta_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['cta_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo $content['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $content['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">CTA Text</label>
                    <textarea name="cta_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['cta_text']); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="update_philosophy" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Content
                </button>
                <a href="../philosophy_on_dress.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i> Preview Page
                </a>
            </div>
        </form>
    </div>
</div>
