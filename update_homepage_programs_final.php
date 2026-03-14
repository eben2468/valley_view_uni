<?php
require_once('includes/db_connect.php');

// Fetch all categories
$categories = $pdo->query("SELECT * FROM program_categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$homepage_update_id = 1;

foreach ($categories as $cat) {
    // Fetch one program for this category
    $stmt = $pdo->prepare("SELECT * FROM academic_programs WHERE category_id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$cat['id']]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($program && $homepage_update_id <= 8) {
        $update_stmt = $pdo->prepare("UPDATE homepage_programs SET title = ?, category = ?, description = ?, link_url = ? WHERE id = ?");
        // Truncate description if too long for homepage
        $short_desc = mb_strimwidth($program['description'], 0, 150, "...");
        
        // Fix link if empty
        $link = $program['link_url'] ?: 'academics.php';
        
        $update_stmt->execute([
            $program['title'],
            $cat['name'],
            $short_desc,
            $link,
            $homepage_update_id
        ]);
        
        echo "Updated homepage program slot $homepage_update_id with '{$program['title']}' (Category: {$cat['name']})\n";
        $homepage_update_id++;
    }
}

echo "Homepage programs update complete.\n";
?>
