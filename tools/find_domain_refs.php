<?php
/**
 * Scan every text column in the database for a string — used to find hardcoded
 * links to the old domain before or after a domain change.
 *
 * CLI only. Read-only: it never modifies anything.
 *
 *     php tools/find_domain_refs.php alpha.vvu.edu.gh
 *
 * Content managers paste absolute URLs into CMS fields all the time, so the
 * database is where old-domain links survive a migration. They keep working
 * only for as long as the old name resolves.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db_connect.php';

$needle = $argv[1] ?? null;
if ($needle === null) {
    fwrite(STDERR, "Usage: php tools/find_domain_refs.php <string>\n");
    fwrite(STDERR, "   eg: php tools/find_domain_refs.php alpha.vvu.edu.gh\n");
    exit(1);
}

echo "Searching every text column for: {$needle}\n\n";

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$totalRows = 0;
$hitCols   = 0;

foreach ($tables as $table) {
    $columns = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        // Only text-ish columns can hold a URL.
        if (!preg_match('/char|text|blob|enum/i', $column['Type'])) {
            continue;
        }

        $field = $column['Field'];

        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM `{$table}` WHERE `{$field}` LIKE ?"
            );
            $stmt->execute(['%' . $needle . '%']);
            $count = (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            continue;   // unreadable column type; skip
        }

        if ($count > 0) {
            printf("  %-40s %d row(s)\n", "{$table}.{$field}", $count);
            $totalRows += $count;
            $hitCols++;
        }
    }
}

echo "\n";
if ($totalRows === 0) {
    echo "Clean — no occurrences anywhere in the database.\n";
    exit(0);
}

echo "Found {$totalRows} row(s) across {$hitCols} column(s).\n\n";
echo "These are content links pointing at the old domain. To update one column:\n\n";
echo "  UPDATE `<table>` SET `<column>` =\n";
echo "      REPLACE(`<column>`, '{$needle}', 'vvu.edu.gh')\n";
echo "  WHERE `<column>` LIKE '%{$needle}%';\n\n";
echo "Back the database up first:\n";
echo "  mysqldump -u root -p valley_view_uni > /root/vvu-before-domain-fix.sql\n";
exit(0);
