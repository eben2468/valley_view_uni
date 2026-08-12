<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
require_once __DIR__ . '/../includes/admin_auth.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Edit Directory Profile";
$current_page = 'manage_directory.php';
$accent = '#7c3aed';

/* The staff tiers rendered as separate sections on staff_encyclopedia.php. */
$STAFF_CATEGORIES = [
    'senior_member' => 'Non-Teaching Senior Member',
    'senior_staff'  => 'Senior Staff',
    'junior_staff'  => 'Junior Staff',
];

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$item = [
    'type' => 'faculty', 'name' => '', 'title' => '', 'job_title' => '', 'department' => '',
    'faculty_group' => '', 'staff_category' => '', 'employment_status' => 'Full-time',
    'email' => '', 'phone' => '', 'office_location' => '', 'image_url' => '', 'bio' => '',
    'education' => '', 'research_interests' => '', 'publications' => '',
    'sort_order' => 0, 'is_active' => 1, 'is_featured' => 0,
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM directory WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) { header("Location: manage_directory.php"); exit(); }
    $item = array_merge($item, $row);
}

/* Suggestion lists so editors reuse the existing department / faculty names
   instead of inventing spellings that split the front-end filters. */
$dept_options  = $pdo->query("SELECT DISTINCT department FROM directory WHERE department <> '' ORDER BY department")->fetchAll(PDO::FETCH_COLUMN);
$group_options = $pdo->query("SELECT DISTINCT faculty_group FROM directory WHERE faculty_group <> '' ORDER BY faculty_group")->fetchAll(PDO::FETCH_COLUMN);
$job_options   = $pdo->query("SELECT DISTINCT job_title FROM directory WHERE job_title <> '' ORDER BY job_title")->fetchAll(PDO::FETCH_COLUMN);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    vvu_require_csrf();

    $type = ($_POST['type'] ?? 'faculty') === 'staff' ? 'staff' : 'faculty';
    $staff_category = $_POST['staff_category'] ?? '';
    if ($type !== 'staff' || !isset($STAFF_CATEGORIES[$staff_category])) {
        $staff_category = $type === 'staff' ? 'senior_staff' : null;
    }

    $fields = [
        'name'              => trim($_POST['name'] ?? ''),
        'title'             => trim($_POST['title'] ?? ''),
        'job_title'         => trim($_POST['job_title'] ?? ''),
        'department'        => trim($_POST['department'] ?? ''),
        'faculty_group'     => trim($_POST['faculty_group'] ?? ''),
        'employment_status' => trim($_POST['employment_status'] ?? ''),
        'email'             => trim($_POST['email'] ?? ''),
        'phone'             => trim($_POST['phone'] ?? ''),
        'office_location'   => trim($_POST['office_location'] ?? ''),
        'bio'               => $_POST['bio'] ?? '',
        'education'         => $_POST['education'] ?? '',
        'research_interests'=> $_POST['research_interests'] ?? '',
        'publications'      => $_POST['publications'] ?? '',
    ];
    $sort_order  = (int) ($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Keep the form filled in if the save fails.
    $item = array_merge($item, $fields, [
        'type' => $type, 'staff_category' => (string) $staff_category,
        'sort_order' => $sort_order, 'is_active' => $is_active, 'is_featured' => $is_featured,
    ]);

    if ($fields['name'] === '') {
        $error = 'A full name is required.';
    } else {
        $image_url = $item['image_url'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = handleAdminFileUpload($_FILES['image'], 'directory', 'dir_');
            if ($uploaded) {
                $image_url = $uploaded;
            } else {
                $error = vvu_take_upload_error() ?: 'The photo could not be uploaded.';
            }
        }

        if ($error === '') {
            // New entries land at the end of their group unless told otherwise.
            if (!$id && $sort_order === 0) {
                $maxStmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM directory WHERE type = ?");
                $maxStmt->execute([$type]);
                $sort_order = ((int) $maxStmt->fetchColumn()) + 10;
            }

            try {
                if ($id) {
                    $pdo->prepare(
                        "UPDATE directory SET type=?, name=?, title=?, job_title=?, department=?, faculty_group=?,
                                staff_category=?, employment_status=?, email=?, phone=?, office_location=?, image_url=?,
                                bio=?, education=?, research_interests=?, publications=?, sort_order=?, is_active=?, is_featured=?
                         WHERE id=?"
                    )->execute([
                        $type, $fields['name'], $fields['title'], $fields['job_title'], $fields['department'],
                        $fields['faculty_group'], $staff_category, $fields['employment_status'], $fields['email'],
                        $fields['phone'], $fields['office_location'], $image_url, $fields['bio'], $fields['education'],
                        $fields['research_interests'], $fields['publications'], $sort_order, $is_active, $is_featured, $id,
                    ]);
                    $msg = 'updated';
                } else {
                    $pdo->prepare(
                        "INSERT INTO directory (type, name, title, job_title, department, faculty_group, staff_category,
                                employment_status, email, phone, office_location, image_url, bio, education,
                                research_interests, publications, sort_order, is_active, is_featured)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    )->execute([
                        $type, $fields['name'], $fields['title'], $fields['job_title'], $fields['department'],
                        $fields['faculty_group'], $staff_category, $fields['employment_status'], $fields['email'],
                        $fields['phone'], $fields['office_location'], $image_url, $fields['bio'], $fields['education'],
                        $fields['research_interests'], $fields['publications'], $sort_order, $is_active, $is_featured,
                    ]);
                    $msg = 'created';
                }
                header("Location: manage_directory.php?msg=$msg&type=" . urlencode($type));
                exit();
            } catch (PDOException $e) {
                $error = ((int) $e->getCode() === 23000)
                    ? 'Another profile of this type already uses that exact name.'
                    : 'The profile could not be saved. Please try again.';
                error_log('VVU directory save failed: ' . $e->getMessage());
            }
        }
    }
}

