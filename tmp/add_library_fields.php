<?php
require_once 'includes/db_connect.php';

$page_slug = 'library_resources';
$stmt = $pdo->prepare("SELECT id FROM administration_pages WHERE page_slug = ?");
$stmt->execute([$page_slug]);
$page_id = $stmt->fetchColumn();

if (!$page_id) {
    die("Page not found.");
}

$fields_to_add = [
    // hero section
    ['hero', 'badge_text', 'Knowledge Hub', 'text'],

    // director section
    ['director', 'welcome_badge', 'Welcome Message', 'text'],
    ['director', 'welcome_heading_1', 'Welcome to the', 'text'],
    ['director', 'welcome_heading_2', 'VVU Library', 'text'],

    // about_libraries section
    ['about_libraries', 'about_badge', 'About Our Libraries', 'text'],

    // library_plans section
    ['library_plans', 'plans_badge', 'Future Plans', 'text'],
    ['library_plans', 'new_building_title', 'New Library Building', 'text'],
    ['library_plans', 'automation_title', 'Automation & Digitization', 'text'],
    ['library_plans', 'operations_title', 'Library Operations', 'text'],
    ['library_plans', 'technology_title', 'Technology Plan', 'text'],
    ['library_plans', 'funding_title', 'Funding & Infrastructure', 'text']
];

foreach ($fields_to_add as $f) {
    $section_key = $f[0];
    $field_key = $f[1];
    $default_value = $f[2];
    $field_type = $f[3];

    // get section id
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
