<?php
// Direct-access guard. This file is normally include()d by
// admin/manage_campus_life_pages.php, but it is also reachable at its own
// URL, where it would otherwise process POSTs and uploads with no login.
// The guard is idempotent, so it is harmless when included.
require_once __DIR__ . "/../../includes/admin_auth.php";
require_once __DIR__ . "/../../includes/upload_helper.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_work_study'])) {
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
    
    // Handle overview image upload
    $overview_image = $_POST['overview_image'];
    if (isset($_FILES['overview_image_upload']) && $_FILES['overview_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['overview_image_upload'], 'campus_life/', 'overview_');
        if ($uploaded !== null) {
            $overview_image = $uploaded;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE work_study_content SET 
        hero_title = ?, hero_subtitle = ?, hero_image = ?,
        overview_heading = ?, overview_text = ?, overview_image = ?,
        program_intro_heading = ?, benefits_heading = ?, benefits_text = ?,
        opportunities_heading = ?, opportunities_text = ?,
        info_heading = ?, info_text = ?, info_list = ?,
        steps_heading = ?, steps_text = ?,
        minimum_hours = ?, spouse_policy_text = ?, application_process = ?,
        cta_heading = ?, cta_text = ?, status = ?
        WHERE id = ?");
    
    $stmt->execute([
        $_POST['hero_title'], $_POST['hero_subtitle'], $hero_image,
        $_POST['overview_heading'], $_POST['overview_text'], $overview_image,
        $_POST['program_intro_heading'], $_POST['benefits_heading'], $_POST['benefits_text'],
        $_POST['opportunities_heading'], $_POST['opportunities_text'],
        $_POST['info_heading'], $_POST['info_text'], $_POST['info_list'],
        $_POST['steps_heading'], $_POST['steps_text'],
        $_POST['minimum_hours'], $_POST['spouse_policy_text'], $_POST['application_process'],
        $_POST['cta_heading'], $_POST['cta_text'], $_POST['status'], 1
    ]);
    
    echo '<div class="alert alert-success">Content updated successfully!</div>';
}

$stmt = $pdo->query("SELECT * FROM work_study_content WHERE id = 1");
$content = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    echo '<div class="alert alert-warning">No content found. Please run the database setup script.</div>';
    return;
}
?>

<div class="dashboard-card">
    <div class="card-header">
        <h5><i class="fas fa-briefcase"></i> Work Study Program Content</h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
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

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-info-circle"></i> Overview Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Overview Heading</label>
                    <input type="text" name="overview_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['overview_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program Intro Heading</label>
                    <input type="text" name="program_intro_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['program_intro_heading'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Overview Image</label>
                    <input type="text" name="overview_image" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['overview_image']); ?>"
                           placeholder="Or upload new image below">
                    <input type="file" name="overview_image_upload" class="form-control" accept="image/*">
                    <?php if (!empty($content['overview_image'])): ?>
                        <div class="mt-2">
                            <img src="../<?php echo htmlspecialchars($content['overview_image']); ?>" 
                                 alt="Current overview image" style="max-width: 200px; max-height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Overview Text</label>
                    <textarea name="overview_text" class="form-control" rows="4"><?php echo htmlspecialchars($content['overview_text']); ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-star"></i> Benefits & Opportunities Sections</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Lists</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Benefits Heading</label>
                    <input type="text" name="benefits_heading" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['benefits_heading'] ?? ''); ?>">
                    <label class="form-label">Benefits Text</label>
                    <textarea name="benefits_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['benefits_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Opportunities Heading</label>
                    <input type="text" name="opportunities_heading" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['opportunities_heading'] ?? ''); ?>">
                    <label class="form-label">Opportunities Text</label>
                    <textarea name="opportunities_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['opportunities_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-circle-info"></i> Important Information</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Info Heading</label>
                    <input type="text" name="info_heading" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['info_heading'] ?? ''); ?>">
                    <label class="form-label">Info Text</label>
                    <textarea name="info_text" class="form-control mb-2" rows="3"><?php echo htmlspecialchars($content['info_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Info List (one per line)</label>
                    <textarea name="info_list" class="form-control" rows="6"><?php echo htmlspecialchars($content['info_list'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-list-check"></i> Application Steps</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Steps</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Steps Heading</label>
                    <input type="text" name="steps_heading" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['steps_heading'] ?? ''); ?>">
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label">Steps Text</label>
                    <textarea name="steps_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['steps_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-clock"></i> Program Details</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Minimum Hours Per Week</label>
                    <input type="number" name="minimum_hours" class="form-control" 
                           value="<?php echo htmlspecialchars($content['minimum_hours']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo $content['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $content['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-users"></i> Policies & Process</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Spouse Policy Text</label>
                    <textarea name="spouse_policy_text" class="form-control" rows="5"><?php echo htmlspecialchars($content['spouse_policy_text']); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Application Process</label>
                    <textarea name="application_process" class="form-control" rows="5"><?php echo htmlspecialchars($content['application_process']); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-bullhorn"></i> Call to Action</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">CTA Heading</label>
                    <input type="text" name="cta_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['cta_heading']); ?>">
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">CTA Text</label>
                    <textarea name="cta_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['cta_text']); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="update_work_study" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Content
                </button>
                <a href="../work_study.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i> Preview Page
                </a>
            </div>
        </form>
    </div>
</div>
