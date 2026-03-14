-- ============================================
-- Academic Overview Pages Content Management
-- Migration for: admissions.php, academic_programs_overview.php, 
--                the_campus.php, learning_outcomes.php
-- ============================================

-- Main Pages Table (stores page-level settings like hero content)
CREATE TABLE IF NOT EXISTS `academic_pages_content` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `page_key` VARCHAR(100) NOT NULL UNIQUE,
    `page_title` VARCHAR(255) NOT NULL,
    `hero_badge` VARCHAR(255) DEFAULT NULL,
    `hero_title` VARCHAR(500) DEFAULT NULL,
    `hero_subtitle` TEXT DEFAULT NULL,
    `hero_description` TEXT DEFAULT NULL,
    `hero_image` VARCHAR(500) DEFAULT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT DEFAULT NULL,
    `cta_title` VARCHAR(255) DEFAULT NULL,
    `cta_subtitle` TEXT DEFAULT NULL,
    `cta_button_text` VARCHAR(100) DEFAULT NULL,
    `cta_button_link` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Page Sections Table (for major sections within each page)
CREATE TABLE IF NOT EXISTS `academic_pages_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `page_key` VARCHAR(100) NOT NULL,
    `section_key` VARCHAR(100) NOT NULL,
    `section_title` VARCHAR(255) DEFAULT NULL,
    `section_subtitle` TEXT DEFAULT NULL,
    `section_description` TEXT DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page_section` (`page_key`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Page Items Table (for cards, features, benefits, requirements, etc.)
CREATE TABLE IF NOT EXISTS `academic_pages_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `page_key` VARCHAR(100) NOT NULL,
    `section_key` VARCHAR(100) NOT NULL,
    `item_title` VARCHAR(255) NOT NULL,
    `item_subtitle` VARCHAR(255) DEFAULT NULL,
    `item_description` TEXT DEFAULT NULL,
    `item_icon` VARCHAR(100) DEFAULT NULL,
    `item_color` VARCHAR(50) DEFAULT 'blue-600',
    `item_image` VARCHAR(500) DEFAULT NULL,
    `item_link` VARCHAR(255) DEFAULT NULL,
    `item_stat_value` VARCHAR(50) DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `extra_data` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page_section_item` (`page_key`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Page Stats Table (for quick stats sections)
CREATE TABLE IF NOT EXISTS `academic_pages_stats` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `page_key` VARCHAR(100) NOT NULL,
    `stat_value` VARCHAR(50) NOT NULL,
    `stat_label` VARCHAR(100) NOT NULL,
    `stat_icon` VARCHAR(50) DEFAULT NULL,
    `stat_suffix` VARCHAR(20) DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page_stats` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Default Content for ADMISSIONS Page
-- ============================================
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('admissions', 'Admissions', 'Admissions 2024/2025', 'Are You Ready', 'To Apply?', 'Join Ghana''s first chartered private university and embark on a journey of holistic education and excellence.', 'vvu_admissions_hero_1766876689316.png', 'Ready to Start Your Journey?', 'Take the first step towards your future. Apply now and join thousands of successful VVU graduates.', 'Apply Now', 'apply.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admissions Stats
INSERT INTO `academic_pages_stats` (`page_key`, `stat_value`, `stat_label`, `stat_icon`, `display_order`) VALUES
('admissions', '50+', 'Programs', 'school', 1),
('admissions', '5000+', 'Students', 'groups', 2),
('admissions', '1979', 'Established', 'verified', 3),
('admissions', '#1', 'Private University', 'emoji_events', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admissions Sections
INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('admissions', 'why_choose', 'Why Choose VVU?', 'Discover what makes Valley View University the perfect choice for your academic journey', 1),
('admissions', 'requirements', 'Admission Requirements', 'What you need to start your journey at VVU', 2),
('admissions', 'process', 'Admission Process', 'Your pathway to becoming a VVU student', 3),
('admissions', 'programs', 'Featured Programs', 'Explore our diverse range of academic offerings', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admissions "Why Choose" Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('admissions', 'why_choose', 'First Chartered Private University', 'Ghana''s pioneering private institution, setting standards since 1979', 'verified', 'blue-600', 1),
('admissions', 'why_choose', 'Holistic Education', 'Nurturing mind, body, and spirit for complete development', 'psychology', 'purple-600', 2),
('admissions', 'why_choose', 'Vibrant Campus Life', 'A diverse international community from over 30 countries', 'diversity_3', 'green-600', 3),
('admissions', 'why_choose', 'Modern Facilities', 'State-of-the-art laboratories, libraries, and learning spaces', 'domain', 'yellow-600', 4),
('admissions', 'why_choose', 'Career Support', 'Comprehensive career services and industry partnerships', 'work', 'red-600', 5),
('admissions', 'why_choose', 'Flexible Payment', 'Various scholarship opportunities and payment plans', 'payments', 'indigo-600', 6)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admissions Requirements Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('admissions', 'requirements', 'Undergraduate', 'For Bachelor''s Degree Programs', 'WASSCE/SSCE with credit passes in 3 core subjects including English and Mathematics, plus 3 elective subjects relevant to your chosen programme. Applicants with A-Level, IB, or equivalent qualifications are also welcome.', 'school', 'blue-600', 1),
('admissions', 'requirements', 'Diploma', 'For Diploma Programs', 'WASSCE/SSCE with passes in 5 subjects including English and Mathematics. Mature applicants (25 years and above) with relevant work experience may apply through the Mature Students Entrance Examination.', 'workspace_premium', 'green-600', 2),
('admissions', 'requirements', 'Postgraduate', 'For Master''s & PhD Programs', 'A good first degree (Second Class Lower or better) from a recognized university. Relevant work experience may be required for some programmes. International applicants must have equivalent qualifications.', 'auto_stories', 'purple-600', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admissions Process Steps
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_stat_value`, `display_order`) VALUES
('admissions', 'process', 'Apply Online', 'Complete the online application form with your personal details, academic history, and programme choice.', 'edit_document', 'blue-600', '01', 1),
('admissions', 'process', 'Submit Documents', 'Upload certified copies of your certificates, transcripts, and other required documents.', 'folder_open', 'purple-600', '02', 2),
('admissions', 'process', 'Review Process', 'Our admissions team will review your application and notify you of the decision.', 'task_alt', 'green-600', '03', 3),
('admissions', 'process', 'Pay Fees', 'Upon admission, complete the registration process and pay your fees to secure your place.', 'payments', 'yellow-600', '04', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- ============================================
-- Insert Default Content for ACADEMIC PROGRAMS OVERVIEW Page
-- ============================================
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('academic_programs', 'Academic Programs Overview', 'Academic Excellence', 'Our Programs<span>Shape Your Future</span>', 'Discover world-class programs designed to prepare you for success in your chosen field.', NULL, 'images/home-2.jpg', 'Ready to Shape Your Future?', 'Join thousands of successful graduates who started their journey at Valley View University.', 'Apply Now', 'admissions.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- ============================================
-- Insert Default Content for THE CAMPUS Page
-- ============================================
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('the_campus', 'The VVU Campus', 'The VVU Experience', 'Explore Our', 'Vibrant Campus', 'Step into a world-class environment where academic excellence meets a serene, Christian atmosphere. Discover the "Very, Very Unique" touch of Valley View University.', NULL, 'Experience the Campus, Start Your Journey', 'Join a university that values your future as much as you do. Explore our programs and apply today.', 'Apply Now', 'apply.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- The Campus Sections
INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('the_campus', 'highlights', 'Why Choose VVU?', 'Experience a unique blend of academic rigor, international culture, and spiritual growth.', 1),
('the_campus', 'features', 'Life on Campus', 'Discover the facilities and standards that make VVU a leader in private education.', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- The Campus Highlight Items (from campus_highlights data)
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_subtitle`, `display_order`) VALUES
('the_campus', 'highlights', 'Ghana''s First Chartered Private University', 'Established in 1979, Valley View University holds the distinction of being Ghana''s first private chartered university. Our legacy of excellence spans over four decades of producing graduates who are leaders in their fields.', 'verified', 'blue-600', '"Setting the Standard in Private Education"', 1),
('the_campus', 'highlights', 'A Global Community', 'Our campus is home to students from over 30 countries across Africa, Europe, and the Americas. This rich diversity creates a unique learning environment where cultures meet and ideas flourish.', 'public', 'green-600', '"Where Cultures Meet and Ideas Flourish"', 2),
('the_campus', 'highlights', 'Faith-Based Excellence', 'As a Seventh-day Adventist institution, we integrate faith with learning, nurturing not just the mind but also the spirit. Our holistic approach develops well-rounded individuals ready to serve humanity.', 'church', 'purple-600', '"Developing the Whole Person"', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- The Campus Feature Items (from campus_features data)
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('the_campus', 'features', 'Modern Lecture Halls', 'Air-conditioned, multimedia-equipped halls designed for optimal learning experiences.', 'meeting_room', 'blue-600', 1),
('the_campus', 'features', 'Digital Library', 'Access thousands of e-books, journals, and research materials 24/7.', 'local_library', 'purple-600', 2),
('the_campus', 'features', 'Science Laboratories', 'State-of-the-art labs for nursing, IT, and science programs.', 'science', 'green-600', 3),
('the_campus', 'features', 'Student Hostels', 'Safe, comfortable accommodation with modern amenities on campus.', 'apartment', 'yellow-600', 4),
('the_campus', 'features', 'Health Centre', 'On-campus medical facility providing healthcare services to students.', 'local_hospital', 'red-600', 5),
('the_campus', 'features', 'Sports Complex', 'Football field, basketball courts, and fitness facilities for recreation.', 'sports_soccer', 'indigo-600', 6)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- ============================================
-- Insert Default Content for LEARNING OUTCOMES Page
-- ============================================
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('learning_outcomes', 'Learning Outcomes', 'Graduate Excellence', 'Learning', 'Outcomes', 'Desired Characteristics of A VVU Graduate — Shaping Leaders for Tomorrow', NULL, 'Begin Your Journey to Excellence', 'Experience an education that transforms not just your career, but your entire life. Join the VVU family today.', 'Explore Programs', 'academic_programs_overview.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Learning Outcomes Sections
INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `section_description`, `display_order`) VALUES
('learning_outcomes', 'intro', 'What Makes a VVU Graduate Exceptional?', 'Defining Excellence', 'The following learning objectives, described in terms of the desired characteristics of educated graduates, are used to guide educators in their development of courses and programmes.', 1),
('learning_outcomes', 'pillars', 'The Eleven Pillars', 'Core characteristics that define every Valley View University graduate.', NULL, 2),
('learning_outcomes', 'methods', 'How We Achieve Excellence', 'Our comprehensive approach to developing well-rounded graduates.', NULL, 3),
('learning_outcomes', 'stats', 'Graduate Success', NULL, NULL, 4),
('learning_outcomes', 'testimonial', NULL, NULL, NULL, 5)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Learning Outcomes - The Eleven Pillars
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_stat_value`, `display_order`) VALUES
('learning_outcomes', 'pillars', 'Spiritual Maturity', 'Students develop a growing relationship with the Lord, nurtured through Bible study, personal devotion, commitment to witnessing, and selfless service to humanity.', 'church', 'blue-600', '1', 1),
('learning_outcomes', 'pillars', 'Love of Learning', 'Students possess a strong desire to learn for the sake of learning—a passion reinforced by formal studies and university experience that remains with them for life.', 'auto_stories', 'green-600', '2', 2),
('learning_outcomes', 'pillars', 'Depth of Understanding', 'Graduates master material in at least one discipline in considerable depth, approaching the frontier of knowledge and understanding the basic foundations of their field.', 'psychology', 'purple-600', '3', 3),
('learning_outcomes', 'pillars', 'Independence of Thought', 'Students think clearly and rigorously for themselves, with the ability to constructively criticize and create when established positions are defective.', 'lightbulb', 'yellow-500', '4', 4),
('learning_outcomes', 'pillars', 'Historical Awareness', 'Students understand that ideas are subject to change and comprehend the history of their discipline and how it relates to other disciplines.', 'history_edu', 'red-600', '5', 5),
('learning_outcomes', 'pillars', 'Breadth of Understanding', 'Students take broad perspectives, understanding how ideas in their discipline relate to similar elements in other fields—science students appreciate arts, and vice versa.', 'diversity_3', 'teal-600', '6', 6),
('learning_outcomes', 'pillars', 'Global Understanding', 'Graduates appreciate national and international dimensions, applying knowledge to promote national dignity and global harmony.', 'public', 'indigo-600', '7', 7),
('learning_outcomes', 'pillars', 'Moral Maturity', 'Students make sound moral judgments, identify ethical questions, weigh competing considerations, and have strength of character to do what is right.', 'balance', 'pink-600', '8', 8),
('learning_outcomes', 'pillars', 'Aesthetic Sensibility', 'Graduates have critical appreciation of fine and performing arts, extending their appreciation of human creativity to the natural environment.', 'palette', 'orange-600', '9', 9),
('learning_outcomes', 'pillars', 'Literacy', 'Students are highly literate, able to read demanding material with full comprehension, develop positions orally, and write with rigor, correctness, and style.', 'menu_book', 'cyan-600', '10', 10),
('learning_outcomes', 'pillars', 'Numeracy', 'Graduates understand mathematical forms of inquiry at a level that overcomes alienation from technology and enables appreciation of its significance.', 'calculate', 'emerald-600', '11', 11)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Learning Outcomes - Methods (How We Achieve)
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `extra_data`, `display_order`) VALUES
('learning_outcomes', 'methods', 'Rigorous Curriculum', 'Our courses are designed to challenge students intellectually while providing practical skills applicable in the real world.', 'school', 'blue-600', '{"points": ["Industry-aligned programmes", "Research-based learning", "Continuous assessment"]}', 1),
('learning_outcomes', 'methods', 'Faith-Based Foundation', 'Spiritual growth is integral to our educational philosophy, nurturing the whole person—mind, body, and spirit.', 'church', 'purple-600', '{"points": ["Chapel services & devotions", "Community service programs", "Values-centered teaching"]}', 2),
('learning_outcomes', 'methods', 'Experiential Learning', 'Hands-on experiences prepare students for real-world challenges through internships, practicums, and projects.', 'engineering', 'green-600', '{"points": ["Industrial attachments", "Clinical rotations (Nursing)", "Research projects"]}', 3),
('learning_outcomes', 'methods', 'Mentorship & Support', 'Dedicated faculty and staff guide students throughout their academic journey with personalized attention.', 'groups', 'yellow-500', '{"points": ["Academic advising", "Career counseling", "Peer tutoring programs"]}', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Learning Outcomes Stats
INSERT INTO `academic_pages_stats` (`page_key`, `stat_value`, `stat_label`, `display_order`) VALUES
('learning_outcomes', '11', 'Core Outcomes', 1),
('learning_outcomes', '95%', 'Graduate Rate', 2),
('learning_outcomes', '85%', 'Employment Rate', 3),
('learning_outcomes', '100%', 'Accredited', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Learning Outcomes Testimonial Item
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_subtitle`, `display_order`) VALUES
('learning_outcomes', 'testimonial', 'VVU Graduate', 'Valley View University didn''t just give me a degree—it gave me a foundation for life. The values I learned here guide me every day in my career and personal life.', 'Class of 2023', 1)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- ============================================
-- Create indexes for better performance
-- ============================================
CREATE INDEX IF NOT EXISTS `idx_page_content_active` ON `academic_pages_content` (`page_key`, `is_active`);
CREATE INDEX IF NOT EXISTS `idx_items_active` ON `academic_pages_items` (`page_key`, `section_key`, `is_active`, `display_order`);
