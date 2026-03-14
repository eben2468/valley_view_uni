<?php
require_once('includes/db_connect.php');

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "\nTable: $table\n";
    $columns = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo " - " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
}
?>
