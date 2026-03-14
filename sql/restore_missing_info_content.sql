-- Comprehensive Migration for Info Pages: freshmen_info, new_to_vvu, take_a_tour, download_forms
-- Restoring missing content and unifying section keys

-- 1. Insert/Update Page Content
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('freshmen_info', 'Freshmen Information', 'Welcome Freshmen!', 'Freshmen', 'Information', '"Welcome to Valley View University - where your academic dreams come to life and your future begins."', 'images/freshmen_hero_bg.png', 'Ready to Start Your Journey?', 'Join our vibrant community of learners and discover your potential. Take the first step towards a bright future.', 'Apply Now', 'apply.php'),
('new_to_vvu', 'New to VVU', 'Welcome Home', 'New to', 'VVU Community', '"Discover the unique opportunities and vibrant campus life that await you at Valley View University."', 'images/new_to_vvu_hero_bg.png', 'Ready to Join the Family?', 'Take the first step towards a transformative educational experience. We are excited to welcome you to our campus.', 'Apply Now', 'apply.php'),
('take_a_tour', 'Take a Tour', 'Explore Our Campus', 'Experience', 'VVU in 360°', '"Experience the beauty, serenity, and state-of-the-art facilities that make Valley View University a world-class institution."', 'images/new_to_vvu_hero_bg.png', 'See Our Campus in Person', 'Nothing beats seeing our campus in person. Schedule a campus visit today and talk to our advisors.', 'Schedule a Visit', 'contact_us.php'),
('download_forms', 'Download Forms', 'Resources & Downloads', 'Download', 'Official Forms', 'Access all necessary application forms, requirements, and information guides in one place.', 'Education-Website-and-AdminPanel/images/pro-bg.jpg', 'Ready to Join Us?', 'Take the first step towards a brighter future. Apply online today for a faster and more convenient process.', 'Apply Online Now', 'https://admissions.vvu.edu.gh/')
ON DUPLICATE KEY UPDATE 
    `page_title` = VALUES(`page_title`),
    `hero_badge` = VALUES(`hero_badge`),
    `hero_title` = VALUES(`hero_title`),
    `hero_subtitle` = VALUES(`hero_subtitle`),
    `hero_description` = VALUES(`hero_description`),
    `hero_image` = VALUES(`hero_image`),
    `cta_title` = VALUES(`cta_title`),
    `cta_subtitle` = VALUES(`cta_subtitle`),
    `cta_button_text` = VALUES(`cta_button_text`),
    `cta_button_link` = VALUES(`cta_button_link`);

-- 2. Sections
INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
-- Freshmen Info
('freshmen_info', 'requirements', 'Entry Requirements', 'Detailed qualifications for our various academic programmes.', 1),
('freshmen_info', 'checklist', 'Freshmen Checklist', 'Essential steps to ensure a smooth start to your university life.', 2),
('freshmen_info', 'grading', 'Grading System', 'Understanding the evaluation criteria for SSSCE/WASSCE/GBCE holders.', 3),
('freshmen_info', 'important', 'Important Information', 'Key details every applicant should know before applying.', 4),
('freshmen_info', 'support', 'Support Services', 'We are committed to your success.', 5),
('freshmen_info', 'resources', 'Quick Resources', 'Access essential documents.', 6),
-- New to VVU
('new_to_vvu', 'why_choose', 'Why Choose VVU?', 'Experience a world-class education.', 1),
('new_to_vvu', 'programs', 'Academic Programs', 'Discover our diverse range of accredited programs.', 2),
('new_to_vvu', 'parents', 'Parent''s Guide', 'A partner in your child''s success.', 3),
('new_to_vvu', 'resources', 'Quick Resources', 'Everything you need to get started.', 4),
-- Take a Tour
('take_a_tour', 'overview', 'Campus Overview', 'Pristine land, modern architecture, natural beauty.', 1),
('take_a_tour', 'landmarks', 'Key Landmarks', 'Iconic buildings and facilities.', 2),
('take_a_tour', 'aerial_tour', 'Aerial Tour', 'Watch our campus from above.', 3),
-- Download Forms
('download_forms', 'undergrad', 'Undergraduate Admissions', 'Available Documents', 1),
('download_forms', 'postgrad', 'Postgraduate Admissions', 'Available Documents', 2),
('download_forms', 'nursing', 'Nursing & Special Programs', 'Available Documents', 3),
('download_forms', 'french', 'International Admissions (Français)', 'Available Documents', 4),
('download_forms', 'others', 'Other Forms & Research', 'Available Documents', 5),
('download_forms', 'help', 'Need Assistance?', 'Contact us for help with forms.', 6)
ON DUPLICATE KEY UPDATE 
    `section_title` = VALUES(`section_title`),
    `section_subtitle` = VALUES(`section_subtitle`),
    `display_order` = VALUES(`display_order`);

