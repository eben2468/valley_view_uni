<?php
require_once 'includes/db_connect.php';
try {
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM news_articles GROUP BY category");
    while ($row = $stmt->fetch()) {
        echo $row['category'] . ": " . $row['count'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
