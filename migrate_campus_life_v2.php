<?php
require_once 'includes/db_connect.php';

function addColumnIfNotExists($pdo, $table, $column, $type) {
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $type");
        echo "Added $column to $table\n";
    } else {
        echo "Column $column already exists in $table\n";
    }
}

try {
    // 1. Philosophy on Dress updates
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'principles_heading', "varchar(255) DEFAULT 'Biblical Principles of Dress'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'principles_subtitle', "text");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'holistic_heading', "varchar(255) DEFAULT 'A Value-Based Holistic Education'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'adventist_values_title', "varchar(255) DEFAULT 'Seventh-day Adventist Values'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'adventist_values_text', "text");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'universal_respect_title', "varchar(255) DEFAULT 'Universal Respect'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'universal_respect_text', "text");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'total_person_title', "varchar(255) DEFAULT 'Total Person Development'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'total_person_text', "text");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'quote_text', "text");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'quote_author', "varchar(255) DEFAULT 'Valley View University'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'quote_author_title', "varchar(255) DEFAULT 'Official Dress Policy'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'guidelines_heading', "varchar(255) DEFAULT 'What We Encourage'");
    addColumnIfNotExists($pdo, 'philosophy_on_dress_content', 'guidelines_subtitle', "text");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `philosophy_dress_principles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text,
        `icon` varchar(100) DEFAULT 'shield',
        `border_color` varchar(50) DEFAULT 'purple-600',
        `display_order` int(11) DEFAULT 0,
        `status` enum('active','inactive') DEFAULT 'active',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `philosophy_dress_benefits` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text,
        `icon` varchar(100) DEFAULT 'groups',
        `gradient_start` varchar(50) DEFAULT 'purple-500',
        `gradient_end` varchar(50) DEFAULT 'purple-700',
        `display_order` int(11) DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 2. Accommodation updates
    addColumnIfNotExists($pdo, 'accommodation_content', 'off_campus_heading', "varchar(255) DEFAULT 'Off-Campus Living'");
    addColumnIfNotExists($pdo, 'accommodation_content', 'off_campus_text', "text");
    addColumnIfNotExists($pdo, 'accommodation_content', 'dining_heading', "varchar(255) DEFAULT 'Dining Services'");
    addColumnIfNotExists($pdo, 'accommodation_content', 'dining_subheading', "varchar(255) DEFAULT 'Wholesome & Healthy Vegetarian Meals'");
    addColumnIfNotExists($pdo, 'accommodation_content', 'dining_text', "text");
    addColumnIfNotExists($pdo, 'accommodation_content', 'dining_list', "text");
    addColumnIfNotExists($pdo, 'accommodation_content', 'dining_image', "varchar(255)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `accommodation_halls` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` enum('male','female') NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text,
        `halls_list` text,
        `image` varchar(255),
        `gradient_start` varchar(50) DEFAULT 'blue-600',
        `gradient_end` varchar(50) DEFAULT 'blue-800',
        `icon` varchar(100) DEFAULT 'man',
        `display_order` int(11) DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 3. Food Services updates
    addColumnIfNotExists($pdo, 'food_services_content', 'breakfast_desc', "text");
    addColumnIfNotExists($pdo, 'food_services_content', 'lunch_desc', "text");
    addColumnIfNotExists($pdo, 'food_services_content', 'dinner_desc', "text");
    addColumnIfNotExists($pdo, 'food_services_content', 'meal_plans_heading', "varchar(255) DEFAULT 'Flexible Meal Plans'");
    addColumnIfNotExists($pdo, 'food_services_content', 'meal_plans_text', "text");
    addColumnIfNotExists($pdo, 'food_services_content', 'meal_plans_btn_text', "varchar(100) DEFAULT 'Register on Portal'");
    addColumnIfNotExists($pdo, 'food_services_content', 'meal_plans_btn_url', "varchar(255) DEFAULT '#'");
    addColumnIfNotExists($pdo, 'food_services_content', 'meal_plans_reg_info', "text");

    // 4. Work Study updates
    addColumnIfNotExists($pdo, 'work_study_content', 'info_heading', "varchar(255) DEFAULT 'Important Information'");
    addColumnIfNotExists($pdo, 'work_study_content', 'info_subtitle', "text");
    addColumnIfNotExists($pdo, 'work_study_content', 'stats_opportunities', "varchar(50) DEFAULT '50+'");
    addColumnIfNotExists($pdo, 'work_study_content', 'steps_heading', "varchar(255) DEFAULT 'How to Apply'");
    addColumnIfNotExists($pdo, 'work_study_content', 'steps_subtitle', "text");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `work_study_steps` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `step_number` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text,
        `color` varchar(50) DEFAULT 'blue',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 5. SLD updates
    addColumnIfNotExists($pdo, 'sld_content', 'stats_staff_count', "varchar(50) DEFAULT '10+'");
    addColumnIfNotExists($pdo, 'sld_content', 'stats_locations_count', "varchar(50) DEFAULT '3'");
    addColumnIfNotExists($pdo, 'sld_content', 'stats_support_text', "varchar(50) DEFAULT '24/7'");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `sld_locations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text,
        `icon` varchar(100) DEFAULT 'location_on',
        `display_order` int(11) DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "\nMigration successful!\n";
} catch (Exception $e) {
    echo "\nMigration failed: " . $e->getMessage() . "\n";
}
