<?php
// Direct-access guard. This file is normally include()d by
// admin/manage_campus_life_pages.php, but it is also reachable at its own
// URL, where it would otherwise process POSTs and uploads with no login.
// The guard is idempotent, so it is harmless when included.
require_once __DIR__ . "/../../includes/admin_auth.php";
require_once __DIR__ . "/../../includes/upload_helper.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_radio'])) {
    $upload_dir = '../uploads/campus_life/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle hero image upload
    $hero_image = $_POST['hero_image'];
    if (isset($_FILES['hero_image_upload']) && $_FILES['hero_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['hero_image_upload'], 'campus_life/', 'radio_hero_');
        if ($uploaded !== null) {
            $hero_image = $uploaded;
        }
    }
    
    // Handle current show image upload
    $current_show_image = $_POST['current_show_image'];
    if (isset($_FILES['current_show_image_upload']) && $_FILES['current_show_image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['current_show_image_upload'], 'campus_life/', 'radio_show_');
        if ($uploaded !== null) {
            $current_show_image = $uploaded;
        }
    }

    // Handle About Images
    $about_images = [];
    for ($i = 1; $i <= 4; $i++) {
        $field_name = "about_image_$i";
        $upload_name = "about_image_{$i}_upload";
        $about_images[$i] = $_POST[$field_name];
        if (isset($_FILES[$upload_name]) && $_FILES[$upload_name]['error'] === UPLOAD_ERR_OK) {
            $uploaded = handleAdminFileUpload($_FILES[$upload_name], 'campus_life/', "radio_about_{$i}_");
            if ($uploaded !== null) {
                $about_images[$i] = $uploaded;
            }
        }
    }
    
    // Handle the printable schedule (PDF)
    $schedule_pdf = $_POST['schedule_pdf'];
    if (isset($_FILES['schedule_pdf_upload']) && $_FILES['schedule_pdf_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded = handleAdminFileUpload($_FILES['schedule_pdf_upload'], 'campus_life/', 'radio_schedule_');
        if ($uploaded !== null) {
            $schedule_pdf = $uploaded;
        }
    }

    // Only the three stream modes the public page knows how to render are
    // accepted; anything else would leave the player in an undefined state.
    $stream_type = in_array($_POST['stream_type'], ['none', 'audio', 'embed'], true)
        ? $_POST['stream_type']
        : 'none';

    // The save runs inside try/catch because PDO is in ERRMODE_EXCEPTION. An
    // uncaught PDOException here (e.g. a column the schema is missing) killed
    // the request mid-page, so with display_errors off in production the admin
    // saw a blank panel and no explanation — an upload appeared to "do nothing"
    // even though the file had already been written to uploads/.
    try {
        $stmt = $pdo->prepare("UPDATE radio_content SET 
            hero_title = ?, hero_subtitle = ?, hero_image = ?,
            live_on_air_text = ?, now_playing_heading = ?,
            current_show = ?, current_host = ?, current_show_image = ?,
            next_show_time = ?, frequency = ?,
            about_heading = ?, about_text = ?,
            about_image_1 = ?, about_image_2 = ?, about_image_3 = ?, about_image_4 = ?,
            programs_heading = ?, programs_text = ?, schedule_pdf = ?,
            cta_heading = ?, cta_text = ?, cta_phone = ?, cta_email = ?, whatsapp_number = ?,
            location_text = ?, status = ?,
            facebook_url = ?, twitter_url = ?, instagram_url = ?, youtube_url = ?, tiktok_url = ?,
            hero_cta_1_text = ?, hero_cta_1_link = ?,
            hero_cta_2_text = ?, hero_cta_2_link = ?,
            station_slogan = ?,
            stream_type = ?, stream_url = ?, stream_embed_code = ?, stream_offline_note = ?
            WHERE id = 1");
        
        $stmt->execute([
            $_POST['hero_title'], $_POST['hero_subtitle'], $hero_image,
            $_POST['live_on_air_text'], $_POST['now_playing_heading'],
            $_POST['current_show'], $_POST['current_host'], $current_show_image,
            $_POST['next_show_time'], $_POST['frequency'],
            $_POST['about_heading'], $_POST['about_text'],
            $about_images[1], $about_images[2], $about_images[3], $about_images[4],
            $_POST['programs_heading'], $_POST['programs_text'], $schedule_pdf,
            $_POST['cta_heading'], $_POST['cta_text'], $_POST['cta_phone'], $_POST['cta_email'], $_POST['whatsapp_number'],
            $_POST['location_text'], $_POST['status'],
            $_POST['facebook_url'], $_POST['twitter_url'], $_POST['instagram_url'], $_POST['youtube_url'], $_POST['tiktok_url'],
            $_POST['hero_cta_1_text'], $_POST['hero_cta_1_link'],
            $_POST['hero_cta_2_text'], $_POST['hero_cta_2_link'],
            $_POST['station_slogan'],
            $stream_type, $_POST['stream_url'], $_POST['stream_embed_code'], $_POST['stream_offline_note']
        ]);
        
        echo '<div class="alert alert-success">Radio content updated successfully!</div>';
    } catch (PDOException $e) {
        error_log('Radio save failed: ' . $e->getMessage());
        echo vvu_render_save_error($e);
    }

    // An upload that failed for its own reasons (too large, unwritable folder)
    // is reported here rather than only by admin/header.php, which has already
    // been rendered by the time this included file runs.
    if (function_exists('vvu_take_upload_error') && ($uploadError = vvu_take_upload_error())) {
        echo '<div class="alert alert-warning"><strong>The image was not uploaded.</strong> '
           . htmlspecialchars($uploadError)
           . '<br><small>Everything else on the form was saved. The previous image is still in place.</small></div>';
    }
}

$content = $pdo->query("SELECT * FROM radio_content WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    echo '<div class="alert alert-warning">Radio content record not found.</div>';
    return;
}
?>

<div class="dashboard-card">
    <div class="card-header">
        <h5><i class="fas fa-radio"></i> VVU Radio 97.7 FM Content</h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-image"></i> Hero Section</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Hero Title</label>
                    <input type="text" name="hero_title" class="form-control" 
                           value="<?php echo htmlspecialchars($content['hero_title']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hero Image</label>
                    <input type="text" name="hero_image" class="form-control mb-2" 
                           value="<?php echo htmlspecialchars($content['hero_image']); ?>">
                    <input type="file" name="hero_image_upload" class="form-control" accept="image/*">
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Hero Subtitle</label>
                    <textarea name="hero_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($content['hero_subtitle']); ?></textarea>
                </div>
                <div class="col-md-4 mt-3">
                    <label class="form-label">Live On Air Tag</label>
                    <input type="text" name="live_on_air_text" class="form-control" value="<?php echo htmlspecialchars($content['live_on_air_text']); ?>">
                </div>
                <!-- Hero Buttons -->
                <div class="col-md-6 mt-3">
                    <label class="form-label">Hero Button 1 Text</label>
                    <input type="text" name="hero_cta_1_text" class="form-control mb-1" value="<?php echo htmlspecialchars($content['hero_cta_1_text']); ?>">
                    <label class="form-label text-muted small">Link</label>
                    <input type="text" name="hero_cta_1_link" class="form-control" value="<?php echo htmlspecialchars($content['hero_cta_1_link']); ?>">
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Hero Button 2 Text</label>
                    <input type="text" name="hero_cta_2_text" class="form-control mb-1" value="<?php echo htmlspecialchars($content['hero_cta_2_text']); ?>">
                    <label class="form-label text-muted small">Link</label>
                    <input type="text" name="hero_cta_2_link" class="form-control" value="<?php echo htmlspecialchars($content['hero_cta_2_link']); ?>">
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-broadcast-tower"></i> Live Stream ("Listen Live")</h6>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info py-2 small mb-3">
                        This is what the <strong>Listen Live</strong> player on the public page connects to.
                        Leave it on <em>No online stream</em> until the station is actually streaming &mdash;
                        visitors then see the notice below instead of a dead play button.
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stream Mode</label>
                    <select name="stream_type" class="form-select" id="vvr-stream-type">
                        <?php
                        $stream_modes = [
                            "none"  => "No online stream yet",
                            "audio" => "Direct stream URL (Icecast / Shoutcast / HLS)",
                            "embed" => "Embedded player (Mixlr, Zeno.FM, Radio.co, YouTube Live)",
                        ];
                        $current_mode = $content["stream_type"] ?? "none";
                        foreach ($stream_modes as $mode_key => $mode_label): ?>
                            <option value="<?php echo $mode_key; ?>" <?php echo $current_mode === $mode_key ? "selected" : ""; ?>><?php echo $mode_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Stream URL <span class="text-muted small">(used by &ldquo;Direct stream URL&rdquo;)</span></label>
                    <input type="url" name="stream_url" class="form-control"
                           placeholder="https://stream.example.com:8000/vvr"
                           value="<?php echo htmlspecialchars($content["stream_url"] ?? ""); ?>">
                    <div class="form-text">Must be served over <strong>https</strong>, or browsers will block it on the https site.</div>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Embed Code <span class="text-muted small">(used by &ldquo;Embedded player&rdquo;)</span></label>
                    <textarea name="stream_embed_code" class="form-control font-monospace" rows="3"
                              placeholder="&lt;iframe src=&quot;https://zeno.fm/player/...&quot; width=&quot;100%&quot; height=&quot;250&quot;&gt;&lt;/iframe&gt;"><?php echo htmlspecialchars($content["stream_embed_code"] ?? ""); ?></textarea>
                    <div class="form-text">Paste the provider&rsquo;s iframe. Any &lt;script&gt; tag is stripped before the page renders it.</div>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Offline Notice</label>
                    <input type="text" name="stream_offline_note" class="form-control"
                           value="<?php echo htmlspecialchars($content["stream_offline_note"] ?? ""); ?>">
                    <div class="form-text">Shown in place of the player while the mode is &ldquo;No online stream yet&rdquo;.</div>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Station Slogan</label>
                    <input type="text" name="station_slogan" class="form-control"
                           value="<?php echo htmlspecialchars($content["station_slogan"] ?? ""); ?>">
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-play"></i> Player Labels</h6>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Now Playing Label</label>
                    <input type="text" name="now_playing_heading" class="form-control" value="<?php echo htmlspecialchars($content['now_playing_heading']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frequency</label>
                    <input type="text" name="frequency" class="form-control" value="<?php echo htmlspecialchars($content['frequency']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Next Show Time <span class="badge bg-secondary">unused</span></label>
                    <input type="text" name="next_show_time" class="form-control" value="<?php echo htmlspecialchars($content['next_show_time']); ?>">
                </div>
                <div class="col-12 mt-3">
                    <div class="alert alert-secondary py-2 small mb-0">
                        <i class="fas fa-clock me-1"></i>
                        <strong>&ldquo;On air now&rdquo; and &ldquo;Up next&rdquo; are worked out automatically</strong> from the weekly
                        schedule in <code>includes/radio_schedule_data.php</code>, using Ghana time. The three fields
                        below are kept only for reference and no longer appear on the page &mdash; edit the schedule file
                        to change what listeners see.
                    </div>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Current Show Title</label>
                    <input type="text" name="current_show" class="form-control" value="<?php echo htmlspecialchars($content['current_show']); ?>">
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Current Host(s)</label>
                    <input type="text" name="current_host" class="form-control" value="<?php echo htmlspecialchars($content['current_host']); ?>">
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Show Image</label>
                    <input type="text" name="current_show_image" class="form-control mb-2" value="<?php echo htmlspecialchars($content['current_show_image']); ?>">
                    <input type="file" name="current_show_image_upload" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> About Section & Features</h6>
                <a href="manage_campus_life_lists.php#tab-radio" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage About Features</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">About Heading</label>
                    <input type="text" name="about_heading" class="form-control mb-2" value="<?php echo htmlspecialchars($content['about_heading']); ?>">
                    <label class="form-label">About Text</label>
                    <textarea name="about_text" class="form-control mb-3" rows="4"><?php echo htmlspecialchars($content['about_text']); ?></textarea>
                </div>
                <div class="col-12"><label class="form-label fw-bold">About Section Images</label></div>
                <?php for($i=1; $i<=4; $i++): ?>
                <div class="col-md-3 mb-3">
                    <label class="form-label small">Image <?php echo $i; ?></label>
                    <input type="text" name="about_image_<?php echo $i; ?>" class="form-control mb-2 small" value="<?php echo htmlspecialchars($content['about_image_'.$i]); ?>">
                    <input type="file" name="about_image_<?php echo $i; ?>_upload" class="form-control form-control-sm" accept="image/*">
                </div>
                <?php endfor; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="mb-0"><i class="fas fa-list-ul"></i> Program Highlights</h6>
                <a href="manage_campus_life_lists.php#tab-radio" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Manage Program List</a>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">Programs Heading</label>
                    <input type="text" name="programs_heading" class="form-control mb-2" value="<?php echo htmlspecialchars($content['programs_heading']); ?>">
                    <label class="form-label">Programs Description Text</label>
                    <textarea name="programs_text" class="form-control mb-3" rows="2"><?php echo htmlspecialchars($content['programs_text']); ?></textarea>
                    <label class="form-label">Printable Schedule (PDF)</label>
                    <input type="text" name="schedule_pdf" class="form-control mb-2"
                           value="<?php echo htmlspecialchars($content['schedule_pdf'] ?? ''); ?>">
                    <input type="file" name="schedule_pdf_upload" class="form-control" accept="application/pdf">
                    <div class="form-text">Adds a &ldquo;Download PDF&rdquo; button to the schedule section. Leave blank to hide it.</div>
                </div>
            </div>

            <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-phone"></i> Contact & CTA</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">CTA Heading</label>
                    <input type="text" name="cta_heading" class="form-control" value="<?php echo htmlspecialchars($content['cta_heading']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="cta_phone" class="form-control" value="<?php echo htmlspecialchars($content['cta_phone']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="text" name="cta_email" class="form-control" value="<?php echo htmlspecialchars($content['cta_email']); ?>">
                </div>
                <div class="col-md-3 mt-3">
                    <label class="form-label"><i class="fab fa-whatsapp text-success"></i> WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="form-control"
                           placeholder="+233 30 701 1832"
                           value="<?php echo htmlspecialchars($content['whatsapp_number'] ?? ''); ?>">
                    <div class="form-text">Blank hides the WhatsApp button.</div>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">CTA Text</label>
                    <textarea name="cta_text" class="form-control" rows="2"><?php echo htmlspecialchars($content['cta_text']); ?></textarea>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">Location Address</label>
                    <input type="text" name="location_text" class="form-control" value="<?php echo htmlspecialchars($content['location_text']); ?>">
                </div>
                <!-- Social Links -->
                <div class="col-md-4 mt-3">
                    <label class="form-label"><i class="fab fa-facebook text-primary"></i> Facebook URL</label>
                    <input type="text" name="facebook_url" class="form-control" value="<?php echo htmlspecialchars($content['facebook_url']); ?>">
                </div>
                <div class="col-md-4 mt-3">
                    <label class="form-label"><i class="fab fa-twitter text-info"></i> Twitter URL</label>
                    <input type="text" name="twitter_url" class="form-control" value="<?php echo htmlspecialchars($content['twitter_url']); ?>">
                </div>
                <div class="col-md-4 mt-3">
                    <label class="form-label"><i class="fab fa-instagram text-danger"></i> Instagram URL</label>
                    <input type="text" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($content['instagram_url']); ?>">
                </div>
                <div class="col-md-4 mt-3">
                    <label class="form-label"><i class="fab fa-youtube text-danger"></i> YouTube URL</label>
                    <input type="text" name="youtube_url" class="form-control" value="<?php echo htmlspecialchars($content['youtube_url'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mt-3">
                    <label class="form-label"><i class="fab fa-tiktok"></i> TikTok URL</label>
                    <input type="text" name="tiktok_url" class="form-control"
                           placeholder="https://www.tiktok.com/@vvr97.7"
                           value="<?php echo htmlspecialchars($content['tiktok_url'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mt-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo $content['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $content['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="update_radio" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Radio Content
                </button>
                <a href="../vvu_radio.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-eye"></i> Preview Page
                </a>
            </div>
        </form>
    </div>
</div>
