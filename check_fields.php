<?php
require_once('includes/db_connect.php');

echo "--- Total Fields Count ---\n";
$stmt = $pdo->query("SELECT COUNT(*) FROM administration_content_fields");
echo "Total: " . $stmt->fetchColumn() . "\n";

echo "\n--- Sample Fields ---\n";
$stmt = $pdo->query("SELECT * FROM administration_content_fields LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n--- Orphaned Fields (fields with content_id that doesn't exist) ---\n";
$stmt = $pdo->query("SELECT COUNT(*) FROM administration_content_fields f LEFT JOIN administration_content c ON f.content_id = c.id WHERE c.id IS NULL");
echo "Orphaned: " . $stmt->fetchColumn() . "\n";
