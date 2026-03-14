
-- Footer Management Tables

CREATE TABLE IF NOT EXISTS `footer_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `footer_links` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `section_id` INT NOT NULL,
    `label` VARCHAR(100) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `icon_class` VARCHAR(50) DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    FOREIGN KEY (`section_id`) REFERENCES `footer_sections`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `footer_settings` (
    `setting_key` VARCHAR(50) PRIMARY KEY,
    `setting_value` TEXT,
    `label` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Sections
INSERT INTO `footer_sections` (`id`, `title`, `display_order`) VALUES
(1, 'Quick Links', 1),
(2, 'Academic Programs', 2),
(3, 'Student Resources', 3),
(4, 'Social Media', 4);

-- Insert Default Links for Section 1 (Quick Links)
INSERT INTO `footer_links` (`section_id`, `label`, `url`, `display_order`) VALUES
(1, 'About Us', 'about_us.php', 1),
(1, 'Mission & Vision', 'mission_and_vision.php', 2),
(1, 'Leadership', 'office_of_the_vice_chancellor.php', 3),
(1, 'Accreditation', 'accreditation_and_charter.php', 4),
(1, 'Strategic Plan', 'strategic_plan.php', 5),
(1, 'FAQs', 'faqs_about_vvu.php', 6),
(1, 'Contact Us', 'contact_us.php', 7);

-- Insert Default Links for Section 2 (Academic Programs)
INSERT INTO `footer_links` (`section_id`, `label`, `url`, `display_order`) VALUES
(2, 'Faculty of Science', 'faculty_of_science.php', 1),
(2, 'School of Business', 'academic_programs_overview.php', 2),
(2, 'Faculty of Arts & Social Science', 'academic_programs_overview.php', 3),
(2, 'School of Nursing & Midwifery', 'academic_programs_overview.php', 4),
(2, 'School of Education', 'academic_programs_overview.php', 5),
(2, 'School of Theology & Missions', 'academic_programs_overview.php', 6),
(2, 'School of Graduate Studies', 'academic_programs_overview.php', 7);

-- Insert Default Links for Section 3 (Student Resources)
INSERT INTO `footer_links` (`section_id`, `label`, `url`, `display_order`) VALUES
(3, 'Student Life', 'student_life.php', 1),
(3, 'Library Resources', 'library_resources.php', 2),
(3, 'Academic Calendar', 'academic_calendar.php', 3),
(3, 'Student Handbook', 'student_handbook.php', 4),
(3, 'Accommodation', 'accommodation.php', 5),
(3, 'Fee Payment', 'mobile_money_fee_payment.php', 6);

-- Insert Default Links for Section 4 (Social Media)
INSERT INTO `footer_links` (`section_id`, `label`, `url`, `icon_class`, `display_order`) VALUES
(4, 'Facebook', '#', 'fa-facebook', 1),
(4, 'Twitter', '#', 'fa-twitter', 2),
(4, 'Instagram', '#', 'fa-instagram', 3),
(4, 'YouTube', '#', 'fa-youtube', 4),
(4, 'LinkedIn', '#', 'fa-linkedin', 5);

-- Insert Default Settings
INSERT INTO `footer_settings` (`setting_key`, `setting_value`, `label`) VALUES
('contact_address', 'Valley View University, Oyibi, Accra, Ghana', 'Office Address'),
('contact_phone', '+233-XXX-XXXX', 'Phone Number'),
('contact_email', 'info@vvu.edu.gh', 'Email Address'),
('connect_description', 'Stay connected with Valley View University through our social media channels and stay updated with the latest news, events, and announcements.', 'Connect Description'),
('copyright_text', '© 2026 Valley View University. All Rights Reserved.', 'Copyright Text');
