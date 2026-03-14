<?php
/**
 * Valley View University Admin Panel
 * Add/Edit News Article Page with Rich Text Editor
 */

require_once('../includes/db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Determine action mode
$action = isset($_GET['action']) ? $_GET['action'] : 'edit';
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize article data
$article = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'content' => '',
    'featured_image' => '',
    'category' => 'news',
    'author' => 'VVU Communications',
    'author_image' => '',
    'tags' => '',
    'status' => 'draft',
    'is_featured' => 0,
    'meta_title' => '',
    'meta_description' => '',
    'publish_date' => date('Y-m-d H:i:s'),
    'event_date' => '',
    'event_time' => '',
    'event_location' => ''
];

// Fetch existing article if editing
if ($action === 'edit' && $article_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM news_articles WHERE id = ?");
        $stmt->execute([$article_id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $article = $fetched;
        } else {
            header("Location: manage_news.php");
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Error loading article: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $article['title'] = trim($_POST['title'] ?? '');
    $article['slug'] = trim($_POST['slug'] ?? '');
    $article['excerpt'] = trim($_POST['excerpt'] ?? '');
    $article['content'] = $_POST['content'] ?? '';
    $article['category'] = $_POST['category'] ?? 'news';
    $article['author'] = trim($_POST['author'] ?? 'VVU Communications');
    $article['tags'] = trim($_POST['tags'] ?? '');
    $article['status'] = $_POST['status'] ?? 'draft';
    $article['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $article['meta_title'] = trim($_POST['meta_title'] ?? '');
    $article['meta_description'] = trim($_POST['meta_description'] ?? '');
    $article['publish_date'] = $_POST['publish_date'] ?? date('Y-m-d H:i:s');
    $article['event_date'] = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
    $article['event_time'] = !empty($_POST['event_time']) ? $_POST['event_time'] : null;
    $article['event_location'] = trim($_POST['event_location'] ?? '');
    
    // Validate required fields
    if (empty($article['title'])) {
        $error_message = "Title is required.";
    } else {
        // Generate slug if empty
        if (empty($article['slug'])) {
            $article['slug'] = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $article['title']));
            $article['slug'] = trim($article['slug'], '-');
        }
        
        // Handle image removal
        if (isset($_POST['remove_featured_image']) && $_POST['remove_featured_image'] === '1' && empty($_FILES['featured_image']['name'])) {
            if (!empty($article['featured_image']) && file_exists('../' . $article['featured_image'])) {
                unlink('../' . $article['featured_image']);
            }
            $article['featured_image'] = null;
        }
        
        // Handle image upload
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/news/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_ext, $allowed_extensions)) {
                $new_filename = $article['slug'] . '-' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if (!empty($article['featured_image']) && file_exists('../' . $article['featured_image'])) {
                        unlink('../' . $article['featured_image']);
                    }
                    $article['featured_image'] = 'uploads/news/' . $new_filename;
                } else {
                    $error_message = "Error uploading image.";
                }
            } else {
                $error_message = "Invalid image format. Allowed: JPG, PNG, GIF, WEBP";
            }
        }
        
        if (empty($error_message)) {
            try {
                if ($action === 'add' || $article_id === 0) {
                    // Insert new article
                    $stmt = $pdo->prepare("
                        INSERT INTO news_articles (
                            title, slug, excerpt, content, featured_image, category, author, 
                            tags, status, is_featured, meta_title, meta_description, 
                            publish_date, event_date, event_time, event_location
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $article['title'], $article['slug'], $article['excerpt'], $article['content'],
                        $article['featured_image'], $article['category'], $article['author'],
                        $article['tags'], $article['status'], $article['is_featured'],
                        $article['meta_title'], $article['meta_description'], $article['publish_date'],
                        $article['event_date'], $article['event_time'], $article['event_location']
                    ]);
                    $success_message = "Article created successfully!";
                    $article_id = $pdo->lastInsertId();
                    $article['id'] = $article_id;
                } else {
                    // Update existing article
                    $stmt = $pdo->prepare("
                        UPDATE news_articles SET
                            title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?,
                            category = ?, author = ?, tags = ?, status = ?, is_featured = ?,
                            meta_title = ?, meta_description = ?, publish_date = ?,
                            event_date = ?, event_time = ?, event_location = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $article['title'], $article['slug'], $article['excerpt'], $article['content'],
                        $article['featured_image'], $article['category'], $article['author'],
                        $article['tags'], $article['status'], $article['is_featured'],
                        $article['meta_title'], $article['meta_description'], $article['publish_date'],
                        $article['event_date'], $article['event_time'], $article['event_location'],
                        $article_id
                    ]);
                    $success_message = "Article updated successfully!";
                }
            } catch (PDOException $e) {
                $error_message = "Database error: " . $e->getMessage();
            }
        }
    }
}

