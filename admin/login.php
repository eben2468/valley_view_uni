<?php
require_once('../includes/session_bootstrap.php');
require_once('../includes/security_headers.php');
require_once('../includes/db_connect.php');

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Brute-force policy: after MAX_ATTEMPTS consecutive failures an account is
// locked for LOCKOUT_MINUTES. Tracked per account in admin_users, so it holds
// across sessions and source IPs (see tools/security_migration.sql).
const MAX_ATTEMPTS     = 5;
const LOCKOUT_MINUTES  = 15;

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!vvu_csrf_valid($_POST['csrf_token'] ?? null)) {
        $error = "Your session expired. Please try again.";
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        // The lock comparison is done by MySQL, not PHP.
        //
        // Doing it in PHP with strtotime($row['locked_until']) > time() is a
        // trap: PHP and MySQL frequently run in different timezones (this
        // project's XAMPP has PHP on Europe/Berlin and MySQL 2 hours behind),
        // and the mismatch silently makes every lockout look already-expired.
        // Comparing against NOW() inside the query keeps both sides in the
        // database's own clock.
        try {
            $stmt = $pdo->prepare(
                "SELECT *,
                        (locked_until IS NOT NULL AND locked_until > NOW()) AS is_locked,
                        GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), locked_until)) AS lock_seconds_left
                   FROM admin_users
                  WHERE username = ?"
            );
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
        } catch (PDOException $e) {
            // Lockout columns missing — tools/security_migration.sql not run yet.
            error_log('VVU: lockout columns missing, run tools/security_migration.sql — ' . $e->getMessage());
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
        }

        if (!empty($admin['is_locked'])) {
            $minutesLeft = max(1, (int)ceil((int)$admin['lock_seconds_left'] / 60));
            $error = "Too many failed attempts. Try again in {$minutesLeft} minute(s).";
            error_log("VVU admin login blocked (locked account '{$username}') from " . ($_SERVER['REMOTE_ADDR'] ?? '?'));
        } elseif ($admin && password_verify($password, $admin['password'])) {
            // Rotate the session id on privilege change — defeats session fixation.
            session_regenerate_id(true);

            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name']     = $admin['full_name'];
            $_SESSION['last_activity']  = time();

            // Re-hash if PHP's default cost/algorithm has moved on since signup.
            if (password_needs_rehash($admin['password'], PASSWORD_DEFAULT)) {
                $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?")
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
            }

            try {
                $pdo->prepare("UPDATE admin_users SET failed_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?")
                    ->execute([$admin['id']]);
            } catch (PDOException $e) {
                error_log('VVU: login bookkeeping failed, run tools/security_migration.sql — ' . $e->getMessage());
            }

            header("Location: index.php");
            exit();
        } else {
            if ($admin) {
                try {
                    $attempts = (int)($admin['failed_attempts'] ?? 0) + 1;
                    if ($attempts >= MAX_ATTEMPTS) {
                        $pdo->prepare("UPDATE admin_users SET failed_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?")
                            ->execute([$attempts, LOCKOUT_MINUTES, $admin['id']]);
                    } else {
                        $pdo->prepare("UPDATE admin_users SET failed_attempts = ? WHERE id = ?")
                            ->execute([$attempts, $admin['id']]);
                    }
                } catch (PDOException $e) {
                    error_log('VVU: lockout tracking failed, run tools/security_migration.sql — ' . $e->getMessage());
                }
            } else {
                // Spend comparable time on unknown usernames so response timing
                // does not reveal which accounts exist.
                password_verify($password, '$2y$10$usesomesillystringforsalt0000000000000000000000000000000000');
            }

            error_log("VVU admin login failed for '{$username}' from " . ($_SERVER['REMOTE_ADDR'] ?? '?'));
            // Deliberately generic — never reveal whether the username existed.
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Login - Valley View University</title>
    <!-- META TAGS -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- FAV ICON(BROWSER TAB ICON) -->
    <link rel="shortcut icon" href="../images/fav.ico" type="image/x-icon">
    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700%7CJosefin+Sans:600,700" rel="stylesheet">
    <!-- ALL CSS FILES -->
    <link href="../Education-Website-and-AdminPanel/css/font-awesome.min.css" rel="stylesheet">
    <!-- MODERN LOGIN CSS -->
    <link href="login-modern.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <section>
		<div class="ad-log-main">
            <!-- Left Side: Branding & Image -->
            <div class="login-left">
                <div class="login-left-content">
                    <div class="vvu-badge">Valley View University</div>
                    <h1>Excellence, Integrity, Service.</h1>
                    <p>Welcome to the official administrative portal. Manage academic programs, faculty details, and university resources with ease and precision.</p>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="login-right">
                <div class="ad-log-in">
                    <div class="ad-log-in-logo">
                        <a href="../index.php"><img src="../vvu_logo.jpg" alt="VVU Logo"></a>
                    </div>
                    <div class="ad-log-in-con">
                        <div class="log-in-pop-right">
                            <h4>Admin Login</h4>
                            <p>Enter your credentials to access the admin panel.</p>
                            
                            <?php if ($error): ?>
                                <div class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <?php echo vvu_csrf_field(); ?>
                                <div class="input-field">
                                    <input type="text" name="username" id="username" placeholder=" " required autocomplete="username">
                                    <label for="username">Username</label>
                                </div>
                                
                                <div class="input-field">
                                    <input type="password" name="password" id="password" placeholder=" " required autocomplete="current-password">
                                    <label for="password">Password</label>
                                </div>
                                
                                <div class="form-options">
                                    <div class="log-ch-bx">
                                        <input type="checkbox" id="remember" name="remember">
                                        <label for="remember">Remember me</label>
                                    </div>
                                    <a href="#" class="forgot-pass">Forgot password?</a>
                                </div>
                                
                                <div class="log-in-btn-wrap">
                                    <button type="submit" class="log-in-btn">
                                        Login to Dashboard
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
		</div>
   </section>

    <!--Import jQuery before materialize.js-->
    <script src="../Education-Website-and-AdminPanel/js/main.min.js"></script>
    <script src="../Education-Website-and-AdminPanel/js/bootstrap.min.js"></script>
    <script src="../Education-Website-and-AdminPanel/js/materialize.min.js"></script>
    <script src="../Education-Website-and-AdminPanel/js/custom.js"></script>
</body>

</html>
