<?php
require 'includes/db_connect.php';

$stmt = $pdo->prepare("
    SELECT ac.section_key, acf.field_key 
    FROM administration_pages ap
    JOIN administration_content ac ON ap.id = ac.page_id
    JOIN administration_content_fields acf ON ac.id = acf.content_id
    WHERE ap.page_slug = 'academic_bulletin'
");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Current fields in DB for academic_bulletin:\n";
foreach($results as $r) {
    echo $r['section_key'] . " -> " . $r['field_key'] . "\n";
}
