<?php
/**
 * Migration: normalise the spelling of the Vice-Chancellor's title in the
 * database.
 *
 * House style is "Vice-Chancellor" (hyphenated) and "Pro Vice-Chancellor"
 * (space, then hyphen) — the Commonwealth/Ghanaian form already used by
 * 'Acting Vice-Chancellor' in the Pro-VC office content.
 *
 * The repo's .php and .sql sources were updated in the same change. This
 * handles rows already saved through the admin CMS, which the source files
 * cannot reach.
 *
 * CLI ONLY — it refuses to run over HTTP, so it is harmless even though it
 * lives inside the web root (it is also denied at the web-server level; see
 * .htaccess and nginx-vvu-security.conf).
 *
 * Usage, from the site directory:
 *     php tools/fix_vc_hyphenation.php            # dry run — reports only
 *     php tools/fix_vc_hyphenation.php --apply    # writes the changes
 *
 * Safe to re-run: a second pass finds nothing left to change.
 *
 * Every pattern requires a literal SPACE between "Vice" and "Chancellor", so
 * slugs, URLs and image paths (past-vice-chancellors/, vice-chancellor-*.jpg)
 * are never touched.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db_connect.php';

$apply = in_array('--apply', $argv, true);

$rules = [
    // Pro-VC first, so the generic rule below can't produce "Pro-Vice-Chancellor".
    '/\bPro[- ]Vice Chancellor(s?)\b/' => 'Pro Vice-Chancellor$1',
    '/\bPRO[- ]VICE CHANCELLOR(S?)\b/' => 'PRO VICE-CHANCELLOR$1',
    '/\bpro[- ]vice chancellor(s?)\b/' => 'pro vice-chancellor$1',
    '/\bVice Chancellor(s?)\b/'        => 'Vice-Chancellor$1',
    '/\bVICE CHANCELLOR(S?)\b/'        => 'VICE-CHANCELLOR$1',
    '/\bvice chancellor(s?)\b/'        => 'vice-chancellor$1',
];

/**
 * Single-column primary key for a table, or null when there isn't one —
 * without it an UPDATE cannot be aimed at exactly one row.
 */
function vvu_primary_key(PDO $pdo, $schema, $table)
{
    $st = $pdo->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.STATISTICS
          WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = 'PRIMARY'
          ORDER BY SEQ_IN_INDEX"
    );
    $st->execute([$schema, $table]);
    $cols = $st->fetchAll(PDO::FETCH_COLUMN);

    return count($cols) === 1 ? $cols[0] : null;
}

$schema = $vvu_db['name'];

echo ($apply ? "APPLYING changes to" : "DRY RUN against") . " database `$schema`\n\n";

$cols = $pdo->prepare(
    "SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = ?
        AND DATA_TYPE IN ('char','varchar','text','tinytext','mediumtext','longtext')
      ORDER BY TABLE_NAME, ORDINAL_POSITION"
);
$cols->execute([$schema]);

$changed = 0;
$skipped = 0;

foreach ($cols->fetchAll(PDO::FETCH_NUM) as list($table, $col)) {
    $pk = vvu_primary_key($pdo, $schema, $table);

    // LIKE '%vice%chancellor%' is deliberately loose: it also matches rows that
    // are already correct, which the regex below then leaves untouched.
    $rows = $pdo->query(
        "SELECT " . ($pk ? "`$pk`," : "") . " `$col`
           FROM `$table` WHERE `$col` LIKE '%vice%chancellor%'"
    )->fetchAll(PDO::FETCH_NUM);

    foreach ($rows as $row) {
        $value = $pk ? $row[1] : $row[0];
        $new   = preg_replace(array_keys($rules), array_values($rules), $value);

        if ($new === $value) {
            continue;
        }

        if ($pk === null) {
            echo "SKIP    $table.$col — no single-column primary key, update by hand\n";
            $skipped++;
            continue;
        }

        echo ($apply ? "UPDATE  " : "WOULD   ") . "$table.$col  (row {$row[0]})\n";
        echo "   old: " . preg_replace('/\s+/', ' ', mb_substr($value, 0, 140)) . "\n";
        echo "   new: " . preg_replace('/\s+/', ' ', mb_substr($new,   0, 140)) . "\n";

        if ($apply) {
            $up = $pdo->prepare("UPDATE `$table` SET `$col` = ? WHERE `$pk` = ?");
            $up->execute([$new, $row[0]]);
        }

        $changed++;
    }
}

echo "\n" . ($apply ? "Updated" : "Would update") . " $changed value(s).\n";

if ($skipped) {
    echo "Skipped $skipped value(s) with no usable primary key.\n";
}

if (!$apply && $changed) {
    echo "\nNothing was written. Re-run with --apply to commit these changes.\n";
}
