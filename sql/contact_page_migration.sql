-- Migration for Contact Page Content Management
CREATE TABLE IF NOT EXISTS contact_hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    badge_text VARCHAR(255) DEFAULT 'Get In Touch',
    title_1 VARCHAR(255) DEFAULT 'Connect With',
    title_2 VARCHAR(255) DEFAULT 'Valley View University',
    description TEXT,
    image_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_quick_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(100),
    title VARCHAR(255),
    description TEXT,
    bg_gradient VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS contact_postal_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(100),
    title VARCHAR(255),
    description TEXT,
    icon_bg_color VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS contact_social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(100),
    icon VARCHAR(100),
    url VARCHAR(255),
    color_class VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS contact_emergency_ussd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_type ENUM('emergency', 'ussd') NOT NULL,
    title VARCHAR(255),
    description TEXT,
    main_value VARCHAR(255), -- phone number or USSD code
    btn_text VARCHAR(100) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS contact_departments_header (
    id INT AUTO_INCREMENT PRIMARY KEY,
    badge_text VARCHAR(255),
    title VARCHAR(255),
    description TEXT
);

CREATE TABLE IF NOT EXISTS contact_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(100),
    name VARCHAR(255),
    phone_1 VARCHAR(100),
    phone_2 VARCHAR(100),
    email VARCHAR(255),
    icon_color VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS contact_faq_header (
    id INT AUTO_INCREMENT PRIMARY KEY,
    badge_text VARCHAR(255),
    title VARCHAR(255),
    description TEXT
);

CREATE TABLE IF NOT EXISTS contact_faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    answer TEXT,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS contact_map_overlay (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    link_text VARCHAR(100),
    link_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS contact_cta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT,
    subtitle VARCHAR(255),
    description TEXT,
    btn1_text VARCHAR(100),
    btn1_url VARCHAR(255),
    btn2_text VARCHAR(100),
    btn2_url VARCHAR(255),
    stat1_value VARCHAR(50),
    stat1_label VARCHAR(100),
    stat2_value VARCHAR(50),
    stat2_label VARCHAR(100),
    stat3_value VARCHAR(50),
    stat3_label VARCHAR(100)
);

