<?php
require_once('../includes/db_connect.php');
if (session_status() == PHP_SESSION_NONE) session_start();

// Handle AJAX save - before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['admin_id'])) { echo json_encode(['success'=>false,'message'=>'Session expired.']); exit; }
    try {
        if (!$pdo->inTransaction()) $pdo->beginTransaction();
        $content_updates = json_decode($_POST['content_data'] ?? '{}', true);
        if (json_last_error() !== JSON_ERROR_NONE) throw new Exception('Invalid JSON data.');

        $upload_dir = '../uploads/administration/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        foreach ($_FILES as $key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $field_id = str_replace('upload_', '', $key);
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx'];
                if (in_array($file_ext, $allowed)) {
                    $new_filename = 'admin_' . $field_id . '_' . time() . '.' . $file_ext;
                    if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename))
                        $content_updates[$field_id] = 'uploads/administration/' . $new_filename;
                }
            }
        }
        foreach ($content_updates as $field_id => $value) {
            $pdo->prepare("UPDATE administration_content_fields SET field_value = ? WHERE id = ?")->execute([$value, $field_id]);
        }
        $pdo->commit();
        echo json_encode(['success'=>true,'message'=>'Changes saved!']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Edit Page Content";
$current_page = 'manage_administration_pages.php';
$accent = '#0891b2';

include 'header.php';
include 'sidebar.php';

$page_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = $pdo->prepare("SELECT * FROM administration_pages WHERE id = ?"); $page->execute([$page_id]); $page = $page->fetch();
if (!$page) { header('Location: manage_administration_pages.php'); exit; }

$sections = $pdo->prepare("SELECT * FROM administration_content WHERE page_id = ? ORDER BY content_order ASC");
$sections->execute([$page_id]); $sections = $sections->fetchAll();

$content_ids = array_column($sections, 'id');
$fields_by_section = [];
if (!empty($content_ids)) {
    $ph = implode(',', array_fill(0, count($content_ids), '?'));
    $fs = $pdo->prepare("SELECT * FROM administration_content_fields WHERE content_id IN ($ph) ORDER BY id ASC");
    $fs->execute($content_ids);
    foreach ($fs->fetchAll() as $f) $fields_by_section[$f['content_id']][] = $f;
}
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-pen-nib"></i> Edit Page Content</h1>
        <p class="page-description">Editing: <?php echo htmlspecialchars($page['page_name']); ?></p>
    </div></div>

    <!-- Header Card -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-pen-nib"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <a href="manage_administration_pages.php" style="color:rgba(255,255,255,0.8);text-decoration:none;font-size:.85rem;font-weight:700;display:flex;align-items:center;gap:6px;margin-bottom:8px;"><i class="fas fa-chevron-left"></i> Back to Directory</a>
                <h2 style="margin:0;font-size:2rem;font-weight:900;"><?php echo htmlspecialchars($page['page_name']); ?></h2>
                <span style="font-family:monospace;font-size:.85rem;opacity:0.7;"><?php echo htmlspecialchars($page['page_slug']); ?>.php</span>
            </div>
            <div style="display:flex;gap:15px;align-items:center;">
                <?php if ($page_id == 53): ?>
                <a href="edit_administration_page.php?id=73" style="padding:12px 25px;background:rgba(255,255,255,0.2);color:#fff;border:2px solid #fff;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;transition:all 0.3s;backdrop-filter:blur(5px);">
                    <i class="fas fa-file-pdf"></i> Manage PDF Books
                </a>
                <a href="../online_resources.php" target="_blank" style="padding:12px 25px;background:rgba(255,255,255,0.2);color:#fff;border:2px solid #fff;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;transition:all 0.3s;backdrop-filter:blur(5px);">
                    <i class="fas fa-book-reader"></i> View Online Resources
                </a>
                <?php endif; ?>
                <a href="../<?php echo $page['page_slug']; ?>.php" target="_blank" style="padding:12px 25px;background:#fff;color:#1e293b;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
                    <i class="fas fa-external-link-alt"></i> Preview Page
                </a>
            </div>
        </div>
    </div>

    <form id="editForm">
        <?php foreach ($sections as $section): ?>
        <div style="background:#fff;border-radius:20px;margin-bottom:30px;box-shadow:0 4px 20px rgba(0,0,0,0.03);border:1px solid #f1f5f9;overflow:hidden;">
            <div style="background:#fafafa;padding:20px 30px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:<?php echo $accent;?>15;color:<?php echo $accent;?>;border-radius:10px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-layer-group"></i></div>
                    <h4 style="margin:0;font-size:1.1rem;font-weight:800;color:#1e293b;text-transform:uppercase;letter-spacing:.03em;"><?php echo htmlspecialchars(str_replace('_', ' ', $section['section_key'])); ?></h4>
                </div>
                <span style="padding:5px 14px;border-radius:8px;font-weight:800;font-size:.75rem;background:<?php echo $accent;?>15;color:<?php echo $accent;?>"><?php echo htmlspecialchars($section['section_type']); ?></span>
            </div>
            <div style="padding:30px;">
                <?php $fields = $fields_by_section[$section['id']] ?? []; ?>
                <?php foreach ($fields as $field): ?>
                <div style="margin-bottom:25px;">
                    <label style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <span style="font-weight:700;color:#334155;font-size:.95rem;"><?php echo htmlspecialchars(str_replace('_', ' ', ucwords($field['field_key']))); ?></span>
                        <span style="font-size:.7rem;text-transform:uppercase;padding:3px 10px;border-radius:6px;background:#f1f5f9;color:#64748b;font-weight:800;"><?php echo $field['field_type']; ?></span>
                    </label>

                    <?php if ($field['field_type'] === 'textarea' || $field['field_type'] === 'html'): ?>
                        <textarea class="content-field" data-field-id="<?php echo $field['id']; ?>" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;min-height:120px;font-size:1rem;line-height:1.6;resize:vertical;"><?php echo htmlspecialchars($field['field_value']); ?></textarea>

                    <?php elseif ($field['field_type'] === 'image'): ?>
                        <?php
                            $url = $field['field_value'];
                            if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) $url = '../' . $url;
                            if (empty($url)) $url = 'https://placehold.co/600x400?text=No+Image';
                        ?>
                        <div style="background:#f8fafc;border:2px dashed #e2e8f0;border-radius:16px;padding:20px;">
                            <div style="display:grid;grid-template-columns:200px 1fr;gap:20px;align-items:center;">
                                <img src="<?php echo htmlspecialchars($url); ?>" id="preview_<?php echo $field['id']; ?>" style="width:100%;height:130px;border-radius:12px;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                <div>
                                    <label style="display:block;margin-bottom:6px;font-weight:700;color:#64748b;font-size:.8rem;text-transform:uppercase;">Image URL</label>
                                    <input type="text" class="content-field" value="<?php echo htmlspecialchars($field['field_value']); ?>" data-field-id="<?php echo $field['id']; ?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;margin-bottom:12px;">
                                    <label style="display:block;margin-bottom:6px;font-weight:700;color:#64748b;font-size:.8rem;text-transform:uppercase;">Or Upload</label>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <input type="file" name="upload_<?php echo $field['id']; ?>" class="content-upload" id="file_<?php echo $field['id']; ?>" accept="image/*" style="display:none;" onchange="previewImage(this, <?php echo $field['id']; ?>)">
                                        <label for="file_<?php echo $field['id']; ?>" style="padding:8px 16px;background:#fff;border:1px solid #e2e8f0;border-radius:10px;font-weight:700;font-size:.85rem;color:#475569;cursor:pointer;display:flex;align-items:center;gap:6px;">
                                            <i class="fas fa-cloud-upload-alt"></i> Choose File
                                        </label>
                                        <span style="font-size:.8rem;color:#94a3b8;" id="filename_<?php echo $field['id']; ?>">No file chosen</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($field['field_type'] === 'file'): ?>
                        <div style="background:#f8fafc;border:2px dashed #e2e8f0;border-radius:16px;padding:20px;">
                            <label style="display:block;margin-bottom:6px;font-weight:700;color:#64748b;font-size:.8rem;text-transform:uppercase;">File Path</label>
                            <input type="text" class="content-field" value="<?php echo htmlspecialchars($field['field_value']); ?>" data-field-id="<?php echo $field['id']; ?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;margin-bottom:12px;">
                            <div style="background:#fff;padding:12px 18px;border-radius:10px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:12px;">
                                <i class="fas fa-file-pdf" style="color:#ef4444;font-size:1.5rem;"></i>
                                <div style="flex:1;">
                                    <div style="font-weight:700;font-size:.85rem;color:#475569;">Replace Document</div>
                                    <input type="file" name="upload_<?php echo $field['id']; ?>" class="content-upload" accept=".pdf,.doc,.docx,.xls,.xlsx" style="margin-top:4px;">
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <input type="text" class="content-field" value="<?php echo htmlspecialchars($field['field_value']); ?>" data-field-id="<?php echo $field['id']; ?>" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:12px;font-size:1rem;">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Sticky Save Bar -->
        <div style="position:sticky;bottom:20px;background:rgba(255,255,255,0.9);backdrop-filter:blur(10px);padding:18px 30px;border-radius:20px;border:1px solid #f1f5f9;box-shadow:0 -5px 30px rgba(0,0,0,0.08);display:flex;align-items:center;justify-content:center;gap:20px;z-index:1000;">
            <button type="submit" style="padding:14px 40px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:14px;font-size:1.05rem;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:10px;box-shadow:0 10px 20px <?php echo $accent;?>33;">
                <i class="fas fa-save"></i> Update All Sections
            </button>
            <div id="saveMsg"></div>
        </div>
    </form>
