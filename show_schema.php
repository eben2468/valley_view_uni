<?php
require_once 'includes/db_connect.php';

foreach(['academic_pages_content', 'academic_pages_sections', 'academic_pages_items'] as $t) {
    echo "Table: $t\n";
    $res = $pdo->query("SHOW CREATE TABLE $t")->fetch();
    echo $res[1] . "\n\n";
}
?>
