<?php
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');
require_once __DIR__ . '/../includes/admin_auth.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Edit Directory Profile";
$current_page = 'manage_directory.php';
$accent = '#7c3aed';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$item = ['type'=>'faculty','name'=>'','title'=>'','job_title'=>'','department'=>'','faculty_group'=>'','employment_status'=>'Full-time','email'=>'','phone'=>'','office_location'=>'','image_url'=>'','bio'=>'','education'=>'','research_interests'=>'','publications'=>''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM directory WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) { header("Location: manage_directory.php"); exit(); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; $name = $_POST['name']; $title = $_POST['title'];
    $job_title = $_POST['job_title']; $department = $_POST['department'];
    $faculty_group = $_POST['faculty_group']; $employment_status = $_POST['employment_status'];
    $email = $_POST['email']; $phone = $_POST['phone']; $office_location = $_POST['office_location'];
    $bio = $_POST['bio']; $education = $_POST['education'];
    $research_interests = $_POST['research_interests']; $publications = $_POST['publications'];

    $image_url = $item['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['image'], 'directory', 'dir_');
        if ($uploaded) $image_url = $uploaded;
    }

    if ($id) {
        $pdo->prepare("UPDATE directory SET type=?,name=?,title=?,job_title=?,department=?,faculty_group=?,employment_status=?,email=?,phone=?,office_location=?,image_url=?,bio=?,education=?,research_interests=?,publications=? WHERE id=?")->execute([$type,$name,$title,$job_title,$department,$faculty_group,$employment_status,$email,$phone,$office_location,$image_url,$bio,$education,$research_interests,$publications,$id]);
        $msg = "updated";
    } else {
        $pdo->prepare("INSERT INTO directory (type,name,title,job_title,department,faculty_group,employment_status,email,phone,office_location,image_url,bio,education,research_interests,publications) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([$type,$name,$title,$job_title,$department,$faculty_group,$employment_status,$email,$phone,$office_location,$image_url,$bio,$education,$research_interests,$publications]);
        $msg = "created";
    }
    header("Location: manage_directory.php?msg=$msg"); exit();
}

include 'header.php';
include 'sidebar.php';
$prev_img = $item['image_url'] ?: '../images/default-profile.png';
if ($prev_img && !str_starts_with($prev_img, 'http') && !str_starts_with($prev_img, '../')) $prev_img = '../' . $prev_img;
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-user-edit"></i> <?php echo $id ? 'Edit' : 'Add New'; ?> Profile</h1>
        <p class="page-description">Manage faculty and staff profile information.</p>
    </div></div>

    <!-- Header Card -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-user-edit"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <a href="manage_directory.php" style="color:rgba(255,255,255,0.8);text-decoration:none;font-size:.85rem;font-weight:700;display:flex;align-items:center;gap:6px;margin-bottom:8px;"><i class="fas fa-chevron-left"></i> Back to Directory</a>
                <h2 style="margin:0;font-size:2rem;font-weight:900;"><?php echo $id ? htmlspecialchars($item['name']) : 'New Profile'; ?></h2>
            </div>
            <?php if($id): ?>
            <span style="background:rgba(255,255,255,0.2);padding:8px 20px;border-radius:12px;font-size:.85rem;font-weight:800;">ID: <?php echo $id; ?></span>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <!-- Photo + Basic Info -->
        <div style="display:grid;grid-template-columns:300px 1fr;gap:30px;margin-bottom:30px;">
            <!-- Photo Card -->
            <div style="background:#fff;border-radius:20px;padding:30px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);text-align:center;">
                <div style="margin-bottom:20px;">
                    <img src="<?php echo htmlspecialchars($prev_img); ?>" id="preview" style="width:200px;height:200px;border-radius:50%;object-fit:cover;border:6px solid #f1f5f9;box-shadow:0 10px 30px rgba(0,0,0,0.1);">
                </div>
                <label for="imageInput" style="display:inline-flex;align-items:center;gap:8px;padding:10px 25px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:12px;font-weight:700;cursor:pointer;transition:all .2s;">
                    <i class="fas fa-camera"></i> Change Photo
                </label>
                <input type="file" name="image" id="imageInput" style="display:none;" onchange="previewImage(this)" accept="image/*">
                <p style="color:#94a3b8;font-size:.8rem;margin-top:10px;">Recommended: Square, min 500×500px</p>
            </div>

            <!-- Basic Info Card -->
            <div style="background:#fff;border-radius:20px;padding:30px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);">
                <h3 style="margin:0 0 25px;font-size:1.3rem;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:10px;"><i class="fas fa-id-badge" style="color:<?php echo $accent;?>;"></i> Basic Information</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Profile Type</label>
                        <select name="type" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-size:.95rem;background:#fff;">
                            <option value="faculty" <?php echo $item['type']=='faculty'?'selected':''; ?>>Faculty / Lecturer</option>
                            <option value="staff" <?php echo $item['type']=='staff'?'selected':''; ?>>Administrative Staff</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Title (Dr., Prof.)</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Job Title / Position</label>
                        <input type="text" name="job_title" value="<?php echo htmlspecialchars($item['job_title']); ?>" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($item['department']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Faculty / Unit Group</label>
                        <input type="text" name="faculty_group" value="<?php echo htmlspecialchars($item['faculty_group']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Employment Status</label>
                        <input type="text" name="employment_status" value="<?php echo htmlspecialchars($item['employment_status']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($item['email']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Phone</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($item['phone']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Office Location</label>
                        <input type="text" name="office_location" value="<?php echo htmlspecialchars($item['office_location']); ?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bio & Academic Info -->
        <div style="background:#fff;border-radius:20px;padding:30px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);margin-bottom:30px;">
            <h3 style="margin:0 0 25px;font-size:1.3rem;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:10px;"><i class="fas fa-book-reader" style="color:#059669;"></i> Academic Information</h3>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Biography</label>
                <textarea name="bio" id="editor-bio" rows="5" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($item['bio']); ?></textarea>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Education (One per line)</label>
                <textarea name="education" id="editor-education" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($item['education']); ?></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Research Interests</label>
                    <textarea name="research_interests" id="editor-research" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($item['research_interests']); ?></textarea>
                </div>
                <div>
                    <label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;font-size:.9rem;">Key Publications</label>
                    <textarea name="publications" id="editor-publications" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($item['publications']); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div style="display:flex;gap:15px;justify-content:flex-end;">
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
@media(max-width:768px){div[style*="grid-template-columns: 300px"]{grid-template-columns:1fr !important;}div[style*="grid-template-columns: 1fr 1fr"]{grid-template-columns:1fr !important;}}
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { document.getElementById('preview').src = e.target.result; }
        reader.readAsDataURL(input.files[0]);
    }
}
const editorConfig = { toolbar: ['heading','|','bold','italic','underline','|','bulletedList','numberedList','|','link','blockQuote','|','undo','redo'] };
ClassicEditor.create(document.querySelector('#editor-bio'), editorConfig).catch(e => console.error(e));
ClassicEditor.create(document.querySelector('#editor-education'), { toolbar: ['bold','italic','|','bulletedList','numberedList','|','undo','redo'] }).catch(e => console.error(e));
ClassicEditor.create(document.querySelector('#editor-research'), { toolbar: ['bold','italic','|','bulletedList','numberedList','|','undo','redo'] }).catch(e => console.error(e));
ClassicEditor.create(document.querySelector('#editor-publications'), { toolbar: ['bold','italic','|','bulletedList','numberedList','|','link','|','undo','redo'] }).catch(e => console.error(e));
</script>

<?php include 'footer.php'; ?>
