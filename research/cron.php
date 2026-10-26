<?php
/**
 * VVU Scholar — scheduled update (new papers from OpenAlex / ORCID / Google
 * Scholar, impact figures, clean-up). See admin/includes/research_autosync.php.
 *
 * Run it from a cron job, either way:
 *
 *   php /path/to/site/research/cron.php
 *   wget -q -O - "https://vvu.edu.gh/research/cron.php?key=<auto_sync_key>"
 *
 * The key is shown in Admin → Research Portal → Data Quality & Sync. Over
 * HTTP without the right key the script does nothing.
 */

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_helper.php';

$isCli = PHP_SAPI === 'cli';
if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Robots-Tag: noindex');
    $key = (string) r_set($pdo, 'auto_sync_key', '');
    if ($key === '' || !hash_equals($key, (string) ($_GET['key'] ?? ''))) {
        http_response_code(403);
        exit("Forbidden.\n");
    }
}

require_once __DIR__ . '/../admin/includes/research_autosync.php';

$force  = $isCli ? in_array('--force', $argv ?? [], true) : !empty($_GET['force']);
if (!$force && !vvur_sync_due($pdo)) {
    echo "Not due yet (or automatic updates are switched off). Use --force / &force=1 to run anyway.\n";
    exit;
}

$result = vvur_auto_sync($pdo, 'cron', $isCli ? 600 : 280);
echo ($result['ok'] ? 'OK: ' : 'FAILED: ') . $result['summary'] . "\n";
