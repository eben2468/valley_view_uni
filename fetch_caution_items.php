<?php
require 'includes/db_connect.php';
$stmt = $pdo->prepare('SELECT * FROM academic_pages_items WHERE page_key = ?');
$stmt->execute(['caution_to_applicants']);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($items, JSON_PRETTY_PRINT);
?>
