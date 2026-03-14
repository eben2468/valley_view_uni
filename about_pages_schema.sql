-- Database Schema for About Pages Content Management System
-- This file contains all tables needed to manage content for:
-- 1. Mission and Vision
-- 2. Core Values
-- 3. VVU Anthem
-- 4. Ecology
-- 5. The Campus

-- ========================================
-- MISSION AND VISION PAGE TABLES
-- ========================================

-- Hero section for Mission and Vision
CREATE TABLE IF NOT EXISTS mission_vision_hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_subtitle VARCHAR(100) NOT NULL DEFAULT 'About Our Institution',
    hero_title_1 VARCHAR(100) NOT NULL DEFAULT 'Our Mission',
    hero_title_2 VARCHAR(100) NOT NULL DEFAULT 'Vision',
    hero_description TEXT,
    hero_image_url TEXT,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Mission and Vision cards
CREATE TABLE IF NOT EXISTS mission_vision_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_type ENUM('vision', 'mission') NOT NULL,
    icon VARCHAR(50) DEFAULT 'visibility',
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    gradient_from VARCHAR(50) DEFAULT 'blue-600',
    gradient_to VARCHAR(50) DEFAULT 'blue-800',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Four Pillars of Development
CREATE TABLE IF NOT EXISTS mission_vision_pillars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon_color VARCHAR(50) DEFAULT 'green-400',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Learning Environment Section
CREATE TABLE IF NOT EXISTS mission_vision_environment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    badge_text VARCHAR(100) DEFAULT 'Our Commitment',
    section_title VARCHAR(200) NOT NULL,
    paragraph_1 TEXT NOT NULL,
    paragraph_2 TEXT NOT NULL,
    feature_1_title VARCHAR(100),
    feature_1_description VARCHAR(200),
    feature_2_title VARCHAR(100),
    feature_2_description VARCHAR(200),
    image_url TEXT,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- CORE VALUES PAGE TABLES
-- ========================================

-- Hero section for Core Values
CREATE TABLE IF NOT EXISTS core_values_hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_subtitle VARCHAR(100) NOT NULL DEFAULT 'Our Foundation',
    hero_title VARCHAR(200) NOT NULL DEFAULT 'Core Values',
    hero_subtitle VARCHAR(200) NOT NULL DEFAULT 'That Define Us',
    hero_description TEXT,
    hero_image_url TEXT,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Three Pillars (Excellence, Integrity, Service)
CREATE TABLE IF NOT EXISTS core_values_pillars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    feature_1 VARCHAR(200),
    feature_2 VARCHAR(200),
    quote TEXT,
    border_color VARCHAR(50) DEFAULT 'blue-600',
    bg_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Values in Action (6 cards)
CREATE TABLE IF NOT EXISTS core_values_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon_bg_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- VVU ANTHEM PAGE TABLES
-- ========================================

-- Hero section for VVU Anthem
CREATE TABLE IF NOT EXISTS anthem_hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_subtitle VARCHAR(100) NOT NULL DEFAULT 'University Anthem',
    hero_title VARCHAR(200) NOT NULL DEFAULT 'VVU Anthem',
    hero_subtitle VARCHAR(200) NOT NULL DEFAULT 'The Spirit of Valley View',
    hero_description TEXT,
    hero_image_url TEXT,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Anthem Stanzas
CREATE TABLE IF NOT EXISTS anthem_stanzas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stanza_number INT NOT NULL,
    stanza_title VARCHAR(50) NOT NULL,
    line_1 TEXT,
    line_2 TEXT,
    line_3 TEXT,
    line_4 TEXT,
    chorus_line_1 TEXT,
    chorus_line_2 TEXT,
    closing_line TEXT,
    border_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Anthem Video Section
