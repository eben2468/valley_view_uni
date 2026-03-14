-- ============================================
-- Administration Pages CMS Schema
-- Valley View University
-- ============================================

-- Create table for administration pages
CREATE TABLE IF NOT EXISTS `administration_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_slug` varchar(100) NOT NULL UNIQUE,
  `page_title` varchar(255) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert page records (only if they don't exist)
INSERT IGNORE INTO `administration_pages` (`page_slug`, `page_title`, `page_name`) VALUES
('office_of_the_vice_chancellor', 'Office of the Vice Chancellor', 'Office of the Vice Chancellor'),
('office_of_the_pro-vice_chancellor', 'Office of the Pro-Vice Chancellor', 'Office of the Pro-Vice Chancellor'),
('office_of_the_registrar', 'Office of the Registrar', 'Office of the Registrar'),
('rectors', 'Campus Rectors', 'Campus Rectors'),
('recorders', 'University Recorders', 'University Recorders');

-- ============================================
-- Create table for page content sections
-- ============================================
CREATE TABLE IF NOT EXISTS `administration_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `section_type` varchar(50) NOT NULL, -- hero, profile, section, card, etc.
  `section_key` varchar(100) NOT NULL,
  `content_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `section_type` (`section_type`),
  FOREIGN KEY (`page_id`) REFERENCES `administration_pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Create table for content fields (flexible schema)
-- ============================================
CREATE TABLE IF NOT EXISTS `administration_content_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `field_value` longtext,
  `field_type` varchar(50) DEFAULT 'text', -- text, textarea, image, url, html
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  FOREIGN KEY (`content_id`) REFERENCES `administration_content`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Insert initial content for Vice Chancellor page
-- ============================================
INSERT INTO `administration_content` (`page_id`, `section_type`, `section_key`, `content_order`) VALUES
(1, 'hero', 'hero_section', 1),
(1, 'profile', 'vc_profile', 2),
(1, 'section', 'strategic_vision', 3),
(1, 'section', 'contact_section', 4),
(1, 'section', 'cta_section', 5);

-- Hero Section Fields
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(1, 'badge_text', 'University Leadership', 'text'),
(1, 'title_main', 'Office of the', 'text'),
(1, 'title_highlight', 'Vice Chancellor', 'text'),
(1, 'subtitle', 'Leading Valley View University towards a future of academic excellence, spiritual growth, and societal impact through dedicated service and visionary leadership.', 'textarea'),
(1, 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image');

-- Profile Section Fields
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(2, 'profile_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCLxwRQaStcMjctmdRSUlFrbTzHrHZ4QmQ7_w-SjNu0YbuDefwcI5HThsfCdLLv2t2buwPecrNFBE0YG9eouPiuXF_v0W_iZyuf-LqyyZDM_LGTDg50yAveRJO1xUoJWTArmE9HlG_NBbpBogj3YzigfkiFnlpHCvldhseWxVTj3HaJpdFaTDwR34NqL0UJmX8pZa6aANMldz55PZSL0ZrzavkAeMjQv_pYGZbL4ObyJK-1ZZU9mW2rBo-Z6I1hzq_bCvv7QBKpvQy0', 'image'),
(2, 'name', 'William Kofi Koomson, PhD', 'text'),
(2, 'title', 'Vice Chancellor', 'text'),
(2, 'bio_paragraph_1', 'William Kofi Koomson, a Ghanaian by birth, lived and worked in the Americas, including Jamaica, Trinidad and Tobago, Canada, and the United States of America for the past 30 years, after acquiring his initial secondary education in Ghana. He is married with four adult children.', 'textarea'),
(2, 'bio_paragraph_2', 'He has worked for the Seventh-day Adventist Church for the past 35 years as an Administrator and Departmental Director at the local Conference, Union and General Conference (Review and Herald Publishing Association) levels, Vice Principal for the Literature Ministry Seminary, University Professor, College Principal/Rector and Pastor.', 'textarea'),
(2, 'bio_paragraph_3', 'As part of his evangelistic outreach, he spearheaded a Community Sharing Ministry program in the USA, Canada and Europe to distribute within seven years over 250,000 copies of the book, Steps to Christ. He led in establishing two Churches in North America and through his evangelistic efforts, more than 1510 souls have been baptized into the Seventh-day Adventist Church.', 'textarea'),
(2, 'experience_text', '35+ Years of Service in the Seventh-day Adventist Church.', 'text'),
(2, 'impact_text', 'Extensive international experience across the Americas and Europe.', 'text');

-- Strategic Vision Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(3, 'section_title', 'Strategic Vision', 'text'),
(3, 'section_subtitle', 'Under the leadership of Dr. William Kofi Koomson, the Office of the Vice Chancellor is focused on four key pillars of transformation.', 'textarea'),
(3, 'pillar_1_title', 'Academic Excellence', 'text'),
(3, 'pillar_1_description', 'Enhancing the quality of teaching and learning through innovative curricula and world-class faculty development.', 'textarea'),
(3, 'pillar_2_title', 'Research & Innovation', 'text'),
(3, 'pillar_2_description', 'Fostering a culture of research that addresses local and global challenges through interdisciplinary collaboration.', 'textarea'),
(3, 'pillar_3_title', 'Community Engagement', 'text'),
(3, 'pillar_3_description', 'Strengthening our impact on society through meaningful service, outreach, and strategic partnerships.', 'textarea'),
(3, 'pillar_4_title', 'Spiritual Growth', 'text'),
(3, 'pillar_4_description', 'Nurturing the spiritual well-being of our community, grounded in the values of the Seventh-day Adventist Church.', 'textarea');

-- Contact Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(4, 'section_title', 'Contact the Office', 'text'),
(4, 'section_description', 'For official inquiries, scheduling, or administrative matters, please reach out to our dedicated team.', 'textarea'),
(4, 'email', 'vc@vvu.edu.gh', 'text'),
(4, 'phone', '+233 (0) 302 501 101', 'text'),
(4, 'office_location', 'Administration Block, Oyibi Campus', 'text');

-- CTA Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(5, 'cta_title', 'Building a Legacy of', 'text'),
(5, 'cta_highlight', 'Excellence Together', 'text'),
(5, 'cta_description', 'Join us in our mission to transform lives through quality Christian education and dedicated service to humanity.', 'textarea'),
(5, 'button_1_text', 'About the University', 'text'),
(5, 'button_1_url', 'about_us.php', 'text'),
(5, 'button_2_text', 'Get in Touch', 'text'),
(5, 'button_2_url', 'contact_us.php', 'text');
