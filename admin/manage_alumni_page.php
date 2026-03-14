<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Manage Alumni Network Page";
$current_page = 'manage_alumni_page.php';
$accent = '#1e40af';
$message = ''; $message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'update_content') {
            $mission_img = $_POST['mission_image'] ?? '';
            $up = handleAdminFileUpload($_FILES['mission_image_file'] ?? [], 'alumni');
            if ($up) $mission_img = $up;
            $coord_img = $_POST['coordinator_image'] ?? '';
            $up2 = handleAdminFileUpload($_FILES['coordinator_image_file'] ?? [], 'alumni');
            if ($up2) $coord_img = $up2;
            $stmt = $pdo->prepare("UPDATE alumni_page_content SET mission_heading=?,mission_text=?,mission_text_2=?,mission_image=?,coordinator_name=?,coordinator_title=?,coordinator_description=?,coordinator_image=?,coordinator_email=?,coordinator_phone=?,cta_heading=?,cta_subtitle=?,cta_description=?,cta_button_text=?,cta_button_link=?,cta_button2_text=?,cta_button2_link=?,contact_heading=?,contact_location=?,contact_phone=?,contact_phone_note=?,contact_address=?,social_facebook=?,social_twitter=?,social_linkedin=?,social_threads=?,social_instagram=?,social_youtube=? WHERE id=1");
            $stmt->execute([$_POST['mission_heading']??'',$_POST['mission_text']??'',$_POST['mission_text_2']??'',$mission_img,$_POST['coordinator_name']??'',$_POST['coordinator_title']??'',$_POST['coordinator_description']??'',$coord_img,$_POST['coordinator_email']??'',$_POST['coordinator_phone']??'',$_POST['cta_heading']??'',$_POST['cta_subtitle']??'',$_POST['cta_description']??'',$_POST['cta_button_text']??'',$_POST['cta_button_link']??'',$_POST['cta_button2_text']??'',$_POST['cta_button2_link']??'',$_POST['contact_heading']??'',$_POST['contact_location']??'',$_POST['contact_phone']??'',$_POST['contact_phone_note']??'',$_POST['contact_address']??'',$_POST['social_facebook']??'',$_POST['social_twitter']??'',$_POST['social_linkedin']??'',$_POST['social_threads']??'',$_POST['social_instagram']??'',$_POST['social_youtube']??'']);
            $message = "Content updated!"; $message_type = "success";
        } elseif ($action === 'update_slide') {
            $img = $_POST['image_url'] ?? '';
            $up = handleAdminFileUpload($_FILES['slide_image_file'] ?? [], 'alumni');
            if ($up) $img = $up;
            $stmt = $pdo->prepare("UPDATE alumni_page_slides SET image_url=?,alt_text=?,is_active=? WHERE id=?");
            $stmt->execute([$img,$_POST['alt_text']??'',isset($_POST['is_active'])?1:0,$_POST['slide_id']]);
            $message = "Slide updated!"; $message_type = "success";
        } elseif ($action === 'add_slide') {
            $img = $_POST['image_url'] ?? '';
            $up = handleAdminFileUpload($_FILES['slide_image_file'] ?? [], 'alumni');
            if ($up) $img = $up;
            $stmt = $pdo->prepare("INSERT INTO alumni_page_slides (image_url,alt_text,display_order) VALUES (?,?,(SELECT COALESCE(MAX(display_order),0)+1 FROM alumni_page_slides s2))");
            $stmt->execute([$img,$_POST['alt_text']??'']);
            $message = "Slide added!"; $message_type = "success";
        } elseif ($action === 'delete_slide') {
            $pdo->prepare("DELETE FROM alumni_page_slides WHERE id=?")->execute([$_POST['slide_id']]);
            $message = "Slide deleted!"; $message_type = "success";
        } elseif ($action === 'update_section') {
            $stmt = $pdo->prepare("UPDATE alumni_page_sections SET section_title=?,section_subtitle=? WHERE id=?");
            $stmt->execute([$_POST['section_title'],$_POST['section_subtitle']??'',$_POST['section_id']]);
            $message = "Section updated!"; $message_type = "success";
        } elseif ($action === 'update_item') {
            $stmt = $pdo->prepare("UPDATE alumni_page_items SET item_title=?,item_description=?,item_icon=?,item_color=?,item_link=?,item_link_text=?,item_link_color=?,item_bg_class=?,is_active=? WHERE id=?");
            $stmt->execute([$_POST['item_title']??'',$_POST['item_description']??'',$_POST['item_icon']??'',$_POST['item_color']??'',$_POST['item_link']??'',$_POST['item_link_text']??'',$_POST['item_link_color']??'',$_POST['item_bg_class']??'',isset($_POST['is_active'])?1:0,$_POST['item_id']]);
            $message = "Item updated!"; $message_type = "success";
        } elseif ($action === 'add_item') {
            $stmt = $pdo->prepare("INSERT INTO alumni_page_items (section_key,item_title,item_description,item_icon,item_color,item_link,item_link_text,item_link_color,item_bg_class,display_order) VALUES (?,?,?,?,?,?,?,?,?,(SELECT COALESCE(MAX(display_order),0)+1 FROM alumni_page_items i2 WHERE i2.section_key=?))");
            $stmt->execute([$_POST['section_key'],$_POST['item_title']??'',$_POST['item_description']??'',$_POST['item_icon']??'',$_POST['item_color']??'alumni-gradient',$_POST['item_link']??'',$_POST['item_link_text']??'',$_POST['item_link_color']??'',$_POST['item_bg_class']??'',$_POST['section_key']]);
            $message = "Item added!"; $message_type = "success";
        } elseif ($action === 'delete_item') {
            $pdo->prepare("DELETE FROM alumni_page_items WHERE id=?")->execute([$_POST['item_id']]);
            $message = "Item deleted!"; $message_type = "success";
        } elseif ($action === 'update_stat') {
            $stmt = $pdo->prepare("UPDATE alumni_page_stats SET stat_value=?,stat_label=?,stat_color=?,stat_bg=? WHERE id=?");
            $stmt->execute([$_POST['stat_value'],$_POST['stat_label'],$_POST['stat_color']??'blue',$_POST['stat_bg']??'',$_POST['stat_id']]);
            $message = "Stat updated!"; $message_type = "success";
        }
    } catch (PDOException $e) { $message = "Error: " . $e->getMessage(); $message_type = "error"; }
}

