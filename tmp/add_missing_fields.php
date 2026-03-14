<?php
require_once('c:/xampp/htdocs/valley_view_uni/includes/db_connect.php');

$sections = [
    'db_partner_libraries' => [
        ['key' => 'section_badge', 'type' => 'text', 'value' => 'Partner Institutions'],
        ['key' => 'section_icon', 'type' => 'text', 'value' => 'handshake']
    ],
    'db_quick_links' => [
        ['key' => 'section_badge', 'type' => 'text', 'value' => 'Quick Access'],
        ['key' => 'section_icon', 'type' => 'text', 'value' => 'link'],
        ['key' => 'section_title', 'type' => 'text', 'value' => 'Useful Links']
    ]
];

foreach ($sections as $section_key => $fields) {
    // Get section ID
    $stmt = $pdo->prepare("SELECT id FROM administration_content WHERE section_key = ? AND page_id = 53");
    $stmt->execute([$section_key]);
    $section_id = $stmt->fetchColumn();
    
    if ($section_id) {
        foreach ($fields as $field) {
            // Check if field exists
            $fstmt = $pdo->prepare("SELECT id FROM administration_content_fields WHERE content_id = ? AND field_key = ?");
            $fstmt->execute([$section_id, $field['key']]);
            if (!$fstmt->fetch()) {
                // Insert field
                $istmt = $pdo->prepare("INSERT INTO administration_content_fields (content_id, field_key, field_type, field_value) VALUES (?, ?, ?, ?)");
                $istmt->execute([$section_id, $field['key'], $field['type'], $field['value']]);
                echo "Added field {$field['key']} to section {$section_key}\n";
            } else {
                echo "Field {$field['key']} already exists in section {$section_key}\n";
            }
        }
    } else {
        echo "Section {$section_key} not found for page 53\n";
    }
}
?>
