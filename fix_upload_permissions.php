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

$apply     = isset($_GET['apply']);
$repair    = isset($_GET['repair']);
$confirmed = isset($_GET['confirm']);
$rows  = [];
$stillBroken = 0;
$repairLog = [];

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

/**
 * Rebuilds a folder PHP cannot chmod (because another user owns it).
 *
 * chmod() requires ownership, so a root-owned folder is untouchable from PHP.
 * But creating, renaming and deleting an ENTRY inside a directory is governed
 * by write permission on the PARENT — and uploads/ is owned by the PHP user.
 * So: move the stubborn folder aside, make a fresh one (now owned by PHP),
 * and copy the contents across. Nothing is ever deleted; the original is kept
 * as <name>.old-<timestamp> for you to remove.
 */
function rebuildFolder($path, &$note) {
    $parent = dirname($path);

    if (!is_writable($parent)) {
        $note = 'Parent folder is not writable — cannot rebuild.';
        return false;
    }
    // Sticky bit on the parent would stop us renaming another user's entry
    if (fileperms($parent) & 01000) {
        $note = 'Parent has the sticky bit set — cannot rebuild.';
        return false;
    }

    $backup = $path . '.old-' . date('YmdHis');
    if (!@rename($path, $backup)) {
        $note = 'Could not move the old folder aside.';
        return false;
    }

    if (!@mkdir($path, 0755, true)) {
        @rename($backup, $path);   // put it back
        $note = 'Could not create the replacement folder.';
        return false;
    }

    // Copy the old contents into the new folder
    $copied = 0;
    $failed = 0;
    $items = @scandir($backup) ?: [];
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $from = $backup . DIRECTORY_SEPARATOR . $item;
        $to   = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($from)) {
            // Nested folders are rare here; copy one level recursively
            @mkdir($to, 0755, true);
            foreach ((@scandir($from) ?: []) as $sub) {
                if ($sub === '.' || $sub === '..') continue;
                @copy($from . DIRECTORY_SEPARATOR . $sub, $to . DIRECTORY_SEPARATOR . $sub)
                    ? $copied++ : $failed++;
            }
        } else {
            @copy($from, $to) ? $copied++ : $failed++;
        }
    }

    if (!reallyWritable($path)) {
        $note = 'Rebuilt, but the new folder still is not writable.';
        return false;
    }

    // If the old folder was empty we can tidy it away completely; if it held
    // files we cannot delete them (they belong to the other user), so it stays.
    $leftover = true;
    if (count(@scandir($backup) ?: []) <= 2) {
        $leftover = !@rmdir($backup);
    }

    $note = $copied > 0 ? "Rebuilt, {$copied} file(s) copied" : 'Rebuilt (was empty)';
    if ($failed > 0)  $note .= ", {$failed} could not be copied";
    if ($leftover)    $note .= '. Old copy left at ' . basename($backup);

    return true;
}

// ---- Optional repair pass, before the report is built ----
if ($repair && $confirmed) {
    foreach ($subdirs as $sub) {
        $path = $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $sub;
        if (!is_dir($path) || reallyWritable($path)) {
            continue;
        }
        $note = '';
        $ok = rebuildFolder($path, $note);
        clearstatcache();
        $repairLog[] = ['uploads/' . $sub, $ok, $note];
    }
}

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

<?php if ($repairLog): ?>
<h3>Rebuild results</h3>
<table>
    <tr><th style="width:28%">Folder</th><th style="width:10%">Result</th><th>Detail</th></tr>
    <?php foreach ($repairLog as $r): ?>
    <tr class="<?php echo $r[1] ? 'good' : 'bad'; ?>">
        <td><code><?php echo htmlspecialchars($r[0]); ?></code></td>
        <td class="<?php echo $r[1] ? 'ok' : 'bad2'; ?>"><?php echo $r[1] ? 'FIXED' : 'FAILED'; ?></td>
        <td><?php echo htmlspecialchars($r[2]); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if ($apply && $stillBroken > 0 && !$confirmed): ?>
<h3>Option A — rebuild the folders from PHP (no SSH needed)</h3>
<p>PHP cannot change the permissions of a folder another user owns. It <em>can</em>,
however, replace it: <code>uploads/</code> belongs to
<code><?php echo htmlspecialchars($phpUser); ?></code>, and creating or renaming
an entry inside a folder depends on the parent folder, not on the entry.</p>
<p>So each stubborn folder is moved aside to <code>&lt;name&gt;.old-&lt;timestamp&gt;</code>,
recreated fresh (owned by <code><?php echo htmlspecialchars($phpUser); ?></code>),
and its contents copied over. <strong>Nothing is deleted</strong> — the original
is kept so you can check it and remove it later.</p>
<p><a class="btn" href="?apply=1&amp;repair=1&amp;confirm=1"
      onclick="return confirm('Rebuild the folders PHP cannot modify? The originals are kept as .old-... copies.');">
   Rebuild the failed folders</a></p>

<h3>Option B — fix it properly over SSH (recommended if you have access)</h3>
<p>Root-owned folders usually mean a deploy was run with <code>sudo</code>.
One command fixes it at the source:</p>
<pre><code>sudo chown -R <?php echo htmlspecialchars($phpUser); ?>:<?php echo htmlspecialchars($phpUser); ?> /path/to/your/site/uploads
sudo find /path/to/your/site/uploads -type d -exec chmod 755 {} \;
sudo find /path/to/your/site/uploads -type f -exec chmod 644 {} \;</code></pre>
<p>To stop it recurring, pull as your normal user rather than with
<code>sudo</code>, or re-run the <code>chown</code> after each deploy.</p>
<?php endif; ?>

<?php if ($apply && $stillBroken > 0 && $confirmed): ?>
<h3>Still failing?</h3>
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
