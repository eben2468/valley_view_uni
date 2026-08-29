<?php
/**
 * Video URL helper.
 *
 * The admin form used to require a hand-built /embed/ URL. Anything copied
 * straight from YouTube's Share button (youtu.be/..., watch?v=..., /shorts/...)
 * produced an iframe that silently refused to play. These normalise whatever
 * the editor pastes into a real embed URL.
 */

if (!function_exists('vvu_youtube_id')) {

    /**
     * Pulls the 11-character video id out of any common YouTube URL shape.
     * Returns null if this isn't a YouTube link.
     */
    function vvu_youtube_id($url) {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        // A bare id pasted on its own
        if (preg_match('#^[A-Za-z0-9_-]{11}$#', $url)) {
            return $url;
        }

        $patterns = [
            '#youtube\.com/watch\?(?:.*&)?v=([A-Za-z0-9_-]{11})#i',  // standard watch link
            '#youtu\.be/([A-Za-z0-9_-]{11})#i',                      // short share link
            '#youtube\.com/embed/([A-Za-z0-9_-]{11})#i',             // already an embed
            '#youtube\.com/shorts/([A-Za-z0-9_-]{11})#i',            // shorts
            '#youtube\.com/live/([A-Za-z0-9_-]{11})#i',              // live stream
            '#youtube\.com/v/([A-Za-z0-9_-]{11})#i',                 // legacy
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    /**
     * Normalises a pasted URL into something an <iframe> can actually play.
     * Non-YouTube URLs are returned untouched so other providers still work.
     */
    function vvu_video_embed($url) {
        $url = trim((string) $url);
        $id  = vvu_youtube_id($url);

        if ($id === null) {
            return $url;
        }

        $embed = 'https://www.youtube.com/embed/' . $id;

        // Keep a start offset if the editor copied a "share at current time"
        // link. The 1h2m3s form must be tested first, otherwise the plain-digit
        // pattern below matches the leading "1" of "1m30s" and starts at 1s.
        if (preg_match('#[?&]t=(?:(\d+)h)?(?:(\d+)m)?(\d+)s#i', $url, $m)) {
            $embed .= '?start=' . ((int) $m[1] * 3600 + (int) $m[2] * 60 + (int) $m[3]);
        } elseif (preg_match('#[?&](?:t|start)=(\d+)(?:&|$)#i', $url, $m)) {
            $embed .= '?start=' . (int) $m[1];
        }

        return $embed;
    }

    /**
     * Thumbnail for a YouTube video, handy for admin previews.
     */
    function vvu_youtube_thumb($url) {
        $id = vvu_youtube_id($url);
        return $id ? 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg' : null;
    }
}

/**
 * Uploaded-video support.
 *
 * The homepage video box originally only played a YouTube embed. Editors also
 * need to publish a file they hold themselves (a promo cut that is not on
 * YouTube, or one that must not carry YouTube's branding and related-video
 * strip), so homepage_video grew two columns:
 *
 *   video_source  'youtube' (default, existing behaviour) or 'upload'
 *   video_file    path relative to the project root, e.g. uploads/videos/x.mp4
 *
 * Everything below degrades to the old YouTube-only behaviour when those
 * columns are missing, so the public page keeps working before the migration
 * (sql/homepage_video_upload_migration.sql) has been run.
 */

if (!function_exists('vvu_video_install')) {

    /**
     * Adds the two columns if they are not there yet. Safe to call on every
     * admin request; returns false if the database would not allow it, in
     * which case the editor is left with the YouTube-only form.
     */
    function vvu_video_install($pdo)
    {
        try {
            $has_source = $pdo->query("SHOW COLUMNS FROM homepage_video LIKE 'video_source'")->fetch();
            if (!$has_source) {
                $pdo->exec("ALTER TABLE homepage_video ADD COLUMN video_source VARCHAR(20) NOT NULL DEFAULT 'youtube'");
            }

            $has_file = $pdo->query("SHOW COLUMNS FROM homepage_video LIKE 'video_file'")->fetch();
            if (!$has_file) {
                $pdo->exec("ALTER TABLE homepage_video ADD COLUMN video_file VARCHAR(500) DEFAULT NULL");
            }

            // video_url is NOT NULL in the original schema, so an upload-only
            // video could not be saved without also pasting a YouTube link.
            // Relaxed once, not on every request.
            $url_col = $pdo->query("SHOW COLUMNS FROM homepage_video LIKE 'video_url'")->fetch(PDO::FETCH_ASSOC);
            if ($url_col && strtoupper($url_col['Null'] ?? '') === 'NO') {
                $pdo->exec("ALTER TABLE homepage_video MODIFY video_url VARCHAR(1000) NULL DEFAULT NULL");
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

if (!function_exists('vvu_video_is_upload')) {

    /**
     * True when this row should play an uploaded file rather than the embed.
     *
     * A row marked "upload" whose file is missing falls back to the YouTube
     * link, so deleting a file from the server degrades to the old video
     * instead of showing a broken player.
     */
    function vvu_video_is_upload($video)
    {
        if (!is_array($video) || ($video['video_source'] ?? 'youtube') !== 'upload') {
            return false;
        }

        // The file has to actually be there. is_file() is a stat the OS has
        // cached anyway, and without it a video deleted from the server would
        // render a player that can never load.
        return vvu_video_file_exists($video['video_file'] ?? '');
    }
}

if (!function_exists('vvu_video_mime')) {

    /**
     * MIME type for an uploaded video, from its extension. Used for the
     * <source type=""> hint so the browser does not have to sniff.
     */
    function vvu_video_mime($path)
    {
        $types = [
            'mp4'  => 'video/mp4',
            'm4v'  => 'video/mp4',
            'webm' => 'video/webm',
            'ogv'  => 'video/ogg',
            'ogg'  => 'video/ogg',
            'mov'  => 'video/quicktime',
        ];

        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

        return $types[$ext] ?? 'video/mp4';
    }
}

if (!function_exists('vvu_video_file_exists')) {

    /**
     * Whether an uploaded video is still present on disk. The admin screen
     * uses it to warn about a row pointing at a file that has been removed.
     */
    function vvu_video_file_exists($path)
    {
        $path = trim((string) $path);
        if ($path === '') {
            return false;
        }

        return is_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
    }
}

if (!function_exists('vvu_video_delete_file')) {

    /**
     * Deletes a previously uploaded video once it has been replaced, so
     * uploads/videos/ does not accumulate 60MB orphans.
     *
     * Restricted to uploads/videos/ and to the extensions the uploader
     * produces, so a tampered form value cannot delete anything else.
     */
    function vvu_video_delete_file($path)
    {
        $path = trim((string) $path);

        if ($path === '' || !preg_match('#^uploads/videos/[A-Za-z0-9._-]+\.(mp4|webm|ogv|mov)$#i', $path)) {
            return false;
        }

        $full = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);

        return is_file($full) ? @unlink($full) : false;
    }
}
