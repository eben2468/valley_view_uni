<?php
/**
 * Content data exporter.
 *
 * Git carries code and the files under uploads/, but not the database. When you
 * change an image or a link in the admin panel, the *file* may reach the live
 * server through git while the database row that points at it stays behind on
 * your machine — so nothing appears to change.
 *
 * Run this LOCALLY. It writes a .sql file containing your current content rows.
 * Import that file on the live server (phpMyAdmin > Import) to bring its
 * database in line.
 *
 * Safety: statements are INSERT ... ON DUPLICATE KEY UPDATE, so importing
 * updates rows that already exist and adds ones that don't. It never issues a
 * DELETE or TRUNCATE, so content created directly on the live server survives.
 * (The flip side: rows you deleted locally are not removed on live — delete
 * those by hand in the admin panel.)
 *
 * Usage:  php export_content_data.php
 *     or: http://localhost/valley_view_uni/export_content_data.php
 */

require_once __DIR__ . '/includes/db_connect.php';

$isCli = (php_sapi_name() === 'cli');

// Groups let you export only what you actually changed.
$groups = [
    'navigation' => [
        'label'  => 'Navigation & footer links',
        'tables' => ['navigation_items', 'navigation_sections', 'navigation_links',
                     'topbar_settings', 'footer_settings', 'footer_sections', 'footer_links'],
    ],
    'homepage' => [
        'label'  => 'Homepage content & images',
        'tables' => ['homepage_sliders', 'homepage_sections', 'homepage_discover_cards',
                     'homepage_programs', 'homepage_gallery', 'homepage_video',
                     'homepage_news', 'homepage_stats_banner', 'homepage_stats_items',
                     'homepage_study_options'],
    ],
];

// ?only=navigation or ?only=homepage to narrow it down
$only = $_GET['only'] ?? ($argv[1] ?? '');
$selected = ($only && isset($groups[$only])) ? [$only => $groups[$only]] : $groups;

$out   = [];
$out[] = "-- Valley View University — content data export";
$out[] = "-- Generated " . date('Y-m-d H:i:s');
$out[] = "-- Import on the live server via phpMyAdmin > Import.";
$out[] = "-- Updates existing rows and adds new ones. Never deletes.";
$out[] = "";
$out[] = "SET NAMES utf8mb4;";
$out[] = "SET FOREIGN_KEY_CHECKS = 0;";
$out[] = "";

$summary = [];

foreach ($selected as $key => $group) {
    $out[] = "-- =====================================================";
    $out[] = "-- " . $group['label'];
    $out[] = "-- =====================================================";
    $out[] = "";

    foreach ($group['tables'] as $table) {
        try {
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $out[] = "-- SKIPPED `$table` (not present in this database)";
            $out[] = "";
            $summary[$table] = 'missing';
            continue;
        }

        $summary[$table] = count($rows);

        if (!$rows) {
            $out[] = "-- `$table` is empty, nothing to export";
            $out[] = "";
            continue;
        }

        $columns   = array_keys($rows[0]);
        $columnSql = '`' . implode('`, `', $columns) . '`';

        // Update every column except the primary key on conflict
        $updates = [];
        foreach ($columns as $col) {
            if ($col === 'id') {
                continue;
            }
            $updates[] = "`$col` = VALUES(`$col`)";
        }

        $out[] = "-- $table (" . count($rows) . " rows)";

        $valueRows = [];
        foreach ($rows as $row) {
            $vals = [];
            foreach ($row as $value) {
                $vals[] = ($value === null) ? 'NULL' : $pdo->quote((string) $value);
            }
            $valueRows[] = '  (' . implode(', ', $vals) . ')';
        }

        $sql = "INSERT INTO `$table` ($columnSql) VALUES\n" . implode(",\n", $valueRows);
        if ($updates) {
            $sql .= "\nON DUPLICATE KEY UPDATE " . implode(', ', $updates);
        }
        $out[] = $sql . ";";
        $out[] = "";
    }
}

$out[] = "SET FOREIGN_KEY_CHECKS = 1;";
$out[] = "";

$filename = 'content_export_' . date('Ymd_His') . '.sql';
$path     = __DIR__ . DIRECTORY_SEPARATOR . $filename;
file_put_contents($path, implode("\n", $out));

// ---- Report ---------------------------------------------------------------
if ($isCli) {
    echo "Wrote $filename (" . round(filesize($path) / 1024, 1) . " KB)\n\n";
    foreach ($summary as $table => $count) {
        printf("  %-28s %s\n", $table, is_int($count) ? "$count rows" : $count);
    }
    echo "\nNext: import this file on the live server (phpMyAdmin > Import).\n";
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>Content Export</title>';
    echo '<style>body{font-family:system-ui,sans-serif;max-width:760px;margin:40px auto;padding:0 20px;line-height:1.6;color:#222}
          h1{color:#002147} table{border-collapse:collapse;width:100%;margin:20px 0}
          td,th{border:1px solid #e2e8f0;padding:8px 12px;text-align:left;font-size:14px}
          th{background:#f8fafc} code{background:#f1f5f9;padding:2px 6px;border-radius:4px}
          .ok{color:#15803d;font-weight:600} .note{background:#fffbeb;border-left:4px solid #f0b429;padding:12px 16px;margin:20px 0}</style>';
    echo '<h1>Content export ready</h1>';
    echo '<p class="ok">Wrote <code>' . htmlspecialchars($filename) . '</code> (' . round(filesize($path) / 1024, 1) . ' KB) to your project folder.</p>';
    echo '<table><tr><th>Table</th><th>Exported</th></tr>';
    foreach ($summary as $table => $count) {
        echo '<tr><td><code>' . htmlspecialchars($table) . '</code></td><td>'
           . (is_int($count) ? $count . ' rows' : '<em>' . htmlspecialchars($count) . '</em>') . '</td></tr>';
    }
    echo '</table>';
    echo '<div class="note"><strong>Next step:</strong> open phpMyAdmin on your live server, pick the
          <code>valley_view_uni</code> database, go to <strong>Import</strong>, and upload this file.
          It updates existing rows and adds new ones &mdash; it never deletes, so anything created
          directly on the live site is safe.</div>';
    echo '<p><strong>Back up first:</strong> in phpMyAdmin on live, use <strong>Export</strong> to save a
          copy of the database before importing, so you can roll back if needed.</p>';
}
