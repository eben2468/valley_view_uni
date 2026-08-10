<?php
// session_bootstrap (not admin_auth) — logging out must not require being
// logged in, and it must read the same VVUSESSID session everything else uses.
require_once __DIR__ . '/../includes/session_bootstrap.php';

// Clear the data, then the cookie, then the session itself. session_destroy()
// on its own leaves the cookie in the browser pointing at a dead session.
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

header("Location: login.php");
exit();
