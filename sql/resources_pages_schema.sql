-- Resource Pages Content Schema
-- Stores full HTML content for resource pages editable in the admin portal

CREATE TABLE IF NOT EXISTS resources_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(60) NOT NULL UNIQUE,
    page_title VARCHAR(255) NOT NULL,
    page_content LONGTEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
