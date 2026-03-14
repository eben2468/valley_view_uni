<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
session_start();

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
        // STRATEGIC PLAN ACTIONS
        // ========================================
        
        if ($action === 'update_strategic_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'] ?? null, 'strategy');
            if ($uploaded) $image_url = $uploaded;

            $pdf_url = $_POST['download_pdf_url'] ?? '';
            $uploaded_pdf = handleAdminFileUpload($_FILES['download_pdf_file'] ?? null, 'strategy', 'pdf_');
            if ($uploaded_pdf) $pdf_url = $uploaded_pdf;

            $stmt = $pdo->prepare("UPDATE strategic_plan_hero SET page_subtitle=?, hero_title_1=?, hero_title_2=?, hero_description=?, hero_image_url=?, download_button_text=?, download_pdf_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title_1'], $_POST['hero_title_2'], $_POST['hero_description'], $image_url, $_POST['download_button_text'], $pdf_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Strategic Plan hero updated successfully!";
        }
        
        elseif ($action === 'update_strategic_president') {
            $image_url = $_POST['president_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['president_image_file'] ?? null, 'strategy');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE strategic_plan_president_message SET section_title=?, president_image_url=?, message_quote=?, message_author=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['section_title'], $image_url, $_POST['message_quote'], $_POST['message_author'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "President message updated successfully!";
        }
        
        elseif ($action === 'update_strategic_pillar') {
            $stmt = $pdo->prepare("UPDATE strategic_plan_pillars SET icon=?, title=?, description=?, feature_1=?, feature_2=?, border_color=?, bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['feature_1'], $_POST['feature_2'], $_POST['border_color'], $_POST['bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Strategic pillar updated successfully!";
        }
        
        elseif ($action === 'update_strategic_timeline') {
            $stmt = $pdo->prepare("UPDATE strategic_plan_timeline SET phase_number=?, phase_badge=?, phase_title=?, phase_description=?, border_color=?, dot_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['phase_number'], $_POST['phase_badge'], $_POST['phase_title'], $_POST['phase_description'], $_POST['border_color'], $_POST['dot_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Timeline phase updated successfully!";
        }
        
        elseif ($action === 'update_strategic_stat') {
            $stmt = $pdo->prepare("UPDATE strategic_plan_stats SET stat_value=?, stat_label=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['stat_value'], $_POST['stat_label'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Stat updated successfully!";
        }
        
        elseif ($action === 'update_strategic_cta') {
            $stmt = $pdo->prepare("UPDATE strategic_plan_cta SET cta_title_1=?, cta_title_2=?, cta_description=?, button_1_text=?, button_1_url=?, button_2_text=?, button_2_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['cta_title_1'], $_POST['cta_title_2'], $_POST['cta_description'], $_POST['button_1_text'], $_POST['button_1_url'], $_POST['button_2_text'], $_POST['button_2_url'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "CTA section updated successfully!";
        }
        
        // ========================================
        // POLICIES ACTIONS
        // ========================================
        
        elseif ($action === 'update_policies_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'] ?? null, 'strategy');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE policies_hero SET page_subtitle=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Policies hero updated successfully!";
        }
        
        elseif ($action === 'update_policies_category') {
            $stmt = $pdo->prepare("UPDATE policies_categories SET icon=?, title=?, description=?, border_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['border_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Policy category updated successfully!";
        }
        
        elseif ($action === 'update_policies_document') {
            $doc_url = $_POST['document_url'];
            $uploaded_doc = handleAdminFileUpload($_FILES['document_file'] ?? null, 'policies', 'doc_');
            if ($uploaded_doc) $doc_url = $uploaded_doc;

            $stmt = $pdo->prepare("UPDATE policies_documents SET category_id=?, document_title=?, document_url=?, icon_color=?, bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['category_id'], $_POST['document_title'], $doc_url, $_POST['icon_color'], $_POST['bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Document updated successfully!";
        }
        
        elseif ($action === 'update_policies_link') {
            $stmt = $pdo->prepare("UPDATE policies_quick_links SET icon=?, title=?, description=?, link_text=?, link_url=?, icon_bg_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['link_text'], $_POST['link_url'], $_POST['icon_bg_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Quick link updated successfully!";
        }
        
        elseif ($action === 'update_policies_cta') {
            $stmt = $pdo->prepare("UPDATE policies_cta SET cta_title_1=?, cta_title_2=?, cta_description=?, button_1_text=?, button_1_url=?, button_2_text=?, button_2_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['cta_title_1'], $_POST['cta_title_2'], $_POST['cta_description'], $_POST['button_1_text'], $_POST['button_1_url'], $_POST['button_2_text'], $_POST['button_2_url'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Policies CTA updated successfully!";
        }
        
        // ========================================
        // HISTORY ACTIONS
        // ========================================
        
        elseif ($action === 'update_history_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'] ?? null, 'history');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE history_hero SET page_subtitle=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "History hero updated successfully!";
        }
        
        elseif ($action === 'update_history_overview') {
            $image_url = $_POST['overview_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['overview_image_file'] ?? null, 'history');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE history_overview SET section_title=?, paragraph_1=?, paragraph_2=?, founded_year=?, chartered_year=?, overview_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['section_title'], $_POST['paragraph_1'], $_POST['paragraph_2'], $_POST['founded_year'], $_POST['chartered_year'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Historical overview updated successfully!";
        }
        
        elseif ($action === 'update_history_milestone') {
            $stmt = $pdo->prepare("UPDATE history_milestones SET year=?, milestone_title=?, milestone_description=?, border_color=?, dot_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['year'], $_POST['milestone_title'], $_POST['milestone_description'], $_POST['border_color'], $_POST['dot_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Milestone updated successfully!";
        }
        
        elseif ($action === 'update_history_community') {
            $stmt = $pdo->prepare("UPDATE history_community SET section_title=?, section_description=?, feature_1_title=?, feature_1_label=?, feature_2_title=?, feature_2_label=?, feature_3_title=?, feature_3_label=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['section_title'], $_POST['section_description'], $_POST['feature_1_title'], $_POST['feature_1_label'], $_POST['feature_2_title'], $_POST['feature_2_label'], $_POST['feature_3_title'], $_POST['feature_3_label'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Community section updated successfully!";
        }
        
        elseif ($action === 'update_history_cta') {
            $stmt = $pdo->prepare("UPDATE history_cta SET cta_title_1=?, cta_title_2=?, cta_description=?, button_1_text=?, button_1_url=?, button_2_text=?, button_2_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['cta_title_1'], $_POST['cta_title_2'], $_POST['cta_description'], $_POST['button_1_text'], $_POST['button_1_url'], $_POST['button_2_text'], $_POST['button_2_url'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "History CTA updated successfully!";
        }
        
        // ========================================
        // ACCREDITATION ACTIONS
        // ========================================
        
        elseif ($action === 'update_accreditation_hero') {
            $image_url = $_POST['hero_image_url'];
            $uploaded = handleAdminFileUpload($_FILES['hero_image_file'] ?? null, 'history');
            if ($uploaded) $image_url = $uploaded;

            $stmt = $pdo->prepare("UPDATE accreditation_hero SET page_subtitle=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['page_subtitle'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url, $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Accreditation hero updated successfully!";
        }
        
        elseif ($action === 'update_accreditation_card') {
            $stmt = $pdo->prepare("UPDATE accreditation_cards SET icon=?, title=?, description=?, border_color=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['icon'], $_POST['title'], $_POST['description'], $_POST['border_color'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Accreditation card updated successfully!";
        }
        
        elseif ($action === 'update_accreditation_charter') {
            $stmt = $pdo->prepare("UPDATE accreditation_charter SET badge_text=?, section_title=?, paragraph_1=?, paragraph_2=?, quote=?, charter_year=?, achievement_text=?, achievement_location=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['badge_text'], $_POST['section_title'], $_POST['paragraph_1'], $_POST['paragraph_2'], $_POST['quote'], $_POST['charter_year'], $_POST['achievement_text'], $_POST['achievement_location'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Charter section updated successfully!";
        }
        
        elseif ($action === 'update_accreditation_membership') {
            $stmt = $pdo->prepare("UPDATE accreditation_memberships SET organization_name=?, organization_description=?, membership_type=?, location=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['organization_name'], $_POST['organization_description'], $_POST['membership_type'], $_POST['location'], $_POST['display_order'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Membership/linkage updated successfully!";
        }
        
        elseif ($action === 'update_accreditation_cta') {
            $stmt = $pdo->prepare("UPDATE accreditation_cta SET cta_title_1=?, cta_title_2=?, cta_description=?, button_1_text=?, button_1_url=?, button_2_text=?, button_2_url=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['cta_title_1'], $_POST['cta_title_2'], $_POST['cta_description'], $_POST['button_1_text'], $_POST['button_1_url'], $_POST['button_2_text'], $_POST['button_2_url'], $_POST['is_active'] ?? 1, $_POST['id']]);
            $success = "Accreditation CTA updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch all data for Strategic Plan
$strategic_hero = $pdo->query("SELECT * FROM strategic_plan_hero ORDER BY id DESC LIMIT 1")->fetch();
$strategic_president = $pdo->query("SELECT * FROM strategic_plan_president_message ORDER BY id DESC LIMIT 1")->fetch();
$strategic_pillars = $pdo->query("SELECT * FROM strategic_plan_pillars ORDER BY display_order ASC")->fetchAll();
$strategic_timeline = $pdo->query("SELECT * FROM strategic_plan_timeline ORDER BY display_order ASC")->fetchAll();
$strategic_stats = $pdo->query("SELECT * FROM strategic_plan_stats ORDER BY display_order ASC")->fetchAll();
$strategic_cta = $pdo->query("SELECT * FROM strategic_plan_cta ORDER BY id DESC LIMIT 1")->fetch();

// Fetch all data for Policies
$policies_hero = $pdo->query("SELECT * FROM policies_hero ORDER BY id DESC LIMIT 1")->fetch();
$policies_categories = $pdo->query("SELECT * FROM policies_categories ORDER BY display_order ASC")->fetchAll();
$policies_documents = $pdo->query("SELECT * FROM policies_documents ORDER BY category_id, display_order ASC")->fetchAll();
$policies_links = $pdo->query("SELECT * FROM policies_quick_links ORDER BY display_order ASC")->fetchAll();
$policies_cta = $pdo->query("SELECT * FROM policies_cta ORDER BY id DESC LIMIT 1")->fetch();

// Fetch all data for History
$history_hero = $pdo->query("SELECT * FROM history_hero ORDER BY id DESC LIMIT 1")->fetch();
$history_overview = $pdo->query("SELECT * FROM history_overview ORDER BY id DESC LIMIT 1")->fetch();
$history_milestones = $pdo->query("SELECT * FROM history_milestones ORDER BY display_order ASC")->fetchAll();
$history_community = $pdo->query("SELECT * FROM history_community ORDER BY id DESC LIMIT 1")->fetch();
$history_cta = $pdo->query("SELECT * FROM history_cta ORDER BY id DESC LIMIT 1")->fetch();

// Fetch all data for Accreditation
$accreditation_hero = $pdo->query("SELECT * FROM accreditation_hero ORDER BY id DESC LIMIT 1")->fetch();
$accreditation_cards = $pdo->query("SELECT * FROM accreditation_cards ORDER BY display_order ASC")->fetchAll();
$accreditation_charter = $pdo->query("SELECT * FROM accreditation_charter ORDER BY id DESC LIMIT 1")->fetch();
$accreditation_memberships = $pdo->query("SELECT * FROM accreditation_memberships ORDER BY membership_type, display_order ASC")->fetchAll();
$accreditation_cta = $pdo->query("SELECT * FROM accreditation_cta ORDER BY id DESC LIMIT 1")->fetch();

include 'header.php';
include 'sidebar.php';
?>

<style>
.nav-tabs {
    border-bottom: 3px solid #e0e0e0;
    margin-bottom: 30px;
    background: #f8f9fa;
    padding: 10px 15px 0;
    border-radius: 8px 8px 0 0;
}
.nav-tabs .nav-link {
    border: none;
    color: #666;
    padding: 12px 24px;
    font-weight: 600;
    border-radius: 6px 6px 0 0;
    margin-right: 4px;
    transition: all 0.3s ease;
}
.nav-tabs .nav-link:hover {
    background: #e0e7ff;
    color: #667eea;
}
.nav-tabs .nav-link.active {
    background: #667eea;
    color: white;
    box-shadow: 0 -2px 8px rgba(102, 126, 234, 0.3);
}
.form-card-title {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 16px;
}
.inn-title {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.inn-title h4 {
    color: white;
    margin: 0;
    font-size: 18px;
    font-weight: 700;
}
.dashboard-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}
.form-label {
    font-weight: 600;
    color: #444;
    margin-bottom: 8px;
    font-size: 14px;
}
.form-control, .form-select {
    border-radius: 6px;
    border: 2px solid #e0e0e0;
    padding: 10px 14px;
    transition: border-color 0.3s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 12px 28px;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}
.alert {
    border-radius: 8px;
    border: none;
    padding: 16px 20px;
    margin-bottom: 24px;
    font-weight: 500;
}
.alert-success {
    background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
    color: white;
}
.alert-danger {
    background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
    color: white;
}
.page-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #e0e0e0;
}
.page-header h2 {
    color: #333;
    font-weight: 700;
    margin-bottom: 8px;
}
.page-header p {
    color: #666;
    font-size: 16px;
    margin: 0;
}
</style>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h2>Manage Strategy & History Pages</h2>
        <p>Edit all content for Strategic Plan, Policies, History, and Accreditation & Charter pages</p>
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
    <ul class="nav nav-tabs" id="strategyHistoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="strategic-tab" data-bs-toggle="tab" data-bs-target="#strategic_plan" type="button" role="tab">
                <i class="fa fa-bullseye" style="margin-right: 6px;"></i>Strategic Plan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="policies-tab" data-bs-toggle="tab" data-bs-target="#policies" type="button" role="tab">
                <i class="fa fa-gavel" style="margin-right: 6px;"></i>Policies
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                <i class="fa fa-history" style="margin-right: 6px;"></i>History
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="accreditation-tab" data-bs-toggle="tab" data-bs-target="#accreditation" type="button" role="tab">
                <i class="fa fa-certificate" style="margin-right: 6px;"></i>Accreditation
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="strategyHistoryTabContent">

<!-- STRATEGIC PLAN TAB -->
<div class="tab-pane fade show active" id="strategic_plan" role="tabpanel">
<div class="dashboard-card">

<!-- Hero Section -->
<div class="inn-title">
<h4><i class="fa fa-image" style="margin-right: 10px;"></i>Strategic Plan Hero Section</h4>
</div>
<?php if ($strategic_hero): ?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_strategic_hero">
<input type="hidden" name="id" value="<?php echo $strategic_hero['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Page Subtitle</label>
<input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($strategic_hero['page_subtitle']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Title 1</label>
<input type="text" name="hero_title_1" class="form-control" value="<?php echo htmlspecialchars($strategic_hero['hero_title_1']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Title 2</label>
<input type="text" name="hero_title_2" class="form-control" value="<?php echo htmlspecialchars($strategic_hero['hero_title_2']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Hero Description</label>
<textarea name="hero_description" class="form-control" rows="4" required><?php echo htmlspecialchars($strategic_hero['hero_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Image URL</label>
<input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($strategic_hero['hero_image_url']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Or Upload Hero Image</label>
<input type="file" name="hero_image_file" class="form-control" accept="image/*">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Download Button Text</label>
<input type="text" name="download_button_text" class="form-control" value="<?php echo htmlspecialchars($strategic_hero['download_button_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Download PDF URL</label>
<input type="text" name="download_pdf_url" class="form-control" value="<?php echo htmlspecialchars($strategic_hero['download_pdf_url']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Or Upload PDF Document</label>
<input type="file" name="download_pdf_file" class="form-control" accept=".pdf">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="strategic_hero_active" <?php echo $strategic_hero['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="strategic_hero_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Hero Section</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- President Message Section -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-user" style="margin-right: 10px;"></i>President's Message</h4>
</div>
<?php if ($strategic_president): ?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_strategic_president">
<input type="hidden" name="id" value="<?php echo $strategic_president['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Section Title</label>
<input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($strategic_president['section_title']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">President Image URL</label>
<input type="text" name="president_image_url" class="form-control" value="<?php echo htmlspecialchars($strategic_president['president_image_url']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Or Upload President Image</label>
<input type="file" name="president_image_file" class="form-control" accept="image/*">
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Message Quote</label>
<textarea name="message_quote" class="form-control" rows="4" required><?php echo htmlspecialchars($strategic_president['message_quote']); ?></textarea>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Message Author</label>
<input type="text" name="message_author" class="form-control" value="<?php echo htmlspecialchars($strategic_president['message_author']); ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="president_active" <?php echo $strategic_president['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="president_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update President Message</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- Strategic Pillars -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-th" style="margin-right: 10px;"></i>Strategic Pillars</h4>
</div>
<?php foreach ($strategic_pillars as $pillar): ?>
<div class="form-card-title" style="background: #f8f9fa; padding: 12px 20px; margin: 20px 0 0 0; border-radius: 6px 6px 0 0; border-bottom: 2px solid #e0e0e0;">
<i class="fa fa-flag" style="margin-right: 8px; color: #667eea;"></i>
<?php echo htmlspecialchars($pillar['title']); ?>
</div>
<form method="POST" class="row" style="border-radius: 0 0 6px 6px; margin-top: 0; padding: 20px; border: 1px solid #ddd;">
<input type="hidden" name="action" value="update_strategic_pillar">
<input type="hidden" name="id" value="<?php echo $pillar['id']; ?>">
<div class="col-md-6 mb-3">
<label class="form-label">Icon (Material Symbol)</label>
<input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($pillar['icon']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Title</label>
<input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($pillar['title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Description</label>
<textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($pillar['description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 1</label>
<input type="text" name="feature_1" class="form-control" value="<?php echo htmlspecialchars($pillar['feature_1']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 2</label>
<input type="text" name="feature_2" class="form-control" value="<?php echo htmlspecialchars($pillar['feature_2']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Border Color (e.g., blue-600)</label>
<input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($pillar['border_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Background Color</label>
<input type="text" name="bg_color" class="form-control" value="<?php echo htmlspecialchars($pillar['bg_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Display Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $pillar['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="pillar_<?php echo $pillar['id']; ?>_active" <?php echo $pillar['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="pillar_<?php echo $pillar['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Pillar</button>
</div>
</form>
<?php endforeach; ?>

<!-- Timeline Phases -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-calendar" style="margin-right: 10px;"></i>Implementation Timeline</h4>
</div>
<?php foreach ($strategic_timeline as $phase): ?>
<div class="form-card-title" style="background: #f8f9fa; padding: 12px 20px; margin: 20px 0 0 0; border-radius: 6px 6px 0 0; border-bottom: 2px solid #e0e0e0;">
<i class="fa fa-clock" style="margin-right: 8px; color: #667eea;"></i>
<?php echo htmlspecialchars($phase['phase_badge']); ?>: <?php echo htmlspecialchars($phase['phase_title']); ?>
</div>
<form method="POST" class="row" style="border-radius: 0 0 6px 6px; margin-top: 0; padding: 20px; border: 1px solid #ddd;">
<input type="hidden" name="action" value="update_strategic_timeline">
<input type="hidden" name="id" value="<?php echo $phase['id']; ?>">
<div class="col-md-4 mb-3">
<label class="form-label">Phase Number</label>
<input type="number" name="phase_number" class="form-control" value="<?php echo $phase['phase_number']; ?>" required>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Phase Badge (e.g., Phase 1)</label>
<input type="text" name="phase_badge" class="form-control" value="<?php echo htmlspecialchars($phase['phase_badge']); ?>" required>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Phase Title</label>
<input type="text" name="phase_title" class="form-control" value="<?php echo htmlspecialchars($phase['phase_title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Phase Description</label>
<textarea name="phase_description" class="form-control" rows="3" required><?php echo htmlspecialchars($phase['phase_description']); ?></textarea>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Border Color</label>
<input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($phase['border_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Dot Color</label>
<input type="text" name="dot_color" class="form-control" value="<?php echo htmlspecialchars($phase['dot_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Display Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $phase['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="phase_<?php echo $phase['id']; ?>_active" <?php echo $phase['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="phase_<?php echo $phase['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Timeline Phase</button>
</div>
</form>
<?php endforeach; ?>

<!-- Impact Stats -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-chart-bar" style="margin-right: 10px;"></i>Impact Statistics</h4>
</div>
<?php foreach ($strategic_stats as $stat): ?>
<form method="POST" class="row" style="padding: 20px; border: 1px solid #ddd; margin-bottom: 15px; border-radius: 6px;">
<input type="hidden" name="action" value="update_strategic_stat">
<input type="hidden" name="id" value="<?php echo $stat['id']; ?>">
<div class="col-md-5 mb-3">
<label class="form-label">Stat Value</label>
<input type="text" name="stat_value" class="form-control" value="<?php echo htmlspecialchars($stat['stat_value']); ?>" required>
</div>
<div class="col-md-5 mb-3">
<label class="form-label">Stat Label</label>
<input type="text" name="stat_label" class="form-control" value="<?php echo htmlspecialchars($stat['stat_label']); ?>" required>
</div>
<div class="col-md-2 mb-3">
<label class="form-label">Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $stat['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="stat_<?php echo $stat['id']; ?>_active" <?php echo $stat['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="stat_<?php echo $stat['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Stat</button>
</div>
</form>
<?php endforeach; ?>

<!-- CTA Section -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-bullhorn" style="margin-right: 10px;"></i>Call-to-Action Section</h4>
</div>
<?php if ($strategic_cta): ?>
<form method="POST">
<input type="hidden" name="action" value="update_strategic_cta">
<input type="hidden" name="id" value="<?php echo $strategic_cta['id']; ?>">
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 1</label>
<input type="text" name="cta_title_1" class="form-control" value="<?php echo htmlspecialchars($strategic_cta['cta_title_1']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 2</label>
<input type="text" name="cta_title_2" class="form-control" value="<?php echo htmlspecialchars($strategic_cta['cta_title_2']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">CTA Description</label>
<textarea name="cta_description" class="form-control" rows="3" required><?php echo htmlspecialchars($strategic_cta['cta_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 Text</label>
<input type="text" name="button_1_text" class="form-control" value="<?php echo htmlspecialchars($strategic_cta['button_1_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 URL</label>
<input type="text" name="button_1_url" class="form-control" value="<?php echo htmlspecialchars($strategic_cta['button_1_url']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 Text</label>
<input type="text" name="button_2_text" class="form-control" value="<?php echo htmlspecialchars($strategic_cta['button_2_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 URL</label>
<input type="text" name="button_2_url" class="form-control" value="<?php echo htmlspecialchars($strategic_cta['button_2_url']); ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="cta_active" <?php echo $strategic_cta['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="cta_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update CTA Section</button>
</div>
</div>
</form>
<?php endif; ?>

</div>
</div>
<!-- End Strategic Plan Tab -->

<!-- POLICIES TAB -->
<div class="tab-pane fade" id="policies" role="tabpanel">
<div class="dashboard-card">

<!-- Policies Hero -->
<div class="inn-title">
<h4><i class="fa fa-image" style="margin-right: 10px;"></i>Policies Hero Section</h4>
</div>
<?php if ($policies_hero): ?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_policies_hero">
<input type="hidden" name="id" value="<?php echo $policies_hero['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Page Subtitle</label>
<input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($policies_hero['page_subtitle']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Title</label>
<input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($policies_hero['hero_title']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Subtitle</label>
<input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($policies_hero['hero_subtitle']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Hero Description</label>
<textarea name="hero_description" class="form-control" rows="3" required><?php echo htmlspecialchars($policies_hero['hero_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Image URL</label>
<input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($policies_hero['hero_image_url']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Or Upload Hero Image</label>
<input type="file" name="hero_image_file" class="form-control" accept="image/*">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="policies_hero_active" <?php echo $policies_hero['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="policies_hero_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Hero</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- Policy Categories -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-folder" style="margin-right: 10px;"></i>Policy Categories</h4>
</div>
<?php foreach ($policies_categories as $category): ?>
<div class="form-card-title" style="background: #f8f9fa; padding: 12px 20px; margin: 20px 0 0 0; border-radius: 6px 6px 0 0; border-bottom: 2px solid #e0e0e0;">
<?php echo htmlspecialchars($category['title']); ?>
</div>
<form method="POST" class="row" style="border-radius: 0 0 6px 6px; margin-top: 0; padding: 20px; border: 1px solid #ddd;">
<input type="hidden" name="action" value="update_policies_category">
<input type="hidden" name="id" value="<?php echo $category['id']; ?>">
<div class="col-md-4 mb-3">
<label class="form-label">Icon</label>
<input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($category['icon']); ?>" required>
</div>
<div class="col-md-8 mb-3">
<label class="form-label">Title</label>
<input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($category['title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Description</label>
<textarea name="description" class="form-control" rows="2" required><?php echo htmlspecialchars($category['description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Border Color</label>
<input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($category['border_color']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Display Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $category['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="cat_<?php echo $category['id']; ?>_active" <?php echo $category['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="cat_<?php echo $category['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Category</button>
</div>
</form>
<?php endforeach; ?>

<!-- Policy Documents -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-file-pdf" style="margin-right: 10px;"></i>Policy Documents</h4>
</div>
<?php foreach ($policies_documents as $doc): ?>
<form method="POST" class="row" enctype="multipart/form-data" style="padding: 15px; border: 1px solid #ddd; margin-bottom: 10px; border-radius: 6px;">
<input type="hidden" name="action" value="update_policies_document">
<input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
<div class="col-md-3 mb-3">
<label class="form-label">Category</label>
<select name="category_id" class="form-control" required>
<?php foreach ($policies_categories as $cat): ?>
<option value="<?php echo $cat['id']; ?>" <?php echo $doc['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['title']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-5 mb-3">
<label class="form-label">Document Title</label>
<input type="text" name="document_title" class="form-control" value="<?php echo htmlspecialchars($doc['document_title']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Document URL</label>
<input type="text" name="document_url" class="form-control" value="<?php echo htmlspecialchars($doc['document_url']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Or Upload Document (PDF)</label>
<input type="file" name="document_file" class="form-control" accept=".pdf">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Icon Color</label>
<input type="text" name="icon_color" class="form-control" value="<?php echo htmlspecialchars($doc['icon_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">BG Color</label>
<input type="text" name="bg_color" class="form-control" value="<?php echo htmlspecialchars($doc['bg_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $doc['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="doc_<?php echo $doc['id']; ?>_active" <?php echo $doc['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="doc_<?php echo $doc['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Document</button>
</div>
</form>
<?php endforeach; ?>

<!-- Quick Links -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-link" style="margin-right: 10px;"></i>Quick Links</h4>
</div>
<?php foreach ($policies_links as $link): ?>
<form method="POST" class="row" style="padding: 15px; border: 1px solid #ddd; margin-bottom: 10px; border-radius: 6px;">
<input type="hidden" name="action" value="update_policies_link">
<input type="hidden" name="id" value="<?php echo $link['id']; ?>">
<div class="col-md-2 mb-3">
<label class="form-label">Icon</label>
<input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($link['icon']); ?>" required>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Title</label>
<input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($link['title']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Description</label>
<input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($link['description']); ?>" required>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Link Text</label>
<input type="text" name="link_text" class="form-control" value="<?php echo htmlspecialchars($link['link_text']); ?>" required>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Link URL</label>
<input type="text" name="link_url" class="form-control" value="<?php echo htmlspecialchars($link['link_url']); ?>" required>
</div>
<div class="col-md-2 mb-3">
<label class="form-label">Icon BG</label>
<input type="text" name="icon_bg_color" class="form-control" value="<?php echo htmlspecialchars($link['icon_bg_color']); ?>">
</div>
<div class="col-md-2 mb-3">
<label class="form-label">Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $link['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="link_<?php echo $link['id']; ?>_active" <?php echo $link['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="link_<?php echo $link['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Link</button>
</div>
</form>
<?php endforeach; ?>

<!-- Policies CTA -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-bullhorn" style="margin-right: 10px;"></i>Call-to-Action</h4>
</div>
<?php if ($policies_cta): ?>
<form method="POST">
<input type="hidden" name="action" value="update_policies_cta">
<input type="hidden" name="id" value="<?php echo $policies_cta['id']; ?>">
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 1</label>
<input type="text" name="cta_title_1" class="form-control" value="<?php echo htmlspecialchars($policies_cta['cta_title_1']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 2</label>
<input type="text" name="cta_title_2" class="form-control" value="<?php echo htmlspecialchars($policies_cta['cta_title_2']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Description</label>
<textarea name="cta_description" class="form-control" rows="2" required><?php echo htmlspecialchars($policies_cta['cta_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 Text</label>
<input type="text" name="button_1_text" class="form-control" value="<?php echo htmlspecialchars($policies_cta['button_1_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 URL</label>
<input type="text" name="button_1_url" class="form-control" value="<?php echo htmlspecialchars($policies_cta['button_1_url']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 Text</label>
<input type="text" name="button_2_text" class="form-control" value="<?php echo htmlspecialchars($policies_cta['button_2_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 URL</label>
<input type="text" name="button_2_url" class="form-control" value="<?php echo htmlspecialchars($policies_cta['button_2_url']); ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="policies_cta_active" <?php echo $policies_cta['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="policies_cta_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update CTA</button>
</div>
</div>
</form>
<?php endif; ?>

</div>
</div>

<!-- HISTORY TAB -->
<div class="tab-pane fade" id="history" role="tabpanel">
<div class="dashboard-card">

<!-- History Hero -->
<div class="inn-title">
<h4><i class="fa fa-image" style="margin-right: 10px;"></i>History Hero Section</h4>
</div>
<?php if ($history_hero): ?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_history_hero">
<input type="hidden" name="id" value="<?php echo $history_hero['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Page Subtitle</label>
<input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($history_hero['page_subtitle']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Title</label>
<input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($history_hero['hero_title']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Subtitle</label>
<input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($history_hero['hero_subtitle']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Hero Description</label>
<textarea name="hero_description" class="form-control" rows="3" required><?php echo htmlspecialchars($history_hero['hero_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Image URL</label>
<input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($history_hero['hero_image_url']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Or Upload Hero Image</label>
<input type="file" name="hero_image_file" class="form-control" accept="image/*">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="history_hero_active" <?php echo $history_hero['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="history_hero_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Hero</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- Historical Overview -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-info-circle" style="margin-right: 10px;"></i>Historical Overview</h4>
</div>
<?php if ($history_overview): ?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_history_overview">
<input type="hidden" name="id" value="<?php echo $history_overview['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Section Title</label>
<input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($history_overview['section_title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Paragraph 1</label>
<textarea name="paragraph_1" class="form-control" rows="3" required><?php echo htmlspecialchars($history_overview['paragraph_1']); ?></textarea>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Paragraph 2</label>
<textarea name="paragraph_2" class="form-control" rows="3" required><?php echo htmlspecialchars($history_overview['paragraph_2']); ?></textarea>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Founded Year</label>
<input type="text" name="founded_year" class="form-control" value="<?php echo htmlspecialchars($history_overview['founded_year']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Chartered Year</label>
<input type="text" name="chartered_year" class="form-control" value="<?php echo htmlspecialchars($history_overview['chartered_year']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Overview Image URL</label>
<input type="text" name="overview_image_url" class="form-control" value="<?php echo htmlspecialchars($history_overview['overview_image_url']); ?>">
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Or Upload Overview Image</label>
<input type="file" name="overview_image_file" class="form-control" accept="image/*">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="history_overview_active" <?php echo $history_overview['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="history_overview_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Overview</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- Historical Milestones -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-flag" style="margin-right: 10px;"></i>Historical Milestones</h4>
</div>
<?php foreach ($history_milestones as $milestone): ?>
<div class="form-card-title" style="background: #f8f9fa; padding: 12px 20px; margin: 20px 0 0 0; border-radius: 6px 6px 0 0; border-bottom: 2px solid #e0e0e0;">
<?php echo htmlspecialchars($milestone['year']); ?>: <?php echo htmlspecialchars($milestone['milestone_title']); ?>
</div>
<form method="POST" class="row" style="border-radius: 0 0 6px 6px; margin-top: 0; padding: 20px; border: 1px solid #ddd;">
<input type="hidden" name="action" value="update_history_milestone">
<input type="hidden" name="id" value="<?php echo $milestone['id']; ?>">
<div class="col-md-3 mb-3">
<label class="form-label">Year</label>
<input type="text" name="year" class="form-control" value="<?php echo htmlspecialchars($milestone['year']); ?>" required>
</div>
<div class="col-md-9 mb-3">
<label class="form-label">Milestone Title</label>
<input type="text" name="milestone_title" class="form-control" value="<?php echo htmlspecialchars($milestone['milestone_title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Milestone Description</label>
<textarea name="milestone_description" class="form-control" rows="2" required><?php echo htmlspecialchars($milestone['milestone_description']); ?></textarea>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Border Color</label>
<input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($milestone['border_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Dot Color</label>
<input type="text" name="dot_color" class="form-control" value="<?php echo htmlspecialchars($milestone['dot_color']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Display Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $milestone['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="milestone_<?php echo $milestone['id']; ?>_active" <?php echo $milestone['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="milestone_<?php echo $milestone['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Milestone</button>
</div>
</form>
<?php endforeach; ?>

<!-- Global Community -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-globe" style="margin-right: 10px;"></i>Global Community Section</h4>
</div>
<?php if ($history_community): ?>
<form method="POST">
<input type="hidden" name="action" value="update_history_community">
<input type="hidden" name="id" value="<?php echo $history_community['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Section Title</label>
<input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($history_community['section_title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Section Description</label>
<textarea name="section_description" class="form-control" rows="3" required><?php echo htmlspecialchars($history_community['section_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 1 Title</label>
<input type="text" name="feature_1_title" class="form-control" value="<?php echo htmlspecialchars($history_community['feature_1_title']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 1 Label</label>
<input type="text" name="feature_1_label" class="form-control" value="<?php echo htmlspecialchars($history_community['feature_1_label']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 2 Title</label>
<input type="text" name="feature_2_title" class="form-control" value="<?php echo htmlspecialchars($history_community['feature_2_title']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 2 Label</label>
<input type="text" name="feature_2_label" class="form-control" value="<?php echo htmlspecialchars($history_community['feature_2_label']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 3 Title</label>
<input type="text" name="feature_3_title" class="form-control" value="<?php echo htmlspecialchars($history_community['feature_3_title']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Feature 3 Label</label>
<input type="text" name="feature_3_label" class="form-control" value="<?php echo htmlspecialchars($history_community['feature_3_label']); ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="history_community_active" <?php echo $history_community['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="history_community_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Community Section</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- History CTA -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-bullhorn" style="margin-right: 10px;"></i>Call-to-Action</h4>
</div>
<?php if ($history_cta): ?>
<form method="POST">
<input type="hidden" name="action" value="update_history_cta">
<input type="hidden" name="id" value="<?php echo $history_cta['id']; ?>">
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 1</label>
<input type="text" name="cta_title_1" class="form-control" value="<?php echo htmlspecialchars($history_cta['cta_title_1']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 2</label>
<input type="text" name="cta_title_2" class="form-control" value="<?php echo htmlspecialchars($history_cta['cta_title_2']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Description</label>
<textarea name="cta_description" class="form-control" rows="2" required><?php echo htmlspecialchars($history_cta['cta_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 Text</label>
<input type="text" name="button_1_text" class="form-control" value="<?php echo htmlspecialchars($history_cta['button_1_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 URL</label>
<input type="text" name="button_1_url" class="form-control" value="<?php echo htmlspecialchars($history_cta['button_1_url']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 Text</label>
<input type="text" name="button_2_text" class="form-control" value="<?php echo htmlspecialchars($history_cta['button_2_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 URL</label>
<input type="text" name="button_2_url" class="form-control" value="<?php echo htmlspecialchars($history_cta['button_2_url']); ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="history_cta_active" <?php echo $history_cta['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="history_cta_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update CTA</button>
</div>
</div>
</form>
<?php endif; ?>

</div>
</div>

<!-- ACCREDITATION TAB -->
<div class="tab-pane fade" id="accreditation" role="tabpanel">
<div class="dashboard-card">

<!-- Accreditation Hero -->
<div class="inn-title">
<h4><i class="fa fa-image" style="margin-right: 10px;"></i>Accreditation Hero Section</h4>
</div>
<?php if ($accreditation_hero): ?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_accreditation_hero">
<input type="hidden" name="id" value="<?php echo $accreditation_hero['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Page Subtitle</label>
<input type="text" name="page_subtitle" class="form-control" value="<?php echo htmlspecialchars($accreditation_hero['page_subtitle']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Title</label>
<input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($accreditation_hero['hero_title']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Subtitle</label>
<input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($accreditation_hero['hero_subtitle']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Hero Description</label>
<textarea name="hero_description" class="form-control" rows="3" required><?php echo htmlspecialchars($accreditation_hero['hero_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Hero Image URL</label>
<input type="text" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars($accreditation_hero['hero_image_url']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Or Upload Hero Image</label>
<input type="file" name="hero_image_file" class="form-control" accept="image/*">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="accred_hero_active" <?php echo $accreditation_hero['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="accred_hero_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Hero</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- Accreditation Cards -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-certificate" style="margin-right: 10px;"></i>Accreditation Bodies</h4>
</div>
<?php foreach ($accreditation_cards as $card): ?>
<div class="form-card-title" style="background: #f8f9fa; padding: 12px 20px; margin: 20px 0 0 0; border-radius: 6px 6px 0 0; border-bottom: 2px solid #e0e0e0;">
<?php echo htmlspecialchars($card['title']); ?>
</div>
<form method="POST" class="row" style="border-radius: 0 0 6px 6px; margin-top: 0; padding: 20px; border: 1px solid #ddd;">
<input type="hidden" name="action" value="update_accreditation_card">
<input type="hidden" name="id" value="<?php echo $card['id']; ?>">
<div class="col-md-3 mb-3">
<label class="form-label">Icon</label>
<input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($card['icon']); ?>" required>
</div>
<div class="col-md-9 mb-3">
<label class="form-label">Title</label>
<input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($card['title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Description (HTML allowed for <strong> tags)</label>
<textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($card['description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Border Color</label>
<input type="text" name="border_color" class="form-control" value="<?php echo htmlspecialchars($card['border_color']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Display Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $card['display_order']; ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="card_<?php echo $card['id']; ?>_active" <?php echo $card['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="card_<?php echo $card['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Card</button>
</div>
</form>
<?php endforeach; ?>

<!-- Presidential Charter -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-award" style="margin-right: 10px;"></i>Presidential Charter Section</h4>
</div>
<?php if ($accreditation_charter): ?>
<form method="POST">
<input type="hidden" name="action" value="update_accreditation_charter">
<input type="hidden" name="id" value="<?php echo $accreditation_charter['id']; ?>">
<div class="row">
<div class="col-md-12 mb-3">
<label class="form-label">Badge Text</label>
<input type="text" name="badge_text" class="form-control" value="<?php echo htmlspecialchars($accreditation_charter['badge_text']); ?>">
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Section Title</label>
<input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($accreditation_charter['section_title']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Paragraph 1</label>
<textarea name="paragraph_1" class="form-control" rows="3" required><?php echo htmlspecialchars($accreditation_charter['paragraph_1']); ?></textarea>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Paragraph 2 (HTML allowed for <strong> tags)</label>
<textarea name="paragraph_2" class="form-control" rows="3" required><?php echo htmlspecialchars($accreditation_charter['paragraph_2']); ?></textarea>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Quote</label>
<textarea name="quote" class="form-control" rows="2"><?php echo htmlspecialchars($accreditation_charter['quote']); ?></textarea>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Charter Year</label>
<input type="text" name="charter_year" class="form-control" value="<?php echo htmlspecialchars($accreditation_charter['charter_year']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Achievement Text</label>
<input type="text" name="achievement_text" class="form-control" value="<?php echo htmlspecialchars($accreditation_charter['achievement_text']); ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Achievement Location</label>
<input type="text" name="achievement_location" class="form-control" value="<?php echo htmlspecialchars($accreditation_charter['achievement_location']); ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="charter_active" <?php echo $accreditation_charter['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="charter_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Charter Section</button>
</div>
</div>
</form>
<?php endif; ?>

<!-- Memberships & Linkages -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-handshake" style="margin-right: 10px;"></i>Memberships & Global Linkages</h4>
</div>
<?php foreach ($accreditation_memberships as $membership): ?>
<div class="form-card-title" style="background: #f8f9fa; padding: 12px 20px; margin: 20px 0 0 0; border-radius: 6px 6px 0 0; border-bottom: 2px solid #e0e0e0;">
<i class="fa fa-<?php echo $membership['membership_type'] == 'membership' ? 'users' : 'link'; ?>" style="margin-right: 8px;"></i>
<?php echo htmlspecialchars($membership['organization_name']); ?>
</div>
<form method="POST" class="row" style="border-radius: 0 0 6px 6px; margin-top: 0; padding: 20px; border: 1px solid #ddd;">
<input type="hidden" name="action" value="update_accreditation_membership">
<input type="hidden" name="id" value="<?php echo $membership['id']; ?>">
<div class="col-md-8 mb-3">
<label class="form-label">Organization Name</label>
<input type="text" name="organization_name" class="form-control" value="<?php echo htmlspecialchars($membership['organization_name']); ?>" required>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Location</label>
<input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($membership['location']); ?>">
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Description</label>
<textarea name="organization_description" class="form-control" rows="2"><?php echo htmlspecialchars($membership['organization_description']); ?></textarea>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Type</label>
<select name="membership_type" class="form-control" required>
<option value="membership" <?php echo $membership['membership_type'] == 'membership' ? 'selected' : ''; ?>>Membership</option>
<option value="linkage" <?php echo $membership['membership_type'] == 'linkage' ? 'selected' : ''; ?>>Linkage</option>
</select>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Display Order</label>
<input type="number" name="display_order" class="form-control" value="<?php echo $membership['display_order']; ?>">
</div>
<div class="col-md-4 mb-3">
<div class="form-check" style="margin-top: 32px;">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="member_<?php echo $membership['id']; ?>_active" <?php echo $membership['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="member_<?php echo $membership['id']; ?>_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save" style="margin-right: 8px;"></i>Update Membership/Linkage</button>
</div>
</form>
<?php endforeach; ?>

<!-- Accreditation CTA -->
<div class="inn-title" style="margin-top: 40px;">
<h4><i class="fa fa-bullhorn" style="margin-right: 10px;"></i>Call-to-Action</h4>
</div>
<?php if ($accreditation_cta): ?>
<form method="POST">
<input type="hidden" name="action" value="update_accreditation_cta">
<input type="hidden" name="id" value="<?php echo $accreditation_cta['id']; ?>">
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 1</label>
<input type="text" name="cta_title_1" class="form-control" value="<?php echo htmlspecialchars($accreditation_cta['cta_title_1']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">CTA Title 2</label>
<input type="text" name="cta_title_2" class="form-control" value="<?php echo htmlspecialchars($accreditation_cta['cta_title_2']); ?>" required>
</div>
<div class="col-md-12 mb-3">
<label class="form-label">Description</label>
<textarea name="cta_description" class="form-control" rows="2" required><?php echo htmlspecialchars($accreditation_cta['cta_description']); ?></textarea>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 Text</label>
<input type="text" name="button_1_text" class="form-control" value="<?php echo htmlspecialchars($accreditation_cta['button_1_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 1 URL</label>
<input type="text" name="button_1_url" class="form-control" value="<?php echo htmlspecialchars($accreditation_cta['button_1_url']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 Text</label>
<input type="text" name="button_2_text" class="form-control" value="<?php echo htmlspecialchars($accreditation_cta['button_2_text']); ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Button 2 URL</label>
<input type="text" name="button_2_url" class="form-control" value="<?php echo htmlspecialchars($accreditation_cta['button_2_url']); ?>">
</div>
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="is_active" value="1" class="form-check-input" id="accred_cta_active" <?php echo $accreditation_cta['is_active'] ? 'checked' : ''; ?>>
<label class="form-check-label" for="accred_cta_active">Active</label>
</div>
</div>
<div class="col-md-12">
<button type="submit" class="btn btn-primary"><i class="fa fa-save" style="margin-right: 8px;"></i>Update CTA</button>
</div>
</div>
</form>
<?php endif; ?>

</div>
</div>

    </div>
</main>

<?php include 'footer.php'; ?>
