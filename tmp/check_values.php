<?php
require_once('c:/xampp/htdocs/valley_view_uni/includes/db_connect.php');

$page_id = 53;
echo "Field values for Partner and Quick Links:\n";
$stmt = $pdo->prepare("
    SELECT c.section_key, f.field_key, f.field_value 
    FROM administration_content c
    JOIN administration_content_fields f ON c.id = f.content_id
    WHERE c.page_id = ? AND c.section_key IN ('db_partner_libraries', 'db_quick_links')
");
$stmt->execute([$page_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Section: {$row['section_key']} | Field: {$row['field_key']} | Value: " . substr($row['field_value'], 0, 100) . "\n";
}
?>
