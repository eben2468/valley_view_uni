<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');

$message = '';

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM academic_programs_stats WHERE id = ?");
    $stmt->execute([$delete_id]);
    $message = '<div class="alert alert-success">Statistic deleted successfully!</div>';
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stat_value = $_POST['stat_value'];
    $stat_label = $_POST['stat_label'];
    $display_order = (int)$_POST['display_order'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE academic_programs_stats SET stat_value = ?, stat_label = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$stat_value, $stat_label, $display_order, $id]);
        $message = '<div class="alert alert-success">Statistic updated successfully!</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO academic_programs_stats (stat_value, stat_label, display_order) VALUES (?, ?, ?)");
        $stmt->execute([$stat_value, $stat_label, $display_order]);
        $message = '<div class="alert alert-success">Statistic added successfully!</div>';
    }
}

// Fetch all stats
$stats = $pdo->query("SELECT * FROM academic_programs_stats ORDER BY display_order ASC")->fetchAll();

// Get specific stat for editing if requested
$edit_stat = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM academic_programs_stats WHERE id = ?");
    $stmt->execute([(int)$_GET['edit_id']]);
    $edit_stat = $stmt->fetch();
}
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Academic Program Statistics</h4>
            <p class="text-muted mb-0">Manage the counters shown in the blue section of the programs page</p>
        </div>
        <a href="manage_programs.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Programs
        </a>
    </div>

    <?php echo $message; ?>

    <div class="row g-4">
        <!-- Stat Form -->
        <div class="col-lg-4">
            <div class="dashboard-card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $edit_stat ? 'Edit' : 'Add New'; ?> Statistic</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit_stat): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_stat['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Metric Value *</label>
                            <input type="text" name="stat_value" class="form-control" value="<?php echo htmlspecialchars($edit_stat['stat_value'] ?? ''); ?>" required placeholder="e.g. 100+ or 1979">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Label *</label>
                            <input type="text" name="stat_label" class="form-control" value="<?php echo htmlspecialchars($edit_stat['stat_label'] ?? ''); ?>" required placeholder="e.g. Programs or Faculties">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo $edit_stat['display_order'] ?? 0; ?>">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> <?php echo $edit_stat ? 'Update' : 'Save'; ?> Statistic
                            </button>
                            <?php if ($edit_stat): ?>
                                <a href="manage_program_stats.php" class="btn btn-light border">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="alert alert-info mt-4 py-2 border-0 shadow-sm">
                <i class="fas fa-lightbulb me-2"></i> Recommended: Add exactly 4 statistics for the best layout balance.
            </div>
        </div>

        <!-- Stat List -->
        <div class="col-lg-8">
            <div class="dashboard-card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Current Overview Page Statistics</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Value</th>
                                    <th>Label</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($stats)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No statistics found.</td>
                                </tr>
                                <?php endif; ?>
                                <?php foreach ($stats as $stat): ?>
                                <tr>
                                    <td><?php echo $stat['display_order']; ?></td>
                                    <td>
                                        <h4 class="mb-0 text-primary fw-bold"><?php echo htmlspecialchars($stat['stat_value']); ?></h4>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-muted font-monospace"><?php echo htmlspecialchars($stat['stat_label']); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="?edit_id=<?php echo $stat['id']; ?>" class="btn btn-sm btn-icon btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-icon btn-danger" title="Delete" onclick="if(confirm('Delete this statistic?')) window.location.href='?delete_id=<?php echo $stat['id']; ?>'">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Live Preview Card -->
            <div class="dashboard-card mt-4 border-0" style="background: linear-gradient(135deg, #2563eb, #1e40af);">
                <div class="card-header border-0 pb-0">
                    <h6 class="text-white-50 small text-uppercase fw-bold">Design Preview</h6>
                </div>
                <div class="card-body py-4">
                    <div class="row g-4 text-center">
                        <?php foreach ($stats as $stat): ?>
                        <div class="col-md-3">
                            <h2 class="text-white fw-black mb-1"><?php echo htmlspecialchars($stat['stat_value']); ?></h2>
                            <p class="text-blue-200 small mb-0 fw-semibold"><?php echo htmlspecialchars($stat['stat_label']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.btn-icon { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
.fw-black { font-weight: 900; }
</style>

<?php include 'footer.php'; ?>
