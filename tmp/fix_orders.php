<?php
require_once('c:/xampp/htdocs/valley_view_uni/includes/db_connect.php');

$orders = [
    'hero' => 1,
    'director' => 2,
    'stats' => 3,
    'digital_resources' => 4,
    'about_libraries' => 5,
    'branch_libraries' => 6,
    'library_plans' => 7,
    'db_hero' => 8,
    'db_qr_ebooks' => 9,
    'db_online_resources' => 10,
    'db_partner_libraries' => 11,
    'db_quick_links' => 12,
    'on_hero' => 13,
    'on_general_resources' => 14,
    'on_health_resources' => 15,
    'on_science_resources' => 16,
    'on_arts_resources' => 17
];

foreach ($orders as $key => $order) {
    $stmt = $pdo->prepare("UPDATE administration_content SET content_order = ? WHERE section_key = ? AND page_id = 53");
    $stmt->execute([$order, $key]);
    echo "Updated order for {$key} to {$order}\n";
}
?>
