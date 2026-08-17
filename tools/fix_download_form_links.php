<?php
/**
 * Migration: point the Download Forms page at files that actually exist.
 *
 * The item_link column is free text typed into the admin panel, and the rows
 * for page_key = 'download_forms' were typed from memory rather than from the
 * uploads directory. Two kinds of breakage resulted:
 *
 *   1. Wrong folder — the forms live in "uploads/Download Forms/", but the
 *      rows said "uploads/<file>.pdf", or gave a bare filename with no folder
 *      at all (the French forms).
 *   2. Wrong filename — three rows named files that were never uploaded under
 *      that name (undergrad_app.pdf, nursing-requirements.pdf, medical_form.pdf).
 *
 * Kind 1 is repaired automatically by matching the basename against the files
 * on disk. Kind 2 needs a human decision, so it goes through $renames below.
 *
 * download-forms.php also resolves and percent-encodes links at render time,
 * so the page copes with a mistyped path on its own. This tool fixes the data
 * itself, so the admin panel shows the correct path too.
 *
 * CLI ONLY — it refuses to run over HTTP, so it is harmless even though it
 * lives inside the web root (it is also denied at the web-server level; see
 * .htaccess and nginx-vvu-security.conf).
 *
 * Usage, from the site directory:
 *     php tools/fix_download_form_links.php            # dry run — reports only
 *     php tools/fix_download_form_links.php --apply    # writes the changes
 *
 * Safe to re-run: a second pass finds nothing left to change.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db_connect.php';

$root  = dirname(__DIR__);
$apply = in_array('--apply', $argv, true);

// Directories searched, in order, for a row's basename.
$search_dirs = ['uploads/Download Forms', 'uploads'];

/**
 * Filenames that no file on disk matches, mapped to the document that was
 * plainly meant. Keyed by the basename as stored in the database.
 */
$renames = [
    // "Undergraduate Admission Form" — the only undergraduate form uploaded.
    'undergrad_app.pdf'        => 'uploads/Download Forms/undergraduate-admission-form-2019.pdf',
    // "Nursing Entry Requirements" — no requirements sheet was uploaded; the
    // Nursing & Midwifery advert is the document that carries them.
    'nursing-requirements.pdf' => 'uploads/Download Forms/Advert-Nursing-Midwifery-February-2019.pdf',
    // 'medical_form.pdf' is deliberately absent: no medical examination form
    // has been uploaded, so the row is reported and left for someone to
    // upload the real document.
];

/**
 * Case-insensitive lookup of a basename inside the search directories.
 * Windows is case-insensitive but the production host is not, so matching
 * loosely here and storing the on-disk spelling fixes casing drift too.
 */
function vvu_locate_form($name, array $search_dirs, $root)
{
    static $index = null;

    if ($index === null) {
        $index = [];
        foreach ($search_dirs as $dir) {
            foreach ((array) @scandir($root . '/' . $dir) as $entry) {
                if ($entry !== '.' && $entry !== '..' && is_file("$root/$dir/$entry")) {
                    // First directory listed wins; do not let uploads/ shadow
                    // the copy in "Download Forms/".
                    $index += [mb_strtolower($entry) => "$dir/$entry"];
                }
            }
        }
    }

    $key = mb_strtolower($name);

    return isset($index[$key]) ? $index[$key] : null;
}

echo ($apply ? "APPLYING changes to" : "DRY RUN against") . " database `{$vvu_db['name']}`\n\n";

$rows = $pdo->query(
    "SELECT id, section_key, item_title, item_link
       FROM academic_pages_items
      WHERE page_key = 'download_forms'
      ORDER BY section_key, display_order"
)->fetchAll(PDO::FETCH_ASSOC);

$changed = 0;
$missing = [];

foreach ($rows as $row) {
    $link = trim((string) $row['item_link']);

    if ($link === '') {
        continue;
    }

    // External destinations and internal pages are not file paths.
    if (preg_match('~^([a-z][a-z0-9+.-]*:|//)~i', $link) || preg_match('~\.php($|[?#])~i', $link)) {
        continue;
    }

    $path = ltrim(str_replace('\\', '/', $link), '/');
    $name = basename($path);

    if (is_file("$root/$path")) {
        continue; // Already correct.
    }

    $new = isset($renames[$name])
        ? $renames[$name]
        : vvu_locate_form($name, $search_dirs, $root);

    if ($new === null || !is_file("$root/$new")) {
        $missing[] = $row;
        echo "MISSING {$row['section_key']}/{$row['item_title']} (row {$row['id']})\n";
        echo "   link: $link — no such file under " . implode(' or ', $search_dirs) . "\n";
        continue;
    }

    echo ($apply ? "UPDATE  " : "WOULD   ") . "{$row['section_key']}/{$row['item_title']} (row {$row['id']})\n";
    echo "   old: $link\n";
    echo "   new: $new\n";

    if ($apply) {
        $up = $pdo->prepare("UPDATE academic_pages_items SET item_link = ? WHERE id = ?");
        $up->execute([$new, $row['id']]);
    }

    $changed++;
}

echo "\n" . ($apply ? "Updated" : "Would update") . " $changed link(s).\n";

if ($missing) {
    echo count($missing) . " link(s) have no file on disk. The page renders these as\n"
       . "\"Coming Soon\" instead of a broken download. Upload the document to\n"
       . "\"uploads/Download Forms/\", then set the item's Link / File Path in\n"
       . "admin/manage_resources_pages.php.\n";
}

if (!$apply && $changed) {
    echo "\nNothing was written. Re-run with --apply to commit these changes.\n";
}
