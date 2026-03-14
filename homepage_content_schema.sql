-- Homepage Content Management Schema
-- Run this SQL to create tables for managing all homepage content

USE valley_view_uni;

-- Create admin users table if not exists
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT IGNORE INTO admin_users (username, password, email, full_name) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@vvu.edu', 'Administrator');

-- Table for homepage sliders
CREATE TABLE IF NOT EXISTS homepage_sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(1000) NOT NULL,
    title VARCHAR(255) NOT NULL,
    highlight_text VARCHAR(255),
    description TEXT,
    button1_text VARCHAR(100),
    button1_link VARCHAR(255),
    button2_text VARCHAR(100),
    button2_link VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for homepage sections (Discover More, Popular Programs, etc.)
CREATE TABLE IF NOT EXISTS homepage_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) UNIQUE NOT NULL,
    section_title VARCHAR(255) NOT NULL,
    section_subtitle TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for discover more cards
CREATE TABLE IF NOT EXISTS homepage_discover_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(1000) NOT NULL,
    title VARCHAR(255) NOT NULL,
    link_url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for popular programs
CREATE TABLE IF NOT EXISTS homepage_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(1000) NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(255),
    description TEXT,
    rating DECIMAL(2,1) DEFAULT 4.5,
    link_url VARCHAR(255),
    button1_text VARCHAR(100) DEFAULT 'Learn More',
    button1_link VARCHAR(255),
    button2_text VARCHAR(100) DEFAULT 'View Details',
    button2_link VARCHAR(255),
    button3_text VARCHAR(100) DEFAULT 'Apply',
    button3_link VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for homepage gallery images
CREATE TABLE IF NOT EXISTS homepage_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(1000) NOT NULL,
    caption VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for homepage news/events
CREATE TABLE IF NOT EXISTS homepage_news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    event_date DATE,
    link_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for homepage video section
CREATE TABLE IF NOT EXISTS homepage_video (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_url VARCHAR(1000) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default slider data
INSERT INTO homepage_sliders (image_url, title, highlight_text, description, button1_text, button1_link, button2_text, button2_link, display_order) VALUES
('https://lh3.googleusercontent.com/aida-public/AB6AXuCBsTWrhC1L8hPKIAHMGfIXKKdwGb5ABivu_X28_0Emw8jLoU_Mm3bylmGNdNzCqWZBYcmechp3JA4aPL2rBeoVGwplow6Jfh-_G8lUhNgfWDYq3dNSvsl2Yoh2IqLUMJRLcbAqMgjEv9azfV63_2apLMt7pduIN0S70RlKg2uWhc5-MJxO01KvRGDte2K8BqP3R7rkD8CNbUhQl_HnqMkzBNjMQIhl5PazZDhtEjZZacQbPQDmTszdlC715JBVJ846acGQDeIhI9hj', 'Innovate', 'Your Future', 'Discover your potential at Valley View University, where tradition meets innovation in a vibrant community of learners.', 'Explore Programs', 'academic_programs_overview.php', 'Request Info', 'admissions.php', 1),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAGK5DGGjd2aSu_vH3dEn7L-PEvRJ0Mht26OSKtmfx6-_hNz2tomODvZelqGQAHcnISoeR_FljlhjH7q8ovhYOX6OZ9aD4LJ3FLLbGwgGo9u84kGLu9mlbLbNsgwEz5oKMvrHuW2iegIk3cNZFqU_8UeKOFt_UzGnS8ZM75ien1rHMtqsk8H_TQXVefa2wkEF-jZ_IHO-KIfI75Xj9zZzUiXX3YR0vRTw-Oqbq9I1xjTFp79wbhuf0RxWmhu_vwVM96YfgOcUp__pS9', 'Pioneering', 'Research', 'Join our community of scholars and researchers who are pushing the boundaries of knowledge and making a global impact.', 'Discover Research', 'research_opportunities.php', 'Faculty Directory', 'faculty_encyclopedia.php', 2),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAhbhKQoDJFNKDLmuL_BNMPiQ7ju1ghsDoV8bIHl-J2nddloWCc_gqmk9Tjd_Es9Kz3N4kcPekEk5B9_HTjzK0S0MQwfcqzhsMkNs9H1vbmvF9geYkpQOSR1rsfImiZItE0iKvhfpO6fVnCDabYhpJrpHpVaae1M40iYq7g4b8dwAbqgsNWGu32p-xRrS9Dx7kNlg8K2vPLdyEHVlXaDGX8TQ0_J4wq1OU7cqOUZxBwC5mBG8AGsf_mW4SyS8h7_EytaAYH15S18RT9', 'A Vibrant', 'Campus Life', 'Immerse yourself in a dynamic campus experience with over 200 clubs, state-of-the-art facilities, and lifelong friendships.', 'Student Activities', 'student_life.php', 'Visit Us', 'campus_map_&_facilities_page.php', 3);

