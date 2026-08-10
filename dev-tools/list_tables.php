<?php
require_once 'includes/db_connect.php';
$stmt = $pdo->query("SHOW TABLES LIKE '%philosophy%'");
echo "Philosophy tables:\n";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) echo $row[0] . "\n";

$stmt = $pdo->query("SHOW TABLES LIKE '%accommodation%'");
echo "\nAccommodation tables:\n";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) echo $row[0] . "\n";

$stmt = $pdo->query("SHOW TABLES LIKE '%food_services%'");
echo "\nFood Services tables:\n";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) echo $row[0] . "\n";

$stmt = $pdo->query("SHOW TABLES LIKE '%work_study%'");
echo "\nWork Study tables:\n";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) echo $row[0] . "\n";

$stmt = $pdo->query("SHOW TABLES LIKE '%sld%'");
echo "\nSLD tables:\n";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) echo $row[0] . "\n";
?>
