<?php
/**
 * Hardened session start-up, shared by the login page and every admin page.
 *
 * Must be required BEFORE any output and BEFORE session_start(), because the
 * cookie parameters can only be set on a session that has not started yet.
 */

if (session_status() === PHP_SESSION_NONE) {
    $isHttps =
        (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');

    session_set_cookie_params([
        'lifetime' => 0,          // dies with the browser session
        'path'     => '/',
        'secure'   => $isHttps,   // HTTPS-only in production, still works on local XAMPP
        'httponly' => true,       // not readable from JavaScript — blunts XSS session theft
        'samesite' => 'Lax',      // blocks the cookie on cross-site POSTs (CSRF defence)
    ]);

    // Don't advertise "PHPSESSID"; don't let a caller pick the session id.
    session_name('VVUSESSID');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_start();
}

/**
 * Idle timeout. An admin session left open on a shared campus machine should
 * not stay valid indefinitely.
 */
const VVU_SESSION_IDLE_TIMEOUT = 3600; // 1 hour

if (isset($_SESSION['admin_id'])) {
    $lastSeen = $_SESSION['last_activity'] ?? time();
    if (time() - $lastSeen > VVU_SESSION_IDLE_TIMEOUT) {
        $_SESSION = [];
        session_destroy();
        session_start();
    } else {
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Return the CSRF token for this session, creating it on first use.
 */
function vvu_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Hidden form field carrying the CSRF token. Drop this inside every admin
 * <form method="post">.
 */
function vvu_csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(vvu_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * True when the submitted token matches the session's. Compared in constant
 * time so the check cannot be probed byte by byte.
 */
function vvu_csrf_valid(?string $submitted): bool {
    return !empty($_SESSION['csrf_token'])
        && is_string($submitted)
        && hash_equals($_SESSION['csrf_token'], $submitted);
}
