<?php
require_once('../includes/db_connect.php');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Staff & Faculty Directory";
$current_page = 'manage_directory.php';
$accent = '#7c3aed';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM directory WHERE id = ?")->execute([$_GET['delete']]);
    header("Location: manage_directory.php?msg=deleted"); exit();
}

$directory = $pdo->query("SELECT * FROM directory ORDER BY name ASC")->fetchAll();
$faculty_count = 0; $staff_count = 0;
foreach ($directory as $d) { if($d['type']==='faculty') $faculty_count++; else $staff_count++; }

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-address-book"></i> Staff & Faculty Directory</h1>
        <p class="page-description">Manage all profiles for the university encyclopedia pages.</p>
    </div></div>

    <?php if (isset($_GET['msg'])): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:#ecfdf5;border:1px solid #10b981;color:#065f46;">
        <i class="fas fa-check-circle"></i> <strong>Done!</strong> Entry <?php echo htmlspecialchars($_GET['msg']); ?> successfully.
    </div>
    <?php endif; ?>

    <!-- Header Card -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-address-book"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <span style="background:rgba(255,255,255,0.2);padding:5px 15px;border-radius:20px;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Directory</span>
                <h2 style="margin:10px 0 5px;font-size:2rem;font-weight:900;">People & Profiles</h2>
            </div>
            <a href="edit_directory.php" style="padding:12px 25px;background:#fff;color:#1e293b;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-plus"></i> Add New Profile
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:35px;">
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#f3e8ff;color:<?php echo $accent;?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-users"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:<?php echo $accent;?>;line-height:1;"><?php echo count($directory); ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Total Profiles</div>
        </div>
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#eff6ff;color:#2563eb;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-chalkboard-teacher"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:#2563eb;line-height:1;"><?php echo $faculty_count; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Faculty</div>
        </div>
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#ecfdf5;color:#10b981;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-user-tie"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:#10b981;line-height:1;"><?php echo $staff_count; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Admin Staff</div>
        </div>
    </div>

    <!-- Directory Table -->
    <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;overflow:hidden;">
        <div style="padding:25px 30px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:45px;height:45px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;"><i class="fas fa-id-card"></i></div>
                <div><h3 style="margin:0;font-size:1.3rem;font-weight:800;color:#1e293b;">All Profiles</h3>
                <p style="margin:0;color:#64748b;font-size:.85rem;">Faculty members and administrative staff</p></div>
            </div>
            <input type="text" id="dirSearch" placeholder="🔍 Search profiles..." style="padding:10px 20px;border:2px solid #e2e8f0;border-radius:12px;min-width:250px;font-size:.9rem;">
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;" id="dirTable">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;">Name</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Type</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Position</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Department</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Email</th>
                        <th style="padding:14px 20px;text-align:center;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($directory as $item):
                        $list_img = $item['image_url'] ?: '../images/default-profile.png';
                        if ($list_img && !str_starts_with($list_img, 'http') && !str_starts_with($list_img, '../')) $list_img = '../' . $list_img;
                    ?>
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background .2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                        <td style="padding:14px 20px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <img src="<?php echo htmlspecialchars($list_img); ?>" style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:3px solid #f1f5f9;">
                                <span style="font-weight:700;color:#1e293b;"><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td style="padding:14px 20px;">
                            <?php if ($item['type'] === 'faculty'): ?>
                                <span style="padding:5px 12px;border-radius:20px;font-weight:800;font-size:.75rem;background:#eff6ff;color:#2563eb;">Faculty</span>
                            <?php else: ?>
                                <span style="padding:5px 12px;border-radius:20px;font-weight:800;font-size:.75rem;background:#f3e8ff;color:#7c3aed;">Staff</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 20px;color:#475569;font-weight:600;"><?php echo htmlspecialchars($item['job_title']); ?></td>
                        <td style="padding:14px 20px;color:#64748b;"><?php echo htmlspecialchars($item['department']); ?></td>
                        <td style="padding:14px 20px;color:#64748b;font-size:.9rem;"><?php echo htmlspecialchars($item['email']); ?></td>
                        <td style="padding:14px 20px;text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="edit_directory.php?id=<?php echo $item['id']; ?>" style="width:34px;height:34px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all .2s;" title="Edit" onmouseover="this.style.background='#2563eb';this.style.color='#fff'" onmouseout="this.style.background='#eff6ff';this.style.color='#2563eb'"><i class="fas fa-edit" style="font-size:.8rem;"></i></a>
                                <a href="manage_directory.php?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete this profile?')" style="width:34px;height:34px;border-radius:8px;background:#fef2f2;color:#ef4444;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all .2s;" title="Delete" onmouseover="this.style.background='#ef4444';this.style.color='#fff'" onmouseout="this.style.background='#fef2f2';this.style.color='#ef4444'"><i class="fas fa-trash" style="font-size:.8rem;"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($directory)): ?>
                    <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">No directory entries found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>

<style>
input:focus{outline:none;border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a;}
@media(max-width:768px){div[style*="grid-template-columns"]{grid-template-columns:1fr !important;}}
</style>

<script>
document.getElementById('dirSearch').addEventListener('keyup', function() {
    let v = this.value.toLowerCase();
    document.querySelectorAll('#dirTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(v) ? '' : 'none';
    });
});
</script>

<?php include 'footer.php'; ?>
