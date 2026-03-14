<?php
require_once('includes/db_connect.php');
$program_categories = $pdo->query("SELECT id, name FROM program_categories")->fetchAll(PDO::FETCH_ASSOC);
$homepage_programs = $pdo->query("SELECT id, title, category FROM homepage_programs")->fetchAll(PDO::FETCH_ASSOC);
echo "### PROGRAM CATEGORIES ###\n";
print_r($program_categories);
echo "\n### HOMEPAGE PROGRAMS ###\n";
print_r($homepage_programs);
?>
