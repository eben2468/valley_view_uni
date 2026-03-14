-- =====================================================
-- Campus Life Pages Content Management System
-- Database Schema for 5 Pages:
-- 1. Philosophy on Dress
-- 2. Accommodation
-- 3. Food Services
-- 4. Work Study
-- 5. Spiritual Life and Development (SLD)
-- =====================================================

-- Table for Philosophy on Dress Page
CREATE TABLE IF NOT EXISTS `philosophy_on_dress_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) NOT NULL DEFAULT 'Philosophy On Dress',
  `hero_subtitle` text,
  `hero_image` varchar(255) DEFAULT 'Education-Website-and-AdminPanel/images/pro-bg.jpg',
  `intro_heading` varchar(255) DEFAULT 'Our Dress Philosophy',
  `intro_text` text,
  `intro_image` varchar(255) DEFAULT 'uploads/Philosophy_on_dress.jpeg',
  `philosophy_statement` text,
  `encouraged_items` text,
  `discouraged_items` text,
  `benefits_text` text,
  `cta_heading` varchar(255) DEFAULT 'Questions About Our Dress Code?',
  `cta_text` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Accommodation Page
CREATE TABLE IF NOT EXISTS `accommodation_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) NOT NULL DEFAULT 'Accommodation',
  `hero_subtitle` text,
  `hero_image` varchar(255) DEFAULT 'images/accommodation_hero.jpg',
  `intro_heading` varchar(255) DEFAULT 'Campus Housing',
  `intro_text` text,
  `intro_image` varchar(255),
  `facilities_description` text,
  `room_types_description` text,
  `application_process` text,
  `rules_and_regulations` text,
  `cta_heading` varchar(255),
  `cta_text` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Accommodation Features
CREATE TABLE IF NOT EXISTS `accommodation_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT 'home',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Food Services Page
CREATE TABLE IF NOT EXISTS `food_services_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) NOT NULL DEFAULT 'Nourishing Body & Mind',
  `hero_subtitle` text,
  `hero_image` varchar(255) DEFAULT 'images/cafeteria_interior.png',
  `philosophy_heading` varchar(255) DEFAULT 'A Healthy Mind Starts with a Healthy Plate',
  `philosophy_text` text,
  `philosophy_image` varchar(255) DEFAULT 'images/vegetarian_meal.png',
  `breakfast_time` varchar(50) DEFAULT '6:30 - 8:30',
  `lunch_time` varchar(50) DEFAULT '10:00 - 2:00',
  `dinner_time` varchar(50) DEFAULT '4:00 - 6:00',
  `meal_plans_description` text,
  `feedback_heading` varchar(255),
  `feedback_text` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Food Services Features
CREATE TABLE IF NOT EXISTS `food_services_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT 'restaurant',
  `color` varchar(50) DEFAULT 'green',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Work Study Page
CREATE TABLE IF NOT EXISTS `work_study_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) NOT NULL DEFAULT 'Work Study Program',
  `hero_subtitle` text,
  `hero_image` varchar(255),
  `overview_heading` varchar(255) DEFAULT 'Campus Employment Philosophy',
  `overview_text` text,
  `overview_image` varchar(255),
  `minimum_hours` int(11) DEFAULT 12,
  `spouse_policy_text` text,
  `application_process` text,
  `cta_heading` varchar(255),
  `cta_text` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Work Study Benefits
CREATE TABLE IF NOT EXISTS `work_study_benefits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT 'work',
  `color` varchar(50) DEFAULT 'blue',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Work Study Opportunities
CREATE TABLE IF NOT EXISTS `work_study_opportunities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `opportunity_name` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for SLD (Spiritual Life and Development) Page
CREATE TABLE IF NOT EXISTS `sld_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) NOT NULL DEFAULT 'Spiritual Life & Development',
  `hero_subtitle` text,
  `hero_image` varchar(255),
  `welcome_heading` varchar(255) DEFAULT 'Welcome to SLD Office',
  `welcome_text` text,
  `welcome_image` varchar(255),
  `mission_statement` text,
  `dean_name` varchar(255),
  `dean_title` varchar(255),
  `dean_description` text,
  `cta_heading` varchar(255),
  `cta_text` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for SLD Services
CREATE TABLE IF NOT EXISTS `sld_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT 'church',
  `color` varchar(50) DEFAULT 'blue',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for SLD Staff Members