$category_labels = [
    'news' => 'News',
    'events' => 'Events',
    'announcements' => 'Notices',
    'press_releases' => 'Press Releases',
    'academic' => 'Academic'
];

$page_title = ($action === 'add' ? 'Add New' : 'Edit') . ' Article';
include 'header.php';
include 'sidebar.php';
?>

<!-- Summernote Editor (Free, No API Key Required) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>


<style>
/* Article Editor Styles */
.editor-container {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 30px;
    margin-top: 24px;
}

.editor-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.editor-sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.editor-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #002147;
}

.card-header i {
    color: #f26838;
    font-size: 18px;
}

.card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #002147;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group label .required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #f26838;
    box-shadow: 0 0 0 4px rgba(242,104,56,0.1);
}

.form-control-lg {
    font-size: 20px;
    font-weight: 600;
    padding: 16px 20px;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-hint {
    color: #6c757d;
    font-size: 12px;
    margin-top: 6px;
}

.slug-preview {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: #f8f9fa;
    border-radius: 6px;
    font-family: monospace;
    font-size: 13px;
    color: #6c757d;
}

.slug-preview span {
    color: #002147;
}

/* Image Upload */
.image-upload-wrapper {
    border: 2px dashed #e9ecef;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.image-upload-wrapper:hover {
    border-color: #f26838;
    background: rgba(242,104,56,0.02);
}

.image-upload-wrapper.has-image {
    padding: 0;
    border-style: solid;
}

.image-preview {
    width: 100%;
    max-height: 250px;
    object-fit: cover;
    border-radius: 10px;
}

.upload-placeholder {
    color: #6c757d;
}

.upload-placeholder i {
    font-size: 48px;
    color: #dee2e6;
    margin-bottom: 12px;
}

.upload-placeholder p {
    margin: 0;
    font-size: 14px;
}

.upload-placeholder strong {
    color: #f26838;
}

.image-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}

.remove-image {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 32px;
    height: 32px;
    background: rgba(220,53,69,0.9);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.remove-image:hover {
    background: #dc3545;
    transform: scale(1.1);
}

/* Status & Category Selects */
.select-wrapper {
    position: relative;
}

.select-wrapper::after {
    content: '\f107';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
}

select.form-control {
    appearance: none;
    padding-right: 40px;
}

/* Checkbox & Toggle */
.form-check {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.form-check input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #f26838;
    cursor: pointer;
}

.form-check label {
    margin: 0;
    cursor: pointer;
}

/* Two Column Layout */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* Tags Input */
.tags-hint {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}

.tags-hint span {
    padding: 4px 10px;
    background: #e9ecef;
    border-radius: 12px;
    font-size: 11px;
    color: #6c757d;
}

/* Action Buttons */
.editor-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #f26838 0%, #e85a2a 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(242,104,56,0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(242,104,56,0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-outline {
    background: white;
    color: #002147;
    border: 2px solid #e9ecef;
}

