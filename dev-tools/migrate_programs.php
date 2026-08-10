<?php
require_once('includes/db_connect.php');

$sql = "
-- Categories table
CREATE TABLE IF NOT EXISTS program_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(100) DEFAULT 'school',
    color_1 VARCHAR(20) DEFAULT '#3b82f6',
    color_2 VARCHAR(20) DEFAULT '#2563eb',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Programs table extension/creation
DROP TABLE IF EXISTS academic_programs;
CREATE TABLE academic_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    full_description TEXT,
    link_url VARCHAR(255),
    duration VARCHAR(100) DEFAULT '4 Years (Full Time)',
    level VARCHAR(50) DEFAULT 'Undergraduate',
    campus VARCHAR(100) DEFAULT 'Main Campus',
    learning_points JSON,
    career_paths JSON,
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES program_categories(id) ON DELETE SET NULL
);

-- Content table for static sections
CREATE TABLE IF NOT EXISTS academic_programs_page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) UNIQUE,
    hero_title VARCHAR(255),
    hero_subtitle TEXT,
    hero_badge VARCHAR(100),
    hero_image VARCHAR(255),
    cta_title VARCHAR(255),
    cta_subtitle TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Stats table
CREATE TABLE IF NOT EXISTS academic_programs_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_value VARCHAR(50),
    stat_label VARCHAR(100),
    display_order INT DEFAULT 0
);
";

try {
    $pdo->exec($sql);
    echo "Tables created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>