include 'header.php';
include 'sidebar.php';

$prev_img = $item['image_url'] ?: '';
if ($prev_img !== '' && !str_starts_with($prev_img, 'http') && !str_starts_with($prev_img, '../')) {
    $prev_img = '../' . $prev_img;
}
$initials = '';
foreach (preg_split('/\s+/', trim((string) $item['name'])) as $w) {
    if ($w !== '' && preg_match('/[A-Za-z]/', $w)) { $initials .= strtoupper($w[0]); }
}
$initials = substr($initials, 0, 2) ?: '?';
$e = static fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-user-edit"></i> <?php echo $id ? 'Edit' : 'Add New'; ?> Profile</h1>
        <p class="page-description">Everything on this page feeds the Faculty &amp; Staff Encyclopedia pages.</p>
    </div></div>

    <?php if ($error): ?>
    <div style="margin-bottom:22px;border-radius:12px;padding:15px 18px;display:flex;align-items:center;gap:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-weight:600;">
        <i class="fas fa-triangle-exclamation"></i> <?php echo $e($error); ?>
    </div>
    <?php endif; ?>

    <!-- Header Card -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-user-edit"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <a href="manage_directory.php" style="color:rgba(255,255,255,0.85);text-decoration:none;font-size:.85rem;font-weight:700;display:flex;align-items:center;gap:6px;margin-bottom:8px;"><i class="fas fa-chevron-left"></i> Back to Directory</a>
                <h2 style="margin:0;font-size:2rem;font-weight:900;"><?php echo $id ? $e($item['name']) : 'New Profile'; ?></h2>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <?php if ($id): ?>
                <a href="../profile.php?id=<?php echo $id; ?>" target="_blank" style="background:rgba(255,255,255,0.2);padding:10px 20px;border-radius:12px;font-size:.85rem;font-weight:800;color:#fff;text-decoration:none;"><i class="fas fa-up-right-from-square"></i> View on site</a>
                <span style="background:rgba(255,255,255,0.2);padding:10px 20px;border-radius:12px;font-size:.85rem;font-weight:800;">ID: <?php echo $id; ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" id="dirForm">
        <?php echo vvu_csrf_field(); ?>

        <div style="display:grid;grid-template-columns:300px 1fr;gap:30px;margin-bottom:30px;" class="dir-split">
            <!-- Photo + placement -->
            <div style="display:flex;flex-direction:column;gap:20px;">
                <div style="background:#fff;border-radius:20px;padding:30px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);text-align:center;">
                    <div style="margin-bottom:20px;">
                        <?php if ($prev_img !== ''): ?>
                            <img src="<?php echo $e($prev_img); ?>" id="preview" style="width:180px;height:180px;border-radius:50%;object-fit:cover;border:6px solid #f1f5f9;box-shadow:0 10px 30px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <img src="" id="preview" style="display:none;width:180px;height:180px;border-radius:50%;object-fit:cover;border:6px solid #f1f5f9;">
                            <div id="previewFallback" style="width:180px;height:180px;margin:0 auto;border-radius:50%;display:grid;place-items:center;background:linear-gradient(140deg,#4c1d95,#7c3aed);color:#fff;font-size:3.4rem;font-weight:900;border:6px solid #f1f5f9;"><?php echo $e($initials); ?></div>
                        <?php endif; ?>
                    </div>
                    <label for="imageInput" style="display:inline-flex;align-items:center;gap:8px;padding:10px 25px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:12px;font-weight:700;cursor:pointer;">
                        <i class="fas fa-camera"></i> <?php echo $prev_img !== '' ? 'Change Photo' : 'Upload Photo'; ?>
                    </label>
                    <input type="file" name="image" id="imageInput" style="display:none;" onchange="previewImage(this)" accept="image/*">
                    <p style="color:#94a3b8;font-size:.8rem;margin-top:10px;">Square, at least 500&times;500px.<br>Without a photo the site shows a monogram.</p>
                </div>

                <!-- Publishing -->
                <div style="background:#fff;border-radius:20px;padding:25px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);">
                    <h3 style="margin:0 0 18px;font-size:1.05rem;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:8px;"><i class="fas fa-toggle-on" style="color:<?php echo $accent;?>;"></i> Publishing</h3>

                    <label style="display:flex;align-items:center;gap:10px;padding:12px;border-radius:12px;background:#f8fafc;cursor:pointer;margin-bottom:10px;">
                        <input type="checkbox" name="is_active" value="1" <?php echo $item['is_active'] ? 'checked' : ''; ?> style="width:18px;height:18px;accent-color:<?php echo $accent;?>;">
                        <span style="font-weight:700;color:#334155;font-size:.9rem;">Visible on the website</span>
                    </label>

                    <label style="display:flex;align-items:center;gap:10px;padding:12px;border-radius:12px;background:#f8fafc;cursor:pointer;margin-bottom:16px;">
                        <input type="checkbox" name="is_featured" value="1" <?php echo $item['is_featured'] ? 'checked' : ''; ?> style="width:18px;height:18px;accent-color:<?php echo $accent;?>;">
                        <span style="font-weight:700;color:#334155;font-size:.9rem;">Feature this profile</span>
                    </label>

                    <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Display order</label>
                    <input type="number" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>" step="10" min="0" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    <p style="color:#94a3b8;font-size:.78rem;margin-top:8px;">Lower numbers come first. Leave 0 on a new profile to add it at the end.</p>
                </div>
            </div>

            <!-- Basic info -->
            <div style="background:#fff;border-radius:20px;padding:30px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);">
                <h3 style="margin:0 0 25px;font-size:1.3rem;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:10px;"><i class="fas fa-id-badge" style="color:<?php echo $accent;?>;"></i> Basic Information</h3>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;" class="dir-fields">
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Profile Type</label>
                        <select name="type" id="typeSelect" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:.95rem;background:#fff;">
                            <option value="faculty" <?php echo $item['type'] === 'faculty' ? 'selected' : ''; ?>>Faculty / Lecturer</option>
                            <option value="staff" <?php echo $item['type'] === 'staff' ? 'selected' : ''; ?>>Administrative Staff</option>
                        </select>
                    </div>

                    <div id="categoryWrap">
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Staff Category <span style="color:#94a3b8;font-weight:600;">(section on the staff page)</span></label>
                        <select name="staff_category" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:.95rem;background:#fff;">
                            <?php foreach ($STAFF_CATEGORIES as $key => $label): ?>
                                <option value="<?php echo $e($key); ?>" <?php echo $item['staff_category'] === $key ? 'selected' : ''; ?>><?php echo $e($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Full Name</label>
                        <input type="text" name="name" value="<?php echo $e($item['name']); ?>" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Honorific (Dr., Prof., Pastor)</label>
                        <input type="text" name="title" value="<?php echo $e($item['title']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div style="grid-column:1 / -1;">
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Rank / Position</label>
                        <input type="text" name="job_title" list="jobList" value="<?php echo $e($item['job_title']); ?>" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                        <datalist id="jobList"><?php foreach ($job_options as $o): ?><option value="<?php echo $e($o); ?>"></option><?php endforeach; ?></datalist>
                        <p style="color:#94a3b8;font-size:.78rem;margin-top:6px;">Anything after a “/” — e.g. <em>Senior Lecturer/Dean SOB</em> — is shown as a leadership ribbon on the card.</p>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Department / Unit</label>
                        <input type="text" name="department" list="deptList" value="<?php echo $e($item['department']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                        <datalist id="deptList"><?php foreach ($dept_options as $o): ?><option value="<?php echo $e($o); ?>"></option><?php endforeach; ?></datalist>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Faculty / School Group</label>
                        <input type="text" name="faculty_group" list="groupList" value="<?php echo $e($item['faculty_group']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                        <datalist id="groupList"><?php foreach ($group_options as $o): ?><option value="<?php echo $e($o); ?>"></option><?php endforeach; ?></datalist>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Employment Status</label>
                        <input type="text" name="employment_status" value="<?php echo $e($item['employment_status']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Email Address</label>
                        <input type="email" name="email" value="<?php echo $e($item['email']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Phone</label>
                        <input type="text" name="phone" value="<?php echo $e($item['phone']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Office Location</label>
                        <input type="text" name="office_location" value="<?php echo $e($item['office_location']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bio & Academic Info -->
        <div style="background:#fff;border-radius:20px;padding:30px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);margin-bottom:30px;">
            <h3 style="margin:0 0 25px;font-size:1.3rem;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:10px;"><i class="fas fa-book-reader" style="color:#059669;"></i> Profile Details</h3>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Biography</label>
                <textarea name="bio" id="editor-bio" rows="5" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo $e($item['bio']); ?></textarea>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Education / Qualifications</label>
                <textarea name="education" id="editor-education" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo $e($item['education']); ?></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="dir-fields">
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Research Interests / Duties</label>
                    <textarea name="research_interests" id="editor-research" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo $e($item['research_interests']); ?></textarea>
                </div>
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Key Publications</label>
                    <textarea name="publications" id="editor-publications" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo $e($item['publications']); ?></textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:15px;justify-content:flex-end;flex-wrap:wrap;">
            <a href="manage_directory.php" style="padding:14px 30px;background:#f1f5f9;color:#475569;border-radius:14px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:8px;"><i class="fas fa-times"></i> Cancel</a>
            <button type="submit" style="padding:14px 40px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:14px;font-size:1.05rem;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:10px;box-shadow:0 10px 20px <?php echo $accent;?>33;">
                <i class="fas fa-save"></i> Save Profile
            </button>
        </div>
    </form>
</div>
</main>

<style>
input:focus,select:focus,textarea:focus{outline:none;border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a;}
.ck-editor__editable_inline{min-height:150px;color:#333;}
#editor-bio+.ck-editor .ck-editor__editable_inline{min-height:200px;}
.ck.ck-editor{width:100% !important;}
.ck.ck-editor__main>.ck-editor__editable{background:#fff;}
.ck-toolbar{border-radius:10px 10px 0 0 !important;background:#f8fafc !important;}
.ck-editor__editable{border-radius:0 0 10px 10px !important;}
@media(max-width:992px){.dir-split{grid-template-columns:1fr !important;}}
@media(max-width:768px){.dir-fields{grid-template-columns:1fr !important;}}
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = document.getElementById('preview');
            var fb  = document.getElementById('previewFallback');
            img.src = e.target.result;
            img.style.display = 'inline-block';
            if (fb) fb.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// The staff category only applies to staff profiles.
(function () {
    var type = document.getElementById('typeSelect');
    var wrap = document.getElementById('categoryWrap');
    function sync() { wrap.style.display = type.value === 'staff' ? '' : 'none'; }
    type.addEventListener('change', sync);
    sync();
})();

const editorConfig = { toolbar: ['heading','|','bold','italic','underline','|','bulletedList','numberedList','|','link','blockQuote','|','undo','redo'] };
ClassicEditor.create(document.querySelector('#editor-bio'), editorConfig).catch(e => console.error(e));
ClassicEditor.create(document.querySelector('#editor-education'), { toolbar: ['bold','italic','|','bulletedList','numberedList','|','undo','redo'] }).catch(e => console.error(e));
ClassicEditor.create(document.querySelector('#editor-research'), { toolbar: ['bold','italic','|','bulletedList','numberedList','|','undo','redo'] }).catch(e => console.error(e));
ClassicEditor.create(document.querySelector('#editor-publications'), { toolbar: ['bold','italic','|','bulletedList','numberedList','|','link','|','undo','redo'] }).catch(e => console.error(e));
</script>

<?php include 'footer.php'; ?>
