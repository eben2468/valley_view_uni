<?php
require_once('../includes/db_connect.php');

$action = $_GET['action'] ?? 'edit';
$id = $_GET['id'] ?? null;
$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $full_description = $_POST['full_description'];
    $link_url = $_POST['link_url'];
    $duration = $_POST['duration'];
    $level = $_POST['level'];
    $campus = $_POST['campus'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $display_order = (int)$_POST['display_order'];
    
    // Process JSON fields (learning points and career paths)
    $learning_points = array_filter($_POST['learning_points'] ?? []);
    $career_paths = array_filter($_POST['career_paths'] ?? []);
    
    $lp_json = json_encode(array_values($learning_points));
    $cp_json = json_encode(array_values($career_paths));

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO academic_programs (category_id, title, description, full_description, link_url, duration, level, campus, learning_points, career_paths, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $title, $description, $full_description, $link_url, $duration, $level, $campus, $lp_json, $cp_json, $is_active, $display_order]);
        header("Location: manage_programs.php?added=1");
        exit();
    } else {
        $stmt = $pdo->prepare("UPDATE academic_programs SET category_id = ?, title = ?, description = ?, full_description = ?, link_url = ?, duration = ?, level = ?, campus = ?, learning_points = ?, career_paths = ?, is_active = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$category_id, $title, $description, $full_description, $link_url, $duration, $level, $campus, $lp_json, $cp_json, $is_active, $display_order, $id]);
        $message = '<div class="alert alert-success">Program updated successfully!</div>';
    }
}

include 'header.php';
include 'sidebar.php';

// Fetch Categories for dropdown
$categories = $pdo->query("SELECT * FROM program_categories ORDER BY name ASC")->fetchAll();

// Fetch Program Data if editing
$program = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM academic_programs WHERE id = ?");
    $stmt->execute([$id]);
    $program = $stmt->fetch();
    
    $lp_data = json_decode($program['learning_points'] ?? '[]', true);
    $cp_data = json_decode($program['career_paths'] ?? '[]', true);
} else {
    $lp_data = ["", "", "", ""];
    $cp_data = ["", "", "", "", ""];
}
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0"><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Program</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="manage_programs.php">Programs</a></li>
                    <li class="breadcrumb-item active"><?php echo $action === 'add' ? 'Add' : 'Edit'; ?></li>
                </ol>
            </nav>
        </div>
        <a href="manage_programs.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php echo $message; ?>

    <form method="POST" class="row g-4">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">General Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Program Title *</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($program['title'] ?? ''); ?>" required placeholder="e.g. BSc Computer Science">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Brief overview for the program list..."><?php echo htmlspecialchars($program['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Description</label>
                        <textarea name="full_description" class="form-control" rows="8" placeholder="Detailed program description for the details page..."><?php echo htmlspecialchars($program['full_description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="dashboard-card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Learning Points & Career Paths</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-3">What You'll Learn</label>
                            <div id="lp-container">
                                <?php foreach ($lp_data as $i => $point): ?>
                                <div class="input-group mb-2">
                                    <input type="text" name="learning_points[]" class="form-control" value="<?php echo htmlspecialchars($point); ?>" placeholder="Point <?php echo $i+1; ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addInput('lp-container', 'learning_points[]')">
                                <i class="fas fa-plus"></i> Add Point
                            </button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-3">Career Opportunities</label>
                            <div id="cp-container">
                                <?php foreach ($cp_data as $i => $path): ?>
                                <div class="input-group mb-2">
                                    <input type="text" name="career_paths[]" class="form-control" value="<?php echo htmlspecialchars($path); ?>" placeholder="Career <?php echo $i+1; ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addInput('cp-container', 'career_paths[]')">
                                <i class="fas fa-plus"></i> Add Path
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Settings & Categorization</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">School / Faculty *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($program && $program['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Academic Level</label>
                        <select name="level" class="form-select">
                            <option value="Undergraduate" <?php echo ($program && $program['level'] == 'Undergraduate') ? 'selected' : ''; ?>>Undergraduate</option>
                            <option value="Postgraduate" <?php echo ($program && $program['level'] == 'Postgraduate') ? 'selected' : ''; ?>>Postgraduate</option>
                            <option value="Diploma" <?php echo ($program && $program['level'] == 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                            <option value="Certificate" <?php echo ($program && $program['level'] == 'Certificate') ? 'selected' : ''; ?>>Certificate</option>
                            <option value="Professional" <?php echo ($program && $program['level'] == 'Professional') ? 'selected' : ''; ?>>Professional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control" value="<?php echo htmlspecialchars($program['duration'] ?? '4 Years (Full Time)'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Campus</label>
                        <input type="text" name="campus" class="form-control" value="<?php echo htmlspecialchars($program['campus'] ?? 'Main Campus'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">External Link (Optional)</label>
                        <input type="url" name="link_url" class="form-control" value="<?php echo htmlspecialchars($program['link_url'] ?? ''); ?>" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="<?php echo $program['display_order'] ?? 0; ?>">
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo (!isset($program) || $program['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="isActive">Program Active & Visible</label>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-save me-2"></i> Save Program
                    </button>
                    <a href="manage_programs.php" class="btn btn-light w-100 mt-2">Cancel</a>
                </div>
            </div>
            
            <div class="dashboard-card">
                 <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Ensure all fields marked with * are filled correctly.</p>
                    <?php if ($id): ?>
                    <button type="button" class="btn btn-outline-danger w-100" onclick="if(confirm('Delete this program?')) window.location.href='manage_programs.php?delete_id=<?php echo $id; ?>'">
                        <i class="fas fa-trash me-2"></i> Delete Program
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
function addInput(containerId, name) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<input type="text" name="${name}" class="form-control" placeholder="New entry...">
                     <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                     </button>`;
    container.appendChild(div);
}
</script>

<?php include 'footer.php'; ?>
