-- ===========================================================================
-- ISIMS (Integrated School Information Management System) page
--
-- Adds the CMS content for isims.php and a navigation link to it.
-- The page uses the shared academic_pages_* tables, so it is edited in
-- Admin -> Departmental Resources -> ISIMS Portal.
--
-- Safe to import more than once: every statement is an upsert or is guarded
-- by NOT EXISTS, so re-importing refreshes the seed rows without duplicates.
--
-- The two PDF guides it links to must exist on the server at:
--   uploads/iSIMS/Valley_View_University_ISIMS_Student_User_Guide.pdf
--   uploads/iSIMS/ISIMS_Login_Troubleshooting_Guide.pdf
-- ===========================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- 1. Hero / global page content
-- ---------------------------------------------------------------------------
INSERT INTO `academic_pages_content`
    (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`,
     `hero_image`, `meta_title`, `meta_description`, `cta_title`, `cta_subtitle`,
     `cta_button_text`, `cta_button_link`, `cta_button_text_2`, `cta_button_link_2`,
     `help_title`, `help_description`, `help_phone`, `is_active`)
VALUES
    ('isims', 'ISIMS', 'New Student Portal', 'ISIMS',
     'Integrated School Information Management System',
     '"One secure portal for course registration, account status, hostel accommodation and cafeteria feeding — available to every VVU student, anywhere."',
     'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1920',
     'ISIMS Student Portal - Valley View University',
     'Log in to ISIMS, the Valley View University Integrated School Information Management System, for course registration, account status, hostel applications and cafeteria feeding. Download the official student user guide and login troubleshooting guide.',
     'Ready to Get Started?',
     'Log in with your Student ID and password. First time here? Click "Forgot Password" on the login page to create your password.',
     'Log In to ISIMS', 'https://isims.vvu.edu.gh',
     'Student User Guide', 'uploads/iSIMS/Valley_View_University_ISIMS_Student_User_Guide.pdf',
     'Password Still Not Working?',
     'If the Forgot Password option cannot resolve your issue, or the reset email never arrives, contact the Admissions and Records Office.',
     '', 1)
ON DUPLICATE KEY UPDATE
    `page_title` = VALUES(`page_title`), `hero_badge` = VALUES(`hero_badge`),
    `hero_title` = VALUES(`hero_title`), `hero_subtitle` = VALUES(`hero_subtitle`),
    `hero_description` = VALUES(`hero_description`), `hero_image` = VALUES(`hero_image`),
    `meta_title` = VALUES(`meta_title`), `meta_description` = VALUES(`meta_description`),
    `cta_title` = VALUES(`cta_title`), `cta_subtitle` = VALUES(`cta_subtitle`),
    `cta_button_text` = VALUES(`cta_button_text`), `cta_button_link` = VALUES(`cta_button_link`),
    `cta_button_text_2` = VALUES(`cta_button_text_2`), `cta_button_link_2` = VALUES(`cta_button_link_2`),
    `help_title` = VALUES(`help_title`), `help_description` = VALUES(`help_description`),
    `help_phone` = VALUES(`help_phone`), `is_active` = 1;

-- ---------------------------------------------------------------------------
-- 2. Section headings
-- ---------------------------------------------------------------------------
INSERT INTO `academic_pages_sections`
    (`page_key`, `section_key`, `section_title`, `section_subtitle`, `section_description`, `display_order`, `is_active`)
VALUES
    ('isims', 'services',     'Everything In One Portal',      'What you can do',          'ISIMS brings the university''s key academic and administrative student services together in one place.', 1, 1),
    ('isims', 'login',        'Logging In For The First Time', 'Fresh students start here', 'Fresh students and anyone who has forgotten their password create their password through "Forgot Password".', 2, 1),
    ('isims', 'registration', 'Registering For Courses',       'Step by step',             'Register for your semester courses from the Course Registration section of ISIMS.', 3, 1),
    ('isims', 'blocked',      'Why Is A Course Blocked?',      'Understanding your list',  'Some courses may appear as Blocked on your registration list. Here is what each reason means.', 4, 1),
    ('isims', 'hostel',       'Hostel Accommodation',          'Apply for a room',         'Submitting an application does not mean a room has been allocated. Check ISIMS regularly for your allocation status.', 5, 1),
    ('isims', 'cafeteria',    'Cafeteria Feeding',             'Choose your meals',        'Select your preferred cafeteria meal option for the semester directly in ISIMS.', 6, 1),
    ('isims', 'troubleshoot', 'Seeing "User Not Found"?',      'Login troubleshooting',    'Some students receive a "User not found" error when they log in. Follow these steps to resolve it.', 7, 1),
    ('isims', 'guides',       'Official ISIMS Guides',         'Download & read',          'Read online or download the official guides prepared by VVU ITS.', 8, 1),
    ('isims', 'notes',        'Before You Click Submit',       'Important student notes',  'A few habits that keep your account safe and your transactions error-free.', 9, 1),
    ('isims', 'support',      'Getting Help',                  'Support channels',         'First confirm your Student ID, password, internet connection and selected semester are correct.', 10, 1)
ON DUPLICATE KEY UPDATE
    `section_title` = VALUES(`section_title`), `section_subtitle` = VALUES(`section_subtitle`),
    `section_description` = VALUES(`section_description`), `display_order` = VALUES(`display_order`),
    `is_active` = 1;

-- ---------------------------------------------------------------------------
-- 3. Section items
-- ---------------------------------------------------------------------------
INSERT INTO `academic_pages_items`
    (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_description`,
     `item_icon`, `item_color`, `item_link`, `item_stat_value`, `display_order`, `is_active`)
