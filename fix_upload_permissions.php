<?php
/**
 * Makes the uploads/ folders writable by PHP.
 *
 * Git does not record folder permissions, so after a clone or pull on the live
 * server the uploads/ directories often end up owned or permissioned such that
 * PHP cannot write into them. Admin image uploads then fail.
 *
 * This walks every folder the admin panel writes to, tries progressively more
 * permissive modes until PHP can actually write a file, and reports the result.
 *
 * Requires an admin login (it changes filesystem permissions), so sign in to
 * the admin panel first in the same browser.
 *
 * DELETE THIS FILE from the server once uploads work.
 */

session_start();

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo '<!doctype html><meta charset="utf-8"><title>Not allowed</title>'
       . '<p style="font-family:system-ui,sans-serif;max-width:640px;margin:60px auto">'
       . 'Please <a href="admin/login.php">sign in to the admin panel</a> first, then reload this page. '
       . 'This tool changes folder permissions, so it is restricted to logged-in administrators.</p>';
    exit;
}

$root = __DIR__;

// Every folder referenced by handleAdminFileUpload() across the codebase
$subdirs = [
    'about', 'academic', 'admissions', 'alumni', 'campus_life', 'cms', 'contact',
    'directory', 'discover', 'encyclopedia', 'faqs', 'gallery', 'graduate',
    'history', 'info', 'nav_featured', 'news', 'policies', 'programs',
    'resources', 'settings', 'sliders', 'stats', 'strategy', 'ventures',
];

$apply = isset($_GET['apply']);
$rows  = [];
$stillBroken = 0;

/** Can PHP genuinely create a file here? The only test that matters. */
function reallyWritable($dir) {
    if (!is_dir($dir)) return false;
    $probe = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '.vvu_probe_' . uniqid();
    if (@file_put_contents($probe, 'x') === false) return false;
    @unlink($probe);
    return true;
}

function permString($path) {
    return is_dir($path) ? substr(sprintf('%o', fileperms($path)), -4) : '----';
}

function ownerName($path) {
    if (!is_dir($path)) return 'n/a';
    if (function_exists('posix_getpwuid')) {
        $info = @posix_getpwuid(@fileowner($path));
        if (!empty($info['name'])) return $info['name'];
    }
    return (string) @fileowner($path);
}

// Who is PHP running as?
$phpUser = function_exists('posix_geteuid') && function_exists('posix_getpwuid')
    ? (@posix_getpwuid(@posix_geteuid())['name'] ?? 'unknown')
    : (get_current_user() ?: 'unknown');

$targets = array_merge([''], $subdirs);   // '' = uploads/ itself

