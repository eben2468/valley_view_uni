<?php
require_once 'includes/db_connect.php';

$page_slug = 'academic_bulletin';
$stmt = $pdo->prepare("SELECT id FROM administration_pages WHERE page_slug = ?");
$stmt->execute([$page_slug]);
$page_id = $stmt->fetchColumn();

if (!$page_id) die("Page not found.");

$fields_to_add = [
    ['hero', 'badge_text', 'Academic Resources', 'text'],
    ['downloads', 'title_1', 'Download', 'text'],
    ['downloads', 'title_2', 'Bulletins', 'text'],
    ['downloads', 'subtitle', 'Access the latest versions of our academic bulletins.', 'textarea'],
    ['downloads', 'button_text', 'Download PDF', 'text']
];

foreach ($fields_to_add as $f) {
    $section_key = $f[0];
    $field_key = $f[1];
    $default_value = $f[2];
    $field_type = $f[3];

    $stmt = $pdo->prepare("SELECT id FROM administration_content WHERE page_id = ? AND section_key = ?");
    $stmt->execute([$page_id, $section_key]);
    $section_id = $stmt->fetchColumn();

    if ($section_id) {
        $stmt = $pdo->prepare("SELECT id FROM administration_content_fields WHERE content_id = ? AND field_key = ?");
        $stmt->execute([$section_id, $field_key]);
        if (!$stmt->fetchColumn()) {
            $stmt = $pdo->prepare("INSERT INTO administration_content_fields (content_id, field_key, field_type, field_value) VALUES (?, ?, ?, ?)");
            $stmt->execute([$section_id, $field_key, $field_type, $default_value]);
            echo "Added $section_key -> $field_key\n";
        }
    }
}
echo "Done.";
