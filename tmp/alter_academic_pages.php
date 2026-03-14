<?php
require_once 'includes/db_connect.php';

try {
    $pdo->exec("ALTER TABLE academic_pages_content 
        ADD COLUMN cta_button_text_2 VARCHAR(100) NULL AFTER cta_button_link,
        ADD COLUMN cta_button_link_2 VARCHAR(255) NULL AFTER cta_button_text_2,
        ADD COLUMN help_title VARCHAR(255) NULL AFTER cta_button_link_2,
        ADD COLUMN help_description TEXT NULL AFTER help_title,
        ADD COLUMN help_phone VARCHAR(50) NULL AFTER help_description,
        ADD COLUMN empty_list_message VARCHAR(255) NULL AFTER help_phone
    ");
    echo "Columns added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