foreach ($targets as $sub) {
    $path  = $root . DIRECTORY_SEPARATOR . 'uploads' . ($sub === '' ? '' : DIRECTORY_SEPARATOR . $sub);
    $label = 'uploads' . ($sub === '' ? '/' : '/' . $sub);

    $created = false;
    if (!is_dir($path)) {
        if (!$apply) {
            $rows[] = [$label, '—', '—', 'missing', 'Will be created', null];
            continue;
        }
        $created = @mkdir($path, 0755, true);
        if (!$created) {
            $rows[] = [$label, '—', '—', 'MISSING', 'Could not create — fix uploads/ first', false];
            $stillBroken++;
            continue;
        }
    }

    $before = permString($path);
    $owner  = ownerName($path);

    if (reallyWritable($path)) {
        $rows[] = [$label, $before, $owner, 'OK', $created ? 'Created' : 'Already writable', true];
        continue;
    }

    if (!$apply) {
        $rows[] = [$label, $before, $owner, 'NOT WRITABLE', 'Will try 755, then 775, then 777', false];
        $stillBroken++;
        continue;
    }

    // Escalate only as far as actually needed
    $fixedWith = null;
    foreach ([0755, 0775, 0777] as $mode) {
        @chmod($path, $mode);
        clearstatcache(true, $path);
        if (reallyWritable($path)) {
            $fixedWith = $mode;
            break;
        }
    }

    if ($fixedWith !== null) {
        $rows[] = [$label, permString($path), $owner, 'FIXED',
                   'Now writable (' . decoct($fixedWith) . ')', true];
    } else {
        $rows[] = [$label, permString($path), $owner, 'FAILED',
                   'PHP cannot chmod this folder — owned by another user', false];
        $stillBroken++;
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html><meta charset="utf-8"><title>Fix upload permissions</title>
<style>
 body{font-family:system-ui,sans-serif;max-width:920px;margin:40px auto;padding:0 20px;color:#222;line-height:1.6}
 h1{color:#002147;margin-bottom:2px}
 table{border-collapse:collapse;width:100%;margin:22px 0;font-size:14px}
 th,td{border:1px solid #e2e8f0;padding:8px 11px;text-align:left}
 th{background:#f8fafc}
 tr.bad{background:#fef2f2} tr.good{background:#f0fdf4}
 .ok{color:#15803d;font-weight:700} .bad2{color:#dc2626;font-weight:700} .warn{color:#b45309;font-weight:700}
 code{background:#f1f5f9;padding:2px 6px;border-radius:4px}
 .btn{display:inline-block;background:#1d4ed8;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:700}
 .banner{padding:16px 20px;border-radius:10px;color:#fff;font-weight:700;margin:20px 0}
 .note{background:#fffbeb;border-left:4px solid #f0b429;padding:12px 16px;margin:22px 0}
</style>

<h1>Fix upload permissions</h1>
<p style="color:#64748b;margin-top:0">
    PHP is running as <code><?php echo htmlspecialchars($phpUser); ?></code>
    &middot; <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'unknown server'); ?>
</p>

<?php if (!$apply): ?>
    <div class="banner" style="background:#334155">
        Preview only — nothing has been changed yet.
    </div>
    <p><a class="btn" href="?apply=1">Fix permissions now</a></p>
<?php elseif ($stillBroken === 0): ?>
    <div class="banner" style="background:#16a34a">
        All upload folders are writable. Go and try the image upload again.
    </div>
<?php else: ?>
    <div class="banner" style="background:#dc2626">
        <?php echo $stillBroken; ?> folder(s) could not be fixed from PHP — see below.
    </div>
<?php endif; ?>

<table>
    <tr><th>Folder</th><th>Mode</th><th>Owner</th><th>Status</th><th>Detail</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?php echo $r[5] === true ? 'good' : ($r[5] === false ? 'bad' : ''); ?>">
        <td><code><?php echo htmlspecialchars($r[0]); ?></code></td>
        <td><code><?php echo htmlspecialchars($r[1]); ?></code></td>
        <td><?php echo htmlspecialchars($r[2]); ?></td>
        <td class="<?php echo $r[5] === true ? 'ok' : ($r[5] === false ? 'bad2' : 'warn'); ?>"><?php echo htmlspecialchars($r[3]); ?></td>
        <td><?php echo htmlspecialchars($r[4]); ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if ($apply && $stillBroken > 0): ?>
<h3>If PHP could not fix it</h3>
<p>That means the folders are owned by a different user than the one PHP runs as
(<code><?php echo htmlspecialchars($phpUser); ?></code>), so PHP is not allowed to change them. Do it directly:</p>

<p><strong>cPanel &rarr; File Manager</strong> — open <code>uploads</code>, select all folders,
<em>Permissions</em>, set to <strong>755</strong>, tick <em>Recurse into subdirectories</em>
and <em>apply to directories only</em>. If it still fails, use <strong>775</strong>.</p>

<p><strong>FTP (FileZilla)</strong> — right-click <code>uploads</code> &rarr; <em>File permissions</em> &rarr;
<code>755</code>, tick <em>Recurse into subdirectories</em> &rarr; <em>Apply to directories only</em>.</p>

<p><strong>SSH</strong>:</p>
<pre><code>find uploads -type d -exec chmod 755 {} \;
find uploads -type f -exec chmod 644 {} \;</code></pre>

<p>If 755 is not enough, your host runs PHP as a separate user (often
<code>www-data</code> or <code>nobody</code>). Then either set the folders to
<strong>775</strong> and make that user the group owner, or ask support to
<em>"make the uploads directory writable by the PHP user"</em>.</p>
<?php endif; ?>

<div class="note">
    <strong>Delete this file from the server once uploads work.</strong>
    It can change folder permissions and should not stay on a live site.
</div>
