<?php
/**
 * Admin authentication gate.
 *
 * Require this at the very top of EVERY file under admin/ that is not the login
 * page itself. Pages that render HTML get it transitively via admin/header.php;
 * standalone endpoints (AJAX handlers, form processors) must require it
 * directly — two of them did not, which meant anyone on the internet could
 * upload files and edit program records without logging in.
 */

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/security_headers.php';

if (!isset($_SESSION['admin_id'])) {
    // Answer JSON/XHR callers with a status code rather than a login page,
    // so the browser console shows the real reason.
    $wantsJson =
        (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
        || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

    if ($wantsJson) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Authentication required.']);
        exit;
    }

    // Send the visitor back where they came from after logging in. Only a
    // same-site path is kept — an absolute URL here would be an open redirect.
    $target = $_SERVER['REQUEST_URI'] ?? '';
    if ($target !== '' && $target[0] === '/' && strpos($target, '//') !== 0) {
        $_SESSION['login_redirect'] = $target;
    }

    $prefix = basename(dirname($_SERVER['SCRIPT_NAME'] ?? '')) === 'admin' ? '' : '/admin/';
    header('Location: ' . $prefix . 'login.php');
    exit;
}

/**
 * Reject state-changing requests that don't carry a valid CSRF token.
 * Call at the top of any admin POST handler:  vvu_require_csrf();
 */
function vvu_require_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    if (vvu_csrf_valid($_POST['csrf_token'] ?? null)) {
        return;
    }

    error_log('VVU: CSRF check failed on ' . ($_SERVER['REQUEST_URI'] ?? '?')
        . ' from ' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
    http_response_code(419);
    exit('Your session expired or the request could not be verified. Go back, reload the page and try again.');
}
