<?php
require_once('../includes/db_connect.php');
require_once __DIR__ . '/../includes/admin_auth.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Staff & Faculty Directory";
$current_page = 'manage_directory.php';
$accent = '#7c3aed';

$STAFF_CATEGORIES = [
    'senior_member' => 'Non-Teaching Senior Members',
    'senior_staff'  => 'Senior Staff',
    'junior_staff'  => 'Junior Staff',
];

/* ---------------------------------------------------------------------------
   Row actions. Both are state changing, so both go through POST + CSRF.
   --------------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    vvu_require_csrf();
    $back = 'manage_directory.php?' . http_build_query(array_filter([
        'type' => $_POST['r_type'] ?? '', 'category' => $_POST['r_category'] ?? '',
        'q' => $_POST['r_q'] ?? '', 'page' => $_POST['r_page'] ?? '',
    ], 'strlen'));

    if (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM directory WHERE id = ?")->execute([(int) $_POST['delete_id']]);
        header("Location: $back" . (strpos($back, '?') === false ? '?' : '&') . "msg=deleted");
        exit();
    }
    if (isset($_POST['toggle_id'])) {
        $pdo->prepare("UPDATE directory SET is_active = 1 - is_active WHERE id = ?")->execute([(int) $_POST['toggle_id']]);
        header("Location: $back" . (strpos($back, '?') === false ? '?' : '&') . "msg=updated");
        exit();
    }
}

/* ---------------------------------------------------------------------------
   Filters + pagination — the roll runs to a few hundred people.
   --------------------------------------------------------------------------- */
$type     = in_array($_GET['type'] ?? '', ['faculty', 'staff'], true) ? $_GET['type'] : '';
$category = isset($STAFF_CATEGORIES[$_GET['category'] ?? '']) ? $_GET['category'] : '';
$q        = trim((string) ($_GET['q'] ?? ''));
$dept     = trim((string) ($_GET['dept'] ?? ''));
$page     = max(1, (int) ($_GET['page'] ?? 1));
$per_page = 40;

