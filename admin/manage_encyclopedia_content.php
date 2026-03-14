<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_key = $_POST['page_key'];
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];
    $cta_title = $_POST['cta_title'];
    $cta_subtitle = $_POST['cta_subtitle'];
    
    // Handle Hero Image
    $hero_image = $_POST['existing_hero_image'];
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['hero_image'], 'encyclopedia/', 'hero_');
        if ($uploaded) {
            $hero_image = 'uploads/' . str_replace('uploads/', '', $uploaded);
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE encyclopedia_content SET hero_title=?, hero_subtitle=?, hero_image=?, cta_title=?, cta_subtitle=? WHERE page_key=?");
        $stmt->execute([$hero_title, $hero_subtitle, $hero_image, $cta_title, $cta_subtitle, $page_key]);
        $success_message = ucfirst($page_key) . " Encyclopedia content updated successfully!";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch content
$stmt = $pdo->query("SELECT * FROM encyclopedia_content");
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
$page_data = [];
foreach ($contents as $row) {
    $page_data[$row['page_key']] = $row;
}
?>

<main class="main-content">
    <div class="page-header mb-4">
        <h4><i class="fas fa-edit mr-2"></i> Manage Encyclopedia Content</h4>
        <p class="text-muted">Edit Hero and CTA sections for Staff and Faculty pages</p>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach (['faculty', 'staff'] as $key): 
            $data = $page_data[$key] ?? [
                'hero_title' => '', 'hero_subtitle' => '', 'hero_image' => '', 
                'cta_title' => '', 'cta_subtitle' => ''
            ];
        ?>
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo ucfirst($key); ?> Page Content</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="page_key" value="<?php echo $key; ?>">
                        <input type="hidden" name="existing_hero_image" value="<?php echo htmlspecialchars($data['hero_image']); ?>">
                        
                        <div class="nav-section mb-3">
                            <p class="nav-label">Hero Section</p>
                        </div>
                        
                        <div class="form-group">
                            <label>Hero Title</label>
                            <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($data['hero_title']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2" required><?php echo htmlspecialchars($data['hero_subtitle']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Hero Background Image</label>
                            <?php if ($data['hero_image']): ?>
                                <div class="mb-2">
                                    <img src="../<?php echo htmlspecialchars($data['hero_image']); ?>" style="height: 100px; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                            <div class="custom-file">
                                <input type="file" name="hero_image" class="custom-file-input" id="hero_img_<?php echo $key; ?>">
                                <label class="custom-file-label" for="hero_img_<?php echo $key; ?>">Choose new image</label>
                            </div>
                        </div>

                        <div class="nav-section mb-3 mt-4">
                            <p class="nav-label">CTA Section (Call to Action)</p>
                        </div>

                        <div class="form-group">
                            <label>CTA Title</label>
                            <input type="text" name="cta_title" class="form-control" value="<?php echo htmlspecialchars($data['cta_title']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>CTA Subtitle</label>
                            <textarea name="cta_subtitle" class="form-control" rows="2" required><?php echo htmlspecialchars($data['cta_subtitle']); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-4">Save <?php echo ucfirst($key); ?> Changes</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
