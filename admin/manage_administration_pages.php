<?php
require_once('../includes/db_connect.php');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Resource & Admin Pages";
$current_page = 'manage_administration_pages.php';
$accent = '#0891b2';

$pages = $pdo->query("
    SELECT p.*, 
    COUNT(DISTINCT c.id) as content_count,
    COUNT(DISTINCT cf.id) as field_count
    FROM administration_pages p
    LEFT JOIN administration_content c ON p.id = c.page_id
    LEFT JOIN administration_content_fields cf ON c.id = cf.content_id
    GROUP BY p.id
    ORDER BY p.id ASC
")->fetchAll();

$total_sections = array_sum(array_column($pages, 'content_count'));
$total_fields = array_sum(array_column($pages, 'field_count'));

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-university"></i> Resource & Admin Pages</h1>
        <p class="page-description">Manage content sections and editable fields for university pages.</p>
    </div></div>

    <!-- Header Card -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-university"></i></div>
        <div style="position:relative;z-index:1;">
            <span style="background:rgba(255,255,255,0.2);padding:5px 15px;border-radius:20px;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Content Management</span>
            <h2 style="margin:10px 0 5px;font-size:2rem;font-weight:900;">Page Management Directory</h2>
            <p style="margin:0;opacity:0.8;font-weight:500;">Edit and manage content on university resource pages through a simple interface.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:35px;">
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#ecfeff;color:<?php echo $accent;?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-file-alt"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:<?php echo $accent;?>;line-height:1;"><?php echo count($pages); ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Active Pages</div>
        </div>
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#ecfdf5;color:#10b981;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-layer-group"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:#10b981;line-height:1;"><?php echo $total_sections; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Sections</div>
        </div>
        <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:15px;top:15px;width:50px;height:50px;background:#fff7ed;color:#f59e0b;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-edit"></i></div>
            <div style="font-size:2.5rem;font-weight:900;color:#f59e0b;line-height:1;"><?php echo $total_fields; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;">Editable Fields</div>
        </div>
    </div>

    <!-- Pages Table -->
    <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;overflow:hidden;">
        <div style="padding:25px 30px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:45px;height:45px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;"><i class="fas fa-list-ul"></i></div>
                <div><h3 style="margin:0;font-size:1.3rem;font-weight:800;color:#1e293b;">All Managed Pages</h3>
                <p style="margin:0;color:#64748b;font-size:.85rem;">Click "Edit Content" to manage each page</p></div>
            </div>
            <input type="text" id="pageSearch" placeholder="🔍 Search pages..." style="padding:10px 20px;border:2px solid #e2e8f0;border-radius:12px;min-width:250px;font-size:.9rem;">
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;" id="pagesTable">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;width:60px;">ID</th>
                        <th style="padding:14px 20px;text-align:left;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Page Details</th>
                        <th style="padding:14px 20px;text-align:center;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Structure</th>
                        <th style="padding:14px 20px;text-align:center;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Fields</th>
                        <th style="padding:14px 20px;text-align:center;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Status</th>
                        <th style="padding:14px 20px;text-align:center;font-weight:800;color:#475569;font-size:.8rem;text-transform:uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background .2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                        <td style="padding:14px 20px;">
                            <span style="font-weight:800;color:#94a3b8;font-size:.9rem;"><?php echo str_pad($page['id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </td>
                        <td style="padding:14px 20px;">
                            <div>
                                <div style="font-weight:700;color:#1e293b;font-size:1.05rem;"><?php echo htmlspecialchars($page['page_name']); ?></div>
                                <span style="font-family:monospace;font-size:.8rem;color:#94a3b8;background:#f8fafc;padding:2px 8px;border-radius:6px;"><?php echo htmlspecialchars($page['page_slug']); ?>.php</span>
                            </div>
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <span style="padding:5px 12px;border-radius:8px;font-weight:800;font-size:.8rem;background:#eff6ff;color:#2563eb;display:inline-flex;align-items:center;gap:5px;">
                                <i class="fas fa-shapes" style="font-size:.7rem;"></i> <?php echo $page['content_count']; ?> Sections
                            </span>
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <span style="padding:5px 12px;border-radius:8px;font-weight:800;font-size:.8rem;background:#fff7ed;color:#ea580c;display:inline-flex;align-items:center;gap:5px;">
                                <i class="fas fa-database" style="font-size:.7rem;"></i> <?php echo $page['field_count']; ?> Fields
                            </span>
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <?php if ($page['is_active']): ?>
                                <span style="padding:5px 14px;border-radius:20px;font-weight:800;font-size:.75rem;background:#ecfdf5;color:#059669;">● Enabled</span>
                            <?php else: ?>
                                <span style="padding:5px 14px;border-radius:20px;font-weight:800;font-size:.75rem;background:#f1f5f9;color:#6b7280;">○ Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <div style="display:flex;gap:8px;justify-content:center;">
                                <a href="../<?php echo $page['page_slug']; ?>.php" target="_blank" style="padding:8px 16px;border-radius:10px;background:#f8fafc;color:#475569;text-decoration:none;font-weight:700;font-size:.85rem;display:flex;align-items:center;gap:6px;border:1px solid #e2e8f0;transition:all .2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                                    <i class="fas fa-external-link-alt" style="font-size:.75rem;"></i> View
                                </a>
                                <a href="edit_administration_page.php?id=<?php echo $page['id']; ?>" style="padding:8px 16px;border-radius:10px;background:linear-gradient(135deg,<?php echo $accent;?>,<?php echo $accent;?>dd);color:#fff;text-decoration:none;font-weight:700;font-size:.85rem;display:flex;align-items:center;gap:6px;box-shadow:0 4px 12px <?php echo $accent;?>33;transition:all .2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <i class="fas fa-pen-nib" style="font-size:.75rem;"></i> Edit Content
                                </a>
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
@media(max-width:768px){div[style*="grid-template-columns"]{grid-template-columns:1fr !important;}}
</style>

<script>
document.getElementById('pageSearch').addEventListener('keyup', function() {
    let v = this.value.toLowerCase();
    document.querySelectorAll('#pagesTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(v) ? '' : 'none';
    });
});
</script>

<?php include 'footer.php'; ?>
