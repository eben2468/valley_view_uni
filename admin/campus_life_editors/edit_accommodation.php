<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_accommodation'])) {
    $id = 1;
    
    $upload_dir = '../uploads/campus_life/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Handle hero image upload
    $hero_image = $_POST['hero_image'];
    if (isset($_FILES['hero_image_upload']) && $_FILES['hero_image_upload']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid('hero_') . '_' . basename($_FILES['hero_image_upload']['name']);
        if (move_uploaded_file($_FILES['hero_image_upload']['tmp_name'], $upload_dir . $filename)) {
            $hero_image = 'uploads/campus_life/' . $filename;
        }
    }
    
    // Handle intro image upload
    $intro_image = $_POST['intro_image'];
    if (isset($_FILES['intro_image_upload']) && $_FILES['intro_image_upload']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid('intro_') . '_' . basename($_FILES['intro_image_upload']['name']);
        if (move_uploaded_file($_FILES['intro_image_upload']['tmp_name'], $upload_dir . $filename)) {
            $intro_image = 'uploads/campus_life/' . $filename;
        }
    }

    // Handle dining image upload
    $dining_image = $_POST['dining_image'];
    if (isset($_FILES['dining_image_upload']) && $_FILES['dining_image_upload']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid('dining_') . '_' . basename($_FILES['dining_image_upload']['name']);
        if (move_uploaded_file($_FILES['dining_image_upload']['tmp_name'], $upload_dir . $filename)) {
            $dining_image = 'uploads/campus_life/' . $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE accommodation_content SET 
        hero_title = ?, hero_subtitle = ?, hero_image = ?,
        intro_heading = ?, intro_text = ?, intro_image = ?,
        facilities_description = ?, room_types_description = ?,
        application_process = ?, rules_and_regulations = ?,
        cta_heading = ?, cta_text = ?, status = ?,
        off_campus_heading = ?, off_campus_text = ?,
        dining_heading = ?, dining_subheading = ?, dining_text = ?, dining_list = ?, dining_image = ?
        WHERE id = ?");
    
    $stmt->execute([
        $_POST['hero_title'], $_POST['hero_subtitle'], $hero_image,
        $_POST['intro_heading'], $_POST['intro_text'], $intro_image,
        $_POST['facilities_description'], $_POST['room_types_description'],
        $_POST['application_process'], $_POST['rules_and_regulations'],
        $_POST['cta_heading'], $_POST['cta_text'], $_POST['status'],
        $_POST['off_campus_heading'], $_POST['off_campus_text'],
        $_POST['dining_heading'], $_POST['dining_subheading'], $_POST['dining_text'], $_POST['dining_list'], $dining_image,
        $id
    ]);
    
    echo '<div class="alert alert-success">Content updated successfully!</div>';
}

// Fetch current content
$stmt = $pdo->query("SELECT * FROM accommodation_content WHERE id = 1");
$content = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    echo '<div class="alert alert-warning">No content found. Please run the database setup script.</div>';
    return;
}
?>

<div class="dashboard-card">
    <div class="card-header">
        <h5><i class="fas fa-home"></i> Accommodation Content</h5>
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

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-info-circle"></i> Introduction</h6>
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

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-building"></i> Facilities & Room Types</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Features & Halls</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Facilities Description</label>
                    <textarea name="facilities_description" class="form-control" rows="5"><?php echo htmlspecialchars($content['facilities_description']); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Room Types Description</label>
                    <textarea name="room_types_description" class="form-control" rows="5"><?php echo htmlspecialchars($content['room_types_description']); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-home"></i> Off-Campus Living</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Off-Campus Heading</label>
                    <input type="text" name="off_campus_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['off_campus_heading'] ?? ''); ?>">
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Off-Campus Text</label>
                    <textarea name="off_campus_text" class="form-control" rows="4"><?php echo htmlspecialchars($content['off_campus_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-utensils"></i> Dining Services</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Dining Heading</label>
                    <input type="text" name="dining_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['dining_heading'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dining Subheading</label>
                    <input type="text" name="dining_subheading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['dining_subheading'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Dining Image</label>
                    <input type="text" name="dining_image" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['dining_image'] ?? ''); ?>"
                           placeholder="Or upload new image below">
                    <input type="file" name="dining_image_upload" class="form-control" accept="image/*">
                    <?php if (!empty($content['dining_image'])): ?>
                        <div class="mt-2">
                            <img src="../<?php echo htmlspecialchars($content['dining_image']); ?>" 
                                 alt="Current dining image" style="max-width: 200px; max-height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Dining List (one per line)</label>
                    <textarea name="dining_list" class="form-control" rows="5"><?php echo htmlspecialchars($content['dining_list'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Dining Text Content</label>
                    <textarea name="dining_text" class="form-control" rows="4"><?php echo htmlspecialchars($content['dining_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-clipboard-list"></i> Application & Rules</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Application Process</label>
                    <textarea name="application_process" class="form-control" rows="5"><?php echo htmlspecialchars($content['application_process']); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rules and Regulations</label>
                    <textarea name="rules_and_regulations" class="form-control" rows="5"><?php echo htmlspecialchars($content['rules_and_regulations']); ?></textarea>
                </div>
            </div>

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
                <button type="submit" name="update_accommodation" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Content
                </button>
                <a href="../accommodation.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i> Preview Page
                </a>
            </div>
        </form>
    </div>
</div>
