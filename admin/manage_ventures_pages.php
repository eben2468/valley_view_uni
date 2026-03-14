<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/upload_helper.php';

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$page_title = "Manage Ventures & Services Pages";
$current_page = 'manage_ventures_pages.php';

$managed_pages = [
    'bakery_factory' => ['title' => 'Bakery Factory', 'icon' => 'fa-bread-slice', 'file' => 'bakery_factory_page.php', 'color' => '#d97706'],
    'water_factory' => ['title' => 'Water Factory', 'icon' => 'fa-tint', 'file' => 'water_factory.php', 'color' => '#0891b2'],
    'grocery' => ['title' => 'VVU Grocery', 'icon' => 'fa-shopping-basket', 'file' => 'grocery.php', 'color' => '#059669'],
    'post_office' => ['title' => 'Post Office', 'icon' => 'fa-envelope', 'file' => 'post_office.php', 'color' => '#2563eb'],
    'vvu_radio' => ['title' => 'VVU Radio', 'icon' => 'fa-broadcast-tower', 'file' => 'vvu_radio.php', 'color' => '#7c3aed']
];

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'update_page_content') {
            $page_key = $_POST['page_key'];
            if ($page_key === 'vvu_radio') {
                $hero_image = $_POST['hero_image'] ?? '';
                $up = handleAdminFileUpload($_FILES['hero_image_file'] ?? [], 'ventures');
                if ($up) $hero_image = $up;
                $stmt = $pdo->prepare("UPDATE radio_content SET hero_title=?, hero_subtitle=?, hero_image=?, about_heading=?, about_text=?, programs_heading=?, programs_text=?, cta_heading=?, cta_text=?, cta_phone=?, cta_email=?, location_text=?, frequency=?, current_show=?, current_host=?, next_show_time=? WHERE id=1");
                $stmt->execute([$_POST['hero_title']??'',$_POST['hero_subtitle']??'',$hero_image,$_POST['about_heading']??'',$_POST['about_text']??'',$_POST['programs_heading']??'',$_POST['programs_text']??'',$_POST['cta_heading']??'',$_POST['cta_text']??'',$_POST['contact_phone']??'',$_POST['contact_email']??'',$_POST['contact_location']??'',$_POST['extra_field_1']??'',$_POST['extra_field_2']??'',$_POST['extra_field_3']??'',$_POST['banner_image']??'']);
            } else {
                $hero_image = $_POST['hero_image'] ?? '';
                $up = handleAdminFileUpload($_FILES['hero_image_file'] ?? [], 'ventures');
                if ($up) $hero_image = $up;
                $about_image = $_POST['about_image'] ?? '';
                $up2 = handleAdminFileUpload($_FILES['about_image_file'] ?? [], 'ventures');
                if ($up2) $about_image = $up2;
                $stmt = $pdo->prepare("UPDATE ventures_pages_content SET hero_badge=?, hero_title=?, hero_subtitle=?, hero_description=?, hero_image=?, about_heading=?, about_text=?, about_image=?, banner_image=?, cta_heading=?, cta_subtitle=?, cta_text=?, cta_button_text=?, cta_button_link=?, contact_phone=?, contact_email=?, contact_location=?, contact_address=?, contact_whatsapp=?, contact_hours=?, extra_field_1=?, extra_field_2=?, extra_field_3=? WHERE page_key=?");
                $stmt->execute([$_POST['hero_badge']??'',$_POST['hero_title']??'',$_POST['hero_subtitle']??'',$_POST['hero_description']??'',$hero_image,$_POST['about_heading']??'',$_POST['about_text']??'',$about_image,$_POST['banner_image']??'',$_POST['cta_heading']??'',$_POST['cta_subtitle']??'',$_POST['cta_text']??'',$_POST['cta_button_text']??'',$_POST['cta_button_link']??'',$_POST['contact_phone']??'',$_POST['contact_email']??'',$_POST['contact_location']??'',$_POST['contact_address']??'',$_POST['contact_whatsapp']??'',$_POST['contact_hours']??'',$_POST['extra_field_1']??'',$_POST['extra_field_2']??'',$_POST['extra_field_3']??'',$page_key]);
            }
            $message = "Page content updated!"; $message_type = "success";
        } elseif ($action === 'update_section') {
            $stmt = $pdo->prepare("UPDATE ventures_pages_sections SET section_title=?, section_subtitle=?, section_description=? WHERE id=?");
            $stmt->execute([$_POST['section_title'],$_POST['section_subtitle']??'',$_POST['section_description']??'',$_POST['section_id']]);
            $message = "Section updated!"; $message_type = "success";
        } elseif ($action === 'update_item') {
            $img = $_POST['item_image'] ?? '';
            $up = handleAdminFileUpload($_FILES['item_image_file'] ?? [], 'ventures');
            if ($up) $img = $up;
            $stmt = $pdo->prepare("UPDATE ventures_pages_items SET item_title=?, item_subtitle=?, item_description=?, item_icon=?, item_color=?, item_image=?, item_link=?, item_stat_value=?, item_stat_label=?, is_active=? WHERE id=?");
            $stmt->execute([$_POST['item_title']??'',$_POST['item_subtitle']??'',$_POST['item_description']??'',$_POST['item_icon']??'',$_POST['item_color']??'blue-600',$img,$_POST['item_link']??'',$_POST['item_stat_value']??'',$_POST['item_stat_label']??'',isset($_POST['is_active'])?1:0,$_POST['item_id']]);
            $message = "Item updated!"; $message_type = "success";
        } elseif ($action === 'add_item') {
            $img = $_POST['item_image'] ?? '';
            $up = handleAdminFileUpload($_FILES['item_image_file'] ?? [], 'ventures');
            if ($up) $img = $up;
            $stmt = $pdo->prepare("INSERT INTO ventures_pages_items (page_key,section_key,item_title,item_subtitle,item_description,item_icon,item_color,item_image,item_link,item_stat_value,item_stat_label,display_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,(SELECT COALESCE(MAX(display_order),0)+1 FROM ventures_pages_items i2 WHERE i2.page_key=? AND i2.section_key=?))");
            $stmt->execute([$_POST['page_key'],$_POST['section_key'],$_POST['item_title']??'',$_POST['item_subtitle']??'',$_POST['item_description']??'',$_POST['item_icon']??'',$_POST['item_color']??'blue-600',$img,$_POST['item_link']??'',$_POST['item_stat_value']??'',$_POST['item_stat_label']??'',$_POST['page_key'],$_POST['section_key']]);
            $message = "Item added!"; $message_type = "success";
        } elseif ($action === 'delete_item') {
            $stmt = $pdo->prepare("DELETE FROM ventures_pages_items WHERE id=?");
            $stmt->execute([$_POST['item_id']]);
            $message = "Item deleted!"; $message_type = "success";
        } elseif ($action === 'update_stat') {
            $stmt = $pdo->prepare("UPDATE ventures_pages_stats SET stat_value=?, stat_label=?, stat_icon=? WHERE id=?");
            $stmt->execute([$_POST['stat_value'],$_POST['stat_label'],$_POST['stat_icon']??'',$_POST['stat_id']]);
            $message = "Stat updated!"; $message_type = "success";
        } elseif ($action === 'update_radio_program') {
            $stmt = $pdo->prepare("UPDATE radio_programs SET title=?, schedule=?, description=?, icon=?, border_color=?, icon_bg_color=?, status=? WHERE id=?");
            $stmt->execute([$_POST['prog_title'],$_POST['prog_schedule']??'',$_POST['prog_description']??'',$_POST['prog_icon']??'radio',$_POST['prog_border']??'purple-600',$_POST['prog_bg']??'purple-600',$_POST['prog_status']??'active',$_POST['prog_id']]);
            $message = "Program updated!"; $message_type = "success";
        } elseif ($action === 'update_radio_feature') {
            $stmt = $pdo->prepare("UPDATE radio_features SET title=?, icon=?, color_class=? WHERE id=?");
            $stmt->execute([$_POST['feat_title'],$_POST['feat_icon']??'school',$_POST['feat_color']??'purple',$_POST['feat_id']]);
            $message = "Feature updated!"; $message_type = "success";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage(); $message_type = "error";
    }
}

