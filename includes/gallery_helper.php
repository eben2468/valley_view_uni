<?php
/**
 * Photo Gallery helpers.
 *
 * One set of tables serves several gallery pages, each identified by a
 * gallery key:
 *
 *     'main' -> gallery.php          'src' -> src_gallery.php
 *     'graduation' -> graduation_gallery.php
 *
 * Every getter takes that key, so adding another gallery page later needs a
 * new key and a new thin template, not another copy of this file.
 *
 * Everything a gallery page renders below the shared hero comes from the
 * gallery_* tables, so admin/manage_gallery.php is the single source of truth.
 * Every getter is wrapped in try/catch and returns a sane empty value: on a
 * server where the SQL files have not been run yet the page still renders
 * instead of dying with a PDOException.
 */

/**
 * Default key, so callers written before multi-gallery still work.
 * Declared with define() rather than const because const is not permitted
 * inside the conditional block below.
 */
if (!defined('VVU_GALLERY_DEFAULT')) {
    define('VVU_GALLERY_DEFAULT', 'main');
}

if (!function_exists('vvu_gallery_content')) {

    /** Single-row page copy for one gallery. Returns [] when unavailable. */
    function vvu_gallery_content($pdo, $galleryKey = VVU_GALLERY_DEFAULT)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM gallery_page_content WHERE page_key = ? LIMIT 1");
            $stmt->execute([$galleryKey]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log('Gallery content unavailable: ' . $e->getMessage());
            // Pre-migration databases have no page_key column; the main
            // gallery still has its original id = 1 row, so fall back to it.
            if ($galleryKey === VVU_GALLERY_DEFAULT) {
                try {
                    return $pdo->query("SELECT * FROM gallery_page_content WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
                } catch (Exception $inner) {
                    return [];
                }
            }
            return [];
        }
    }

    /**
     * Falls back to a default when the stored value is empty, so a blank field
     * in the admin never leaves a hole in the page.
     */
    function vvu_gallery_text($content, $key, $default = '')
    {
        $value = isset($content[$key]) ? trim((string) $content[$key]) : '';
        return $value !== '' ? $value : $default;
    }

    /** Filter pills, each carrying the number of albums inside it. */
    function vvu_gallery_categories($pdo, $galleryKey = VVU_GALLERY_DEFAULT)
    {
        try {
            $sql = "SELECT c.*, COUNT(a.id) AS album_count
                      FROM gallery_categories c
                      LEFT JOIN gallery_albums a
                             ON a.category_id = c.id AND a.status = 'active'
                            AND a.gallery_key = c.gallery_key
                     WHERE c.status = 'active' AND c.gallery_key = ?
                  GROUP BY c.id
                    HAVING album_count > 0
                  ORDER BY c.display_order ASC, c.name ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$galleryKey]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Gallery categories unavailable: ' . $e->getMessage());
            return [];
        }
    }

    /** Album cards for the index grid, with a live photo count. */
    function vvu_gallery_albums($pdo, $galleryKey = VVU_GALLERY_DEFAULT)
    {
        try {
            $sql = "SELECT a.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon,
                           (SELECT COUNT(*) FROM gallery_album_images i
                             WHERE i.album_id = a.id AND i.status = 'active') AS photo_count
                      FROM gallery_albums a
                      LEFT JOIN gallery_categories c ON c.id = a.category_id
                     WHERE a.status = 'active' AND a.gallery_key = ?
                  ORDER BY a.display_order ASC, a.event_date DESC, a.id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$galleryKey]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Gallery albums unavailable: ' . $e->getMessage());
            return [];
        }
    }

    /** One album by its slug within a gallery, or null. */
    function vvu_gallery_album($pdo, $slug, $galleryKey = VVU_GALLERY_DEFAULT)
    {
        try {
            $sql = "SELECT a.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
                      FROM gallery_albums a
                      LEFT JOIN gallery_categories c ON c.id = a.category_id
                     WHERE a.slug = ? AND a.gallery_key = ? AND a.status = 'active'
                     LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$slug, $galleryKey]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log('Gallery album lookup failed: ' . $e->getMessage());
            return null;
        }
    }

    /** Every active photo in an album, in display order. */
    function vvu_gallery_album_images($pdo, $albumId)
    {
        try {
            $stmt = $pdo->prepare(
                "SELECT * FROM gallery_album_images
                  WHERE album_id = ? AND status = 'active'
               ORDER BY display_order ASC, id ASC"
            );
            $stmt->execute([$albumId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Gallery images unavailable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Stat tiles. Rows flagged photos/albums are counted live, scoped to this
     * gallery, so the numbers cannot drift away from what is actually published.
     */
    function vvu_gallery_stats($pdo, $galleryKey = VVU_GALLERY_DEFAULT)
    {
        try {
            $stmt = $pdo->prepare(
                "SELECT * FROM gallery_stats
                  WHERE status = 'active' AND gallery_key = ?
               ORDER BY display_order ASC, id ASC"
            );
            $stmt->execute([$galleryKey]);
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $counts = null;
            foreach ($stats as &$stat) {
                if ($stat['auto_source'] === 'none' || $stat['auto_source'] === '') {
                    continue;
                }
                if ($counts === null) {
                    $c = $pdo->prepare(
                        "SELECT (SELECT COUNT(*) FROM gallery_album_images i
                                   JOIN gallery_albums a ON a.id = i.album_id
                                  WHERE a.gallery_key = :k AND a.status = 'active' AND i.status = 'active') AS photos,
                                (SELECT COUNT(*) FROM gallery_albums
                                  WHERE gallery_key = :k2 AND status = 'active') AS albums"
                    );
                    $c->execute([':k' => $galleryKey, ':k2' => $galleryKey]);
                    $counts = $c->fetch(PDO::FETCH_ASSOC);
                }
                $stat['value_text'] = number_format((int) ($counts[$stat['auto_source']] ?? 0));
            }
            unset($stat);

            return $stats;
        } catch (Exception $e) {
            error_log('Gallery stats unavailable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Resolves a stored image path for use in src="".
     *
     * Paths are stored relative to the project root ("uploads/gallery/…"), but
     * an editor may also paste an absolute URL — both must work, and an empty
     * value must not produce src="" (which makes the browser re-request the
     * current page).
     */
    function vvu_gallery_src($path, $fallback = '')
    {
        $path = trim((string) $path);
        if ($path === '') {
            return $fallback;
        }
        return $path;
    }

    /**
     * The album's "View all photos" link (Google Photos, Drive…), or '' when
     * there is none. Only http(s) links are honoured, so a stray value can
     * never become a javascript: URL. Works before the column exists too.
     */
    function vvu_gallery_external_url($album)
    {
        $url = trim((string) ($album['external_url'] ?? ''));
        return preg_match('#^https?://#i', $url) ? $url : '';
    }

    /** True for an image served by Google Photos (lh3.googleusercontent.com…). */
    function vvu_gallery_is_google_image($url)
    {
        $host = strtolower((string) parse_url(trim((string) $url), PHP_URL_HOST));
        return $host !== '' && preg_match('/(^|\.)(googleusercontent\.com|ggpht\.com)$/', $host) === 1;
    }

    /**
     * A Google Photos image URL without its size suffix ("…=w600-h315-p-k"),
     * so the page can request exactly the size it needs.
     */
    function vvu_gallery_google_base($url)
    {
        $url = trim((string) $url);
        $url = preg_replace('/[?#].*$/', '', $url);
        // The size options follow the last "=" in the final path segment.
        return preg_replace('#(/[^/=]+)=[^/]*$#', '$1', $url);
    }

    /**
     * src="" for a gallery image at a given size.
     *   Google Photos  -> Google resizes it (=w1600, or =w600-h600-c to crop)
     *   local upload   -> cached thumbnail via vvu_thumb() when a box is given
     *   anything else  -> unchanged
     */
    function vvu_gallery_img($path, $w = 0, $h = 0)
    {
        $path = vvu_gallery_src($path);
        if ($path === '') {
            return '';
        }
        if (vvu_gallery_is_google_image($path)) {
            $base = vvu_gallery_google_base($path);
            if ($w > 0 && $h > 0) {
                return $base . '=w' . (int) $w . '-h' . (int) $h . '-c';
            }
            return $base . '=w' . (int) ($w > 0 ? $w : 2048);
        }
        if ($w > 0 && $h > 0 && function_exists('vvu_thumb')) {
            return vvu_thumb($path, $w, $h);
        }
        return $path;
    }

    /**
     * Turns what an editor pasted into something an <img> can show.
     *
     *   uploads/… or any non-Google https image   -> kept as is
     *   lh3.googleusercontent.com/… ("Copy image address") -> size suffix removed
     *   photos.app.goo.gl/… or photos.google.com/share/…/photo/… (a single
     *   photo's share link) -> the page is fetched and its preview image used
     *
     * Returns the value to store, or null with $error set.
     */
    function vvu_gallery_resolve_image_link($input, &$error = null)
    {
        $error = null;
        $url   = trim((string) $input);
        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            return $url; // local path or empty
        }
        if (vvu_gallery_is_google_image($url)) {
            return vvu_gallery_google_base($url);
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $isGooglePhotos = in_array($host, ['photos.app.goo.gl', 'photos.google.com', 'goo.gl'], true);
        if (!$isGooglePhotos) {
            return $url; // some other image host
        }

        if (!function_exists('curl_init')) {
            $error = 'This server cannot open Google Photos links. In Google Photos, right-click the photo and choose "Copy image address" instead.';
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_PROTOCOLS      => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS=> CURLPROTO_HTTPS,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
            CURLOPT_HTTPHEADER     => ['Accept-Language: en'],
        ]);
        $html  = curl_exec($ch);
        $final = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($html === false || $code >= 400) {
            $error = 'Could not open that Google Photos link. Check that the album is shared ("Share" → "Create link").';
            return null;
        }
        $finalHost = strtolower((string) parse_url($final, PHP_URL_HOST));
        if ($finalHost !== 'photos.google.com') {
            $error = 'That link does not lead to a shared Google Photos photo. Make sure the album is shared by link.';
            return null;
        }
        if (strpos((string) parse_url($final, PHP_URL_PATH), '/photo/') === false) {
            $error = 'That is a link to a whole album. Paste it in the "View all photos" field instead — or open one photo and copy that photo\'s link.';
            return null;
        }
        if (preg_match('#<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']#i', $html, $m)
            || preg_match('#<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']#i', $html, $m)) {
            $img = html_entity_decode($m[1], ENT_QUOTES);
            if (vvu_gallery_is_google_image($img)) {
                return vvu_gallery_google_base($img);
            }
        }
        $error = 'Could not find the picture behind that link. In Google Photos, right-click the photo and choose "Copy image address", then paste that.';
        return null;
    }

    /** "07 June 2023" — falls back to whatever is stored if it will not parse. */
    function vvu_gallery_date($date, $format = 'j F Y')
    {
        $date = trim((string) $date);
        if ($date === '' || $date === '0000-00-00') {
            return '';
        }
        $ts = strtotime($date);
        return $ts ? date($format, $ts) : $date;
    }
}
