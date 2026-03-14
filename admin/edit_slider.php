<?php
require_once('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'edit';
$id = $_GET['id'] ?? null;

// Handle delete
if (isset($_GET['delete']) && $_GET['delete']) {
    try {
        $stmt = $pdo->prepare("DELETE FROM homepage_sliders WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: manage_homepage_content.php?tab=sliders&deleted=1");
        exit();
    } catch (Exception $e) {
        $error = "Error deleting: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'] ?? '';
        $highlight_text = $_POST['highlight_text'] ?? '';
        $description = $_POST['description'] ?? '';
        $button1_text = $_POST['button1_text'] ?? '';
        $button1_link = $_POST['button1_link'] ?? '';
        $button2_text = $_POST['button2_text'] ?? '';
        $button2_link = $_POST['button2_link'] ?? '';
        $button3_text = $_POST['button3_text'] ?? '';
        $button3_link = $_POST['button3_link'] ?? '';
        $content_position = $_POST['content_position'] ?? 'middle-center';
        $display_order = $_POST['display_order'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $image_url = $_POST['current_image'] ?? '';

        // Handle File Upload
        if (isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['slider_image']['tmp_name'];
            $fileName = $_FILES['slider_image']['name'];
            $fileSize = $_FILES['slider_image']['size'];
            $fileType = $_FILES['slider_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Sanitize file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = '../Education-Website-and-AdminPanel/images/slider/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                $dest_path = $uploadFileDir . $newFileName;

                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $image_url = 'Education-Website-and-AdminPanel/images/slider/' . $newFileName;
                } else {
                    throw new Exception('There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.');
                }
            } else {
                throw new Exception('Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions));
            }
        }

        if (empty($image_url) && $action === 'add') {
            throw new Exception('Please upload an image.');
        }
        
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO homepage_sliders (image_url, title, highlight_text, description, button1_text, button1_link, button2_text, button2_link, button3_text, button3_link, content_position, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$image_url, $title, $highlight_text, $description, $button1_text, $button1_link, $button2_text, $button2_link, $button3_text, $button3_link, $content_position, $display_order, $is_active]);
        } else {
            $stmt = $pdo->prepare("UPDATE homepage_sliders SET image_url=?, title=?, highlight_text=?, description=?, button1_text=?, button1_link=?, button2_text=?, button2_link=?, button3_text=?, button3_link=?, content_position=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$image_url, $title, $highlight_text, $description, $button1_text, $button1_link, $button2_text, $button2_link, $button3_text, $button3_link, $content_position, $display_order, $is_active, $id]);
        }
        
        header("Location: manage_homepage_content.php?tab=sliders&saved=1");
        exit();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch data if editing
$slider = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM homepage_sliders WHERE id = ?");
    $stmt->execute([$id]);
    $slider = $stmt->fetch();
}

include 'header.php';
include 'sidebar.php';
?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="row mb-4">
                <div class="col-12">
                    <h4><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Slider</h4>
                    <p class="text-muted">Manage hero slider content</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" 
                                           value="<?php echo htmlspecialchars($slider['title'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Highlight Text</label>
                                    <input type="text" name="highlight_text" class="form-control" 
                                           value="<?php echo htmlspecialchars($slider['highlight_text'] ?? ''); ?>">
                                    <small class="text-muted">This text will be highlighted in the title</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="sliderDescription" class="form-control" rows="3"><?php echo htmlspecialchars($slider['description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Slider Image *</label>
                                    <?php if (!empty($slider['image_url'])): ?>
                                        <div class="mb-2">
                                            <img src="../<?php echo htmlspecialchars($slider['image_url']); ?>" alt="Current Slider" style="max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                                            <p class="small text-muted mt-1">Current image: <?php echo htmlspecialchars($slider['image_url']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="slider_image" class="form-control" <?php echo $action === 'add' ? 'required' : ''; ?>>
                                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($slider['image_url'] ?? ''); ?>">
                                    <small class="text-muted">Recommended size: 1920x1080px. Allowed formats: JPG, PNG, WEBP</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Button 1 Text</label>
                                        <input type="text" name="button1_text" class="form-control" 
                                               value="<?php echo htmlspecialchars($slider['button1_text'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Button 1 Link</label>
                                        <input type="text" name="button1_link" class="form-control" 
                                               value="<?php echo htmlspecialchars($slider['button1_link'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Button 2 Text</label>
                                        <input type="text" name="button2_text" class="form-control" 
                                               value="<?php echo htmlspecialchars($slider['button2_text'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Button 2 Link</label>
                                        <input type="text" name="button2_link" class="form-control" 
                                               value="<?php echo htmlspecialchars($slider['button2_link'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Button 3 Text</label>
                                        <input type="text" name="button3_text" class="form-control" 
                                               value="<?php echo htmlspecialchars($slider['button3_text'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Button 3 Link</label>
                                        <input type="text" name="button3_link" class="form-control" 
                                               value="<?php echo htmlspecialchars($slider['button3_link'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Content Position</label>
                                    <select name="content_position" class="form-select">
                                        <?php
                                        $positions = [
                                            'top-left' => 'Top Left',
                                            'top-center' => 'Top Center',
                                            'top-right' => 'Top Right',
                                            'middle-left' => 'Middle Left',
                                            'middle-center' => 'Middle Center',
                                            'middle-right' => 'Middle Right',
                                            'bottom-left' => 'Bottom Left',
                                            'bottom-center' => 'Bottom Center',
                                            'bottom-right' => 'Bottom Right'
                                        ];
                                        foreach ($positions as $value => $label):
                                            $selected = ($slider['content_position'] ?? 'middle-center') === $value ? 'selected' : '';
                                            echo "<option value=\"$value\" $selected>$label</option>";
                                        endforeach;
                                        ?>
                                    </select>
                                    <small class="text-muted">Choose where the text and buttons should appear on the slide</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Display Order *</label>
                                        <input type="number" name="display_order" class="form-control" 
                                               value="<?php echo htmlspecialchars($slider['display_order'] ?? '1'); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="form-check form-switch" style="padding-top: 8px;">
                                            <input class="form-check-input" type="checkbox" name="is_active" 
                                                   <?php echo ($slider['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                    <a href="manage_homepage_content.php?tab=sliders" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <?php if ($action === 'edit' && $id): ?>
                                    <button type="button" class="btn btn-danger ms-auto" onclick="deleteItem(<?php echo $id; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h6>Tips</h6>
                        </div>
                        <div class="card-body">
                            <ul style="padding-left: 20px; margin: 0;">
                                <li class="mb-2">Use high-quality images (1920x1080px recommended)</li>
                                <li class="mb-2">Keep titles short and impactful</li>
                                <li class="mb-2">Display order determines slide sequence</li>
                                <li class="mb-2">Toggle active status to show/hide slides</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this slider? This action cannot be undone.')) {
            window.location.href = 'edit_slider.php?delete=' + id;
        }
    }

    // Initialize CKEditor 5
    ClassicEditor
        .create(document.querySelector('#sliderDescription'), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
        })
        .catch(error => {
            console.error(error);
        });
    </script>

    <style>
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-danger {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary {
        background: #4680ff;
        color: white;
    }
    .btn-primary:hover {
        background: #3066d9;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }
    .btn-danger {
        background: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .d-flex {
        display: flex;
    }
    .gap-2 {
        gap: 0.5rem;
    }
    .ms-auto {
        margin-left: auto;
    }
    .form-check-input {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }
    
    /* CKEditor Height Adjustment */
    .ck-editor__editable_inline {
        min-height: 200px;
        color: #333;
    }
    .ck.ck-editor {
        width: 100% !important;
    }
    </style>

<?php include 'footer.php'; ?>
