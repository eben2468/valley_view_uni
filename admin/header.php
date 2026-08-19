<?php
ob_start();
require_once(__DIR__ . '/../includes/admin_auth.php');

// Include database connection
require_once('../includes/db_connect.php');
// Function definitions only — needed here so the upload-error banner below can
// render even on pages that were redirected to and don't upload anything.
require_once('../includes/upload_helper.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Panel - Valley View University</title>
    <!-- META TAGS -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- FAV ICON(BROWSER TAB ICON) -->
    <link rel="icon" href="../favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FONTAWESOME ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- jQuery (Required for Bootstrap and Summernote) -->
    <script src="../js/vendor/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CUSTOM ADMIN CSS -->
    <link href="admin-styles.css?v=1.3" rel="stylesheet" />
    <link href="admin-modern.css?v=1.3" rel="stylesheet" />
    <!-- Responsive corrections. Loaded last so it can override the two above;
         its companion script tags wide tables and fixed grids that only a
         stylesheet cannot reach. -->
    <link href="admin-responsive.css?v=1.0" rel="stylesheet" />
    <script src="js/admin-responsive.js?v=1.0" defer></script>
    <!-- Shrinks oversized photos in the browser so uploads can't trip the
         server's request-size limit (HTTP 413) -->
    <script src="js/upload-guard.js?v=1.1" defer></script>

    <!-- Page search (Ctrl+K). The index is built from the manager files by
         includes/page_index.php, so nested "?page=" editors are findable
         without knowing which manager owns them. -->
    <link href="page-search.css?v=1.0" rel="stylesheet" />
    <script>
        window.VVU_PAGE_INDEX = <?php
            require_once __DIR__ . '/includes/page_index.php';
            echo json_encode(
                vvu_admin_page_index(),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
            );
        ?>;
    </script>
    <script src="js/page-search.js?v=1.0" defer></script>
</head>

<body>
    <div class="admin-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <!-- Opens the page-search overlay (admin/js/page-search.js).
                     This box was previously an inert text input that did
                     nothing when typed into. It is now a button so it is
                     keyboard-reachable and announces itself correctly. -->
                <button type="button" class="search-box" data-vvu-search-trigger
                        aria-label="Search admin pages">
                    <i class="fas fa-search"></i>
                    <span class="vvu-search-placeholder">Search pages…</span>
                    <span class="vvu-search-kbd">Ctrl K</span>
                </button>
            </div>
            <div class="header-right">
                <button class="header-icon">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-danger">5</span>
                </button>
                <button class="header-icon">
                    <i class="far fa-envelope"></i>
                    <span class="badge badge-success">3</span>
                </button>
                <div class="user-profile dropdown">
                    <button class="dropdown-toggle border-0 bg-transparent p-0 d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php 
                        // Fetch admin profile picture
                        $profile_pic_query = $pdo->prepare("SELECT profile_picture FROM admin_users WHERE id = ?");
                        $profile_pic_query->execute([$_SESSION['admin_id']]);
                        $admin_data = $profile_pic_query->fetch();
                        $header_profile_pic = !empty($admin_data['profile_picture']) && file_exists('../' . $admin_data['profile_picture']) 
                            ? '../' . $admin_data['profile_picture'] 
                            : '../Education-Website-and-AdminPanel/images/user/6.png';
                        ?>
                        <img src="<?php echo $header_profile_pic; ?>" alt="User" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                        <span class="user-name d-none d-md-inline"><?php echo $_SESSION['admin_name']; ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <?php
        // Upload failures used to be swallowed: the page redirected and simply
        // kept the old image, so a failed upload was indistinguishable from
        // not choosing a file. Shown here so every admin page reports it.
        if (function_exists('vvu_take_upload_error') && ($vvu_upload_error = vvu_take_upload_error())):
        ?>
        <div class="alert alert-danger alert-dismissible fade show" style="margin: 20px 30px 0;">
            <i class="fas fa-triangle-exclamation me-2"></i>
            <strong>The image was not uploaded.</strong>
            <?php echo htmlspecialchars($vvu_upload_error); ?>
            <br><small class="text-muted">Everything else on the form was saved. The previous image is still in place.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
