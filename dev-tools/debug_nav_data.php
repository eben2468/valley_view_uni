<?php
include 'includes/db_connect.php';
include 'includes/navigation_helper.php';
// $pdo is already defined in db_connect.php
$main_nav = getNavItems($pdo, 'main');
foreach ($main_nav as $item) {
    echo "ID: {$item['id']} | Title: {$item['title']} | Menu Class: {$item['menu_class']} | Mega Type: {$item['megamenu_type']}\n";
    if (!empty($item['sections'])) {
        foreach ($item['sections'] as $sec) {
            echo "   Section: {$sec['section_title']} | Type: {$sec['section_type']} | Col: {$sec['column_position']}\n";
        }
    }
}
?>
