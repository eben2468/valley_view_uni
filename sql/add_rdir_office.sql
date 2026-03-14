-- ============================================
-- Office of RDIR Content
-- ============================================
INSERT INTO `administration_content` (`page_id`, `section_type`, `section_key`, `content_order`) VALUES
(7, 'hero', 'hero_section', 1),
(7, 'profile', 'rdir_profile', 2),
(7, 'section', 'research_vision', 3),
(7, 'section', 'contact_section', 4),
(7, 'section', 'cta_section', 5);

-- RDIR Hero Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'badge_text', 'Research & Global Engagement', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'title_main', 'Office of', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'title_highlight', 'Research, Development & International Relations', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'subtitle', 'Advancing knowledge through innovative research, fostering international partnerships, and driving institutional development for global impact.', 'textarea'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'hero_section';

-- RDIR Profile Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'profile_image', 'https://via.placeholder.com/400x500/10b981/ffffff?text=RDIR', 'image'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'name', 'Director of RDIR', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'title', 'Director, Research Development & International Relations', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_title', 'Leadership & Vision', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'bio_paragraph_1', 'The Office of Research, Development, and International Relations (RDIR) serves as the catalyst for academic research, institutional development, and global partnerships at Valley View University. Our mission is to promote excellence in research and foster meaningful international collaborations.', 'textarea'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'bio_paragraph_2', 'We support faculty and students in conducting impactful research, securing funding opportunities, and establishing partnerships with institutions worldwide. Through strategic initiatives, we aim to position VVU as a leading research institution in Africa.', 'textarea'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'experience_title', 'Research Excellence', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'experience_text', 'Promoting cutting-edge research and innovation.', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'impact_title', 'Global Partnerships', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'impact_text', 'Building international collaborations and exchange programs.', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'rdir_profile';

-- RDIR Research Vision Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_title', 'Strategic Focus Areas', 'text'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'research_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_description', 'Our office drives excellence through research support, international collaboration, and institutional development initiatives.', 'textarea'
FROM administration_content c WHERE c.page_id = 7 AND c.section_key = 'research_vision';
