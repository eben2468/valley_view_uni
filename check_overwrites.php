<?php
require_once('includes/db_connect.php');
$ids = [7, 8, 9, 10, 45, 52];
foreach ($ids as $id) {
    $stmt = $pdo->prepare("SELECT title, full_description FROM academic_programs WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "ID " . $id . " (" . $row['title'] . "): " . substr($row['full_description'], 0, 50) . "...\n";
}
?>
