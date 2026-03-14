-- Migration for More Resources Pages: mobile_money_fee_payment, policies, faculty_and_staff_forms, employment_opportunity, elearning_materials

-- 1. Insert Page Content (Hero & CTA)
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('mobile_money_payment', 'Mobile Money Fee Payment', 'Student Resources', 'Mobile Money', 'Fee Payment', '"Convenient, secure, and instant. Pay your university fees from the comfort of your home using our approved USSD short codes."', 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=1600', 'Need Assistance?', 'If you encounter any issues during the payment process or your payment hasn''t reflected after 24 hours, please reach out to our finance team.', 'finance@vvu.edu.gh', 'mailto:finance@vvu.edu.gh'),
('policies', 'University Policies', 'Governance & Standards', 'University', 'Policies', '"A comprehensive guide to the principles, regulations, and procedures that govern Valley View University. We ensure transparency and fairness in all our operations."', 'uploads/strategy/img_1770600004_69893644a6dec.jpg', 'Committed to', 'Integrity & Transparency', 'Our Mission', 'mission_and_vision.php'),
('faculty_staff_forms', 'Faculty and Staff Forms', 'Resources for Faculty & Staff', 'Official Forms', '& Documentation', '"Streamlining administrative processes for our dedicated faculty and staff members."', 'images/faculty_of_science_hero.png', 'Need Assistance?', 'If you have questions regarding any of these forms, please contact the Registry or Human Resources office.', 'hr@vvu.edu.gh', 'mailto:hr@vvu.edu.gh'),
('employment_opportunities', 'Employment Opportunities', 'Careers at VVU', 'Join Our', 'Academic Family', '"Be part of a community dedicated to excellence in education, research, and service. Discover your next professional milestone."', 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&q=80&w=1920', 'Start Your Journey With Us', 'Can''t find the right role? Send us your resume and we''ll keep you in mind for future opportunities.', 'Submit General Interest', 'contact_us.php'),
('elearning_materials', 'E-Learning Materials', 'Digital Resources', 'E-Learning', 'Materials & Resources', '"Access comprehensive guides, manuals, and video tutorials to maximize your online learning experience at Valley View University."', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80', 'Ready to Start', 'Learning Online?', 'Access E-Learning Portal', 'https://learning.vvu.edu.gh')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 2. Sections
INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
-- Mobile Money
('mobile_money_payment', 'networks', 'Supported Networks', 'Secure and instant payment via G-Money and all major mobile networks.', 1),
('mobile_money_payment', 'ussd_options', 'USSD Payment Options', 'Approved Methods', 2),
('mobile_money_payment', 'important_notes', 'Important Notes', 'Please read carefully before proceeding', 3),
('mobile_money_payment', 'why_used', 'Why Use Mobile Money?', 'Experience the most efficient way to handle your university finances.', 4),
-- Policies
('policies', 'framework', 'Policy Framework', 'Access our official documents and handbooks to understand the standards of our community.', 1),
('policies', 'quick_links', 'Find a Policy', 'Search our comprehensive database for specific regulations and guidelines.', 2),
-- Faculty & Staff Forms
('faculty_staff_forms', 'downloadable_forms', 'Downloadable Forms', 'Access the necessary documents for teaching, administration, and reporting.', 1),
('faculty_staff_forms', 'guidelines', 'Submission Guidelines', 'Please follow these steps to ensure timely processing of your forms.', 2),
-- Employment Opportunities
('employment_opportunities', 'featured_openings', 'Featured Openings', 'Join our dynamic team', 1),
('employment_opportunities', 'all_openings', 'All Openings', 'Discover your next career move', 2),
('employment_opportunities', 'why_join', 'Why Join Us?', 'Benefits and culture at VVU', 3),
('employment_opportunities', 'how_apply', 'How to Apply', 'Simple steps to join us', 4),
('employment_opportunities', 'hiring_process', 'Hiring Process', 'Transparent selection journey', 5),
-- E-Learning Materials
('elearning_materials', 'platform_guides', 'E-Learning Platform Guides', 'Learn the basics', 1),
('elearning_materials', 'email_activation', 'VVU Email Activation', 'Setting up your digital identity', 2),
('elearning_materials', 'video_tutorials', 'Video Tutorials', 'Watch and learn', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 3. Mobile Money Payment Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_image`, `item_color`, `display_order`) VALUES
('mobile_money_payment', 'networks', 'MTN MoMo', 'images/mtn_logo.png', 'yellow-400', 1),
('mobile_money_payment', 'networks', 'Telecel Cash', 'images/telecel_logo.png', 'blue-600', 2),
('mobile_money_payment', 'networks', 'AT Money', 'images/at_logo.png', 'blue-600', 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_description`, `item_icon`, `item_color`, `item_link`, `item_stat_value`, `display_order`) VALUES
('mobile_money_payment', 'ussd_options', 'Option 1: Mobile Money USSD', 'Mobile Money', '1. Dial *772*42#\n2. Select Pay Fees\n3. Choose University\n4. Select Valley View\n5. Enter Student ID\n6. Confirm & Amount\n7. Enter MoMo PIN', 'smartphone', 'yellow-400', '*772*42#', '*772*42#', 1),
('mobile_money_payment', 'ussd_options', 'Option 2: Mobile Money USSD', 'Multi-Network', '1. Dial *924*200#\n2. Select Pay Fees\n3. Choose University\n4. Select Valley View\n5. Enter Student ID\n6. Confirm & Amount\n7. Authorize MoMo PIN', 'dialpad', 'blue-600', '*924*200#', '*924*200#', 2),
('mobile_money_payment', 'ussd_options', 'Option 3: Mobile Money USSD', 'All Other Network', '1. Dial *800*50#\n2. Select Pay Fees\n3. Choose University\n4. Select Valley View\n5. Enter Student ID\n6. Confirm & Amount\n7. Enter MoMo PIN', 'payments', 'green-600', '*800*50#', '*800*50#', 3),
('mobile_money_payment', 'important_notes', 'Balance Check', null, 'Ensure you have sufficient balance in your Mobile Money wallet.', 'check_circle', 'blue-600', null, null, 1),
('mobile_money_payment', 'important_notes', 'Confirm Details', null, 'Always confirm student details before authorizing payment.', 'check_circle', 'blue-600', null, null, 2),
('mobile_money_payment', 'important_notes', 'Sms Receipt', null, 'Keep the SMS confirmation message as proof of payment.', 'check_circle', 'blue-600', null, null, 3),
('mobile_money_payment', 'important_notes', 'Processing Time', null, 'Allow some time for the payment to reflect in the school system.', 'check_circle', 'blue-600', null, null, 4),
('mobile_money_payment', 'why_used', 'Instant Reflection', null, 'Payments are processed in real-time and reflect on your student portal almost immediately.', 'bolt', 'blue-600', null, null, 1),
('mobile_money_payment', 'why_used', 'Highly Secure', null, 'Every transaction requires your personal MoMo PIN, ensuring your funds are always safe.', 'security', 'green-600', null, null, 2),
('mobile_money_payment', 'why_used', '24/7 Availability', null, 'Pay your fees at any time of the day or night, including weekends and public holidays.', 'schedule', 'yellow-500', null, null, 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 4. Policies Items (Categories with Documents in extra_data)
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_link`, `item_subtitle`, `extra_data`, `display_order`) VALUES
('policies', 'framework', 'Governance', 'Foundational documents that define the legal and operational structure of the university.', 'gavel', 'blue-600', null, null, '[{"title":"University Statutes","url":"uploads/Statutes.pdf","icon":"picture_as_pdf","color":"blue-600"},{"title":"VVU Bylaws","url":"uploads/VVU Bylaws.pdf","icon":"picture_as_pdf","color":"blue-600"}]', 1),
('policies', 'framework', 'Academic', 'Guidelines for academic standards, student conduct, and university life.', 'menu_book', 'yellow-500', null, null, '[{"title":"Academic Bulletin","url":"uploads/VVU-Academic-Bulletin-June-2020.pdf","icon":"picture_as_pdf","color":"yellow-600"}]', 2),
('policies', 'framework', 'Staff', 'Resources and contracts for faculty and staff members of the university.', 'badge', 'green-600', null, null, '[]', 3),
('policies', 'quick_links', 'Archives', 'Access historical policy documents and previous versions of university statutes.', 'description', 'blue-600', '#!', 'View Archives', null, 1),
('policies', 'quick_links', 'FAQs', 'Common questions regarding university regulations and policy implementation.', 'help', 'yellow-500', 'faqs_about_vvu.php', 'Read FAQs', null, 2),
('policies', 'quick_links', 'Support', 'Need clarification on a policy? Contact the Registrar''s office for assistance.', 'contact_support', 'green-600', 'contact_us.php', 'Contact Us', null, 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 5. Faculty & Staff Forms Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_link`, `display_order`) VALUES
('faculty_staff_forms', 'downloadable_forms', 'Employee Weekly Tasks', 'Form for recording and reporting weekly tasks and accomplishments.', 'task', 'blue-600', 'uploads/Faculty-and-Staff-Forms/Employee Weekly Tasks Form Autosaved.pdf', 1),
('faculty_staff_forms', 'downloadable_forms', 'Faculty Workload (1)', 'Standard form for faculty workload assignment and tracking.', 'assignment_ind', 'purple-600', 'uploads/Faculty-and-Staff-Forms/Faculty Workload Assignment Form - 1.pdf', 2),
('faculty_staff_forms', 'downloadable_forms', 'Faculty Workload (2)', 'Supplementary form for faculty workload documentation.', 'assignment_turned_in', 'purple-600', 'uploads/Faculty-and-Staff-Forms/Faculty Workload Assignment Form - 2.pdf', 3),
('faculty_staff_forms', 'downloadable_forms', 'Non-Teaching Assignment', 'Assignment form for non-teaching and administrative staff.', 'badge', 'green-600', 'uploads/Faculty-and-Staff-Forms/Non-Teaching Staff Assignment Form.pdf', 4),
('faculty_staff_forms', 'downloadable_forms', 'Registry Weekly Tasks', 'Weekly task reporting form specifically for Registry staff.', 'description', 'yellow-500', 'uploads/Faculty-and-Staff-Forms/Registry-Staff-Weekly-Tasks-Form.docx', 5),
('faculty_staff_forms', 'downloadable_forms', 'Taxpayer Registration', 'Individual taxpayer registration form for staff members.', 'account_balance', 'red-600', 'uploads/Faculty-and-Staff-Forms/taxpayer_registration_form_individual.pdf', 6),
('faculty_staff_forms', 'guidelines', 'Complete Digitally', 'Fill out the forms electronically whenever possible to ensure clarity and accuracy.', 'edit_note', 'blue-600', null, 1),
('faculty_staff_forms', 'guidelines', 'Obtain Signatures', 'Ensure all required departmental signatures are obtained before final submission.', 'verified', 'green-600', null, 2),
('faculty_staff_forms', 'guidelines', 'Submit to Registry', 'Submit completed and signed forms to the Registry or HR office as specified.', 'mail', 'yellow-500', null, 3),
('faculty_staff_forms', 'guidelines', 'Keep a Copy', 'Always retain a copy of your submitted forms for your personal records.', 'history', 'purple-600', null, 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 6. Employment Opportunities Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_description`, `item_icon`, `item_color`, `item_link`, `display_order`) VALUES
('employment_opportunities', 'featured_openings', 'Assistant Professor of Computer Science', 'Department of Engineering and Computer Science', 'Seeking a dynamic educator and researcher to join our growing computer science program, focusing on AI and machine learning.', 'star', 'blue-600', '#', 1),
('employment_opportunities', 'featured_openings', 'Admissions Counselor', 'Office of Admissions', 'Join our team to recruit and guide prospective students through the admissions process for our undergraduate programs.', 'star', 'blue-600', '#', 2),
('employment_opportunities', 'all_openings', 'Librarian for Digital Scholarship', 'University Libraries', 'Support faculty and student research by managing digital collections and promoting scholarly communication initiatives.', 'work', 'blue-600', '#', 1),
('employment_opportunities', 'all_openings', 'Campus Facilities Manager', 'Physical Plant', 'Oversee the maintenance, operations, and safety of university buildings and grounds.', 'work', 'blue-600', '#', 2),
('employment_opportunities', 'why_join', 'Flexible Health', null, 'Comprehensive health and retirement benefits.', 'favorite', 'yellow-400', null, 1),
('employment_opportunities', 'why_join', 'Tuition Remission', null, 'Generous tuition remission programs.', 'school', 'blue-600', null, 2),
('employment_opportunities', 'how_apply', 'Simple Portal', null, 'Applying is easy! Click \"View & Apply\" on any listing to see full details and submit your application through our online portal.', 'download', 'blue-900', 'uploads/employment-forma.pdf', 1),
('employment_opportunities', 'hiring_process', 'Application', null, 'Submit your CV and required documents.', 'person', 'blue-600', null, 1),
('employment_opportunities', 'hiring_process', 'Screening', null, 'Our HR team reviews your qualifications.', 'manage_search', 'blue-600', null, 2),
('employment_opportunities', 'hiring_process', 'Interview', null, 'Meet with the department heads and team.', 'groups', 'blue-600', null, 3)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 7. E-Learning Materials Items (Guides & Email Activation)
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_link`, `display_order`) VALUES
('elearning_materials', 'platform_guides', 'Lecturer''s Manual', 'Comprehensive guide for lecturers on how to use the VVU E-Learning platform effectively.', 'menu_book', 'blue-600', 'uploads/Email Materials/vvu-e-learning-manunal-for-lecturers-1-1.pdf', 1),
('elearning_materials', 'platform_guides', 'Student Technical Manual', 'Complete technical training manual for students.', 'person', 'green-600', 'uploads/Email Materials/student-manual-min-compressed.pdf', 2),
('elearning_materials', 'platform_guides', 'Join E-Learning', 'Step-by-step guide on the new procedure to join the VVU E-Learning platform.', 'login', 'purple-600', 'uploads/Email Materials/new-procedure-to-join-e-learning.pdf', 3),
('elearning_materials', 'platform_guides', 'Mobile App Access', 'Learn how to access the E-Learning platform on your mobile device.', 'smartphone', 'orange-600', 'uploads/Email Materials/learning.vvu.edu.gh-mobile-app-access-to-elearning-compressed.pdf', 4),
('elearning_materials', 'platform_guides', 'Unenroll from Courses', 'Guide on how to unenroll from unwanted courses on the E-Learning platform.', 'logout', 'cyan-600', 'uploads/Email Materials/how-to-unenroll-from-unwanted-course-on-the-e-learning-final.pdf', 5),
('elearning_materials', 'platform_guides', 'BigBlueButton Guide', 'Everything you need to know about BigBlueButton (BBB).', 'video_call', 'blue-700', 'uploads/Email Materials/big-blue-button-min.pdf', 6),
('elearning_materials', 'email_activation', 'Activate VVU Email', 'Step-by-step process to activate your official VVU email address from a computer.', 'computer', 'orange-600', 'uploads/Email Materials/activation-of-vvu-email-final-compressed.pdf', 1),
('elearning_materials', 'email_activation', 'Mobile Email Activation', 'Activate your VVU email address using your mobile phone.', 'phone_android', 'green-600', 'uploads/Email Materials/mobile-phone-activation-of-vvu-email--compressed.pdf', 2),
-- Video Tutorials (using item_link for video source and item_image for poster)
('elearning_materials', 'video_tutorials', 'Platform Overview', 'A comprehensive overview of the VVU E-Learning platform.', 'play_circle', 'blue-600', 'uploads/Email Materials/OVERVIEW.mp4.mp4', 1),
('elearning_materials', 'video_tutorials', 'Activity Module', 'Learn how to use the Activity Module to submit assignments.', 'play_circle', 'green-600', 'uploads/Email Materials/ACTIVITY MODULE.mp4.mp4', 2),
('elearning_materials', 'video_tutorials', 'Announcement Module', 'Stay updated with course announcements.', 'play_circle', 'purple-600', 'uploads/Email Materials/ANNOUNCEMENT MODULE.mp4.mp4', 3),
('elearning_materials', 'video_tutorials', 'BigBlueButton Tutorial', 'Master the BigBlueButton virtual classroom.', 'play_circle', 'orange-500', 'uploads/Email Materials/BIG BLUE BUTTON.mp4.mp4', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- 8. E-Learning Stats
INSERT INTO `academic_pages_stats` (`page_key`, `stat_value`, `stat_label`, `stat_icon`, `display_order`) VALUES
('elearning_materials', '8+', 'PDF Guides', 'description', 1),
('elearning_materials', '4+', 'Video Tutorials', 'play_circle', 2),
('elearning_materials', '24/7', 'Access', 'history', 3),
('elearning_materials', '100%', 'Free', 'verified', 4)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;