-- 3. Freshmen Info Items
DELETE FROM `academic_pages_items` WHERE `page_key` = 'freshmen_info';
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_icon`, `item_subtitle`, `item_stat_value`, `display_order`) VALUES
-- Requirements
('freshmen_info', 'requirements', 'Undergraduate Programmes', '<p><strong>WASSCE/SSSCE/GBCE:</strong> Six (6) subjects comprising three (3) Core subjects (English, Mathematics, Science/Social Studies) plus three (3) relevant elective subjects.</p>', 'blue-600', 'school', 'Standard Entry', NULL, 1),
('freshmen_info', 'requirements', 'Top-up Programmes', '<p>Holders of HND, Diploma from recognized institutions (University of Ghana, UCC, etc.) are eligible to apply for direct entry into Level 300 or 200.</p>', 'green-600', 'upgrade', 'Diploma Holders', NULL, 2),
('freshmen_info', 'requirements', 'Diploma Programmes', '<p>WASSCE/SSSCE/GBCE with at least five (5) passes including English and Mathematics.</p>', 'purple-600', 'workspace_premium', '2-Year Programs', NULL, 3),

-- Checklist
('freshmen_info', 'checklist', 'Admission Letter', '<p>Download and print your official admission letter from the applicant portal.</p>', 'blue-600', 'drafts', NULL, NULL, 1),
('freshmen_info', 'checklist', 'Fee Payment', '<p>Pay your fees at any designated bank or via the online payment portal.</p>', 'green-600', 'payments', NULL, NULL, 2),
('freshmen_info', 'checklist', 'Medical Examination', '<p>Conduct your mandatory medical exam at the VVU Hospital on the Techiman or Oyibi campus.</p>', 'red-600', 'medical_services', NULL, NULL, 3),
('freshmen_info', 'checklist', 'Registration', '<p>Complete your online course registration and biometric data capture.</p>', 'indigo-600', 'app_registration', NULL, NULL, 4),

-- Grading System
('freshmen_info', 'grading', 'A1', '<p>Excellent</p>', 'blue-600', NULL, 'A', 'A', 1),
('freshmen_info', 'grading', 'B2', '<p>Very Good</p>', 'blue-600', NULL, 'B', 'B', 2),
('freshmen_info', 'grading', 'B3', '<p>Good</p>', 'blue-600', NULL, 'C', 'C', 3),
('freshmen_info', 'grading', 'C4', '<p>Credit</p>', 'blue-600', NULL, 'D', 'D', 4),
('freshmen_info', 'grading', 'C5', '<p>Credit</p>', 'blue-600', NULL, 'E', 'E', 5),
('freshmen_info', 'grading', 'C6', '<p>Credit</p>', 'blue-600', NULL, 'F', 'F', 6),

-- Important
('freshmen_info', 'important', 'Official Channels Only', '<p>VVU does not utilize admission agents. Always apply directly through the official website.</p>', 'red-600', 'gpp_maybe', NULL, NULL, 1),
('freshmen_info', 'important', 'Verified Payments', '<p>Payments should only be made into the official University bank accounts listed in your admission letter.</p>', 'yellow-600', 'warning', NULL, NULL, 2),

-- Support
('freshmen_info', 'support', 'Chaplaincy', '<p>Spiritual guidance and counseling for all students, regardless of faith.</p>', 'blue-600', 'church', NULL, NULL, 1),
('freshmen_info', 'support', 'Academic Advising', '<p>Each student is assigned an advisor to help navigate their academic path.</p>', 'green-600', 'groups', NULL, NULL, 2),
('freshmen_info', 'support', 'IT Helpdesk', '<p>Support for student portal, Wi-Fi, and other technical needs.</p>', 'indigo-600', 'computer', NULL, NULL, 3),

-- Resources
('freshmen_info', 'resources', 'Admission Guide', NULL, 'blue-600', 'description', '2024/2025 Guide', NULL, 1),
('freshmen_info', 'resources', 'Student Handbook', NULL, 'green-600', 'menu_book', 'Rules & Regs', NULL, 2);

-- 4. New to VVU Items
DELETE FROM `academic_pages_items` WHERE `page_key` = 'new_to_vvu';
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_icon`, `item_subtitle`, `display_order`) VALUES
('new_to_vvu', 'why_choose', 'Global Accreditation', '<p>Degrees recognized by the AAA and GTEC, opening doors globally.</p>', 'blue-600', 'verified', NULL, 1),
('new_to_vvu', 'why_choose', 'Holistic Growth', '<p>Education that balances mind, body, and spirit for total wellness.</p>', 'green-600', 'psychology', NULL, 2),
('new_to_vvu', 'why_choose', 'Eco-Friendly Campus', '<p>Study in Africa''s first eco-friendly university environment.</p>', 'teal-600', 'eco', NULL, 3),

