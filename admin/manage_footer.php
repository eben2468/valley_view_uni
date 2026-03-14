<?php
include 'header.php';
include 'sidebar.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_settings'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $pdo->prepare("UPDATE footer_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        $success_msg = "Footer settings updated successfully!";
    }

    if (isset($_POST['add_link'])) {
        $section_id = $_POST['section_id'];
        $label = $_POST['label'];
        $url = $_POST['url'];
        $icon = $_POST['icon_class'] ?? null;
        $order = $_POST['display_order'] ?? 0;
        
        $stmt = $pdo->prepare("INSERT INTO footer_links (section_id, label, url, icon_class, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$section_id, $label, $url, $icon, $order]);
        $success_msg = "New link added successfully!";
    }

    if (isset($_POST['update_link'])) {
        $id = $_POST['link_id'];
        $label = $_POST['label'];
        $url = $_POST['url'];
        $icon = $_POST['icon_class'] ?? null;
        $order = $_POST['display_order'] ?? 0;
        $active = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE footer_links SET label = ?, url = ?, icon_class = ?, display_order = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$label, $url, $icon, $order, $active, $id]);
        $success_msg = "Link updated successfully!";
    }

    if (isset($_POST['delete_link'])) {
        $id = $_POST['link_id'];
        $stmt = $pdo->prepare("DELETE FROM footer_links WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = "Link deleted successfully!";
    }
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM footer_settings");
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM footer_sections ORDER BY display_order");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM footer_links ORDER BY section_id, display_order");
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grouped_links = [];
foreach ($links as $link) {
    $grouped_links[$link['section_id']][] = $link;
}
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">Footer Management</h2>
                        <p class="text-muted">Manage all content, links, and contact information displayed in the website footer.</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- General Settings -->
            <div class="col-lg-12 mb-4">
                <div class="dashboard-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-cog me-2"></i>General Footer Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row g-4">
                                <?php foreach ($settings as $setting): ?>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><?php echo $setting['label']; ?></label>
                                        <?php if ($setting['setting_key'] == 'connect_description'): ?>
                                            <textarea name="settings[<?php echo $setting['setting_key']; ?>]" class="form-control rounded-3" rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                        <?php else: ?>
                                            <input type="text" name="settings[<?php echo $setting['setting_key']; ?>]" value="<?php echo htmlspecialchars($setting['setting_value']); ?>" class="form-control rounded-3">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-4 text-end">
                                <button type="submit" name="update_settings" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update All Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sections Management -->
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="card-header border-bottom-0">
                        <h5><i class="fas fa-list-ul me-2"></i>Footer Link Columns</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills p-3 bg-light rounded-4 m-3 mb-0" id="pills-tab" role="tablist">
                            <?php foreach ($sections as $index => $section): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $index == 0 ? 'active' : ''; ?> fw-bold rounded-3 me-2" 
                                            id="pill-<?php echo $section['id']; ?>-tab" 
                                            data-bs-toggle="pill" 
                                            data-bs-target="#pill-<?php echo $section['id']; ?>" 
                                            type="button" role="tab">
                                        <?php echo $section['title']; ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content p-4" id="pills-tabContent">
                            <?php foreach ($sections as $index => $section): ?>
                                <div class="tab-pane fade <?php echo $index == 0 ? 'show active' : ''; ?>" 
                                     id="pill-<?php echo $section['id']; ?>" role="tabpanel">
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="fw-bold mb-0">Managing: <?php echo $section['title']; ?></h6>
                                        <button class="btn btn-sm btn-success rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#addLinkModal<?php echo $section['id']; ?>">
                                            <i class="fas fa-plus me-1"></i> Add New Link
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table modern-table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Label</th>
                                                    <th>URL / Icon</th>
                                                    <th>Order</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (isset($grouped_links[$section['id']])): ?>
                                                    <?php foreach ($grouped_links[$section['id']] as $link): ?>
                                                        <tr>
                                                            <td>
                                                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($link['label']); ?></span>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted d-block"><?php echo htmlspecialchars($link['url']); ?></small>
                                                                <?php if ($link['icon_class']): ?>
                                                                    <span class="badge bg-light text-dark border"><i class="fab <?php echo $link['icon_class']; ?> me-1"></i><?php echo $link['icon_class']; ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo $link['display_order']; ?></td>
                                                            <td>
                                                                <?php if ($link['is_active']): ?>
                                                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger-subtle text-danger px-3 rounded-pill">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="btn-group">
                                                                    <button class="btn btn-sm btn-light border rounded-3 me-2" data-bs-toggle="modal" data-bs-target="#editLinkModal<?php echo $link['id']; ?>">
                                                                        <i class="fas fa-edit text-primary"></i>
                                                                    </button>
                                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this link?');">
                                                                        <input type="hidden" name="link_id" value="<?php echo $link['id']; ?>">
                                                                        <button type="submit" name="delete_link" class="btn btn-sm btn-light border rounded-3">
                                                                            <i class="fas fa-trash text-danger"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        <!-- Edit Link Modal -->
                                                        <div class="modal fade" id="editLinkModal<?php echo $link['id']; ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content border-0 shadow-lg rounded-4">
                                                                    <div class="modal-header border-bottom-0 pb-0">
                                                                        <h5 class="fw-bold">Edit Link</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="POST" action="">
                                                                        <div class="modal-body p-4">
                                                                            <input type="hidden" name="link_id" value="<?php echo $link['id']; ?>">
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-bold">Link Label</label>
                                                                                <input type="text" name="label" value="<?php echo htmlspecialchars($link['label']); ?>" class="form-control rounded-3" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-bold">Link URL</label>
                                                                                <input type="text" name="url" value="<?php echo htmlspecialchars($link['url']); ?>" class="form-control rounded-3" required>
                                                                            </div>
                                                                            <?php if ($section['id'] == 4): ?>
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-bold">Icon Class (e.g. fa-facebook)</label>
                                                                                <input type="text" name="icon_class" value="<?php echo htmlspecialchars($link['icon_class']); ?>" class="form-control rounded-3">
                                                                            </div>
                                                                            <?php endif; ?>
                                                                            <div class="row">
                                                                                <div class="col-6 mb-3">
                                                                                    <label class="form-label fw-bold">Display Order</label>
                                                                                    <input type="number" name="display_order" value="<?php echo $link['display_order']; ?>" class="form-control rounded-3">
                                                                                </div>
                                                                                <div class="col-6 mb-3 d-flex align-items-end">
                                                                                    <div class="form-check form-switch mb-2">
                                                                                        <input class="form-check-input" type="checkbox" name="is_active" <?php echo $link['is_active'] ? 'checked' : ''; ?>>
                                                                                        <label class="form-check-label fw-bold">Is Active</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer border-top-0 pt-0">
                                                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit" name="update_link" class="btn btn-primary rounded-3 text-white px-4">Save Changes</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">No links found in this section.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Add Link Modal -->
                                <div class="modal fade" id="addLinkModal<?php echo $section['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="fw-bold">Add New Link to <?php echo $section['title']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Link Label</label>
                                                        <input type="text" name="label" class="form-control rounded-3" placeholder="e.g. About Us" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Link URL</label>
                                                        <input type="text" name="url" class="form-control rounded-3" placeholder="e.g. about_us.php" required>
                                                    </div>
                                                    <?php if ($section['id'] == 4): ?>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Icon Class (e.g. fa-facebook)</label>
                                                        <input type="text" name="icon_class" class="form-control rounded-3" placeholder="fa-facebook">
                                                    </div>
                                                    <?php endif; ?>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Display Order</label>
                                                        <input type="number" name="display_order" value="0" class="form-control rounded-3">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="add_link" class="btn btn-primary rounded-3 text-white px-4">Add Link</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link {
        color: #6c757d;
        background: transparent;
        transition: var(--transition);
        border: 1px solid transparent;
    }
    .nav-pills .nav-link.active {
        background: white !important;
        color: #002147 !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05) !important;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .nav-pills .nav-link:hover:not(.active) {
        background: rgba(0, 0, 0, 0.05);
    }
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }
    .modern-table thead th {
        font-size: 0.75rem !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<?php include 'footer.php'; ?>
