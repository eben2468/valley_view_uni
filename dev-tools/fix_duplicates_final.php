<?php
require_once 'includes/db_connect.php';

try {
    echo "Starting full deduplication...\n";

    // Deduplicate academic_pages_sections
    $pdo->exec("
        DELETE t1 FROM academic_pages_sections t1
        INNER JOIN academic_pages_sections t2 
        WHERE t1.id > t2.id 
        AND t1.page_key = t2.page_key 
        AND t1.section_key = t2.section_key
    ");
    echo "Deduplicated academic_pages_sections.\n";

    // Deduplicate academic_pages_items
    $pdo->exec("
        DELETE t1 FROM academic_pages_items t1
        INNER JOIN academic_pages_items t2 
        WHERE t1.id > t2.id 
        AND t1.page_key = t2.page_key 
        AND t1.section_key = t2.section_key
        AND t1.item_title = t2.item_title
    ");
    echo "Deduplicated academic_pages_items.\n";

    // Now try to add unique constraints
    try {
        $pdo->exec("ALTER TABLE academic_pages_sections ADD UNIQUE KEY `unique_page_section` (page_key, section_key)");
        echo "Added unique constraint to academic_pages_sections.\n";
    } catch (PDOException $e) {
        echo "Section constraint exists or error: " . $e->getMessage() . "\n";
    }

    try {
        $pdo->exec("ALTER TABLE academic_pages_items ADD UNIQUE KEY `unique_page_section_item` (page_key, section_key, item_title)");
        echo "Added unique constraint to academic_pages_items.\n";
    } catch (PDOException $e) {
        echo "Item constraint exists or error: " . $e->getMessage() . "\n";
    }

    // Finally re-run the specific migration to ensure clean data for our target pages
    $sqlFile = 'sql/admissions_pages_content.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "Re-migration successful.\n";
    }

    echo "Fix complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
