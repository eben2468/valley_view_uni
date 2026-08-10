<?php
require_once('includes/db_connect.php');

try {
    echo "Adding unique constraint to administration_content...\n";
    $pdo->exec("ALTER TABLE administration_content ADD UNIQUE KEY unique_section (page_id, section_key)");
    
    echo "Adding unique constraint to administration_content_fields...\n";
    $pdo->exec("ALTER TABLE administration_content_fields ADD UNIQUE KEY unique_field (content_id, field_key)");
    
    echo "Constraints added successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
