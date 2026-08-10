<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
require_once __DIR__ . '/../includes/admin_auth.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'update_page') {
            $image_url = $_POST['hero_image'];
            if (function_exists('handleAdminFileUpload')) {
                $uploaded = handleAdminFileUpload($_FILES['hero_image_file'] ?? null, 'directory');
                if ($uploaded) $image_url = $uploaded;
            }
            
            $stmt = $pdo->prepare("UPDATE university_directory_page SET hero_badge=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image=?, cta_heading=?, cta_subtitle=?, cta_text=?, cta_btn1_text=?, cta_btn1_url=?, cta_btn2_text=?, cta_btn2_url=?, stat1_value=?, stat1_label=?, stat2_value=?, stat2_label=?, stat3_value=?, stat3_label=? WHERE id=?");
            $stmt->execute([
                $_POST['hero_badge'], $_POST['hero_title'], $_POST['hero_subtitle'], $_POST['hero_description'], $image_url,
                $_POST['cta_heading'], $_POST['cta_subtitle'], $_POST['cta_text'],
                $_POST['cta_btn1_text'], $_POST['cta_btn1_url'], $_POST['cta_btn2_text'], $_POST['cta_btn2_url'],
                $_POST['stat1_value'], $_POST['stat1_label'], $_POST['stat2_value'], $_POST['stat2_label'], $_POST['stat3_value'], $_POST['stat3_label'],
                $_POST['id']
            ]);
            $success = "Page content updated successfully!";
        }
        elseif ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO university_directory (name, title, category, display_order, email, phone, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['name'], $_POST['title'], $_POST['category'], (int)$_POST['display_order'], $_POST['email'], $_POST['phone'] ?? '', isset($_POST['is_active']) ? 1 : 0]);
            $success = "Entry added successfully!";
        }
        elseif ($action === 'edit') {
            $stmt = $pdo->prepare("UPDATE university_directory SET name=?, title=?, category=?, display_order=?, email=?, phone=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['name'], $_POST['title'], $_POST['category'], (int)$_POST['display_order'], $_POST['email'], $_POST['phone'] ?? '', isset($_POST['is_active']) ? 1 : 0, $_POST['id']]);
            $success = "Entry updated successfully!";
        }
        elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM university_directory WHERE id = ?")->execute([$_POST['id']]);
            $success = "Entry deleted successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch page content
$page_content = $pdo->query("SELECT * FROM university_directory_page WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// Fetch all entries
$entries = $pdo->query("SELECT * FROM university_directory ORDER BY category, display_order ASC")->fetchAll(PDO::FETCH_ASSOC);

// Categories
$categories = [
    'Principal Officers',
    'Campus Administration',
    'Academic Deans & Research',
    'Departmental & Unit Heads',
    'University Directors',
    'Associate Officers & Section Heads',
    'Financial Officers',
    'Operations & Services Support'
];

$page_title = "University Directory";
$accent = '#002147';

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-sitemap"></i> University Directory</h1>
        <p class="page-description">Manage the university's leadership hierarchy on the public directory page.</p>
    </div></div>

    <?php if ($success): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:#ecfdf5;border:1px solid #10b981;color:#065f46;">
        <i class="fas fa-check-circle"></i> <strong><?php echo htmlspecialchars($success); ?></strong>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:#fef2f2;border:1px solid #ef4444;color:#991b1b;">
        <i class="fas fa-exclamation-triangle"></i> <strong><?php echo htmlspecialchars($error); ?></strong>
    </div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="dirTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_page" type="button">Hero & CTA</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_entries" type="button">Directory Entries</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Page Content Tab -->
        <div class="tab-pane fade show active" id="tab_page" role="tabpanel">
            <?php if ($page_content): ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_page">
                <input type="hidden" name="id" value="<?php echo $page_content['id']; ?>">

                <!-- Hero Section -->
                <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;padding:30px;margin-bottom:25px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:25px;">
                        <div style="width:45px;height:45px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;"><i class="fas fa-image"></i></div>
                        <div><h3 style="margin:0;font-size:1.3rem;font-weight:800;color:#1e293b;">Hero Section</h3>
                        <p style="margin:0;color:#64748b;font-size:.85rem;">Main header content of the directory page</p></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Badge Text</label>
                            <input type="text" name="hero_badge" class="form-control" value="<?php echo htmlspecialchars($page_content['hero_badge']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Title (Line 1)</label>
                            <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($page_content['hero_title']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Subtitle (Line 2)</label>
                            <input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($page_content['hero_subtitle']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="hero_description" class="form-control" rows="2"><?php echo htmlspecialchars($page_content['hero_description']); ?></textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Background Image URL</label>
                            <input type="text" name="hero_image" class="form-control" value="<?php echo htmlspecialchars($page_content['hero_image']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Or Upload</label>
                            <input type="file" name="hero_image_file" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;padding:30px;margin-bottom:25px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:25px;">
                        <div style="width:45px;height:45px;background:#f2683815;color:#f26838;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;"><i class="fas fa-bullhorn"></i></div>
                        <div><h3 style="margin:0;font-size:1.3rem;font-weight:800;color:#1e293b;">CTA Section</h3>
                        <p style="margin:0;color:#64748b;font-size:.85rem;">Call-to-action at the bottom of the page</p></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">CTA Heading</label>
                            <input type="text" name="cta_heading" class="form-control" value="<?php echo htmlspecialchars($page_content['cta_heading']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">CTA Subtitle</label>
                            <input type="text" name="cta_subtitle" class="form-control" value="<?php echo htmlspecialchars($page_content['cta_subtitle']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">CTA Text</label>
                            <textarea name="cta_text" class="form-control" rows="2"><?php echo htmlspecialchars($page_content['cta_text']); ?></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Button 1 Text</label>
                            <input type="text" name="cta_btn1_text" class="form-control" value="<?php echo htmlspecialchars($page_content['cta_btn1_text']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Button 1 URL</label>
                            <input type="text" name="cta_btn1_url" class="form-control" value="<?php echo htmlspecialchars($page_content['cta_btn1_url']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Button 2 Text</label>
                            <input type="text" name="cta_btn2_text" class="form-control" value="<?php echo htmlspecialchars($page_content['cta_btn2_text']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Button 2 URL</label>
                            <input type="text" name="cta_btn2_url" class="form-control" value="<?php echo htmlspecialchars($page_content['cta_btn2_url']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;padding:30px;margin-bottom:25px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:25px;">
                        <div style="width:45px;height:45px;background:#10b98115;color:#10b981;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;"><i class="fas fa-chart-bar"></i></div>
                        <div><h3 style="margin:0;font-size:1.3rem;font-weight:800;color:#1e293b;">CTA Statistics</h3>
                        <p style="margin:0;color:#64748b;font-size:.85rem;">Three stat counters shown at the bottom</p></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-2"><label class="form-label small fw-bold">Stat 1 Value</label><input type="text" name="stat1_value" class="form-control" value="<?php echo htmlspecialchars($page_content['stat1_value']); ?>"></div>
                        <div class="col-md-2"><label class="form-label small fw-bold">Stat 1 Label</label><input type="text" name="stat1_label" class="form-control" value="<?php echo htmlspecialchars($page_content['stat1_label']); ?>"></div>
                        <div class="col-md-2"><label class="form-label small fw-bold">Stat 2 Value</label><input type="text" name="stat2_value" class="form-control" value="<?php echo htmlspecialchars($page_content['stat2_value']); ?>"></div>
                        <div class="col-md-2"><label class="form-label small fw-bold">Stat 2 Label</label><input type="text" name="stat2_label" class="form-control" value="<?php echo htmlspecialchars($page_content['stat2_label']); ?>"></div>
                        <div class="col-md-2"><label class="form-label small fw-bold">Stat 3 Value</label><input type="text" name="stat3_value" class="form-control" value="<?php echo htmlspecialchars($page_content['stat3_value']); ?>"></div>
                        <div class="col-md-2"><label class="form-label small fw-bold">Stat 3 Label</label><input type="text" name="stat3_label" class="form-control" value="<?php echo htmlspecialchars($page_content['stat3_label']); ?>"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg px-5"><i class="fas fa-save me-2"></i>Save Page Content</button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Entries Tab -->
        <div class="tab-pane fade" id="tab_entries" role="tabpanel">
            <!-- Add New -->
            <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
                <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-sitemap"></i></div>
                <div style="position:relative;z-index:1;">
                    <h3 style="font-weight:900;font-size:1.5rem;margin-bottom:5px;">Add New Entry</h3>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-3">
                            <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Full Name (with titles)" required></div>
                            <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Position / Job Title" required></div>
                            <div class="col-md-2">
                                <select name="category" class="form-select" required>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-1"><input type="number" name="display_order" class="form-control" placeholder="#" value="0"></div>
                            <div class="col-md-2"><input type="email" name="email" class="form-control" placeholder="Email (optional)"></div>
                            <div class="col-md-1"><button type="submit" class="btn btn-light fw-bold w-100"><i class="fas fa-plus"></i> Add</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search -->
            <div style="margin-bottom:20px;">
                <input type="text" id="adminDirSearch" placeholder="🔍 Filter entries..." style="padding:12px 20px;border:2px solid #e2e8f0;border-radius:14px;width:100%;max-width:400px;font-size:.95rem;">
            </div>

            <!-- Entries by Category -->
            <?php foreach ($categories as $cat): ?>
            <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:30px;">
                <div style="padding:18px 25px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;background:#f8fafc;">
                    <h4 style="margin:0;font-weight:800;color:#1e293b;font-size:1.1rem;text-transform:uppercase;letter-spacing:.05em;"><?php echo $cat; ?></h4>
                    <span style="background:<?php echo $accent;?>15;color:<?php echo $accent;?>;padding:4px 14px;border-radius:20px;font-weight:800;font-size:.8rem;">
                        <?php echo count(array_filter($entries, fn($e) => $e['category'] === $cat)); ?> entries
                    </span>
                </div>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;" class="admin-dir-table">
                        <thead>
                            <tr style="background:#fafbfc;">
                                <th style="padding:10px 15px;text-align:left;font-weight:800;color:#475569;font-size:.75rem;text-transform:uppercase;width:50px;">#</th>
                                <th style="padding:10px 15px;text-align:left;font-weight:800;color:#475569;font-size:.75rem;text-transform:uppercase;">Name</th>
                                <th style="padding:10px 15px;text-align:left;font-weight:800;color:#475569;font-size:.75rem;text-transform:uppercase;">Position</th>
                                <th style="padding:10px 15px;text-align:left;font-weight:800;color:#475569;font-size:.75rem;text-transform:uppercase;">Email</th>
                                <th style="padding:10px 15px;text-align:center;font-weight:800;color:#475569;font-size:.75rem;text-transform:uppercase;width:60px;">Active</th>
                                <th style="padding:10px 15px;text-align:center;font-weight:800;color:#475569;font-size:.75rem;text-transform:uppercase;width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $found = false;
                            foreach ($entries as $entry): 
                                if ($entry['category'] !== $cat) continue;
                                $found = true;
                            ?>
                            <tr style="border-bottom:1px solid #f1f5f9;" class="dir-row" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                <form method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($entry['category']); ?>">
                                    <td style="padding:10px 15px;"><input type="number" name="display_order" class="form-control form-control-sm" value="<?php echo $entry['display_order']; ?>" style="width:55px;"></td>
                                    <td style="padding:10px 15px;"><input type="text" name="name" class="form-control form-control-sm fw-bold" value="<?php echo htmlspecialchars($entry['name']); ?>"></td>
                                    <td style="padding:10px 15px;"><input type="text" name="title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($entry['title']); ?>"></td>
                                    <td style="padding:10px 15px;"><input type="email" name="email" class="form-control form-control-sm" value="<?php echo htmlspecialchars($entry['email']); ?>"></td>
                                    <td style="padding:10px 15px;text-align:center;">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $entry['is_active'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td style="padding:10px 15px;text-align:center;">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="submit" class="btn btn-sm btn-success" title="Save"><i class="fas fa-save"></i></button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $entry['id']; ?>)" title="Delete"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (!$found): ?>
                            <tr><td colspan="6" style="padding:25px;text-align:center;color:#94a3b8;font-style:italic;">No entries in this category</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</main>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<style>
    .nav-tabs .nav-link { color:#4b5563; font-weight:700; padding:14px 24px; border:none; border-bottom:3px solid transparent; transition:all .3s ease; font-size:1rem; }
    .nav-tabs .nav-link.active { color:<?php echo $accent;?>; background:transparent; border-bottom-color:<?php echo $accent;?>; }
    input:focus, select:focus, textarea:focus { outline:none; border-color:<?php echo $accent;?> !important; box-shadow:0 0 0 4px <?php echo $accent;?>1a; }
    @media(max-width:768px) { .row { margin-left:0 !important; margin-right:0 !important; } }
</style>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this entry?')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Simple admin search
document.getElementById('adminDirSearch').addEventListener('keyup', function() {
    let v = this.value.toLowerCase();
    document.querySelectorAll('.dir-row').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(v) ? '' : 'none';
    });
});
</script>

<?php include 'footer.php'; ?>
