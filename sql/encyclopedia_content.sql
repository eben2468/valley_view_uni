-- Encyclopedia Content Table for Hero and CTA sections
CREATE TABLE IF NOT EXISTS encyclopedia_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key ENUM('faculty', 'staff') NOT NULL,
    hero_title VARCHAR(255),
    hero_subtitle TEXT,
    hero_image VARCHAR(500),
    cta_title VARCHAR(255),
    cta_subtitle TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT IGNORE INTO encyclopedia_content (page_key, hero_title, hero_subtitle, hero_image, cta_title, cta_subtitle) VALUES
('faculty', 'Faculty Encyclopedia', 'Discover our distinguished team of academic professionals shaping the future.', 'images/faculty_of_science_hero.png', 'Join Our Academic Community', 'Are you passionate about education and research? Explore careers at Valley View University.'),
('staff', 'Staff Encyclopedia', 'Meet the dedicated administrative professionals who keep our university running smoothly.', 'images/home-2.jpg', 'Join Our Administrative Team', 'Help us build a better university environment for our students and staff.');