-- Insert default sections
INSERT INTO homepage_sections (section_key, section_title, section_subtitle) VALUES
('discover_more', 'Discover <span>More</span>', 'Explore Valley View University\'s comprehensive academic programs, vibrant student life, and cutting-edge research opportunities.'),
('popular_programs', 'Popular <span>Programs</span>', 'Explore our most sought-after academic programs designed to prepare you for success in your chosen field.'),
('news_events', 'Latest News & <span>Events</span>', 'Stay updated with the latest happenings, announcements, and upcoming events at Valley View University.');

-- Insert default discover cards
INSERT INTO homepage_discover_cards (image_url, title, link_url, display_order) VALUES
('https://lh3.googleusercontent.com/aida-public/AB6AXuCCAWYDTkMrNEF76F7moKNtsvR8Z-aXUU76xT-0X08fJV3napzbvuBTT-f3oxaRyUzn6LPVuBIRdEHDI-uWXRSpyrN49UXKCjEHSxtNKfv8XXWrFpWvP1pVr2riHo9t0lCXT-MfJ239NhtfrfQ2YFhk9-h14wWaX_Iyfbnz6oDTzKMfxmLFLbefoPkjDS2Tv0znKp9FCUGaJFeMM3zV4ShkGRKHz1vyFOCrku1nCWcmaECFjJIfEHnhwBXrJ1USDMJb7kcOj-ZUSbwF', 'Admissions', 'admissions.php', 1),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAkO6W4ZumAM9XRQBouJYbDBZ2YvTOmJz8Y9wIx6ZQiMoCSnsWP2wcABBq1TFHFRPRBv6Ounx4cgWP6GFIVjvea5T6wRdM3cT-HNbZzCpmDnFmiT6Lx7efg8kQz_v0Do1-TU73vjvHBbNHbXaAF0sVM1AWGHDkH2pLWOBiKN6SY9UUPwObY9NhFekgTEbAAUl6uoSYGL0KccejjVSuIq7Zo36QSwmFnYEB4fwMViqQyj9otveJkzzYDXm796liZoqK87S-GjWOmzREB', 'Academics', 'academic_programs_overview.php', 2),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAhbhKQoDJFNKDLmuL_BNMPiQ7ju1ghsDoV8bIHl-J2nddloWCc_gqmk9Tjd_Es9Kz3N4kcPekEk5B9_HTjzK0S0MQwfcqzhsMkNs9H1vbmvF9geYkpQOSR1rsfImiZItE0iKvhfpO6fVnCDabYhpJrpHpVaae1M40iYq7g4b8dwAbqgsNWGu32p-xRrS9Dx7kNlg8K2vPLdyEHVlXaDGX8TQ0_J4wq1OU7cqOUZxBwC5mBG8AGsf_mW4SyS8h7_EytaAYH15S18RT9', 'Student Life', 'student_life.php', 3),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAGK5DGGjd2aSu_vH3dEn7L-PEvRJ0Mht26OSKtmfx6-_hNz2tomODvZelqGQAHcnISoeR_FljlhjH7q8ovhYOX6OZ9aD4LJ3FLLbGwgGo9u84kGLu9mlbLbNsgwEz5oKMvrHuW2iegIk3cNZFqU_8UeKOFt_UzGnS8ZM75ien1rHMtqsk8H_TQXVefa2wkEF-jZ_IHO-KIfI75Xj9zZzUiXX3YR0vRTw-Oqbq9I1xjTFp79wbhuf0RxWmhu_vwVM96YfgOcUp__pS9', 'Research & Education', 'research_opportunities.php', 4),
('https://lh3.googleusercontent.com/aida-public/AB6AXuCo3buv9jPCTibuvXJlEKIlE16n3wTLNwqJoiwca8ytkH4w4s-WuoiwDVgZnkOQzeJNULJNkS0XSlSFou46iOb1eS84niGc8IdYunJLvGtoH7qHQZozZ3bf812HxnFeSsZCxt_dFnV1b1NNNPpt6IjlNmeD7wlI27DPa2w7RlE1yQpy-z5bMUMe6O8E571nw2xxhb1rBTSLoaxzdSxkD_8F-x33u0K4mJb5-tLwMruhOCOXEqO1rseBMBjIduLVX5x1pcVnSrSI_s1l', 'Faculty Directory', 'faculty_encyclopedia.php', 5),
('Education-Website-and-AdminPanel/images/h-adm.jpg', 'Library Resources', 'library_resources.php', 6),
('Education-Website-and-AdminPanel/images/h-cam1.jpg', 'Campus & Facilities', 'campus_map_&_facilities_page.php', 7),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAaojC8YfAnUrkTWdviyuGMq-o-Saflg7ObA-DKasuyRcq7N1QyKvh8UMtGg0OdctBpj1bZnSHc61ybnf-tj4ZGt33excBJux4ruZDVcUoASWkSORaCVviBTmKiXvtNK8qz7N10wNdytq8WcCz9pMF6JNcTajt54bVSOtGg7649HmiX2xwuUOEmo0Ha72Uz34LI15jxLIz-HUC5Wr3nFk8WC_UAVbGuCauUBwkDVqTmEd40jyvJZWDHd0_-1a-Ssi2XrUObMmLC6ezj', 'Events & News', 'events.php', 8);

