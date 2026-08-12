<?php
/**
 * E-Books QR Code
 *
 * Serves a QR code that encodes the sign-in signpost URL rather than the raw
 * Google Drive link, so a phone scan lands on the "use your student account"
 * explainer first. The Drive folder itself is what enforces access.
 *
 * The PNG is generated once and cached under uploads/Library/qr-cache/, keyed by
 * the encoded URL, so the site keeps working offline after the first render.
 * If generation fails and no cache exists, the static QR image stored in the CMS
 * is served as a fallback.
 */
require_once 'includes/db_connect.php';
require_once 'includes/ebook_access.php';

$resource_key = isset($_GET['r']) ? preg_replace('/[^a-z0-9_\-]/i', '', $_GET['r']) : 'ebooks';
if ($resource_key === '' || !vvu_ebook_resource($resource_key, $pdo)) {
    $resource_key = 'ebooks';
}

$size = isset($_GET['size']) ? (int)$_GET['size'] : 600;
$size = max(200, min(1000, $size));

$target = vvu_ebook_gate_absolute_url($resource_key);

$cache_dir  = __DIR__ . '/uploads/Library/qr-cache';
$cache_file = $cache_dir . '/' . md5($target . '|' . $size) . '.png';

function vvu_qr_send_png($bytes) {
    header('Content-Type: image/png');
    header('Content-Length: ' . strlen($bytes));
    header('Cache-Control: public, max-age=86400');
    echo $bytes;
    exit;
}

// 1. Cached copy.
if (is_file($cache_file) && filesize($cache_file) > 0) {
    vvu_qr_send_png(file_get_contents($cache_file));
}

// 2. Generate it.
$api = 'https://api.qrserver.com/v1/create-qr-code/?'
     . http_build_query([
         'size'    => $size . 'x' . $size,
         'margin'  => 8,
         'ecc'     => 'M',
         'format'  => 'png',
         'data'    => $target,
     ]);

$png = false;
if (function_exists('curl_init')) {
    $ch = curl_init($api);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response !== false && $status === 200 && strncmp($response, "\x89PNG", 4) === 0) {
        $png = $response;
    }
}
if ($png === false && ini_get('allow_url_fopen')) {
    $response = @file_get_contents($api, false, stream_context_create([
        'http' => ['timeout' => 12],
    ]));
    if ($response !== false && strncmp($response, "\x89PNG", 4) === 0) {
        $png = $response;
    }
}

if ($png !== false) {
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }
    if (is_dir($cache_dir) && is_writable($cache_dir)) {
        @file_put_contents($cache_file, $png);
    }
    vvu_qr_send_png($png);
}

// 3. Generation failed. Draw a local placeholder rather than falling back to the
//    old static QR image — that one encodes an out-of-date Google Drive link.
if (function_exists('imagecreatetruecolor')) {
    $img = imagecreatetruecolor($size, $size);
    $bg     = imagecolorallocate($img, 255, 255, 255);
    $border = imagecolorallocate($img, 203, 213, 225);
    $ink    = imagecolorallocate($img, 71, 85, 105);
    imagefilledrectangle($img, 0, 0, $size, $size, $bg);
    imagerectangle($img, 4, 4, $size - 5, $size - 5, $border);

    $lines = ['QR code unavailable', 'Use the "Access E-Books', 'Collection" button'];
    $font = 5;
    $line_h = imagefontheight($font) + 8;
    $y = (int)(($size - (count($lines) * $line_h)) / 2);
    foreach ($lines as $line) {
        $x = (int)(($size - imagefontwidth($font) * strlen($line)) / 2);
        imagestring($img, $font, max(6, $x), $y, $line, $ink);
        $y += $line_h;
    }

    ob_start();
    imagepng($img);
    imagedestroy($img);
    vvu_qr_send_png(ob_get_clean());
}

http_response_code(404);
header('Content-Type: text/plain');
echo 'QR code unavailable.';
