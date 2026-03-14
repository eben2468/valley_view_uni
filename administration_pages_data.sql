-- ============================================
-- Complete Data for Remaining 4 Administration Pages
-- Run this AFTER administration_pages_schema.sql
-- Vice Chancellor data is already in the schema file
-- ============================================

-- Note: This file only adds data for pages 2-5
-- Page 1 (Vice Chancellor) is already created in the schema file

-- ============================================
-- PRO-VICE CHANCELLOR PAGE DATA (page_id = 2)
-- ============================================

-- Get the Pro-VC page ID
SET @provc_page_id = (SELECT id FROM administration_pages WHERE page_slug = 'office_of_the_pro-vice_chancellor' LIMIT 1);

INSERT INTO `administration_content` (`page_id`, `section_type`, `section_key`, `content_order`) VALUES
(@provc_page_id, 'hero', 'hero_section', 1),
(@provc_page_id, 'profile', 'provc_profile', 2),
(@provc_page_id, 'section', 'career_leadership', 3),
(@provc_page_id, 'section', 'research_contributions', 4),
(@provc_page_id, 'section', 'contact_section', 5),
(@provc_page_id, 'section', 'cta_section', 6);

-- Get content section IDs
SET @provc_hero = LAST_INSERT_ID();
SET @provc_profile = @provc_hero + 1;

-- Hero Section for Pro-VC
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@provc_hero, 'badge_text', 'Academic Leadership', 'text'),
(@provc_hero, 'title_main', 'Office of the', 'text'),
(@provc_hero, 'title_highlight', 'Pro-Vice Chancellor', 'text'),
(@provc_hero, 'subtitle', 'Empowering academic excellence, fostering innovation, and driving digital transformation to shape the future of Valley View University.', 'textarea'),
(@provc_hero, 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image');

-- Profile Section for Pro-VC
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@provc_profile, 'profile_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA3DYOu722UbeqMnt5A6z1RSC3ZJL7ObJOF_ymftJttcbb5hu5KPxUDwEWQ1YnJlEH67SXOPJLcgHfK6yLx9gBSAua8CWI_F6jNO2wY-e7O34KnmgWRDReSfhRVWn52zOTyEdtoE2cGzFfFu9sNA1Dh-aJLxeJGilTtSnsSi8a9Y43daV1pkjPRFDI5UuJzqGSbsFQFsvwFGALUyQptWXtxWsDY-4eLAiFyVJgje0T_UrdsWG0iKcP-FCYMHijjKe-1x5gwT5xhNjWk', 'image'),
(@provc_profile, 'name', 'Prof. Winfred Ofoe Larkotey', 'text'),
(@provc_profile, 'title', 'Pro-Vice Chancellor', 'text'),
(@provc_profile, 'bio_paragraph_1', 'Prof. Winfred Ofoe Larkotey, an accomplished academic and visionary leader, embodies a rare blend of academic excellence, innovative thinking, and administrative prowess. With a robust background in Information Systems coupled with extensive experience in higher education administration, Prof. Larkotey has made significant contributions to academia, research, and institutional development.', 'textarea'),
(@provc_profile, 'bio_paragraph_2', 'His exemplary journey epitomizes academic excellence, leadership, and a steadfast commitment to driving positive change. As he continues to inspire and empower the next generation of scholars and leaders, his impact resonates far beyond the confines of academia, shaping the future of technology and education.', 'textarea'),
(@provc_profile, 'academic_journey', 'PhD in Information Systems from the University of Ghana, Legon.', 'text'),
(@provc_profile, 'tech_expertise', 'Specialist in Software Development, Fintech, and Digital Transformation.', 'text');

-- ============================================
-- OFFICE OF THE REGISTRAR PAGE DATA (page_id = 3)
-- ============================================

-- Get the Registrar page ID
SET @registrar_page_id = (SELECT id FROM administration_pages WHERE page_slug = 'office_of_the_registrar' LIMIT 1);

INSERT INTO `administration_content` (`page_id`, `section_type`, `section_key`, `content_order`) VALUES
(@registrar_page_id, 'hero', 'hero_section', 1),
(@registrar_page_id, 'profile', 'registrar_profile', 2),
(@registrar_page_id, 'section', 'services_section', 3),
(@registrar_page_id, 'section', 'quick_links', 4),
(@registrar_page_id, 'section', 'contact_section', 5),
(@registrar_page_id, 'section', 'cta_section', 6);

-- Get content section IDs
SET @reg_hero = LAST_INSERT_ID();
SET @reg_profile = @reg_hero + 1;
SET @reg_contact = @reg_hero + 4;