-- Insert default programs
INSERT INTO homepage_programs (image_url, title, category, description, rating, link_url, button1_link, button2_link, button3_link, display_order) VALUES
('Education-Website-and-AdminPanel/images/course/sm-1.jpg', 'Faculty of Science', 'Technology / Research / Innovation', 'Discover cutting-edge programs in biological sciences, computer science, mathematics and more', 4.8, 'academic_programs_overview.php', 'academic_programs_overview.php', 'faculty_of_science.php', 'apply.php', 1),
('Education-Website-and-AdminPanel/images/course/sm-2.jpg', 'School of Business', 'Business / Management / Finance', 'Gain practical business skills and knowledge to excel in the corporate world and entrepreneurship', 4.7, 'academic_programs_overview.php', 'academic_programs_overview.php', 'academic_programs_overview.php', 'apply.php', 2),
('Education-Website-and-AdminPanel/images/course/sm-3.jpg', 'School of Nursing & Midwifery', 'Healthcare / Nursing / Medical', 'Train to become a compassionate healthcare professional with our comprehensive nursing programs', 4.9, 'academic_programs_overview.php', 'academic_programs_overview.php', 'academic_programs_overview.php', 'apply.php', 3),
('Education-Website-and-AdminPanel/images/course/sm-4.jpg', 'School of Education', 'Teaching / Education / Development', 'Shape the future by becoming an inspiring educator through our accredited education programs', 4.6, 'academic_programs_overview.php', 'academic_programs_overview.php', 'academic_programs_overview.php', 'apply.php', 4),
('Education-Website-and-AdminPanel/images/course/sm-5.jpg', 'Faculty of Arts & Social Science', 'Arts / Humanities / Social Studies', 'Explore human culture, society, and communication through our diverse arts programs', 4.5, 'academic_programs_overview.php', 'academic_programs_overview.php', 'academic_programs_overview.php', 'apply.php', 5),
('Education-Website-and-AdminPanel/images/course/sm-6.jpg', 'School of Theology & Missions', 'Theology / Ministry / Religious Studies', 'Deepen your faith and prepare for ministry with our comprehensive theological education', 4.8, 'academic_programs_overview.php', 'academic_programs_overview.php', 'academic_programs_overview.php', 'apply.php', 6),
('Education-Website-and-AdminPanel/images/course/sm-7.jpg', 'School of Graduate Studies', 'Postgraduate / Masters / Research', 'Advance your career with our prestigious graduate programs and research opportunities', 4.7, 'academic_programs_overview.php', 'academic_programs_overview.php', 'academic_programs_overview.php', 'apply.php', 7),
('Education-Website-and-AdminPanel/images/course/sm-8.jpg', 'Continuing Education', 'Professional Development / Skills Training', 'Enhance your professional skills with our flexible continuing education and certificate programs', 4.6, 'academic_programs_overview.php', 'academic_programs_overview.php', 'academic_programs_overview.php', 'apply.php', 8);

