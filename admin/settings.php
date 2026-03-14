<?php
/**
 * Admin Settings Management Page
 * Premium, Modern, and Responsive CMS for Global Site Settings
 */
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');

$success = '';
$error = '';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    try {
        $pdo->beginTransaction();
        
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $pdo->prepare("UPDATE global_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }

        // Handle File Uploads (Logos, Icons etc)
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $setting_key = str_replace('file_', '', $key);
                    $upload_path = handleAdminFileUpload($file, 'settings');
                    if ($upload_path) {
                        $stmt = $pdo->prepare("UPDATE global_settings SET setting_value = ? WHERE setting_key = ?");
                        $stmt->execute([$upload_path, $setting_key]);
                    }
                }
            }
        }

        $pdo->commit();
        $success = "Global settings updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error updating settings: " . $e->getMessage();
    }
}

// Fetch Settings Grouped by Category
$settings_raw = $pdo->query("SELECT * FROM global_settings ORDER BY setting_group, display_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$groups = [];
foreach ($settings_raw as $s) {
    $groups[$s['setting_group']][] = $s;
}

// Define display names for groups
$group_names = [
    'general' => ['label' => 'General Config', 'icon' => 'fas fa-sliders-h'],
    'seo'     => ['label' => 'SEO & Analytics', 'icon' => 'fas fa-search-plus'],
    'system'  => ['label' => 'System Control', 'icon' => 'fas fa-server'],
    'theme'   => ['label' => 'Visual Theme', 'icon' => 'fas fa-palette'],
];
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4 animate__animated animate__fadeInDown">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">Global Site Settings</h2>
                        <p class="text-muted mb-0">Manage core configurations, SEO, and system behaviors from one central hub.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" form="settingsForm" name="update_settings" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-2"></i> Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-4 me-3"></i>
                    <div><strong>Success!</strong> <?php echo $success; ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                    <div><strong>Error!</strong> <?php echo $error; ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form id="settingsForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_settings" value="1">
            
            <div class="row">
                <!-- Navigation Sidebar for Settings -->
                <div class="col-lg-3">
                    <div class="dashboard-card sticky-top" style="top: 80px; z-index: 10;">
                        <div class="card-body p-3">
                            <div class="nav flex-column nav-pills" id="settings-pills-tab" role="tablist">
                                <?php $first = true; foreach ($group_names as $key => $meta): ?>
                                    <button class="nav-link <?php echo $first ? 'active' : ''; ?> d-flex align-items-center py-3 px-4 mb-2 rounded-3 text-start border-0" 
                                            id="pill-<?php echo $key; ?>-tab" 
                                            data-bs-toggle="pill" 
                                            data-bs-target="#pill-<?php echo $key; ?>" 
                                            type="button" role="tab">
                                        <i class="<?php echo $meta['icon']; ?> me-3 fs-5"></i>
                                        <span class="fw-bold"><?php echo $meta['label']; ?></span>
                                    </button>
                                <?php $first = false; endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="col-lg-9">
                    <div class="tab-content" id="settings-pills-tabContent">
                        <?php $first = true; foreach ($group_names as $group_key => $group_meta): ?>
                            <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" 
                                 id="pill-<?php echo $group_key; ?>" 
                                 role="tabpanel">
                                
                                <div class="dashboard-card border-0 shadow-sm mb-4 overflow-hidden">
                                    <div class="card-header bg-light-blue p-4 border-0 d-flex align-items-center">
                                        <div class="icon-circle me-3">
                                            <i class="<?php echo $group_meta['icon']; ?> text-primary"></i>
                                        </div>
                                        <h4 class="mb-0 fw-bold"><?php echo $group_meta['label']; ?></h4>
                                    </div>
                                    <div class="card-body p-4 pt-2">
                                        <?php if (isset($groups[$group_key])): ?>
                                            <?php foreach ($groups[$group_key] as $setting): ?>
                                                <div class="setting-item py-4 border-bottom last-border-0">
                                                    <div class="row align-items-start">
                                                        <div class="col-md-4 mb-2 mb-md-0">
                                                            <label class="form-label d-block fw-bold text-dark mb-1">
                                                                <?php echo $setting['label']; ?>
                                                            </label>
                                                            <small class="text-muted"><code><?php echo $setting['setting_key']; ?></code></small>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <?php if ($setting['input_type'] === 'textarea'): ?>
                                                                <textarea name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                                          class="form-control rounded-3" 
                                                                          rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                                            
                                                            <?php elseif ($setting['input_type'] === 'toggle'): ?>
                                                                <div class="form-check form-switch form-switch-lg mt-1">
                                                                    <input type="hidden" name="settings[<?php echo $setting['setting_key']; ?>]" value="0">
                                                                    <input class="form-check-input" type="checkbox" 
                                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                                           value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label text-muted small ms-2">Enabled / Active</label>
                                                                </div>
                                                            
                                                            <?php elseif ($setting['input_type'] === 'file'): ?>
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <?php if (!empty($setting['setting_value'])): ?>
                                                                        <div class="current-file-preview">
                                                                            <img src="../<?php echo $setting['setting_value']; ?>" alt="Logo" class="rounded border" style="height: 50px; width: 50px; object-fit: contain; background: #f8f9fa;">
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div class="flex-grow-1">
                                                                        <input type="file" name="file_<?php echo $setting['setting_key']; ?>" class="form-control rounded-3">
                                                                        <small class="text-muted mt-1 d-block">Current Path: <?php echo htmlspecialchars($setting['setting_value']); ?></small>
                                                                    </div>
                                                                </div>
                                                            
                                                            <?php else: ?>
                                                                <input type="<?php echo $setting['input_type']; ?>" 
                                                                       name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>" 
                                                                       class="form-control rounded-3">
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="fas fa-ghost fs-1 text-light mb-3"></i>
                                                <p class="text-muted">No settings found in this category.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php $first = false; endforeach; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<style>
/* Modern Admin Settings UI Enhancements */
.bg-light-blue { background-color: rgba(70, 128, 255, 0.04); }
.last-border-0:last-child { border-bottom: 0 !important; }

.nav-pills .nav-link {
    color: #475569;
    transition: all 0.2s ease-in-out;
}
.nav-pills .nav-link:hover {
    background-color: rgba(70, 128, 255, 0.05);
    color: #4680ff;
}
.nav-pills .nav-link.active {
    background: var(--primary-gradient);
    color: white !important;
    box-shadow: 0 4px 12px rgba(0, 33, 71, 0.2);
}

.icon-circle {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.form-switch-lg .form-check-input {
    width: 3.5rem;
    height: 1.75rem;
    cursor: pointer;
}

.setting-item {
    transition: background 0.2s;
}
.setting-item:hover {
    background-color: #fafbfc;
}

.form-control:focus {
    border-color: #4680ff;
    box-shadow: 0 0 0 0.25rem rgba(70, 128, 255, 0.1);
}

/* Animations */
.animate__fadeInDown {
    animation: fadeInDown 0.5s ease-out;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include 'footer.php'; ?>
