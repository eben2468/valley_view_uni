<?php
/**
 * Update Modern Lecture Halls to Radio in the_campus page
 */

require_once 'includes/db_connect.php';

try {
    // Update the item
    $stmt = $pdo->prepare("
        UPDATE academic_pages_items 
        SET 
            item_title = 'Radio',
            item_description = 'Campus radio station broadcasting news, music, and student programs.',
            item_icon = 'radio'
        WHERE page_key = 'the_campus' 
          AND section_key = 'features' 
          AND item_title = 'Modern Lecture Halls'
    ");
    
    $stmt->execute();
    $affected = $stmt->rowCount();
    
    if ($affected > 0) {
        echo "✓ Successfully updated Modern Lecture Halls to Radio ($affected row(s) affected)\n";
        echo "\nYou can now edit this content in the admin panel at:\n";
        echo "admin/manage_academic_pages.php?page=the_campus\n";
    } else {
        echo "⚠ No rows were updated. The item might not exist or already has this content.\n";
        echo "\nChecking current features...\n\n";
        
        // Show current features
        $check = $pdo->prepare("
            SELECT item_title, item_description, item_icon 
            FROM academic_pages_items 
            WHERE page_key = 'the_campus' AND section_key = 'features'
            ORDER BY display_order
        ");
        $check->execute();
        $features = $check->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($features as $feature) {
            echo "- {$feature['item_title']} (icon: {$feature['item_icon']})\n";
            echo "  {$feature['item_description']}\n\n";
        }
    }
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
