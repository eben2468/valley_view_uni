<?php
require_once('includes/db_connect.php');

echo "--- Duplicate Fields Check ---\n";
$stmt = $pdo->query("SELECT content_id, field_key, COUNT(*) as count FROM administration_content_fields GROUP BY content_id, field_key HAVING count > 1");
$dupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($dupes)) {
    echo "No duplicate fields found within sections.\n";
} else {
    foreach ($dupes as $dupe) {
        echo "Section ID: {$dupe['content_id']} | Key: {$dupe['field_key']} | Count: {$dupe['count']}\n";
    }
}