VALUES
    -- services
    ('isims', 'services', 'Course Registration', '', 'Choose your level, active semester and group, review the available courses and billing, then submit your registration online.', 'how_to_reg', 'blue-600', '', '', 1, 1),
    ('isims', 'services', 'Account Status', '', 'See your account balance and registration status on your dashboard the moment you log in.', 'account_balance_wallet', 'green-600', '', '', 2, 1),
    ('isims', 'services', 'Hostel Accommodation', '', 'Apply for a campus room, compare room types and their costs, and track your allocation status.', 'apartment', 'purple-600', '', '', 3, 1),
    ('isims', 'services', 'Cafeteria Feeding', '', 'Pick your preferred meal option and confirm your selection in just a few clicks.', 'restaurant', 'orange-600', '', '', 4, 1),

    -- login
    ('isims', 'login', 'Open The Portal', 'Step 1', 'Visit isims.vvu.edu.gh in Google Chrome, Microsoft Edge or Mozilla Firefox.', 'language', 'blue-600', 'https://isims.vvu.edu.gh', '1', 1, 1),
    ('isims', 'login', 'Click Forgot Password', 'Step 2', 'Fresh students, or anyone who has forgotten their password, click "Forgot Password" on the login page.', 'key', 'purple-600', '', '2', 2, 1),
    ('isims', 'login', 'Enter Your Student ID', 'Step 3', 'Type your Student ID number and click "Send Password Reset Link".', 'badge', 'green-600', '', '3', 3, 1),
    ('isims', 'login', 'Check Your Email', 'Step 4', 'Open the reset email sent to your registered address and create a new password. Check Spam/Junk too.', 'mark_email_read', 'orange-600', '', '4', 4, 1),
    ('isims', 'login', 'Log In', 'Step 5', 'Return to the login page and sign in with your Student ID and new password.', 'login', 'yellow-500', '', '5', 5, 1),

    -- registration
    ('isims', 'registration', 'Check Your Balance', 'Before you begin', 'Your dashboard shows your account balance. It must meet the minimum required balance, or you will not be able to complete registration.', 'account_balance_wallet', 'green-600', '', '1', 1, 1),
    ('isims', 'registration', 'Open Course Registration', 'Left-hand sidebar', 'From the left-hand sidebar, click "Course Registration".', 'menu_open', 'blue-600', '', '2', 2, 1),
    ('isims', 'registration', 'Select Your Details', 'Level, semester & group', 'Choose your Level, Active Semester and Group Type, then click "Continue". Select your Group Type as instructed by your Department Head.', 'tune', 'purple-600', '', '3', 3, 1),
    ('isims', 'registration', 'Review Courses & Billing', 'Check carefully', 'Review the courses available to you, then scroll down to check the billing breakdown before you submit.', 'receipt_long', 'orange-600', '', '4', 4, 1),
    ('isims', 'registration', 'Submit Registration', 'Final step', 'Click "Submit Registration" and wait. Do not refresh or close the browser while it processes, then confirm your Registration Status has updated.', 'task_alt', 'yellow-500', '', '5', 5, 1),

    -- blocked
    ('isims', 'blocked', 'Already Passed', '', 'You have previously passed this course, so you do not need to register for it again.', 'verified', 'green-600', '', '', 1, 1),
    ('isims', 'blocked', 'Already In Progress', '', 'You registered for this course before, but it has not yet been graded or completed.', 'hourglass_top', 'yellow-500', '', '', 2, 1),
    ('isims', 'blocked', 'Prerequisite Not Met', '', 'You have not yet completed the prerequisite course(s) required for this course.', 'lock', 'red-600', '', '', 3, 1),

    -- hostel
    ('isims', 'hostel', 'Open Hostel Application', '', 'From the left-hand sidebar, click "Hostel Application".', 'apartment', 'purple-600', '', '1', 1, 1),
    ('isims', 'hostel', 'Start Your Application', '', 'When the hostel application portal opens, click "Apply for Hostel".', 'add_home', 'purple-600', '', '2', 2, 1),
    ('isims', 'hostel', 'Select Your Preferred Room', '', 'Choose a room type from the list. Its cost is displayed automatically.', 'bed', 'purple-600', '', '3', 3, 1),
    ('isims', 'hostel', 'Submit Your Application', '', 'Review your selection carefully, then click "Apply".', 'send', 'purple-600', '', '4', 4, 1),
    ('isims', 'hostel', 'Await Allocation', '', 'The Hall Administrator processes your application. Once a room is allocated, it appears in ISIMS and your status changes to Approved.', 'verified', 'purple-600', '', '5', 5, 1),

    -- cafeteria
    ('isims', 'cafeteria', 'Open The Cafeteria Section', '', 'Navigate to the cafeteria/feeding section and click "Proceed to Confirmation".', 'restaurant', 'orange-600', '', '1', 1, 1),
    ('isims', 'cafeteria', 'Select Your Meal Option', '', 'Choose your preferred meal option from the available choices and review it carefully.', 'restaurant_menu', 'orange-600', '', '2', 2, 1),
    ('isims', 'cafeteria', 'Confirm Your Selection', '', 'Click "Proceed". The system processes your selection. Keep a screenshot of the confirmation.', 'check_circle', 'orange-600', '', '3', 3, 1),

    -- troubleshoot
    ('isims', 'troubleshoot', 'Try Logging In', '', 'Open the ISIMS login page and attempt to log in with your Student ID and password.', 'login', 'blue-600', '', '1', 1, 1),
    ('isims', 'troubleshoot', 'Click "Forgot Password"', '', 'If you see "User not found", click "Forgot Password" below the login section.', 'key', 'blue-600', '', '2', 2, 1),
    ('isims', 'troubleshoot', 'Enter Your Student ID & Submit', '', 'Enter your Student ID on the password recovery page and click the Submit/Reset Password button.', 'badge', 'blue-600', '', '3', 3, 1),
    ('isims', 'troubleshoot', 'Check Your Email', '', 'A reset link is sent to the email registered with your ISIMS account. Check your Spam/Junk folder as well.', 'mark_email_read', 'blue-600', '', '4', 4, 1),
    ('isims', 'troubleshoot', 'Create A New Password', '', 'Click the reset link in the email, enter and confirm your new password, then submit.', 'password', 'blue-600', '', '5', 5, 1),
    ('isims', 'troubleshoot', 'Log In Again', '', 'Return to the ISIMS login page and sign in with your Student ID and new password.', 'task_alt', 'blue-600', '', '6', 6, 1),

    -- guides (the PDFs)
    ('isims', 'guides', 'ISIMS Student User Guide', 'PDF · 16 pages · 1.4 MB', 'The complete walkthrough: logging in, your dashboard, course registration, hostel applications, cafeteria feeding and getting help.', 'menu_book', 'blue-600', 'uploads/iSIMS/Valley_View_University_ISIMS_Student_User_Guide.pdf', 'Download Guide', 1, 1),
    ('isims', 'guides', 'ISIMS Login Troubleshooting Guide', 'PDF · 7 pages · 760 KB', 'How to resolve the "User not found" error and reset your password, with screenshots for every step.', 'build_circle', 'orange-600', 'uploads/iSIMS/ISIMS_Login_Troubleshooting_Guide.pdf', 'Download Guide', 2, 1),

    -- notes
    ('isims', 'notes', 'Protect Your Login Details', '', 'Never share your Student ID, password, password reset link or email credentials with anyone.', 'shield', 'red-600', '', '', 1, 1),
    ('isims', 'notes', 'Verify Before Submitting', '', 'Check your semester, level, courses, group, billing, hostel room and meal selection before you submit.', 'fact_check', 'blue-600', '', '', 2, 1),
    ('isims', 'notes', 'Keep Your Records', '', 'After any important transaction, keep a screenshot or record of the confirmation for your reference.', 'photo_camera', 'green-600', '', '', 3, 1),
    ('isims', 'notes', 'Use A Stable Connection', '', 'Do not close or refresh the browser while a registration, hostel or cafeteria transaction is processing.', 'wifi', 'purple-600', '', '', 4, 1),

    -- support
    ('isims', 'support', 'ISIMS Login Support Form', 'Still locked out?', 'Followed every step and still cannot log in? Submit the form with your correct Student ID and clear details of the problem.', 'support_agent', 'yellow-500', 'https://docs.google.com/forms/d/e/1FAIpQLSdZl2DQTuI-ZKpiJTt1NyUS0h8pjfS-K6z7qACE8QvDfFKW8Q/viewform?usp=dialog', 'Open Support Form', 1, 1),
    ('isims', 'support', 'Admissions & Records Office', 'Password & account issues', 'For password problems the Forgot Password option cannot fix, or other ISIMS account issues.', 'contact_mail', 'blue-600', 'contact_us.php', 'Contact Us', 2, 1)
