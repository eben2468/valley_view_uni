-- Graduate School Page - Database Migration

CREATE TABLE IF NOT EXISTS graduate_page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hero_badge VARCHAR(255) DEFAULT 'School of Graduate Studies',
    hero_title VARCHAR(500) DEFAULT '',
    hero_subtitle TEXT,
    hero_image VARCHAR(500) DEFAULT '',
    about_heading VARCHAR(255) DEFAULT '',
    about_text TEXT,
    about_text_2 TEXT,
    about_image VARCHAR(500) DEFAULT '',
    why_heading VARCHAR(255) DEFAULT '',
    why_subtitle TEXT,
    admission_heading VARCHAR(255) DEFAULT '',
    admission_subtitle TEXT,
    programs_heading VARCHAR(255) DEFAULT '',
    programs_subtitle TEXT,
    cta_heading VARCHAR(500) DEFAULT '',
    cta_subtitle TEXT,
    cta_button_text VARCHAR(255) DEFAULT 'Apply Now',
    cta_button_link VARCHAR(500) DEFAULT '',
    cta_button2_text VARCHAR(255) DEFAULT 'Contact Us',
    cta_button2_link VARCHAR(500) DEFAULT '',
    contact_heading VARCHAR(255) DEFAULT '',
    contact_phone VARCHAR(100) DEFAULT '',
    contact_email VARCHAR(255) DEFAULT '',
    contact_location TEXT,
    contact_hours VARCHAR(255) DEFAULT '',
    dean_name VARCHAR(255) DEFAULT '',
    dean_title VARCHAR(255) DEFAULT '',
    dean_message TEXT,
    dean_image VARCHAR(500) DEFAULT '',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS graduate_page_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL,
    section_title VARCHAR(255) NOT NULL,
    section_subtitle TEXT,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS graduate_page_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL,
    item_title VARCHAR(255) NOT NULL,
    item_description TEXT,
    item_icon VARCHAR(100) DEFAULT '',
    item_color VARCHAR(100) DEFAULT '',
    item_link VARCHAR(500) DEFAULT '',
    item_extra VARCHAR(500) DEFAULT '',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS graduate_page_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_value VARCHAR(50) NOT NULL,
    stat_label VARCHAR(255) NOT NULL,
    stat_icon VARCHAR(100) DEFAULT '',
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert main content
INSERT INTO graduate_page_content (
    hero_badge, hero_title, hero_subtitle, hero_image,
    about_heading, about_text, about_text_2, about_image,
    why_heading, why_subtitle,
    admission_heading, admission_subtitle,
    programs_heading, programs_subtitle,
    cta_heading, cta_subtitle, cta_button_text, cta_button_link, cta_button2_text, cta_button2_link,
    contact_heading, contact_phone, contact_email, contact_location, contact_hours,
    dean_name, dean_title, dean_message, dean_image
) VALUES (
    'School of Graduate Studies',
    'Elevate Your Career with Advanced Education',
    'The School of Graduate Studies at Valley View University offers rigorous postgraduate programs designed to develop leaders, innovators, and scholars who transform communities across Africa and beyond.',
    'images/home-2.jpg',
    'About the Graduate School',
    'The School of Graduate Studies at Valley View University is committed to advancing knowledge through cutting-edge research, scholarly inquiry, and professional development. Established to meet the growing demand for highly qualified professionals in Ghana and across West Africa, our graduate school provides a transformative educational experience rooted in Christian values and academic excellence.',
    'Our programs are accredited by the National Accreditation Board (NAB) of Ghana and are designed to equip students with advanced analytical, leadership, and research skills. Whether you are a working professional seeking career advancement or a scholar pursuing academic research, our flexible scheduling options — including evening and weekend classes — make it possible to balance your studies with your professional and personal commitments.',
    'images/Graduate.png',
    'Why Choose VVU Graduate School?',
    'Discover the advantages that set our graduate programs apart from the rest.',
    'Admission Requirements',
    'Learn about the qualifications needed to join our esteemed graduate programs.',
    'Our Graduate Programs',
    'Explore our comprehensive range of master''s and doctoral programs across multiple disciplines.',
    'Begin Your Graduate Journey Today',
    'Take the next step in your academic and professional career. Apply to one of our graduate programs and join a community of scholars dedicated to excellence.',
    'Apply Now', 'https://apply.vvu.edu.gh/',
    'Request Info', 'contact_us.php',
    'Graduate School Office',
    '+233 307 011 832', 'graduateschool@vvu.edu.gh',
    'Valley View University Main Campus\nOyibi, Accra, Ghana',
    'Mon - Fri: 8:00 AM - 5:00 PM',
    '', 'Dean, School of Graduate Studies',
    'Welcome to the School of Graduate Studies at Valley View University. Our graduate school is dedicated to producing thought leaders and change agents who will make meaningful contributions to society. We invite you to explore our programs and discover how VVU can help you achieve your academic and professional aspirations.',
    ''
);

