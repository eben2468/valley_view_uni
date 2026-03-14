<?php
require_once('../includes/db_connect.php');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

if (isset($_GET['delete_id'])) {
    $pdo->prepare("DELETE FROM academic_programs WHERE id = ?")->execute([(int)$_GET['delete_id']]);
    header("Location: manage_programs.php?deleted=1"); exit();
}

$page_title = "Academic Programs";
$current_page = 'manage_programs.php';
$accent = '#2563eb';

include 'header.php';
include 'sidebar.php';

$programs = $pdo->query("SELECT ap.*, pc.name as category_name FROM academic_programs ap LEFT JOIN program_categories pc ON ap.category_id = pc.id ORDER BY pc.name ASC, ap.title ASC")->fetchAll();
$cat_count = $pdo->query("SELECT COUNT(*) FROM program_categories")->fetchColumn();
$active_count = $pdo->query("SELECT COUNT(*) FROM academic_programs WHERE is_active = 1")->fetchColumn();
$inactive_count = count($programs) - $active_count;
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-graduation-cap"></i> Academic Programs</h1>
        <p class="page-description">Manage all undergraduate and postgraduate programs.</p>
    </div></div>

    <?php if (isset($_GET['deleted'])): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:#ecfdf5;border:1px solid #10b981;color:#065f46;">
        <i class="fas fa-check-circle"></i> <strong>Done!</strong> Program deleted successfully.
    </div>
    <?php endif; ?>

    <!-- Page Header Card -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-graduation-cap"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <span style="background:rgba(255,255,255,0.2);padding:5px 15px;border-radius:20px;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Management</span>
                <h2 style="margin:10px 0 5px;font-size:2rem;font-weight:900;">Programs & Courses</h2>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="manage_program_categories.php" style="padding:10px 20px;background:rgba(255,255,255,0.15);color:#fff;border-radius:12px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,0.3);backdrop-filter:blur(5px);">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="manage_program_page_content.php" style="padding:10px 20px;background:rgba(255,255,255,0.15);color:#fff;border-radius:12px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,0.3);backdrop-filter:blur(5px);">
                    <i class="fas fa-edit"></i> Page Content
                </a>
                <a href="manage_program_stats.php" style="padding:10px 20px;background:rgba(255,255,255,0.15);color:#fff;border-radius:12px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,0.3);backdrop-filter:blur(5px);">
                    <i class="fas fa-chart-bar"></i> Stats
                </a>
                <a href="edit_program_detail.php?action=add" style="padding:10px 20px;background:#fff;color:#1e293b;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-plus"></i> Add Program
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:35px;">
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#eff6ff;color:<?php echo $accent;?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-graduation-cap"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:<?php echo $accent;?>;line-height:1;"><?php echo count($programs); ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Total Programs</div>
        </div>
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#ecfdf5;color:#10b981;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-check-circle"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:#10b981;line-height:1;"><?php echo $active_count; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Active</div>
        </div>
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#fef2f2;color:#ef4444;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-pause-circle"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:#ef4444;line-height:1;"><?php echo $inactive_count; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Inactive</div>
        </div>
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#fff7ed;color:#f59e0b;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-university"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:#f59e0b;line-height:1;"><?php echo $cat_count; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Faculties</div>
        </div>
    </div>

    <!-- Programs Table -->
    <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;overflow:hidden;">
        <div style="padding:25px 30px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:45px;height:45px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;"><i class="fas fa-list-ul"></i></div>
                <div><h3 style="margin:0;font-size:1.3rem;font-weight:800;color:#1e293b;">Program List</h3>
                <p style="margin:0;color:#64748b;font-size:.85rem;">All registered academic programs</p></div>
            </div>
            <input type="text" id="tableSearch" placeholder="🔍 Search programs..." style="padding:10px 20px;border:2px solid #e2e8f0;border-radius:12px;min-width:250px;font-size:.9rem;">
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;" id="programsTable">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;">Program Title</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Category</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Level</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Duration</th>
                        <th style="padding:14px 20px;text-align:center;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Status</th>
                        <th style="padding:14px 20px;text-align:center;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $program): ?>
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background .2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                        <td style="padding:14px 20px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:40px;height:40px;background:#eff6ff;color:<?php echo $accent;?>;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-book-open"></i></div>
                                <div>
                                    <div style="font-weight:700;color:#1e293b;"><?php echo htmlspecialchars($program['title']); ?></div>
                                    <div style="font-size:.8rem;color:#94a3b8;"><?php echo htmlspecialchars($program['campus']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 20px;">
                            <span style="padding:5px 12px;border-radius:8px;font-weight:700;font-size:.8rem;background:#f1f5f9;color:#475569;"><?php echo htmlspecialchars($program['category_name'] ?? 'Uncategorized'); ?></span>
                        </td>
                        <td style="padding:14px 20px;font-weight:600;color:#475569;"><?php echo htmlspecialchars($program['level']); ?></td>
                        <td style="padding:14px 20px;color:#64748b;"><?php echo htmlspecialchars($program['duration']); ?></td>
                        <td style="padding:14px 20px;text-align:center;">
                            <?php if ($program['is_active']): ?>
                                <span style="padding:5px 14px;border-radius:20px;font-weight:800;font-size:.75rem;background:#ecfdf5;color:#059669;">● Active</span>
                            <?php else: ?>
                                <span style="padding:5px 14px;border-radius:20px;font-weight:800;font-size:.75rem;background:#fef2f2;color:#dc2626;">● Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="edit_program_detail.php?id=<?php echo $program['id']; ?>" style="width:34px;height:34px;border-radius:8px;background:#eff6ff;color:<?php echo $accent;?>;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all .2s;" title="Edit" onmouseover="this.style.background='<?php echo $accent;?>';this.style.color='#fff'" onmouseout="this.style.background='#eff6ff';this.style.color='<?php echo $accent;?>'"><i class="fas fa-edit" style="font-size:.8rem;"></i></a>
                                <button onclick="confirmDelete(<?php echo $program['id']; ?>, '<?php echo addslashes($program['title']); ?>')" style="width:34px;height:34px;border-radius:8px;background:#fef2f2;color:#ef4444;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;transition:all .2s;" title="Delete" onmouseover="this.style.background='#ef4444';this.style.color='#fff'" onmouseout="this.style.background='#fef2f2';this.style.color='#ef4444'"><i class="fas fa-trash" style="font-size:.8rem;"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>

<style>
input:focus{outline:none;border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a;}
@media(max-width:768px){div[style*="grid-template-columns"]{grid-template-columns:1fr 1fr !important;}}
</style>

<script>
function confirmDelete(id, title) {
    if (confirm('Delete "' + title + '"? This cannot be undone.')) {
        window.location.href = 'manage_programs.php?delete_id=' + id;
    }
}
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let v = this.value.toLowerCase();
    document.querySelectorAll('#programsTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(v) ? '' : 'none';
    });
});
</script>

<?php include 'footer.php'; ?>
