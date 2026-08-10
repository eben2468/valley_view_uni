<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
require_once __DIR__ . '/../includes/admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'update_faq_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'faqs');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE faq_hero SET badge_text=?, title_black=?, title_gradient=?, description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['badge_text'], $_POST['title_black'], $_POST['title_gradient'], $_POST['description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "FAQ Hero updated successfully!";
        }
        
        elseif ($action === 'update_faq_trending') {
            $stmt = $pdo->prepare("UPDATE faq_trending SET icon=?, bg_color_class=?, icon_color_class=?, hover_bg_class=?, question=?, answer=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['bg_color_class'], $_POST['icon_color_class'], $_POST['hover_bg_class'], $_POST['question'], $_POST['answer'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Trending question updated successfully!";
        }
        
        elseif ($action === 'add_faq_trending') {
            $stmt = $pdo->prepare("INSERT INTO faq_trending (icon, bg_color_class, icon_color_class, hover_bg_class, question, answer, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['icon'], $_POST['bg_color_class'], $_POST['icon_color_class'], $_POST['hover_bg_class'], $_POST['question'], $_POST['answer'], $_POST['display_order']]);
            $success = "Trending question added successfully!";
        }

        elseif ($action === 'delete_faq_trending') {
            $stmt = $pdo->prepare("DELETE FROM faq_trending WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Trending question deleted!";
        }
        
        elseif ($action === 'update_faq_category') {
            $stmt = $pdo->prepare("UPDATE faq_categories SET category_name=?, category_slug=?, icon=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['category_name'], $_POST['category_slug'], $_POST['icon'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Category updated successfully!";
        }

        elseif ($action === 'add_faq_category') {
            $stmt = $pdo->prepare("INSERT INTO faq_categories (category_name, category_slug, icon, display_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['category_name'], $_POST['category_slug'], $_POST['icon'], $_POST['display_order']]);
            $success = "Category added successfully!";
        }
        
        elseif ($action === 'update_faq_item') {
            $stmt = $pdo->prepare("UPDATE faqs SET category_id=?, question=?, answer=?, icon=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['category_id'], $_POST['question'], $_POST['answer'], $_POST['icon'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "FAQ updated successfully!";
        }

        elseif ($action === 'add_faq_item') {
            $stmt = $pdo->prepare("INSERT INTO faqs (category_id, question, answer, icon, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['category_id'], $_POST['question'], $_POST['answer'], $_POST['icon'], $_POST['display_order']]);
            $success = "FAQ added successfully!";
        }

        elseif ($action === 'delete_faq_item') {
            $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "FAQ deleted!";
        }
        
        elseif ($action === 'update_faq_doc') {
            $stmt = $pdo->prepare("UPDATE faq_docs SET title=?, file_info=?, icon=?, file_url=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['file_info'], $_POST['icon'], $_POST['file_url'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Document updated successfully!";
        }

        elseif ($action === 'add_faq_doc') {
            $stmt = $pdo->prepare("INSERT INTO faq_docs (title, file_info, icon, file_url, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['title'], $_POST['file_info'], $_POST['icon'], $_POST['file_url'], $_POST['display_order']]);
            $success = "Document added successfully!";
        }
        
        elseif ($action === 'update_faq_support') {
            $stmt = $pdo->prepare("UPDATE faq_support SET title=?, description=?, icon=?, icon_bg_color=?, btn_text=?, btn_link=?, btn_color_class=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['icon'], $_POST['icon_bg_color'], $_POST['btn_text'], $_POST['btn_link'], $_POST['btn_color_class'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Support resource updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch all data
$faq_hero = $pdo->query("SELECT * FROM faq_hero ORDER BY id DESC LIMIT 1")->fetch();
$faq_trending = $pdo->query("SELECT * FROM faq_trending ORDER BY display_order ASC")->fetchAll();
$faq_categories = $pdo->query("SELECT * FROM faq_categories ORDER BY display_order ASC")->fetchAll();
$faqs_all = $pdo->query("SELECT f.*, c.category_name FROM faqs f JOIN faq_categories c ON f.category_id = c.id ORDER BY f.category_id, f.display_order ASC")->fetchAll();
$faq_docs = $pdo->query("SELECT * FROM faq_docs ORDER BY display_order ASC")->fetchAll();
$faq_support = $pdo->query("SELECT * FROM faq_support ORDER BY display_order ASC")->fetchAll();

include 'header.php';
include 'sidebar.php';
?>

<!-- Custom Styling -->
<style>
    :root {
        --vvu-blue: #003366;
        --vvu-gold: #ffcc00;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }
    
    .main-content {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding-bottom: 50px;
    }

    .page-header {
        background: linear-gradient(135deg, var(--vvu-blue) 0%, #004080 100%);
        padding: 40px;
        border-radius: 20px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 51, 102, 0.1);
    }

    .page-header h2 {
        font-weight: 800;
        letter-spacing: -1px;
    }

    /* Tabs Styling */
    .nav-tabs {
        border: none;
        gap: 10px;
        margin-bottom: 20px;
        padding: 10px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-tabs .nav-link i {
        font-size: 1.1rem;
    }

    .nav-tabs .nav-link:hover {
        background: #f8f9fa;
        color: var(--vvu-blue);
    }

    .nav-tabs .nav-link.active {
        background: var(--vvu-blue);
        color: white;
        box-shadow: 0 8px 20px rgba(0, 51, 102, 0.2);
    }

    /* Card Styling */
    .dashboard-card {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03);
        margin-bottom: 30px;
        transition: transform 0.3s ease;
    }

    .dashboard-card h4 {
        font-weight: 700;
        color: var(--vvu-blue);
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f1f1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Form Elements */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1.5px solid #e9ecef;
        background-color: #fcfcfc;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--vvu-blue);
        box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05);
        background-color: white;
    }

    .btn-primary {
        background: var(--vvu-blue);
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #002244;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
    }

    .btn-success {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
    }

    /* Form item containers (for lists) */
    .faq-edit-item {
        background: #fbfbfb;
        border: 1px solid #eee;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .faq-edit-item:hover {
        border-color: var(--vvu-blue);
        background: white;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    /* Table styling */
    .table thead th {
        background: #f8f9fa;
        color: #6c757d;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 15px;
        border: none;
    }

    .table td {
        padding: 15px;
        vertical-align: middle;
        border-color: #f1f1f1;
    }

    /* Badge labels */
    .field-badge {
        font-size: 0.75rem;
        background: #e9ecef;
        color: #495057;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
    }
</style>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h2><i class="fas fa-question-circle me-3"></i>Manage FAQ Page Content</h2>
        <p class="opacity-75 mb-0">Total control over hero, trending questions, categories, and faq items</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="fa fa-check-circle fs-4 me-3"></i>
            <div>
                <strong>Success!</strong><br>
                <?php echo htmlspecialchars($success); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="fa fa-exclamation-circle fs-4 me-3"></i>
            <div>
                <strong>Error Occurred</strong><br>
                <?php echo htmlspecialchars($error); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="faqTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-hero">
                <i class="fas fa-image"></i> Hero Section
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-trending">
                <i class="fas fa-fire"></i> Trending
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-categories">
                <i class="fas fa-tags"></i> Categories
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-questions">
                <i class="fas fa-question"></i> Questions
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-docs">
                <i class="fas fa-file-pdf"></i> Documents
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-support">
                <i class="fas fa-headset"></i> Support
            </button>
        </li>
    </ul>

    <div class="tab-content pt-4" id="faqTabContent">
        <!-- Hero Section Tab -->
        <div class="tab-pane fade show active" id="tab-hero">
            <div class="dashboard-card">
                <div class="inn-title"><h4><i class="fas fa-magic me-2"></i>Edit Hero Content</h4></div>
                <?php if ($faq_hero): ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_faq_hero">
                    <input type="hidden" name="id" value="<?php echo $faq_hero['id']; ?>">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Badge Text</label>
                            <input type="text" name="badge_text" class="form-control" value="<?php echo htmlspecialchars($faq_hero['badge_text']); ?>" placeholder="e.g. Support Center">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title (Main)</label>
                            <input type="text" name="title_black" class="form-control" value="<?php echo htmlspecialchars($faq_hero['title_black']); ?>" placeholder="Frequently Asked">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title (Highlighted)</label>
                            <input type="text" name="title_gradient" class="form-control" value="<?php echo htmlspecialchars($faq_hero['title_gradient']); ?>" placeholder="Questions">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Welcome Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="A short welcoming text..."><?php echo htmlspecialchars($faq_hero['description']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 border rounded-4 bg-light">
                                <label class="form-label d-block mb-3"><i class="fas fa-link me-2"></i>Background Image URL</label>
                                <input type="text" name="hero_image_url" class="form-control mb-3" value="<?php echo htmlspecialchars($faq_hero['hero_image_url']); ?>">
                                <small class="text-muted">Current URL: <code><?php echo htmlspecialchars($faq_hero['hero_image_url']); ?></code></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 border rounded-4 bg-light">
                                <label class="form-label d-block mb-3"><i class="fas fa-upload me-2"></i>Upload New Image</label>
                                <input type="file" name="hero_image_file" class="form-control mb-3">
                                <small class="text-info"><i class="fas fa-info-circle me-1"></i>Uploading a file will automatically update the URL above.</small>
                            </div>
                        </div>
                        <div class="col-md-12 pt-3">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-save me-2"></i>Update Hero Section
                            </button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Trending Questions Tab -->
        <div class="tab-pane fade" id="tab-trending">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-chart-line me-2"></i>Trending Questions</h4>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTrendingModal">
                        <i class="fas fa-plus me-2"></i>Add Question
                    </button>
                </div>
                <div class="row px-3">
                    <?php foreach ($faq_trending as $trend): ?>
                    <div class="col-md-12 faq-edit-item">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_faq_trending">
                            <input type="hidden" name="id" value="<?php echo $trend['id']; ?>">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Material Icon Name</label>
                                    <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($trend['icon']); ?>">
                                    <small class="text-muted">e.g. event_available</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Background Class</label>
                                    <input type="text" name="bg_color_class" class="form-control" value="<?php echo htmlspecialchars($trend['bg_color_class']); ?>">
                                    <small class="text-muted">Tailwind: bg-blue-50</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Icon Color Class</label>
                                    <input type="text" name="icon_color_class" class="form-control" value="<?php echo htmlspecialchars($trend['icon_color_class']); ?>">
                                    <small class="text-muted">Tailwind: text-blue-600</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="display_order" class="form-control" value="<?php echo $trend['display_order']; ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Question</label>
                                    <input type="text" name="question" class="form-control fw-bold" value="<?php echo htmlspecialchars($trend['question']); ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Short Answer Snippet</label>
                                    <textarea name="answer" class="form-control" rows="2"><?php echo htmlspecialchars($trend['answer']); ?></textarea>
                                </div>
                                <div class="col-md-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                        <i class="fas fa-check me-2"></i>Save Changes
                                    </button>
                                    <button type="submit" name="action" value="delete_faq_trending" class="btn btn-outline-danger px-4" onclick="return confirm('Archive this question?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div class="tab-pane fade" id="tab-categories">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-tags me-2"></i>FAQ Categories</h4>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i>New Category
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th width="25%">Category Name</th>
                                <th width="20%">Slug / Link</th>
                                <th width="20%">Icon (Material)</th>
                                <th width="15%">Order</th>
                                <th width="20%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faq_categories as $cat): ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_faq_category">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <td>
                                        <input type="text" name="category_name" class="form-control fw-bold" value="<?php echo htmlspecialchars($cat['category_name']); ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="category_slug" class="form-control text-muted" value="<?php echo htmlspecialchars($cat['category_slug']); ?>">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><span class="material-symbols-outlined"><?php echo htmlspecialchars($cat['icon']); ?></span></span>
                                            <input type="text" name="icon" class="form-control border-start-0 ps-0" value="<?php echo htmlspecialchars($cat['icon']); ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" name="display_order" class="form-control" value="<?php echo $cat['display_order']; ?>">
                                    </td>
                                    <td class="text-end">
                                        <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                                            <i class="fas fa-save me-1"></i>Save
                                        </button>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Questions Tab -->
        <div class="tab-pane fade" id="tab-questions">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-clipboard-question me-2"></i>Knowledge Base (FAQs)</h4>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFAQModal">
                        <i class="fas fa-plus me-2"></i>New Question
                    </button>
                </div>
                <div class="row px-3">
                    <?php foreach ($faqs_all as $faq): ?>
                    <div class="col-md-12 faq-edit-item">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_faq_item">
                            <input type="hidden" name="id" value="<?php echo $faq['id']; ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select border-primary-subtle">
                                        <?php foreach ($faq_categories as $c): ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo $c['id'] == $faq['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c['category_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Material Icon</label>
                                    <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($faq['icon']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="display_order" class="form-control" value="<?php echo $faq['display_order']; ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">FAQ Question</label>
                                    <input type="text" name="question" class="form-control fw-bold border-0 bg-white fs-5" value="<?php echo htmlspecialchars($faq['question']); ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-primary">Detailed Answer</label>
                                    <textarea name="answer" class="form-control" rows="4"><?php echo htmlspecialchars($faq['answer']); ?></textarea>
                                </div>
                                <div class="col-md-12 mt-4 d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                        <i class="fas fa-save me-2"></i>Update FAQ
                                    </button>
                                    <button type="submit" name="action" value="delete_faq_item" class="btn btn-outline-danger" onclick="return confirm('Archive this FAQ?')">
                                        <i class="fas fa-trash me-2"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Documents Tab -->
        <div class="tab-pane fade" id="tab-docs">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-file-invoice me-2"></i>Document Center</h4>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDocModal">
                        <i class="fas fa-plus me-2"></i>Add Document
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th width="25%">Document Title</th>
                                <th width="20%">Info (e.g. PDF • 1MB)</th>
                                <th width="15%">Icon (Material)</th>
                                <th width="25%">Download URL</th>
                                <th width="15%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faq_docs as $doc): ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_faq_doc">
                                    <input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
                                    <td><input type="text" name="title" class="form-control fw-bold" value="<?php echo htmlspecialchars($doc['title']); ?>"></td>
                                    <td><input type="text" name="file_info" class="form-control text-muted" value="<?php echo htmlspecialchars($doc['file_info']); ?>"></td>
                                    <td><input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($doc['icon']); ?>"></td>
                                    <td><input type="text" name="file_url" class="form-control" value="<?php echo htmlspecialchars($doc['file_url']); ?>"></td>
                                    <td class="text-end">
                                        <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                                            <i class="fas fa-save me-1"></i>Save
                                        </button>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Support Tab -->
        <div class="tab-pane fade" id="tab-support">
            <div class="dashboard-card">
                <div class="inn-title"><h4><i class="fas fa-headset me-2"></i>Help & Support Resources</h4></div>
                <div class="row px-3">
                    <?php foreach ($faq_support as $sup): ?>
                    <div class="col-md-12 faq-edit-item">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_faq_support">
                            <input type="hidden" name="id" value="<?php echo $sup['id']; ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Resource Title</label>
                                    <input type="text" name="title" class="form-control fw-bold" value="<?php echo htmlspecialchars($sup['title']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Material Icon</label>
                                    <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($sup['icon']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Icon Background (Tailwind)</label>
                                    <input type="text" name="icon_bg_color" class="form-control" value="<?php echo htmlspecialchars($sup['icon_bg_color']); ?>">
                                    <small class="text-muted">e.g. bg-yellow-400</small>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-primary">Resource Description</label>
                                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($sup['description']); ?></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" name="btn_text" class="form-control" value="<?php echo htmlspecialchars($sup['btn_text']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Button Link</label>
                                    <input type="text" name="btn_link" class="form-control" value="<?php echo htmlspecialchars($sup['btn_link']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Button Class</label>
                                    <input type="text" name="btn_color_class" class="form-control" value="<?php echo htmlspecialchars($sup['btn_color_class']); ?>">
                                    <small class="text-muted">e.g. text-yellow-400</small>
                                </div>
                                <div class="col-md-12 mt-3 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                        <i class="fas fa-save me-2"></i>Update Resource
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modals for adding items -->
<div class="modal fade" id="addFAQModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_faq_item">
            <div class="modal-header bg-primary text-white border-0 py-4 px-5 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New FAQ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label">Select Category</label>
                        <select name="category_id" class="form-select bg-light" required>
                            <?php foreach ($faq_categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Question</label>
                        <input type="text" name="question" class="form-control bg-light" placeholder="What is your question?" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Answer</label>
                        <textarea name="answer" class="form-control bg-light" rows="4" placeholder="Provide a detailed answer..." required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Material Icon Name</label>
                        <input type="text" name="icon" class="form-control bg-light" value="verified">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control bg-light" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Save Question</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_faq_category">
            <div class="modal-header bg-primary text-white border-0 py-4 px-5 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-tag me-2"></i>Add Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="mb-4">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="category_name" class="form-control bg-light" placeholder="e.g. Admission" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">URL Slug</label>
                    <input type="text" name="category_slug" class="form-control bg-light" placeholder="admission (lowercase, no spaces)" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-0">
                        <label class="form-label">Icon Name</label>
                        <input type="text" name="icon" class="form-control bg-light" value="grid_view">
                    </div>
                    <div class="col-6 mb-0">
                        <label class="form-label">Order</label>
                        <input type="number" name="display_order" class="form-control bg-light" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Create Category</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addTrendingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_faq_trending">
            <div class="modal-header bg-primary text-white border-0 py-4 px-5 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-bolt me-2"></i>Add Trending Question</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label">Question</label>
                        <input type="text" name="question" class="form-control bg-light" placeholder="e.g. Can I pay online?" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Brief Answer</label>
                        <textarea name="answer" class="form-control bg-light" rows="3" placeholder="Provide a short answer..." required></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Material Icon</label>
                        <input type="text" name="icon" class="form-control bg-light" value="trending_up">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">BG Class</label>
                        <input type="text" name="bg_color_class" class="form-control bg-light" value="bg-blue-50">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Icon Color</label>
                        <input type="text" name="icon_color_class" class="form-control bg-light" value="text-blue-600">
                    </div>
                    <input type="hidden" name="hover_bg_class" value="group-hover:bg-blue-600">
                    <div class="col-md-12 text-center pt-2">
                        <label class="form-label">Order</label>
                        <input type="number" name="display_order" class="form-control bg-light mx-auto" style="width: 100px;" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Save Trending</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addDocModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="action" value="add_faq_doc">
            <div class="modal-header bg-primary text-white border-0 py-4 px-5 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-circle-plus me-2"></i>Add Document</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="mb-4">
                    <label class="form-label">Document Title</label>
                    <input type="text" name="title" class="form-control bg-light" placeholder="e.g. Fee Structure 2024" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Info Snippet</label>
                    <input type="text" name="file_info" class="form-control bg-light" placeholder="PDF • 1.2 MB">
                </div>
                <div class="row">
                    <div class="col-6 mb-4">
                        <label class="form-label">Icon Name</label>
                        <input type="text" name="icon" class="form-control bg-light" value="picture_as_pdf">
                    </div>
                    <div class="col-6 mb-4">
                        <label class="form-label">Order</label>
                        <input type="number" name="display_order" class="form-control bg-light" value="0">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">File Link / URL</label>
                    <input type="text" name="file_url" class="form-control bg-light" value="#" placeholder="https://example.com/file.pdf">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Add Document</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
