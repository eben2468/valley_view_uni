<?php
/**
 * Valley View University - News & Events shared helpers
 *
 * Small presentation helpers used by the newsroom pages
 * (news_&_events.php, events.php, news_detail.php, event_detail.php).
 * Everything is prefixed vvu_ and guarded so it can be included safely
 * alongside the per-page helper functions that already exist.
 */

if (!function_exists('vvu_read_time')) {
    /**
     * Estimated reading time in whole minutes (min 1).
     */
    function vvu_read_time($content)
    {
        $words = str_word_count(strip_tags((string)$content));
        $minutes = (int)ceil($words / 200);
        return $minutes > 0 ? $minutes : 1;
    }
}

if (!function_exists('vvu_relative_date')) {
    /**
     * Human phrasing for recent dates, absolute for older ones.
     * "Today", "Yesterday", "3 days ago", then "12 March 2026".
     */
    function vvu_relative_date($date)
    {
        if (empty($date)) {
            return '';
        }
        $ts = strtotime($date);
        if (!$ts) {
            return '';
        }

        $today = strtotime('today');
        $day = strtotime(date('Y-m-d', $ts));
        $diff = (int)floor(($today - $day) / 86400);

        if ($diff === 0)  return 'Today';
        if ($diff === 1)  return 'Yesterday';
        if ($diff > 1 && $diff < 7)  return $diff . ' days ago';
        if ($diff >= 7 && $diff < 14) return 'Last week';
        if ($diff < 0) {
            // Future dated (upcoming events)
            $ahead = abs($diff);
            if ($ahead === 1) return 'Tomorrow';
            if ($ahead < 7)   return 'In ' . $ahead . ' days';
        }
        return date('j F Y', $ts);
    }
}

if (!function_exists('vvu_initials')) {
    /**
     * Up to two initials for an author avatar.
     */
    function vvu_initials($name)
    {
        $name = trim(strip_tags((string)$name));
        if ($name === '') {
            return 'VVU';
        }
        $parts = preg_split('/\s+/', $name);
        $initials = '';
        foreach ($parts as $part) {
            $letter = preg_replace('/[^A-Za-z]/', '', $part);
            if ($letter !== '') {
                $initials .= strtoupper($letter[0]);
            }
            if (strlen($initials) >= 2) {
                break;
            }
        }
        return $initials !== '' ? $initials : 'V';
    }
}

if (!function_exists('vvu_avatar_tone')) {
    /**
     * Stable warm tone per author so the same person always
     * gets the same avatar colour across the site.
     */
    function vvu_avatar_tone($name)
    {
        $tones = [
            'linear-gradient(135deg, #1d3557 0%, #2f5c8a 100%)',
            'linear-gradient(135deg, #7a4419 0%, #b9682c 100%)',
            'linear-gradient(135deg, #2d5544 0%, #4d8163 100%)',
            'linear-gradient(135deg, #5c3a58 0%, #8a5b83 100%)',
            'linear-gradient(135deg, #6b3030 0%, #a34f4f 100%)',
        ];
        $index = abs(crc32(strtolower(trim((string)$name)))) % count($tones);
        return $tones[$index];
    }
}

if (!function_exists('vvu_kicker_tone')) {
    /**
     * Accent colour for the small category kicker dot.
     */
    function vvu_kicker_tone($category)
    {
        $tones = [
            'news'           => '#1f6feb',
            'events'         => '#9c4dcc',
            'announcements'  => '#d98324',
            'press_releases' => '#0f6466',
            'academic'       => '#3f8f5b',
        ];
        return $tones[$category] ?? '#1f6feb';
    }
}

