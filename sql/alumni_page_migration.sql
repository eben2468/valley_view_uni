-- Alumni Network Page - Database Migration
-- Tables for managing alumni_network_page_1.php content

CREATE TABLE IF NOT EXISTS alumni_page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Mission Section
    mission_heading VARCHAR(255) DEFAULT 'Our Mission',
    mission_text TEXT,
    mission_text_2 TEXT,
    mission_image VARCHAR(500) DEFAULT '',
    -- Coordinator Section
    coordinator_name VARCHAR(255) DEFAULT '',
    coordinator_title VARCHAR(255) DEFAULT 'Alumni Coordinator',
    coordinator_description TEXT,
    coordinator_image VARCHAR(500) DEFAULT '',
    coordinator_email VARCHAR(255) DEFAULT '',
    coordinator_phone VARCHAR(100) DEFAULT '',
    -- Legacy Fund CTA
    cta_heading VARCHAR(500) DEFAULT '',
    cta_subtitle VARCHAR(500) DEFAULT '',
    cta_description TEXT,
    cta_button_text VARCHAR(255) DEFAULT 'Donate Now',
    cta_button_link VARCHAR(500) DEFAULT '',
    cta_button2_text VARCHAR(255) DEFAULT 'Learn More',
    cta_button2_link VARCHAR(500) DEFAULT '',
    -- Contact Section
    contact_heading VARCHAR(255) DEFAULT 'Contact Alumni Relations',
    contact_location TEXT,
    contact_phone VARCHAR(100) DEFAULT '',
    contact_phone_note VARCHAR(255) DEFAULT '',
    contact_address TEXT,
    -- Social Links
    social_facebook VARCHAR(500) DEFAULT '',
    social_twitter VARCHAR(500) DEFAULT '',
    social_linkedin VARCHAR(500) DEFAULT '',
    social_threads VARCHAR(500) DEFAULT '',
    social_instagram VARCHAR(500) DEFAULT '',
    social_youtube VARCHAR(500) DEFAULT '',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS alumni_page_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) DEFAULT '',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS alumni_page_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL,
    section_title VARCHAR(255) NOT NULL,
    section_subtitle TEXT,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS alumni_page_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL,
    item_title VARCHAR(255) NOT NULL,
    item_description TEXT,
    item_icon VARCHAR(100) DEFAULT '',
    item_color VARCHAR(100) DEFAULT 'alumni-gradient',
    item_link VARCHAR(500) DEFAULT '',
    item_link_text VARCHAR(255) DEFAULT '',
    item_link_color VARCHAR(100) DEFAULT '',
    item_bg_class VARCHAR(255) DEFAULT '',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS alumni_page_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_value VARCHAR(50) NOT NULL,
    stat_label VARCHAR(255) NOT NULL,
    stat_color VARCHAR(100) DEFAULT 'blue',
    stat_bg VARCHAR(100) DEFAULT '',
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial content
INSERT INTO alumni_page_content (
    mission_heading, mission_text, mission_text_2, mission_image,
    coordinator_name, coordinator_title, coordinator_description, coordinator_image, coordinator_email, coordinator_phone,
    cta_heading, cta_subtitle, cta_description, cta_button_text, cta_button_link, cta_button2_text, cta_button2_link,
    contact_heading, contact_location, contact_phone, contact_phone_note, contact_address,
    social_facebook, social_twitter, social_linkedin, social_threads
) VALUES (
    'Our Mission',
    'Our mission is to foster lifelong connections between alumni and the university by providing engagement, philanthropy, and professional development opportunities.',
    'We are committed to supporting our alumni in their personal and professional pursuits. We believe that our alumni are our greatest asset while upholding the core values of excellence, integrity, and service.',
    'images/Graduate.png',
    'Godfred Ackah',
    'Alumni Coordinator',
    'As your Alumni Coordinator, I am dedicated to fostering meaningful connections between our graduates and the university. Whether you want to reconnect, give back, or simply stay informed, our office is here to support you on your journey.',
    'images/Godfred%20Ackah%20-%20Alumni%20Coordinator.jpg',
    'alumni@vvu.edu.gh',
    '+233 307 011 832',
    'Support Our Legacy Fund',
    'Invest in Future Generations',
    'Your contribution helps provide scholarships, improve facilities, and create opportunities for the next generation of VVU students.',
    'Donate Now',
    'https://www.vvuf.org/',
    'Learn More',
    'contact_us.php',
    'Contact Alumni Relations',
    'Mile 19 Off the Adenta-Dodowa Road\nValley View University, Oyibi',
    '+233 307 011 832',
    'Available Weekdays',
    'P.O. Box AF 595\nAdentan, Ghana',
    'https://www.facebook.com/share/15Uqe4J9GG/',
    'https://x.com/vvualumlegafund',
    'https://www.linkedin.com/company/vvualumnilegacyfund/',
    'https://www.threads.net/@vvualumnilegacyfund'
);

