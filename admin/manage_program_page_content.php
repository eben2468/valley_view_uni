<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');

$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];
    $hero_badge = $_POST['hero_badge'];
    $hero_image = $_POST['hero_image'];
    $cta_title = $_POST['cta_title'];
    $cta_subtitle = $_POST['cta_subtitle'];

    // Handle Image Upload
    $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'programs');
    if ($uploaded) {
        $hero_image = $uploaded;
    }

    $stmt = $pdo->prepare("UPDATE academic_programs_page_content SET hero_title = ?, hero_subtitle = ?, hero_badge = ?, hero_image = ?, cta_title = ?, cta_subtitle = ? WHERE section_key = 'overview'");
    $stmt->execute([$hero_title, $hero_subtitle, $hero_badge, $hero_image, $cta_title, $cta_subtitle]);
    $message = '<div class="alert alert-success">Page content updated successfully!</div>';
}

// Fetch Page Content
$stmt = $pdo->prepare("SELECT * FROM academic_programs_page_content WHERE section_key = 'overview'");
$stmt->execute();
$content = $stmt->fetch();
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Academic Programs Page Content</h4>
            <p class="text-muted mb-0">Manage the hero section and call-to-action on the programs overview page</p>
        </div>
        <a href="manage_programs.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Programs
        </a>
    </div>

    <?php echo $message; ?>

    <?php
    // academic_programs_overview.php reads academic_pages_content with
    // page_key='academic_programs'. This page writes to the separate
    // academic_programs_page_content table, which nothing reads.
    $legacy_page_name  = 'Academic Programs Overview';
    $legacy_target_url = 'manage_academic_pages.php?page=academic_programs';
    $legacy_public_url = 'academic_programs_overview.php';
    include '_legacy_editor_notice.php';
    ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Hero Section -->
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Hero Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Hero Badge Text</label>
                            <input type="text" name="hero_badge" class="form-control" value="<?php echo htmlspecialchars($content['hero_badge']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero Title (HTML allowed)</label>
                            <textarea name="hero_title" class="form-control" rows="3"><?php echo htmlspecialchars($content['hero_title']); ?></textarea>
                            <small class="text-muted">Use <code>&lt;br&gt;</code> for line breaks and <code>&lt;span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400"&gt;text&lt;/span&gt;</code> for gradients.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($content['hero_subtitle']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero Background Image</label>
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" name="hero_image" id="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($content['hero_image']); ?>" placeholder="Image URL...">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('hero_image_file').click()">
                                    <i class="fas fa-upload"></i>
                                </button>
                            </div>
                            <input type="file" name="hero_image_file" id="hero_image_file" class="d-none" onchange="updateFileName(this)">
                            <small class="text-muted d-block" id="file-name-display">Or upload a new image to replace the current one.</small>
                            <?php if ($content['hero_image']): ?>
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">Current Image Preview:</small>
                                    <img src="../<?php echo htmlspecialchars($content['hero_image']); ?>" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <script>
                        function updateFileName(input) {
                            const fileName = input.files[0] ? input.files[0].name : "Or upload a new image to replace the current one.";
                            document.getElementById('file-name-display').innerText = "Selected: " + fileName;
                            document.getElementById('hero_image_url').value = "uploads/" + fileName; // Placeholder to show it will be uploaded
                            document.getElementById('hero_image_url').readOnly = true;
                        }
                        </script>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Call-to-Action Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">CTA Title</label>
                            <input type="text" name="cta_title" class="form-control" value="<?php echo htmlspecialchars($content['cta_title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CTA Subtitle</label>
                            <textarea name="cta_subtitle" class="form-control" rows="4"><?php echo htmlspecialchars($content['cta_subtitle']); ?></textarea>
                        </div>
                        <div class="alert alert-info py-2 mt-4">
                            <i class="fas fa-info-circle me-2"></i> The buttons (Apply Now, Contact Us) point to <code>admissions.php</code> and <code>contact.php</code> respectively.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="dashboard-card p-3">
                    <div class="d-flex justify-content-end gap-3">
                        <a href="manage_programs.php" class="btn btn-light px-4 border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i> Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<?php include 'footer.php'; ?>