if (!function_exists('vvu_html_to_text')) {
    /**
     * Flatten article HTML to plain text.
     *
     * strip_tags() deletes a tag without leaving anything in its place, so
     * "<p>...September 13, 2026</p><p>Date: August 14, 2026</p>" collapses to
     * "...September 13, 2026Date: August 14, 2026" — the run-together wording
     * that turned up in excerpts and standfirsts wherever an author wrote the
     * body as separate paragraphs. A block boundary is a word break, so it
     * becomes a space before the tags are stripped.
     *
     * Inline tags (<strong>, <em>, <a>, <sub>) are still removed with nothing
     * in their place, so mid-word emphasis and "H<sub>2</sub>O" survive.
     */
    function vvu_html_to_text($html)
    {
        $text = (string) $html;

        // Non-prose blocks contribute nothing to a summary.
        $text = preg_replace('#<(script|style|figure|figcaption|table)[^>]*>.*?</\1>#is', ' ', $text);

        // Every block-level boundary reads as a word break.
        $blocks = 'p|div|br|hr|li|ul|ol|dl|dt|dd|tr|td|th|h[1-6]|section|article'
                . '|header|footer|aside|nav|blockquote|pre|figure|figcaption'
                . '|table|thead|tbody|tfoot|caption|address|main|form|fieldset';
        $text = preg_replace('#<\s*/?\s*(' . $blocks . ')\b[^>]*>#i', ' ', $text);

        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // &nbsp; decodes to U+00A0, which \s does not match.
        $text = str_replace("\xC2\xA0", ' ', $text);

        // Collapse the whitespace the substitutions above introduce, then pull
        // punctuation back onto the word it belongs to (a paragraph that opens
        // with a comma or full stop is rare but reads badly when left adrift).
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = preg_replace('/\s+([,.;:!?])/u', '$1', $text);

        return trim($text);
    }
}

if (!function_exists('vvu_auto_excerpt')) {
    /**
     * Build a summary from article body copy.
     *
     * Prefers whole sentences: keeps adding sentences while they fit inside
     * $limit, and only falls back to a word-boundary cut if the very first
     * sentence is already too long.
     */
    function vvu_auto_excerpt($content, $limit = 200)
    {
        // Flattened via vvu_html_to_text() so block boundaries become spaces
        // instead of running the last word of one paragraph into the next.
        $text = vvu_html_to_text($content);

        if ($text === '') {
            return '';
        }

        $strlen = function ($s) {
            return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s);
        };
        $substr = function ($s, $start, $len = null) {
            if (function_exists('mb_substr')) {
                return $len === null ? mb_substr($s, $start, null, 'UTF-8') : mb_substr($s, $start, $len, 'UTF-8');
            }
            return $len === null ? substr($s, $start) : substr($s, $start, $len);
        };

        if ($strlen($text) <= $limit) {
            return $text;
        }

        // Split on sentence endings, keeping the punctuation
        $sentences = preg_split('/(?<=[.!?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $summary = '';
        foreach ($sentences as $sentence) {
            $candidate = $summary === '' ? $sentence : $summary . ' ' . $sentence;
            if ($strlen($candidate) > $limit) {
                break;
            }
            $summary = $candidate;
        }

        if ($summary !== '') {
            return $summary;
        }

        // First sentence alone overshoots — cut on the last whole word
        $cut = $substr($text, 0, $limit);
        $space = function_exists('mb_strrpos') ? mb_strrpos($cut, ' ', 0, 'UTF-8') : strrpos($cut, ' ');
        if ($space !== false && $space > $limit * 0.5) {
            $cut = $substr($cut, 0, $space);
        }
        return rtrim($cut, " ,;:-") . '…';
    }
}

if (!function_exists('vvu_excerpt')) {
    /**
     * Trimmed plain-text summary, falling back to the article body.
     */
    function vvu_excerpt($excerpt, $content = '', $length = 165)
    {
        $text = vvu_html_to_text($excerpt);
        if ($text === '') {
            $text = vvu_html_to_text($content);
        }
        if ($text === '') {
            return '';
        }
        if (function_exists('mb_strlen') && mb_strlen($text) > $length) {
            return rtrim(mb_substr($text, 0, $length), " ,.;:-") . '…';
        }
        if (strlen($text) > $length) {
            return rtrim(substr($text, 0, $length), " ,.;:-") . '…';
        }
        return $text;
    }
}
