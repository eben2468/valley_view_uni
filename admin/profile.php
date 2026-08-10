<?php
require_once('../includes/db_connect.php');
require_once __DIR__ . '/../includes/admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $full_name = $_POST['name'];
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $bio = $_POST['bio'] ?? '';
        
        $stmt = $pdo->prepare("UPDATE admin_users SET full_name = ?, email = ?, phone = ?, bio = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $phone, $bio, $_SESSION['admin_id']]);
        
        $_SESSION['admin_name'] = $full_name;
        $success = "Profile updated successfully!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    try {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Get current password from database
        $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if (password_verify($current_password, $admin['password'])) {
            if ($new_password === $confirm_password) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $_SESSION['admin_id']]);
                $password_success = "Password changed successfully!";
            } else {
                $password_error = "New passwords do not match!";
            }
        } else {
            $password_error = "Current password is incorrect!";
        }
    } catch (Exception $e) {
        $password_error = "Error: " . $e->getMessage();
    }
}

// Fetch admin data
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Check if admin was found
if (!$admin) {
    header("Location: logout.php");
    exit();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    try {
        $file = $_FILES['profile_picture'];
        
        // Check if file was uploaded without errors
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            // Validate file type
            if (!in_array($file['type'], $allowed_types)) {
                $error = "Invalid file type. Please upload a JPG, PNG, or GIF image.";
            }
            // Validate file size
            elseif ($file['size'] > $max_size) {
                $error = "File is too large. Maximum size is 5MB.";
            }
            else {
                // Create uploads directory if it doesn't exist
                $upload_dir = '../uploads/profile_pictures/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . $_SESSION['admin_id'] . '_' . time() . '.' . $extension;
                $filepath = $upload_dir . $filename;
                
                // Delete old profile picture if exists
                if (!empty($admin['profile_picture']) && file_exists('../' . $admin['profile_picture'])) {
                    unlink('../' . $admin['profile_picture']);
                }
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Save to database
                    $db_path = 'uploads/profile_pictures/' . $filename;
                    $stmt = $pdo->prepare("UPDATE admin_users SET profile_picture = ? WHERE id = ?");
                    $stmt->execute([$db_path, $_SESSION['admin_id']]);
                    
                    // Refresh admin data
                    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
                    $stmt->execute([$_SESSION['admin_id']]);
                    $admin = $stmt->fetch();
                    
                    $success = "Profile picture updated successfully!";
                } else {
                    $error = "Failed to upload file. Please try again.";
                }
            }
        }
    } catch (Exception $e) {
        $error = "Error uploading picture: " . $e->getMessage();
    }
}

include 'header.php';
include 'sidebar.php';
?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="row mb-4">
                <div class="col-12">
                    <h4>My Profile</h4>
                    <p class="text-muted">Manage your account settings and profile information</p>
                </div>
            </div>

            <div class="row g-3">
                <!-- Profile Card -->
                <div class="col-lg-4">
                    <div class="dashboard-card text-center">
                        <div class="card-body" style="padding: 30px;">
                            <div class="profile-avatar mb-3 position-relative" style="display: inline-block;">
                                <?php 
                                $profile_pic = !empty($admin['profile_picture']) && file_exists('../' . $admin['profile_picture']) 
                                    ? '../' . $admin['profile_picture'] 
                                    : '../Education-Website-and-AdminPanel/images/user/6.png';
                                ?>
                                <img id="profileImage" src="<?php echo $profile_pic; ?>" 
                                     alt="Profile" 
                                     style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid #4680ff; object-fit: cover;">
                                <label for="profilePictureInput" style="position: absolute; bottom: 0; right: 0; background: #4680ff; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white;">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                            
                            <form method="POST" enctype="multipart/form-data" id="pictureForm" style="display: none;">
                                <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*" onchange="document.getElementById('pictureForm').submit();">
                            </form>
                            
                            <h5 class="mb-1"><?php echo htmlspecialchars($admin['full_name'] ?? $admin['username'] ?? 'Admin'); ?></h5>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($admin['email'] ?? $admin['username'] ?? ''); ?></p>
                            <span class="badge badge-success" style="font-size: 13px; padding: 8px 16px;">Administrator</span>
                            
                            <div class="mt-4 pt-4" style="border-top: 1px solid #e0e0e0;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Member Since</span>
                                    <strong><?php echo isset($admin['created_at']) ? date('M Y', strtotime($admin['created_at'])) : 'N/A'; ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Last Login</span>
                                    <strong><?php echo isset($admin['last_login']) ? date('M d, Y', strtotime($admin['last_login'])) : (isset($admin['created_at']) ? date('M d, Y', strtotime($admin['created_at'])) : 'N/A'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Information Form -->
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5>Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" 
                                               value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" 
                                               placeholder="your.email@example.com">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" 
                                           value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>" 
                                           placeholder="+233 XX XXX XXXX">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control" rows="4" 
                                              placeholder="Tell us about yourself..."><?php echo htmlspecialchars($admin['bio'] ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Change Password -->
                    <div class="dashboard-card mt-3">
                        <div class="card-header">
                            <h5>Change Password</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($password_success)): ?>
                                <div class="alert alert-success"><?php echo $password_success; ?></div>
                            <?php endif; ?>
                            <?php if (isset($password_error)): ?>
                                <div class="alert alert-danger"><?php echo $password_error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                    </div>
                                </div>
                                
                                <button type="submit" name="change_password" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
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
    .btn-primary {
        background: #4680ff;
        color: white;
    }
    .btn-primary:hover {
        background: #3066d9;
    }
    .btn-warning {
        background: #ffb64d;
        color: white;
    }
    .btn-warning:hover {
        background: #ff9f1a;
    }
    .position-relative {
        position: relative;
    }
    label[for="profilePictureInput"]:hover {
        background: #3066d9 !important;
        transform: scale(1.1);
        transition: all 0.2s;
    }
    </style>

<?php include 'footer.php'; ?>
