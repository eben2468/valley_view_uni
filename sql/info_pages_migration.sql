-- Migration for Info Pages: freshmen_info, new_to_vvu, take_a_tour, download_forms

-- 1. Insert Page Content
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('freshmen_info', 'Freshmen Information', 'Welcome Freshmen', 'Freshmen Info', 'Your Journey Starts Here', '"Welcome to Valley View University - where your academic dreams come to life and your future begins."', 'images/freshmen_hero_bg.png', 'Start Your Journey at VVU Today', 'Join our vibrant community of learners and discover your potential. Take the first step towards a bright future.', 'Apply Now', 'apply.php'),
('new_to_vvu', 'New to VVU', 'Welcome to Our Community', 'New to VVU', 'Discover Your Future', '"Explore the unique opportunities and vibrant campus life that await you at Valley View University."', 'images/new_to_vvu_hero_bg.png', 'Ready to Join the VVU Family?', 'Take the first step towards a transformative educational experience. We are excited to welcome you to our campus.', 'Apply Now', 'apply.php'),
('take_a_tour', 'Take a Tour', 'Explore Our Campus', 'Take a Tour', 'See VVU for Yourself', '"Take a virtual walk through our beautiful campus and discover our state-of-the-art facilities."', 'images/take_a_tour_hero_bg.png', 'Experience VVU in Person', 'Nothing beats seeing our campus in person. Schedule a campus visit today.', 'Schedule Visit', 'contact_us.php'),
('download_forms', 'Download Forms', 'Essential Documents', 'Download Forms', 'Everything You Need', 'Access and download all necessary application forms, medical forms, and other essential documents here.', 'images/pro-bg.jpg', 'Need More Help?', 'If you can''t find the form you''re looking for, please contact the admissions office.', 'Contact Us', 'contact_us.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

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
('take_a_tour', 'aerial', 'Aerial Tour', 'Watch our campus from above.', 3),
-- Download Forms
('download_forms', 'undergrad', 'Undergraduate Forms', 'Application forms for undergraduate programs.', 1),
('download_forms', 'postgrad', 'Postgraduate Forms', 'Application forms for postgraduate programs.', 2),
('download_forms', 'nursing', 'Nursing & Midwifery', 'Forms for nursing and midwifery programs.', 3),
('download_forms', 'french', 'French Programs', 'Forms for our French language programs.', 4),
('download_forms', 'others', 'Other Important Forms', 'Medical forms and other documents.', 5),
('download_forms', 'help', 'Need Assistance?', 'Contact us for help with forms.', 6)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 3. Freshmen Info Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_icon`, `display_order`) VALUES
('freshmen_info', 'requirements', 'Undergraduate Programmes', '<p><strong>WASSCE/SSSCE/GBCE:</strong> Six (6) subjects comprising three (3) Core subjects (English, Maths, Science/Social Studies) plus three (3) relevant elective subjects.</p>', 'blue-600', 'school', 1),
('freshmen_info', 'requirements', 'Top-up Programmes', '<p>Holders of HND, Diploma from recognized institutions (University of Ghana, UCC, etc.) are eligible to apply.</p>', 'green-600', 'upgrade', 2),
('freshmen_info', 'checklist', 'Admission Letter', 'Download and print your official admission letter from the portal.', 'blue-600', 'drafts', 1),
('freshmen_info', 'checklist', 'Fee Payment', 'Pay your fees at any designated bank or via mobile money.', 'blue-600', 'payments', 2),
('freshmen_info', 'checklist', 'Registration', 'Complete your online course registration.', 'blue-600', 'app_registration', 3),
('freshmen_info', 'resources', 'Admission Guide', 'Download our full guide for the current academic year.', 'blue-600', 'description', 1),
('freshmen_info', 'resources', 'Student Handbook', 'Rules and regulations for campus life.', 'green-600', 'menu_book', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 4. New to VVU Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_icon`, `display_order`) VALUES
('new_to_vvu', 'why_choose', 'Accredited Degrees', 'Earn degrees accredited by NAB (Ghana) and Adventist Accrediting Association.', 'blue-600', 'verified', 1),
('new_to_vvu', 'why_choose', 'Academic Standards', 'Highly-qualified teachers dedicated to your success.', 'green-600', 'auto_stories', 2),
('new_to_vvu', 'programs', 'Computer Science', 'Master the latest technologies and software practices.', 'blue-600', 'computer', 1),
('new_to_vvu', 'programs', 'Nursing', 'Join a noble profession with our highly-regarded program.', 'green-600', 'medical_services', 2),
('new_to_vvu', 'parents', 'Safe & Secure', '24/7 security and a supportive community.', 'blue-600', 'security', 1),
('new_to_vvu', 'resources', 'Freshmen Info', 'Essential guide for new students.', 'blue-600', 'info', 1),
('new_to_vvu', 'resources', 'FAQs', 'Common questions answered.', 'yellow-600', 'help', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 5. Take a Tour Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_icon`, `display_order`) VALUES
('take_a_tour', 'overview', 'Strategic Location', 'Located at Mile 19 off the Adenta-Dodowa Road.', 'blue-600', 'location_on', 1),
('take_a_tour', 'overview', 'Eco-Friendly', 'Africa''s only eco-friendly university campus.', 'green-600', 'eco', 2),
('take_a_tour', 'landmarks', 'Administration Block', 'The heart of the university.', 'blue-600', 'account_balance', 1),
('take_a_tour', 'landmarks', 'University Library', 'A state-of-the-art facility for research.', 'green-600', 'menu_book', 2),
('take_a_tour', 'aerial', 'Drone Footage', 'uploads/AERIAL VIEW OF VALLEY VIEW UNIVERSITY BY OPAREDAWURO.mp4.mp4', 'indigo-600', 'videocam', 1)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 6. Download Forms Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_link`, `display_order`) VALUES
('download_forms', 'undergrad', 'Main Application Form', 'Required for all undergraduate programs.', 'blue-600', 'uploads/undergrad_app.pdf', 1),
('download_forms', 'others', 'Medical Examination Form', 'Required for all new students.', 'red-600', 'uploads/medical_form.pdf', 1),
('download_forms', 'help', 'Contact Support', 'Get help from our admissions team.', 'blue-600', 'contact_support', 1)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;
