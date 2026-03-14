<?php
require_once 'includes/db_connect.php';

$pages = [
    'provisional_admission_list',
    'entry_requirements',
    'caution_to_applicants',
    'scholarships',
    'scholarships_forms'
];

try {
    $pdo->beginTransaction();

    // 1. Remove all sections and items for these pages to start fresh
    $placeholders = implode(',', array_fill(0, count($pages), '?'));
    
    $pdo->prepare("DELETE FROM academic_pages_items WHERE page_key IN ($placeholders)")->execute($pages);
    echo "Deleted all items for targeted pages.\n";
    
    $pdo->prepare("DELETE FROM academic_pages_sections WHERE page_key IN ($placeholders)")->execute($pages);
    echo "Deleted all sections for targeted pages.\n";

    // 2. Add Unique Constraints to prevent future duplicates if they don't exist
    // Check if index exists first (optional but safer)
    try {
        $pdo->exec("ALTER TABLE academic_pages_sections ADD UNIQUE KEY `unique_page_section` (page_key, section_key)");
        echo "Added unique constraint to academic_pages_sections.\n";
    } catch (PDOException $e) {
        echo "Unique constraint for sections already exists or error: " . $e->getMessage() . "\n";
    }

    try {
        // For items, we use a combination of title and section to define uniqueness for the migration logic
        $pdo->exec("ALTER TABLE academic_pages_items ADD UNIQUE KEY `unique_page_section_item` (page_key, section_key, item_title)");
        echo "Added unique constraint to academic_pages_items.\n";
    } catch (PDOException $e) {
        echo "Unique constraint for items already exists or error: " . $e->getMessage() . "\n";
    }

    $pdo->commit();
    echo "Cleanup and constraint update successful.\n";

    // 3. Re-run migration
    $sqlFile = 'sql/admissions_pages_content.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "Re-migration from sql/admissions_pages_content.sql successful.\n";
    } else {
        echo "Migration file not found: $sqlFile\n";
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
?>
