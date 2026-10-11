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
        if ($relPath !== '' && function_exists('vvu_is_google_image') && vvu_is_google_image($relPath)) {
            // Google Photos resizes and crops on its side.
            return vvu_google_image_base($relPath) . '=w' . (int) $w . '-h' . (int) $h . '-c';
        }
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

/**
 * Google Photos images.
 *
 * Editors can paste a Google Photos link instead of uploading a file. What
 * is stored is the bare image URL (lh3.googleusercontent.com/…, no size
 * suffix); pages then ask Google for exactly the size they need, so a
 * hotlinked photo is as light as a resized upload.
 */
if (!function_exists('vvu_is_google_image')) {

    /** True for an image served by Google Photos (lh3.googleusercontent.com…). */
    function vvu_is_google_image($url)
    {
        $host = strtolower((string) parse_url(trim((string) $url), PHP_URL_HOST));
        return $host !== '' && preg_match('/(^|\.)(googleusercontent\.com|ggpht\.com)$/', $host) === 1;
    }

    /** A Google Photos image URL without its size suffix ("…=w600-h315-p-k"). */
    function vvu_google_image_base($url)
    {
        $url = trim((string) $url);
        $url = preg_replace('/[?#].*$/', '', $url);
        // The size options follow the last "=" in the final path segment.
        return preg_replace('#(/[^/=]+)=[^/]*$#', '$1', $url);
    }

    /**
     * src="" for an image shown up to $w pixels wide. Google Photos images
     * are requested at that width; anything else is returned unchanged.
     */
    function vvu_img_src($path, $w = 1600)
    {
        $path = trim((string) $path);
        if ($path !== '' && vvu_is_google_image($path)) {
            return vvu_google_image_base($path) . '=w' . max(1, (int) $w);
        }
        return $path;
    }

    /** src="" for an admin preview: Google images small, local paths from admin/. */
    function vvu_admin_img_preview($path, $w = 400)
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (vvu_is_google_image($path)) {
            return vvu_img_src($path, $w);
        }
        return preg_match('#^(https?:)?//#i', $path) ? $path : '../' . ltrim($path, '/');
    }

    /**
     * Turns what an editor pasted into something an <img> can show.
     *
     *   uploads/… or any non-Google image URL              -> kept as is
     *   lh3.googleusercontent.com/… ("Copy image address")  -> size suffix removed
     *   photos.app.goo.gl/… or photos.google.com/share/…/photo/… (a single
     *   photo's share link) -> the page is fetched and its preview image used
     *
     * Returns the value to store, or null with $error set.
     */
    function vvu_resolve_image_link($input, &$error = null)
    {
        $error = null;
        $url   = trim((string) $input);
        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            return $url; // local path or empty
        }
        if (vvu_is_google_image($url)) {
            return vvu_google_image_base($url);
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (!in_array($host, ['photos.app.goo.gl', 'photos.google.com', 'goo.gl'], true)) {
            return $url; // some other image host
        }

        if (!function_exists('curl_init')) {
            $error = 'This server cannot open Google Photos links. In Google Photos, right-click the photo and choose "Copy image address" instead.';
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 5,
            CURLOPT_CONNECTTIMEOUT  => 8,
            CURLOPT_TIMEOUT         => 15,
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
            CURLOPT_HTTPHEADER      => ['Accept-Language: en'],
        ]);
        $html  = curl_exec($ch);
        $final = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($html === false || $code >= 400) {
            $error = 'Could not open that Google Photos link. Check that the album is shared ("Share" → "Create link").';
            return null;
        }
        if (strtolower((string) parse_url($final, PHP_URL_HOST)) !== 'photos.google.com') {
            $error = 'That link does not lead to a shared Google Photos photo. Make sure the album is shared by link.';
            return null;
        }
        if (strpos((string) parse_url($final, PHP_URL_PATH), '/photo/') === false) {
            $error = 'That is a link to a whole album, not one photo. Open a single photo and copy that photo\'s link (or right-click it → "Copy image address").';
            return null;
        }
        if (preg_match('#<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']#i', $html, $m)
            || preg_match('#<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']#i', $html, $m)) {
            $img = html_entity_decode($m[1], ENT_QUOTES);
            if (vvu_is_google_image($img)) {
                return vvu_google_image_base($img);
            }
        }
        $error = 'Could not find the picture behind that link. In Google Photos, right-click the photo and choose "Copy image address", then paste that.';
        return null;
    }
}
