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
