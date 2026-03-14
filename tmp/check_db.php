<?php
require_once('c:/xampp/htdocs/valley_view_uni/includes/db_connect.php');

$page_id = 53;
echo "Sections for page 53:\n";
$stmt = $pdo->prepare("SELECT id, section_key, section_type FROM administration_content WHERE page_id = ?");
$stmt->execute([$page_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Key: {$row['section_key']} | Type: {$row['section_type']}\n";
    
    $fstmt = $pdo->prepare("SELECT id, field_key, field_type FROM administration_content_fields WHERE content_id = ?");
    $fstmt->execute([$row['id']]);
    while ($frow = $fstmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - Field ID: {$frow['id']} | Key: {$frow['field_key']} | Type: {$frow['field_type']}\n";
    }
}
?>
