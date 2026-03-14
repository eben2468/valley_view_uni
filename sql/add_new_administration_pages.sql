-- ============================================
-- Add New Administration Office Pages
-- Valley View University
-- ============================================

-- Insert new page records
INSERT IGNORE INTO `administration_pages` (`page_slug`, `page_title`, `page_name`) VALUES
('office_of_the_cfo', 'Office of the Chief Finance Officer', 'Office of the Chief Finance Officer'),
('office_of_rdir', 'Office of Research, Development, and International Relations', 'Office of Research, Development, and International Relations'),
('office_of_dsls', 'Office of the Dean of Students'' Life and Services', 'Office of the Dean of Students'' Life and Services'),
('office_of_sls', 'Office of the Dean of Spiritual Life and Development', 'Office of the Dean of Spiritual Life and Development');

-- Get the page IDs (these will be auto-incremented, adjust if needed)
-- Assuming the last page_id is 5, the new ones will be 6, 7, 8, 9

-- ============================================
-- Office of the CFO Content
-- ============================================
INSERT INTO `administration_content` (`page_id`, `section_type`, `section_key`, `content_order`) VALUES
(6, 'hero', 'hero_section', 1),
(6, 'profile', 'cfo_profile', 2),
(6, 'section', 'financial_vision', 3),
(6, 'section', 'contact_section', 4),
(6, 'section', 'cta_section', 5);

-- CFO Hero Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'badge_text', 'Financial Leadership', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'title_main', 'Office of the', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'title_highlight', 'Chief Finance Officer', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'subtitle', 'Ensuring financial sustainability and strategic resource management to support the university''s mission of academic excellence and institutional growth.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'hero_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'hero_section';

-- CFO Profile Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'profile_image', 'https://via.placeholder.com/400x500/4680ff/ffffff?text=CFO', 'image'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'name', 'Chief Finance Officer', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'title', 'Chief Finance Officer', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_title', 'Profile & Expertise', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'bio_paragraph_1', 'The Chief Finance Officer oversees all financial operations of Valley View University, ensuring fiscal responsibility, transparency, and strategic resource allocation to support the institution''s academic and operational goals.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'bio_paragraph_2', 'With extensive experience in financial management and higher education administration, the CFO leads initiatives in budget planning, financial reporting, investment management, and compliance with regulatory requirements.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'experience_title', 'Financial Expertise', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'experience_text', 'Strategic financial planning and institutional resource management.', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'impact_title', 'Fiscal Stewardship', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'impact_text', 'Ensuring financial sustainability and accountability.', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cfo_profile';

-- CFO Financial Vision Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_title', 'Financial Management Pillars', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_description', 'Our financial strategy is built on transparency, sustainability, and strategic investment in the university''s future.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_1_title', 'Budget Management', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_1_description', 'Strategic allocation of resources to support academic programs, infrastructure development, and operational excellence.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_2_title', 'Financial Reporting', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_2_description', 'Maintaining transparent and accurate financial records that meet international accounting standards and regulatory requirements.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_3_title', 'Investment Strategy', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_3_description', 'Prudent investment of university funds to generate sustainable returns and support long-term institutional growth.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_4_title', 'Risk Management', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'pillar_4_description', 'Implementing robust financial controls and risk mitigation strategies to protect university assets and ensure compliance.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'financial_vision';

-- CFO Contact Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_title', 'Contact the Office', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'section_description', 'For financial inquiries, budget matters, or administrative questions, please reach out to our finance team.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'email', 'cfo@vvu.edu.gh', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'phone', '+233 (0) 302 501 101', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'office_location', 'Finance Office, Administration Block', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'form_title', 'Financial Inquiry', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'form_description', 'Submit your financial questions or requests for information.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'form_btn_text', 'Submit Inquiry', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'contact_section';

-- CFO CTA Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'cta_title', 'Financial Transparency &', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cta_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'cta_highlight', 'Accountability', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cta_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'cta_description', 'Committed to responsible stewardship of university resources for the benefit of our students and community.', 'textarea'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cta_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'button_1_text', 'Financial Reports', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cta_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'button_1_url', '#', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cta_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'button_2_text', 'Contact Finance', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cta_section';

INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) 
SELECT c.id, 'button_2_url', 'contact_us.php', 'text'
FROM administration_content c WHERE c.page_id = 6 AND c.section_key = 'cta_section';