('new_to_vvu', 'programs', 'Computer Science', '<p>Leading IT training with modern laboratories and industry links.</p>', 'blue-600', 'computer', 'Browse Program', 1),
('new_to_vvu', 'programs', 'Nursing & Midwifery', '<p>Clinical excellence in a caring, professional environment.</p>', 'green-600', 'medical_services', 'Browse Program', 2),
('new_to_vvu', 'programs', 'Business Admin', '<p>Developing future leaders with ethical business practices.</p>', 'indigo-600', 'business_center', 'Browse Program', 3),

('new_to_vvu', 'parents', 'Safety First', '<p>Gated community with 24/7 security and a safe social atmosphere.</p>', 'blue-600', 'security', 'Core Priority', 1),
('new_to_vvu', 'parents', 'Moral Foundation', '<p>Strong focus on Christian values and character building.</p>', 'purple-600', 'church', 'Core Priority', 2),

('new_to_vvu', 'resources', 'Campus Map', NULL, 'blue-600', 'map', 'Find your way', 1),
('new_to_vvu', 'resources', 'Freshmen Portal', NULL, 'indigo-600', 'login', 'Start online', 2);

-- 5. Take a Tour Items
DELETE FROM `academic_pages_items` WHERE `page_key` = 'take_a_tour';
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_icon`, `item_image`, `item_link`, `display_order`) VALUES
('take_a_tour', 'overview', 'Serene Atmosphere', '<p>Experience a campus designed for focus, reflection, and academic success.</p>', 'blue-600', 'spa', NULL, NULL, 1),
('take_a_tour', 'overview', 'Modern Facilities', '<p>From smart classrooms to high-tech labs, we provide the best tools for learning.</p>', 'indigo-600', 'domain', NULL, NULL, 2),

('take_a_tour', 'landmarks', 'University Library', '<p>A multi-story hub for research and study, housing thousands of resources.</p>', 'blue-600', 'local_library', 'images/excellence.png', NULL, 1),
('take_a_tour', 'landmarks', 'The Great Hall', '<p>Host to major events, graduation ceremonies, and spiritual gatherings.</p>', 'indigo-600', 'account_balance', 'images/home-2.jpg', NULL, 2),

('take_a_tour', 'aerial_tour', 'Drone Footage', '<p>Watch the full aerial overview of our pristine 100-acre campus.</p>', 'blue-600', 'videocam', NULL, 'uploads/AERIAL VIEW OF VALLEY VIEW UNIVERSITY BY OPAREDAWURO.mp4.mp4', 1);

-- 6. Download Forms Items
DELETE FROM `academic_pages_items` WHERE `page_key` = 'download_forms';
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_icon`, `item_link`, `item_subtitle`, `display_order`) VALUES
('download_forms', 'undergrad', 'Undergraduate Admission Form', 'Required for all first-degree applications.', 'blue-600', 'description', 'uploads/undergrad_app.pdf', 'PDF', 1),
('download_forms', 'undergrad', 'Requirements Guide', 'General entry requirements for SHS graduates.', 'blue-600', 'info', 'uploads/admission-requirements-general.pdf', 'PDF', 2),

('download_forms', 'postgrad', 'Postgraduate Application Form', 'For Masters and PhD programs.', 'purple-600', 'workspace_premium', 'uploads/post-graduate-form.pdf', 'PDF', 1),
('download_forms', 'postgrad', 'Appendix A (Reference)', 'Required reference form for PG applicants.', 'purple-600', 'summarize', 'uploads/post-graduate-appendix-a.pdf', 'PDF', 2),

('download_forms', 'nursing', 'Nursing Entry Requirements', 'Specific requirements for Nursing/Midwifery.', 'green-600', 'medical_services', 'uploads/nursing-requirements.pdf', 'PDF', 1),
('download_forms', 'nursing', 'Nursing Access Form', 'For the Nursing Access program.', 'green-600', 'description', 'uploads/nursing-access-application-form-updated.pdf', 'PDF', 2),

('download_forms', 'french', 'Formulaire Béninois', 'Admission pour le Bénin.', 'yellow-600', 'public', 'Formulaire dadmission au Bnin.pdf', 'PDF', 1),
('download_forms', 'french', 'Formulaire Togolais', 'Admission pour le Togo.', 'yellow-600', 'public', 'Formulaire dadmission au Togo.pdf', 'PDF', 2),

('download_forms', 'others', 'Medical Examination Form', 'Required for all newly admitted students.', 'gray-700', 'health_and_safety', 'uploads/medical_form.pdf', 'PDF', 1),
('download_forms', 'others', 'Employment Form', 'Form for job applications at the university.', 'gray-700', 'work', 'uploads/employment-forma.pdf', 'PDF', 2),

('download_forms', 'help', 'Contact Support', 'Get help from our admissions team via phone or email.', 'blue-600', 'support_agent', 'contact_us.php', 'Agent', 1);