$where = [];
$params = [];
if ($type !== '')     { $where[] = 'type = ?';           $params[] = $type; }
if ($category !== '') { $where[] = 'staff_category = ?'; $params[] = $category; }
if ($dept !== '')     { $where[] = 'department = ?';     $params[] = $dept; }
if ($q !== '') {
    $where[] = '(name LIKE ? OR job_title LIKE ? OR department LIKE ? OR faculty_group LIKE ? OR email LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%");
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (function () use ($pdo, $where_sql, $params) {
    $s = $pdo->prepare("SELECT COUNT(*) FROM directory $where_sql");
    $s->execute($params);
    return $s->fetchColumn();
})();
$pages  = max(1, (int) ceil($total / $per_page));
$page   = min($page, $pages);
$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("SELECT * FROM directory $where_sql ORDER BY type ASC, sort_order ASC, name ASC LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$directory = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Counts for the tab bar — always over the whole table, not the filtered page. */
$counts = ['all' => 0, 'faculty' => 0, 'staff' => 0, 'hidden' => 0];
foreach ($STAFF_CATEGORIES as $k => $_) { $counts[$k] = 0; }
foreach ($pdo->query("SELECT type, IFNULL(staff_category,'') AS cat, is_active, COUNT(*) AS n FROM directory GROUP BY type, cat, is_active") as $r) {
    $n = (int) $r['n'];
    $counts['all'] += $n;
    $counts[$r['type']] = ($counts[$r['type']] ?? 0) + $n;
    if ($r['cat'] !== '' && isset($counts[$r['cat']])) { $counts[$r['cat']] += $n; }
    if (!(int) $r['is_active']) { $counts['hidden'] += $n; }
}

$dept_options = $pdo->query("SELECT DISTINCT department FROM directory WHERE department <> '' ORDER BY department")->fetchAll(PDO::FETCH_COLUMN);

$qs = static function (array $overrides = []) use ($type, $category, $q, $dept, $page) {
    $base = ['type' => $type, 'category' => $category, 'q' => $q, 'dept' => $dept, 'page' => $page];
    $merged = array_filter(array_merge($base, $overrides), static fn ($v) => $v !== '' && $v !== null && $v !== 0);
    return $merged ? '?' . http_build_query($merged) : '';
};
$e = static fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-address-book"></i> Staff &amp; Faculty Directory</h1>
        <p class="page-description">Every profile shown on the Faculty and Staff Encyclopedia pages.</p>
    </div></div>

    <?php if (isset($_GET['msg'])): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:#ecfdf5;border:1px solid #10b981;color:#065f46;font-weight:600;">
        <i class="fas fa-check-circle"></i> Entry <?php echo $e($_GET['msg']); ?> successfully.
    </div>
    <?php endif; ?>

    <!-- Header Card -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:28px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-address-book"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <span style="background:rgba(255,255,255,0.2);padding:5px 15px;border-radius:20px;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Directory</span>
                <h2 style="margin:10px 0 5px;font-size:2rem;font-weight:900;">People &amp; Profiles</h2>
                <p style="margin:0;opacity:.85;font-weight:600;font-size:.9rem;"><?php echo $counts['all']; ?> profiles on file<?php echo $counts['hidden'] ? ' · ' . $counts['hidden'] . ' hidden from the site' : ''; ?></p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="manage_encyclopedia_content.php" style="padding:12px 22px;background:rgba(255,255,255,0.18);color:#fff;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:8px;"><i class="fas fa-heading"></i> Page Text</a>
                <a href="edit_directory.php" style="padding:12px 25px;background:#fff;color:#1e293b;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;"><i class="fas fa-plus"></i> Add New Profile</a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;margin-bottom:26px;" class="dir-stat-grid">
        <?php
        $tiles = [
            ['Total Profiles', $counts['all'], 'fa-users', $accent, '#f3e8ff'],
            ['Faculty', $counts['faculty'], 'fa-chalkboard-teacher', '#2563eb', '#eff6ff'],
            ['Senior Members', $counts['senior_member'], 'fa-award', '#d97706', '#fffbeb'],
            ['Senior Staff', $counts['senior_staff'], 'fa-id-badge', '#10b981', '#ecfdf5'],
            ['Junior Staff', $counts['junior_staff'], 'fa-helmet-safety', '#0ea5e9', '#f0f9ff'],
        ];
        foreach ($tiles as [$label, $num, $icon, $col, $bg]): ?>
        <div style="background:#fff;border-radius:18px;padding:22px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);position:relative;overflow:hidden;">
            <div style="position:absolute;right:14px;top:14px;width:44px;height:44px;background:<?php echo $bg;?>;color:<?php echo $col;?>;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;"><i class="fas <?php echo $icon;?>"></i></div>
            <div style="font-size:2.2rem;font-weight:900;color:<?php echo $col;?>;line-height:1;"><?php echo $num; ?></div>
            <div style="font-weight:700;color:#64748b;margin-top:5px;font-size:.85rem;"><?php echo $label; ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filter bar -->
    <div style="background:#fff;border-radius:18px;padding:18px 20px;border:1px solid #f1f5f9;box-shadow:0 4px 15px rgba(0,0,0,0.03);margin-bottom:22px;">
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <?php
            $tabs = [
                ['', '', 'All', $counts['all']],
                ['faculty', '', 'Faculty', $counts['faculty']],
                ['staff', 'senior_member', 'Senior Members', $counts['senior_member']],
                ['staff', 'senior_staff', 'Senior Staff', $counts['senior_staff']],
                ['staff', 'junior_staff', 'Junior Staff', $counts['junior_staff']],
            ];
            foreach ($tabs as [$t, $c, $label, $n]):
                $active = ($type === $t && $category === $c);
                $href = 'manage_directory.php' . ($qs(['type' => $t, 'category' => $c, 'page' => 1]) ?: '');
            ?>
            <a href="<?php echo $e($href); ?>" style="padding:9px 16px;border-radius:10px;text-decoration:none;font-weight:800;font-size:.85rem;display:inline-flex;align-items:center;gap:8px;<?php echo $active ? "background:$accent;color:#fff;" : 'background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;'; ?>">
                <?php echo $label; ?>
                <span style="font-size:.72rem;padding:2px 7px;border-radius:20px;background:<?php echo $active ? 'rgba(255,255,255,.25)' : '#e2e8f0'; ?>;"><?php echo $n; ?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <form method="GET" action="manage_directory.php" style="display:grid;grid-template-columns:1fr 240px auto auto;gap:10px;" class="dir-filter-form">
            <input type="hidden" name="type" value="<?php echo $e($type); ?>">
            <input type="hidden" name="category" value="<?php echo $e($category); ?>">
            <input type="text" name="q" value="<?php echo $e($q); ?>" placeholder="🔍 Search name, rank, department or email…" style="padding:12px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:.9rem;">
            <select name="dept" style="padding:12px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:.9rem;background:#fff;">
                <option value="">All departments</option>
                <?php foreach ($dept_options as $d): ?>
                    <option value="<?php echo $e($d); ?>" <?php echo $dept === $d ? 'selected' : ''; ?>><?php echo $e($d); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="padding:12px 26px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:12px;font-weight:800;cursor:pointer;"><i class="fas fa-filter"></i> Filter</button>
            <?php if ($q !== '' || $dept !== '' || $type !== '' || $category !== ''): ?>
                <a href="manage_directory.php" style="padding:12px 20px;background:#f1f5f9;color:#475569;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:6px;"><i class="fas fa-rotate-left"></i> Reset</a>
            <?php else: ?><span></span><?php endif; ?>
        </form>
    </div>

    <!-- Directory Table -->
    <div style="background:#fff;border-radius:20px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;overflow:hidden;">
        <div style="padding:20px 26px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:44px;height:44px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;"><i class="fas fa-id-card"></i></div>
                <div>
                    <h3 style="margin:0;font-size:1.2rem;font-weight:800;color:#1e293b;">
                        <?php echo $total; ?> profile<?php echo $total === 1 ? '' : 's'; ?>
                    </h3>
                    <p style="margin:0;color:#64748b;font-size:.82rem;">Showing <?php echo $total ? $offset + 1 : 0; ?>–<?php echo min($offset + $per_page, $total); ?> · page <?php echo $page; ?> of <?php echo $pages; ?></p>
                </div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:900px;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <?php foreach (['Name', 'Category', 'Rank / Position', 'Department', 'Order', 'Status', 'Actions'] as $i => $h): ?>
                        <th style="padding:13px 18px;text-align:<?php echo $i >= 4 ? 'center' : 'left'; ?>;font-weight:800;color:#475569;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;"><?php echo $h; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($directory as $item):
                        $img = $item['image_url'] ?: '';
                        if ($img !== '' && !str_starts_with($img, 'http') && !str_starts_with($img, '../')) { $img = '../' . $img; }
                        $ini = '';
                        foreach (preg_split('/\s+/', trim($item['name'])) as $w) {
                            if ($w !== '' && preg_match('/[A-Za-z]/', $w)) { $ini .= strtoupper($w[0]); }
                        }
                        $ini = substr($ini, 0, 2) ?: '?';
                        $catLabel = $item['type'] === 'faculty' ? 'Faculty' : ($STAFF_CATEGORIES[$item['staff_category'] ?? ''] ?? 'Staff');
                        $catStyle = $item['type'] === 'faculty' ? 'background:#eff6ff;color:#2563eb;' : match ($item['staff_category'] ?? '') {
                            'senior_member' => 'background:#fffbeb;color:#d97706;',
                            'senior_staff'  => 'background:#ecfdf5;color:#059669;',
                            'junior_staff'  => 'background:#f0f9ff;color:#0284c7;',
                            default         => 'background:#f3e8ff;color:#7c3aed;',
                        };
                    ?>
                    <tr style="border-bottom:1px solid #f1f5f9;<?php echo (int) $item['is_active'] ? '' : 'opacity:.55;'; ?>">
                        <td style="padding:12px 18px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <?php if ($img !== ''): ?>
                                    <img src="<?php echo $e($img); ?>" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #f1f5f9;flex:none;">
                                <?php else: ?>
                                    <span style="width:40px;height:40px;border-radius:50%;flex:none;display:grid;place-items:center;background:#ede9fe;color:#6d28d9;font-weight:900;font-size:.8rem;"><?php echo $e($ini); ?></span>
                                <?php endif; ?>
                                <div style="min-width:0;">
                                    <div style="font-weight:700;color:#1e293b;"><?php echo $e($item['name']); ?></div>
                                    <?php if (!empty($item['email'])): ?>
                                        <div style="font-size:.76rem;color:#94a3b8;"><?php echo $e($item['email']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 18px;"><span style="padding:5px 11px;border-radius:20px;font-weight:800;font-size:.72rem;white-space:nowrap;<?php echo $catStyle; ?>"><?php echo $e($catLabel); ?></span></td>
                        <td style="padding:12px 18px;color:#475569;font-weight:600;font-size:.86rem;"><?php echo $e($item['job_title']); ?></td>
                        <td style="padding:12px 18px;color:#64748b;font-size:.86rem;"><?php echo $e($item['department']); ?></td>
                        <td style="padding:12px 18px;text-align:center;color:#94a3b8;font-size:.85rem;font-weight:700;"><?php echo (int) $item['sort_order']; ?></td>
                        <td style="padding:12px 18px;text-align:center;">
                            <form method="POST" style="display:inline;">
                                <?php echo vvu_csrf_field(); ?>
                                <input type="hidden" name="toggle_id" value="<?php echo (int) $item['id']; ?>">
                                <input type="hidden" name="r_type" value="<?php echo $e($type); ?>">
                                <input type="hidden" name="r_category" value="<?php echo $e($category); ?>">
                                <input type="hidden" name="r_q" value="<?php echo $e($q); ?>">
                                <input type="hidden" name="r_page" value="<?php echo $page; ?>">
                                <button type="submit" title="<?php echo (int) $item['is_active'] ? 'Hide from website' : 'Show on website'; ?>"
                                        style="border:none;cursor:pointer;padding:5px 12px;border-radius:20px;font-weight:800;font-size:.72rem;white-space:nowrap;<?php echo (int) $item['is_active'] ? 'background:#ecfdf5;color:#059669;' : 'background:#f1f5f9;color:#94a3b8;'; ?>">
                                    <i class="fas fa-<?php echo (int) $item['is_active'] ? 'eye' : 'eye-slash'; ?>"></i>
                                    <?php echo (int) $item['is_active'] ? 'Live' : 'Hidden'; ?>
                                </button>
                            </form>
                        </td>
                        <td style="padding:12px 18px;text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="edit_directory.php?id=<?php echo (int) $item['id']; ?>" title="Edit" style="width:34px;height:34px;border-radius:9px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;text-decoration:none;"><i class="fas fa-edit" style="font-size:.8rem;"></i></a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete <?php echo $e(addslashes($item['name'])); ?> from the directory?');">
                                    <?php echo vvu_csrf_field(); ?>
                                    <input type="hidden" name="delete_id" value="<?php echo (int) $item['id']; ?>">
                                    <input type="hidden" name="r_type" value="<?php echo $e($type); ?>">
                                    <input type="hidden" name="r_category" value="<?php echo $e($category); ?>">
                                    <input type="hidden" name="r_q" value="<?php echo $e($q); ?>">
                                    <input type="hidden" name="r_page" value="<?php echo $page; ?>">
                                    <button type="submit" title="Delete" style="width:34px;height:34px;border:none;border-radius:9px;background:#fef2f2;color:#ef4444;display:flex;align-items:center;justify-content:center;cursor:pointer;"><i class="fas fa-trash" style="font-size:.8rem;"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($directory)): ?>
                    <tr><td colspan="7" style="padding:50px;text-align:center;color:#94a3b8;font-style:italic;">No profiles match these filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pages > 1): ?>
        <div style="padding:18px 26px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:6px;flex-wrap:wrap;">
            <?php
            $window = 2;
            $links = array_unique(array_filter(array_merge(
                [1, $pages],
                range(max(1, $page - $window), min($pages, $page + $window))
            )));
            sort($links);
            $prev = 0;
            foreach ($links as $p):
                if ($prev && $p > $prev + 1) { echo '<span style="padding:8px 6px;color:#cbd5e1;">…</span>'; }
                $prev = $p;
            ?>
                <a href="manage_directory.php<?php echo $e($qs(['page' => $p])); ?>"
                   style="min-width:38px;text-align:center;padding:8px 12px;border-radius:10px;text-decoration:none;font-weight:800;font-size:.85rem;<?php echo $p === $page ? "background:$accent;color:#fff;" : 'background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;'; ?>"><?php echo $p; ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</main>

<style>
input:focus,select:focus{outline:none;border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a;}
tbody tr:hover{background:#f8fafc;}
@media(max-width:900px){
  .dir-filter-form{grid-template-columns:1fr !important;}
  .dir-stat-grid{grid-template-columns:repeat(auto-fit,minmax(150px,1fr)) !important;}
}
</style>

<?php include 'footer.php'; ?>
