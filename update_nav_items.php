<?php
include 'includes/db_connect.php';

try {
    // Add active_key column if it doesn't exist
    $result = $pdo->query("SHOW COLUMNS FROM navigation_items LIKE 'active_key'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE navigation_items ADD COLUMN active_key VARCHAR(50) DEFAULT NULL AFTER target");
        echo "Added active_key column.\n";
    }

    // Update data
    $updates = [
        ['HOME', '', 'home'],
        ['ABOUT', 'about-menu', 'about'],
        ['ACADEMICS', 'admi-menu', 'academics'],
        ['ADMISSIONS', 'admi-menu', 'admissions'],
        ['LIFE @ VVU', 'admi-menu', 'student_life'],
        ['STORIES', 'about-menu', null],
        ['RESOURCES', 'admi-menu', null],
        ['VENTURES', 'admi-menu', null]
    ];

    $stmt = $pdo->prepare("UPDATE navigation_items SET menu_class = ?, active_key = ? WHERE title = ?");
    foreach ($updates as $u) {
        $stmt->execute([$u[1], $u[2], $u[0]]);
        echo "Updated {$u[0]}\n";
    }

    echo "Navigation items updated successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