</div>
</main>

<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<style>
input:focus,textarea:focus{outline:none;border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a;}
.ck-editor__editable{min-height:250px;border-radius:0 0 12px 12px !important;}
.ck-toolbar{border-radius:12px 12px 0 0 !important;background:#f8fafc !important;border-color:#e2e8f0 !important;}
.ck.ck-editor__main>.ck-editor__editable:not(.ck-focused){border-color:#e2e8f0 !important;}
.ck.ck-editor__main>.ck-editor__editable.ck-focused{border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a !important;}
@media(max-width:768px){div[style*="grid-template-columns: 200px"]{grid-template-columns:1fr !important;}}
</style>

<script>
const editors = {};
document.querySelectorAll('textarea.content-field').forEach(textarea => {
    const fieldId = textarea.getAttribute('data-field-id');
    ClassicEditor.create(textarea, { toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','undo','redo'] })
        .then(e => { editors[fieldId] = e; }).catch(e => console.error(e));
});

function previewImage(input, id) {
    document.getElementById('filename_' + id).textContent = input.files[0].name;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('preview_' + id).setAttribute('src', e.target.result);
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const msg = document.getElementById('saveMsg');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SAVING...';

    const formData = new FormData(); formData.append('ajax', '1');
    const data = {};
    document.querySelectorAll('.content-field').forEach(f => {
        const fid = f.getAttribute('data-field-id');
        data[fid] = editors[fid] ? editors[fid].getData() : f.value;
    });
    formData.append('content_data', JSON.stringify(data));
    document.querySelectorAll('.content-upload').forEach(u => { if (u.files.length > 0) formData.append(u.name, u.files[0]); });

    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Update All Sections';
        if (res.success) {
            msg.innerHTML = '<span style="color:#059669;font-weight:800;"><i class="fas fa-check-circle"></i> Saved!</span>';
            setTimeout(() => location.reload(), 1500);
        } else alert('Error: ' + res.message);
    })
    .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Update All Sections'; alert('Save failed.'); });
});
</script>

<?php include 'footer.php'; ?>
