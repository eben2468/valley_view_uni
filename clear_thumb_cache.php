<?php
/**
 * Clears generated thumbnails.
 *
 * Thumbnails under uploads/<dir>/thumbs/ are cached and reused while they look
 * newer than their source image. Git does not preserve file timestamps, so
 * after a pull a stale thumbnail can appear newer than the image it was made
 * from — and the old picture keeps showing. Deleting the cache forces every
 * thumbnail to be rebuilt on the next page load.
 *
 * Safe: only touches files inside a `thumbs/` folder. Originals are untouched.
 *
 * Usage:  php clear_thumb_cache.php
 *     or: https://your-site/clear_thumb_cache.php
 */

$root   = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
$isCli  = (php_sapi_name() === 'cli');
$removed = 0;
$freed   = 0;
$failed  = [];

if (is_dir($root)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        // Only files that live inside a directory literally named "thumbs"
        if (basename($file->getPath()) !== 'thumbs') {
            continue;
        }

        $size = $file->getSize();
        if (@unlink($file->getPathname())) {
            $removed++;
            $freed += $size;
        } else {
            $failed[] = $file->getPathname();
        }
    }
}

$writable = is_dir($root) && is_writable($root);
$freedKb  = round($freed / 1024, 1);

if ($isCli) {
    echo "Deleted $removed cached thumbnail(s), freed {$freedKb} KB\n";
    if ($failed) {
        echo "\nCould not delete " . count($failed) . " file(s) — check permissions:\n";
        foreach (array_slice($failed, 0, 10) as $f) {
            echo "  $f\n";
        }
    }
    echo $writable
        ? "uploads/ is writable — thumbnails will rebuild on the next page load.\n"
        : "WARNING: uploads/ is NOT writable. Thumbnails cannot be regenerated;\n"
        . "the site will fall back to full-size originals (slower, but still visible).\n"
        . "Set the folder to 755 (or 775) on the server.\n";
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>Thumbnail cache cleared</title>';
    echo '<style>body{font-family:system-ui,sans-serif;max-width:700px;margin:40px auto;padding:0 20px;line-height:1.6;color:#222}
          h1{color:#002147} code{background:#f1f5f9;padding:2px 6px;border-radius:4px}
          .ok{color:#15803d;font-weight:600} .warn{background:#fef2f2;border-left:4px solid #dc2626;padding:12px 16px;margin:20px 0}
          .note{background:#f0fdf4;border-left:4px solid #16a34a;padding:12px 16px;margin:20px 0}</style>';
    echo '<h1>Thumbnail cache cleared</h1>';
    echo '<p class="ok">Deleted ' . $removed . ' cached thumbnail(s), freed ' . $freedKb . ' KB.</p>';

    if ($failed) {
        echo '<div class="warn"><strong>Could not delete ' . count($failed) . ' file(s).</strong> '
           . 'The web server user may not own them. Delete <code>uploads/*/thumbs/</code> over FTP instead.</div>';
    }

    echo $writable
        ? '<div class="note"><code>uploads/</code> is writable. Reload the homepage and thumbnails will rebuild automatically.</div>'
        : '<div class="warn"><strong><code>uploads/</code> is not writable.</strong> Thumbnails cannot be regenerated, '
        . 'so pages will fall back to full-size originals — still correct, just heavier. '
        . 'Set the folder permissions to 755 (or 775) on the server.</div>';

    echo '<p><strong>Delete this file from the server when you are done.</strong></p>';
}
