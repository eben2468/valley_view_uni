<?php
/**
 * Migration: put back the spaces that strip_tags() ate out of saved excerpts.
 *
 * Excerpts are built from the article body. Until vvu_html_to_text() was added
 * that was done with a bare strip_tags(), which removes a tag without leaving
 * anything in its place — so an article written as separate paragraphs came out
 * as one run-on line:
 *
 *   Title: New Students to Report on September 13, 2026Date: August 14, 2026The
 *   Office of Student Life & Services, in collaboration with...
 *
 * The generator is fixed, but excerpts saved before the fix still hold the
 * damaged text. This rebuilds those, keeping each excerpt exactly as long as
 * the author left it.
 *
 * Only excerpts that demonstrably came from the article body are touched: an
 * excerpt qualifies when, ignoring all whitespace, it is a prefix of the body.
 * A hand-written summary will not match and is left alone. That check is also
 * why no attempt is made to guess word breaks from the excerpt on its own —
 * "McDonald" and "PhD" are indistinguishable from real glue without the body
 * to compare against.
 *
 * CLI ONLY — it refuses to run over HTTP, so it is harmless even though it
 * lives inside the web root (it is also denied at the web-server level; see
 * .htaccess and nginx-vvu-security.conf).
 *
 * Usage, from the site directory:
 *     php tools/fix_news_excerpt_spacing.php            # dry run — reports only
 *     php tools/fix_news_excerpt_spacing.php --apply    # writes the changes
 *
 * Safe to re-run: a second pass finds nothing left to change.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/news_helpers.php';

$apply = in_array('--apply', $argv, true);

/** Everything except whitespace — the form in which glued and spaced text compare equal. */
function vvu_squash($s)
{
    return preg_replace('/\s+/u', '', (string) $s);
}

/**
 * Prefix of $flat holding the first $want non-whitespace characters.
 * Re-slicing this way keeps the excerpt the length the author settled on,
 * rather than regenerating it and silently changing where it stops.
 */
function vvu_prefix_by_visible_chars($flat, $want)
{
    $len = mb_strlen($flat, 'UTF-8');
    $seen = 0;
    for ($i = 0; $i < $len; $i++) {
        $ch = mb_substr($flat, $i, 1, 'UTF-8');
        if (preg_match('/\S/u', $ch)) {
            $seen++;
            if ($seen === $want) {
                return mb_substr($flat, 0, $i + 1, 'UTF-8');
            }
        }
    }
    return $flat;
}

echo ($apply ? "APPLYING changes to" : "DRY RUN against") . " database `{$vvu_db['name']}`\n\n";

$rows = $pdo->query(
    "SELECT id, title, excerpt, content FROM news_articles ORDER BY id"
)->fetchAll(PDO::FETCH_ASSOC);

$changed = 0;
$unverifiable = 0;

foreach ($rows as $row) {
    $stored = trim((string) $row['excerpt']);
    if ($stored === '') {
        continue;
    }

    // The trailing ellipsis is the generator's, not the author's — set it aside
    // and put it back on whatever replaces the text.
    $suffix = '';
    if (preg_match('/(\x{2026}|\.\.\.)$/u', $stored, $m)) {
        $suffix = $m[1];
    }
    $body = rtrim(mb_substr($stored, 0, mb_strlen($stored, 'UTF-8') - mb_strlen($suffix, 'UTF-8'), 'UTF-8'));

    $flat = vvu_html_to_text($row['content']);
    $squashed_body = vvu_squash($body);

    if ($squashed_body === '' || strpos(vvu_squash($flat), $squashed_body) !== 0) {
        // Not derived from the body — a hand-written summary, or the body has
        // since been rewritten. Only worth mentioning if it looks damaged.
        if (preg_match('/[a-z0-9,\.\)][A-Z]/u', $body)) {
            echo "REVIEW  {$row['title']} (row {$row['id']})\n";
            echo "   Looks run-together but does not match the article body — check by hand.\n";
            echo "   {$stored}\n";
            $unverifiable++;
        }
        continue;
    }

    $fixed = rtrim(vvu_prefix_by_visible_chars($flat, mb_strlen($squashed_body, 'UTF-8'))) . $suffix;

    if ($fixed === $stored) {
        continue;
    }

    echo ($apply ? "UPDATE  " : "WOULD   ") . "{$row['title']} (row {$row['id']})\n";
    echo "   old: {$stored}\n";
    echo "   new: {$fixed}\n";

    if ($apply) {
        $up = $pdo->prepare("UPDATE news_articles SET excerpt = ? WHERE id = ?");
        $up->execute([$fixed, $row['id']]);
    }

    $changed++;
}

echo "\n" . ($apply ? "Updated" : "Would update") . " $changed excerpt(s).\n";

if ($unverifiable) {
    echo "$unverifiable excerpt(s) need a human eye — see REVIEW above.\n";
}

if (!$apply && $changed) {
    echo "\nNothing was written. Re-run with --apply to commit these changes.\n";
}
