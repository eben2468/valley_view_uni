<?php
require 'includes/db_connect.php';

$stmt = $pdo->prepare("
    SELECT ac.section_key, acf.field_key, acf.field_value 
    FROM administration_pages ap
    JOIN administration_content ac ON ap.id = ac.page_id
    JOIN administration_content_fields acf ON ac.id = acf.content_id
    WHERE ap.page_slug = 'library_resources'
");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Current fields in DB for library_resources:\n";
foreach($results as $r) {
    echo $r['section_key'] . " -> " . $r['field_key'] . "\n";
}
