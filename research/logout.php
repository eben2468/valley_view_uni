<?php
/**
 * VVU Scholar — researcher sign-out. POST only, so a link elsewhere cannot
 * sign someone out.
 */

require_once __DIR__ . '/includes/research_account.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ra_check_csrf();
    ra_logout();
    session_regenerate_id(true);
    ra_flash('ok', 'You have signed out.');
}
header('Location: login.php');
exit;
