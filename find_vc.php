<?php
require_once('includes/db_connect.php');

echo "--- Searching for VC Name in Fields ---\n";
$stmt = $pdo->prepare("SELECT * FROM administration_content_fields WHERE field_value LIKE ?");
$stmt->execute(['%William Kofi Koomson%']);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($results);

if (!empty($results)) {
    $content_id = $results[0]['content_id'];
    echo "\n--- Section Info for Content ID $content_id ---\n";
    $stmt = $pdo->prepare("SELECT * FROM administration_content WHERE id = ?");
    $stmt->execute([$content_id]);
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
}
