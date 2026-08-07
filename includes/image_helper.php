<?php
/**
 * Thumbnail helper.
 *
 * The CMS stores whatever the admin uploaded — often a 4000x6000, 3MB camera
 * original. Painting that into a ~150px card box makes the browser downscale
 * ~40x in a single bilinear pass, which is why those cards looked soft and
 * smeared. This generates a properly resampled thumbnail once, caches it on
 * disk next to the source, and serves that instead.
 */

if (!function_exists('vvu_thumb')) {

    /**
     * Returns a cached thumbnail path for an image, generating it on first use.
     * Falls back to the original path if anything goes wrong, so a card is
     * never left with a broken image.
     *
     * @param string $relPath Path relative to project root, e.g. uploads/programs/x.webp
     * @param int    $w       Target width in px
     * @param int    $h       Target height in px (image is cropped to fill, like object-fit: cover)
     * @return string Path to use in the src attribute
     */
    function vvu_thumb($relPath, $w = 400, $h = 450) {
        $relPath = trim((string) $relPath);
        if ($relPath === '' || preg_match('#^(https?:)?//#i', $relPath)) {
            return $relPath; // remote image — nothing we can do locally
        }

        $root = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $src  = $root . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relPath, '/'));

        if (!is_file($src) || !extension_loaded('gd')) {
            return $relPath;
        }

        $dir  = dirname($relPath);
        $base = pathinfo($relPath, PATHINFO_FILENAME);
        $ext  = function_exists('imagewebp') ? 'webp' : 'jpg';
        $relThumb = $dir . '/thumbs/' . $base . '_' . $w . 'x' . $h . '.' . $ext;
        $absThumb = $root . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relThumb);

        // Serve the cache unless the source has since been replaced
        if (is_file($absThumb) && filemtime($absThumb) >= filemtime($src)) {
            return $relThumb;
        }

        return vvu_make_thumb($src, $absThumb, $w, $h) ? $relThumb : $relPath;
    }

    /**
     * Writes a cropped, high-quality thumbnail. Returns true on success.
     */
    function vvu_make_thumb($src, $dest, $w, $h) {
        $info = @getimagesize($src);
        if (!$info) {
            return false;
        }

        switch ($info[2]) {
            case IMAGETYPE_JPEG: $img = @imagecreatefromjpeg($src); break;
            case IMAGETYPE_PNG:  $img = @imagecreatefrompng($src);  break;
            case IMAGETYPE_GIF:  $img = @imagecreatefromgif($src);  break;
            case IMAGETYPE_WEBP: $img = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($src) : false; break;
            default:             $img = false;
        }
        if (!$img) {
            return false;
        }

        $srcW = imagesx($img);
        $srcH = imagesy($img);

        // Crop box: the largest region of the source matching the target ratio
        // (equivalent to CSS object-fit: cover, centred).
        $scale = max($w / $srcW, $h / $srcH);
        $cropW = (int) round($w / $scale);
        $cropH = (int) round($h / $scale);
        $cropX = (int) round(($srcW - $cropW) / 2);
        $cropY = (int) round(($srcH - $cropH) / 2);

        // Halve repeatedly before the final resample. A single bilinear pass
        // from 6000px to 400px skips most source pixels and aliases badly;
        // successive halving averages them and keeps detail crisp.
        $stepW = $cropW;
        $stepH = $cropH;
        $step  = imagecrop($img, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
        imagedestroy($img);
        if (!$step) {
            return false;
        }

        while ($stepW / 2 >= $w && $stepH / 2 >= $h) {
            $halfW = (int) floor($stepW / 2);
            $halfH = (int) floor($stepH / 2);
            $half  = imagecreatetruecolor($halfW, $halfH);
            imagecopyresampled($half, $step, 0, 0, 0, 0, $halfW, $halfH, $stepW, $stepH);
            imagedestroy($step);
            $step  = $half;
            $stepW = $halfW;
            $stepH = $halfH;
        }

        $out = imagecreatetruecolor($w, $h);
        imagecopyresampled($out, $step, 0, 0, 0, 0, $w, $h, $stepW, $stepH);
        imagedestroy($step);

        $destDir = dirname($dest);
        if (!is_dir($destDir) && !@mkdir($destDir, 0777, true)) {
            imagedestroy($out);
            return false;
        }

        $ok = (pathinfo($dest, PATHINFO_EXTENSION) === 'webp')
            ? @imagewebp($out, $dest, 82)
            : @imagejpeg($out, $dest, 86);

        imagedestroy($out);
        return $ok;
    }
}