-- Insert hero slider images
INSERT INTO alumni_page_slides (image_url, alt_text, display_order) VALUES
('images/home-1.jpg', 'VVU Alumni Event 1', 1),
('images/home-2.jpg', 'VVU Alumni Event 2', 2),
('images/home-3.jpg', 'VVU Alumni Event 3', 3),
('images/home-4.jpg', 'VVU Alumni Event 4', 4);

-- Insert sections
INSERT INTO alumni_page_sections (section_key, section_title, section_subtitle, display_order) VALUES
('benefits', 'Benefits of Being an Alumnus', 'Unlock exclusive opportunities and stay connected with your VVU family.', 1),
('get_involved', 'Get Involved', 'There are many ways to stay connected and contribute to our growing community.', 2);

-- Insert stats
INSERT INTO alumni_page_stats (stat_value, stat_label, stat_color, stat_bg, display_order) VALUES
('30+', 'Years of Excellence', 'blue', 'blue-50', 1),
('10K+', 'Alumni Worldwide', 'yellow', 'yellow-50', 2),
('100+', 'Partner Companies', 'emerald', 'emerald-50', 3);

-- Insert benefit items
INSERT INTO alumni_page_items (section_key, item_title, item_description, item_icon, item_color, display_order) VALUES
('benefits', 'Alumni Directory', 'Connect with fellow alumni from all generations and expand your professional network.', 'contacts', 'alumni-gradient', 1),
('benefits', 'Career Services', 'Access job search assistance and professional development resources to advance your career.', 'work', 'alumni-gradient', 2),
('benefits', 'Lifelong Learning', 'Continue growing through online courses, workshops, and educational programs.', 'school', 'alumni-gradient', 3),
('benefits', 'Exclusive Events', 'Attend reunions, lectures, and social gatherings designed for our alumni community.', 'celebration', 'alumni-gradient-gold', 4),
('benefits', 'Volunteer Opportunities', 'Give back to your community and make a difference through meaningful volunteer work.', 'volunteer_activism', 'alumni-gradient-gold', 5),
('benefits', 'Mentorship Program', 'Guide current students and recent graduates, sharing your experience and expertise.', 'psychology', 'alumni-gradient-gold', 6);

-- Insert get involved items
INSERT INTO alumni_page_items (section_key, item_title, item_description, item_icon, item_color, item_link, item_link_text, item_link_color, item_bg_class, display_order) VALUES
('get_involved', 'Update Your Info', 'Update your contact information to stay connected with the university and receive important updates.', 'contact_mail', 'alumni-gradient', 'contact_us.php', 'Update Now', 'text-blue-700', 'alumni-gradient', 1),
('get_involved', 'Attend Events', 'Join us at networking events and engage with fellow alumni and the university community.', 'event', 'alumni-gradient-gold', '#', 'View Events', 'text-amber-700', 'alumni-gradient-gold', 2),
('get_involved', 'Become a Mentor', 'Guide current students and recent graduates through our mentoring program.', 'groups', 'bg-gradient-to-br from-emerald-600 to-emerald-800', 'contact_us.php', 'Join Program', 'text-emerald-700', 'bg-gradient-to-br from-emerald-600 to-emerald-800', 3),
('get_involved', 'Make a Gift', 'Support the university''s mission and ensure future generations have access to quality education.', 'redeem', 'bg-gradient-to-br from-purple-600 to-purple-800', '#legacy-fund', 'Donate Now', 'text-purple-700', 'bg-gradient-to-br from-purple-600 to-purple-800', 4);
