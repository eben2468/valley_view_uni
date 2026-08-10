<?php
require_once('includes/db_connect.php');
$programs = $pdo->query("SELECT ap.id, ap.title, pc.name as category_name FROM academic_programs ap JOIN program_categories pc ON ap.category_id = pc.id LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
echo "### ACADEMIC PROGRAMS ###\n";
print_r($programs);
?>