CREATE TABLE IF NOT EXISTS `sld_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `campus` varchar(100),
  `icon_color` varchar(50) DEFAULT 'blue',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default data for Philosophy on Dress
INSERT INTO `philosophy_on_dress_content` (`id`, `hero_title`, `hero_subtitle`, `intro_heading`, `intro_text`, `philosophy_statement`, `cta_heading`, `cta_text`) VALUES
(1, 'Philosophy On Dress', 'Modesty, Chastity, Simplicity, and Comeliness – The Biblical Standards We Embrace', 'Our Dress Philosophy', 
'Valley View University\'s philosophy of dress is firmly established on biblical ideals and professional standards expected of a Christian institution. Members of Valley View University seek to glorify and honor our Creator, God and respect self and others in our physical appearance.',
'Valley View University dress ideal seeks for appropriate covering of the body parts and avoids contemporary styles that are revealing or suggestive. It accentuates natural beauty rather than looks encouraged by fashion trends.',
'Questions About Our Dress Code?',
'Our Student Affairs Office is ready to help you understand our dress philosophy and guidelines.');

-- Insert default data for Accommodation
INSERT INTO `accommodation_content` (`id`, `hero_title`, `hero_subtitle`, `intro_heading`, `intro_text`) VALUES
(1, 'Accommodation', 'Comfortable and secure housing for students on campus', 'Campus Housing', 
'Valley View University provides quality accommodation facilities for students who wish to live on campus. Our residence halls offer a safe, comfortable, and conducive environment for academic success and personal growth.');

-- Insert default accommodation features
INSERT INTO `accommodation_features` (`title`, `description`, `icon`, `display_order`) VALUES
('Secure Environment', '24/7 security and controlled access to ensure student safety', 'security', 1),
('Modern Facilities', 'Well-maintained rooms with essential amenities', 'home', 2),
('Study Areas', 'Quiet spaces dedicated for academic work', 'menu_book', 3),
('Common Rooms', 'Social spaces for interaction and relaxation', 'groups', 4);

-- Insert default data for Food Services
INSERT INTO `food_services_content` (`id`, `hero_title`, `hero_subtitle`, `philosophy_heading`, `philosophy_text`) VALUES
(1, 'Nourishing Body & Mind', 'Experience wholesome, vegetarian cuisine prepared with care for our university community', 
'A Healthy Mind Starts with a Healthy Plate',
'At Valley View University, we believe that physical well-being is the foundation of academic and spiritual growth. Our cafeteria is dedicated to providing balanced, nutritious, and delicious vegetarian meals that fuel your journey.');

-- Insert default food services features
INSERT INTO `food_services_features` (`title`, `description`, `icon`, `color`, `display_order`) VALUES
('Hygiene First', 'Strict adherence to international food safety and sanitation standards', 'verified', 'green', 1),
('Fresh Bakery', 'Daily baked breads and pastries from our on-campus bakery facility', 'bakery_dining', 'blue', 2),
('Social Hub', 'A vibrant space designed for meaningful interactions and community building', 'groups', 'yellow', 3),
('Quiet Zones', 'Designated areas for those who prefer a peaceful dining and study experience', 'local_library', 'purple', 4);

-- Insert default data for Work Study
INSERT INTO `work_study_content` (`id`, `hero_title`, `hero_subtitle`, `overview_heading`, `overview_text`, `minimum_hours`, `spouse_policy_text`) VALUES
(1, 'Work Study Program', 'Learn, Work, and Grow - Experience holistic development through meaningful campus employment opportunities',
'Campus Employment Philosophy',
'In keeping with the Seventh-day Adventist philosophy of education, which emphasizes the development of the physical nature of humanity, Valley View University provides varied opportunities for students to work in campus-related industries.',
12,
'If a student\'s spouse is employed in the Work Study Programme at Valley View University to support the student financially, the spouse\'s employment may be terminated once the student has graduated and ceased to be a registered student at the university.');

-- Insert default work study benefits
INSERT INTO `work_study_benefits` (`title`, `description`, `icon`, `color`, `display_order`) VALUES
('Practical Experience', 'Gain hands-on work experience in various campus departments, building real-world skills', 'work', 'blue', 1),
('Financial Support', 'Earn income to support your educational expenses while maintaining focus on your studies', 'payments', 'green', 2),
('Character Building', 'Develop essential values like responsibility, punctuality, teamwork, and work ethic', 'psychology', 'purple', 3),
('Skill Development', 'Learn transferable professional skills including time management and communication', 'school', 'yellow', 4),
('Community Connection', 'Build meaningful relationships with faculty, staff, and fellow students', 'diversity_3', 'red', 5),
('Personal Growth', 'Cultivate self-reliance and a strong work ethic', 'self_improvement', 'indigo', 6);

-- Insert default work study opportunities
INSERT INTO `work_study_opportunities` (`category`, `opportunity_name`, `display_order`) VALUES
('Campus Industries', 'Bakery Factory Operations', 1),
('Campus Industries', 'Water Factory Production', 2),
('Campus Industries', 'Grocery Store Management', 3),
('Campus Industries', 'Food Services & Catering', 4),
('Campus Industries', 'Cement Block Factory', 5),
('Academic & Admin', 'Library Services & Support', 1),
('Academic & Admin', 'Computer Labs & IT Support', 2),
('Academic & Admin', 'Student Affairs Offices', 3),
('Academic & Admin', 'Campus Maintenance', 4),
('Academic & Admin', 'Administrative Support', 5);

-- Insert default data for SLD
INSERT INTO `sld_content` (`id`, `hero_title`, `hero_subtitle`, `welcome_heading`, `welcome_text`, `mission_statement`, `dean_name`, `dean_title`, `dean_description`) VALUES
(1, 'Spiritual Life & Development', 'Nurturing faith, character, and purpose in every student\'s journey',
'Welcome to SLD Office',
'The Spiritual Life and Development office is committed to fostering holistic growth through spiritual guidance, counseling, and ministry programs that strengthen faith and character.',
'To nurture spiritual growth, provide pastoral care, and empower students to live purpose-driven lives rooted in Christian values and service to humanity.',
'Emmanuel H. Takyi, Ph.D, DMin',
'Dean of Spiritual Life And Development Office',
'Leading our vision for holistic spiritual development across the university community.');

-- Insert default SLD services
INSERT INTO `sld_services` (`title`, `description`, `icon`, `color`, `display_order`) VALUES
('Chaplaincy Services', 'Professional chaplains providing spiritual guidance, pastoral care, and support', 'auto_stories', 'blue', 1),
('Counseling Services', 'Professional counseling support for personal, academic, and spiritual challenges', 'psychology', 'green', 2),
('Student Ministries', 'Vibrant student-led ministry programs, Bible study groups, and mission opportunities', 'groups', 'purple', 3),
('Worship Services', 'Regular worship services, prayer meetings, and spiritual retreats', 'celebration', 'yellow', 4),
('Mission & Outreach', 'Community service projects and mission trips that put faith into action', 'public', 'red', 5),
('Hospital Chaplaincy', 'Dedicated chaplaincy services at SDAVVU Hospital', 'health_and_safety', 'indigo', 6);

-- Insert default SLD staff
INSERT INTO `sld_staff` (`name`, `position`, `campus`, `icon_color`, `display_order`) VALUES
('Daniel Okoe Okai', 'Senior Administrative Assistant / SDAVVU Hospital Chaplain, Oyibi Campus', 'Oyibi', 'green', 1),
('Akua Amponsah', 'University Counselor', 'Main', 'purple', 2),
('Flacus Afriyie Amponsah', 'Associate Chaplain for Faculty and Staff / Office Coordinator', 'Main', 'blue', 3),
('Kusi Appiah', 'Associate Chaplain for Student Ministries and Missions', 'Main', 'yellow', 4),
('Disciple Amertil', 'Associate Chaplain, Counseling Services', 'Main', 'red', 5),
('Solomon Appiah', 'Associate Chaplain, Kumasi Campus', 'Kumasi', 'indigo', 6),
('Peter Obeng Manu', 'Associate Chaplain, Techiman Campus', 'Techiman', 'teal', 7),
('Kenneth Oppong', 'Assistant Chaplain, Techiman Campus', 'Techiman', 'orange', 8),
('Sydney Nii Okai Larmie', 'University Church Pastor', 'Main', 'pink', 9);
