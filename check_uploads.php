<?php
/**
 * Upload readiness check.
 *
 * Run this ON THE LIVE SERVER when admin image uploads silently do nothing.
 * It reports which of the usual causes is actually in play, instead of leaving
 * you to guess.
 *
 * DELETE THIS FILE from the server once you have the answer — it reveals
 * server paths and configuration.
 */

require_once __DIR__ . '/includes/upload_helper.php';

$root  = __DIR__;
$rows  = [];
$fatal = 0;
$warn  = 0;

function row(&$rows, &$fatal, &$warn, $label, $ok, $detail, $fix = '') {
    // $ok: true = pass, false = fail, null = warning
    if ($ok === false) { $fatal++; }
    if ($ok === null)  { $warn++; }
    $rows[] = compact('label', 'ok', 'detail', 'fix');
}

// 1. PHP uploads enabled at all
row($rows, $fatal, $warn, 'File uploads enabled',
    (bool) ini_get('file_uploads'),
    ini_get('file_uploads') ? 'On' : 'Off',
    'Ask your host to set file_uploads = On');

// 2. Size limits
$umf = ini_get('upload_max_filesize');
$pms = ini_get('post_max_size');
$toBytes = function ($v) {
    $v = trim((string) $v);
    $unit = strtolower(substr($v, -1));
    $n = (int) $v;
    if ($unit === 'g') return $n * 1073741824;
    if ($unit === 'm') return $n * 1048576;
    if ($unit === 'k') return $n * 1024;
    return $n;
};
$umfBytes = $toBytes($umf);
row($rows, $fatal, $warn, 'upload_max_filesize',
    $umfBytes >= 8388608 ? true : null,
    $umf,
    $umfBytes >= 8388608 ? '' : 'Under 8MB — large photos will be rejected. See UPLOAD_LIMITS.md');
row($rows, $fatal, $warn, 'post_max_size',
    $toBytes($pms) >= $umfBytes ? true : null,
    $pms,
    $toBytes($pms) >= $umfBytes ? '' : 'Should be larger than upload_max_filesize');

// 3. fileinfo extension — the silent killer on shared hosting
$hasFileinfo = function_exists('finfo_open');
row($rows, $fatal, $warn, 'PHP "fileinfo" extension',
    $hasFileinfo ? true : null,
    $hasFileinfo ? 'Loaded' : 'MISSING',
    $hasFileinfo ? '' : 'Uploads now fall back to getimagesize(), but ask your host to enable fileinfo');

row($rows, $fatal, $warn, 'GD image library',
    extension_loaded('gd'),
    extension_loaded('gd') ? 'Loaded' : 'MISSING',
    extension_loaded('gd') ? '' : 'Needed to generate thumbnails; ask your host to enable it');

// 4. Temp directory
$tmp = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
row($rows, $fatal, $warn, 'Temp upload folder writable',
    is_writable($tmp),
    $tmp,
    is_writable($tmp) ? '' : 'PHP cannot stage uploads; contact your host');

// 5. Every upload target folder the admin actually writes to
$subdirs = ['programs', 'gallery', 'news', 'sliders', 'about', 'admissions',
            'academic', 'cms', 'discover', 'nav_featured'];

$uploadsDir = $root . DIRECTORY_SEPARATOR . 'uploads';
row($rows, $fatal, $warn, 'uploads/ exists',
    is_dir($uploadsDir), $uploadsDir,
    is_dir($uploadsDir) ? '' : 'Create the folder and set it to 755');
row($rows, $fatal, $warn, 'uploads/ writable',
    is_dir($uploadsDir) && is_writable($uploadsDir),
    is_dir($uploadsDir) ? (is_writable($uploadsDir) ? 'Writable' : 'NOT WRITABLE') : 'n/a',
    (is_dir($uploadsDir) && is_writable($uploadsDir)) ? '' : 'chmod 755 uploads (some hosts need 775)');

