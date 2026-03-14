<?php
include 'includes/db_connect.php';
$s = $pdo->query('SELECT * FROM homepage_sliders WHERE is_active=1')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($s, JSON_PRETTY_PRINT);
?>
