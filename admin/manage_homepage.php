<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        // Common image upload handling
        $image_url = $_POST['image_url'] ?? '';
        
        // SLIDER ACTIONS
        if ($action === 'add_slider' || $action === 'edit_slider') {
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploaded = handleAdminFileUpload($_FILES['image_file'], 'sliders');
                if ($uploaded) $image_url = $uploaded;
            }
            
            if ($action === 'add_slider') {
                $stmt = $pdo->prepare("INSERT INTO homepage_sliders (image_url, title, highlight_text, description, button1_text, button1_link, button2_text, button2_link, button3_text, button3_link, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$image_url, $_POST['title'], $_POST['highlight_text'], $_POST['description'], $_POST['button1_text'], $_POST['button1_link'], $_POST['button2_text'], $_POST['button2_link'], $_POST['button3_text'], $_POST['button3_link'], $_POST['display_order'], $_POST['is_active'] ?? 1]);
                $success = "Slider added successfully!";
            } else {
                $stmt = $pdo->prepare("UPDATE homepage_sliders SET image_url=?, title=?, highlight_text=?, description=?, button1_text=?, button1_link=?, button2_text=?, button2_link=?, button3_text=?, button3_link=?, display_order=?, is_active=? WHERE id=?");
                $stmt->execute([$image_url, $_POST['title'], $_POST['highlight_text'], $_POST['description'], $_POST['button1_text'], $_POST['button1_link'], $_POST['button2_text'], $_POST['button2_link'], $_POST['button3_text'], $_POST['button3_link'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
                $success = "Slider updated successfully!";
            }
        }
        elseif ($action === 'delete_slider') {
            $stmt = $pdo->prepare("DELETE FROM homepage_sliders WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Slider deleted successfully!";
        }
        
        // DISCOVER CARD ACTIONS
        elseif ($action === 'add_discover_card' || $action === 'edit_discover_card') {
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploaded = handleAdminFileUpload($_FILES['image_file'], 'discover');
                if ($uploaded) $image_url = $uploaded;
            }
            
            if ($action === 'add_discover_card') {
                $stmt = $pdo->prepare("INSERT INTO homepage_discover_cards (image_url, title, link_url, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$image_url, $_POST['title'], $_POST['link_url'], $_POST['display_order'], $_POST['is_active'] ?? 1]);
                $success = "Discover card added successfully!";
            } else {
                $stmt = $pdo->prepare("UPDATE homepage_discover_cards SET image_url=?, title=?, link_url=?, display_order=?, is_active=? WHERE id=?");
                $stmt->execute([$image_url, $_POST['title'], $_POST['link_url'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
                $success = "Discover card updated successfully!";
            }
        }
        elseif ($action === 'delete_discover_card') {
            $stmt = $pdo->prepare("DELETE FROM homepage_discover_cards WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Discover card deleted successfully!";
        }
        
        // PROGRAM ACTIONS
        elseif ($action === 'add_program' || $action === 'edit_program') {
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploaded = handleAdminFileUpload($_FILES['image_file'], 'programs');
                if ($uploaded) $image_url = $uploaded;
            }
            
            if ($action === 'add_program') {
                $stmt = $pdo->prepare("INSERT INTO homepage_programs (image_url, title, category, description, rating, link_url, button1_text, button1_link, button2_text, button2_link, button3_text, button3_link, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$image_url, $_POST['title'], $_POST['category'], $_POST['description'], $_POST['rating'], $_POST['link_url'], $_POST['button1_text'], $_POST['button1_link'], $_POST['button2_text'], $_POST['button2_link'], $_POST['button3_text'], $_POST['button3_link'], $_POST['display_order'], $_POST['is_active'] ?? 1]);
                $success = "Program added successfully!";
            } else {
                $stmt = $pdo->prepare("UPDATE homepage_programs SET image_url=?, title=?, category=?, description=?, rating=?, link_url=?, button1_text=?, button1_link=?, button2_text=?, button2_link=?, button3_text=?, button3_link=?, display_order=?, is_active=? WHERE id=?");
                $stmt->execute([$image_url, $_POST['title'], $_POST['category'], $_POST['description'], $_POST['rating'], $_POST['link_url'], $_POST['button1_text'], $_POST['button1_link'], $_POST['button2_text'], $_POST['button2_link'], $_POST['button3_text'], $_POST['button3_link'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
                $success = "Program updated successfully!";
            }
        }
        elseif ($action === 'delete_program') {
            $stmt = $pdo->prepare("DELETE FROM homepage_programs WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Program deleted successfully!";
        }
        
        // GALLERY ACTIONS
        elseif ($action === 'add_gallery' || $action === 'edit_gallery') {
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploaded = handleAdminFileUpload($_FILES['image_file'], 'gallery');
                if ($uploaded) $image_url = $uploaded;
            }
            
            if ($action === 'add_gallery') {
                $stmt = $pdo->prepare("INSERT INTO homepage_gallery (image_url, caption, display_order, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([$image_url, $_POST['caption'], $_POST['display_order'], $_POST['is_active'] ?? 1]);
                $success = "Gallery image added successfully!";
            } else {
                $stmt = $pdo->prepare("UPDATE homepage_gallery SET image_url=?, caption=?, display_order=?, is_active=? WHERE id=?");
                $stmt->execute([$image_url, $_POST['caption'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
                $success = "Gallery image updated successfully!";
            }
        }
        elseif ($action === 'delete_gallery') {
            $stmt = $pdo->prepare("DELETE FROM homepage_gallery WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Gallery image deleted successfully!";
        }
        
        // NEWS ACTIONS
        elseif ($action === 'add_news') {
            $stmt = $pdo->prepare("INSERT INTO homepage_news (title, description, category, event_date, link_url, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['category'], $_POST['event_date'], $_POST['link_url'], $_POST['display_order'], $_POST['is_active'] ?? 1]);
            $success = "News item added successfully!";
        }
        elseif ($action === 'edit_news') {
            $stmt = $pdo->prepare("UPDATE homepage_news SET title=?, description=?, category=?, event_date=?, link_url=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['category'], $_POST['event_date'], $_POST['link_url'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "News item updated successfully!";
        }
        elseif ($action === 'delete_news') {
            $stmt = $pdo->prepare("DELETE FROM homepage_news WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "News item deleted successfully!";
        }
        
        // VIDEO ACTIONS
        elseif ($action === 'edit_video') {
            $stmt = $pdo->prepare("UPDATE homepage_video SET video_url=?, title=?, description=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['video_url'], $_POST['title'], $_POST['description'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Video updated successfully!";
        }
        
        // SECTION ACTIONS
        elseif ($action === 'edit_section') {
            $stmt = $pdo->prepare("UPDATE homepage_sections SET section_title=?, section_subtitle=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['section_title'], $_POST['section_subtitle'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Section updated successfully!";
        }

        // STATS BANNER ACTIONS
        elseif ($action === 'edit_stats_banner') {
            if (isset($_FILES['bg_image_file']) && $_FILES['bg_image_file']['error'] === UPLOAD_ERR_OK) {
                $uploaded = handleAdminFileUpload($_FILES['bg_image_file'], 'stats');
                if ($uploaded) $_POST['bg_image'] = $uploaded;
            }
            $stmt = $pdo->prepare("UPDATE homepage_stats_banner SET banner_text=?, bg_image=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['banner_text'], $_POST['bg_image'] ?? '', $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Stats banner updated successfully!";
        }
        elseif ($action === 'add_stats_item' || $action === 'edit_stats_item') {
            if ($action === 'add_stats_item') {
                $stmt = $pdo->prepare("INSERT INTO homepage_stats_items (label, value, display_order, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['label'], $_POST['value'], $_POST['display_order'], $_POST['is_active'] ?? 1]);
                $success = "Stats item added successfully!";
            } else {
                $stmt = $pdo->prepare("UPDATE homepage_stats_items SET label=?, value=?, display_order=?, is_active=? WHERE id=?");
                $stmt->execute([$_POST['label'], $_POST['value'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
                $success = "Stats item updated successfully!";
            }
        }
        elseif ($action === 'delete_stats_item') {
            $stmt = $pdo->prepare("DELETE FROM homepage_stats_items WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Stats item deleted successfully!";
        }

        // STUDY OPTION ACTIONS
        elseif ($action === 'add_study_option' || $action === 'edit_study_option') {
            if ($action === 'add_study_option') {
                $stmt = $pdo->prepare("INSERT INTO homepage_study_options (title, description, btn1_text, btn1_link, btn1_style, btn2_text, btn2_link, btn2_style, accent_color, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['title'], $_POST['description'], $_POST['btn1_text'], $_POST['btn1_link'], $_POST['btn1_style'], $_POST['btn2_text'], $_POST['btn2_link'], $_POST['btn2_style'], $_POST['accent_color'], $_POST['display_order'], $_POST['is_active'] ?? 1]);
                $success = "Study option added successfully!";
            } else {
                $stmt = $pdo->prepare("UPDATE homepage_study_options SET title=?, description=?, btn1_text=?, btn1_link=?, btn1_style=?, btn2_text=?, btn2_link=?, btn2_style=?, accent_color=?, display_order=?, is_active=? WHERE id=?");
                $stmt->execute([$_POST['title'], $_POST['description'], $_POST['btn1_text'], $_POST['btn1_link'], $_POST['btn1_style'], $_POST['btn2_text'], $_POST['btn2_link'], $_POST['btn2_style'], $_POST['accent_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
                $success = "Study option updated successfully!";
            }
        }
        elseif ($action === 'delete_study_option') {
            $stmt = $pdo->prepare("DELETE FROM homepage_study_options WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Study option deleted successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get edit ID from URL
$edit_id = $_GET['edit'] ?? null;
$edit_type = $_GET['type'] ?? null;

// Fetch data based on edit type
if ($edit_id && $edit_type) {
    $tables = [
        'slider' => 'homepage_sliders',
        'discover' => 'homepage_discover_cards',
        'program' => 'homepage_programs',
        'gallery' => 'homepage_gallery',
        'news' => 'homepage_news',
        'video' => 'homepage_video',
        'section' => 'homepage_sections',
        'stats_item' => 'homepage_stats_items',
        'study_option' => 'homepage_study_options'
    ];
    
    if (isset($tables[$edit_type])) {
        $stmt = $pdo->prepare("SELECT * FROM {$tables[$edit_type]} WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_data = $stmt->fetch();
    }
}

// Fetch all data
$sliders = $pdo->query("SELECT * FROM homepage_sliders ORDER BY display_order ASC")->fetchAll();
$discover_cards = $pdo->query("SELECT * FROM homepage_discover_cards ORDER BY display_order ASC")->fetchAll();
$programs = $pdo->query("SELECT * FROM homepage_programs ORDER BY display_order ASC")->fetchAll();
$gallery = $pdo->query("SELECT * FROM homepage_gallery ORDER BY display_order ASC")->fetchAll();
$news = $pdo->query("SELECT * FROM homepage_news ORDER BY display_order ASC")->fetchAll();
$video = $pdo->query("SELECT * FROM homepage_video WHERE id=1")->fetch();
$sections = $pdo->query("SELECT * FROM homepage_sections")->fetchAll();

$stats_banner = $pdo->query("SELECT * FROM homepage_stats_banner WHERE id=1")->fetch();
$stats_items = $pdo->query("SELECT * FROM homepage_stats_items ORDER BY display_order ASC")->fetchAll();
$study_options = $pdo->query("SELECT * FROM homepage_study_options ORDER BY display_order ASC")->fetchAll();
?>

<div class="sb2-2">
    <div class="sb2-2-2">
        <ul>
            <li><a href="../index.php"><i class="fa fa-home"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Manage Homepage</a></li>
        </ul>
    </div>
    
    <div class="sb2-2-1">
        <h2>Homepage Content Management</h2>
        <p>Edit all content displayed on the homepage</p>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#sliders" data-toggle="tab">Sliders</a></li>
            <li role="presentation"><a href="#discover" data-toggle="tab">Discover Cards</a></li>
            <li role="presentation"><a href="#programs" data-toggle="tab">Programs</a></li>
            <li role="presentation"><a href="#gallery" data-toggle="tab">Gallery</a></li>
            <li role="presentation"><a href="#news" data-toggle="tab">News</a></li>
            <li role="presentation"><a href="#video" data-toggle="tab">Video</a></li>
            <li role="presentation"><a href="#stats" data-toggle="tab">Stats Banner</a></li>
            <li role="presentation"><a href="#study" data-toggle="tab">Study Options</a></li>
            <li role="presentation"><a href="#sections" data-toggle="tab">Sections</a></li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- SLIDERS TAB -->
            <div role="tabpanel" class="tab-pane active" id="sliders">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4><?php echo $edit_type === 'slider' ? 'Edit Slider' : 'Add New Slider'; ?></h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $edit_type === 'slider' ? 'edit_slider' : 'add_slider'; ?>">
                            <?php if ($edit_type === 'slider'): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="image_url" value="<?php echo $edit_data['image_url'] ?? ''; ?>">
                                    <label>Image URL</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="file" name="image_file" accept="image/*">
                                    <p class="help-block">Or upload a file (overrides URL)</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" required>
                                    <label>Title</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="highlight_text" value="<?php echo $edit_data['highlight_text'] ?? ''; ?>">
                                    <label>Highlight Text</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="description" class="materialize-textarea"><?php echo $edit_data['description'] ?? ''; ?></textarea>
                                    <label>Description</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="button1_text" value="<?php echo $edit_data['button1_text'] ?? ''; ?>">
                                    <label>Button 1 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="button1_link" value="<?php echo $edit_data['button1_link'] ?? ''; ?>">
                                    <label>Button 1 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="button2_text" value="<?php echo $edit_data['button2_text'] ?? ''; ?>">
                                    <label>Button 2 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="button2_link" value="<?php echo $edit_data['button2_link'] ?? ''; ?>">
                                    <label>Button 2 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="button3_text" value="<?php echo $edit_data['button3_text'] ?? ''; ?>">
                                    <label>Button 3 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="button3_link" value="<?php echo $edit_data['button3_link'] ?? ''; ?>">
                                    <label>Button 3 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="number" name="display_order" value="<?php echo $edit_data['display_order'] ?? '0'; ?>" required>
                                    <label>Display Order</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($edit_data['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($edit_data['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">
                                        <?php echo $edit_type === 'slider' ? 'Update Slider' : 'Add Slider'; ?>
                                    </button>
                                    <?php if ($edit_type === 'slider'): ?>
                                        <a href="manage_homepage.php" class="waves-effect waves-light btn-large">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Sliders List -->
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>All Sliders</h4>
                    </div>
                    <div class="tab-inn">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sliders as $slider): ?>
                                <tr>
                                    <td><?php echo $slider['display_order']; ?></td>
                                    <td><img src="../<?php echo $slider['image_url']; ?>" style="width:100px; height:60px; object-fit:cover;" alt=""></td>
                                    <td><?php echo htmlspecialchars($slider['title']); ?></td>
                                    <td><?php echo $slider['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $slider['id']; ?>&type=slider" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this slider?');">
                                            <input type="hidden" name="action" value="delete_slider">
                                            <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- DISCOVER CARDS TAB -->
            <div role="tabpanel" class="tab-pane" id="discover">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4><?php echo $edit_type === 'discover' ? 'Edit Discover Card' : 'Add New Discover Card'; ?></h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $edit_type === 'discover' ? 'edit_discover_card' : 'add_discover_card'; ?>">
                            <?php if ($edit_type === 'discover'): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="image_url" value="<?php echo $edit_data['image_url'] ?? ''; ?>">
                                    <label>Image URL</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="file" name="image_file" accept="image/*">
                                    <p class="help-block">Or upload a file (overrides URL)</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" required>
                                    <label>Title</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="link_url" value="<?php echo $edit_data['link_url'] ?? ''; ?>" required>
                                    <label>Link URL</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="number" name="display_order" value="<?php echo $edit_data['display_order'] ?? '0'; ?>" required>
                                    <label>Display Order</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($edit_data['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($edit_data['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">
                                        <?php echo $edit_type === 'discover' ? 'Update Card' : 'Add Card'; ?>
                                    </button>
                                    <?php if ($edit_type === 'discover'): ?>
                                        <a href="manage_homepage.php" class="waves-effect waves-light btn-large">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Discover Cards List -->
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>All Discover Cards</h4>
                    </div>
                    <div class="tab-inn">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Link</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($discover_cards as $card): ?>
                                <tr>
                                    <td><?php echo $card['display_order']; ?></td>
                                    <td><img src="../<?php echo $card['image_url']; ?>" style="width:80px; height:80px; object-fit:cover;" alt=""></td>
                                    <td><?php echo htmlspecialchars($card['title']); ?></td>
                                    <td><?php echo htmlspecialchars($card['link_url']); ?></td>
                                    <td><?php echo $card['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $card['id']; ?>&type=discover#discover" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this card?');">
                                            <input type="hidden" name="action" value="delete_discover_card">
                                            <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- PROGRAMS TAB -->
            <div role="tabpanel" class="tab-pane" id="programs">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4><?php echo $edit_type === 'program' ? 'Edit Program' : 'Add New Program'; ?></h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $edit_type === 'program' ? 'edit_program' : 'add_program'; ?>">
                            <?php if ($edit_type === 'program'): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="image_url" value="<?php echo $edit_data['image_url'] ?? ''; ?>">
                                    <label>Image URL</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="file" name="image_file" accept="image/*">
                                    <p class="help-block">Or upload a file (overrides URL)</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" required>
                                    <label>Title</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="category" value="<?php echo $edit_data['category'] ?? ''; ?>">
                                    <label>Category</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="description" class="materialize-textarea"><?php echo $edit_data['description'] ?? ''; ?></textarea>
                                    <label>Description</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="number" step="0.1" name="rating" value="<?php echo $edit_data['rating'] ?? '4.5'; ?>">
                                    <label>Rating</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="number" name="display_order" value="<?php echo $edit_data['display_order'] ?? '0'; ?>" required>
                                    <label>Display Order</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="button1_text" value="<?php echo $edit_data['button1_text'] ?? 'Learn More'; ?>">
                                    <label>Button 1 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="button1_link" value="<?php echo $edit_data['button1_link'] ?? ''; ?>">
                                    <label>Button 1 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="button2_text" value="<?php echo $edit_data['button2_text'] ?? 'View Details'; ?>">
                                    <label>Button 2 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="button2_link" value="<?php echo $edit_data['button2_link'] ?? ''; ?>">
                                    <label>Button 2 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="button3_text" value="<?php echo $edit_data['button3_text'] ?? 'Apply'; ?>">
                                    <label>Button 3 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="button3_link" value="<?php echo $edit_data['button3_link'] ?? ''; ?>">
                                    <label>Button 3 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($edit_data['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($edit_data['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">
                                        <?php echo $edit_type === 'program' ? 'Update Program' : 'Add Program'; ?>
                                    </button>
                                    <?php if ($edit_type === 'program'): ?>
                                        <a href="manage_homepage.php" class="waves-effect waves-light btn-large">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>All Programs</h4>
                    </div>
                    <div class="tab-inn">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($programs as $prog): ?>
                                <tr>
                                    <td><?php echo $prog['display_order']; ?></td>
                                    <td><img src="../<?php echo $prog['image_url']; ?>" style="width:60px; height:60px; object-fit:cover;" alt=""></td>
                                    <td><?php echo htmlspecialchars($prog['title']); ?></td>
                                    <td><?php echo htmlspecialchars($prog['category']); ?></td>
                                    <td><?php echo $prog['rating']; ?></td>
                                    <td><?php echo $prog['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $prog['id']; ?>&type=program#programs" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this program?');">
                                            <input type="hidden" name="action" value="delete_program">
                                            <input type="hidden" name="id" value="<?php echo $prog['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- GALLERY TAB -->
            <div role="tabpanel" class="tab-pane" id="gallery">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4><?php echo $edit_type === 'gallery' ? 'Edit Gallery Image' : 'Add New Gallery Image'; ?></h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $edit_type === 'gallery' ? 'edit_gallery' : 'add_gallery'; ?>">
                            <?php if ($edit_type === 'gallery'): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="image_url" value="<?php echo $edit_data['image_url'] ?? ''; ?>">
                                    <label>Image URL</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="file" name="image_file" accept="image/*">
                                    <p class="help-block">Or upload a file (overrides URL)</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="caption" value="<?php echo $edit_data['caption'] ?? ''; ?>">
                                    <label>Caption</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="number" name="display_order" value="<?php echo $edit_data['display_order'] ?? '0'; ?>" required>
                                    <label>Display Order</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($edit_data['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($edit_data['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">
                                        <?php echo $edit_type === 'gallery' ? 'Update Image' : 'Add Image'; ?>
                                    </button>
                                    <?php if ($edit_type === 'gallery'): ?>
                                        <a href="manage_homepage.php" class="waves-effect waves-light btn-large">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>All Gallery Images</h4>
                    </div>
                    <div class="tab-inn">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Image</th>
                                    <th>Caption</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gallery as $img): ?>
                                <tr>
                                    <td><?php echo $img['display_order']; ?></td>
                                    <td><img src="../<?php echo $img['image_url']; ?>" style="width:80px; height:80px; object-fit:cover;" alt=""></td>
                                    <td><?php echo htmlspecialchars($img['caption']); ?></td>
                                    <td><?php echo $img['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $img['id']; ?>&type=gallery#gallery" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this image?');">
                                            <input type="hidden" name="action" value="delete_gallery">
                                            <input type="hidden" name="id" value="<?php echo $img['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- NEWS TAB -->
            <div role="tabpanel" class="tab-pane" id="news">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4><?php echo $edit_type === 'news' ? 'Edit News Item' : 'Add New News Item'; ?></h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12">
                            <input type="hidden" name="action" value="<?php echo $edit_type === 'news' ? 'edit_news' : 'add_news'; ?>">
                            <?php if ($edit_type === 'news'): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" required>
                                    <label>Title</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="description" class="materialize-textarea"><?php echo $edit_data['description'] ?? ''; ?></textarea>
                                    <label>Description</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="category" value="<?php echo $edit_data['category'] ?? ''; ?>">
                                    <label>Category</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="date" name="event_date" value="<?php echo $edit_data['event_date'] ?? ''; ?>">
                                    <label>Event Date</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="link_url" value="<?php echo $edit_data['link_url'] ?? ''; ?>">
                                    <label>Link URL</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="number" name="display_order" value="<?php echo $edit_data['display_order'] ?? '0'; ?>" required>
                                    <label>Display Order</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($edit_data['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($edit_data['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">
                                        <?php echo $edit_type === 'news' ? 'Update News' : 'Add News'; ?>
                                    </button>
                                    <?php if ($edit_type === 'news'): ?>
                                        <a href="manage_homepage.php" class="waves-effect waves-light btn-large">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>All News Items</h4>
                    </div>
                    <div class="tab-inn">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($news as $item): ?>
                                <tr>
                                    <td><?php echo $item['display_order']; ?></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td><?php echo date('d M, Y', strtotime($item['event_date'])); ?></td>
                                    <td><?php echo $item['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $item['id']; ?>&type=news#news" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this news item?');">
                                            <input type="hidden" name="action" value="delete_news">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- VIDEO TAB -->
            <div role="tabpanel" class="tab-pane" id="video">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>Edit Homepage Video</h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12">
                            <input type="hidden" name="action" value="edit_video">
                            <input type="hidden" name="id" value="<?php echo $video['id'] ?? 1; ?>">
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="video_url" value="<?php echo $video['video_url'] ?? ''; ?>" required>
                                    <label>Video URL (YouTube Embed)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="title" value="<?php echo $video['title'] ?? ''; ?>" required>
                                    <label>Title</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="description" class="materialize-textarea"><?php echo $video['description'] ?? ''; ?></textarea>
                                    <label>Description</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($video['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($video['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">Update Video</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- STATS BANNER TAB -->
            <div role="tabpanel" class="tab-pane" id="stats">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>Edit Stats Banner</h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="edit_stats_banner">
                            <input type="hidden" name="id" value="<?php echo $stats_banner['id'] ?? 1; ?>">
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="banner_text" class="materialize-textarea" required><?php echo $stats_banner['banner_text'] ?? ''; ?></textarea>
                                    <label>Banner Text</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="bg_image" value="<?php echo $stats_banner['bg_image'] ?? ''; ?>">
                                    <label>Background Image URL</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="file" name="bg_image_file" accept="image/*">
                                    <p class="help-block">Or upload a background image</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($stats_banner['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($stats_banner['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">Update Banner</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4><?php echo $edit_type === 'stats_item' ? 'Edit Stats Item' : 'Add Stats Item'; ?></h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12">
                            <input type="hidden" name="action" value="<?php echo $edit_type === 'stats_item' ? 'edit_stats_item' : 'add_stats_item'; ?>">
                            <?php if ($edit_type === 'stats_item'): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="label" value="<?php echo $edit_data['label'] ?? ''; ?>" required>
                                    <label>Label (e.g. Graduates)</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="value" value="<?php echo $edit_data['value'] ?? ''; ?>" required>
                                    <label>Value (e.g. 14098)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="number" name="display_order" value="<?php echo $edit_data['display_order'] ?? '0'; ?>" required>
                                    <label>Display Order</label>
                                </div>
                                <div class="input-field col s6">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($edit_data['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($edit_data['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">
                                        <?php echo $edit_type === 'stats_item' ? 'Update Item' : 'Add Item'; ?>
                                    </button>
                                    <?php if ($edit_type === 'stats_item'): ?>
                                        <a href="manage_homepage.php#stats" class="waves-effect waves-light btn-large">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>All Stats Items</h4>
                    </div>
                    <div class="tab-inn">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Label</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats_items as $item): ?>
                                <tr>
                                    <td><?php echo $item['display_order']; ?></td>
                                    <td><?php echo htmlspecialchars($item['label']); ?></td>
                                    <td><?php echo htmlspecialchars($item['value']); ?></td>
                                    <td><?php echo $item['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $item['id']; ?>&type=stats_item#stats" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this item?');">
                                            <input type="hidden" name="action" value="delete_stats_item">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- STUDY OPTIONS TAB -->
            <div role="tabpanel" class="tab-pane" id="study">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4><?php echo $edit_type === 'study_option' ? 'Edit Study Option' : 'Add Study Option'; ?></h4>
                    </div>
                    <div class="tab-inn">
                        <form method="POST" class="s12">
                            <input type="hidden" name="action" value="<?php echo $edit_type === 'study_option' ? 'edit_study_option' : 'add_study_option'; ?>">
                            <?php if ($edit_type === 'study_option'): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" required>
                                    <label>Section Title</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="description" class="materialize-textarea" required><?php echo $edit_data['description'] ?? ''; ?></textarea>
                                    <label>Description</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="btn1_text" value="<?php echo $edit_data['btn1_text'] ?? ''; ?>">
                                    <label>Button 1 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="btn1_link" value="<?php echo $edit_data['btn1_link'] ?? ''; ?>">
                                    <label>Button 1 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <select name="btn1_style" class="browser-default">
                                        <option value="outline" <?php echo ($edit_data['btn1_style'] ?? 'outline') == 'outline' ? 'selected' : ''; ?>>Outline</option>
                                        <option value="filled" <?php echo ($edit_data['btn1_style'] ?? 'outline') == 'filled' ? 'selected' : ''; ?>>Filled</option>
                                    </select>
                                    <label>Button 1 Style</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="accent_color" value="<?php echo $edit_data['accent_color'] ?? '#006B3F'; ?>">
                                    <label>Accent Color (Hex)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="btn2_text" value="<?php echo $edit_data['btn2_text'] ?? ''; ?>">
                                    <label>Button 2 Text</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="btn2_link" value="<?php echo $edit_data['btn2_link'] ?? ''; ?>">
                                    <label>Button 2 Link</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <select name="btn2_style" class="browser-default">
                                        <option value="filled" <?php echo ($edit_data['btn2_style'] ?? 'filled') == 'filled' ? 'selected' : ''; ?>>Filled</option>
                                        <option value="outline" <?php echo ($edit_data['btn2_style'] ?? 'filled') == 'outline' ? 'selected' : ''; ?>>Outline</option>
                                    </select>
                                    <label>Button 2 Style</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="number" name="display_order" value="<?php echo $edit_data['display_order'] ?? '0'; ?>" required>
                                    <label>Display Order</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo ($edit_data['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($edit_data['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn-large">
                                        <?php echo $edit_type === 'study_option' ? 'Update Option' : 'Add Option'; ?>
                                    </button>
                                    <?php if ($edit_type === 'study_option'): ?>
                                        <a href="manage_homepage.php#study" class="waves-effect waves-light btn-large">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>All Study Options</h4>
                    </div>
                    <div class="tab-inn">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Color</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($study_options as $option): ?>
                                <tr>
                                    <td><?php echo $option['display_order']; ?></td>
                                    <td><?php echo htmlspecialchars($option['title']); ?></td>
                                    <td><span style="display:inline-block; width:20px; height:20px; background:<?php echo $option['accent_color']; ?>; border:1px solid #ccc;"></span> <?php echo $option['accent_color']; ?></td>
                                    <td><?php echo $option['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>'; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $option['id']; ?>&type=study_option#study" class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this option?');">
                                            <input type="hidden" name="action" value="delete_study_option">
                                            <input type="hidden" name="id" value="<?php echo $option['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- SECTIONS TAB -->
            <div role="tabpanel" class="tab-pane" id="sections">
                <div class="box-inn-sp" style="margin-top: 20px;">
                    <div class="inn-title">
                        <h4>Edit Section Titles</h4>
                    </div>
                    <div class="tab-inn">
                        <?php foreach ($sections as $section): ?>
                        <form method="POST" class="s12" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd;">
                            <input type="hidden" name="action" value="edit_section">
                            <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                            
                            <h5><?php echo ucwords(str_replace('_', ' ', $section['section_key'])); ?></h5>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="section_title" value="<?php echo htmlspecialchars($section['section_title']); ?>" required>
                                    <label>Section Title (HTML allowed for <span> tags)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="section_subtitle" class="materialize-textarea"><?php echo htmlspecialchars($section['section_subtitle']); ?></textarea>
                                    <label>Section Subtitle</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="is_active" class="browser-default">
                                        <option value="1" <?php echo $section['is_active'] == 1 ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo $section['is_active'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button type="submit" class="waves-effect waves-light btn">Update Section</button>
                                </div>
                            </div>
                        </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
