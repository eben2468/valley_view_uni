<?php
/**
 * Resource Pages Helper
 * Stores and retrieves resource page content from the database.
 */

function ensureResourcePagesTable(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS resources_pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_key VARCHAR(60) NOT NULL UNIQUE,
            page_title VARCHAR(255) NOT NULL,
            page_content LONGTEXT NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

function getResourcePage(PDO $pdo, string $page_key): ?array {
    ensureResourcePagesTable($pdo);
    $stmt = $pdo->prepare("SELECT * FROM resources_pages WHERE page_key = ? LIMIT 1");
    $stmt->execute([$page_key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function upsertResourcePage(PDO $pdo, string $page_key, string $page_title, string $page_content): void {
    ensureResourcePagesTable($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO resources_pages (page_key, page_title, page_content, is_active)
        VALUES (?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE
            page_title = VALUES(page_title),
            page_content = VALUES(page_content),
            is_active = 1
    ");
    $stmt->execute([$page_key, $page_title, $page_content]);
}
?>
