-- ============================================
-- Admissions Info Pages Content Management
-- Migration for: 
-- 1. provisional_admission_list.php
-- 2. entry-requirement.php
-- 3. caution-to-applicants.php
-- 4. scholarships.php
-- 5. scholarships-forms.php
-- ============================================

-- 1. PROVISIONAL ADMISSION LIST PAGE
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('provisional_admission_list', 'Provisional Admission List', 'Admissions 2024/2025', 'Provisional', 'Admission List', 'Check your status and join the family of excellence.', 'images/pro-bg.jpg', 'Still Waiting for Your Name?', 'Our admissions process is ongoing. If your name is not on the list, don\'t panic! More batches are coming.', 'Contact Admissions', 'contact_us.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('provisional_admission_list', 'official_lists', 'Official Admission Lists', 'Browse the categories below to find your admission status.', 1),
('provisional_admission_list', 'guidance', 'I\'ve found my name. What\'s next?', 'Follow these steps to complete your enrollment.', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_link`, `item_stat_value`, `display_order`) VALUES
('provisional_admission_list', 'official_lists', 'Undergraduate Batch 1', 'List of provisionally admitted students for undergraduate programs.', 'uploads/admission_lists/undergrad_batch_1.pdf', 'Batch 1', 1),
('provisional_admission_list', 'official_lists', 'Postgraduate Batch 1', 'List of provisionally admitted students for postgraduate programs.', 'uploads/admission_lists/postgrad_batch_1.pdf', 'Batch 1', 2),
('provisional_admission_list', 'guidance', 'Download Admission Letter', 'Log in to the portal with your application ID to download your official letter.', NULL, '1', 1),
('provisional_admission_list', 'guidance', 'Pay Commitment Fee', 'Secure your spot by paying the required commitment fee before the deadline.', NULL, '2', 2),
('provisional_admission_list', 'guidance', 'Complete Medicals', 'Undergo the mandatory university medical examination at the VVU Hospital.', NULL, '3', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 2. ENTRY REQUIREMENTS PAGE
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('entry_requirements', 'Entry Requirements', 'Admissions 2025', 'Entry', 'Requirements', 'Your journey to excellence begins here. Explore the pathways to becoming a part of the Valley View University community.', 'https://images.unsplash.com/photo-1523050853063-bd80e27433fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80', 'Ready to Apply?', 'Take the first step towards your future today. Our application process is simple and straightforward.', 'Apply Now', 'apply.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('entry_requirements', 'intro', 'Pathways to Success', 'Valley View University offers diverse and inclusive entry points, designed to bridge your ambition with academic excellence at every stage of your career.', 0),
('entry_requirements', 'postgraduate', 'Postgraduate Programs', 'Requirements for Master\'s and PhD programs', 1),
('entry_requirements', 'undergraduate', 'First Degree Programs', 'Requirements for Bachelor\'s and Diploma programs', 2),
('entry_requirements', 'special', 'Special & Professional Entry', 'Alternative entry pathways and special categories', 3),
('entry_requirements', 'resources', 'Admission Resources', 'Everything you need to complete your application', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('entry_requirements', 'postgraduate', 'Terminal Degree (PhD)', 'Relevant Master\'s Degree\nPass Admission Interview', 'workspace_premium', 'blue-600', 1),
('entry_requirements', 'postgraduate', 'Second Degree (Masters)', 'Relevant Bachelor\'s Degree\nSecond Class Lower or Better\nPass Admission Interview', 'workspace_premium', 'yellow-500', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_subtitle`, `item_icon`, `item_color`, `display_order`) VALUES
('entry_requirements', 'undergraduate', 'WASSCE / SSSCE', 'Credit Passes (A1-C6 / A-D) in six (6) subjects.', '3 Core Subjects (incl. English & Math), 3 Relevant Elective Subjects', 'history_edu', 'purple-600', 1),
('entry_requirements', 'undergraduate', 'GCE Advanced Level', 'Passes in three (3) subjects (at least one Grade D or better).', 'Credit passes in 5 GCE O-Level subjects, Incl. English, Math & Science/Arts', 'military_tech', 'green-600', 2),
('entry_requirements', 'undergraduate', 'GBCE / ABCE', 'Full Diploma Certificate in ABCE or Credit Passes in GBCE.', '6 GBCE Credit Passes (incl. English & Math), 5 GBCE/SSSCE/WASSCE passes for ABCE', 'auto_stories', 'red-600', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('entry_requirements', 'special', 'Mature Students', '25 Years and Above\nPass Mature Entrance Exam', 'event_available', 'orange-600', 1),
('entry_requirements', 'special', 'Diploma Holders', 'Nursing / Midwifery Certificates\nRecognized Tertiary Diplomas', 'medical_services', 'cyan-600', 2),
('entry_requirements', 'special', 'Foreign Students', 'Baccalaureate / Foreign Diplomas\nGTEC Evaluation Required', 'public', 'indigo-600', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_link`, `display_order`) VALUES
('entry_requirements', 'resources', 'Student Handbook', 'Comprehensive guide to university life and regulations.', 'menu_book', 'uploads/Student Handbook.pdf', 1),
('entry_requirements', 'resources', 'FAQs', 'Find answers to common questions about admissions.', 'help', 'faqs_about_vvu.php', 2),
('entry_requirements', 'resources', 'Admission Materials', 'Download forms and other essential application materials.', 'description', 'admissions.php', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 3. CAUTION TO APPLICANTS PAGE
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('caution_to_applicants', 'Caution to Applicants', 'Important Notice', 'Caution to', 'Applicants', 'Protecting your future starts with a secure application process. Stay informed and avoid fraudulent admission agents.', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80', 'Apply Safely', 'Don\'t let fraudulent agents compromise your education. Start your official application today.', 'Apply Directly', 'apply.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('caution_to_applicants', 'warning', 'Official Warning', 'Valley View University DOES NOT use or encourage the use of agents in applying for admissions.', 1),
('caution_to_applicants', 'why_direct', 'Why Apply Directly?', 'Benefits of using our official application portal.', 2),
('caution_to_applicants', 'red_flags', 'Red Flags to Watch For', 'Stay vigilant against these common fraudulent tactics.', 3),
('caution_to_applicants', 'channels', 'Official Application Channels', 'Use only these verified methods to apply.', 4),
('caution_to_applicants', 'verify', 'Need to Verify?', 'Contact us directly if you are unsure about any communication.', 5)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('caution_to_applicants', 'why_direct', 'Direct Application', 'Applying directly through our official portal ensures that your data is handled securely and your application is processed without unnecessary delays.', 'verified_user', 'blue-600', 1),
('caution_to_applicants', 'why_direct', 'No Hidden Fees', 'The only official fees are those listed on our website. Never pay any individual or agent for "guaranteed admission".', 'payments', 'yellow-500', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `display_order`) VALUES
('caution_to_applicants', 'red_flags', 'Guaranteed Admission', 'Admission is strictly based on meeting academic requirements and official review.', 'red-500', 1),
('caution_to_applicants', 'red_flags', 'Personal Bank Accounts', 'Official payments are only made through designated university bank accounts or portals.', 'red-500', 2),
('caution_to_applicants', 'red_flags', 'Urgent Pressure', 'Agents often use false deadlines to pressure you into making quick, unverified payments.', 'red-500', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_link`, `display_order`) VALUES
('caution_to_applicants', 'channels', 'Online Portal', 'The only official way to apply online is through our secure portal.', 'language', 'apply.php', 1),
('caution_to_applicants', 'channels', 'On-Campus', 'Visit the Admissions Office at any of our campuses for direct assistance.', 'location_on', NULL, 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_stat_value`, `item_icon`, `display_order`) VALUES
('caution_to_applicants', 'verify', 'Call Us', '+233 307011832', 'call', 1),
('caution_to_applicants', 'verify', 'Email Us', 'admissions@vvu.edu.gh', 'mail', 2),
('caution_to_applicants', 'verify', 'Live Chat', 'Available on Website', 'chat', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 4. SCHOLARSHIPS PAGE
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('scholarships', 'Scholarships & Financial Aid', 'Financial Support', 'Scholarships', '& Awards', 'Empowering excellence through financial aid. We believe every talented mind deserves the opportunity to shine.', 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80', 'Your Future Awaits', 'Don\'t let financial barriers stand in the way of your education. Explore our scholarship opportunities today.', 'Apply Now', 'apply.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('scholarships', 'intro', 'Investing in You', 'Financial aid policies and processing.', 1),
('scholarships', 'categories', 'Award Categories', 'Different types of financial support available.', 2),
('scholarships', 'process', 'How it Works', 'Our transparent scholarship distribution process.', 3),
('scholarships', 'success', 'Success Stories', 'Hear from our scholarship recipients.', 4),
('scholarships', 'resources', 'Resources & Contact', 'Access forms and get assistance.', 5)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('scholarships', 'categories', 'Merit Scholarships', 'Awarded to students who demonstrate exceptional academic performance, leadership potential, and outstanding character.', 'workspace_premium', 'yellow-500', 1),
('scholarships', 'categories', 'Financial Grants', 'Need-based assistance designed to support students from diverse backgrounds who require financial aid.', 'volunteer_activism', 'blue-600', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_stat_value`, `display_order`) VALUES
('scholarships', 'process', 'Departmental Review', 'Applications are reviewed in consultation with Department Heads to ensure alignment with academic goals.', '1', 1),
('scholarships', 'process', 'Committee Processing', 'The Student Finance Services Committee evaluates candidates based on Academic Board policies.', '2', 2),
('scholarships', 'process', 'Fund Distribution', 'The committee makes final decisions regarding the distribution of funds to successful applicants.', '3', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_subtitle`, `display_order`) VALUES
('scholarships', 'success', 'Sarah Mensah', 'The scholarship I received at Valley View University didn\'t just pay for my tuition; it gave me the confidence to pursue my dreams.', 'Class of 2023, Computer Science', 1)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_link`, `display_order`) VALUES
('scholarships', 'resources', 'Application Forms', 'Download the latest scholarship and grant application materials.', 'description', 'scholarships-forms.php', 1),
('scholarships', 'resources', 'FAQs', 'Find answers to common questions about eligibility and deadlines.', 'help', 'faqs_about_vvu.php', 2),
('scholarships', 'resources', 'Need Help?', 'Contact the Student Finance Services Committee for assistance.\n+233 307011832', 'contact_support', NULL, 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 5. SCHOLARSHIP FORMS PAGE
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('scholarships_forms', 'Scholarship Forms', 'Application Center', 'Scholarship', 'Forms', 'Your journey to financial support begins here. Download the necessary forms and start your application today.', 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80', 'Start Your Future', 'Don\'t wait. Download your forms and take the first step towards your academic goals.', 'Scholarship Info', 'scholarships.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('scholarships_forms', 'intro', 'Official Forms', 'Please ensure you download the correct form for the scholarship you are applying for. Completed forms should be submitted to the Student Finance Services Committee.', 0),
('scholarships_forms', 'forms', 'Download Your Materials', 'Ensure you download the correct form.', 1),
('scholarships_forms', 'tips', 'Application Tips', 'Follow these guidelines to increase your chances of a successful application.', 2),
('scholarships_forms', 'submission', 'Submission Information', 'How and where to submit your completed forms.', 3),
('scholarships_forms', 'support', 'Need Assistance?', 'If you have any questions regarding the scholarship forms or the application process, our team is here to help.', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_link`, `item_color`, `display_order`) VALUES
('scholarships_forms', 'forms', 'Adventist Heritage Bursary', 'Application form for the Adventist Heritage Bursary supporting students with an Adventist background.', 'church', 'uploads/Scholarship-Forms/Adventist Heritage_Bursary Application Form .pdf', 'blue-600', 1),
('scholarships_forms', 'forms', 'Svanikier Ga-Dangme Scholarship', 'Supports talented students from the Ga-Dangme community. (2023)', 'school', 'uploads/Scholarship-Forms/Svanikier Ga-Dangme Scholarship Application Form_2023.pdf', 'yellow-500', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `display_order`) VALUES
('scholarships_forms', 'tips', 'Read Carefully', 'Ensure you understand all the requirements and eligibility criteria before filling out the form.', 'blue-600', 1),
('scholarships_forms', 'tips', 'Complete All Sections', 'Incomplete forms may not be processed. Double-check that every field is filled correctly.', 'blue-600', 2),
('scholarships_forms', 'tips', 'Attach Documents', 'Make sure all required supporting documents (transcripts, IDs, etc.) are attached.', 'blue-600', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `display_order`) VALUES
('scholarships_forms', 'submission', 'Email Submission', 'Scan and send your completed forms to admissions@vvu.edu.gh', 'mail', 1),
('scholarships_forms', 'submission', 'Physical Submission', 'Drop off your forms at the Admissions Office, Mile 19 Off the Adenta-Dodowa Road.', 'location_on', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `display_order`) VALUES
('scholarships_forms', 'support', 'Call Us', '+233 307011832', 'call', 1),
('scholarships_forms', 'support', 'Email Us', 'admissions@vvu.edu.gh', 'mail', 2),
('scholarships_forms', 'support', 'Live Chat', 'Available on Website', 'chat', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;