-- Hero Section for Registrar
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@reg_hero, 'badge_text', 'Administrative Excellence', 'text'),
(@reg_hero, 'title_main', 'Office of the', 'text'),
(@reg_hero, 'title_highlight', 'Registrar', 'text'),
(@reg_hero, 'subtitle', 'Your partner in academic success. We are committed to providing exceptional service to the Valley View University community from registration to graduation.', 'textarea'),
(@reg_hero, 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAdHExs_SfkRASYoES-KYWziZLFeXa6CwRE1tFfcoJoSatmp3K87chu9ZaDIp4kjBmAC4kTIatiMlZ3XOe354S5VOhhunVP4Wo9_FMc1LLmh72jKzKTTlzaL4qCmkTEo6z_WERGbhxGfFNtdyLOIJMxOTvuW1sK-AmKP0QVv4GCOd6a1lt3FrWoQ9IVoflIKJeoTiDMa44B7wkgq0Ykb3ud1rt5gDR_byRW18BjRjWDIiNKKd4-z8QKco_zxFkDaYymChai--z4X8Hv', 'image');

-- Profile Section for Registrar
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@reg_profile, 'profile_image', 'https://vvu.edu.gh/images/2021/04/20/mr-ibrah-web.jpg', 'image'),
(@reg_profile, 'name', 'Albert Kweku Imbrah', 'text'),
(@reg_profile, 'title', 'Registrar', 'text'),
(@reg_profile, 'bio_paragraph_1', 'Albert Kweku Imbrah joined Valley View University on March 1, 2006, having been appointed to set up and run the University''s Human Resource Department. His experience at Valley View University spans three administrations with each serving for a quinquennium.', 'textarea'),
(@reg_profile, 'bio_paragraph_2', 'With extensive experience of the workings of the administrative machinery of the contemporary tertiary academic landscape coupled with his strong leadership capabilities, the Registrar''s collaborative vision is to engender an administrative apparatus that ensures the University becomes a leading centre of excellence for value-based Christian Education.', 'textarea'),
(@reg_profile, 'academic_credentials', 'MA in Human Resource Management, BA in Social Science, LLB from KNUST', 'text'),
(@reg_profile, 'professional_membership', 'Member, Institute of Human Resource Practitioners, Ghana', 'text');

-- Contact Section for Registrar
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@reg_contact, 'section_title', 'Get in Touch', 'text'),
(@reg_contact, 'section_description', 'We are here to assist you with all your academic needs.', 'textarea'),
(@reg_contact, 'email', 'registrar@vvu.edu.gh', 'text'),
(@reg_contact, 'phone', '+233 (0) 307 051 149', 'text'),
(@reg_contact, 'office_location', 'Admin Block, Oyibi Campus', 'text'),
(@reg_contact, 'office_hours', 'Monday - Friday: 8:00 AM - 5:00 PM', 'text');

-- ============================================
-- RECTORS PAGE DATA (page_id = 4)
-- ============================================

-- Get the Rectors page ID
SET @rectors_page_id = (SELECT id FROM administration_pages WHERE page_slug = 'rectors' LIMIT 1);

INSERT INTO `administration_content` (`page_id`, `section_type`, `section_key`, `content_order`) VALUES
(@rectors_page_id, 'hero', 'hero_section', 1),
(@rectors_page_id, 'section', 'introduction', 2),
(@rectors_page_id, 'profile', 'kumasi_rector', 3),
(@rectors_page_id, 'profile', 'techiman_rector', 4),
(@rectors_page_id, 'section', 'leadership_impact', 5),
(@rectors_page_id, 'section', 'cta_section', 6);

-- Get content section IDs
SET @rect_hero = LAST_INSERT_ID();
SET @rect_intro = @rect_hero + 1;
SET @rect_kumasi = @rect_hero + 2;
SET @rect_techiman = @rect_hero + 3;

-- Hero Section for Rectors
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rect_hero, 'badge_text', 'Campus Leadership', 'text'),
(@rect_hero, 'title_main', 'Campus', 'text'),
(@rect_hero, 'title_highlight', 'Leadership', 'text'),
(@rect_hero, 'subtitle', 'Leading with vision, integrity, and commitment to academic excellence across our three campuses. Meet the distinguished leaders shaping the future of Valley View University.', 'textarea'),
(@rect_hero, 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image');

-- Introduction Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rect_intro, 'section_title', 'Our Campus Leadership Structure', 'text'),
(@rect_intro, 'section_description', 'Valley View University operates across three campuses. The main campus is led by the Vice Chancellor and Pro-Vice Chancellor, while the Kumasi and Techiman campuses each have a Rector who serves as the chief academic and administrative officer, ensuring excellence in teaching, research, and community engagement.', 'textarea');