-- Insert default gallery images
INSERT INTO homepage_gallery (image_url, caption, display_order) VALUES
('https://lh3.googleusercontent.com/aida-public/AB6AXuCBsTWrhC1L8hPKIAHMGfIXKKdwGb5ABivu_X28_0Emw8jLoU_Mm3bylmGNdNzCqWZBYcmechp3JA4aPL2rBeoVGwplow6Jfh-_G8lUhNgfWDYq3dNSvsl2Yoh2IqLUMJRLcbAqMgjEv9azfV63_2apLMt7pduIN0S70RlKg2uWhc5-MJxO01KvRGDte2K8BqP3R7rkD8CNbUhQl_HnqMkzBNjMQIhl5PazZDhtEjZZacQbPQDmTszdlC715JBVJ846acGQDeIhI9hj', 'VVU Campus Life', 1),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAGK5DGGjd2aSu_vH3dEn7L-PEvRJ0Mht26OSKtmfx6-_hNz2tomODvZelqGQAHcnISoeR_FljlhjH7q8ovhYOX6OZ9aD4LJ3FLLbGwgGo9u84kGLu9mlbLbNsgwEz5oKMvrHuW2iegIk3cNZFqU_8UeKOFt_UzGnS8ZM75ien1rHMtqsk8H_TQXVefa2wkEF-jZ_IHO-KIfI75Xj9zZzUiXX3YR0vRTw-Oqbq9I1xjTFp79wbhuf0RxWmhu_vwVM96YfgOcUp__pS9', 'VVU Research Facilities', 2),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAhbhKQoDJFNKDLmuL_BNMPiQ7ju1ghsDoV8bIHl-J2nddloWCc_gqmk9Tjd_Es9Kz3N4kcPekEk5B9_HTjzK0S0MQwfcqzhsMkNs9H1vbmvF9geYkpQOSR1rsfImiZItE0iKvhfpO6fVnCDabYhpJrpHpVaae1M40iYq7g4b8dwAbqgsNWGu32p-xRrS9Dx7kNlg8K2vPLdyEHVlXaDGX8TQ0_J4wq1OU7cqOUZxBwC5mBG8AGsf_mW4SyS8h7_EytaAYH15S18RT9', 'VVU Students', 3),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAkO6W4ZumAM9XRQBouJYbDBZ2YvTOmJz8Y9wIx6ZQiMoCSnsWP2wcABBq1TFHFRPRBv6Ounx4cgWP6GFIVjvea5T6wRdM3cT-HNbZzCpmDnFmiT6Lx7efg8kQz_v0Do1-TU73vjvHBbNHbXaAF0sVM1AWGHDkH2pLWOBiKN6SY9UUPwObY9NhFekgTEbAAUl6uoSYGL0KccejjVSuIq7Zo36QSwmFnYEB4fwMViqQyj9otveJkzzYDXm796liZoqK87S-GjWOmzREB', 'VVU Lecture Halls', 4),
('https://lh3.googleusercontent.com/aida-public/AB6AXuCo3buv9jPCTibuvXJlEKIlE16n3wTLNwqJoiwca8ytkH4w4s-WuoiwDVgZnkOQzeJNULJNkS0XSlSFou46iOb1eS84niGc8IdYunJLvGtoH7qHQZozZ3bf812HxnFeSsZCxt_dFnV1b1NNNPpt6IjlNmeD7wlI27DPa2w7RlE1yQpy-z5bMUMe6O8E571nw2xxhb1rBTSLoaxzdSxkD_8F-x33u0K4mJb5-tLwMruhOCOXEqO1rseBMBjIduLVX5x1pcVnSrSI_s1l', 'VVU Library', 5),
('https://lh3.googleusercontent.com/aida-public/AB6AXuAaojC8YfAnUrkTWdviyuGMq-o-Saflg7ObA-DKasuyRcq7N1QyKvh8UMtGg0OdctBpj1bZnSHc61ybnf-tj4ZGt33excBJux4ruZDVcUoASWkSORaCVviBTmKiXvtNK8qz7N10wNdytq8WcCz9pMF6JNcTajt54bVSOtGg7649HmiX2xwuUOEmo0Ha72Uz34LI15jxLIz-HUC5Wr3nFk8WC_UAVbGuCauUBwkDVqTmEd40jyvJZWDHd0_-1a-Ssi2XrUObMmLC6ezj', 'VVU Sports', 6),
('https://lh3.googleusercontent.com/aida-public/AB6AXuCCAWYDTkMrNEF76F7moKNtsvR8Z-aXUU76xT-0X08fJV3napzbvuBTT-f3oxaRyUzn6LPVuBIRdEHDI-uWXRSpyrN49UXKCjEHSxtNKfv8XXWrFpWvP1pVr2riHo9t0lCXT-MfJ239NhtfrfQ2YFhk9-h14wWaX_Iyfbnz6oDTzKMfxmLFLbefoPkjDS2Tv0znKp9FCUGaJFeMM3zV4ShkGRKHz1vyFOCrku1nCWcmaECFjJIfEHnhwBXrJ1USDMJb7kcOj-ZUSbwF', 'VVU Graduation', 7),
('Education-Website-and-AdminPanel/images/ami/8.jpg', 'VVU Events', 8),
('Education-Website-and-AdminPanel/images/ami/9.jpg', 'VVU Community', 9),
('Education-Website-and-AdminPanel/images/ami/10.jpg', 'VVU Activities', 10),
('Education-Website-and-AdminPanel/images/ami/11.jpg', 'VVU Campus', 11),
('Education-Website-and-AdminPanel/images/ami/1.jpg', 'VVU Excellence', 12);