.btn-outline:hover {
    border-color: #002147;
    background: #f8f9fa;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20963c 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
}

/* Alert Messages */
.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: rgba(40,167,69,0.1);
    color: #28a745;
    border: 1px solid rgba(40,167,69,0.2);
}

.alert-danger {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
    border: 1px solid rgba(220,53,69,0.2);
}

/* Summernote Editor Container */
.note-editor.note-frame {
    border: 2px solid #e9ecef !important;
    border-radius: 8px !important;
    overflow: hidden;
}

.note-editor.note-frame .note-toolbar {
    background: #f8f9fa !important;
    border-bottom: 1px solid #e9ecef !important;
    padding: 8px !important;
}

.note-editor.note-frame .note-editing-area {
    background: white;
}

.note-editor.note-frame .note-editing-area .note-editable {
    padding: 20px !important;
    font-size: 16px;
    line-height: 1.6;
}

.note-btn {
    border-radius: 4px !important;
}

.note-btn:hover {
    background: #e9ecef !important;
}


/* Responsive */
@media screen and (max-width: 1200px) {
    .editor-container {
        grid-template-columns: 1fr;
    }
    
    .editor-sidebar {
        order: -1;
    }
}

@media screen and (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .editor-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h4>
            <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?>"></i>
            <?php echo $action === 'add' ? 'Add New Article' : 'Edit Article'; ?>
        </h4>
        <a href="manage_news.php" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Articles
        </a>
    </div>

    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success_message); ?>
        <a href="manage_news.php" style="margin-left: auto; color: inherit;">View All Articles →</a>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="articleForm">
        <div class="editor-container">
            <!-- Main Editor Column -->
            <div class="editor-main">
                <!-- Title Card -->
                <div class="editor-card">
                    <div class="card-header">
                        <i class="fas fa-heading"></i>
                        <h5>Article Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Title <span class="required">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($article['title']); ?>" 
                                   placeholder="Enter article title..." 
                                   required 
                                   id="articleTitle">
                        </div>
                        <div class="form-group">
                            <label>URL Slug</label>
                            <div class="slug-preview">
                                /news_detail.php?slug=<span id="slugPreview"><?php echo htmlspecialchars($article['slug'] ?: 'article-slug'); ?></span>
                            </div>
                            <input type="hidden" name="slug" id="articleSlug" value="<?php echo htmlspecialchars($article['slug']); ?>">
                            <p class="form-hint">Auto-generated from title. Leave empty for automatic slug generation.</p>
                        </div>
                        <div class="form-group">
                            <label>Excerpt / Summary</label>
                            <textarea name="excerpt" class="form-control" rows="3" 
                                      placeholder="Brief summary of the article (appears in news listings)..."><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
                            <p class="form-hint">Keep it under 200 characters for best display in news cards.</p>
                        </div>
                    </div>
                </div>

                <!-- Content Editor Card -->
                <div class="editor-card">
                    <div class="card-header">
                        <i class="fas fa-file-alt"></i>
                        <h5>Article Content</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="content" id="contentEditor"><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>
                </div>

                <!-- SEO Card -->
                <div class="editor-card">
                    <div class="card-header">
                        <i class="fas fa-search"></i>
                        <h5>SEO Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" 
                                   value="<?php echo htmlspecialchars($article['meta_title']); ?>" 
                                   placeholder="Custom title for search engines (optional)...">
                            <p class="form-hint">Leave empty to use article title.</p>
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2" 
                                      placeholder="Description for search engine results..."><?php echo htmlspecialchars($article['meta_description']); ?></textarea>
                            <p class="form-hint">Recommended length: 150-160 characters.</p>
                        </div>
                        <div class="form-group">
                            <label>Tags</label>
                            <input type="text" name="tags" class="form-control" 
                                   value="<?php echo htmlspecialchars($article['tags']); ?>" 
                                   placeholder="tag1, tag2, tag3...">
                            <p class="form-hint">Separate tags with commas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="editor-sidebar">
                <!-- Publish Card -->
                <div class="editor-card">
                    <div class="card-header">
                        <i class="fas fa-paper-plane"></i>
                        <h5>Publish</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Status</label>
                            <div class="select-wrapper">
                                <select name="status" class="form-control">
                                    <option value="draft" <?php echo $article['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo $article['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="archived" <?php echo $article['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Publish Date</label>
                            <input type="datetime-local" name="publish_date" class="form-control" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($article['publish_date'])); ?>">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" id="isFeatured" 
                                   <?php echo $article['is_featured'] ? 'checked' : ''; ?>>
                            <label for="isFeatured">
                                <i class="fas fa-star text-warning"></i> Feature this article
                            </label>
                        </div>
                        
                        <div class="editor-actions" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                <i class="fas fa-save"></i>
                                <?php echo $action === 'add' ? 'Create Article' : 'Update Article'; ?>
                            </button>
                            <?php if ($article['id'] > 0): ?>
                            <a href="../news_detail.php?slug=<?php echo urlencode($article['slug']); ?>" target="_blank" class="btn btn-outline">
                                <i class="fas fa-external-link-alt"></i> Preview
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Category Card -->
                <div class="editor-card">
                    <div class="card-header">
                        <i class="fas fa-folder"></i>
                        <h5>Category</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="select-wrapper">
                                <select name="category" class="form-control" id="categorySelect">
                                    <?php foreach ($category_labels as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $article['category'] === $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Author</label>
                            <input type="text" name="author" class="form-control" 
                                   value="<?php echo htmlspecialchars($article['author']); ?>" 
                                   placeholder="Article author...">
                        </div>
                    </div>
                </div>

                <!-- Event Details Card (shows for events category) -->
                <div class="editor-card" id="eventDetailsCard" style="<?php echo $article['category'] !== 'events' ? 'display:none;' : ''; ?>">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt"></i>
                        <h5>Event Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Event Date</label>
                            <input type="date" name="event_date" class="form-control" 
                                   value="<?php echo $article['event_date'] ? date('Y-m-d', strtotime($article['event_date'])) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Event Time</label>
                            <input type="time" name="event_time" class="form-control" 
                                   value="<?php echo $article['event_time'] ? date('H:i', strtotime($article['event_time'])) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Event Location</label>
                            <input type="text" name="event_location" class="form-control" 
                                   value="<?php echo htmlspecialchars($article['event_location']); ?>" 
                                   placeholder="Venue or location...">
                        </div>
                    </div>
                </div>

                <!-- Featured Image Card -->
                <div class="editor-card">
                    <div class="card-header">
                        <i class="fas fa-image"></i>
                        <h5>Featured Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="image-upload-wrapper <?php echo !empty($article['featured_image']) ? 'has-image' : ''; ?>" id="imageWrapper">
                            <?php if (!empty($article['featured_image'])): ?>
                            <img src="../<?php echo htmlspecialchars($article['featured_image']); ?>" alt="Featured Image" class="image-preview" id="imagePreview">
                            <button type="button" class="remove-image" id="removeImage">
                                <i class="fas fa-times"></i>
                            </button>
                            <?php else: ?>
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Click to upload</strong> or drag and drop</p>
                                <p>PNG, JPG, GIF, WEBP up to 5MB</p>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="featured_image" accept="image/*" class="image-input" id="imageInput">
                        </div>
                        <p class="form-hint" style="margin-top: 12px;">Recommended size: 1200x630 pixels for optimal display.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
</div>

<script>
// Initialize Summernote Editor (Free, No API Required)
$('#contentEditor').summernote({
    height: 450,
    placeholder: 'Write your article content here...',
    tabsize: 2,
    toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['forecolor', 'backcolor']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ],
    fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact', 'Lucida Sans Unicode', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'],
    fontNamesIgnoreCheck: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact', 'Lucida Sans Unicode', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'],
    fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '28', '32', '36', '48', '64', '82', '150'],
    styleTags: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
    callbacks: {
        onImageUpload: function(files) {
            // Handle image upload - you can implement AJAX upload here
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onloadend = function() {
                    const img = $('<img>').attr('src', reader.result);
                    $('#contentEditor').summernote('insertNode', img[0]);
                };
                reader.readAsDataURL(files[i]);
            }
        }
    }
});