foreach ($subdirs as $sub) {
    $dir = $uploadsDir . DIRECTORY_SEPARATOR . $sub;
    if (!is_dir($dir)) {
        row($rows, $fatal, $warn, "uploads/$sub", null, 'Does not exist yet',
            'Will be created on first upload if uploads/ is writable');
        continue;
    }
    $writable = is_writable($dir);
    row($rows, $fatal, $warn, "uploads/$sub", $writable,
        $writable ? 'Writable' : 'NOT WRITABLE',
        $writable ? '' : "chmod 755 uploads/$sub");
}

// 6. Live write test
$testDir  = $uploadsDir . DIRECTORY_SEPARATOR . 'programs';
$writeOk  = false;
$writeMsg = 'Skipped (folder missing)';
if (is_dir($testDir)) {
    $testFile = $testDir . DIRECTORY_SEPARATOR . 'vvu_write_test_' . uniqid() . '.txt';
    if (@file_put_contents($testFile, 'test') !== false) {
        $writeOk = true;
        $writeMsg = 'Wrote and removed a test file successfully';
        @unlink($testFile);
    } else {
        $writeMsg = 'Could NOT write a test file';
    }
}
row($rows, $fatal, $warn, 'Actual write test (uploads/programs)', $writeOk, $writeMsg,
    $writeOk ? '' : 'This is why uploads are failing. Fix the folder permissions.');

$verdict = $fatal > 0
    ? ['Uploads will FAIL on this server', '#dc2626', $fatal . ' blocking problem(s) found — see the red rows.']
    : ($warn > 0
        ? ['Uploads should work, with caveats', '#d97706', $warn . ' warning(s) — see the amber rows.']
        : ['Everything looks correct', '#16a34a', 'No problems found. If uploads still fail, the admin panel will now tell you why.']);

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html><meta charset="utf-8"><title>Upload readiness check</title>
<style>
 body{font-family:system-ui,sans-serif;max-width:900px;margin:40px auto;padding:0 20px;color:#222;line-height:1.6}
 h1{color:#002147;margin-bottom:4px}
 table{border-collapse:collapse;width:100%;margin:24px 0;font-size:14px}
 th,td{border:1px solid #e2e8f0;padding:9px 12px;text-align:left;vertical-align:top}
 th{background:#f8fafc}
 .pass{color:#15803d;font-weight:700}
 .fail{color:#dc2626;font-weight:700}
 .warnc{color:#b45309;font-weight:700}
 tr.failrow{background:#fef2f2}
 tr.warnrow{background:#fffbeb}
 .verdict{padding:16px 20px;border-radius:10px;color:#fff;font-weight:700;font-size:17px}
 code{background:#f1f5f9;padding:2px 6px;border-radius:4px}
 .note{background:#fffbeb;border-left:4px solid #f0b429;padding:12px 16px;margin:24px 0}
</style>
<h1>Upload readiness check</h1>
<p style="color:#64748b;margin-top:0">PHP <?php echo PHP_VERSION; ?> &middot; <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'unknown server'); ?></p>

<div class="verdict" style="background:<?php echo $verdict[1]; ?>">
    <?php echo htmlspecialchars($verdict[0]); ?><br>
    <span style="font-weight:400;font-size:15px"><?php echo htmlspecialchars($verdict[2]); ?></span>
</div>

<table>
    <tr><th style="width:32%">Check</th><th style="width:10%">Result</th><th style="width:28%">Detail</th><th>What to do</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?php echo $r['ok'] === false ? 'failrow' : ($r['ok'] === null ? 'warnrow' : ''); ?>">
        <td><?php echo htmlspecialchars($r['label']); ?></td>
        <td class="<?php echo $r['ok'] === false ? 'fail' : ($r['ok'] === null ? 'warnc' : 'pass'); ?>">
            <?php echo $r['ok'] === false ? 'FAIL' : ($r['ok'] === null ? 'WARN' : 'OK'); ?>
        </td>
        <td><code><?php echo htmlspecialchars($r['detail']); ?></code></td>
        <td><?php echo htmlspecialchars($r['fix']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="note">
    <strong>Delete this file from the server</strong> once you have your answer — it exposes server paths and configuration.
</div>
