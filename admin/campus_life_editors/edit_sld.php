<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_sld'])) {
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
    
    // Handle welcome image upload
    $welcome_image = $_POST['welcome_image'];
    if (isset($_FILES['welcome_image_upload']) && $_FILES['welcome_image_upload']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid('welcome_') . '_' . basename($_FILES['welcome_image_upload']['name']);
        if (move_uploaded_file($_FILES['welcome_image_upload']['tmp_name'], $upload_dir . $filename)) {
            $welcome_image = 'uploads/campus_life/' . $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE sld_content SET 
        hero_title = ?, hero_subtitle = ?, hero_image = ?,
        welcome_heading = ?, welcome_text = ?, welcome_image = ?,
        mission_statement = ?, dean_name = ?, dean_title = ?, dean_description = ?,
        services_heading = ?, services_text = ?,
        team_heading = ?, team_text = ?,
        locations_heading = ?, locations_text = ?,
        stats_staff = ?, stats_locations = ?,
        cta_heading = ?, cta_text = ?, status = ?
        WHERE id = ?");
    
    $stmt->execute([
        $_POST['hero_title'], $_POST['hero_subtitle'], $hero_image,
        $_POST['welcome_heading'], $_POST['welcome_text'], $welcome_image,
        $_POST['mission_statement'], $_POST['dean_name'], $_POST['dean_title'], $_POST['dean_description'],
        $_POST['services_heading'], $_POST['services_text'],
        $_POST['team_heading'], $_POST['team_text'],
        $_POST['locations_heading'], $_POST['locations_text'],
        $_POST['stats_staff'], $_POST['stats_locations'],
        $_POST['cta_heading'], $_POST['cta_text'], $_POST['status'], 1
    ]);
    
    echo '<div class="alert alert-success">Content updated successfully!</div>';
}

$stmt = $pdo->query("SELECT * FROM sld_content WHERE id = 1");
$content = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    echo '<div class="alert alert-warning">No content found. Please run the database setup script.</div>';
    return;
}
?>

<div class="dashboard-card">
    <div class="card-header">
        <h5><i class="fas fa-church"></i> Spiritual Life & Development Content</h5>
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

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-hand-holding-heart"></i> Welcome Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Welcome Heading</label>
                    <input type="text" name="welcome_heading" class="form-control" 
                           value="<?php echo htmlspecialchars($content['welcome_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Welcome Image</label>
                    <input type="text" name="welcome_image" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['welcome_image']); ?>"
                           placeholder="Or upload new image below">
                    <input type="file" name="welcome_image_upload" class="form-control" accept="image/*">
                    <small class="text-muted">Upload new image or enter path manually</small>
                    <?php if (!empty($content['welcome_image'])): ?>
                        <div class="mt-2">
                            <img src="../<?php echo htmlspecialchars($content['welcome_image']); ?>" 
                                 alt="Current welcome image" style="max-width: 200px; max-height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Welcome Text</label>
                    <textarea name="welcome_text" class="form-control" rows="4"><?php echo htmlspecialchars($content['welcome_text']); ?></textarea>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-bullseye"></i> Mission Statement</h6>
            <div class="mb-4">
                <textarea name="mission_statement" class="form-control" rows="3"><?php echo htmlspecialchars($content['mission_statement']); ?></textarea>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-tie"></i> Dean Information</h6>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Dean Name</label>
                    <input type="text" name="dean_name" class="form-control" 
                           value="<?php echo htmlspecialchars($content['dean_name']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dean Title</label>
                    <input type="text" name="dean_title" class="form-control" 
                           value="<?php echo htmlspecialchars($content['dean_title']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo $content['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $content['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Dean Description</label>
                    <textarea name="dean_description" class="form-control" rows="2"><?php echo htmlspecialchars($content['dean_description']); ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-list-ul"></i> Services & Team Sections</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Services & Team members</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Services Heading</label>
                    <input type="text" name="services_heading" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['services_heading'] ?? ''); ?>">
                    <label class="form-label">Services Text</label>
                    <textarea name="services_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['services_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team Heading</label>
                    <input type="text" name="team_heading" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['team_heading'] ?? ''); ?>">
                    <label class="form-label">Team Text</label>
                    <textarea name="team_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['team_text'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Locations & Stats</h6>
                <a href="manage_campus_life_lists.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Locations</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Locations Heading</label>
                    <input type="text" name="locations_heading" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['locations_heading'] ?? ''); ?>">
                    <label class="form-label">Locations Text</label>
                    <textarea name="locations_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['locations_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Staff Count Stat</label>
                    <input type="text" name="stats_staff" class="form-control" 
                           value="<?php echo htmlspecialchars($content['stats_staff'] ?? ''); ?>">
                    <small class="text-muted">e.g., 10+</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Locations Count Stat</label>
                    <input type="text" name="stats_locations" class="form-control" 
                           value="<?php echo htmlspecialchars($content['stats_locations'] ?? ''); ?>">
                    <small class="text-muted">e.g., 3</small>
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
                <button type="submit" name="update_sld" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Content
                </button>
                <a href="../sld.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i> Preview Page
                </a>
            </div>
        </form>
    </div>
</div>