-- Insert stats
INSERT INTO graduate_page_stats (stat_value, stat_label, stat_icon, display_order) VALUES
('12', 'Graduate Programs', 'school', 1),
('500+', 'Graduate Students', 'groups', 2),
('95%', 'Employment Rate', 'trending_up', 3),
('30+', 'Expert Faculty', 'psychology', 4);

-- Insert sections
INSERT INTO graduate_page_sections (section_key, section_title, section_subtitle, display_order) VALUES
('why_choose', 'Why Choose VVU Graduate School?', 'Discover the advantages that set our graduate programs apart from the rest.', 1),
('admission', 'Admission Requirements', 'Learn about the qualifications needed to join our esteemed graduate programs.', 2),
('research', 'Research & Innovation', 'Our graduate students engage in meaningful research that addresses real-world challenges.', 3);

-- Why Choose items
INSERT INTO graduate_page_items (section_key, item_title, item_description, item_icon, item_color, display_order) VALUES
('why_choose', 'NAB Accredited Programs', 'All our graduate programs are fully accredited by the National Accreditation Board of Ghana, ensuring internationally recognized qualifications.', 'verified', 'blue', 1),
('why_choose', 'Flexible Scheduling', 'Evening and weekend classes designed for working professionals. Balance your career while earning an advanced degree.', 'schedule', 'emerald', 2),
('why_choose', 'Expert Faculty', 'Learn from distinguished professors with extensive industry experience and doctoral-level qualifications from renowned institutions worldwide.', 'psychology', 'purple', 3),
('why_choose', 'Christian Values Foundation', 'Our programs integrate Christian ethics and moral values, developing graduates who lead with integrity and a commitment to service.', 'church', 'amber', 4),
('why_choose', 'Research Opportunities', 'Engage in cutting-edge research with access to modern facilities, dedicated supervisors, and opportunities to publish in academic journals.', 'biotech', 'rose', 5),
('why_choose', 'Career Advancement', 'Our graduates occupy leadership positions across government, private sector, NGOs, and academia throughout Ghana and beyond.', 'rocket_launch', 'cyan', 6);

-- Admission requirement items
INSERT INTO graduate_page_items (section_key, item_title, item_description, item_icon, item_color, display_order) VALUES
('admission', 'Master''s Programs', 'A good first degree (at least Second Class Lower Division) from a recognized university. Applicants with relevant professional experience may also be considered.', 'workspace_premium', 'blue', 1),
('admission', 'Doctoral Programs (PhD)', 'A master''s degree in a relevant field from an accredited institution. A research proposal and academic references are required as part of the application.', 'military_tech', 'purple', 2),
('admission', 'English Proficiency', 'All applicants must demonstrate proficiency in English. International students may need to provide TOEFL or IELTS scores if their previous education was not in English.', 'translate', 'emerald', 3),
('admission', 'Application Documents', 'Official transcripts, certificates, two recommendation letters, a statement of purpose, CV/resume, and passport-sized photographs.', 'folder_open', 'amber', 4);

-- Research items
INSERT INTO graduate_page_items (section_key, item_title, item_description, item_icon, item_color, display_order) VALUES
('research', 'Thesis & Dissertation Support', 'Dedicated research supervisors guide students through the entire research process, from proposal development to final defense.', 'edit_document', 'blue', 1),
('research', 'Conference Presentations', 'Graduate students are encouraged to present their research at national and international conferences, building their academic profile.', 'podium', 'emerald', 2),
('research', 'Publication Opportunities', 'VVU supports graduate student publications in peer-reviewed journals, contributing to the body of knowledge in various fields.', 'menu_book', 'purple', 3);