ON DUPLICATE KEY UPDATE
    `item_subtitle` = VALUES(`item_subtitle`), `item_description` = VALUES(`item_description`),
    `item_icon` = VALUES(`item_icon`), `item_color` = VALUES(`item_color`),
    `item_link` = VALUES(`item_link`), `item_stat_value` = VALUES(`item_stat_value`),
    `display_order` = VALUES(`display_order`), `is_active` = 1;

-- ---------------------------------------------------------------------------
-- 4. Hero stat cards (no unique key on this table, so each is guarded)
-- ---------------------------------------------------------------------------
INSERT INTO `academic_pages_stats` (`page_key`, `stat_value`, `stat_label`, `stat_icon`, `display_order`, `is_active`)
SELECT * FROM (
    SELECT 'isims' AS pk, 'Student ID' AS v, 'Your Username' AS l, 'badge' AS i, 1 AS o, 1 AS a UNION ALL
    SELECT 'isims', '24/7',       'Online Access',  'schedule',  2, 1 UNION ALL
    SELECT 'isims', '4',          'Core Services',  'apps',      3, 1 UNION ALL
    SELECT 'isims', 'Any Device', 'Browser Based',  'devices',   4, 1
) AS seed
WHERE NOT EXISTS (
    SELECT 1 FROM `academic_pages_stats` s WHERE s.page_key = seed.pk AND s.stat_label = seed.l
);

-- ---------------------------------------------------------------------------
-- 5. Navigation: RESOURCES -> "Current Students" (appended to the list)
--
-- Added only if the link is not already there, so this can be re-run.
-- Remove this block if you would rather add the link by hand in
-- Admin -> Navigation Manager.
-- ---------------------------------------------------------------------------
INSERT INTO `navigation_links` (`section_id`, `title`, `url`, `target`, `sort_order`, `is_active`)
SELECT s.id,
       'ISIMS Portal',
       'isims.php',
       '_self',
       COALESCE((SELECT MAX(l2.sort_order) FROM navigation_links l2 WHERE l2.section_id = s.id), 0) + 1,
       1
  FROM `navigation_sections` s
  JOIN `navigation_items` n ON n.id = s.navigation_item_id
 WHERE n.menu_type = 'main'
   AND n.title = 'RESOURCES'
   AND s.section_title = 'Current Students'
   AND NOT EXISTS (
        SELECT 1 FROM `navigation_links` l
         WHERE l.section_id = s.id AND l.url = 'isims.php'
   )
 LIMIT 1;
