<?php
/**
 * Photo Gallery helpers.
 *
 * One set of tables serves several gallery pages, each identified by a
 * gallery key:
 *
 *     'main' -> gallery.php          'src' -> src_gallery.php
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
