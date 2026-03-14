<?php
require_once('includes/db_connect.php');
$stmt = $pdo->query("SELECT id, title FROM academic_programs");
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($programs as $program) {
    echo $program['id'] . "| " . $program['title'] . "\n";
}
?>
