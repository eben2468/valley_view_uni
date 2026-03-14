-- Navigation Management System Tables
-- Run this SQL to create the navigation tables

-- Main navigation items table
CREATE TABLE IF NOT EXISTS navigation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT DEFAULT NULL,
    menu_type ENUM('main', 'topbar', 'mobile', 'quickaccess') DEFAULT 'main',
    title VARCHAR(255) NOT NULL,
    url VARCHAR(500) DEFAULT '#',
    icon_class VARCHAR(100) DEFAULT NULL,
    menu_class VARCHAR(100) DEFAULT NULL,
    target VARCHAR(20) DEFAULT '_self',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    has_megamenu TINYINT(1) DEFAULT 0,
    megamenu_type VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES navigation_items(id) ON DELETE CASCADE
);

-- Navigation sub-sections (for mega menu columns)
CREATE TABLE IF NOT EXISTS navigation_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    navigation_item_id INT NOT NULL,
    section_title VARCHAR(255) NOT NULL,
    section_type ENUM('links', 'featured', 'description') DEFAULT 'links',
    column_position INT DEFAULT 1,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    featured_image VARCHAR(500) DEFAULT NULL,
    featured_link VARCHAR(500) DEFAULT NULL,
    featured_text VARCHAR(500) DEFAULT NULL,
    description_text TEXT DEFAULT NULL,
    button_text VARCHAR(100) DEFAULT NULL,
    button_link VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (navigation_item_id) REFERENCES navigation_items(id) ON DELETE CASCADE
);

-- Navigation sub-links (links within sections)
CREATE TABLE IF NOT EXISTS navigation_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(500) DEFAULT '#',
    target VARCHAR(20) DEFAULT '_self',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES navigation_sections(id) ON DELETE CASCADE
);

-- Top bar settings
CREATE TABLE IF NOT EXISTS topbar_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default topbar settings
INSERT INTO topbar_settings (setting_key, setting_value) VALUES
('contact_address', 'Valley View University, Oyibi, Accra'),
('contact_phone', '+233 307 051 149')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
