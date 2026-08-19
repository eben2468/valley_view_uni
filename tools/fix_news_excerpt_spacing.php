<?php
/**
 * Migration: restore the sentence breaks that strip_tags() ate out of saved
 * excerpts.
 *
 * Excerpts are built from the article body. Until vvu_html_to_text() was added
 * that was done with a bare strip_tags(), which removes a tag without leaving
 * anything in its place — so an article written as separate paragraphs came out
 * as one run-on line:
 *
 *   Title: New Students to Report on September 13, 2026Date: August 14, 2026The
 *   Office of Student Life & Services, in collaboration with...
 *
 * and should read:
 *
 *   Title: New Students to Report on September 13, 2026. Date: August 14, 2026.
 *   The Office of Student Life & Services, in collaboration with...
 *
 * The generator is fixed, but excerpts saved before the fix still hold the
 * damaged text. This rebuilds those, keeping each excerpt exactly as long as
 * the author left it. It also picks up excerpts left half-repaired by an
 * earlier run of this tool, from before the full stops were added.
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

/**
 * Letters and digits only.
 *
 * Comparing on these alone means a damaged excerpt, a half-repaired one that
 * has spaces but no full stops, and a fully repaired one all reduce to the
 * same string — so this tool still recognises its own earlier output and can
 * finish the job on a site that was migrated before full stops were added.
 */
function vvu_letters($s)
{
    return preg_replace('/[^\p{L}\p{N}]+/u', '', (string) $s);
}

/**
 * Prefix of $flat holding the first $want letters/digits, plus any punctuation
 * sitting immediately after them.
 *
 * Re-slicing this way keeps the excerpt the length the author settled on,
 * rather than regenerating it and silently changing where it stops. The
 * punctuation sweep matters for an excerpt that was never truncated: without
 * it, an excerpt ending "...are open." would come back as "...are open".
 */
function vvu_prefix_by_letters($flat, $want)
{
    $len  = mb_strlen($flat, 'UTF-8');
    $seen = 0;

    for ($i = 0; $i < $len; $i++) {
        if (!preg_match('/[\p{L}\p{N}]/u', mb_substr($flat, $i, 1, 'UTF-8'))) {
            continue;
        }

        if (++$seen < $want) {
            continue;
        }

        $end = $i + 1;
        while ($end < $len && preg_match('/^[.!?,;:)\]"\x{2019}\x{201d}]$/u', mb_substr($flat, $end, 1, 'UTF-8'))) {
            $end++;
        }

        return mb_substr($flat, 0, $end, 'UTF-8');
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
    $body_letters = vvu_letters($body);

    if ($body_letters === '' || strpos(vvu_letters($flat), $body_letters) !== 0) {
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

    $fixed = rtrim(vvu_prefix_by_letters($flat, mb_strlen($body_letters, 'UTF-8'))) . $suffix;

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
