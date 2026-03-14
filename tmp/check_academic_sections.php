<?php
require_once 'includes/db_connect.php';
$stmt = $pdo->query("SELECT * FROM academic_pages_sections WHERE page_key = 'provisional_admission_list'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