// Title to slug conversion
document.getElementById('articleTitle').addEventListener('input', function() {
    const title = this.value;
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slugPreview').textContent = slug || 'article-slug';
    document.getElementById('articleSlug').value = slug;
});

// Category change handler (show/hide event details)
document.getElementById('categorySelect').addEventListener('change', function() {
    const eventCard = document.getElementById('eventDetailsCard');
    eventCard.style.display = this.value === 'events' ? 'block' : 'none';
});

// Image upload & Drag and Drop functionalitity
const imageWrapper = document.getElementById('imageWrapper');
const imageInput = document.getElementById('imageInput');

if (imageWrapper && imageInput) {
    // Click to upload
    imageWrapper.addEventListener('click', (e) => {
        if (!e.target.closest('.remove-image')) {
            imageInput.click();
        }
    });

    // Drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        imageWrapper.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        imageWrapper.addEventListener(eventName, () => {
            imageWrapper.style.borderColor = '#f26838';
            imageWrapper.style.backgroundColor = 'rgba(242,104,56,0.05)';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        imageWrapper.addEventListener(eventName, () => {
            imageWrapper.style.borderColor = '';
            imageWrapper.style.backgroundColor = '';
        }, false);
    });

    imageWrapper.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            imageInput.files = files;
            handleImageChange(files[0]);
        }
    }

    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            handleImageChange(this.files[0]);
        }
    });

    function handleImageChange(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update UI without re-rendering everything
            imageWrapper.classList.add('has-image');
            
            // Remove the hidden removal input if it exists
            const removedInput = document.querySelector('input[name="remove_featured_image"]');
            if (removedInput) removedInput.remove();
            
            // Clear existing preview or placeholder
            const placeholder = document.getElementById('uploadPlaceholder');
            if (placeholder) placeholder.style.display = 'none';
            
            let preview = document.getElementById('imagePreview');
            let removeBtn = document.getElementById('removeBtn');
            
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'imagePreview';
                preview.className = 'image-preview';
                imageWrapper.prepend(preview);
            }
            
            if (!removeBtn) {
                removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.id = 'removeBtn';
                removeBtn.className = 'remove-image';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                imageWrapper.appendChild(removeBtn);
                
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeImage();
                });
            }
            
            preview.src = e.target.result;
            preview.style.display = 'block';
            removeBtn.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }

    function removeImage() {
        imageInput.value = '';
        imageWrapper.classList.remove('has-image');
        
        const preview = document.getElementById('imagePreview');
        const removeBtn = document.getElementById('removeBtn') || document.getElementById('removeImage');
        const placeholder = document.getElementById('uploadPlaceholder');
        
        if (preview) preview.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
    }

    // Initialize removal for existing images
    const existingRemoveBtn = document.getElementById('removeImage');
    if (existingRemoveBtn) {
        existingRemoveBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            removeImage();
            
            // Add a hidden input to signify removal if needed by backend
            if (!document.querySelector('input[name="remove_featured_image"]')) {
                const removedInput = document.createElement('input');
                removedInput.type = 'hidden';
                removedInput.name = 'remove_featured_image';
                removedInput.value = '1';
                imageWrapper.appendChild(removedInput);
            }
        });
    }
}

// Form submission - Summernote content is auto-synced
document.getElementById('articleForm').addEventListener('submit', function() {
    return true;
});
</script>

<?php include 'footer.php'; ?>
