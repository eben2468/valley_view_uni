<?php
require_once('includes/db_connect.php');
$schema = $pdo->query("DESCRIBE academic_programs")->fetchAll(PDO::FETCH_ASSOC);
print_r($schema);
?>
