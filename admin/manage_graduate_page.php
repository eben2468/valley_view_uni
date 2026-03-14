<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Manage Graduate School Page";
$current_page = 'manage_graduate_page.php';
$accent = '#1e3a5f';
$message = ''; $message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'update_content') {
            $hero_img = $_POST['hero_image'] ?? '';
            $up = handleAdminFileUpload($_FILES['hero_image_file'] ?? [], 'graduate');
            if ($up) $hero_img = $up;
            $about_img = $_POST['about_image'] ?? '';
            $up2 = handleAdminFileUpload($_FILES['about_image_file'] ?? [], 'graduate');
            if ($up2) $about_img = $up2;
            $dean_img = $_POST['dean_image'] ?? '';
            $up3 = handleAdminFileUpload($_FILES['dean_image_file'] ?? [], 'graduate');
            if ($up3) $dean_img = $up3;
            $stmt = $pdo->prepare("UPDATE graduate_page_content SET hero_badge=?,hero_title=?,hero_subtitle=?,hero_image=?,about_heading=?,about_text=?,about_text_2=?,about_image=?,programs_heading=?,programs_subtitle=?,cta_heading=?,cta_subtitle=?,cta_button_text=?,cta_button_link=?,cta_button2_text=?,cta_button2_link=?,contact_heading=?,contact_phone=?,contact_email=?,contact_location=?,contact_hours=?,dean_name=?,dean_title=?,dean_message=?,dean_image=? WHERE id=1");
            $stmt->execute([$_POST['hero_badge']??'',$_POST['hero_title']??'',$_POST['hero_subtitle']??'',$hero_img,$_POST['about_heading']??'',$_POST['about_text']??'',$_POST['about_text_2']??'',$about_img,$_POST['programs_heading']??'',$_POST['programs_subtitle']??'',$_POST['cta_heading']??'',$_POST['cta_subtitle']??'',$_POST['cta_button_text']??'',$_POST['cta_button_link']??'',$_POST['cta_button2_text']??'',$_POST['cta_button2_link']??'',$_POST['contact_heading']??'',$_POST['contact_phone']??'',$_POST['contact_email']??'',$_POST['contact_location']??'',$_POST['contact_hours']??'',$_POST['dean_name']??'',$_POST['dean_title']??'',$_POST['dean_message']??'',$dean_img]);
            $message = "Content updated!"; $message_type = "success";
        } elseif ($action === 'update_section') {
            $pdo->prepare("UPDATE graduate_page_sections SET section_title=?,section_subtitle=? WHERE id=?")->execute([$_POST['section_title'],$_POST['section_subtitle']??'',$_POST['section_id']]);
            $message = "Section updated!"; $message_type = "success";
        } elseif ($action === 'update_item') {
            $pdo->prepare("UPDATE graduate_page_items SET item_title=?,item_description=?,item_icon=?,item_color=?,item_link=?,is_active=? WHERE id=?")->execute([$_POST['item_title']??'',$_POST['item_description']??'',$_POST['item_icon']??'',$_POST['item_color']??'',$_POST['item_link']??'',isset($_POST['is_active'])?1:0,$_POST['item_id']]);
            $message = "Item updated!"; $message_type = "success";
        } elseif ($action === 'add_item') {
            $pdo->prepare("INSERT INTO graduate_page_items (section_key,item_title,item_description,item_icon,item_color,display_order) VALUES (?,?,?,?,?,(SELECT COALESCE(MAX(display_order),0)+1 FROM graduate_page_items i2 WHERE i2.section_key=?))")->execute([$_POST['section_key'],$_POST['item_title']??'',$_POST['item_description']??'',$_POST['item_icon']??'',$_POST['item_color']??'blue',$_POST['section_key']]);
            $message = "Item added!"; $message_type = "success";
        } elseif ($action === 'delete_item') {
            $pdo->prepare("DELETE FROM graduate_page_items WHERE id=?")->execute([$_POST['item_id']]);
            $message = "Item deleted!"; $message_type = "success";
        } elseif ($action === 'update_stat') {
            $pdo->prepare("UPDATE graduate_page_stats SET stat_value=?,stat_label=?,stat_icon=? WHERE id=?")->execute([$_POST['stat_value'],$_POST['stat_label'],$_POST['stat_icon']??'',$_POST['stat_id']]);
            $message = "Stat updated!"; $message_type = "success";
        }
    } catch (PDOException $e) { $message = "Error: " . $e->getMessage(); $message_type = "error"; }
}