-- Initial Data seeding for Hero
INSERT INTO contact_hero (badge_text, title_1, title_2, description, image_url) VALUES 
('Get In Touch', 'Connect With', 'Valley View University', '"Dear valued visitor, you are welcome to Valley View University. Your feedback is very important to us. Kindly reach out and we will get back to you as soon as possible."', 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=1920');

-- Initial Data seeding for Quick Cards
INSERT INTO contact_quick_cards (icon, title, description, bg_gradient, display_order) VALUES 
('location_on', 'Main Campus', 'Mile 19, Adenta-Dodowa Rd, Oyibi', 'from-blue-500 to-blue-700', 1),
('call', 'Call Us', '+233 307 011 832', 'from-green-500 to-green-700', 2),
('mail', 'Email Us', 'contact@vvu.edu.gh', 'from-purple-500 to-purple-700', 3),
('schedule', 'Office Hours', 'Mon - Fri, 8:00am - 5:00pm', 'from-amber-500 to-amber-700', 4);

-- Initial Data seeding for Postal Addresses
INSERT INTO contact_postal_addresses (icon, title, description, icon_bg_color, display_order) VALUES 
('markunread_mailbox', 'P. O. Box VV100', 'Valley View Agency, Oyibi - Accra', 'bg-blue-100 dark:bg-blue-900/30 text-blue-600', 1),
('markunread_mailbox', 'P. O. Box AF 595', 'Adentan - Ghana', 'bg-green-100 dark:bg-green-900/30 text-green-600', 2),
('mail', 'Academic Affairs', 'aad@vvu.edu.gh / contact@vvu.edu.gh', 'bg-purple-100 dark:bg-purple-900/30 text-purple-600', 3);

-- Initial Data seeding for Social Links
INSERT INTO contact_social_links (platform, icon, url, color_class, display_order) VALUES 
('Facebook', 'fa-facebook', '#', 'text-blue-600 hover:bg-blue-600', 1),
('Twitter', 'fa-twitter', '#', 'text-blue-400 hover:bg-blue-400', 2),
('LinkedIn', 'fa-linkedin', '#', 'text-blue-800 hover:bg-blue-800', 3),
('Instagram', 'fa-instagram', '#', 'text-pink-600 hover:bg-pink-600', 4),
('Youtube', 'fa-youtube-play', '#', 'text-red-600 hover:bg-red-600', 5);

-- Seeding Emergency and USSD
INSERT INTO contact_emergency_ussd (section_type, title, description, main_value, btn_text) VALUES 
('emergency', '🚨 Emergency Support', 'For urgent security or medical assistance on campus, please call our 24/7 emergency line.', '0307 051 137', 'Security: 0307 051 137'),
('ussd', '📱 Quick Dial', 'Dial our USSD code for quick access to VVU services.', '*800*50#', NULL);

-- Departments Header
INSERT INTO contact_departments_header (badge_text, title, description) VALUES 
('Directory', 'Departmental Contacts', 'Reach out directly to the specific office or department you need.');

-- Departments Seeding
INSERT INTO contact_departments (icon, name, phone_1, phone_2, email, icon_color, display_order) VALUES 
('school', 'Admissions', '0307 051 149', '0307 010 260', 'admissions@vvu.edu.gh', 'text-blue-600', 1),
('business_center', 'VC''s Office', '0307 011 844', NULL, NULL, 'text-green-600', 2),
('assignment_ind', 'Registrar', '0307 011 836', NULL, 'registrar@vvu.edu.gh', 'text-purple-600', 3),
('payments', 'Finance Office (CFO)', '0307 011 877', NULL, 'finaid@vvu.edu.gh', 'text-amber-600', 4),
('groups', 'Student Life', '0307 011 833', NULL, NULL, 'text-rose-600', 5),
('church', 'Chaplaincy', '0307 011 917', NULL, NULL, 'text-cyan-600', 6),
('auto_stories', 'Graduate School', '0307 325 053', NULL, NULL, 'text-indigo-600', 7),
('psychology', 'School of Education', '0307 011 850', NULL, NULL, 'text-teal-600', 8),
('storefront', 'School of Business (SOB)', '024 113 5570', NULL, NULL, 'text-orange-600', 9),
('public', 'Development Studies', '0307 011 834', NULL, NULL, 'text-lime-600', 10),
('gavel', 'FASS', '027 320 4000', NULL, NULL, 'text-sky-600', 11),
('menu_book', 'Theology', '0307 011 893', NULL, NULL, 'text-violet-600', 12),
('badge', 'Human Resource (HR)', '0307 011 846', NULL, NULL, 'text-pink-600', 13),
('account_balance', 'Treasury', '0307 051 136', '0307 011 878', NULL, 'text-emerald-600', 14),
('verified', 'Quality Assurance', '0307 011 879', NULL, NULL, 'text-red-600', 15),
('how_to_reg', 'Enrolment', '0307 001 567', NULL, NULL, 'text-blue-600', 16),
('lan', 'Online Coordinator', '0307 001 557', NULL, NULL, 'text-fuchsia-600', 17),
('school', 'Alumni', '0307 001 557', NULL, 'alumni@vvu.edu.gh', 'text-yellow-600', 18),
('engineering', 'Physical Plant', '0307 011 869', NULL, NULL, 'text-stone-600', 19),
('policy', 'Audit', '0307 001 556', NULL, NULL, 'text-gray-600', 20),
('videocam', 'Studio', '0307 051 058', NULL, NULL, 'text-red-600', 21),
('shield', 'Security', '0307 051 137', NULL, NULL, 'text-blue-600', 22);

-- FAQ Header
INSERT INTO contact_faq_header (badge_text, title, description) VALUES 
('Quick Help', 'Frequently Asked Questions', 'Find quick answers to common questions about contacting VVU.');

-- FAQs Seeding
INSERT INTO contact_faqs (question, answer, display_order) VALUES 
('How do I apply for admission?', 'You can apply online through our admissions portal or contact the Admissions Office at 0307 051 149 / 0307 010 260. Visit our <a href="admissions.php" class="text-blue-600 underline">Admissions page</a> for detailed instructions.', 1),
('What are the university''s postal addresses?', 'VVU has two postal addresses: P. O. Box VV100, Valley View Agency, Oyibi - Accra, and P. O. Box AF 595, Adentan - Ghana.', 2),
('How do I reach the university campus?', 'Valley View University is located at Mile 19 off the Adenta-Dodowa Road in Oyibi, Accra, Ghana. You can use the map below for directions or schedule a campus tour.', 3),
('Who do I contact for financial aid inquiries?', 'For financial aid, contact the Finance Office (CFO) at 0307 011 877 or the Treasury at 0307 051 136. You can also email finaid@vvu.edu.gh for assistance.', 4),
('What is the USSD code for VVU services?', 'You can dial *800*50# on your mobile phone to access VVU services quickly.', 5);

-- Map Overlay
INSERT INTO contact_map_overlay (title, description, link_text, link_url) VALUES 
('Visit Our Campus', 'Experience our vibrant community firsthand. Schedule a tour or drop by our main campus in Oyibi.', 'Book a Campus Tour', 'take_a_tour.php');

-- CTA Seeding
INSERT INTO contact_cta (title, subtitle, description, btn1_text, btn1_url, btn2_text, btn2_url, stat1_value, stat1_label, stat2_value, stat2_label, stat3_value, stat3_label) VALUES 
('Ready to Start Your Journey?', 'Join the VVU Community Today', 'Be part of a university that stands for excellence, integrity, and service. Discover how Valley View University can shape your future.', 'Learn More About VVU', 'about_us.php', 'Apply Now', 'apply.php', '24+', 'Departments', '24/7', 'Security Support', 'Fast', 'Response Time');
