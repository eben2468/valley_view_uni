-- Database schema for Strategic Plan, Policies, History, and Accreditation & Charter pages
-- Author: Valley View University Admin System
-- Date: 2025

-- ============================================
-- STRATEGIC PLAN PAGE TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS strategic_plan_hero (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_subtitle VARCHAR(255) NOT NULL DEFAULT 'Vision 2026 & Beyond',
    hero_title_1 VARCHAR(255) NOT NULL DEFAULT 'Strategic Plan',
    hero_title_2 VARCHAR(255) NOT NULL DEFAULT 'Shaping Our Future',
    hero_description TEXT NOT NULL,
    hero_image_url TEXT NOT NULL,
    download_button_text VARCHAR(100) DEFAULT 'Download Vision 2025 (PDF)',
    download_pdf_url VARCHAR(255) DEFAULT 'uploads/VISION 2025.pdf',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS strategic_plan_president_message (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_title VARCHAR(255) NOT NULL DEFAULT 'A Message From The Vice Chancellor',
    president_image_url TEXT NOT NULL,
    message_quote TEXT NOT NULL,
    message_author VARCHAR(255) DEFAULT '— Join us as we build the future of Valley View.',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS strategic_plan_pillars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    icon VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    feature_1 VARCHAR(255),
    feature_2 VARCHAR(255),
    border_color VARCHAR(50) DEFAULT 'blue-600',
    bg_color VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS strategic_plan_timeline (
    id INT PRIMARY KEY AUTO_INCREMENT,
    phase_number INT NOT NULL,
    phase_badge VARCHAR(100) NOT NULL,
    phase_title VARCHAR(255) NOT NULL,
    phase_description TEXT NOT NULL,
    border_color VARCHAR(50) DEFAULT 'blue-600',
    dot_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS strategic_plan_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stat_value VARCHAR(50) NOT NULL,
    stat_label VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS strategic_plan_cta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cta_title_1 VARCHAR(255) NOT NULL DEFAULT 'Join Us on Our Journey,',
    cta_title_2 VARCHAR(255) NOT NULL DEFAULT 'Build the Future',
    cta_description TEXT NOT NULL,
    button_1_text VARCHAR(100) DEFAULT 'Download Full Plan (PDF)',
    button_1_url VARCHAR(255) DEFAULT 'uploads/VISION 2025.pdf',
    button_2_text VARCHAR(100) DEFAULT 'Contact Us',
    button_2_url VARCHAR(255) DEFAULT 'contact_us.php',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- POLICIES PAGE TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS policies_hero (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_subtitle VARCHAR(255) NOT NULL DEFAULT 'Governance & Standards',
    hero_title VARCHAR(255) NOT NULL DEFAULT 'University',
    hero_subtitle VARCHAR(255) NOT NULL DEFAULT 'Policies',
    hero_description TEXT NOT NULL,
    hero_image_url TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS policies_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    icon VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    border_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS policies_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    document_title VARCHAR(255) NOT NULL,
    document_url VARCHAR(255) NOT NULL,
    icon_color VARCHAR(50) DEFAULT 'blue-600',
    bg_color VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES policies_categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS policies_quick_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    icon VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    link_text VARCHAR(100) NOT NULL,
    link_url VARCHAR(255) NOT NULL,
    icon_bg_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS policies_cta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cta_title_1 VARCHAR(255) NOT NULL DEFAULT 'Committed to',
    cta_title_2 VARCHAR(255) NOT NULL DEFAULT 'Integrity & Transparency',
    cta_description TEXT NOT NULL,
    button_1_text VARCHAR(100) DEFAULT 'Our Mission',
    button_1_url VARCHAR(255) DEFAULT 'mission_and_vision.php',
    button_2_text VARCHAR(100) DEFAULT 'Our Values',
    button_2_url VARCHAR(255) DEFAULT 'core_values.php',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- HISTORY PAGE TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS history_hero (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_subtitle VARCHAR(255) NOT NULL DEFAULT 'Our Legacy',
    hero_title VARCHAR(255) NOT NULL DEFAULT 'The Journey',
    hero_subtitle VARCHAR(255) NOT NULL DEFAULT 'Of Excellence',
    hero_description TEXT NOT NULL,
    hero_image_url TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS history_overview (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_title VARCHAR(255) NOT NULL DEFAULT 'A Visionary Beginning',
    paragraph_1 TEXT NOT NULL,
    paragraph_2 TEXT NOT NULL,
    founded_year VARCHAR(10) DEFAULT '1979',
    chartered_year VARCHAR(10) DEFAULT '2006',
    overview_image_url TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS history_milestones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    year VARCHAR(10) NOT NULL,
    milestone_title VARCHAR(255) NOT NULL,
    milestone_description TEXT NOT NULL,
    border_color VARCHAR(50) DEFAULT 'blue-600',
    dot_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS history_community (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_title VARCHAR(255) NOT NULL DEFAULT 'A Global Community',
    section_description TEXT NOT NULL,
    feature_1_title VARCHAR(100) DEFAULT 'Global',
    feature_1_label VARCHAR(100) DEFAULT 'Reach',
    feature_2_title VARCHAR(100) DEFAULT 'Inclusive',
    feature_2_label VARCHAR(100) DEFAULT 'Community',
    feature_3_title VARCHAR(100) DEFAULT 'Chartered',
    feature_3_label VARCHAR(100) DEFAULT 'Excellence',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS history_cta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cta_title_1 VARCHAR(255) NOT NULL DEFAULT 'Be Part of Our',
    cta_title_2 VARCHAR(255) NOT NULL DEFAULT 'Future History',
    cta_description TEXT NOT NULL,
    button_1_text VARCHAR(100) DEFAULT 'Apply Now',
    button_1_url VARCHAR(255) DEFAULT 'admissions.php',
    button_2_text VARCHAR(100) DEFAULT 'Contact Us',
    button_2_url VARCHAR(255) DEFAULT 'contact_us.php',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- ACCREDITATION & CHARTER PAGE TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS accreditation_hero (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_subtitle VARCHAR(255) NOT NULL DEFAULT 'Quality Assurance',
    hero_title VARCHAR(255) NOT NULL DEFAULT 'Accreditation',
    hero_subtitle VARCHAR(255) NOT NULL DEFAULT '& University Charter',
    hero_description TEXT NOT NULL,
    hero_image_url TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS accreditation_cards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    icon VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    border_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS accreditation_charter (
    id INT PRIMARY KEY AUTO_INCREMENT,
    badge_text VARCHAR(100) DEFAULT 'A Historic Milestone',
    section_title VARCHAR(255) NOT NULL DEFAULT 'The Presidential Charter',
    paragraph_1 TEXT NOT NULL,
    paragraph_2 TEXT NOT NULL,
    quote TEXT,
    charter_year VARCHAR(10) DEFAULT '2006',
    achievement_text VARCHAR(255) DEFAULT 'First Chartered Private University',
    achievement_location VARCHAR(100) DEFAULT 'Ghana • 2006',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS accreditation_memberships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    organization_name VARCHAR(255) NOT NULL,
    organization_description TEXT,
    membership_type ENUM('membership', 'linkage') DEFAULT 'membership',
    location VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS accreditation_cta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cta_title_1 VARCHAR(255) NOT NULL DEFAULT 'Committed to',
    cta_title_2 VARCHAR(255) NOT NULL DEFAULT 'Academic Excellence',
    cta_description TEXT NOT NULL,
    button_1_text VARCHAR(100) DEFAULT 'Explore Programs',
    button_1_url VARCHAR(255) DEFAULT 'academics.php',
    button_2_text VARCHAR(100) DEFAULT 'Contact Us',
    button_2_url VARCHAR(255) DEFAULT 'contact_us.php',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