$content = $pdo->query("SELECT * FROM graduate_page_content WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$sections = $pdo->query("SELECT * FROM graduate_page_sections ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
$all_items = $pdo->query("SELECT * FROM graduate_page_items ORDER BY section_key, display_order")->fetchAll(PDO::FETCH_ASSOC);
$items = []; foreach ($all_items as $item) $items[$item['section_key']][] = $item;
$stats = $pdo->query("SELECT * FROM graduate_page_stats ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-graduation-cap"></i> Graduate School CMS</h1>
        <p class="page-description">Manage all content on the School of Graduate Studies page.</p>
    </div></div>

    <?php if ($message): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:<?php echo $message_type==='success'?'#ecfdf5':'#fef2f2';?>;border:1px solid <?php echo $message_type==='success'?'#10b981':'#ef4444';?>;color:<?php echo $message_type==='success'?'#065f46':'#991b1b';?>;">
        <i class="fas fa-<?php echo $message_type==='success'?'check-circle':'exclamation-circle';?>"></i>
        <strong><?php echo $message_type==='success'?'Success!':'Error:';?></strong> <?php echo htmlspecialchars($message);?>
    </div>
    <?php endif;?>

    <!-- Page Preview Header -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-graduation-cap"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <span style="background:rgba(255,255,255,0.2);padding:5px 15px;border-radius:20px;font-size:.75rem;font-weight:800;text-transform:uppercase;">Editing</span>
                <h2 style="margin:10px 0 5px;font-size:2rem;font-weight:900;">Graduate School</h2>
            </div>
            <a href="../graduate.php" target="_blank" style="padding:12px 25px;background:#fff;color:#1e293b;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-external-link-alt"></i> Live View
            </a>
        </div>
    </div>

    <!-- Main Content Form -->
    <div style="background:#fff;border-radius:20px;padding:35px;margin-bottom:40px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:15px;margin-bottom:30px;">
            <div style="width:50px;height:50px;background:<?php echo $accent;?>22;color:<?php echo $accent;?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="fas fa-pager"></i></div>
            <div><h3 style="margin:0;font-size:1.5rem;font-weight:800;color:#1e293b;">Hero, About & Contact</h3>
            <p style="margin:0;color:#64748b;">Main page content and branding.</p></div>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_content">

            <!-- Hero -->
            <h4 style="color:<?php echo $accent;?>;font-weight:800;margin:0 0 15px;border-bottom:2px solid <?php echo $accent;?>22;padding-bottom:8px;"><i class="fas fa-flag"></i> Hero Section</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Badge</label>
                <input type="text" name="hero_badge" value="<?php echo htmlspecialchars($content['hero_badge']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Title</label>
                <input type="text" name="hero_title" value="<?php echo htmlspecialchars($content['hero_title']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Subtitle</label>
            <textarea name="hero_subtitle" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['hero_subtitle']??'');?></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:15px;background:#f8fafc;padding:15px;border-radius:12px;border:1px dashed #cbd5e1;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Hero Image URL</label>
                <input type="text" name="hero_image" value="<?php echo htmlspecialchars($content['hero_image']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Upload</label>
                <input type="file" name="hero_image_file" style="width:100%;padding:8px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;"></div>
            </div>

            <!-- About -->
            <h4 style="color:#059669;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #ecfdf5;padding-bottom:8px;"><i class="fas fa-info-circle"></i> About Section</h4>
            <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">About Heading</label>
            <input type="text" name="about_heading" value="<?php echo htmlspecialchars($content['about_heading']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">About Text (Primary)</label>
            <textarea name="about_text" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['about_text']??'');?></textarea></div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">About Text (Secondary)</label>
            <textarea name="about_text_2" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['about_text_2']??'');?></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:15px;background:#f8fafc;padding:15px;border-radius:12px;border:1px dashed #cbd5e1;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">About Image URL</label>
                <input type="text" name="about_image" value="<?php echo htmlspecialchars($content['about_image']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Upload</label>
                <input type="file" name="about_image_file" style="width:100%;padding:8px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;"></div>
            </div>

            <!-- Programs -->
            <h4 style="color:#7c3aed;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #ede9fe;padding-bottom:8px;"><i class="fas fa-book"></i> Programs Section Headers</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Programs Heading</label>
                <input type="text" name="programs_heading" value="<?php echo htmlspecialchars($content['programs_heading']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Programs Subtitle</label>
                <input type="text" name="programs_subtitle" value="<?php echo htmlspecialchars($content['programs_subtitle']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>

            <!-- Dean -->
            <h4 style="color:#b45309;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #fef3c7;padding-bottom:8px;"><i class="fas fa-user-tie"></i> Dean / Leadership</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Name</label>
                <input type="text" name="dean_name" value="<?php echo htmlspecialchars($content['dean_name']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Title</label>
                <input type="text" name="dean_title" value="<?php echo htmlspecialchars($content['dean_title']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Dean's Message</label>
            <textarea name="dean_message" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['dean_message']??'');?></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:15px;background:#f8fafc;padding:15px;border-radius:12px;border:1px dashed #cbd5e1;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Dean Image URL</label>
                <input type="text" name="dean_image" value="<?php echo htmlspecialchars($content['dean_image']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Upload</label>
                <input type="file" name="dean_image_file" style="width:100%;padding:8px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;"></div>
            </div>

            <!-- CTA & Contact -->
            <h4 style="color:#0891b2;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #ecfeff;padding-bottom:8px;"><i class="fas fa-bullhorn"></i> CTA & Contact</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">CTA Heading</label>
                <input type="text" name="cta_heading" value="<?php echo htmlspecialchars($content['cta_heading']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">CTA Subtitle</label>
                <input type="text" name="cta_subtitle" value="<?php echo htmlspecialchars($content['cta_subtitle']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 1 Text</label>
                <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars($content['cta_button_text']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 1 Link</label>
                <input type="text" name="cta_button_link" value="<?php echo htmlspecialchars($content['cta_button_link']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 2 Text</label>
                <input type="text" name="cta_button2_text" value="<?php echo htmlspecialchars($content['cta_button2_text']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 2 Link</label>
                <input type="text" name="cta_button2_link" value="<?php echo htmlspecialchars($content['cta_button2_link']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Contact Heading</label>
                <input type="text" name="contact_heading" value="<?php echo htmlspecialchars($content['contact_heading']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Phone</label>
                <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($content['contact_phone']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Email</label>
                <input type="text" name="contact_email" value="<?php echo htmlspecialchars($content['contact_email']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Hours</label>
                <input type="text" name="contact_hours" value="<?php echo htmlspecialchars($content['contact_hours']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Location</label>
            <textarea name="contact_location" rows="2" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"><?php echo htmlspecialchars($content['contact_location']??'');?></textarea></div>

            <div style="margin-top:25px;text-align:right;">
                <button type="submit" style="padding:14px 35px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:14px;font-size:1.05rem;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:10px;box-shadow:0 10px 20px <?php echo $accent;?>33;">
                    <i class="fas fa-save"></i> Save All Content
                </button>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <?php if (!empty($stats)):?>
    <div style="background:#fff;border-radius:20px;padding:35px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);margin-bottom:40px;">
        <h3 style="margin:0 0 25px;font-size:1.5rem;font-weight:800;color:#1e293b;"><i class="fas fa-chart-bar" style="color:<?php echo $accent;?>;margin-right:10px;"></i>Statistics</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
        <?php foreach ($stats as $stat):?>
            <form method="POST" style="background:#fafafa;border:1px solid #e2e8f0;padding:15px;border-radius:12px;">
                <input type="hidden" name="action" value="update_stat"><input type="hidden" name="stat_id" value="<?php echo $stat['id'];?>">
                <input type="text" name="stat_value" value="<?php echo htmlspecialchars($stat['stat_value']);?>" style="width:100%;border:none;border-bottom:2px solid #e2e8f0;background:transparent;font-size:1.3rem;font-weight:900;color:<?php echo $accent;?>;padding:4px;margin-bottom:8px;">
                <input type="text" name="stat_label" value="<?php echo htmlspecialchars($stat['stat_label']);?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.85rem;margin-bottom:8px;">
                <div style="display:flex;gap:6px;"><input type="text" name="stat_icon" value="<?php echo htmlspecialchars($stat['stat_icon']??'');?>" style="flex:1;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;" placeholder="Icon">
                <button type="submit" style="background:<?php echo $accent;?>;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;"><i class="fas fa-save"></i></button></div>
            </form>
        <?php endforeach;?>
        </div>
    </div>
    <?php endif;?>

    <!-- Sections & Items -->
    <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
        <h2 style="margin:0;font-size:1.8rem;font-weight:900;color:#1e293b;">Page Sections & Content</h2>
    </div>
    <?php foreach ($sections as $section):?>
    <div style="background:#fff;border-radius:20px;margin-bottom:35px;box-shadow:0 4px 20px rgba(0,0,0,0.03);border:1px solid #f1f5f9;overflow:hidden;">
        <div style="background:#fafafa;padding:20px 30px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">
                    <span style="background:<?php echo $accent;?>;color:#fff;padding:3px 10px;border-radius:6px;font-size:.7rem;font-weight:900;text-transform:uppercase;"><?php echo $section['section_key'];?></span>
                    <h3 style="margin:0;font-size:1.2rem;font-weight:800;color:#1e293b;"><?php echo htmlspecialchars($section['section_title']);?></h3>
                </div>
                <p style="margin:0;color:#64748b;font-size:.9rem;font-style:italic;"><?php echo htmlspecialchars($section['section_subtitle']??'');?></p>
            </div>
            <div style="display:flex;gap:8px;">
                <button onclick="editSection(<?php echo $section['id'];?>,'<?php echo addslashes($section['section_title']);?>','<?php echo addslashes($section['section_subtitle']??'');?>')" style="padding:8px 16px;border-radius:10px;background:#fff;border:1px solid #e2e8f0;color:#475569;font-weight:700;cursor:pointer;"><i class="fas fa-edit"></i> Edit</button>
                <button onclick="addItem('<?php echo $section['section_key'];?>')" style="padding:8px 16px;border-radius:10px;background:#10b981;border:none;color:#fff;font-weight:700;cursor:pointer;"><i class="fas fa-plus"></i> Add</button>
            </div>
        </div>
        <div style="padding:25px;">
            <?php if (empty($items[$section['section_key']])):?>
                <div style="text-align:center;padding:30px;color:#94a3b8;">Empty section</div>
            <?php else:?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:15px;">
                <?php foreach ($items[$section['section_key']] as $item):?>
                    <div style="background:<?php echo $item['is_active']?'#fff':'#f8fafc';?>;border:1px solid #e2e8f0;padding:20px;border-radius:14px;opacity:<?php echo $item['is_active']?'1':'0.7';?>;">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_item"><input type="hidden" name="item_id" value="<?php echo $item['id'];?>">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                <input type="text" name="item_title" value="<?php echo htmlspecialchars($item['item_title']);?>" style="font-weight:800;font-size:1rem;color:#1e293b;border:none;border-bottom:2px solid #f1f5f9;padding:4px;width:65%;background:transparent;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <label style="display:flex;align-items:center;gap:4px;font-size:.75rem;font-weight:700;color:#64748b;cursor:pointer;"><input type="checkbox" name="is_active" <?php echo $item['is_active']?'checked':'';?>>On</label>
                                    <button type="button" onclick="deleteItem(<?php echo $item['id'];?>)" style="background:#fee2e2;color:#ef4444;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;"><i class="fas fa-trash-alt" style="font-size:.7rem;"></i></button>
                                </div>
                            </div>
                            <textarea name="item_description" rows="2" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px;font-size:.85rem;margin-bottom:10px;"><?php echo htmlspecialchars($item['item_description']??'');?></textarea>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Icon</label>
                                <input type="text" name="item_icon" value="<?php echo htmlspecialchars($item['item_icon']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Color</label>
                                <input type="text" name="item_color" value="<?php echo htmlspecialchars($item['item_color']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                            </div>
                            <button type="submit" style="width:100%;padding:10px;background:#1e293b;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;"><i class="fas fa-check-double"></i> Save</button>
                        </form>
                    </div>
                <?php endforeach;?>
                </div>
            <?php endif;?>
        </div>
    </div>
    <?php endforeach;?>
</div>
</main>

<!-- Modals -->
<div id="addItemModal" class="admin-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:flex-start;justify-content:center;padding:40px 20px;overflow-y:auto;backdrop-filter:blur(5px);">
    <div style="background:#fff;width:90%;max-width:550px;padding:30px;border-radius:20px;box-shadow:0 25px 50px rgba(0,0,0,0.25);margin:auto;">
        <h3 style="margin-top:0;font-weight:900;color:#1e293b;"><i class="fas fa-plus-circle" style="color:<?php echo $accent;?>;"></i> Add New Item</h3>
        <p style="color:#64748b;">Section: <strong id="sectionDisplay"></strong></p>
        <form method="POST">
            <input type="hidden" name="action" value="add_item"><input type="hidden" name="section_key" id="addItemSectionKey">
            <div style="display:grid;gap:12px;">
                <input type="text" name="item_title" required placeholder="Title" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                <textarea name="item_description" rows="2" placeholder="Description" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></textarea>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <input type="text" name="item_icon" placeholder="Icon" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                    <input type="text" name="item_color" placeholder="Color (blue)" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                </div>
                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('addItemModal').style.display='none'" style="padding:12px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:12px 30px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="editSectionModal" class="admin-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:flex-start;justify-content:center;padding:40px 20px;overflow-y:auto;backdrop-filter:blur(5px);">
    <div style="background:#fff;width:90%;max-width:450px;padding:30px;border-radius:20px;box-shadow:0 25px 50px rgba(0,0,0,0.25);margin:auto;">
        <h3 style="margin-top:0;font-weight:900;color:#1e293b;"><i class="fas fa-layer-group" style="color:<?php echo $accent;?>;"></i> Edit Section</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_section"><input type="hidden" name="section_id" id="editSectionId">
            <div style="display:grid;gap:12px;">
                <input type="text" name="section_title" id="editSectionTitle" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                <input type="text" name="section_subtitle" id="editSectionSubtitle" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;" placeholder="Subtitle">
                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('editSectionModal').style.display='none'" style="padding:12px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:12px 30px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<form id="deleteItemForm" method="POST" style="display:none;"><input type="hidden" name="action" value="delete_item"><input type="hidden" name="item_id" id="deleteItemId"></form>

<script>
function deleteItem(id){if(confirm('Delete?')){document.getElementById('deleteItemId').value=id;document.getElementById('deleteItemForm').submit();}}
function addItem(sk){document.getElementById('addItemSectionKey').value=sk;document.getElementById('sectionDisplay').innerText=sk.replace(/_/g,' ').toUpperCase();document.getElementById('addItemModal').style.display='flex';}
function editSection(id,t,s){document.getElementById('editSectionId').value=id;document.getElementById('editSectionTitle').value=t;document.getElementById('editSectionSubtitle').value=s;document.getElementById('editSectionModal').style.display='flex';}
window.onclick=function(e){if(e.target.classList.contains('admin-modal'))e.target.style.display='none';}
</script>
<style>
input:focus,textarea:focus{outline:none;border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a;}
@media(max-width:768px){div[style*="grid-template-columns"]{grid-template-columns:1fr !important;}}
</style>
<?php include 'footer.php';?>
