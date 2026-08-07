<?php
/**
 * Creates and seeds the tables behind the "Join Our Community of Excellence"
 * call-to-action block at the bottom of mission_and_vision.php.
 *
 * That section (heading, subtitle, the two buttons and the three quick-link
 * cards) was hard-coded in the template. These tables make it editable from
 * Admin > About Pages > Mission & Vision.
 *
 * Safe to run more than once: tables are only created if missing, and seed
 * rows are only inserted when the table is empty, so your edits are never
 * overwritten.
 *
 * Usage:  php install_mission_vision_cta.php
 *     or: http://localhost/valley_view_uni/install_mission_vision_cta.php
 */

require_once __DIR__ . '/includes/db_connect.php';

$isCli = (php_sapi_name() === 'cli');
$log   = [];

try {
    // ---- Main CTA block (single row) ----
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `mission_vision_cta` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `heading` varchar(255) NOT NULL,
            `subtitle` text DEFAULT NULL,
            `primary_btn_text` varchar(100) DEFAULT NULL,
            `primary_btn_link` varchar(255) DEFAULT NULL,
            `primary_btn_icon` varchar(50) DEFAULT 'info',
            `secondary_btn_text` varchar(100) DEFAULT NULL,
            `secondary_btn_link` varchar(255) DEFAULT NULL,
            `secondary_btn_icon` varchar(50) DEFAULT 'how_to_reg',
            `links_eyebrow` varchar(100) DEFAULT 'Explore More',
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    $log[] = 'Table `mission_vision_cta` ready.';

    // ---- The three quick-link cards ----
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `mission_vision_cta_links` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `icon` varchar(50) NOT NULL DEFAULT 'star',
            `title` varchar(100) NOT NULL,
            `description` varchar(255) DEFAULT NULL,
            `link_url` varchar(255) NOT NULL DEFAULT '#',
            `display_order` int(11) DEFAULT 0,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    $log[] = 'Table `mission_vision_cta_links` ready.';

    // ---- Seed only if empty, so re-running never clobbers edits ----
    if ((int) $pdo->query("SELECT COUNT(*) FROM mission_vision_cta")->fetchColumn() === 0) {
        $pdo->prepare("
            INSERT INTO mission_vision_cta
                (heading, subtitle, primary_btn_text, primary_btn_link, primary_btn_icon,
                 secondary_btn_text, secondary_btn_link, secondary_btn_icon, links_eyebrow, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ")->execute([
            'Join Our Community of Excellence',
            'Discover how Valley View University can help you achieve holistic development and prepare for meaningful service to God and humanity.',
            'Learn More About VVU', 'about_us.php', 'info',
            'Apply Now', 'apply.php', 'how_to_reg',
            'Explore More',
        ]);
        $log[] = 'Seeded the CTA heading, subtitle and buttons from the current page.';
    } else {
        $log[] = 'CTA row already present — left untouched.';
    }

    if ((int) $pdo->query("SELECT COUNT(*) FROM mission_vision_cta_links")->fetchColumn() === 0) {
        $stmt = $pdo->prepare("
            INSERT INTO mission_vision_cta_links (icon, title, description, link_url, display_order, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $seed = [
            ['star', 'Our Core Values', 'The beliefs that shape how we teach, serve and live.', 'core_values.php', 1],
            ['menu_book', 'Academic Programs', 'Undergraduate, graduate and professional courses.', 'academic_programs_overview.php', 2],
            ['location_city', 'Visit Our Campus', 'See Oyibi for yourself — facilities, halls and green space.', 'the_campus.php', 3],
        ];
        foreach ($seed as $row) {
            $stmt->execute($row);
        }
        $log[] = 'Seeded the 3 quick-link cards.';
    } else {
        $log[] = 'Quick-link cards already present — left untouched.';
    }

    $cta   = $pdo->query("SELECT * FROM mission_vision_cta ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $links = $pdo->query("SELECT * FROM mission_vision_cta_links ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);

    $log[] = 'Done: 1 CTA block, ' . count($links) . ' quick-link card(s).';

} catch (PDOException $e) {
    $log[] = 'ERROR: ' . $e->getMessage();
}

if ($isCli) {
    foreach ($log as $line) {
        echo $line . "\n";
    }
    echo "\nNext: Admin > About Pages > Mission & Vision (scroll to the bottom).\n";
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>Install Mission &amp; Vision CTA</title>';
    echo '<style>body{font-family:system-ui,sans-serif;max-width:720px;margin:40px auto;padding:0 20px;line-height:1.7;color:#222}
          h1{color:#002147} li{margin-bottom:6px} code{background:#f1f5f9;padding:2px 6px;border-radius:4px}
          .note{background:#f0fdf4;border-left:4px solid #16a34a;padding:12px 16px;margin:20px 0}</style>';
    echo '<h1>Mission &amp; Vision CTA installed</h1><ul>';
    foreach ($log as $line) {
        echo '<li>' . htmlspecialchars($line) . '</li>';
    }
    echo '</ul>';
    echo '<div class="note">Edit it at <strong>Admin &rarr; About Pages &rarr; Mission &amp; Vision</strong>, at the bottom of the tab.</div>';
    echo '<p><strong>Delete this file from the server once it has run.</strong></p>';
}
