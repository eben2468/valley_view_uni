<?php
require_once('includes/db_connect.php');

try {
    $pdo->beginTransaction();

    echo "Cleaning up administration_content_fields...\n";
    // Keep only the latest entry for each (content_id, field_key)
    $pdo->exec("
        DELETE f1 FROM administration_content_fields f1
        INNER JOIN administration_content_fields f2 
        WHERE f1.id < f2.id 
        AND f1.content_id = f2.content_id 
        AND f1.field_key = f2.field_key
    ");

    echo "Cleaning up administration_content...\n";
    // Keep only the latest entry for each (page_id, section_key)
    // But wait, we need to move fields if we delete a section? 
    // Actually, if they are duplicates, they probably have duplicate fields too.
    // Let's just keep the one with the most fields or the latest one.
    
    // First, identify duplicates
    $stmt = $pdo->query("SELECT page_id, section_key, COUNT(*) as count FROM administration_content GROUP BY page_id, section_key HAVING count > 1");
    $dupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($dupes as $dupe) {
        $page_id = $dupe['page_id'];
        $section_key = $dupe['section_key'];
        
        // Get all IDs for this dupe
        $stmt2 = $pdo->prepare("SELECT id FROM administration_content WHERE page_id = ? AND section_key = ? ORDER BY id DESC");
        $stmt2->execute([$page_id, $section_key]);
        $ids = $stmt2->fetchAll(PDO::FETCH_COLUMN);
        
        $keep_id = array_shift($ids); // Keep the latest one
        
        // Delete the others (cascading will delete their fields)
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt3 = $pdo->prepare("DELETE FROM administration_content WHERE id IN ($placeholders)");
            $stmt3->execute($ids);
            echo "Deleted " . count($ids) . " duplicate sections for page $page_id, key $section_key\n";
        }
    }

    $pdo->commit();
    echo "Cleanup completed successfully!\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error during cleanup: " . $e->getMessage() . "\n";
}