CREATE TABLE IF NOT EXISTS anthem_video (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_title VARCHAR(200) DEFAULT 'Listen to the Anthem',
    section_description TEXT,
    video_url TEXT,
    video_poster_url TEXT,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- About the Anthem
CREATE TABLE IF NOT EXISTS anthem_about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    history_title VARCHAR(100) DEFAULT 'History of the Anthem',
    history_content TEXT,
    composer_title VARCHAR(100) DEFAULT 'About the Composer',
    composer_content TEXT,
    composer_name VARCHAR(100) DEFAULT 'Pastor Emmanuel O. Abbey',
    composition_date VARCHAR(50) DEFAULT 'September 2011',
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- ECOLOGY PAGE TABLES
-- ========================================

-- Hero section for Ecology
CREATE TABLE IF NOT EXISTS ecology_hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_subtitle VARCHAR(100) NOT NULL DEFAULT 'Ecological Stewardship',
    hero_title VARCHAR(200) NOT NULL DEFAULT 'Harmony with',
    hero_subtitle VARCHAR(200) NOT NULL DEFAULT 'God\'s Creation',
    hero_description TEXT,
    hero_image_url TEXT,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Ecological Philosophy (3 pillars)
CREATE TABLE IF NOT EXISTS ecology_philosophy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    feature_1 VARCHAR(200),
    feature_2 VARCHAR(200),
    quote TEXT,
    border_color VARCHAR(50) DEFAULT 'green-600',
    bg_color VARCHAR(50) DEFAULT 'green-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Green Initiatives (6 cards)
CREATE TABLE IF NOT EXISTS ecology_initiatives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon_bg_color VARCHAR(50) DEFAULT 'green-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ecological Impact Stats
CREATE TABLE IF NOT EXISTS ecology_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_value VARCHAR(50) NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- THE CAMPUS PAGE TABLES
-- ========================================

-- Hero section for The Campus
CREATE TABLE IF NOT EXISTS campus_hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_subtitle VARCHAR(100) NOT NULL DEFAULT 'The VVU Experience',
    hero_title VARCHAR(200) NOT NULL DEFAULT 'Explore Our',
    hero_subtitle VARCHAR(200) NOT NULL DEFAULT 'Vibrant Campus',
    hero_description TEXT,
    hero_image_url TEXT,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Why Choose VVU (3 main features)
CREATE TABLE IF NOT EXISTS campus_highlights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    quote TEXT,
    border_color VARCHAR(50) DEFAULT 'blue-600',
    bg_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Life on Campus Features (6 cards)
CREATE TABLE IF NOT EXISTS campus_features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon_bg_color VARCHAR(50) DEFAULT 'blue-600',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- INSERT DEFAULT DATA
-- ========================================

-- Mission and Vision Hero
INSERT INTO mission_vision_hero (page_subtitle, hero_title_1, hero_title_2, hero_description, hero_image_url) VALUES
('About Our Institution', 'Our Mission', 'Vision', 'Guiding principles and aspirations that define Valley View University\'s commitment to value-based Christian education and holistic development.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE');

-- Mission and Vision Cards
INSERT INTO mission_vision_cards (card_type, icon, title, content, gradient_from, gradient_to, display_order) VALUES
('vision', 'visibility', 'Our Vision', 'To be a leading center of Value-based Christian Education that transforms lives and impacts communities through excellence in teaching, research, and service, while nurturing ethical leaders who embody integrity, compassion, and a commitment to lifelong learning.', 'blue-600', 'blue-800', 1),
('mission', 'flag', 'Our Mission', 'To foster a harmonious development of the physical, intellectual, social, and spiritual faculties of students and staff in a well-designed and sound learning research environment for meaningful service to God and humanity.', 'green-500', 'green-700', 2);

-- Four Pillars
INSERT INTO mission_vision_pillars (icon, title, description, icon_color, display_order) VALUES
('fitness_center', 'Physical', 'Nurturing healthy bodies through sports, wellness programs, and facilities that promote active living.', 'green-400', 1),
('psychology', 'Intellectual', 'Cultivating critical thinking, academic excellence, and a passion for knowledge and research.', 'blue-400', 2),
('diversity_3', 'Social', 'Building strong relationships, leadership skills, and community engagement for positive impact.', 'purple-400', 3),
('church', 'Spiritual', 'Deepening faith, values, and character development for meaningful service to God and humanity.', 'yellow-400', 4);

-- Learning Environment
INSERT INTO mission_vision_environment (badge_text, section_title, paragraph_1, paragraph_2, feature_1_title, feature_1_description, feature_2_title, feature_2_description, image_url) VALUES
('Our Commitment', 'A Well-Designed Learning Environment', 'Valley View University provides a carefully designed learning and research environment that supports academic excellence and personal growth.', 'Our campus features modern facilities, state-of-the-art laboratories, comprehensive library resources, and collaborative spaces that inspire innovation and discovery.', 'Academic Excellence', 'Quality programs and expert faculty', 'Research Focus', 'Cutting-edge research opportunities', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC_zX1b5CqKFCcLw7g0EOVOSbzvzSiEe9fKThfucB1GLXCi6i0fbZpaup06wsjnyBK_97mggYZaL4waHy7OGg2OTLkGWzh5g8QO1MFLQs4R88Eu6bAAA8q5y5U3EYCV79ANtq4h3lN7QiWVX16GeBQlEnNo6bFLhnpDT4zokrykmBZPZjYZvZN8DOcwViDgpyvKcKRsOsa6ayE8lv-IcawcFeJaOtpDZzGZ-St1atk_m3Y-SZy3ZG0UZcYJ8GmUbRdny2Z-uX_udLPf');

-- Core Values Hero
INSERT INTO core_values_hero (page_subtitle, hero_title, hero_subtitle, hero_description, hero_image_url) VALUES
('Our Foundation', 'Core Values', 'That Define Us', 'At Valley View University, our core values are the guiding principles that shape our culture, inspire our community, and define our commitment to excellence in Christian education.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE');

-- Core Values Pillars
INSERT INTO core_values_pillars (icon, title, description, feature_1, feature_2, quote, border_color, bg_color, display_order) VALUES
('workspace_premium', 'Excellence', 'Excellence is our unwavering commitment to achieving the highest standards in all we do. We believe that every student deserves access to world-class education.', 'Academic Rigor & Innovation', 'Critical Thinking Focus', 'We don\'t just teach knowledge; we inspire a lifelong pursuit of excellence.', 'blue-600', 'blue-600', 1),
('verified_user', 'Integrity', 'Integrity is the moral compass that guides our actions. We are committed to upholding the highest ethical standards in all aspects of university life.', 'Ethical Leadership', 'Transparency & Honor', 'Integrity is the foundation of trust that binds our community together.', 'yellow-500', 'yellow-500', 2),
('volunteer_activism', 'Service', 'Service is our calling to make a positive difference. We believe that true education extends beyond the classroom and into the world.', 'Servant Leadership', 'Community Impact', 'We educate not just for careers, but for meaningful service.', 'green-600', 'green-600', 3);

-- Core Values in Action
INSERT INTO core_values_actions (icon, title, description, icon_bg_color, display_order) VALUES
('school', 'In Academics', 'Rigorous curriculum, research opportunities, and dedicated faculty ensure academic excellence in every program.', 'blue-600', 1),
('gavel', 'In Conduct', 'Honor codes, ethical guidelines, and a culture of accountability uphold integrity across campus.', 'yellow-500', 2),
('handshake', 'In Community', 'Outreach programs, volunteer initiatives, and mission work demonstrate our commitment to service.', 'green-600', 3),
('emoji_events', 'In Achievement', 'Recognizing and celebrating outstanding performance while maintaining high standards for all.', 'blue-600', 4),
('balance', 'In Leadership', 'Ethical leadership training that prepares students to lead with integrity and moral courage.', 'yellow-500', 5),
('favorite', 'In Compassion', 'Caring for one another and extending God\'s love through acts of kindness and support.', 'green-600', 6);

-- Anthem Hero
INSERT INTO anthem_hero (page_subtitle, hero_title, hero_subtitle, hero_description, hero_image_url) VALUES
('University Anthem', 'VVU Anthem', 'The Spirit of Valley View', 'Through Excellence, Integrity and Service; Valley View sends us to the world with peace.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO7K3MdvhJBsjnRN7t5ahbUnpEsN6IBzUuZZwH7CLb_OOZoqM3pwpXrQV7wTMDVY18bMLximB5Zpi0iNvsgzXDtOrZt20qiq3aKc6ohFAZ7FtlLVdEfxa6mSjbk6EnoF25ccqAEmVf4y-AF3Xq6laGg5Oxwl6WoCqTAcdqgl5ZHKssfYqfv0_HJmwgVa0RIAiC8lKcDETXxxgrOLnYn8C_ELq9y7H2k5L_YYT2-KC8QAIpSMdEOtygPw4fv94jht34itrHs6p5i4rl');

-- Anthem Stanzas
INSERT INTO anthem_stanzas (stanza_number, stanza_title, line_1, line_2, line_3, line_4, chorus_line_1, chorus_line_2, closing_line, border_color, display_order) VALUES
(1, 'Stanza One', 'You are so dear, You give us balanced living;', 'Christ is so near, You train for humanity.', 'Through Excellence, Integrity and Service;', 'Valley View sends us to the world with peace.', 'Rising to the task, Serving till the last', 'Human on earth shall receive and prize the right.', 'V V U glows the world with Hope and Light!', 'blue-600', 1),
(2, 'Stanza Two', 'Bright V V U, Premier, private Varsity,', 'Ghana needs you, The vanguard of unity.', 'From nations far and near, we learn together', 'To help our fatherlands to grow higher.', 'Sharing God\'s desire, Living to inspire', 'The young, old, rich and the poor to heights unknown.', 'V V U leads to bright and golden dawn.', 'yellow-500', 2),
(3, 'Stanza Three', 'Radiant You are, your expectations guide us;', 'Into a life that no one else can give us.', 'True diligence in labour we are seeking;', 'Valley View grants us this through God\'s leading.', 'Working day and night, Doing what is right;', 'Until a soul shall revive and sprout from gloom.', 'V V U guides us make some lives to bloom.', 'green-600', 3);

-- Anthem Video
INSERT INTO anthem_video (section_title, section_description, video_url, video_poster_url) VALUES
('Listen to the Anthem', 'Experience the official VVU Anthem - Vocal Path Cover', 'uploads/vvu-anthem-video.mp4', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO7K3MdvhJBsjnRN7t5ahbUnpEsN6IBzUuZZwH7CLb_OOZoqM3pwpXrQV7wTMDVY18bMLximB5Zpi0iNvsgzXDtOrZt20qiq3aKc6ohFAZ7FtlLVdEfxa6mSjbk6EnoF25ccqAEmVf4y-AF3Xq6laGg5Oxwl6WoCqTAcdqgl5ZHKssfYqfv0_HJmwgVa0RIAiC8lKcDETXxxgrOLnYn8C_ELq9y7H2k5L_YYT2-KC8QAIpSMdEOtygPw4fv94jht34itrHs6p5i4rl');

-- Anthem About
INSERT INTO anthem_about (history_title, history_content, composer_title, composer_content, composer_name, composition_date) VALUES
('History of the Anthem', 'The Valley View University anthem was composed by Pastor Emmanuel O. Abbey in September 2011. This inspiring piece encapsulates the university\'s enduring values of excellence, integrity, and service. The anthem beautifully expresses VVU\'s commitment to providing balanced education grounded in Christian principles, training students to serve humanity and bring hope and light to the world.', 'About the Composer', 'Pastor Emmanuel O. Abbey crafted this anthem with deep reverence for the university\'s mission and vision. His composition masterfully weaves together themes of faith, education, and service. The anthem has become a cherished symbol of VVU\'s identity, sung with pride at graduation ceremonies, convocations, and other significant university events.', 'Pastor Emmanuel O. Abbey', 'September 2011');

-- Ecology Hero
INSERT INTO ecology_hero (page_subtitle, hero_title, hero_subtitle, hero_description, hero_image_url) VALUES
('Ecological Stewardship', 'Harmony with', 'God\'s Creation', 'At Valley View University, we believe that caring for the environment is a sacred responsibility. Our campus is a living laboratory for sustainable development and ecological preservation.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAmDxsoRYwbAdA-K6FnHtGy5wBKf5vqZyCFrV-HUs0bGBbSYDDD3Wneaa4B3Mghrt-m8pX84m8r7qCgwcfDWVTgZ50_6SQnuA8eFAgja8xXsyydOyiQerdpRe8ByyUddDBpqrZiEkjhGqS2kqGy0E8GeQPOwbB-ubqUVSYHeioclUPe1rVhk9B5n7d1x91PPmJdcrant8ajJ6wr62nzNnnytxiWlIHbUtB4rcls1XQWOj-_Fb4eja9I6pobhorje4VNZvJg6liAcbOK');

-- Ecology Philosophy
INSERT INTO ecology_philosophy (icon, title, description, feature_1, feature_2, quote, border_color, bg_color, display_order) VALUES
('nature_people', 'Green Campus', 'Our campus is designed to coexist with nature. We maintain vast green spaces, botanical gardens, and protected wildlife habitats.', '15,000+ Trees Planted', 'Biodiversity Protection', 'A green environment fosters a clear mind and a peaceful spirit.', 'green-600', 'green-600', 1),
('solar_power', 'Sustainability', 'We are committed to reducing our environmental impact through energy conservation, waste segregation, and efficient resource management.', 'Energy Conservation', 'Waste Segregation', 'Innovation in sustainability is the key to our future survival.', 'yellow-500', 'yellow-500', 2),
('school', 'Education', 'We educate the next generation of environmental leaders through specialized courses and hands-on research.', 'Ecological Research', 'Community Workshops', 'Knowledge of the environment is the first step toward its protection.', 'blue-600', 'blue-600', 3);

-- Ecology Initiatives
INSERT INTO ecology_initiatives (icon, title, description, icon_bg_color, display_order) VALUES
('potted_plant', 'Organic Farm', 'Our university farm produces organic fruits and vegetables, promoting healthy living and sustainable agriculture.', 'green-600', 1),
('forest', 'Tree Nursery', 'Our dedicated tree nursery provides seedlings for our annual afforestation exercises and campus beautification projects.', 'blue-500', 2),
('recycling', 'Zero Waste', 'Our comprehensive recycling and composting programs aim to minimize waste sent to landfills.', 'yellow-600', 3),
('hiking', 'Nature Trails', 'Kilometers of nature trails provide students and staff with opportunities for exercise and reflection in nature.', 'green-600', 4),
('apartment', 'Eco-Architecture', 'New campus buildings are designed with natural ventilation and lighting to minimize energy consumption.', 'yellow-500', 5),
('cleaning_services', 'Sanitation', 'We maintain high standards of campus cleanliness through regular sanitation exercises and hygiene awareness programs.', 'blue-600', 6);

-- Ecology Stats
INSERT INTO ecology_stats (stat_value, stat_label, display_order) VALUES
('15k+', 'Trees Planted', 1),
('90%', 'Green Cover', 2),
('Zero', 'Plastic Waste', 3);

-- Campus Hero
INSERT INTO campus_hero (page_subtitle, hero_title, hero_subtitle, hero_description, hero_image_url) VALUES
('The VVU Experience', 'Explore Our', 'Vibrant Campus', 'Step into a world-class environment where academic excellence meets a serene, Christian atmosphere. Discover the \'Very, Very Unique\' touch of Valley View University.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCwMQREF1DNTiVX8Mt0yT_NXwihbW7HzEPMJWSNgBQCilTtI-Pyqwx0uf9UU1yMrmyCrXnx6GTxjWDSvbYKs1wCTGuYSJMd2wgD6bECQqPP84Ec0-M-7ROpYFQ7abu2FYSfGFlKV67C1vCRZkwCpYOR8wyyFr2Hn4inae6smuiwWtZUcdoGjyb4hX0aZBacOylHmMC6mBzEJy-CcMqb-ACqd8gK33jYhXbzNUejTEVIO-hLydTXEXEKoFBlnayg56kMq5_r5-6juVQr');

-- Campus Highlights
INSERT INTO campus_highlights (icon, title, description, quote, border_color, bg_color, display_order) VALUES
('verified', 'Accredited Degrees', 'Earn degrees accredited by the National Accreditation Board (Ghana) and the Accrediting Association of Adventist Universities.', 'Transferable credits to many global universities.', 'blue-600', 'blue-600', 1),
('landscape', 'Congenial Setting', 'Located on a 335-acre pristine land, providing a serene environment for studies and personal meditation.', '31 kilometres northeast of the capital, Accra.', 'yellow-500', 'yellow-500', 2),
('church', 'Christian Community', 'Experience the "Very, Very Unique" Christian touch where the whole university family cares for one another.', 'A family atmosphere for all nationalities.', 'green-600', 'green-600', 3);

-- Campus Features
INSERT INTO campus_features (icon, title, description, icon_bg_color, display_order) VALUES
('school', 'Academic Standards', 'Learn with an international staff of highly-qualified teachers interested in your personal success.', 'blue-600', 1),
('public', 'International Flavour', 'Make friends with students from different nationalities in a diverse and welcoming community.', 'yellow-500', 2),
('location_city', 'Proximity to City', 'Close to Accra, providing access to a multi-cultural environment and urban resources.', 'green-600', 3),
('payments', 'Value for Money', 'Get more than your money\'s worth through quality education and holistic development.', 'blue-600', 4),
('lightbulb', 'Innovation Hub', 'State-of-the-art facilities for creativity, startup incubation, and collaborative research.', 'yellow-500', 5),
('auto_awesome', 'Spiritual Growth', 'A dedicated environment for personal meditation and spiritual development.', 'green-600', 6);
