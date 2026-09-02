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
        // ========================================
        // MISSION AND VISION ACTIONS
        // ========================================
        
        if ($action === 'update_mission_vision_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'about');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE mission_vision_hero SET page_subtitle=?, hero_title_1=?, hero_title_2=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title_1'], $_POST['hero_title_2'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Mission & Vision hero updated successfully!";
        }
        
        elseif ($action === 'update_mission_vision_card') {
            $stmt = $pdo->prepare("UPDATE mission_vision_cards SET card_type=?, icon=?, title=?, content=?, gradient_from=?, gradient_to=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['card_type'], $_POST['icon'], $_POST['title'], $_POST['content'], $_POST['gradient_from'], $_POST['gradient_to'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Mission/Vision card updated successfully!";
        }
        
        elseif ($action === 'update_pillar') {
            $stmt = $pdo->prepare("UPDATE mission_vision_pillars SET icon=?, title=?, description=?, icon_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['icon_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Pillar updated successfully!";
        }
        
        elseif ($action === 'update_environment') {
            $image_url = $_POST['image_url'];
            $uploaded = handleAdminFileUpload($_FILES['image_file'], 'about');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE mission_vision_environment SET badge_text=?, section_title=?, paragraph_1=?, paragraph_2=?, feature_1_title=?, feature_1_description=?, feature_2_title=?, feature_2_description=?, image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['badge_text'], $_POST['section_title'], $_POST['paragraph_1'], $_POST['paragraph_2'], $_POST['feature_1_title'], $_POST['feature_1_description'], $_POST['feature_2_title'], $_POST['feature_2_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Learning environment section updated successfully!";
        }
        
        // ---- Bottom call-to-action block ----
        elseif ($action === 'update_mission_vision_cta') {
            $stmt = $pdo->prepare("UPDATE mission_vision_cta SET heading=?, subtitle=?, primary_btn_text=?, primary_btn_link=?, primary_btn_icon=?, secondary_btn_text=?, secondary_btn_link=?, secondary_btn_icon=?, links_eyebrow=?, is_active=? WHERE id=?");
            $stmt->execute([
                $_POST['heading'], $_POST['subtitle'],
                $_POST['primary_btn_text'], $_POST['primary_btn_link'], $_POST['primary_btn_icon'],
                $_POST['secondary_btn_text'], $_POST['secondary_btn_link'], $_POST['secondary_btn_icon'],
                $_POST['links_eyebrow'], isset($_POST['is_active']) ? 1 : 0, $_POST['id']
            ]);
            $success = "Call-to-action section updated successfully!";
        }

        elseif ($action === 'update_mission_vision_cta_link') {
            $stmt = $pdo->prepare("UPDATE mission_vision_cta_links SET icon=?, title=?, description=?, link_url=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([
                $_POST['icon'], $_POST['title'], $_POST['description'], $_POST['link_url'],
                (int) $_POST['display_order'], isset($_POST['is_active']) ? 1 : 0, $_POST['id']
            ]);
            $success = "Quick link card updated successfully!";
        }

        elseif ($action === 'add_mission_vision_cta_link') {
            $stmt = $pdo->prepare("INSERT INTO mission_vision_cta_links (icon, title, description, link_url, display_order, is_active) VALUES (?, ?, ?, ?, (SELECT COALESCE(MAX(display_order),0)+1 FROM mission_vision_cta_links l2), 1)");
            $stmt->execute([
                $_POST['icon'] ?: 'star', $_POST['title'], $_POST['description'], $_POST['link_url'] ?: '#'
            ]);
            $success = "Quick link card added successfully!";
        }

        elseif ($action === 'delete_mission_vision_cta_link') {
            $pdo->prepare("DELETE FROM mission_vision_cta_links WHERE id=?")->execute([$_POST['id']]);
            $success = "Quick link card deleted successfully!";
        }

        // ========================================
        // CORE VALUES ACTIONS
        // ========================================

        elseif ($action === 'update_core_values_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'about');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE core_values_hero SET page_subtitle=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Core Values hero updated successfully!";
        }
        
        elseif ($action === 'update_core_values_pillar') {
            $stmt = $pdo->prepare("UPDATE core_values_pillars SET icon=?, title=?, description=?, feature_1=?, feature_2=?, quote=?, border_color=?, bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['feature_1'], $_POST['feature_2'], $_POST['quote'], $_POST['border_color'], $_POST['bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Core value pillar updated successfully!";
        }
        
        elseif ($action === 'update_core_values_action') {
            $stmt = $pdo->prepare("UPDATE core_values_actions SET icon=?, title=?, description=?, icon_bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['icon_bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Values in action card updated successfully!";
        }
        
        // ========================================
        // ANTHEM ACTIONS
        // ========================================
        
        elseif ($action === 'update_anthem_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'about');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE anthem_hero SET page_subtitle=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Anthem hero updated successfully!";
        }
        
        elseif ($action === 'update_anthem_stanza') {
            $stmt = $pdo->prepare("UPDATE anthem_stanzas SET stanza_number=?, stanza_title=?, content=?, border_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['stanza_number'], $_POST['stanza_title'], $_POST['content'], $_POST['border_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Anthem stanza updated successfully!";
        }
        
        elseif ($action === 'update_anthem_video') {
            $image_url = $_POST['video_poster_url'];
            $uploaded = handleAdminFileUpload($_FILES['video_poster_file'], 'about');
            if ($uploaded) $image_url = $uploaded;

            // The anthem plays either an audio recording or a video clip, and
            // both live in `video_url`. Keep whatever is stored unless a new
            // file actually arrives, so editing the description does not wipe
            // the recording.
            $media_url = $_POST['video_url'];
            if (isset($_FILES['anthem_media_file']) && $_FILES['anthem_media_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $media = $_FILES['anthem_media_file'];
                $is_video = strpos((string) vvu_detect_video_mime($media['tmp_name'], $media['name']), 'video/') === 0;
                $uploaded_media = $is_video
                    ? handleAdminVideoUpload($media, 'anthem', 'anthem_')
                    : handleAdminAudioUpload($media, 'anthem', 'anthem_');

                if ($uploaded_media) {
                    $media_url = $uploaded_media;
                } else {
                    $error = vvu_last_upload_error() ?: 'The anthem file could not be uploaded.';
                }
            }

            if ($error === '') {
                $stmt = $pdo->prepare("UPDATE anthem_video SET section_title=?, section_description=?, video_url=?, video_poster_url=?, is_active=? WHERE id=?");
                $stmt->execute([$_POST['section_title'], $_POST['section_description'], $media_url, $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
                $success = "Anthem video section updated successfully!";
            }
        }
        
        elseif ($action === 'update_anthem_about') {
            $stmt = $pdo->prepare("UPDATE anthem_about SET history_title=?, history_content=?, composer_title=?, composer_content=?, composer_name=?, composition_date=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['history_title'], $_POST['history_content'], $_POST['composer_title'], $_POST['composer_content'], $_POST['composer_name'], $_POST['composition_date'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Anthem about section updated successfully!";
        }
        
        elseif ($action === 'update_anthem_cta') {
            $stmt = $pdo->prepare("UPDATE anthem_cta SET title_line_1=?, title_line_2=?, description=?, btn1_text=?, btn1_url=?, btn1_icon=?, btn2_text=?, btn2_url=?, btn2_icon=?, stat1_value=?, stat1_label=?, stat2_value=?, stat2_label=?, stat3_value=?, stat3_label=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['title_line_1'], $_POST['title_line_2'], $_POST['description'], $_POST['btn1_text'], $_POST['btn1_url'], $_POST['btn1_icon'], $_POST['btn2_text'], $_POST['btn2_url'], $_POST['btn2_icon'], $_POST['stat1_value'], $_POST['stat1_label'], $_POST['stat2_value'], $_POST['stat2_label'], $_POST['stat3_value'], $_POST['stat3_label'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Anthem CTA section updated successfully!";
        }
        
        // ========================================
        // ECOLOGY ACTIONS
        // ========================================
        
        elseif ($action === 'update_ecology_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'about');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE ecology_hero SET page_subtitle=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Ecology hero updated successfully!";
        }
        
        elseif ($action === 'update_ecology_philosophy') {
            $stmt = $pdo->prepare("UPDATE ecology_philosophy SET icon=?, title=?, description=?, feature_1=?, feature_2=?, quote=?, border_color=?, bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['feature_1'], $_POST['feature_2'], $_POST['quote'], $_POST['border_color'], $_POST['bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Ecology philosophy updated successfully!";
        }
        
        elseif ($action === 'update_ecology_initiative') {
            $stmt = $pdo->prepare("UPDATE ecology_initiatives SET icon=?, title=?, description=?, icon_bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['icon_bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Ecology initiative updated successfully!";
        }
        
        elseif ($action === 'update_ecology_stat') {
            $stmt = $pdo->prepare("UPDATE ecology_stats SET stat_value=?, stat_label=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['stat_value'], $_POST['stat_label'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Ecology stat updated successfully!";
        }
        
        elseif ($action === 'update_ecology_cta') {
            $stmt = $pdo->prepare("UPDATE ecology_cta SET title_white=?, title_green=?, description=?, button_1_text=?, button_1_link=?, button_1_icon=?, button_2_text=?, button_2_link=?, button_2_icon=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['title_white'], $_POST['title_green'], $_POST['description'], $_POST['button_1_text'], $_POST['button_1_link'], $_POST['button_1_icon'], $_POST['button_2_text'], $_POST['button_2_link'], $_POST['button_2_icon'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Ecology CTA updated successfully!";
        }
        
        // ========================================
        // CAMPUS ACTIONS
        // ========================================
        
        elseif ($action === 'update_campus_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'], 'about');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE campus_hero SET page_subtitle=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Campus hero updated successfully!";
        }
        
        elseif ($action === 'update_campus_highlight') {
            $stmt = $pdo->prepare("UPDATE campus_highlights SET icon=?, title=?, description=?, quote=?, border_color=?, bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['quote'], $_POST['border_color'], $_POST['bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Campus highlight updated successfully!";
        }
        
        elseif ($action === 'update_campus_feature') {
            $stmt = $pdo->prepare("UPDATE campus_features SET icon=?, title=?, description=?, icon_bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['icon_bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Campus feature updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch all data
$mission_vision_hero = $pdo->query("SELECT * FROM mission_vision_hero ORDER BY id DESC LIMIT 1")->fetch();
$mission_vision_cards = $pdo->query("SELECT * FROM mission_vision_cards ORDER BY display_order ASC")->fetchAll();
$pillars = $pdo->query("SELECT * FROM mission_vision_pillars ORDER BY display_order ASC")->fetchAll();
$environment = $pdo->query("SELECT * FROM mission_vision_environment ORDER BY id DESC LIMIT 1")->fetch();

// Bottom call-to-action block. Wrapped because the tables only exist after
// install_mission_vision_cta.php has been run.
try {
    $mv_cta       = $pdo->query("SELECT * FROM mission_vision_cta ORDER BY id DESC LIMIT 1")->fetch();
    $mv_cta_links = $pdo->query("SELECT * FROM mission_vision_cta_links ORDER BY display_order ASC")->fetchAll();
} catch (PDOException $e) {
    $mv_cta = null;
    $mv_cta_links = [];
}

$core_values_hero = $pdo->query("SELECT * FROM core_values_hero ORDER BY id DESC LIMIT 1")->fetch();
$core_values_pillars = $pdo->query("SELECT * FROM core_values_pillars ORDER BY display_order ASC")->fetchAll();
$core_values_actions = $pdo->query("SELECT * FROM core_values_actions ORDER BY display_order ASC")->fetchAll();

$anthem_hero = $pdo->query("SELECT * FROM anthem_hero ORDER BY id DESC LIMIT 1")->fetch();
$anthem_stanzas = $pdo->query("SELECT * FROM anthem_stanzas ORDER BY display_order ASC")->fetchAll();
$anthem_video = $pdo->query("SELECT * FROM anthem_video ORDER BY id DESC LIMIT 1")->fetch();
$anthem_about = $pdo->query("SELECT * FROM anthem_about ORDER BY id DESC LIMIT 1")->fetch();
$anthem_cta = $pdo->query("SELECT * FROM anthem_cta ORDER BY id DESC LIMIT 1")->fetch();

$ecology_hero = $pdo->query("SELECT * FROM ecology_hero ORDER BY id DESC LIMIT 1")->fetch();
$ecology_philosophy = $pdo->query("SELECT * FROM ecology_philosophy ORDER BY display_order ASC")->fetchAll();
$ecology_initiatives = $pdo->query("SELECT * FROM ecology_initiatives ORDER BY display_order ASC")->fetchAll();
$ecology_stats = $pdo->query("SELECT * FROM ecology_stats ORDER BY display_order ASC")->fetchAll();
$ecology_cta = $pdo->query("SELECT * FROM ecology_cta ORDER BY id DESC LIMIT 1")->fetch();

$campus_hero = $pdo->query("SELECT * FROM campus_hero ORDER BY id DESC LIMIT 1")->fetch();
$campus_highlights = $pdo->query("SELECT * FROM campus_highlights ORDER BY display_order ASC")->fetchAll();
$campus_features = $pdo->query("SELECT * FROM campus_features ORDER BY display_order ASC")->fetchAll();

include 'header.php';
include 'sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h2>Manage About Pages Content</h2>
        <p>Edit all content for Mission & Vision, Core Values, VVU Anthem, Ecology, and The Campus pages</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fa fa-check-circle" style="margin-right: 8px;"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle" style="margin-right: 8px;"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="aboutPagesTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="mission-tab" data-bs-toggle="tab" data-bs-target="#mission_vision" type="button" role="tab">
                <i class="fa fa-flag" style="margin-right: 6px;"></i>Mission & Vision
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="values-tab" data-bs-toggle="tab" data-bs-target="#core_values" type="button" role="tab">
                <i class="fa fa-star" style="margin-right: 6px;"></i>Core Values
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="anthem-tab" data-bs-toggle="tab" data-bs-target="#anthem" type="button" role="tab">
                <i class="fa fa-music" style="margin-right: 6px;"></i>VVU Anthem
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ecology-tab" data-bs-toggle="tab" data-bs-target="#ecology" type="button" role="tab">
                <i class="fa fa-leaf" style="margin-right: 6px;"></i>Ecology
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="campus-tab" data-bs-toggle="tab" data-bs-target="#campus" type="button" role="tab">
                <i class="fa fa-building" style="margin-right: 6px;"></i>The Campus
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="aboutPagesTabContent">

<!-- MISSION & VISION TAB -->
<div class="tab-pane fade show active" id="mission_vision" role="tabpanel">
<div class="dashboard-card">

<!-- Hero Section -->
<div class="inn-title">
<h4><i class="fa fa-image" style="margin-right: 10px;"></i>Mission & Vision Hero Section</h4>
</div>
<?php if ($mission_vision_hero): ?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_mission_vision_hero">
<input type="hidden" name="id" value="<?php echo $mission_vision_hero['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label for="mv_page_subtitle" class="form-label">Page Subtitle</label>
<input type="text" name="page_subtitle" id="mv_page_subtitle" class="form-control" value="<?php echo htmlspecialchars($mission_vision_hero['page_subtitle']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label for="mv_hero_title_1" class="form-label">Hero Title 1</label>
<input type="text" name="hero_title_1" id="mv_hero_title_1" class="form-control" value="<?php echo htmlspecialchars($mission_vision_hero['hero_title_1']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label for="mv_hero_title_2" class="form-label">Hero Title 2</label>
<input type="text" name="hero_title_2" id="mv_hero_title_2" class="form-control" value="<?php echo htmlspecialchars($mission_vision_hero['hero_title_2']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label for="mv_hero_description" class="form-label">Hero Description</label>
<textarea name="hero_description" id="mv_hero_description" class="form-control" rows="4" required><?php echo htmlspecialchars($mission_vision_hero['hero_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label for="mv_hero_image_url" class="form-label">Hero Image URL</label>
<input type="text" name="hero_image_url" id="mv_hero_image_url" class="form-control" value="<?php echo htmlspecialchars($mission_vision_hero['hero_image_url']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label for="mv_hero_image_file" class="form-label">Or Upload Image</label>
<input type="file" name="hero_image_file" id="mv_hero_image_file" class="form-control" accept="image/*">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="mv_is_active" <?php echo $mission_vision_hero['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="mv_is_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Hero Section</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- Mission/Vision Cards -->
<div class="inn-title">
<h4><i class="fa fa-th-large" style="margin-right: 10px;"></i>Mission & Vision Cards</h4>
</div>
<?php foreach ($mission_vision_cards as $index => $card): ?>
<div class="form-card-title" style="background: #f8f9fa; padding: 12px 20px; margin: 20px 0 0 0; border-radius: 6px 6px 0 0; border-bottom: 2px solid #e0e0e0;">
<i class="fa fa-<?php echo $card['card_type'] == 'vision' ? 'eye' : 'flag'; ?>" style="margin-right: 8px; color: #667eea;"></i>
<?php echo ucfirst($card['card_type']); ?> Card
</div>
<form method="POST" class="row p-4 bg-white border border-top-0 rounded-bottom">
    <input type="hidden" name="action" value="update_mission_vision_card">
    <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
    
    <div class="col-md-4 mb-3">
        <label for="card_type_<?php echo $card['id']; ?>" class="form-label text-muted small fw-bold">Card Type</label>
        <select name="card_type" id="card_type_<?php echo $card['id']; ?>" class="form-select" required>
            <option value="vision" <?php echo $card['card_type'] == 'vision' ? 'selected' : ''; ?>>Vision</option>
            <option value="mission" <?php echo $card['card_type'] == 'mission' ? 'selected' : ''; ?>>Mission</option>
        </select>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="icon_<?php echo $card['id']; ?>" class="form-label text-muted small fw-bold">Icon (Material Symbol)</label>
        <input type="text" name="icon" id="icon_<?php echo $card['id']; ?>" class="form-control" value="<?php echo htmlspecialchars($card['icon']); ?>" required>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="display_order_<?php echo $card['id']; ?>" class="form-label text-muted small fw-bold">Display Order</label>
        <input type="number" name="display_order" id="display_order_<?php echo $card['id']; ?>" class="form-control" value="<?php echo $card['display_order']; ?>">
    </div>

    <div class="col-md-12 mb-3">
        <label for="title_<?php echo $card['id']; ?>" class="form-label text-muted small fw-bold">Title</label>
        <input type="text" name="title" id="title_<?php echo $card['id']; ?>" class="form-control" value="<?php echo htmlspecialchars($card['title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label for="content_<?php echo $card['id']; ?>" class="form-label text-muted small fw-bold">Content</label>
        <textarea name="content" id="content_<?php echo $card['id']; ?>" class="form-control" rows="4" required><?php echo htmlspecialchars($card['content']); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="gradient_from_<?php echo $card['id']; ?>" class="form-label text-muted small fw-bold">Gradient From (e.g., blue-600)</label>
        <input type="text" name="gradient_from" id="gradient_from_<?php echo $card['id']; ?>" class="form-control" value="<?php echo htmlspecialchars($card['gradient_from']); ?>">
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="gradient_to_<?php echo $card['id']; ?>" class="form-label text-muted small fw-bold">Gradient To (e.g., blue-800)</label>
        <input type="text" name="gradient_to" id="gradient_to_<?php echo $card['id']; ?>" class="form-control" value="<?php echo htmlspecialchars($card['gradient_to']); ?>">
    </div>
    
    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_<?php echo $card['id']; ?>" <?php echo $card['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_active_<?php echo $card['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary shadow-sm"><i class="fa fa-save me-2"></i>Update Card</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Four Pillars -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Four Pillars of Development</h4>
</div>
<?php foreach ($pillars as $pillar): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_pillar">
    <input type="hidden" name="id" value="<?php echo $pillar['id']; ?>">
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($pillar['icon']); ?>" required>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($pillar['title']); ?>" required>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Icon Color Class</label>
        <input type="text" name="icon_color" class="form-control" value="<?php echo htmlspecialchars($pillar['icon_color']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($pillar['description']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $pillar['display_order']; ?>">
    </div>
    
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="pillar_active_<?php echo $pillar['id']; ?>" <?php echo $pillar['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="pillar_active_<?php echo $pillar['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save me-2"></i>Update Pillar</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Learning Environment -->
<?php if ($environment): ?>
<div class="inn-title" style="margin-top: 40px;">
<h4>Learning Environment Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update_environment">
    <input type="hidden" name="id" value="<?php echo $environment['id']; ?>">
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Badge Text</label>
        <input type="text" name="badge_text" class="form-control" value="<?php echo htmlspecialchars($environment['badge_text']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Section Title</label>
        <input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($environment['section_title']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Paragraph 1</label>
        <textarea name="paragraph_1" class="form-control" rows="4" required><?php echo htmlspecialchars($environment['paragraph_1']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Paragraph 2</label>
        <textarea name="paragraph_2" class="form-control" rows="4" required><?php echo htmlspecialchars($environment['paragraph_2']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 1 Title</label>
        <input type="text" name="feature_1_title" class="form-control" value="<?php echo htmlspecialchars($environment['feature_1_title']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 1 Description</label>
        <input type="text" name="feature_1_description" class="form-control" value="<?php echo htmlspecialchars($environment['feature_1_description']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 2 Title</label>
        <input type="text" name="feature_2_title" class="form-control" value="<?php echo htmlspecialchars($environment['feature_2_title']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 2 Description</label>
        <input type="text" name="feature_2_description" class="form-control" value="<?php echo htmlspecialchars($environment['feature_2_description']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Image URL</label>
        <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($environment['image_url']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Or Upload Image</label>
        <input type="file" name="image_file" class="form-control" accept="image/*">
    </div>
    
    <div class="col-md-12 mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="env_active" <?php echo $environment['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="env_active">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Environment Section</button>
    </div>
</form>
<?php endif; ?>

<!-- ============================================================
     BOTTOM CALL-TO-ACTION ("Join Our Community of Excellence")
     ============================================================ -->
<?php if (!$mv_cta): ?>
    <div class="alert alert-warning" style="margin-top: 40px;">
        <i class="fa fa-triangle-exclamation me-2"></i>
        The call-to-action section is not installed yet. Run
        <code>install_mission_vision_cta.php</code> once, then reload this page.
    </div>
<?php else: ?>

<div class="inn-title" style="margin-top: 40px;">
    <h4>Call-to-Action Section (bottom of the page)</h4>
    <p class="text-muted mb-0">The blue "Join Our Community of Excellence" band, its two buttons, and the quick-link cards beneath it.</p>
</div>

<form method="POST" class="row p-4 bg-white border rounded shadow-sm">
    <input type="hidden" name="action" value="update_mission_vision_cta">
    <input type="hidden" name="id" value="<?php echo (int) $mv_cta['id']; ?>">

    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Heading</label>
        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($mv_cta['heading']); ?>" required>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Subtitle</label>
        <textarea name="subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($mv_cta['subtitle'] ?? ''); ?></textarea>
    </div>

    <div class="col-md-12"><hr><h6 class="fw-bold text-primary">Primary Button (yellow)</h6></div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Text</label>
        <input type="text" name="primary_btn_text" class="form-control" value="<?php echo htmlspecialchars($mv_cta['primary_btn_text'] ?? ''); ?>">
    </div>
    <div class="col-md-5 mb-3">
        <label class="form-label fw-bold">Link</label>
        <input type="text" name="primary_btn_link" class="form-control" value="<?php echo htmlspecialchars($mv_cta['primary_btn_link'] ?? ''); ?>" placeholder="about_us.php">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="primary_btn_icon" class="form-control" value="<?php echo htmlspecialchars($mv_cta['primary_btn_icon'] ?? ''); ?>" placeholder="info">
        <div class="form-text small">Material Symbols name</div>
    </div>

    <div class="col-md-12"><hr><h6 class="fw-bold text-primary">Secondary Button (outlined)</h6></div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Text</label>
        <input type="text" name="secondary_btn_text" class="form-control" value="<?php echo htmlspecialchars($mv_cta['secondary_btn_text'] ?? ''); ?>">
    </div>
    <div class="col-md-5 mb-3">
        <label class="form-label fw-bold">Link</label>
        <input type="text" name="secondary_btn_link" class="form-control" value="<?php echo htmlspecialchars($mv_cta['secondary_btn_link'] ?? ''); ?>" placeholder="apply.php">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="secondary_btn_icon" class="form-control" value="<?php echo htmlspecialchars($mv_cta['secondary_btn_icon'] ?? ''); ?>" placeholder="how_to_reg">
    </div>

    <div class="col-md-12"><hr></div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Quick Links Heading</label>
        <input type="text" name="links_eyebrow" class="form-control" value="<?php echo htmlspecialchars($mv_cta['links_eyebrow'] ?? ''); ?>" placeholder="Explore More">
        <div class="form-text small">Small yellow label above the cards.</div>
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="mv_cta_active" <?php echo $mv_cta['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="mv_cta_active">Show this section on the page</label>
        </div>
    </div>

    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Call-to-Action</button>
    </div>
</form>

<!-- Quick link cards -->
<div class="d-flex justify-content-between align-items-center" style="margin-top: 35px;">
    <div class="inn-title mb-0">
        <h4>Quick Link Cards</h4>
        <p class="text-muted mb-0">The cards under the buttons (Our Core Values, Academic Programs, Visit Our Campus).</p>
    </div>
</div>

<?php foreach ($mv_cta_links as $cta_link): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mt-3">
    <input type="hidden" name="action" value="update_mission_vision_cta_link">
    <input type="hidden" name="id" value="<?php echo (int) $cta_link['id']; ?>">

    <div class="col-md-5 mb-3">
        <label class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control fw-bold" value="<?php echo htmlspecialchars($cta_link['title']); ?>" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($cta_link['icon']); ?>" placeholder="star">
        <div class="form-text small"><a href="https://fonts.google.com/icons" target="_blank" rel="noopener">Material Symbols</a> name</div>
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label fw-bold">Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo (int) $cta_link['display_order']; ?>" min="0">
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="cta_link_active_<?php echo (int) $cta_link['id']; ?>" <?php echo $cta_link['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="cta_link_active_<?php echo (int) $cta_link['id']; ?>">Active</label>
        </div>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($cta_link['description'] ?? ''); ?>" placeholder="One short line explaining the link">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Link URL</label>
        <input type="text" name="link_url" class="form-control" value="<?php echo htmlspecialchars($cta_link['link_url']); ?>" placeholder="core_values.php">
    </div>

    <div class="col-md-12 d-flex justify-content-between border-top pt-3">
        <button type="submit" form="delete_cta_link_<?php echo (int) $cta_link['id']; ?>" class="btn btn-outline-danger btn-sm"
                onclick="return confirm('Delete this quick link card?');">
            <i class="fa fa-trash me-1"></i>Delete
        </button>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save me-2"></i>Update Card</button>
    </div>
</form>
<!-- Separate form so the delete button can't submit the edit fields -->
<form method="POST" id="delete_cta_link_<?php echo (int) $cta_link['id']; ?>" class="d-none">
    <input type="hidden" name="action" value="delete_mission_vision_cta_link">
    <input type="hidden" name="id" value="<?php echo (int) $cta_link['id']; ?>">
</form>
<?php endforeach; ?>

<form method="POST" class="row p-4 bg-light border rounded shadow-sm mt-3">
    <input type="hidden" name="action" value="add_mission_vision_cta_link">
    <div class="col-md-12 mb-3"><h6 class="fw-bold text-success mb-0"><i class="fa fa-plus me-2"></i>Add a Quick Link Card</h6></div>
    <div class="col-md-5 mb-3">
        <label class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control" required placeholder="e.g. Student Life">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="icon" class="form-control" placeholder="star">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Link URL</label>
        <input type="text" name="link_url" class="form-control" placeholder="student_life.php">
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <input type="text" name="description" class="form-control" placeholder="One short line explaining the link">
    </div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-plus me-2"></i>Add Card</button>
    </div>
</form>

<?php endif; ?>
</div>
</div>

<!-- CORE VALUES TAB -->
<div class="tab-pane fade" id="core_values" role="tabpanel">
<div class="dashboard-card">
<!-- Hero -->
<?php if ($core_values_hero): ?>
<div class="inn-title">
<h4>Core Values Hero Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update_core_values_hero">
    <input type="hidden" name="id" value="<?php echo $core_values_hero['id']; ?>">
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Page Subtitle</label>
        <input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($core_values_hero['page_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Title</label>
        <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($core_values_hero['hero_title']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Subtitle</label>
        <input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($core_values_hero['hero_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Hero Description</label>
        <textarea name="hero_description" class="form-control" rows="4" required><?php echo htmlspecialchars($core_values_hero['hero_description']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Image URL</label>
        <input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($core_values_hero['hero_image_url']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Or Upload Image</label>
        <input type="file" name="hero_image_file" class="form-control" accept="image/*">
    </div>
    
    <div class="col-md-12 mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="cv_hero_active" <?php echo $core_values_hero['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="cv_hero_active">Active</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Hero Section</button>
    </div>
</form>
<?php endif; ?>

<!-- Three Pillars -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Core Values Pillars (Excellence, Integrity, Service)</h4>
</div>
<?php foreach ($core_values_pillars as $pillar): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_core_values_pillar">
    <input type="hidden" name="id" value="<?php echo $pillar['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($pillar['icon']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($pillar['title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($pillar['description']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 1</label>
        <input type="text" name="feature_1" class="form-control" value="<?php echo htmlspecialchars($pillar['feature_1']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 2</label>
        <input type="text" name="feature_2" class="form-control" value="<?php echo htmlspecialchars($pillar['feature_2']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Quote</label>
        <textarea name="quote" class="form-control" rows="2"><?php echo htmlspecialchars($pillar['quote']); ?></textarea>
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Border Color</label>
        <input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($pillar['border_color']); ?>">
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Background Color</label>
        <input type="text" name="bg_color" class="form-control" value="<?php echo htmlspecialchars($pillar['bg_color']); ?>">
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $pillar['display_order']; ?>">
    </div>
    
    <div class="col-md-3 mb-3 d-flex align-items-end px-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="cv_pillar_active_<?php echo $pillar['id']; ?>" <?php echo $pillar['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="cv_pillar_active_<?php echo $pillar['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary btn-sm px-4"><i class="fa fa-save me-2"></i>Update Pillar</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Values in Action -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Living Our Values (6 Cards)</h4>
</div>
<?php foreach ($core_values_actions as $action_card): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_core_values_action">
    <input type="hidden" name="id" value="<?php echo $action_card['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($action_card['icon']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($action_card['title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($action_card['description']); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Icon Background Color</label>
        <input type="text" name="icon_bg_color" class="form-control" value="<?php echo htmlspecialchars($action_card['icon_bg_color']); ?>">
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $action_card['display_order']; ?>">
    </div>
    
    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="action_active_<?php echo $action_card['id']; ?>" <?php echo $action_card['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="action_active_<?php echo $action_card['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save me-2"></i>Update Card</button>
    </div>
</form>
<?php endforeach; ?>
</div>
</div>

<!-- VVU ANTHEM TAB -->
<div class="tab-pane fade" id="anthem" role="tabpanel">
<div class="dashboard-card">
<!-- Hero -->
<?php if ($anthem_hero): ?>
<div class="inn-title">
<h4>VVU Anthem Hero Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update_anthem_hero">
    <input type="hidden" name="id" value="<?php echo $anthem_hero['id']; ?>">
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Page Subtitle</label>
        <input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($anthem_hero['page_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Title</label>
        <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($anthem_hero['hero_title']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Subtitle</label>
        <input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($anthem_hero['hero_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Hero Description</label>
        <textarea name="hero_description" class="form-control" rows="4" required><?php echo htmlspecialchars($anthem_hero['hero_description']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Image URL</label>
        <input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($anthem_hero['hero_image_url']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Or Upload Image</label>
        <input type="file" name="hero_image_file" class="form-control" accept="image/*">
    </div>
    
    <div class="col-md-12">
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="anthem_hero_active" <?php echo $anthem_hero['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="anthem_hero_active">Active</label>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Hero Section</button>
    </div>
</form>
<?php endif; ?>

<!-- Anthem Stanzas -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Anthem Stanzas</h4>
</div>
<?php foreach ($anthem_stanzas as $stanza): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_anthem_stanza">
    <input type="hidden" name="id" value="<?php echo $stanza['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stanza Number</label>
        <input type="number" name="stanza_number" class="form-control" value="<?php echo $stanza['stanza_number']; ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stanza Title</label>
        <input type="text" name="stanza_title" class="form-control" value="<?php echo htmlspecialchars($stanza['stanza_title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Stanza Content (Lyrics)</label>
        <textarea name="content" class="form-control ckeditor" rows="6"><?php echo htmlspecialchars($stanza['content'] ?? ''); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label small">Border Color</label>
        <input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($stanza['border_color']); ?>">
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label small">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $stanza['display_order']; ?>">
    </div>
    
    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="stanza_active_<?php echo $stanza['id']; ?>" <?php echo $stanza['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="stanza_active_<?php echo $stanza['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save me-2"></i>Update Stanza</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Video Section -->
<?php if ($anthem_video): ?>
<div class="inn-title" style="margin-top: 40px;">
<h4>Anthem Video Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update_anthem_video">
    <input type="hidden" name="id" value="<?php echo $anthem_video['id']; ?>">
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Section Title</label>
        <input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($anthem_video['section_title']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Section Description</label>
        <textarea name="section_description" class="form-control" rows="3"><?php echo htmlspecialchars($anthem_video['section_description']); ?></textarea>
    </div>
    
    <?php
    // One column holds the anthem media, which may be an audio recording or a
    // video clip; the page picks the right player from the file extension.
    $anthem_media_is_audio = vvu_media_is_audio($anthem_video['video_url'] ?? '');
    $anthem_upload_limit   = vvu_php_upload_limit_bytes();
    ?>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Audio / Video URL</label>
        <input type="text" name="video_url" class="form-control" value="<?php echo htmlspecialchars($anthem_video['video_url']); ?>">
        <small class="text-muted">
            Currently playing as
            <strong><?php echo $anthem_media_is_audio ? 'audio' : 'video'; ?></strong>.
            Upload an MP3 to switch this section to an audio player — the poster image is kept and shown behind it.
        </small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Or Upload Audio / Video File</label>
        <input type="file" name="anthem_media_file" class="form-control" accept="audio/*,video/*">
        <small class="text-muted">
            MP3, M4A, OGG or WAV for audio; MP4, WebM or MOV for video.
            <?php if ($anthem_upload_limit): ?>
                Maximum <?php echo round($anthem_upload_limit / 1048576); ?>MB per file.
            <?php endif; ?>
            Leave empty to keep the current file.
        </small>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Video Poster URL</label>
        <input type="text" name="video_poster_url" class="form-control" value="<?php echo htmlspecialchars($anthem_video['video_poster_url']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Or Upload Poster Image</label>
        <input type="file" name="video_poster_file" class="form-control" accept="image/*">
    </div>
    
    <div class="col-md-12">
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="anthem_video_active" <?php echo $anthem_video['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="anthem_video_active">Active</label>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Video Section</button>
    </div>
</form>
<?php endif; ?>

<!-- About Anthem -->
<?php if ($anthem_about): ?>
<div class="inn-title" style="margin-top: 40px;">
<h4>About the Anthem</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm">
    <input type="hidden" name="action" value="update_anthem_about">
    <input type="hidden" name="id" value="<?php echo $anthem_about['id']; ?>">
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">History Title</label>
        <input type="text" name="history_title" class="form-control" value="<?php echo htmlspecialchars($anthem_about['history_title']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">History Content</label>
        <textarea name="history_content" class="form-control" rows="4"><?php echo htmlspecialchars($anthem_about['history_content']); ?></textarea>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Composer Title</label>
        <input type="text" name="composer_title" class="form-control" value="<?php echo htmlspecialchars($anthem_about['composer_title']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Composer Content</label>
        <textarea name="composer_content" class="form-control" rows="4"><?php echo htmlspecialchars($anthem_about['composer_content']); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold text-muted small">Composer Name</label>
        <input type="text" name="composer_name" class="form-control" value="<?php echo htmlspecialchars($anthem_about['composer_name']); ?>">
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold text-muted small">Composition Date</label>
        <input type="text" name="composition_date" class="form-control" value="<?php echo htmlspecialchars($anthem_about['composition_date']); ?>">
    </div>
    
    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="anthem_about_active" <?php echo $anthem_about['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="anthem_about_active">Active</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update About Section</button>
    </div>
</form>
<?php endif; ?>

<!-- Anthem CTA -->
<?php if ($anthem_cta): ?>
<div class="inn-title" style="margin-top: 40px;">
<h4>Anthem CTA Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm">
    <input type="hidden" name="action" value="update_anthem_cta">
    <input type="hidden" name="id" value="<?php echo $anthem_cta['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Title Line 1</label>
        <input type="text" name="title_line_1" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['title_line_1']); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Title Line 2</label>
        <input type="text" name="title_line_2" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['title_line_2']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($anthem_cta['description']); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 1 Text</label>
        <input type="text" name="btn1_text" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['btn1_text']); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 1 URL</label>
        <input type="text" name="btn1_url" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['btn1_url']); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 1 Icon</label>
        <input type="text" name="btn1_icon" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['btn1_icon']); ?>">
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 2 Text</label>
        <input type="text" name="btn2_text" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['btn2_text']); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 2 URL</label>
        <input type="text" name="btn2_url" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['btn2_url']); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 2 Icon</label>
        <input type="text" name="btn2_icon" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['btn2_icon']); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stat 1 Value</label>
        <input type="text" name="stat1_value" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['stat1_value']); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stat 1 Label</label>
        <input type="text" name="stat1_label" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['stat1_label']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stat 2 Value</label>
        <input type="text" name="stat2_value" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['stat2_value']); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stat 2 Label</label>
        <input type="text" name="stat2_label" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['stat2_label']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stat 3 Value</label>
        <input type="text" name="stat3_value" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['stat3_value']); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Stat 3 Label</label>
        <input type="text" name="stat3_label" class="form-control" value="<?php echo htmlspecialchars($anthem_cta['stat3_label']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="anthem_cta_active" <?php echo $anthem_cta['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="anthem_cta_active">Active</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update CTA Section</button>
    </div>
</form>
<?php endif; ?>
</div>
</div>

<!-- ECOLOGY TAB -->
<div class="tab-pane fade" id="ecology" role="tabpanel">
<div class="dashboard-card">
<!-- Hero -->
<?php if ($ecology_hero): ?>
<div class="inn-title">
<h4>Ecology Hero Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update_ecology_hero">
    <input type="hidden" name="id" value="<?php echo $ecology_hero['id']; ?>">
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Page Subtitle</label>
        <input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($ecology_hero['page_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Title</label>
        <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($ecology_hero['hero_title']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Subtitle</label>
        <input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($ecology_hero['hero_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Hero Description</label>
        <textarea name="hero_description" class="form-control" rows="4" required><?php echo htmlspecialchars($ecology_hero['hero_description']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Image URL</label>
        <input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($ecology_hero['hero_image_url']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Or Upload Image</label>
        <input type="file" name="hero_image_file" class="form-control" accept="image/*">
    </div>
    
    <div class="col-md-12">
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="ecology_hero_active" <?php echo $ecology_hero['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="ecology_hero_active">Active</label>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Hero Section</button>
    </div>
</form>
<?php endif; ?>

<!-- Philosophy Pillars -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Ecological Philosophy (3 Pillars)</h4>
</div>
<?php foreach ($ecology_philosophy as $phil): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_ecology_philosophy">
    <input type="hidden" name="id" value="<?php echo $phil['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($phil['icon']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($phil['title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($phil['description']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 1</label>
        <input type="text" name="feature_1" class="form-control" value="<?php echo htmlspecialchars($phil['feature_1']); ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Feature 2</label>
        <input type="text" name="feature_2" class="form-control" value="<?php echo htmlspecialchars($phil['feature_2']); ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Quote</label>
        <textarea name="quote" class="form-control" rows="2"><?php echo htmlspecialchars($phil['quote']); ?></textarea>
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label small fw-bold">Border Color</label>
        <input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($phil['border_color']); ?>">
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label small fw-bold">Background Color</label>
        <input type="text" name="bg_color" class="form-control" value="<?php echo htmlspecialchars($phil['bg_color']); ?>">
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label small fw-bold">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $phil['display_order']; ?>">
    </div>
    
    <div class="col-md-3 mb-3 d-flex align-items-end px-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="phil_active_<?php echo $phil['id']; ?>" <?php echo $phil['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="phil_active_<?php echo $phil['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save me-2"></i>Update Philosophy</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Green Initiatives -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Green Initiatives (6 Cards)</h4>
</div>
<?php foreach ($ecology_initiatives as $init): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_ecology_initiative">
    <input type="hidden" name="id" value="<?php echo $init['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold text-muted small">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($init['icon']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold text-muted small">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($init['title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold text-muted small">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($init['description']); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold text-muted small">Icon Background Color</label>
        <input type="text" name="icon_bg_color" class="form-control" value="<?php echo htmlspecialchars($init['icon_bg_color']); ?>">
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold text-muted small">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $init['display_order']; ?>">
    </div>
    
    <div class="col-md-4 mb-3 d-flex align-items-end px-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="init_active_<?php echo $init['id']; ?>" <?php echo $init['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="init_active_<?php echo $init['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary btn-sm px-4"><i class="fa fa-save me-2"></i>Update Initiative</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Ecology Stats -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Ecological Impact Stats</h4>
</div>
<?php foreach ($ecology_stats as $stat): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_ecology_stat">
    <input type="hidden" name="id" value="<?php echo $stat['id']; ?>">
    
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold text-muted small">Stat Value</label>
        <input type="text" name="stat_value" class="form-control" value="<?php echo htmlspecialchars($stat['stat_value']); ?>" required>
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold text-muted small">Stat Label</label>
        <input type="text" name="stat_label" class="form-control" value="<?php echo htmlspecialchars($stat['stat_label']); ?>" required>
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold text-muted small">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $stat['display_order']; ?>">
    </div>
    
    <div class="col-md-3 mb-3 d-flex align-items-end px-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="stat_active_<?php echo $stat['id']; ?>" <?php echo $stat['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="stat_active_<?php echo $stat['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary btn-sm px-4"><i class="fa fa-save me-2"></i>Update Stat</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Ecology CTA -->
<?php if ($ecology_cta): ?>
<div class="inn-title" style="margin-top: 40px;">
<h4>Ecology CTA Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm">
    <input type="hidden" name="action" value="update_ecology_cta">
    <input type="hidden" name="id" value="<?php echo $ecology_cta['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Title (White part)</label>
        <input type="text" name="title_white" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['title_white']); ?>" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Title (Green part)</label>
        <input type="text" name="title_green" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['title_green']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($ecology_cta['description']); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 1 Text</label>
        <input type="text" name="button_1_text" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['button_1_text']); ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 1 Link</label>
        <input type="text" name="button_1_link" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['button_1_link']); ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 1 Icon</label>
        <input type="text" name="button_1_icon" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['button_1_icon']); ?>" required>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 2 Text</label>
        <input type="text" name="button_2_text" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['button_2_text']); ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 2 Link</label>
        <input type="text" name="button_2_link" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['button_2_link']); ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Button 2 Icon</label>
        <input type="text" name="button_2_icon" class="form-control" value="<?php echo htmlspecialchars($ecology_cta['button_2_icon']); ?>" required>
    </div>

    <div class="col-md-12 mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="ecology_cta_active" <?php echo $ecology_cta['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="ecology_cta_active">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary btn-sm px-4"><i class="fa fa-save me-2"></i>Update CTA</button>
    </div>
</form>
<?php endif; ?>

</div>
</div>

<!-- CAMPUS TAB -->
<div class="tab-pane fade" id="campus" role="tabpanel">
<div class="dashboard-card">
<?php
// the_campus.php reads academic_pages_content/sections/items, but everything in
// this tab writes to campus_hero / campus_highlights / campus_features, which no
// public page reads. Saves here succeed and change nothing.
$legacy_page_name  = 'The Campus';
$legacy_target_url = 'manage_academic_pages.php?page=the_campus';
$legacy_public_url = 'the_campus.php';
include '_legacy_editor_notice.php';
?>
<!-- Hero -->
<?php if ($campus_hero): ?>
<div class="inn-title">
<h4>The Campus Hero Section</h4>
</div>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="update_campus_hero">
    <input type="hidden" name="id" value="<?php echo $campus_hero['id']; ?>">
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Page Subtitle</label>
        <input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($campus_hero['page_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Title</label>
        <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($campus_hero['hero_title']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Subtitle</label>
        <input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($campus_hero['hero_subtitle']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Hero Description</label>
        <textarea name="hero_description" class="form-control" rows="4" required><?php echo htmlspecialchars($campus_hero['hero_description']); ?></textarea>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Hero Image URL</label>
        <input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($campus_hero['hero_image_url']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Or Upload Image</label>
        <input type="file" name="hero_image_file" class="form-control" accept="image/*">
    </div>
    
    <div class="col-md-12">
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="campus_hero_active" <?php echo $campus_hero['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="campus_hero_active">Active</label>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Hero Section</button>
    </div>
</form>
<?php endif; ?>

<!-- Campus Highlights -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Why Choose VVU (3 Highlights)</h4>
</div>
<?php foreach ($campus_highlights as $highlight): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_campus_highlight">
    <input type="hidden" name="id" value="<?php echo $highlight['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold text-muted small">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($highlight['icon']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold text-muted small">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($highlight['title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold text-muted small">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($highlight['description']); ?></textarea>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold text-muted small">Quote</label>
        <textarea name="quote" class="form-control" rows="2"><?php echo htmlspecialchars($highlight['quote']); ?></textarea>
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label small fw-bold text-muted">Border Color</label>
        <input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($highlight['border_color']); ?>">
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label small fw-bold text-muted">Background Color</label>
        <input type="text" name="bg_color" class="form-control" value="<?php echo htmlspecialchars($highlight['bg_color']); ?>">
    </div>
    
    <div class="col-md-3 mb-3">
        <label class="form-label small fw-bold text-muted">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $highlight['display_order']; ?>">
    </div>
    
    <div class="col-md-3 mb-3 d-flex align-items-end px-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="high_active_<?php echo $highlight['id']; ?>" <?php echo $highlight['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="high_active_<?php echo $highlight['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary btn-sm px-4"><i class="fa fa-save me-2"></i>Update Highlight</button>
    </div>
</form>
<?php endforeach; ?>

<!-- Campus Features -->
<div class="inn-title" style="margin-top: 40px;">
<h4>Life on Campus Features (6 Cards)</h4>
</div>
<?php foreach ($campus_features as $feature): ?>
<form method="POST" class="row p-4 bg-white border rounded shadow-sm mb-4">
    <input type="hidden" name="action" value="update_campus_feature">
    <input type="hidden" name="id" value="<?php echo $feature['id']; ?>">
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold text-muted small">Icon</label>
        <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($feature['icon']); ?>" required>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold text-muted small">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($feature['title']); ?>" required>
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold text-muted small">Description</label>
        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($feature['description']); ?></textarea>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold text-muted small">Icon Background Color</label>
        <input type="text" name="icon_bg_color" class="form-control" value="<?php echo htmlspecialchars($feature['icon_bg_color']); ?>">
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold text-muted small">Display Order</label>
        <input type="number" name="display_order" class="form-control" value="<?php echo $feature['display_order']; ?>">
    </div>
    
    <div class="col-md-4 mb-3 d-flex align-items-end px-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="feat_active_<?php echo $feature['id']; ?>" <?php echo $feature['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="feat_active_<?php echo $feature['id']; ?>">Active</label>
        </div>
    </div>
    
    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary btn-sm px-4"><i class="fa fa-save me-2"></i>Update Feature</button>
    </div>
</form>
<?php endforeach; ?>
</div>
</div>

    </div><!-- End tab-content -->
</main>
</div><!-- End admin-wrapper -->

<?php include 'footer.php'; ?>

<style>
.page-header {
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e0e0;
}

.page-header h2 {
    font-size: 32px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.page-header p {
    color: #7f8c8d;
    font-size: 16px;
    margin: 0;
}

.nav-tabs {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 25px;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    font-size: 16px;
    padding: 14px 24px;
    transition: all 0.3s;
}

.nav-tabs .nav-link:hover {
    color: #4680ff;
    background: rgba(70, 128, 255, 0.05);
    border-bottom-color: #4680ff;
}

.nav-tabs .nav-link.active {
    color: #4680ff;
    background: white;
    border-bottom-color: #4680ff;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.inn-title {
    margin: 30px 0 20px 0;
    padding: 18px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    color: white;
}

.inn-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: white;
}

.inn-title:first-child {
    margin-top: 0;
}

.form-card-title {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 14px 24px;
    margin: 25px 0 0 0;
    border-radius: 8px 8px 0 0;
    border-bottom: 3px solid #4680ff;
    font-weight: 600;
    font-size: 17px;
    color: #2c3e50;
}

form {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 25px;
}

form .row {
    margin: 0;
}

/* Make Materialize-style fields look like Bootstrap */
.input-field {
    margin-bottom: 20px !important;
    position: relative;
}

.input-field label {
    position: static !important;
    font-weight: 500 !important;
    color: #2c3e50 !important;
    margin-bottom: 8px !important;
    font-size: 16px !important;
    display: block !important;
    pointer-events: auto !important;
}

.input-field input[type="text"],
.input-field input[type="number"],
.input-field input[type="email"],
.input-field select,
.input-field textarea,
.form-control,
.form-select {
    border: 1px solid #ddd !important;
    border-radius: 6px !important;
    padding: 12px 16px !important;
    font-size: 16px !important;
    width: 100% !important;
    box-sizing: border-box !important;
    transition: all 0.3s !important;
    margin: 0 !important;
    height: auto !important;
    background: white !important;
}

.input-field input[type="text"]:focus,
.input-field input[type="number"]:focus,
.input-field input[type="email"]:focus,
.input-field select:focus,
.input-field textarea:focus,
.form-control:focus,
.form-select:focus {
    border-color: #4680ff !important;
    box-shadow: 0 0 0 0.2rem rgba(70, 128, 255, 0.15) !important;
    outline: none !important;
}

.input-field textarea,
textarea.form-control,
textarea.materialize-textarea {
    min-height: 120px !important;
    resize: vertical !important;
    font-family: inherit !important;
    line-height: 1.6 !important;
}

/* Style select dropdowns */
.input-field select {
    appearance: auto !important;
    -webkit-appearance: menulist !important;
    -moz-appearance: menulist !important;
    cursor: pointer !important;
}

/* Remove Materialize floating label behavior */
.input-field label.active {
    transform: none !important;
    font-size: 16px !important;
}

/* Checkbox styling */
.input-field label:has(input[type="checkbox"]) {
    display: flex !important;
    align-items: center !important;
    font-weight: 500 !important;
    padding: 12px 0 !important;
}

.input-field input[type="checkbox"] {
    width: auto !important;
    margin-right: 10px !important;
    cursor: pointer !important;
    transform: scale(1.3) !important;
}

.input-field span {
    font-weight: 500 !important;
    font-size: 16px !important;
    color: #2c3e50 !important;
}

.form-label {
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 16px;
    display: block;
}

.btn {
    padding: 12px 24px !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
    font-size: 16px !important;
    transition: all 0.3s !important;
    border: none !important;
}

.btn-primary,
.waves-effect.waves-light.btn {
    background: #4680ff !important;
    color: white !important;
}

.btn-primary:hover,
.waves-effect.waves-light.btn:hover {
    background: #3066d9 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(70, 128, 255, 0.3) !important;
}

.alert {
    padding: 18px 24px;
    border-radius: 8px;
    margin-bottom: 25px;
    border: none;
    font-size: 16px;
}

.alert-success {
    background: #d1f2eb;
    color: #0c5d42;
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.form-check {
    margin-top: 10px;
    padding: 14px 0;
}

.form-check-input {
    transform: scale(1.3);
    margin-right: 10px;
    cursor: pointer;
}

.form-check-label {
    font-weight: 500;
    font-size: 16px;
    margin-left: 8px;
    cursor: pointer;
}

/* Grid column classes */
.col.s12, .col.s6 {
    padding: 0 10px;
}

.mb-3 {
    margin-bottom: 1.5rem !important;
}

/* Responsive grid */
@media (min-width: 768px) {
    .col.s6 {
        width: 50%;
        float: left;
    }
}

.col.s12 {
    width: 100%;
}
</style>

<script>
// Auto-dismiss success/error messages after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

// Add loading state to submit buttons
$('form').on('submit', function() {
    var $btn = $(this).find('button[type="submit"]');
    $btn.addClass('loading').prop('disabled', true);
    $btn.html('<i class="fa fa-spinner fa-spin" style="margin-right: 8px;"></i>Saving...');
});
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.ckeditor').forEach(textarea => {
        ClassicEditor.create(textarea, {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
        }).catch(error => console.error(error));
    });
});
</script>
<style>
.ck-editor__editable { min-height: 250px; }
.ck-editor__main { color: #2c3e50; font-size: 16px; }
</style>

<?php include 'footer.php'; ?>
  
 
 