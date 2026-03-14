<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');

$success_msg = "";
$error_msg = "";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent deleting the currently logged-in user
    if ($id == $_SESSION['admin_id']) {
        $error_msg = "You cannot delete your own account while logged in.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success_msg = "Account deleted successfully.";
        } else {
            $error_msg = "Failed to delete account.";
        }
    }
}

// Handle Add New User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error_msg = "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $full_name, $email])) {
                $success_msg = "New account created successfully.";
            } else {
                $error_msg = "Failed to create account.";
            }
        }
    }
}

// Handle Update User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $user_id = (int)$_POST['user_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];

    try {
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET full_name = ?, email = ?, username = ?, password = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $username, $hashed_password, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin_users SET full_name = ?, email = ?, username = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $username, $user_id]);
        }
        $success_msg = "User account updated successfully.";
    } catch (Exception $e) {
        $error_msg = "Failed to update user: " . $e->getMessage();
    }
}

// Fetch all users
$users = $pdo->query("SELECT * FROM admin_users ORDER BY id DESC")->fetchAll();
?>

<style>
    .manage-users-container {
        padding: 30px;
        position: relative;
    }
    
    /* Ensure Top Navbar is visible and high-priority */
    .top-header {
        background: #ffffff !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 1050 !important; /* Higher than sidebar (1001) and modal (1000) */
        box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .page-header h1 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }
    .btn-add-user {
        background: linear-gradient(135deg, #2563eb, #0891b2);
        color: #fff;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    .btn-add-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
    }
    
    .users-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table th {
        background: #f8fafc;
        padding: 16px 24px;
        text-align: left;
        font-weight: 700;
        color: #64748b;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #f1f5f9;
    }

    .users-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f8fafc;
        color: #334155;
        font-size: 0.95rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        text-transform: uppercase;
    }

    .badge-admin {
        background: #ecfdf5;
        color: #059669;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .action-btns {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-edit { background: #f1f5f9; color: #475569; }
    .btn-edit:hover { background: #e2e8f0; color: #1e293b; }
    .btn-delete { background: #fef2f2; color: #ef4444; }
    .btn-delete:hover { background: #fee2e2; color: #dc2626; }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 30px;
        border-radius: 24px;
        width: 500px;
        max-width: 90%;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
    }

    .close {
        font-size: 28px;
        font-weight: bold;
        color: #94a3b8;
        cursor: pointer;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #475569;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .alert {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-weight: 600;
    }
    .alert-success { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
    .alert-error { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }
</style>

<main class="main-content">
    <div class="manage-users-container">
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="page-header">
            <div>
                <h1><i class="fas fa-user-shield me-2"></i> Admin Accounts</h1>
                <p class="text-muted">Manage people who have access to this administrative portal.</p>
            </div>
            <button class="btn-add-user" onclick="openModal()">
                <i class="fas fa-plus"></i>
                Create New Account
            </button>
        </div>

        <div class="users-card">
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar" style="background: <?php echo '#' . substr(md5($user['username']), 0, 6); ?>20; color: <?php echo '#' . substr(md5($user['username']), 0, 6); ?>;">
                                        <?php echo strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                        <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($user['phone'] ?? 'No phone'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><code style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; color: #2563eb;"><?php echo htmlspecialchars($user['username']); ?></code></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="badge-admin">Administrator</span></td>
                            <td style="color: #94a3b8; font-size: 0.85rem;">
                                <?php echo isset($user['last_login']) ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-edit" title="Edit User" 
                                            onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" class="btn-action btn-delete" title="Delete User" onclick="return confirm('Are you sure you want to delete this account?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New Admin</h2>
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
            </div>
            <div style="margin-top: 30px;">
                <button type="submit" name="add_user" class="btn-add-user" style="width: 100%; justify-content: center;">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Admin Account</h2>
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
            </div>
            <div style="margin-top: 30px;">
                <button type="submit" name="edit_user" class="btn-add-user" style="width: 100%; justify-content: center; background: linear-gradient(135deg, #10b981, #059669);">
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('addUserModal').style.display = "block";
    }

    function openEditModal(user) {
        document.getElementById('edit_user_id').value = user.id;
        document.getElementById('edit_full_name').value = user.full_name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('editUserModal').style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Close modall when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
</script>

<?php include 'footer.php'; ?>