// Fetch all data
$content = $pdo->query("SELECT * FROM alumni_page_content WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$slides = $pdo->query("SELECT * FROM alumni_page_slides ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
$sections = $pdo->query("SELECT * FROM alumni_page_sections ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
$all_items = $pdo->query("SELECT * FROM alumni_page_items ORDER BY section_key, display_order")->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($all_items as $item) $items[$item['section_key']][] = $item;
$stats = $pdo->query("SELECT * FROM alumni_page_stats ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-user-graduate"></i> Alumni Network CMS</h1>
        <p class="page-description">Manage all content on the Alumni Network page.</p>
    </div></div>

    <?php if ($message): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:<?php echo $message_type==='success'?'#ecfdf5':'#fef2f2';?>;border:1px solid <?php echo $message_type==='success'?'#10b981':'#ef4444';?>;color:<?php echo $message_type==='success'?'#065f46':'#991b1b';?>;">
        <i class="fas fa-<?php echo $message_type==='success'?'check-circle':'exclamation-circle';?>"></i>
        <div><strong><?php echo $message_type==='success'?'Success!':'Error:';?></strong> <?php echo htmlspecialchars($message);?></div>
    </div>
    <?php endif;?>

    <!-- Page Preview Header -->
    <div style="background:linear-gradient(135deg,<?php echo $accent;?>cc,<?php echo $accent;?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas fa-user-graduate"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <span style="background:rgba(255,255,255,0.2);padding:5px 15px;border-radius:20px;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Editing</span>
                <h2 style="margin:10px 0 5px;font-size:2rem;font-weight:900;">Alumni Network</h2>
            </div>
            <a href="../alumni_network_page_1.php" target="_blank" style="padding:12px 25px;background:#fff;color:#1e293b;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-external-link-alt"></i> Live View
            </a>
        </div>
    </div>

    <!-- Hero Slider Images -->
    <div style="background:#fff;border-radius:20px;padding:35px;margin-bottom:40px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:25px;">
            <div style="display:flex;align-items:center;gap:15px;">
                <div style="width:50px;height:50px;background:<?php echo $accent;?>22;color:<?php echo $accent;?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="fas fa-images"></i></div>
                <div><h3 style="margin:0;font-size:1.5rem;font-weight:800;color:#1e293b;">Hero Slider Images</h3>
                <p style="margin:0;color:#64748b;">Background images that rotate in the hero section.</p></div>
            </div>
            <button onclick="document.getElementById('addSlideModal').style.display='flex'" style="padding:10px 20px;background:#10b981;color:#fff;border:none;border-radius:12px;font-weight:700;cursor:pointer;"><i class="fas fa-plus"></i> Add Slide</button>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:15px;">
        <?php foreach ($slides as $slide): ?>
            <form method="POST" enctype="multipart/form-data" style="background:#f8fafc;border:1px solid #e2e8f0;padding:15px;border-radius:14px;">
                <input type="hidden" name="action" value="update_slide"><input type="hidden" name="slide_id" value="<?php echo $slide['id'];?>">
                <?php if ($slide['image_url']):?><img src="../<?php echo htmlspecialchars($slide['image_url']);?>" style="width:100%;height:140px;object-fit:cover;border-radius:10px;margin-bottom:10px;"><?php endif;?>
                <input type="text" name="image_url" value="<?php echo htmlspecialchars($slide['image_url']);?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;font-size:.85rem;" placeholder="Image URL">
                <input type="file" name="slide_image_file" style="width:100%;margin-bottom:8px;font-size:.75rem;">
                <input type="text" name="alt_text" value="<?php echo htmlspecialchars($slide['alt_text']);?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;font-size:.85rem;" placeholder="Alt text">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <label style="display:flex;align-items:center;gap:4px;font-size:.8rem;font-weight:700;color:#64748b;cursor:pointer;"><input type="checkbox" name="is_active" <?php echo $slide['is_active']?'checked':'';?>> Active</label>
                    <div style="display:flex;gap:6px;">
                        <button type="submit" style="padding:8px 14px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:.8rem;"><i class="fas fa-save"></i> Save</button>
                        <button type="button" onclick="deleteSlide(<?php echo $slide['id'];?>)" style="padding:8px 10px;background:#fee2e2;color:#ef4444;border:none;border-radius:8px;cursor:pointer;font-size:.8rem;"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </form>
        <?php endforeach;?>
        </div>
    </div>

    <!-- Mission & Content -->
    <div style="background:#fff;border-radius:20px;padding:35px;margin-bottom:40px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:15px;margin-bottom:30px;">
            <div style="width:50px;height:50px;background:<?php echo $accent;?>22;color:<?php echo $accent;?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="fas fa-bullseye"></i></div>
            <div><h3 style="margin:0;font-size:1.5rem;font-weight:800;color:#1e293b;">Mission, Coordinator & Contact</h3>
            <p style="margin:0;color:#64748b;">Main page content sections.</p></div>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_content">

            <!-- Mission -->
            <h4 style="color:<?php echo $accent;?>;font-weight:800;margin:0 0 15px;border-bottom:2px solid <?php echo $accent;?>22;padding-bottom:8px;"><i class="fas fa-crosshairs"></i> Mission Section</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Mission Heading</label>
                <input type="text" name="mission_heading" value="<?php echo htmlspecialchars($content['mission_heading']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Mission Image URL</label>
                    <input type="text" name="mission_image" value="<?php echo htmlspecialchars($content['mission_image']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Upload Image</label>
                    <input type="file" name="mission_image_file" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;"></div>
                </div>
            </div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Mission Text (Primary)</label>
            <textarea name="mission_text" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['mission_text']??'');?></textarea></div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Mission Text (Secondary)</label>
            <textarea name="mission_text_2" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['mission_text_2']??'');?></textarea></div>

            <!-- Coordinator -->
            <h4 style="color:#b45309;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #fef3c722;padding-bottom:8px;"><i class="fas fa-user-tie"></i> Alumni Coordinator</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Name</label>
                <input type="text" name="coordinator_name" value="<?php echo htmlspecialchars($content['coordinator_name']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Title</label>
                <input type="text" name="coordinator_title" value="<?php echo htmlspecialchars($content['coordinator_title']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Email</label>
                <input type="text" name="coordinator_email" value="<?php echo htmlspecialchars($content['coordinator_email']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Phone</label>
                <input type="text" name="coordinator_phone" value="<?php echo htmlspecialchars($content['coordinator_phone']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Coordinator Description</label>
            <textarea name="coordinator_description" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['coordinator_description']??'');?></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:15px;background:#f8fafc;padding:15px;border-radius:12px;border:1px dashed #cbd5e1;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Coordinator Image URL</label>
                <input type="text" name="coordinator_image" value="<?php echo htmlspecialchars($content['coordinator_image']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Upload Photo</label>
                <input type="file" name="coordinator_image_file" style="width:100%;padding:8px;border:2px solid #e2e8f0;border-radius:10px;background:#fff;"></div>
            </div>

            <!-- Legacy Fund CTA -->
            <h4 style="color:#7c3aed;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #ede9fe;padding-bottom:8px;"><i class="fas fa-hand-holding-heart"></i> Legacy Fund CTA</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">CTA Heading</label>
                <input type="text" name="cta_heading" value="<?php echo htmlspecialchars($content['cta_heading']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">CTA Subtitle</label>
                <input type="text" name="cta_subtitle" value="<?php echo htmlspecialchars($content['cta_subtitle']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>
            <div style="margin-top:15px;"><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">CTA Description</label>
            <textarea name="cta_description" rows="2" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($content['cta_description']??'');?></textarea></div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-top:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 1 Text</label>
                <input type="text" name="cta_button_text" value="<?php echo htmlspecialchars($content['cta_button_text']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 1 Link</label>
                <input type="text" name="cta_button_link" value="<?php echo htmlspecialchars($content['cta_button_link']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 2 Text</label>
                <input type="text" name="cta_button2_text" value="<?php echo htmlspecialchars($content['cta_button2_text']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Button 2 Link</label>
                <input type="text" name="cta_button2_link" value="<?php echo htmlspecialchars($content['cta_button2_link']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>

            <!-- Social Links -->
            <h4 style="color:#059669;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #ecfdf5;padding-bottom:8px;"><i class="fas fa-share-alt"></i> Social Media Links</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook</label>
                <input type="text" name="social_facebook" value="<?php echo htmlspecialchars($content['social_facebook']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;"><i class="fab fa-twitter" style="color:#1da1f2;"></i> Twitter/X</label>
                <input type="text" name="social_twitter" value="<?php echo htmlspecialchars($content['social_twitter']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;"><i class="fab fa-linkedin" style="color:#0077b5;"></i> LinkedIn</label>
                <input type="text" name="social_linkedin" value="<?php echo htmlspecialchars($content['social_linkedin']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;"><i class="fab fa-threads"></i> Threads</label>
                <input type="text" name="social_threads" value="<?php echo htmlspecialchars($content['social_threads']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;"><i class="fab fa-instagram" style="color:#e4405f;"></i> Instagram</label>
                <input type="text" name="social_instagram" value="<?php echo htmlspecialchars($content['social_instagram']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;"><i class="fab fa-youtube" style="color:#ff0000;"></i> YouTube</label>
                <input type="text" name="social_youtube" value="<?php echo htmlspecialchars($content['social_youtube']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>

            <!-- Contact -->
            <h4 style="color:#0891b2;font-weight:800;margin:30px 0 15px;border-bottom:2px solid #ecfeff;padding-bottom:8px;"><i class="fas fa-address-card"></i> Contact Section</h4>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Contact Heading</label>
                <input type="text" name="contact_heading" value="<?php echo htmlspecialchars($content['contact_heading']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Phone</label>
                <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($content['contact_phone']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Phone Note</label>
                <input type="text" name="contact_phone_note" value="<?php echo htmlspecialchars($content['contact_phone_note']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:15px;">
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Location</label>
                <textarea name="contact_location" rows="2" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"><?php echo htmlspecialchars($content['contact_location']??'');?></textarea></div>
                <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#334155;">Mailing Address</label>
                <textarea name="contact_address" rows="2" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"><?php echo htmlspecialchars($content['contact_address']??'');?></textarea></div>
            </div>

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
        <h3 style="margin:0 0 25px;font-size:1.5rem;font-weight:800;color:#1e293b;"><i class="fas fa-chart-bar" style="color:<?php echo $accent;?>;margin-right:10px;"></i>Mission Statistics</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
        <?php foreach ($stats as $stat):?>
            <form method="POST" style="background:#fafafa;border:1px solid #e2e8f0;padding:15px;border-radius:12px;">
                <input type="hidden" name="action" value="update_stat"><input type="hidden" name="stat_id" value="<?php echo $stat['id'];?>">
                <input type="text" name="stat_value" value="<?php echo htmlspecialchars($stat['stat_value']);?>" style="width:100%;border:none;border-bottom:2px solid #e2e8f0;background:transparent;font-size:1.3rem;font-weight:900;color:<?php echo $accent;?>;padding:4px;margin-bottom:8px;">
                <input type="text" name="stat_label" value="<?php echo htmlspecialchars($stat['stat_label']);?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.85rem;margin-bottom:8px;">
                <div style="display:flex;gap:6px;">
                    <input type="text" name="stat_color" value="<?php echo htmlspecialchars($stat['stat_color']??'');?>" style="flex:1;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;" placeholder="Color">
                    <input type="text" name="stat_bg" value="<?php echo htmlspecialchars($stat['stat_bg']??'');?>" style="flex:1;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;" placeholder="BG Class">
                    <button type="submit" style="background:<?php echo $accent;?>;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;"><i class="fas fa-save"></i></button>
                </div>
            </form>
        <?php endforeach;?>
        </div>
    </div>
    <?php endif;?>

    <!-- Sections & Items -->
    <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
        <h2 style="margin:0;font-size:1.8rem;font-weight:900;color:#1e293b;">Page Sections & Content</h2>
        <div style="color:#64748b;font-weight:600;">Total: <?php echo count($sections);?> sections</div>
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
                <button onclick="editSection(<?php echo $section['id'];?>,'<?php echo addslashes($section['section_title']);?>','<?php echo addslashes($section['section_subtitle']??'');?>')" style="padding:8px 16px;border-radius:10px;background:#fff;border:1px solid #e2e8f0;color:#475569;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="fas fa-edit"></i> Edit</button>
                <button onclick="addItem('<?php echo $section['section_key'];?>')" style="padding:8px 16px;border-radius:10px;background:#10b981;border:none;color:#fff;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="fas fa-plus"></i> Add</button>
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
                                    <label style="display:flex;align-items:center;gap:4px;font-size:.75rem;font-weight:700;color:#64748b;cursor:pointer;"><input type="checkbox" name="is_active" <?php echo $item['is_active']?'checked':'';?> style="width:14px;height:14px;">On</label>
                                    <button type="button" onclick="deleteItem(<?php echo $item['id'];?>)" style="background:#fee2e2;color:#ef4444;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;"><i class="fas fa-trash-alt" style="font-size:.7rem;"></i></button>
                                </div>
                            </div>
                            <textarea name="item_description" rows="2" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px;font-size:.85rem;margin-bottom:10px;"><?php echo htmlspecialchars($item['item_description']??'');?></textarea>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Icon</label>
                                <input type="text" name="item_icon" value="<?php echo htmlspecialchars($item['item_icon']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Color/Gradient</label>
                                <input type="text" name="item_color" value="<?php echo htmlspecialchars($item['item_color']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Link URL</label>
                                <input type="text" name="item_link" value="<?php echo htmlspecialchars($item['item_link']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Link Text</label>
                                <input type="text" name="item_link_text" value="<?php echo htmlspecialchars($item['item_link_text']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Link Color</label>
                                <input type="text" name="item_link_color" value="<?php echo htmlspecialchars($item['item_link_color']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">BG Class</label>
                                <input type="text" name="item_bg_class" value="<?php echo htmlspecialchars($item['item_bg_class']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
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
<div id="addSlideModal" class="admin-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:flex-start;justify-content:center;padding:40px 20px;overflow-y:auto;backdrop-filter:blur(5px);">
    <div style="background:#fff;width:90%;max-width:450px;padding:30px;border-radius:20px;box-shadow:0 25px 50px rgba(0,0,0,0.25);margin:auto;">
        <h3 style="margin-top:0;font-size:1.4rem;font-weight:900;color:#1e293b;"><i class="fas fa-image" style="color:<?php echo $accent;?>;"></i> Add Slider Image</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_slide">
            <div style="display:grid;gap:12px;">
                <input type="text" name="image_url" placeholder="Image URL" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                <input type="file" name="slide_image_file" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;">
                <input type="text" name="alt_text" placeholder="Alt text" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('addSlideModal').style.display='none'" style="padding:12px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:12px 30px;background:<?php echo $accent;?>;color:#fff;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Add Slide</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="addItemModal" class="admin-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:flex-start;justify-content:center;padding:40px 20px;overflow-y:auto;backdrop-filter:blur(5px);">
    <div style="background:#fff;width:90%;max-width:550px;padding:30px;border-radius:20px;box-shadow:0 25px 50px rgba(0,0,0,0.25);margin:auto;">
        <h3 style="margin-top:0;font-size:1.4rem;font-weight:900;color:#1e293b;"><i class="fas fa-plus-circle" style="color:<?php echo $accent;?>;"></i> Add New Item</h3>
        <p style="color:#64748b;">Section: <strong id="sectionDisplay"></strong></p>
        <form method="POST">
            <input type="hidden" name="action" value="add_item"><input type="hidden" name="section_key" id="addItemSectionKey">
            <div style="display:grid;gap:12px;">
                <input type="text" name="item_title" required placeholder="Title" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                <textarea name="item_description" rows="2" placeholder="Description" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></textarea>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <input type="text" name="item_icon" placeholder="Icon" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                    <input type="text" name="item_color" placeholder="Color" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                    <input type="text" name="item_link" placeholder="Link URL" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                    <input type="text" name="item_link_text" placeholder="Link Text" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
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
        <h3 style="margin-top:0;font-size:1.4rem;font-weight:900;color:#1e293b;"><i class="fas fa-layer-group" style="color:<?php echo $accent;?>;"></i> Edit Section</h3>
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
<form id="deleteSlideForm" method="POST" style="display:none;"><input type="hidden" name="action" value="delete_slide"><input type="hidden" name="slide_id" id="deleteSlideId"></form>

<script>
function deleteItem(id){if(confirm('Delete this item?')){document.getElementById('deleteItemId').value=id;document.getElementById('deleteItemForm').submit();}}
function deleteSlide(id){if(confirm('Delete this slide?')){document.getElementById('deleteSlideId').value=id;document.getElementById('deleteSlideForm').submit();}}
function addItem(sk){document.getElementById('addItemSectionKey').value=sk;document.getElementById('sectionDisplay').innerText=sk.replace(/_/g,' ').toUpperCase();document.getElementById('addItemModal').style.display='flex';}
function editSection(id,title,subtitle){document.getElementById('editSectionId').value=id;document.getElementById('editSectionTitle').value=title;document.getElementById('editSectionSubtitle').value=subtitle;document.getElementById('editSectionModal').style.display='flex';}
window.onclick=function(e){if(e.target.classList.contains('admin-modal'))e.target.style.display='none';}
</script>

<style>
input:focus,textarea:focus,select:focus{outline:none;border-color:<?php echo $accent;?> !important;box-shadow:0 0 0 4px <?php echo $accent;?>1a;}
@media(max-width:768px){div[style*="grid-template-columns"]{grid-template-columns:1fr !important;}}
</style>

<?php include 'footer.php';?>
