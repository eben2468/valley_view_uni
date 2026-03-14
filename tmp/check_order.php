<?php
require_once('c:/xampp/htdocs/valley_view_uni/includes/db_connect.php');

$page_id = 53;
echo "Sections for page 53 with order:\n";
$stmt = $pdo->prepare("SELECT id, section_key, content_order FROM administration_content WHERE page_id = ? ORDER BY content_order ASC");
$stmt->execute([$page_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Key: {$row['section_key']} | Order: {$row['content_order']}\n";
}
?>