$current_page_key = $_GET['page'] ?? 'bakery_factory';
if (!isset($managed_pages[$current_page_key])) $current_page_key = 'bakery_factory';

// Fetch data
try {
    if ($current_page_key === 'vvu_radio') {
        $stmt = $pdo->query("SELECT * FROM radio_content WHERE id = 1");
        $pc = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $page_content = [
            'page_key'=>'vvu_radio','hero_badge'=>'VVU Radio','hero_title'=>$pc['hero_title']??'','hero_subtitle'=>$pc['hero_subtitle']??'',
            'hero_description'=>'','hero_image'=>$pc['hero_image']??'','about_heading'=>$pc['about_heading']??'','about_text'=>$pc['about_text']??'',
            'about_image'=>'','banner_image'=>$pc['next_show_time']??'','cta_heading'=>$pc['cta_heading']??'','cta_subtitle'=>'',
            'cta_text'=>$pc['cta_text']??'','contact_phone'=>$pc['cta_phone']??'','contact_email'=>$pc['cta_email']??'',
            'contact_location'=>$pc['location_text']??'','contact_hours'=>'','contact_whatsapp'=>'','contact_address'=>'',
            'cta_button_text'=>'','cta_button_link'=>'','extra_field_1'=>$pc['frequency']??'97.7 FM',
            'extra_field_2'=>$pc['current_show']??'','extra_field_3'=>$pc['current_host']??'',
            'programs_heading'=>$pc['programs_heading']??'','programs_text'=>$pc['programs_text']??''
        ];
        $page_sections = [];
        $page_items = [];
        $radio_programs = $pdo->query("SELECT * FROM radio_programs ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
        $radio_features = $pdo->query("SELECT * FROM radio_features ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM ventures_pages_content WHERE page_key=?");
        $stmt->execute([$current_page_key]);
        $page_content = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $stmt = $pdo->prepare("SELECT * FROM ventures_pages_sections WHERE page_key=? ORDER BY display_order");
        $stmt->execute([$current_page_key]);
        $page_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare("SELECT * FROM ventures_pages_items WHERE page_key=? ORDER BY section_key, display_order");
        $stmt->execute([$current_page_key]);
        $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $page_items = [];
        foreach ($all_items as $item) $page_items[$item['section_key']][] = $item;
        $radio_programs = [];
        $radio_features = [];
    }
    $stmt = $pdo->prepare("SELECT * FROM ventures_pages_stats WHERE page_key=? ORDER BY display_order");
    $stmt->execute([$current_page_key]);
    $page_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "DB Error: " . $e->getMessage(); $message_type = "error";
    $page_content = []; $page_sections = []; $page_items = []; $page_stats = [];
    $radio_programs = []; $radio_features = [];
}

$info = $managed_pages[$current_page_key];
include 'header.php';
include 'sidebar.php';
?>

<main class="main-content">
<div class="content-wrapper">
    <div class="page-header"><div class="page-header-content">
        <h1><i class="fas fa-store"></i> Ventures & Services CMS</h1>
        <p class="page-description">Manage Bakery, Water Factory, Grocery, Post Office & Radio pages.</p>
    </div></div>

    <?php if ($message): ?>
    <div style="margin-bottom:25px;border-radius:12px;padding:15px;display:flex;align-items:center;gap:10px;background:<?php echo $message_type==='success'?'#ecfdf5':'#fef2f2';?>;border:1px solid <?php echo $message_type==='success'?'#10b981':'#ef4444';?>;color:<?php echo $message_type==='success'?'#065f46':'#991b1b';?>;">
        <i class="fas fa-<?php echo $message_type==='success'?'check-circle':'exclamation-circle';?>"></i>
        <div><strong><?php echo $message_type==='success'?'Success!':'Error:';?></strong> <?php echo htmlspecialchars($message);?></div>
    </div>
    <?php endif;?>

    <!-- Page Tabs -->
    <div style="background:#fff;padding:10px;border-radius:16px;margin-bottom:30px;box-shadow:0 4px 15px rgba(0,0,0,0.05);display:flex;gap:8px;overflow-x:auto;">
        <?php foreach ($managed_pages as $key => $pg):?>
        <a href="?page=<?php echo $key;?>" style="display:flex;align-items:center;gap:10px;padding:12px 20px;border-radius:12px;text-decoration:none;font-weight:700;white-space:nowrap;transition:all .3s;
            background:<?php echo $current_page_key===$key?"linear-gradient(135deg,{$pg['color']}dd,{$pg['color']})":"transparent";?>;
            color:<?php echo $current_page_key===$key?'#fff':'#64748b';?>;
            box-shadow:<?php echo $current_page_key===$key?"0 8px 20px {$pg['color']}44":'none';?>;">
            <i class="fas <?php echo $pg['icon'];?>"></i><?php echo $pg['title'];?>
        </a>
        <?php endforeach;?>
    </div>

    <!-- Page Preview Header -->
    <div style="background:linear-gradient(135deg,<?php echo $info['color'];?>cc,<?php echo $info['color'];?>);padding:30px;border-radius:20px;color:#fff;margin-bottom:35px;box-shadow:0 10px 25px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        <div style="position:absolute;right:-20px;top:-20px;font-size:12rem;opacity:0.08;"><i class="fas <?php echo $info['icon'];?>"></i></div>
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <span style="background:rgba(255,255,255,0.2);padding:5px 15px;border-radius:20px;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;">Editing</span>
                <h2 style="margin:10px 0 5px;font-size:2rem;font-weight:900;"><?php echo $info['title'];?></h2>
            </div>
            <a href="../<?php echo $info['file'];?>" target="_blank" style="padding:12px 25px;background:#fff;color:#1e293b;border-radius:12px;text-decoration:none;font-weight:800;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-external-link-alt"></i> Live View
            </a>
        </div>
    </div>

    <!-- Hero Content Form -->
    <div style="background:#fff;border-radius:20px;padding:35px;margin-bottom:40px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:15px;margin-bottom:30px;">
            <div style="width:50px;height:50px;background:<?php echo $info['color'];?>22;color:<?php echo $info['color'];?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="fas fa-pager"></i></div>
            <div><h3 style="margin:0;font-size:1.5rem;font-weight:800;color:#1e293b;">Hero & Page Content</h3>
            <p style="margin:0;color:#64748b;">Main branding, about section, CTA, and contact info.</p></div>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_page_content">
            <input type="hidden" name="page_key" value="<?php echo $current_page_key;?>">

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
                <?php if ($current_page_key !== 'vvu_radio'):?>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Badge</label>
                <input type="text" name="hero_badge" value="<?php echo htmlspecialchars($page_content['hero_badge']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <?php endif;?>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Title</label>
                <input type="text" name="hero_title" value="<?php echo htmlspecialchars($page_content['hero_title']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Subtitle</label>
                <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($page_content['hero_subtitle']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
            </div>

            <?php if ($current_page_key !== 'vvu_radio'):?>
            <div style="margin-top:20px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Description</label>
            <textarea name="hero_description" rows="3" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($page_content['hero_description']??'');?></textarea></div>
            <?php endif;?>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:20px;background:#f8fafc;padding:20px;border-radius:14px;border:1px dashed #cbd5e1;">
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Hero Image URL</label>
                <input type="text" name="hero_image" value="<?php echo htmlspecialchars($page_content['hero_image']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Upload Hero Image</label>
                <input type="file" name="hero_image_file" style="width:100%;padding:8px;background:#fff;border-radius:10px;border:2px solid #e2e8f0;"></div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:20px;">
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">About Heading</label>
                <input type="text" name="about_heading" value="<?php echo htmlspecialchars($page_content['about_heading']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <?php if ($current_page_key !== 'vvu_radio'):?>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Banner Image URL</label>
                <input type="text" name="banner_image" value="<?php echo htmlspecialchars($page_content['banner_image']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <?php else:?>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Next Show Time</label>
                <input type="text" name="banner_image" value="<?php echo htmlspecialchars($page_content['banner_image']??'');?>" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <?php endif;?>
            </div>

            <div style="margin-top:20px;"><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">About Text</label>
            <textarea name="about_text" rows="4" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;resize:vertical;"><?php echo htmlspecialchars($page_content['about_text']??'');?></textarea></div>

            <?php if ($current_page_key !== 'vvu_radio'):?>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:20px;background:#f8fafc;padding:20px;border-radius:14px;border:1px dashed #cbd5e1;">
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">About Image URL</label>
                <input type="text" name="about_image" value="<?php echo htmlspecialchars($page_content['about_image']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:10px;"></div>
                <div><label style="display:block;margin-bottom:8px;font-weight:700;color:#334155;">Upload About Image</label>
                <input type="file" name="about_image_file" style="width:100%;padding:8px;background:#fff;border-radius:10px;border:2px solid #e2e8f0;"></div>
            </div>
            <?php endif;?>

            <!-- CTA Section -->
            <div style="margin-top:25px;background:#eff6ff;padding:20px;border-radius:14px;border:1px solid #dbeafe;">
                <h4 style="margin:0 0 15px;color:#1e40af;font-weight:800;">Call to Action & Contact</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">CTA Heading</label>
                    <input type="text" name="cta_heading" value="<?php echo htmlspecialchars($page_content['cta_heading']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">CTA Subtitle</label>
                    <input type="text" name="cta_subtitle" value="<?php echo htmlspecialchars($page_content['cta_subtitle']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">CTA Text</label>
                    <input type="text" name="cta_text" value="<?php echo htmlspecialchars($page_content['cta_text']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">Phone</label>
                    <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($page_content['contact_phone']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">Email</label>
                    <input type="text" name="contact_email" value="<?php echo htmlspecialchars($page_content['contact_email']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">Location</label>
                    <input type="text" name="contact_location" value="<?php echo htmlspecialchars($page_content['contact_location']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <?php if ($current_page_key !== 'vvu_radio'):?>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">Address</label>
                    <input type="text" name="contact_address" value="<?php echo htmlspecialchars($page_content['contact_address']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">WhatsApp</label>
                    <input type="text" name="contact_whatsapp" value="<?php echo htmlspecialchars($page_content['contact_whatsapp']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#1e40af;font-size:.9rem;">Hours</label>
                    <input type="text" name="contact_hours" value="<?php echo htmlspecialchars($page_content['contact_hours']??'');?>" style="width:100%;padding:10px;border:1px solid #bfdbfe;border-radius:10px;"></div>
                    <?php endif;?>
                </div>
            </div>

            <?php if ($current_page_key === 'vvu_radio'):?>
            <div style="margin-top:25px;background:#faf5ff;padding:20px;border-radius:14px;border:1px solid #e9d5ff;">
                <h4 style="margin:0 0 15px;color:#7c3aed;font-weight:800;">Radio-Specific Fields</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#7c3aed;font-size:.9rem;">Frequency</label>
                    <input type="text" name="extra_field_1" value="<?php echo htmlspecialchars($page_content['extra_field_1']??'');?>" style="width:100%;padding:10px;border:1px solid #e9d5ff;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#7c3aed;font-size:.9rem;">Current Show</label>
                    <input type="text" name="extra_field_2" value="<?php echo htmlspecialchars($page_content['extra_field_2']??'');?>" style="width:100%;padding:10px;border:1px solid #e9d5ff;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#7c3aed;font-size:.9rem;">Current Host</label>
                    <input type="text" name="extra_field_3" value="<?php echo htmlspecialchars($page_content['extra_field_3']??'');?>" style="width:100%;padding:10px;border:1px solid #e9d5ff;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#7c3aed;font-size:.9rem;">Programs Heading</label>
                    <input type="text" name="programs_heading" value="<?php echo htmlspecialchars($page_content['programs_heading']??'');?>" style="width:100%;padding:10px;border:1px solid #e9d5ff;border-radius:10px;"></div>
                    <div><label style="display:block;margin-bottom:6px;font-weight:700;color:#7c3aed;font-size:.9rem;">Programs Text</label>
                    <input type="text" name="programs_text" value="<?php echo htmlspecialchars($page_content['programs_text']??'');?>" style="width:100%;padding:10px;border:1px solid #e9d5ff;border-radius:10px;"></div>
                </div>
            </div>
            <?php endif;?>

            <div style="margin-top:25px;text-align:right;">
                <button type="submit" style="padding:14px 35px;background:<?php echo $info['color'];?>;color:#fff;border:none;border-radius:14px;font-size:1.05rem;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:10px;box-shadow:0 10px 20px <?php echo $info['color'];?>33;">
                    <i class="fas fa-save"></i> Save Page Content
                </button>
            </div>
        </form>
    </div>

    <?php if ($current_page_key === 'vvu_radio' && !empty($radio_programs)):?>
    <!-- Radio Programs -->
    <div style="background:#fff;border-radius:20px;padding:35px;margin-bottom:40px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <h3 style="margin:0 0 25px;font-size:1.5rem;font-weight:800;color:#1e293b;"><i class="fas fa-podcast" style="color:#7c3aed;margin-right:10px;"></i>Radio Programs</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:20px;">
        <?php foreach ($radio_programs as $prog):?>
            <form method="POST" style="background:#faf5ff;border:1px solid #e9d5ff;padding:20px;border-radius:16px;">
                <input type="hidden" name="action" value="update_radio_program"><input type="hidden" name="prog_id" value="<?php echo $prog['id'];?>">
                <div style="display:grid;gap:12px;">
                    <input type="text" name="prog_title" value="<?php echo htmlspecialchars($prog['title']);?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:8px;font-weight:700;" placeholder="Title">
                    <input type="text" name="prog_schedule" value="<?php echo htmlspecialchars($prog['schedule']??'');?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:8px;" placeholder="Schedule">
                    <textarea name="prog_description" rows="2" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:8px;"><?php echo htmlspecialchars($prog['description']??'');?></textarea>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                        <input type="text" name="prog_icon" value="<?php echo htmlspecialchars($prog['icon']??'');?>" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px;" placeholder="Icon">
                        <input type="text" name="prog_border" value="<?php echo htmlspecialchars($prog['border_color']??'');?>" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px;" placeholder="Border color">
                        <select name="prog_status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px;">
                            <option value="active" <?php echo ($prog['status']??'')==='active'?'selected':'';?>>Active</option>
                            <option value="inactive" <?php echo ($prog['status']??'')==='inactive'?'selected':'';?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" style="padding:10px;background:#7c3aed;color:#fff;border:none;border-radius:10px;font-weight:700;cursor:pointer;"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        <?php endforeach;?>
        </div>
    </div>

    <!-- Radio Features -->
    <div style="background:#fff;border-radius:20px;padding:35px;margin-bottom:40px;box-shadow:0 4px 25px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <h3 style="margin:0 0 25px;font-size:1.5rem;font-weight:800;color:#1e293b;"><i class="fas fa-star" style="color:#7c3aed;margin-right:10px;"></i>Radio Features</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">
        <?php foreach ($radio_features as $feat):?>
            <form method="POST" style="background:#faf5ff;border:1px solid #e9d5ff;padding:20px;border-radius:16px;">
                <input type="hidden" name="action" value="update_radio_feature"><input type="hidden" name="feat_id" value="<?php echo $feat['id'];?>">
                <div style="display:grid;gap:12px;">
                    <input type="text" name="feat_title" value="<?php echo htmlspecialchars($feat['title']);?>" style="width:100%;padding:10px;border:2px solid #e2e8f0;border-radius:8px;font-weight:700;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <input type="text" name="feat_icon" value="<?php echo htmlspecialchars($feat['icon']??'');?>" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px;" placeholder="Icon">
                        <input type="text" name="feat_color" value="<?php echo htmlspecialchars($feat['color_class']??'');?>" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px;" placeholder="Color">
                    </div>
                    <button type="submit" style="padding:10px;background:#7c3aed;color:#fff;border:none;border-radius:10px;font-weight:700;cursor:pointer;"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        <?php endforeach;?>
        </div>
    </div>
    <?php endif;?>

    <?php if ($current_page_key !== 'vvu_radio'):?>
    <!-- Sections & Items -->
    <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
        <h2 style="margin:0;font-size:1.8rem;font-weight:900;color:#1e293b;">Page Sections & Content</h2>
        <div style="color:#64748b;font-weight:600;">Total: <?php echo count($page_sections);?> sections</div>
    </div>

    <?php if (empty($page_sections)):?>
        <div style="background:#f8fafc;border:2px dashed #e2e8f0;padding:60px;border-radius:20px;text-align:center;">
            <i class="fas fa-folder-open" style="font-size:4rem;color:#cbd5e1;margin-bottom:20px;"></i>
            <h4 style="color:#64748b;">No sections found for this page.</h4>
        </div>
    <?php else:?>
        <?php foreach ($page_sections as $section):?>
        <div style="background:#fff;border-radius:20px;margin-bottom:35px;box-shadow:0 4px 20px rgba(0,0,0,0.03);border:1px solid #f1f5f9;overflow:hidden;">
            <div style="background:#fafafa;padding:20px 30px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">
                        <span style="background:<?php echo $info['color'];?>;color:#fff;padding:3px 10px;border-radius:6px;font-size:.7rem;font-weight:900;text-transform:uppercase;"><?php echo $section['section_key'];?></span>
                        <h3 style="margin:0;font-size:1.2rem;font-weight:800;color:#1e293b;"><?php echo htmlspecialchars($section['section_title']);?></h3>
                    </div>
                    <p style="margin:0;color:#64748b;font-size:.9rem;font-style:italic;"><?php echo htmlspecialchars($section['section_subtitle']??'');?></p>
                </div>
                <div style="display:flex;gap:8px;">
                    <button onclick="editSection(<?php echo $section['id'];?>,'<?php echo addslashes($section['section_title']);?>','<?php echo addslashes($section['section_subtitle']??'');?>')" style="padding:8px 16px;border-radius:10px;background:#fff;border:1px solid #e2e8f0;color:#475569;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="fas fa-edit"></i> Edit</button>
                    <button onclick="addItem('<?php echo $current_page_key;?>','<?php echo $section['section_key'];?>')" style="padding:8px 16px;border-radius:10px;background:#10b981;border:none;color:#fff;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="fas fa-plus"></i> Add</button>
                </div>
            </div>
            <div style="padding:25px;">
                <?php if (empty($page_items[$section['section_key']])):?>
                    <div style="text-align:center;padding:30px;color:#94a3b8;">Empty section</div>
                <?php else:?>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:15px;">
                    <?php foreach ($page_items[$section['section_key']] as $item):?>
                        <div style="background:<?php echo $item['is_active']?'#fff':'#f8fafc';?>;border:1px solid #e2e8f0;padding:20px;border-radius:14px;opacity:<?php echo $item['is_active']?'1':'0.7';?>;">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_item"><input type="hidden" name="item_id" value="<?php echo $item['id'];?>">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                    <input type="text" name="item_title" value="<?php echo htmlspecialchars($item['item_title']);?>" style="font-weight:800;font-size:1rem;color:#1e293b;border:none;border-bottom:2px solid #f1f5f9;padding:4px;width:70%;background:transparent;">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <label style="display:flex;align-items:center;gap:4px;font-size:.75rem;font-weight:700;color:#64748b;cursor:pointer;"><input type="checkbox" name="is_active" <?php echo $item['is_active']?'checked':'';?> style="width:14px;height:14px;">On</label>
                                        <button type="button" onclick="deleteItem(<?php echo $item['id'];?>)" style="background:#fee2e2;color:#ef4444;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;"><i class="fas fa-trash-alt" style="font-size:.7rem;"></i></button>
                                    </div>
                                </div>
                                <textarea name="item_description" rows="2" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px;font-size:.85rem;margin-bottom:10px;"><?php echo htmlspecialchars($item['item_description']??'');?></textarea>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                                    <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Subtitle</label>
                                    <input type="text" name="item_subtitle" value="<?php echo htmlspecialchars($item['item_subtitle']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                    <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Badge/Value</label>
                                    <input type="text" name="item_stat_value" value="<?php echo htmlspecialchars($item['item_stat_value']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                    <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Icon</label>
                                    <input type="text" name="item_icon" value="<?php echo htmlspecialchars($item['item_icon']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                    <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Color</label>
                                    <input type="text" name="item_color" value="<?php echo htmlspecialchars($item['item_color']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                                    <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Link</label>
                                    <input type="text" name="item_link" value="<?php echo htmlspecialchars($item['item_link']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                    <div><label style="font-size:.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Stat Label</label>
                                    <input type="text" name="item_stat_label" value="<?php echo htmlspecialchars($item['item_stat_label']??'');?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;"></div>
                                </div>
                                <div style="background:#fafafa;padding:8px;border-radius:8px;margin-bottom:10px;">
                                    <div style="display:flex;gap:8px;"><input type="text" name="item_image" value="<?php echo htmlspecialchars($item['item_image']??'');?>" style="flex-grow:1;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.75rem;" placeholder="Image URL">
                                    <input type="file" name="item_image_file" style="width:90px;font-size:.65rem;"></div>
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
    <?php endif;?>

    <!-- Stats -->
    <?php if (!empty($page_stats)):?>
    <div style="background:#fff;border-radius:20px;padding:35px;border:1px solid #f1f5f9;box-shadow:0 4px 20px rgba(0,0,0,0.03);margin-bottom:40px;">
        <h3 style="margin:0 0 25px;font-size:1.5rem;font-weight:800;color:#1e293b;"><i class="fas fa-chart-bar" style="color:<?php echo $info['color'];?>;margin-right:10px;"></i>Statistics / Counters</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">
        <?php foreach ($page_stats as $stat):?>
            <form method="POST" style="background:#fafafa;border:1px solid #e2e8f0;padding:15px;border-radius:12px;">
                <input type="hidden" name="action" value="update_stat"><input type="hidden" name="stat_id" value="<?php echo $stat['id'];?>">
                <input type="text" name="stat_value" value="<?php echo htmlspecialchars($stat['stat_value']);?>" style="width:100%;border:none;border-bottom:2px solid #e2e8f0;background:transparent;font-size:1.3rem;font-weight:900;color:<?php echo $info['color'];?>;padding:4px;margin-bottom:8px;">
                <input type="text" name="stat_label" value="<?php echo htmlspecialchars($stat['stat_label']);?>" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.85rem;margin-bottom:8px;">
                <div style="display:flex;gap:6px;"><input type="text" name="stat_icon" value="<?php echo htmlspecialchars($stat['stat_icon']??'');?>" style="flex-grow:1;border:1px solid #e2e8f0;border-radius:6px;padding:6px;font-size:.8rem;" placeholder="Icon">
                <button type="submit" style="background:<?php echo $info['color'];?>;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;"><i class="fas fa-save"></i></button></div>
            </form>
        <?php endforeach;?>
        </div>
    </div>
    <?php endif;?>
    <?php endif;?>

</div>
</main>

<!-- Modals -->
<div id="addItemModal" class="admin-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:flex-start;justify-content:center;padding:40px 20px;overflow-y:auto;backdrop-filter:blur(5px);">
    <div style="background:#fff;width:90%;max-width:550px;padding:30px;border-radius:20px;box-shadow:0 25px 50px rgba(0,0,0,0.25);margin:auto;">
        <h3 style="margin-top:0;font-size:1.4rem;font-weight:900;color:#1e293b;"><i class="fas fa-plus-circle" style="color:<?php echo $info['color'];?>;"></i> Add New Entry</h3>
        <p style="color:#64748b;">Section: <strong id="sectionDisplay"></strong></p>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_item"><input type="hidden" name="page_key" id="addItemPageKey"><input type="hidden" name="section_key" id="addItemSectionKey">
            <div style="display:grid;gap:12px;">
                <input type="text" name="item_title" required placeholder="Title" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                <textarea name="item_description" rows="2" placeholder="Description" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;"></textarea>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <input type="text" name="item_subtitle" placeholder="Subtitle" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                    <input type="text" name="item_stat_value" placeholder="Badge value" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                    <input type="text" name="item_icon" placeholder="Icon" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                    <input type="text" name="item_color" placeholder="Color (e.g. blue-600)" style="padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                </div>
                <div style="display:flex;gap:8px;"><input type="text" name="item_image" placeholder="Image URL" style="flex-grow:1;padding:10px;border:2px solid #e2e8f0;border-radius:8px;">
                <input type="file" name="item_image_file" style="width:120px;font-size:.7rem;"></div>
                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('addItemModal').style.display='none'" style="padding:12px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:12px 30px;background:<?php echo $info['color'];?>;color:#fff;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="editSectionModal" class="admin-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:flex-start;justify-content:center;padding:40px 20px;overflow-y:auto;backdrop-filter:blur(5px);">
    <div style="background:#fff;width:90%;max-width:450px;padding:30px;border-radius:20px;box-shadow:0 25px 50px rgba(0,0,0,0.25);margin:auto;">
        <h3 style="margin-top:0;font-size:1.4rem;font-weight:900;color:#1e293b;"><i class="fas fa-layer-group" style="color:<?php echo $info['color'];?>;"></i> Edit Section</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_section"><input type="hidden" name="section_id" id="editSectionId">
            <div style="display:grid;gap:12px;">
                <input type="text" name="section_title" id="editSectionTitle" required style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;">
                <input type="text" name="section_subtitle" id="editSectionSubtitle" style="width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:10px;" placeholder="Subtitle">
                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('editSectionModal').style.display='none'" style="padding:12px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:12px 30px;background:<?php echo $info['color'];?>;color:#fff;border:none;border-radius:10px;font-weight:800;cursor:pointer;">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form id="deleteItemForm" method="POST" style="display:none;"><input type="hidden" name="action" value="delete_item"><input type="hidden" name="item_id" id="deleteItemId"></form>

<script>
function deleteItem(id){if(confirm('Delete this item?')){document.getElementById('deleteItemId').value=id;document.getElementById('deleteItemForm').submit();}}
function addItem(pk,sk){document.getElementById('addItemPageKey').value=pk;document.getElementById('addItemSectionKey').value=sk;document.getElementById('sectionDisplay').innerText=sk.replace(/_/g,' ').toUpperCase();document.getElementById('addItemModal').style.display='flex';}
function editSection(id,title,subtitle){document.getElementById('editSectionId').value=id;document.getElementById('editSectionTitle').value=title;document.getElementById('editSectionSubtitle').value=subtitle;document.getElementById('editSectionModal').style.display='flex';}
window.onclick=function(e){if(e.target.classList.contains('admin-modal'))e.target.style.display='none';}
document.querySelectorAll('.item-card').forEach(c=>{c.addEventListener('mouseenter',()=>{c.style.transform='translateY(-4px)';c.style.boxShadow='0 8px 25px rgba(0,0,0,0.06)';c.style.transition='all .3s';});c.addEventListener('mouseleave',()=>{c.style.transform='';c.style.boxShadow='';});});
</script>

<style>
.page-tabs-container::-webkit-scrollbar{height:4px;}
.page-tabs-container::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:10px;}
input:focus,textarea:focus,select:focus{outline:none;border-color:<?php echo $info['color'];?> !important;box-shadow:0 0 0 4px <?php echo $info['color'];?>1a;}
@media(max-width:768px){
    div[style*="grid-template-columns"]{grid-template-columns:1fr !important;}
}
</style>

<?php include 'footer.php';?>
