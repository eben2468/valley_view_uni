<?php
require_once('../includes/db_connect.php');
require_once('../includes/slider_settings.php');
require_once('../includes/slider_buttons.php');
require_once __DIR__ . '/../includes/admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

vvu_slider_install($pdo);
vvu_slide_buttons_install($pdo);
$slider_timing = vvu_slider_settings($pdo);

$button_positions = vvu_slide_button_positions();
$button_options   = vvu_slide_button_option_sets();
$button_defaults  = vvu_slide_button_defaults();

$action = $_GET['action'] ?? 'edit';
$id = $_GET['id'] ?? null;

// Handle delete
if (isset($_GET['delete']) && $_GET['delete']) {
    try {
        $stmt = $pdo->prepare("DELETE FROM homepage_sliders WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: manage_homepage_content.php?tab=sliders&deleted=1");
        exit();
    } catch (Exception $e) {
        $error = "Error deleting: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'] ?? '';
        $highlight_text = $_POST['highlight_text'] ?? '';
        $description = $_POST['description'] ?? '';
        $button1_text = $_POST['button1_text'] ?? '';
        $button1_link = $_POST['button1_link'] ?? '';
        $button2_text = $_POST['button2_text'] ?? '';
        $button2_link = $_POST['button2_link'] ?? '';
        $button3_text = $_POST['button3_text'] ?? '';
        $button3_link = $_POST['button3_link'] ?? '';
        $content_position = $_POST['content_position'] ?? 'middle-center';
        $display_order = $_POST['display_order'];

        // Button placement and styling. Everything is validated against the
        // lists in includes/slider_buttons.php, so only known values are ever
        // stored and the front end never has to second-guess the database.
        $pick = function ($key, array $allowed, $fallback) {
            $value = strtolower(trim((string)($_POST[$key] ?? '')));
            return isset($allowed[$value]) ? $value : $fallback;
        };
        $hex_or_null = function ($key) {
            $value = trim((string)($_POST[$key] ?? ''));
            return preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value) ? strtolower($value) : null;
        };

        $button_position = $pick('button_position', $button_positions, 'inherit');
        $button_x        = vvu_slide_button_clamp_percent($_POST['button_x'] ?? null, 50);
        $button_y        = vvu_slide_button_clamp_percent($_POST['button_y'] ?? null, 80);
        $button_layout   = $pick('button_layout', $button_options['layout'], 'row');
        $button_size     = $pick('button_size', $button_options['size'], 'medium');
        $button_shape    = $pick('button_shape', $button_options['shape'], 'pill');
        $button_mobile      = $pick('button_mobile', $button_options['mobile'], 'same');
        $button_mobile_x    = vvu_slide_button_clamp_percent($_POST['button_mobile_x'] ?? null, 50);
        $button_mobile_y    = vvu_slide_button_clamp_percent($_POST['button_mobile_y'] ?? null, 85);
        $button_mobile_size  = $pick('button_mobile_size', $button_options['mobile_size'], 'inherit');
        $button_mobile_scale = vvu_slide_button_scale($_POST['button_mobile_scale'] ?? null);

        $button1_style = $pick('button1_style', $button_options['style'], $button_defaults[1]['style']);
        $button2_style = $pick('button2_style', $button_options['style'], $button_defaults[2]['style']);
        $button3_style = $pick('button3_style', $button_options['style'], $button_defaults[3]['style']);
        $button1_bg    = $hex_or_null('button1_bg');
        $button2_bg    = $hex_or_null('button2_bg');
        $button3_bg    = $hex_or_null('button3_bg');
        $button1_color = $hex_or_null('button1_color');
        $button2_color = $hex_or_null('button2_color');
        $button3_color = $hex_or_null('button3_color');

        // Blank / 0 means "use the site-wide timing"
        $slide_interval = trim($_POST['slide_interval'] ?? '');
        $slide_interval = ($slide_interval === '' || (int)$slide_interval < 1)
            ? null
            : vvu_slider_clamp($slide_interval, 5);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $image_url = $_POST['current_image'] ?? '';

        // Handle File Upload
        if (isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['slider_image']['tmp_name'];
            $fileName = $_FILES['slider_image']['name'];
            $fileSize = $_FILES['slider_image']['size'];
            $fileType = $_FILES['slider_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Sanitize file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = '../Education-Website-and-AdminPanel/images/slider/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                $dest_path = $uploadFileDir . $newFileName;

                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $image_url = 'Education-Website-and-AdminPanel/images/slider/' . $newFileName;
                } else {
                    throw new Exception('There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.');
                }
            } else {
                throw new Exception('Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions));
            }
        }

        if (empty($image_url) && $action === 'add') {
            throw new Exception('Please upload an image.');
        }

        $values = [
            $image_url, $title, $highlight_text, $description,
            $button1_text, $button1_link, $button2_text, $button2_link, $button3_text, $button3_link,
            $button_position, $button_x, $button_y, $button_layout, $button_size, $button_shape,
            $button_mobile, $button_mobile_x, $button_mobile_y, $button_mobile_size, $button_mobile_scale,
            $button1_style, $button1_bg, $button1_color,
            $button2_style, $button2_bg, $button2_color,
            $button3_style, $button3_bg, $button3_color,
            $content_position, $display_order, $is_active, $slide_interval,
        ];

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO homepage_sliders (
                    image_url, title, highlight_text, description,
                    button1_text, button1_link, button2_text, button2_link, button3_text, button3_link,
                    button_position, button_x, button_y, button_layout, button_size, button_shape,
                    button_mobile, button_mobile_x, button_mobile_y, button_mobile_size, button_mobile_scale,
                    button1_style, button1_bg, button1_color,
                    button2_style, button2_bg, button2_color,
                    button3_style, button3_bg, button3_color,
                    content_position, display_order, is_active, slide_interval
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($values);
        } else {
            $stmt = $pdo->prepare("UPDATE homepage_sliders SET
                    image_url=?, title=?, highlight_text=?, description=?,
                    button1_text=?, button1_link=?, button2_text=?, button2_link=?, button3_text=?, button3_link=?,
                    button_position=?, button_x=?, button_y=?, button_layout=?, button_size=?, button_shape=?,
                    button_mobile=?, button_mobile_x=?, button_mobile_y=?, button_mobile_size=?, button_mobile_scale=?,
                    button1_style=?, button1_bg=?, button1_color=?,
                    button2_style=?, button2_bg=?, button2_color=?,
                    button3_style=?, button3_bg=?, button3_color=?,
                    content_position=?, display_order=?, is_active=?, slide_interval=?
                WHERE id=?");
            $values[] = $id;
            $stmt->execute($values);
        }

        header("Location: manage_homepage_content.php?tab=sliders&saved=1");
        exit();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch data if editing
$slider = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM homepage_sliders WHERE id = ?");
    $stmt->execute([$id]);
    $slider = $stmt->fetch();
}

// Everything the button controls and the preview need, already normalised.
$buttons_config = vvu_slide_button_config(is_array($slider) ? $slider : []);
$button_field = function ($i, $key) use ($slider, $buttons_config, $button_defaults) {
    foreach ($buttons_config['buttons'] as $button) {
        if ($button['index'] === $i) {
            return $button[$key];
        }
    }
    // Slot has no text yet: show the built-in look for that slot.
    $map = ['style' => 'style', 'bg' => 'bg', 'fg' => 'color'];
    return $button_defaults[$i][$map[$key] ?? 'style'];
};

$content_position_current = $slider['content_position'] ?? 'middle-center';

include 'header.php';
include 'sidebar.php';
?>

        <!-- The live preview runs on the very stylesheet the homepage uses, so
             a position set here is the position visitors get. -->
        <link href="../css/slider-buttons.css?v=1.2" rel="stylesheet" />

        <!-- Main Content -->
        <main class="main-content">
            <div class="row mb-4">
                <div class="col-12">
                    <h4><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Slider</h4>
                    <p class="text-muted">Banner artwork, wording and the buttons that sit on it</p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="sliderForm">
            <div class="row">
                <div class="col-lg-7">
                    <div class="dashboard-card mb-4">
                        <div class="card-header"><h6 class="mb-0">Slide content</h6></div>
                        <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" id="sliderTitle" class="form-control"
                                           value="<?php echo htmlspecialchars($slider['title'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Highlight Text</label>
                                    <input type="text" name="highlight_text" id="sliderHighlight" class="form-control"
                                           value="<?php echo htmlspecialchars($slider['highlight_text'] ?? ''); ?>">
                                    <small class="text-muted">This text will be highlighted in the title</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="sliderDescription" class="form-control" rows="3"><?php echo htmlspecialchars($slider['description'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Slider Image *</label>
                                    <?php if (!empty($slider['image_url'])): ?>
                                        <p class="small text-muted mb-2">Current image: <?php echo htmlspecialchars($slider['image_url']); ?></p>
                                    <?php endif; ?>
                                    <input type="file" name="slider_image" id="sliderImageInput" class="form-control" accept="image/*" <?php echo $action === 'add' ? 'required' : ''; ?>>
                                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($slider['image_url'] ?? ''); ?>">
                                    <small class="text-muted">Recommended size: 1920x1080px. Allowed formats: JPG, PNG, WEBP. The preview updates as soon as you pick a file.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Text Position</label>
                                    <select name="content_position" id="contentPosition" class="form-select">
                                        <?php
                                        $positions = [
                                            'top-left' => 'Top Left',
                                            'top-center' => 'Top Center',
                                            'top-right' => 'Top Right',
                                            'middle-left' => 'Middle Left',
                                            'middle-center' => 'Middle Center',
                                            'middle-right' => 'Middle Right',
                                            'bottom-left' => 'Bottom Left',
                                            'bottom-center' => 'Bottom Center',
                                            'bottom-right' => 'Bottom Right'
                                        ];
                                        foreach ($positions as $value => $label):
                                            $selected = $content_position_current === $value ? 'selected' : '';
                                            echo "<option value=\"" . htmlspecialchars($value) . "\" $selected>" . htmlspecialchars($label) . "</option>";
                                        endforeach;
                                        ?>
                                    </select>
                                    <small class="text-muted">Where the title and description sit. The buttons can follow this or go anywhere else - see below.</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Display Order *</label>
                                        <input type="number" name="display_order" class="form-control"
                                               value="<?php echo htmlspecialchars($slider['display_order'] ?? '1'); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Display Time</label>
                                        <div class="input-group">
                                            <input type="number" name="slide_interval" class="form-control"
                                                   min="1" max="60" step="1"
                                                   placeholder="<?php echo (int)$slider_timing['interval_seconds']; ?>"
                                                   value="<?php echo !empty($slider['slide_interval']) ? (int)$slider['slide_interval'] : ''; ?>">
                                            <span class="input-group-text">seconds</span>
                                        </div>
                                        <small class="text-muted">
                                            Leave empty to use the site default
                                            (<?php echo (int)$slider_timing['interval_seconds']; ?>s).
                                            <a href="manage_homepage_content.php?tab=sliders">Change default</a>
                                        </small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="form-check form-switch" style="padding-top: 8px;">
                                            <input class="form-check-input" type="checkbox" name="is_active"
                                                   <?php echo ($slider['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                    <!-- ============ BUTTON PLACEMENT ============ -->
                    <div class="dashboard-card mb-4">
                        <div class="card-header"><h6 class="mb-0">Where the buttons sit</h6></div>
                        <div class="card-body">
                            <input type="hidden" name="button_position" id="buttonPosition"
                                   value="<?php echo htmlspecialchars($buttons_config['position']); ?>">

                            <div class="vvu-place-row">
                                <div>
                                    <div class="vvu-place-grid" id="placeGrid">
                                        <?php
                                        $grid = ['top-left', 'top-center', 'top-right',
                                                 'middle-left', 'middle-center', 'middle-right',
                                                 'bottom-left', 'bottom-center', 'bottom-right'];
                                        foreach ($grid as $zone):
                                            $active = $buttons_config['position'] === $zone ? ' is-active' : '';
                                        ?>
                                        <button type="button" class="vvu-place-cell<?php echo $active; ?>"
                                                data-position="<?php echo $zone; ?>"
                                                title="<?php echo htmlspecialchars($button_positions[$zone]); ?>"
                                                aria-label="<?php echo htmlspecialchars($button_positions[$zone]); ?>"><span></span></button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="small text-muted mb-2">Pick one of the nine spots, or drop the buttons on an exact point by dragging them in the preview.</p>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button type="button" class="vvu-place-chip<?php echo $buttons_config['position'] === 'inherit' ? ' is-active' : ''; ?>" data-position="inherit">
                                            <i class="fas fa-align-left"></i> With the text
                                        </button>
                                        <button type="button" class="vvu-place-chip<?php echo $buttons_config['position'] === 'custom' ? ' is-active' : ''; ?>" data-position="custom">
                                            <i class="fas fa-crosshairs"></i> Exact spot
                                        </button>
                                    </div>
                                    <div class="row g-2" id="customCoords">
                                        <div class="col-12">
                                            <span class="vvu-coord-title"><i class="fas fa-desktop"></i> Desktop &amp; tablet spot</span>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Across (% from left)</label>
                                            <input type="number" name="button_x" id="buttonX" class="form-control form-control-sm"
                                                   min="2" max="98" step="0.5" value="<?php echo htmlspecialchars($buttons_config['x']); ?>">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Down (% from top)</label>
                                            <input type="number" name="button_y" id="buttonY" class="form-control form-control-sm"
                                                   min="2" max="98" step="0.5" value="<?php echo htmlspecialchars($buttons_config['y']); ?>">
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted">These two numbers are the centre of the button group, measured on the banner itself - so the same spot holds on every screen size.</small>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-1" id="mobileCoords">
                                        <div class="col-12">
                                            <span class="vvu-coord-title"><i class="fas fa-mobile-screen"></i> Phone spot</span>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Across (% from left)</label>
                                            <input type="number" name="button_mobile_x" id="buttonMobileX" class="form-control form-control-sm"
                                                   min="2" max="98" step="0.5" value="<?php echo htmlspecialchars($buttons_config['mobile_x']); ?>">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Down (% from top)</label>
                                            <input type="number" name="button_mobile_y" id="buttonMobileY" class="form-control form-control-sm"
                                                   min="2" max="98" step="0.5" value="<?php echo htmlspecialchars($buttons_config['mobile_y']); ?>">
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted">Used when <strong>On phones</strong> is set to <em>its own spot</em>. Switch the preview to the phone icon and drag - these fill in by themselves.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row g-3">
                                <?php
                                $selects = [
                                    'button_layout'      => ['label' => 'Arrangement',   'set' => 'layout',      'value' => $buttons_config['layout'],      'id' => 'buttonLayout'],
                                    'button_size'        => ['label' => 'Size (desktop)', 'set' => 'size',        'value' => $buttons_config['size'],        'id' => 'buttonSize'],
                                    'button_shape'       => ['label' => 'Corners',        'set' => 'shape',       'value' => $buttons_config['shape'],       'id' => 'buttonShape'],
                                    'button_mobile'      => ['label' => 'On phones',      'set' => 'mobile',      'value' => $buttons_config['mobile'],      'id' => 'buttonMobile'],
                                    'button_mobile_size' => ['label' => 'Size (phone)',   'set' => 'mobile_size', 'value' => $buttons_config['mobile_size'], 'id' => 'buttonMobileSize'],
                                ];
                                foreach ($selects as $name => $meta): ?>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1"><?php echo $meta['label']; ?></label>
                                    <select name="<?php echo $name; ?>" id="<?php echo $meta['id']; ?>" class="form-select form-select-sm">
                                        <?php foreach ($button_options[$meta['set']] as $value => $label): ?>
                                            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $meta['value'] === $value ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endforeach; ?>

                                <div class="col-md-6 col-12">
                                    <label class="form-label small mb-1" for="buttonMobileScale">
                                        Fine-tune on phones
                                        <span class="vvu-scale-readout" id="buttonMobileScaleOut"><?php echo (int)round($buttons_config['mobile_scale'] * 100); ?>%</span>
                                    </label>
                                    <input type="range" class="form-range" id="buttonMobileScale" name="button_mobile_scale"
                                           min="0.6" max="1.4" step="0.05"
                                           value="<?php echo htmlspecialchars($buttons_config['mobile_scale']); ?>">
                                    <small class="text-muted">Shrinks or grows the phone buttons around the size above. Watch the phone preview as you drag it.</small>
                                </div>

                                <div class="col-12">
                                    <small class="text-muted">
                                        <i class="fas fa-mobile-screen"></i>
                                        <strong>On phones</strong> matters when the banner artwork already carries text: keeping the same spot is fine on a clean image,
                                        <em>its own spot</em> lets you drag the buttons somewhere else entirely in the phone preview, and
                                        <em>move to the bottom</em> lifts them clear of the artwork. <strong>Size (phone)</strong> overrides the desktop size below 768px.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ THE BUTTONS ============ -->
                    <div class="dashboard-card mb-4">
                        <div class="card-header"><h6 class="mb-0">The buttons</h6></div>
                        <div class="card-body">
                            <p class="small text-muted">A button appears on the banner as soon as it has text. Leave the text empty to drop it.</p>
                            <?php foreach ([1, 2, 3] as $i): ?>
                            <div class="vvu-btn-card" data-button="<?php echo $i; ?>">
                                <div class="vvu-btn-card-head">
                                    <span class="vvu-btn-card-num"><?php echo $i; ?></span>
                                    <span class="vvu-btn-card-title">Button <?php echo $i; ?></span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Text</label>
                                        <input type="text" name="button<?php echo $i; ?>_text" class="form-control form-control-sm js-btn-text"
                                               placeholder="<?php echo $i === 1 ? 'e.g. Apply Now' : ''; ?>"
                                               value="<?php echo htmlspecialchars($slider["button{$i}_text"] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Link</label>
                                        <input type="text" name="button<?php echo $i; ?>_link" class="form-control form-control-sm"
                                               placeholder="e.g. admissions.php"
                                               value="<?php echo htmlspecialchars($slider["button{$i}_link"] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">Look</label>
                                        <select name="button<?php echo $i; ?>_style" class="form-select form-select-sm js-btn-style">
                                            <?php foreach ($button_options['style'] as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $button_field($i, 'style') === $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">Colour</label>
                                        <div class="vvu-colour-field">
                                            <input type="color" class="form-control form-control-color js-btn-bg-picker"
                                                   value="<?php echo htmlspecialchars($button_field($i, 'bg')); ?>"
                                                   aria-label="Button <?php echo $i; ?> colour">
                                            <input type="text" name="button<?php echo $i; ?>_bg" class="form-control form-control-sm js-btn-bg-hex"
                                                   value="<?php echo htmlspecialchars($button_field($i, 'bg')); ?>" maxlength="7" spellcheck="false">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">Text colour</label>
                                        <div class="vvu-colour-field">
                                            <input type="color" class="form-control form-control-color js-btn-fg-picker"
                                                   value="<?php echo htmlspecialchars($button_field($i, 'fg')); ?>"
                                                   aria-label="Button <?php echo $i; ?> text colour">
                                            <input type="text" name="button<?php echo $i; ?>_color" class="form-control form-control-sm js-btn-fg-hex"
                                                   value="<?php echo htmlspecialchars($button_field($i, 'fg')); ?>" maxlength="7" spellcheck="false">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="vvu-swatch-row js-swatches">
                                            <span class="vvu-swatch-label">Colours in this banner:</span>
                                            <span class="vvu-swatch-empty text-muted small">reading the image…</span>
                                        </div>
                                        <small class="text-muted">Click a swatch to paint the button in a colour taken straight from the artwork. The text colour follows automatically.</small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="manage_homepage_content.php?tab=sliders" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <?php if ($action === 'edit' && $id): ?>
                        <button type="button" class="btn btn-danger ms-auto" onclick="deleteItem(<?php echo (int)$id; ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ============ LIVE PREVIEW ============ -->
                <div class="col-lg-5">
                    <div class="vvu-preview-sticky">
                        <div class="dashboard-card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Live preview</h6>
                                <div class="btn-group btn-group-sm" role="group" id="deviceSwitch">
                                    <button type="button" class="btn btn-outline-secondary active" data-width="1280"><i class="fas fa-desktop"></i></button>
                                    <button type="button" class="btn btn-outline-secondary" data-width="900"><i class="fas fa-tablet-screen-button"></i></button>
                                    <button type="button" class="btn btn-outline-secondary" data-width="390"><i class="fas fa-mobile-screen"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="vvu-preview-frame" id="previewFrame">
                                    <div class="vvu-preview-scaler" id="previewScaler">
                                        <div class="vvu-slide-preview-stage" id="previewStage">
                                            <img id="previewImage" alt=""
                                                 src="<?php echo !empty($slider['image_url']) ? '../' . htmlspecialchars($slider['image_url']) : ''; ?>">
                                            <div class="vvu-preview-caption pos-<?php echo htmlspecialchars($content_position_current); ?>" id="previewCaption">
                                                <div class="slider-con">
                                                    <h2 id="previewTitle"></h2>
                                                    <p id="previewDescription"></p>
                                                    <div id="previewCaptionButtons"></div>
                                                </div>
                                            </div>
                                            <div id="previewLayerHost"></div>
                                            <div class="vvu-preview-crosshair" id="previewCrosshair" hidden></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="small text-muted mt-2 mb-0" id="previewHint">
                                    <i class="fas fa-hand-pointer"></i> Drag the buttons anywhere on the banner to place them exactly. Fine-tune with the two percentage boxes.
                                </p>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="card-header"><h6 class="mb-0">Tips</h6></div>
                            <div class="card-body">
                                <ul style="padding-left: 20px; margin: 0;" class="small">
                                    <li class="mb-2">Use high-quality images (1920x1080px recommended)</li>
                                    <li class="mb-2">Artwork that already carries its own text usually needs no title here - just a button placed on the empty part of the image</li>
                                    <li class="mb-2">Two buttons read better than three</li>
                                    <li class="mb-2">Glass and outline looks sit well on busy photographs; solid works best on a plain area</li>
                                    <li class="mb-2">Display order determines slide sequence</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </main>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this slider? This action cannot be undone.')) {
            window.location.href = 'edit_slider.php?delete=' + id;
        }
    }

    /* ------------------------------------------------------------------
       Live preview.

       The stage is a real 1280px-wide slide that is scaled down to fit the
       card, so the browser measures it exactly like the homepage does and
       the shared stylesheet (css/slider-buttons.css) decides the result.
       The button markup below mirrors vvu_slide_buttons_html() in
       includes/slider_buttons.php - the same classes and the same custom
       properties - so nothing is approximated.
       ------------------------------------------------------------------ */
    (function () {
        const form        = document.getElementById('sliderForm');
        const frame       = document.getElementById('previewFrame');
        const scaler      = document.getElementById('previewScaler');
        const stage       = document.getElementById('previewStage');
        const image       = document.getElementById('previewImage');
        const caption     = document.getElementById('previewCaption');
        const titleEl     = document.getElementById('previewTitle');
        const descEl      = document.getElementById('previewDescription');
        const captionBtns = document.getElementById('previewCaptionButtons');
        const layerHost   = document.getElementById('previewLayerHost');
        const crosshair   = document.getElementById('previewCrosshair');
        const positionInput = document.getElementById('buttonPosition');
        const xInput      = document.getElementById('buttonX');
        const yInput      = document.getElementById('buttonY');
        const mxInput     = document.getElementById('buttonMobileX');
        const myInput     = document.getElementById('buttonMobileY');
        const mobileInput = document.getElementById('buttonMobile');
        const scaleInput  = document.getElementById('buttonMobileScale');
        const scaleOut    = document.getElementById('buttonMobileScaleOut');
        const coordsBox   = document.getElementById('customCoords');
        const mobileBox   = document.getElementById('mobileCoords');
        const hint        = document.getElementById('previewHint');

        // The stage is a real slide at a real width; below this the phone rules
        // in css/slider-buttons.css take over, exactly as they do live.
        const PHONE_MAX = 767;
        let stageWidth = 1280;

        function inPhoneView() { return stageWidth <= PHONE_MAX; }
        let descriptionText = <?php echo json_encode(strip_tags((string)($slider['description'] ?? ''))); ?>;

        /* ---------- helpers shared with the PHP renderer ---------- */

        function normaliseHex(value, fallback) {
            value = (value || '').trim();
            return /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(value) ? value.toLowerCase() : fallback;
        }

        function expand(hex) {
            hex = hex.replace('#', '');
            return hex.length === 3 ? hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2] : hex;
        }

        function rgbOf(hex) {
            const h = expand(normaliseHex(hex, '#000000'));
            return [parseInt(h.slice(0, 2), 16), parseInt(h.slice(2, 4), 16), parseInt(h.slice(4, 6), 16)];
        }

        // Same brightness test as vvu_slide_button_ink().
        function inkOn(hex) {
            const [r, g, b] = rgbOf(hex);
            return (0.299 * r + 0.587 * g + 0.114 * b) > 150 ? '#111111' : '#ffffff';
        }

        function readButtons() {
            return Array.prototype.map.call(document.querySelectorAll('.vvu-btn-card'), function (card) {
                const fallbackBg = card.querySelector('.js-btn-bg-picker').defaultValue;
                const fallbackFg = card.querySelector('.js-btn-fg-picker').defaultValue;
                return {
                    text:  card.querySelector('.js-btn-text').value.trim(),
                    style: card.querySelector('.js-btn-style').value,
                    bg:    normaliseHex(card.querySelector('.js-btn-bg-hex').value, fallbackBg),
                    fg:    normaliseHex(card.querySelector('.js-btn-fg-hex').value, fallbackFg)
                };
            }).filter(function (button) { return button.text !== ''; });
        }

        function buildGroup(buttons) {
            const group = document.createElement('div');
            const mobileSize = document.getElementById('buttonMobileSize').value;
            group.className = 'vvu-slide-btns lay-' + document.getElementById('buttonLayout').value +
                              ' size-' + document.getElementById('buttonSize').value +
                              ' shape-' + document.getElementById('buttonShape').value +
                              (mobileSize !== 'inherit' ? ' msize-' + mobileSize : '');

            const scale = parseFloat(scaleInput.value);
            if (scale && scale !== 1) {
                group.style.setProperty('--btn-mscale', scale);
            }

            buttons.forEach(function (button) {
                const a = document.createElement('a');
                a.className = 'vvu-slide-btn is-' + button.style;
                // The preview never navigates - the click handler on the stage
                // swallows it - but an href keeps the link styling honest.
                a.href = '#';
                a.style.setProperty('--btn-bg', button.bg);
                a.style.setProperty('--btn-fg', button.fg);
                a.style.setProperty('--btn-ink', inkOn(button.bg));
                a.style.setProperty('--btn-rgb', rgbOf(button.bg).join(', '));
                const span = document.createElement('span');
                span.textContent = button.text;
                a.appendChild(span);
                group.appendChild(a);
            });

            return group;
        }

        function render() {
            const position = positionInput.value;
            const buttons  = readButtons();

            // Caption text
            const title     = document.getElementById('sliderTitle').value.trim();
            const highlight = document.getElementById('sliderHighlight').value.trim();
            titleEl.innerHTML = '';
            if (title || highlight) {
                titleEl.appendChild(document.createTextNode(title + ' '));
                if (highlight) {
                    const span = document.createElement('span');
                    span.textContent = highlight;
                    titleEl.appendChild(span);
                }
            }
            titleEl.hidden = !(title || highlight);
            descEl.textContent = descriptionText;
            descEl.hidden = descriptionText.trim() === '';

            caption.className = 'vvu-preview-caption pos-' + document.getElementById('contentPosition').value;
            caption.hidden = titleEl.hidden && descEl.hidden && !(position === 'inherit' && buttons.length);

            // Buttons - in the caption, or in their own placement layer
            captionBtns.innerHTML = '';
            layerHost.innerHTML = '';

            const mobileMode = mobileInput.value;

            if (buttons.length) {
                if (position === 'inherit') {
                    captionBtns.appendChild(buildGroup(buttons));
                } else {
                    const layer = document.createElement('div');
                    layer.className = 'vvu-btn-layer at-' + position + ' mob-' + mobileMode;
                    if (position === 'custom') {
                        layer.style.setProperty('--btn-x', (parseFloat(xInput.value) || 50) + '%');
                        layer.style.setProperty('--btn-y', (parseFloat(yInput.value) || 80) + '%');
                    }
                    if (mobileMode === 'own') {
                        layer.style.setProperty('--btn-mx', (parseFloat(mxInput.value) || 50) + '%');
                        layer.style.setProperty('--btn-my', (parseFloat(myInput.value) || 85) + '%');
                    }
                    layer.appendChild(buildGroup(buttons));
                    layerHost.appendChild(layer);
                }
            }

            // The crosshair marks the point being edited in the view you are
            // looking at: the phone's own spot in phone view, otherwise the
            // desktop one.
            const showingMobileSpot = inPhoneView() && mobileMode === 'own' && position !== 'inherit';
            crosshair.hidden = !(showingMobileSpot || position === 'custom');

            if (showingMobileSpot) {
                crosshair.style.left = (parseFloat(mxInput.value) || 50) + '%';
                crosshair.style.top  = (parseFloat(myInput.value) || 85) + '%';
            } else if (position === 'custom') {
                crosshair.style.left = (parseFloat(xInput.value) || 50) + '%';
                crosshair.style.top  = (parseFloat(yInput.value) || 80) + '%';
            }

            coordsBox.classList.toggle('is-muted', position !== 'custom');
            mobileBox.classList.toggle('is-muted', mobileMode !== 'own' || position === 'inherit');
            document.querySelectorAll('.vvu-place-cell, .vvu-place-chip').forEach(function (el) {
                el.classList.toggle('is-active', el.dataset.position === position);
            });

            scaleOut.textContent = Math.round((parseFloat(scaleInput.value) || 1) * 100) + '%';

            hint.innerHTML = inPhoneView()
                ? '<i class="fas fa-mobile-screen"></i> This is the phone layout. Dragging here sets the <strong>phone spot</strong> only - the desktop one stays where it is.'
                : '<i class="fas fa-hand-pointer"></i> Drag the buttons anywhere on the banner to place them exactly. Fine-tune with the two percentage boxes.';

            fit();
        }

        /* ---------- scaling: keep a true 1280px slide inside the card ---------- */

        function fit() {
            const available = frame.clientWidth;
            stage.classList.toggle('is-empty', !image.getAttribute('src'));
            if (!available) { return; }
            // Never enlarge: a phone view blown up to fill the card makes every
            // button look bigger than it will ever be on a real handset.
            const scale = Math.min(available / stageWidth, 1);
            scaler.style.setProperty('--preview-scale', scale);
            scaler.style.setProperty('--preview-offset', ((available - stageWidth * scale) / 2) + 'px');
            scaler.style.width = stageWidth + 'px';
            frame.style.height = (stage.offsetHeight * scale) + 'px';
        }

        window.addEventListener('resize', fit);
        image.addEventListener('load', function () { fit(); readPalette(); });

        const deviceSwitch = document.getElementById('deviceSwitch');

        function setDevice(width) {
            stageWidth = width;
            deviceSwitch.querySelectorAll('button').forEach(function (b) {
                b.classList.toggle('active', parseInt(b.dataset.width, 10) === width);
            });
            // A full render, not just a resize: the crosshair, the hint and the
            // spot that dragging edits all depend on which view is showing.
            render();
        }

        deviceSwitch.addEventListener('click', function (event) {
            const button = event.target.closest('button[data-width]');
            if (button) { setDevice(parseInt(button.dataset.width, 10)); }
        });

        // The fine-tune only bites below 768px, so show that view while it is
        // being dragged - otherwise the slider would look like it does nothing.
        scaleInput.addEventListener('input', function () {
            if (!inPhoneView()) {
                setDevice(390);
            } else {
                render();
            }
        });

        /* ---------- placement controls ---------- */

        document.querySelectorAll('.vvu-place-cell, .vvu-place-chip').forEach(function (el) {
            el.addEventListener('click', function () {
                positionInput.value = el.dataset.position;
                render();
            });
        });

        [xInput, yInput].forEach(function (input) {
            input.addEventListener('input', function () {
                positionInput.value = 'custom';
                render();
            });
        });

        // Typing a phone coordinate is a request to use one.
        [mxInput, myInput].forEach(function (input) {
            input.addEventListener('input', function () {
                if (mobileInput.value !== 'own') {
                    mobileInput.value = 'own';
                }
                render();
            });
        });

        // Drag the buttons straight onto the artwork. The grab offset is kept
        // so the group follows the pointer instead of jumping under it.
        let dragging = false;
        let grabOffset = { x: 0, y: 0 };

        function snap(value) {
            return Math.round(Math.min(98, Math.max(2, value)) * 2) / 2;
        }

        function pointTo(event) {
            const rect = stage.getBoundingClientRect();
            if (!rect.width || !rect.height) { return; }
            const x = snap(((event.clientX - rect.left) / rect.width) * 100 - grabOffset.x);
            const y = snap(((event.clientY - rect.top) / rect.height) * 100 - grabOffset.y);

            if (inPhoneView()) {
                // Dragging in the phone view moves the phone spot and nothing
                // else. Buttons that were still sitting with the text have to
                // come out of the caption first - there is nothing to place
                // otherwise.
                if (positionInput.value === 'inherit') {
                    positionInput.value = 'custom';
                    xInput.value = x;
                    yInput.value = y;
                }
                mobileInput.value = 'own';
                mxInput.value = x;
                myInput.value = y;
            } else {
                positionInput.value = 'custom';
                xInput.value = x;
                yInput.value = y;
            }

            render();
        }

        stage.addEventListener('pointerdown', function (event) {
            const group = event.target.closest('.vvu-slide-btns');
            if (!group) { return; }
            event.preventDefault();

            const stageRect = stage.getBoundingClientRect();
            const groupRect = group.getBoundingClientRect();
            grabOffset = {
                x: ((event.clientX - (groupRect.left + groupRect.width / 2)) / stageRect.width) * 100,
                y: ((event.clientY - (groupRect.top + groupRect.height / 2)) / stageRect.height) * 100
            };

            dragging = true;
            stage.setPointerCapture(event.pointerId);
            stage.classList.add('is-dragging');
            pointTo(event);
        });

        stage.addEventListener('pointermove', function (event) {
            if (dragging) { pointTo(event); }
        });

        ['pointerup', 'pointercancel'].forEach(function (type) {
            stage.addEventListener(type, function () {
                dragging = false;
                stage.classList.remove('is-dragging');
            });
        });

        // Clicking a button in the preview must never follow the link.
        stage.addEventListener('click', function (event) {
            if (event.target.closest('.vvu-slide-btn')) { event.preventDefault(); }
        });

        /* ---------- colours taken from the banner ---------- */

        function readPalette() {
            if (!image.getAttribute('src')) { return; }
            let colours = [];
            try {
                const canvas = document.createElement('canvas');
                const w = canvas.width = 90;
                const h = canvas.height = Math.max(1, Math.round(90 * (image.naturalHeight / image.naturalWidth)));
                const ctx = canvas.getContext('2d');
                ctx.drawImage(image, 0, 0, w, h);
                const data = ctx.getImageData(0, 0, w, h).data;
                const buckets = {};

                for (let i = 0; i < data.length; i += 4) {
                    if (data[i + 3] < 200) { continue; }
                    // Round each channel to 32 steps: enough to group shades of
                    // the same colour without merging distinct ones.
                    const key = [data[i], data[i + 1], data[i + 2]]
                        .map(function (c) { return Math.round(c / 32) * 32; }).join(',');
                    buckets[key] = (buckets[key] || 0) + 1;
                }

                colours = Object.keys(buckets)
                    .sort(function (a, b) { return buckets[b] - buckets[a]; })
                    .slice(0, 8)
                    .map(function (key) {
                        return '#' + key.split(',').map(function (c) {
                            return Math.min(255, parseInt(c, 10)).toString(16).padStart(2, '0');
                        }).join('');
                    });
            } catch (e) {
                // A cross-origin image cannot be read; the pickers still work.
                colours = [];
            }

            document.querySelectorAll('.js-swatches').forEach(function (row) {
                row.querySelectorAll('.vvu-swatch, .vvu-swatch-empty').forEach(function (el) { el.remove(); });

                if (!colours.length) {
                    const note = document.createElement('span');
                    note.className = 'vvu-swatch-empty text-muted small';
                    note.textContent = 'add an image to pick colours from it';
                    row.appendChild(note);
                    return;
                }

                colours.forEach(function (colour) {
                    const swatch = document.createElement('button');
                    swatch.type = 'button';
                    swatch.className = 'vvu-swatch';
                    swatch.style.background = colour;
                    swatch.title = colour;
                    swatch.addEventListener('click', function () {
                        const card = row.closest('.vvu-btn-card');
                        card.querySelector('.js-btn-bg-hex').value = colour;
                        card.querySelector('.js-btn-bg-picker').value = colour;
                        // Outline and text-link styles keep their own label
                        // colour; the solid ones need a readable one.
                        const style = card.querySelector('.js-btn-style').value;
                        if (style === 'solid' || style === 'glass') {
                            const ink = inkOn(colour);
                            card.querySelector('.js-btn-fg-hex').value = ink;
                            card.querySelector('.js-btn-fg-picker').value = ink;
                        }
                        render();
                    });
                    row.appendChild(swatch);
                });
            });
        }

        /* ---------- keep the colour picker and the hex box in step ---------- */

        document.querySelectorAll('.vvu-btn-card').forEach(function (card) {
            [['bg'], ['fg']].forEach(function (pair) {
                const picker = card.querySelector('.js-btn-' + pair[0] + '-picker');
                const hex    = card.querySelector('.js-btn-' + pair[0] + '-hex');
                picker.addEventListener('input', function () { hex.value = picker.value; render(); });
                hex.addEventListener('input', function () {
                    if (/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(hex.value.trim())) {
                        picker.value = hex.value.trim();
                        render();
                    }
                });
            });
        });

        form.addEventListener('input', function (event) {
            if (event.target.closest('.vvu-colour-field')) { return; }
            render();
        });
        form.addEventListener('change', render);

        // Swap the preview image the moment a new file is chosen.
        document.getElementById('sliderImageInput').addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) { return; }
            image.src = URL.createObjectURL(file);
        });

        /* ---------- description comes from CKEditor ---------- */

        ClassicEditor
            .create(document.querySelector('#sliderDescription'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
            })
            .then(function (editor) {
                editor.model.document.on('change:data', function () {
                    const div = document.createElement('div');
                    div.innerHTML = editor.getData();
                    descriptionText = (div.textContent || '').trim();
                    render();
                });
            })
            .catch(function (error) { console.error(error); });

        render();
        if (image.complete) { fit(); readPalette(); }
    })();
    </script>

    <style>
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-danger {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary { background: #4680ff; color: white; }
    .btn-primary:hover { background: #3066d9; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn-secondary:hover { background: #5a6268; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; }
    .btn-group-sm .btn { padding: 5px 12px; }
    .form-check-input { width: 3rem; height: 1.5rem; cursor: pointer; }

    /* CKEditor Height Adjustment */
    .ck-editor__editable_inline { min-height: 160px; color: #333; }
    .ck.ck-editor { width: 100% !important; }

    /* ---- Placement picker ---- */
    .vvu-place-row { display: flex; gap: 22px; align-items: flex-start; flex-wrap: wrap; }

    .vvu-place-grid {
        display: grid;
        grid-template-columns: repeat(3, 34px);
        grid-template-rows: repeat(3, 26px);
        gap: 4px;
        padding: 6px;
        background: #eef1f6;
        border-radius: 10px;
    }
    .vvu-place-cell {
        border: 1px solid #d3dae6;
        background: #fff;
        border-radius: 5px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .15s ease;
    }
    .vvu-place-cell span {
        width: 12px; height: 5px; border-radius: 3px; background: #b9c3d3;
        transition: background .15s ease;
    }
    .vvu-place-cell:hover { border-color: #4680ff; }
    .vvu-place-cell.is-active { background: #4680ff; border-color: #4680ff; }
    .vvu-place-cell.is-active span { background: #fff; }

    .vvu-place-chip {
        border: 1px solid #d3dae6;
        background: #fff;
        border-radius: 999px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        color: #4a5568;
        cursor: pointer;
    }
    .vvu-place-chip:hover { border-color: #4680ff; color: #4680ff; }
    .vvu-place-chip.is-active { background: #4680ff; border-color: #4680ff; color: #fff; }

    #customCoords.is-muted,
    #mobileCoords.is-muted { opacity: .5; }

    .vvu-scale-readout {
        display: inline-block;
        min-width: 42px;
        margin-left: 4px;
        padding: 1px 6px;
        border-radius: 999px;
        background: #eef1f6;
        font-weight: 700;
        font-size: 11px;
        color: #4680ff;
        text-align: center;
    }

    .vvu-coord-title {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #6b7280;
    }

    /* ---- Button cards ---- */
    .vvu-btn-card {
        border: 1px solid #e6eaf2;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 14px;
        background: #fbfcfe;
    }
    .vvu-btn-card-head { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
    .vvu-btn-card-num {
        width: 22px; height: 22px; border-radius: 50%;
        background: #4680ff; color: #fff;
        font-size: 12px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }
    .vvu-btn-card-title { font-weight: 700; font-size: 13px; color: #2d3748; }

    .vvu-colour-field { display: flex; gap: 6px; align-items: center; }
    .vvu-colour-field input[type="color"] {
        width: 42px; padding: 2px; height: 31px; flex: 0 0 auto; cursor: pointer;
    }

    .vvu-swatch-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
    .vvu-swatch-label { font-size: 12px; color: #6b7280; }
    .vvu-swatch {
        width: 24px; height: 24px; border-radius: 6px;
        border: 1px solid rgba(0, 0, 0, .15);
        padding: 0; cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
    }
    .vvu-swatch:hover { transform: scale(1.12); }

    /* ---- Live preview ---- */
    .vvu-preview-sticky { position: sticky; top: 20px; }

    .vvu-preview-frame {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        background: #0b1220;
    }
    .vvu-preview-scaler {
        transform: translateX(var(--preview-offset, 0px)) scale(var(--preview-scale, 1));
        transform-origin: top left;
    }
    /* A real slide, at a real width - the scaler only shrinks the picture of
       it, so container queries and percentages behave as they do live. */
    .vvu-slide-preview-stage {
        position: relative;
        width: 100%;
        background: #000;
        overflow: hidden;
        user-select: none;
    }
    .vvu-slide-preview-stage img { display: block; width: 100%; height: auto; }
    .vvu-slide-preview-stage.is-dragging { cursor: grabbing; }
    .vvu-slide-preview-stage .vvu-slide-btns { cursor: grab; }

    /* No artwork chosen yet: keep a slide-shaped box so the placement
       controls still have something to work against. */
    .vvu-slide-preview-stage.is-empty { aspect-ratio: 16 / 9; background: #16233b; }
    .vvu-slide-preview-stage.is-empty img { display: none; }
    .vvu-slide-preview-stage.is-empty::after {
        content: 'Choose a slider image';
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        color: rgba(255, 255, 255, .5); font-size: 1.6cqi; letter-spacing: 1px;
    }

    /* pointer-events mirrors the live stylesheet: the caption box is wide, and
       it must not steal a drag aimed at the buttons behind it. */
    .vvu-preview-caption { position: absolute; inset: 0; display: grid; padding: 5% 6%; pointer-events: none; }
    .vvu-preview-caption[hidden] { display: none; }
    .vvu-preview-caption .vvu-slide-btns { pointer-events: auto; }
    .vvu-preview-caption .slider-con { max-width: 65%; color: #fff; text-shadow: 2px 2px 10px rgba(0, 0, 0, .8); }
    .vvu-preview-caption h2 {
        font-size: 4.6cqi; font-weight: 900; text-transform: uppercase;
        margin: 0 0 .4em; line-height: 1.05; color: #fff;
    }
    .vvu-preview-caption h2 span { color: #ff5722; }
    .vvu-preview-caption p { font-size: 1.7cqi; margin: 0; color: #fff; }
    .vvu-preview-caption [hidden] { display: none; }

    .vvu-preview-caption.pos-top-left,
    .vvu-preview-caption.pos-top-center,
    .vvu-preview-caption.pos-top-right { align-items: start; }
    .vvu-preview-caption.pos-middle-left,
    .vvu-preview-caption.pos-middle-center,
    .vvu-preview-caption.pos-middle-right { align-items: center; }
    .vvu-preview-caption.pos-bottom-left,
    .vvu-preview-caption.pos-bottom-center,
    .vvu-preview-caption.pos-bottom-right { align-items: end; }
    .vvu-preview-caption.pos-top-left,
    .vvu-preview-caption.pos-middle-left,
    .vvu-preview-caption.pos-bottom-left { justify-items: start; text-align: left; }
    .vvu-preview-caption.pos-top-center,
    .vvu-preview-caption.pos-middle-center,
    .vvu-preview-caption.pos-bottom-center { justify-items: center; text-align: center; }
    .vvu-preview-caption.pos-top-right,
    .vvu-preview-caption.pos-middle-right,
    .vvu-preview-caption.pos-bottom-right { justify-items: end; text-align: right; }

    .vvu-preview-crosshair {
        position: absolute;
        width: 18px; height: 18px;
        margin: -9px 0 0 -9px;
        border: 2px solid rgba(255, 255, 255, .9);
        border-radius: 50%;
        box-shadow: 0 0 0 2px rgba(0, 0, 0, .35);
        z-index: 7;
        pointer-events: none;
    }
    .vvu-preview-crosshair[hidden] { display: none; }
    </style>

<?php include 'footer.php'; ?>
