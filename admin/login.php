<?php
session_start();
require_once('../includes/db_connect.php');

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password";
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
                                <div class="error-msg"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
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
