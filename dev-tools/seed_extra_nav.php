<?php
require_once('includes/db_connect.php');

try {
    // 1. Seed Topbar Navigation
    $topbar_nav = [
        ['title' => 'Schools & Faculties', 'url' => 'academic_programs_overview.php'],
        ['title' => 'Academic Calendar', 'url' => 'academic_calendar.php'],
        ['title' => 'Student Portal', 'url' => 'https://ischool.vvu.edu.gh/Default.aspx'],
        ['title' => 'E-Learning', 'url' => 'https://learning.vvu.edu.gh/'],
        ['title' => 'Library', 'url' => 'library_resources.php']
    ];

    $stmt = $pdo->prepare("INSERT INTO navigation_items (title, url, menu_type, sort_order) VALUES (?, ?, 'topbar', ?)");
    foreach ($topbar_nav as $idx => $item) {
        $stmt->execute([$item['title'], $item['url'], $idx]);
    }
    echo "Topbar navigation seeded.<br>\n";

    // 2. Seed Quick Access (Mobile Menu)
    $quick_nav = [
        ['title' => 'Freshmen Info', 'url' => 'freshmen_info.php'],
        ['title' => 'iSchool', 'url' => 'https://ischool.vvu.edu.gh/Default.aspx'],
        ['title' => 'E-Learning', 'url' => 'https://learning.vvu.edu.gh/'],
        ['title' => 'Donate', 'url' => 'https://www.vvuf.org/'],
        ['title' => 'Alumni', 'url' => 'alumni_network_page_1.php'],
        ['title' => 'Contact', 'url' => 'contact_us.php']
    ];

    $stmt = $pdo->prepare("INSERT INTO navigation_items (title, url, menu_type, sort_order) VALUES (?, ?, 'quickaccess', ?)");
    foreach ($quick_nav as $idx => $item) {
        $stmt->execute([$item['title'], $item['url'], $idx]);
    }
    echo "Quick Access seeded.<br>\n";

    // 3. Seed Topbar Settings
    $settings = [
        ['contact_address', 'Contact: Valley View University, Oyibi, Accra'],
        ['contact_phone', '+233 307 051149']
    ];

    $stmt = $pdo->prepare("INSERT INTO topbar_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach ($settings as $s) {
        $stmt->execute([$s[0], $s[1]]);
    }
    echo "Topbar settings seeded.<br>\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