-- Insert default news
INSERT INTO homepage_news (title, description, category, event_date, link_url, display_order) VALUES
('VVU Researchers Secure $5M Grant', 'Valley View University researchers have secured a groundbreaking grant for cancer study.', 'Research Excellence', '2024-12-15', 'news_&_events.php', 1),
('Annual Innovation Fair Success', 'Students showcased creativity and entrepreneurship at the annual innovation fair.', 'Campus Life', '2024-12-10', 'news_&_events.php', 2),
('New Academic Programs Announced', 'VVU expands academic offerings with new cutting-edge programs for 2025.', 'Academic Affairs', '2024-12-05', 'news_&_events.php', 3),
('Global Leadership Summit', 'VVU to host industry titans at upcoming global leadership summit.', 'Events', '2024-12-01', 'news_&_events.php', 4);

-- Insert default video
INSERT INTO homepage_video (video_url, title, description) VALUES
('https://www.youtube.com/embed/Nm0Dw2Zwyzg?si=_nggQLChojllZzjM', 'Welcome to Valley View University', 'Experience the vibrant campus life, state-of-the-art facilities, and diverse community that makes VVU a premier institution of higher learning. Join thousands of students who have chosen VVU as their pathway to success. Discover what makes our university special and how we can help you achieve your academic and career goals.');
