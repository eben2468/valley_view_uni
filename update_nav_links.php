<?php
require_once 'includes/db_connect.php';

try {
    // Update Faculty Encyclopedia link if it exists
    $stmt = $pdo->prepare("UPDATE navigation_links SET url = 'faculty_encyclopedia.php' WHERE title LIKE '%Faculty Encyclopedia%'");
    $stmt->execute();
    
    // Update Staff Encyclopedia link if it exists
    $stmt = $pdo->prepare("UPDATE navigation_links SET url = 'staff_encyclopedia.php' WHERE title LIKE '%Staff Encyclopedia%'");
    $stmt->execute();

    echo "Navigation links updated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
