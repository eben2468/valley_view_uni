<?php
/**
 * Adds the "Graduation Status" link (https://status.vvu.edu.gh/) to the
 * top bar and the mobile Quick Access menu. Safe to re-run.
 */
require_once __DIR__ . '/includes/db_connect.php';

$url   = 'https://status.vvu.edu.gh/';
$title = 'Graduation Status';

foreach (['topbar' => 7, 'quickaccess' => 6] as $menu_type => $sort_order) {
    $check = $pdo->prepare("SELECT id FROM navigation_items WHERE menu_type = ? AND url = ?");
    $check->execute([$menu_type, $url]);

    if ($existing = $check->fetchColumn()) {
        $pdo->prepare("UPDATE navigation_items SET title = ?, target = '_blank', sort_order = ?, is_active = 1 WHERE id = ?")
            ->execute([$title, $sort_order, $existing]);
        echo "Updated {$menu_type} link (id {$existing})\n";
        continue;
    }

    // Make room by pushing existing items at/after this position down one slot
    $pdo->prepare("UPDATE navigation_items SET sort_order = sort_order + 1 WHERE menu_type = ? AND sort_order >= ?")
        ->execute([$menu_type, $sort_order]);

    $pdo->prepare("INSERT INTO navigation_items (menu_type, title, url, target, sort_order, is_active) VALUES (?, ?, ?, '_blank', ?, 1)")
        ->execute([$menu_type, $title, $url, $sort_order]);
    echo "Added {$menu_type} link (id " . $pdo->lastInsertId() . ")\n";
}

echo "Done.\n";
