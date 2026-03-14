<?php
require_once 'includes/db_connect.php';

echo "--- academic_pages_content ---\n";
$stmt = $pdo->query("SELECT page_key, COUNT(*) as count FROM academic_pages_content GROUP BY page_key HAVING count > 1");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

echo "\n--- academic_pages_sections ---\n";
$stmt = $pdo->query("SELECT page_key, section_key, COUNT(*) as count FROM academic_pages_sections GROUP BY page_key, section_key HAVING count > 1");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

echo "\n--- academic_pages_items ---\n";
// This is harder to define duplicates for without a unique key, but let's check title/desc/page/section
$stmt = $pdo->query("SELECT page_key, section_key, item_title, COUNT(*) as count FROM academic_pages_items GROUP BY page_key, section_key, item_title HAVING count > 1");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>
