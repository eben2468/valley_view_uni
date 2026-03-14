<?php
require_once('includes/db_connect.php');

try {
    // Update the link for 'Provisional Admissions List' in the navigation_links table
    $stmt = $pdo->prepare("UPDATE navigation_links SET url = 'provisional_admission_list.php' WHERE title = 'Provisional Admissions List'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "Navigation link updated successfully.";
    } else {
        echo "Link not found or already updated.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
