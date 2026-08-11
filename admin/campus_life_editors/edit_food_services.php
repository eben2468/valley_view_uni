<?php
// Direct-access guard. This file is normally include()d by
// admin/manage_campus_life_pages.php, but it is also reachable at its own
// URL, where it would otherwise process POSTs and uploads with no login.
// The guard is idempotent, so it is harmless when included.
require_once __DIR__ . "/../../includes/admin_auth.php";
require_once __DIR__ . "/../../includes/upload_helper.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_food'])) {
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
    
    // Handle philosophy image upload
    $philosophy_image = $_POST['philosophy_image'];
    if (isset($_FILES['philosophy_image_upload']) && $_FILES['philosophy_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['philosophy_image_upload'], 'campus_life/', 'philosophy_');
        if ($uploaded !== null) {
            $philosophy_image = $uploaded;
        }
    }
    
    // The save runs inside try/catch because PDO is in ERRMODE_EXCEPTION. An
    // uncaught PDOException here (e.g. a column the schema is missing) killed
    // the request mid-page, so with display_errors off in production the admin
    // saw a blank panel and no explanation — an upload appeared to "do nothing"
    // even though the file had already been written to uploads/.
    try {
        $stmt = $pdo->prepare("UPDATE food_services_content SET 
            hero_title = ?, hero_subtitle = ?, hero_image = ?,
            philosophy_heading = ?, philosophy_text = ?, philosophy_image = ?,
            breakfast_time = ?, breakfast_desc = ?, lunch_time = ?, lunch_desc = ?, dinner_time = ?, dinner_desc = ?,
            meal_plans_heading = ?, meal_plans_text = ?, meal_plans_reg_info = ?, meal_plans_btn_text = ?, meal_plans_btn_url = ?,
            feedback_heading = ?, feedback_text = ?, status = ?
            WHERE id = ?");
        
        $stmt->execute([
            $_POST['hero_title'], $_POST['hero_subtitle'], $hero_image,
            $_POST['philosophy_heading'], $_POST['philosophy_text'], $philosophy_image,
            $_POST['breakfast_time'], $_POST['breakfast_desc'], $_POST['lunch_time'], $_POST['lunch_desc'], $_POST['dinner_time'], $_POST['dinner_desc'],
            $_POST['meal_plans_heading'], $_POST['meal_plans_text'], $_POST['meal_plans_reg_info'], $_POST['meal_plans_btn_text'], $_POST['meal_plans_btn_url'],
            $_POST['feedback_heading'], $_POST['feedback_text'],
            $_POST['status'], 1
        ]);
        
        echo '<div class="alert alert-success">Content updated successfully!</div>';
    } catch (PDOException $e) {
        error_log('Food Services save failed: ' . $e->getMessage());
        echo vvu_render_save_error($e);
    }

    // An upload that failed for its own reasons (too large, unwritable folder)
    // is reported here rather than only by admin/header.php, which has already
    // been rendered by the time this included file runs.
    if (function_exists('vvu_take_upload_error') && ($uploadError = vvu_take_upload_error())) {
        echo '<div class="alert alert-warning"><strong>The image was not uploaded.</strong> '
           . htmlspecialchars($uploadError)
           . '<br><small>Everything else on the form was saved. The previous image is still in place.</small></div>';
    }
}

$stmt = $pdo->query("SELECT * FROM food_services_content WHERE id = 1");
$content = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    echo '<div class="alert alert-warning">No content found. Please run the database setup script.</div>';
    return;
}
?>

<div class="dashboard-card">
    <div class="card-header">
        <h5><i class="fas fa-utensils"></i> Food Services Content</h5>
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

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-leaf"></i> Philosophy Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Philosophy Heading</label>
                    <input type="text" name="philosophy_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['philosophy_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Philosophy Image</label>
                    <input type="text" name="philosophy_image" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['philosophy_image']); ?>"
                           placeholder="Or upload new image below">
                    <input type="file" name="philosophy_image_upload" class="form-control" accept="image/*">
                    <small class="text-muted">Upload new image or enter path manually</small>
                    <?php if (!empty($content['philosophy_image'])): ?>
                        <div class="mt-2">
                            <img src="../<?php echo htmlspecialchars($content['philosophy_image']); ?>" 
                                 alt="Current philosophy image" style="max-width: 200px; max-height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Philosophy Text</label>
                    <textarea name="philosophy_text" class="form-control" rows="4"><?php echo htmlspecialchars($content['philosophy_text']); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-clock"></i> Dining Hours</h6>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Breakfast Time</label>
                    <input type="text" name="breakfast_time" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['breakfast_time']); ?>">
                    <label class="form-label">Breakfast Description</label>
                    <textarea name="breakfast_desc" class="form-control" rows="2"><?php echo htmlspecialchars($content['breakfast_desc'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lunch Time</label>
                    <input type="text" name="lunch_time" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['lunch_time']); ?>">
                    <label class="form-label">Lunch Description</label>
                    <textarea name="lunch_desc" class="form-control" rows="2"><?php echo htmlspecialchars($content['lunch_desc'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dinner Time</label>
                    <input type="text" name="dinner_time" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['dinner_time']); ?>">
                    <label class="form-label">Dinner Description</label>
                    <textarea name="dinner_desc" class="form-control" rows="2"><?php echo htmlspecialchars($content['dinner_desc'] ?? ''); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-credit-card"></i> Meal Plans Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Meal Plans Heading</label>
                    <input type="text" name="meal_plans_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['meal_plans_heading'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meal Plans Button Text</label>
                    <input type="text" name="meal_plans_btn_text" class="form-control" 
                           value="<?php echo htmlspecialchars($content['meal_plans_btn_text'] ?? ''); ?>">
                </div>
                <div class="col-md-12 mt-3">
                    <label class="form-label">Meal Plans Button URL</label>
                    <input type="text" name="meal_plans_btn_url" class="form-control" 
                           value="<?php echo htmlspecialchars($content['meal_plans_btn_url'] ?? ''); ?>">
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Meal Plans Text</label>
                    <textarea name="meal_plans_text" class="form-control" rows="3"><?php echo htmlspecialchars($content['meal_plans_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Registration Info</label>
                    <textarea name="meal_plans_reg_info" class="form-control" rows="2"><?php echo htmlspecialchars($content['meal_plans_reg_info'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-utensils"></i> Food Features</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Features</a>
            </div>
            <p class="text-muted small mb-4">Manage icons, titles, and descriptions of food service features (e.g., Balanced Meals, Hygiene Standards).</p>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-comments"></i> Feedback Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Feedback Heading</label>
                    <input type="text" name="feedback_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['feedback_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo $content['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $content['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Feedback Text</label>
                    <textarea name="feedback_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['feedback_text']); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="update_food" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Content
                </button>
                <a href="../food_services.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i> Preview Page
                </a>
            </div>
        </form>
    </div>
</div>