-- Kumasi Rector Profile
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rect_kumasi, 'profile_image', 'https://vvu.edu.gh/images/2021/04/21/dr-larkotey.jpg', 'image'),
(@rect_kumasi, 'name', 'Winfred Ofoe Larkotey, PhD', 'text'),
(@rect_kumasi, 'title', 'Rector, Kumasi Campus', 'text'),
(@rect_kumasi, 'bio_paragraph_1', 'Winfred Ofoe Larkotey, PhD, is an enthusiastic information systems specialist and a Senior Lecturer with nine years of experience in consulting and training young minds on the development and use of technology. He has been a faculty member with Valley View University since January 2012.', 'textarea'),
(@rect_kumasi, 'bio_paragraph_2', 'Currently, Dr. Larkotey serves as the Rector of the Valley View University, Kumasi Campus, an appointment that started in February 2021. Previously, he served as Vice Rector for the Kumasi campus and Director of Information Technology Services.', 'textarea'),
(@rect_kumasi, 'academic_credentials', 'PhD in Information Systems, University of Ghana. BSc Computer Science from VVU', 'text'),
(@rect_kumasi, 'research_interests', 'Digital Government, Mobile Platforms, Human-Computer Interaction', 'text');

-- Techiman Rector Profile
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rect_techiman, 'profile_image', 'https://vvu.edu.gh/images/principal-officers/dr-emmanuel-bismarck-amponsah.jpg', 'image'),
(@rect_techiman, 'name', 'Emmanuel B. Amponsah, PhD', 'text'),
(@rect_techiman, 'title', 'Rector, Techiman Campus', 'text'),
(@rect_techiman, 'bio_paragraph_1', 'Emmanuel B. Amponsah (affectionately called EB) is an Associate Professor of Accounting who started teaching with the Ghana Education Service in 1986 and joined the Valley View University faculty in 2006. He has 9 enviable academic awards, bagging 5 of them on a single graduation day.', 'textarea'),
(@rect_techiman, 'bio_paragraph_2', 'Prof. EB joined the University Administration on February 1, 2016, as the Acting Rector of the Kumasi Campus where he is now the Rector of Techiman Campus. He is a gifted resource person, meticulous moderator, and successful fundraiser who has netted hundreds of thousands of cedis in assets for the University.', 'textarea'),
(@rect_techiman, 'academic_credentials', 'PhD in Business Administration, MPhil Accounting, BA Religion/Business Administration', 'text'),
(@rect_techiman, 'professional_status', 'Member, Chartered Institute of Management Accountants (UK & Ghana) since 2002', 'text');

-- ============================================
-- RECORDERS PAGE DATA (page_id = 5)
-- ============================================

-- Get the Recorders page ID
SET @recorders_page_id = (SELECT id FROM administration_pages WHERE page_slug = 'recorders' LIMIT 1);

INSERT INTO `administration_content` (`page_id`, `section_type`, `section_key`, `content_order`) VALUES
(@recorders_page_id, 'hero', 'hero_section', 1),
(@recorders_page_id, 'section', 'introduction', 2),
(@recorders_page_id, 'section', 'whats_included', 3),
(@recorders_page_id, 'section', 'access_documents', 4),
(@recorders_page_id, 'section', 'transparency', 5),
(@recorders_page_id, 'section', 'cta_section', 6);

-- Get content section IDs
SET @rec_hero = LAST_INSERT_ID();
SET @rec_intro = @rec_hero + 1;
SET @rec_whats = @rec_hero + 2;
SET @rec_access = @rec_hero + 3;

-- Hero Section for Recorders
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rec_hero, 'badge_text', 'Official Records', 'text'),
(@rec_hero, 'title_main', 'University', 'text'),
(@rec_hero, 'title_highlight', 'Recorders', 'text'),
(@rec_hero, 'subtitle', 'Official documentation of university decisions, policies, and administrative actions. Your gateway to institutional transparency and governance records.', 'textarea'),
(@rec_hero, 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image');

-- Introduction Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rec_intro, 'section_title', 'What Are University Recorders?', 'text'),
(@rec_intro, 'section_description', 'University Recorders are official documents that record important decisions, policies, appointments, and administrative actions taken by Valley View University. These documents serve as the institutional memory and provide transparency in university governance.', 'textarea');

-- What's Included Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rec_whats, 'section_title', 'What''s Included', 'text'),
(@rec_whats, 'section_description', 'University Recorders document the following types of institutional actions and decisions:', 'textarea');

-- Access Documents Section
INSERT INTO `administration_content_fields` (`content_id`, `field_key`, `field_value`, `field_type`) VALUES
(@rec_access, 'section_title', 'Access Recorder Documents', 'text'),
(@rec_access, 'section_description', 'All official university recorders are available for review. Access requires authentication to ensure document integrity and proper access control.', 'textarea'),
(@rec_access, 'login_url', 'https://ischool.vvu.edu.gh', 'text');
