<?php
require_once('includes/db_connect.php');

echo "--- Administration Content ---\n";
$stmt = $pdo->query("SELECT id, page_id, section_key, section_type FROM administration_content ORDER BY page_id, section_key");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Page: {$row['page_id']} | Key: {$row['section_key']} | Type: {$row['section_type']}\n";
}

echo "\n--- Duplicate Sections Check ---\n";
$stmt = $pdo->query("SELECT page_id, section_key, COUNT(*) as count FROM administration_content GROUP BY page_id, section_key HAVING count > 1");
$dupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($dupes)) {
    echo "No duplicate sections found.\n";
} else {
    foreach ($dupes as $dupe) {
        echo "Page: {$dupe['page_id']} | Key: {$dupe['section_key']} | Count: {$dupe['count']}\n";
    }
}
