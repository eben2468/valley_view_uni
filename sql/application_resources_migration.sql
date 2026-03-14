-- ============================================
-- Application & Resources Pages Content
-- Migration for: fees-structure.php, why_choose_vvu.php, 
--                download-forms.php, mature-entrance.php,
--                degree_and_diploma_in_music.php
-- ============================================

-- Main Pages Content
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('fees_structure', 'Fee Structure', 'Financial Planning', 'Fee', 'Structure', 'Transparent and affordable education. Plan your academic journey with clarity and confidence.', 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80', 'Secure Your Education', 'Take the next step in your academic journey. Review the fee structure and prepare for a bright future.', 'Apply Now', 'apply.php'),

('why_choose_vvu', 'Why Choose VVU', 'Excellence Without Compromise', 'Why Choose', 'Valley View?', '"Your choice of a university is your opportunity to demonstrate your genuine interest in and passion for the course you wish to pursue."', 'Education-Website-and-AdminPanel/images/pro-bg.jpg', 'Start Your Journey Today', 'Join the thousands of successful graduates who chose Valley View University for their future.', 'Apply Now', 'apply.php'),

('download_forms', 'Download Forms', 'Resources & Downloads', 'Download', 'Official Forms', 'Access all necessary application forms, requirements, and information guides in one place.', 'Education-Website-and-AdminPanel/images/pro-bg.jpg', 'Ready to Join Us?', 'Take the first step towards a brighter future. Apply online today for a faster and more convenient process.', 'Apply Online Now', 'https://admissions.vvu.edu.gh/'),

('mature_entrance', 'Mature Entrance', 'Admissions Open', 'Mature Entrance', 'Examination', '"Welcome to the Mature Entrance Exams Centre. Your gateway to higher education regardless of your educational background."', 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', 'It''s Never Too Late To Pursue Your Dreams', 'Start your journey towards a university degree today through our Mature Entrance Programme.', 'Apply Online', 'apply.php'),

('music_programs', 'Degree & Diploma in Music', 'Music Education', 'Degree & Diploma', 'In Music', '"Transform your passion for music into a rewarding career. Learn to teach, perform, and lead in the world of music education."', 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80', 'Start Your Musical Journey Today', 'Transform your passion for music into a fulfilling career. Apply now and join our community of aspiring musicians and educators.', 'Apply Now', 'apply.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP, `page_title` = VALUES(`page_title`), `hero_badge` = VALUES(`hero_badge`), `hero_title` = VALUES(`hero_title`), `hero_subtitle` = VALUES(`hero_subtitle`), `hero_description` = VALUES(`hero_description`), `hero_image` = VALUES(`hero_image`), `cta_title` = VALUES(`cta_title`), `cta_subtitle` = VALUES(`cta_subtitle`), `cta_button_text` = VALUES(`cta_button_text`), `cta_button_link` = VALUES(`cta_button_link`);

-- Sections
INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `section_description`, `display_order`) VALUES
-- Fees Structure
('fees_structure', 'tuition_fees', 'Tuition & Fees', 'Investing in Your Future', 'Valley View University is committed to providing high-quality education at a competitive cost. Below you will find the official fee schedules for the upcoming academic year and summer sessions.', 1),
('fees_structure', 'payment_methods', 'Payment Methods', 'Convenient Ways to Pay', 'We offer several convenient ways to settle your university fees.', 2),
('fees_structure', 'financial_policies', 'Financial Policies', NULL, NULL, 3),
('fees_structure', 'billing_inquiries', 'Billing Inquiries?', NULL, 'Our finance office is available to assist you with any questions regarding your student account or fee payments.', 4),

-- Why Choose VVU
('why_choose_vvu', 'mission', 'Our Mission', 'Motivating Potential, Building Ability', 'At Valley View University, we motivate, build the potential and ability in each person for further study, by providing you with not only academic opportunities, but with a challenging learning environment that will make you suitable for industry.', 1),
('why_choose_vvu', 'achievements', 'Our Achievements', 'A legacy of firsts and a commitment to global excellence in education and innovation.', NULL, 2),
('why_choose_vvu', 'eco_friendly', 'Sustainable Future', 'Africa''s Only Eco-Friendly University', 'We lead by example in environmental stewardship, utilizing innovative technologies to power our campus and protect our planet.', 3),
('why_choose_vvu', 'facilities', 'Comprehensive Facilities', 'We provide a complete ecosystem for living, learning, and growing.', NULL, 4),
('why_choose_vvu', 'student_life', 'A Vibrant Community', NULL, 'Life at Valley View is more than just academics. It''s about building lifelong friendships, exploring your passions, and growing in a supportive, values-driven environment.', 5),

-- Download Forms
('download_forms', 'undergrad', 'Undergraduate Admissions', 'Available Documents', '4 forms available', 1),
('download_forms', 'postgrad', 'Postgraduate Admissions', 'Available Documents', '3 forms available', 2),
('download_forms', 'nursing_special', 'Nursing & Special Programs', 'Available Documents', '4 forms available', 3),
('download_forms', 'international', 'International Admissions (Français)', 'Available Documents', '4 forms available', 4),
('download_forms', 'others', 'Other Forms & Research', 'Available Documents', '4 forms available', 5),

-- Mature Entrance
('mature_entrance', 'intro', 'What is the Mature Entrance?', NULL, 'The Mature Entrance Examination is designed for individuals who are 25 years of age or older and do not have the traditional academic qualifications required for university admission.', 1),
('mature_entrance', 'why_choose', 'Why Choose VVU Mature Entrance?', NULL, NULL, 2),
('mature_entrance', 'programs', 'Available Programs', NULL, 'Choose from our wide range of accredited degree programs available for mature students.', 3),
('mature_entrance', 'sessions', 'Enrollment Sessions', NULL, 'Choose a session that fits your schedule. We offer flexible class options throughout the year.', 4),
('mature_entrance', 'how_to_apply', 'How to Apply', NULL, 'Follow these simple steps to begin your journey towards higher education.', 5),
('mature_entrance', 'form_download', 'Download Application Form', NULL, 'Get started with your application today. Download the official Mature Entrance Examination application form and take the first step towards your degree.', 6),
('mature_entrance', 'contact', 'Need More Information?', NULL, 'Contact our admissions team for any questions about the Mature Entrance programme.', 7),

-- Music Programs
('music_programs', 'about', 'About the Program', NULL, 'Our Music Education programs are designed to equip students with the skills and competencies needed to excel in teaching music, dance, and drama at all educational levels.', 1),
('music_programs', 'benefits', 'Why You Should Enroll', NULL, 'Discover the benefits of studying music education at Valley View University.', 2),
('music_programs', 'programs', 'Programs Offered', NULL, 'Choose from our comprehensive music education programs designed to prepare you for a successful career.', 3),
('music_programs', 'careers', 'Career Opportunities', NULL, 'A degree or diploma in music opens doors to exciting career paths.', 4),
('music_programs', 'why_vvu', 'Why Choose VVU?', NULL, 'Valley View University offers a unique educational experience.', 5)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP, `section_title` = VALUES(`section_title`), `section_subtitle` = VALUES(`section_subtitle`), `section_description` = VALUES(`section_description`);

-- Items
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_description`, `item_icon`, `item_color`, `item_image`, `item_link`, `item_stat_value`, `display_order`) VALUES
-- Fees Structure
('fees_structure', 'tuition_fees', '2025-2026 Academic Year', NULL, 'Comprehensive fee schedule for the 2025-2026 academic year, covering tuition, registration, and other institutional charges.', 'calendar_today', 'blue-600', NULL, 'uploads/Fees Structure/2025-26 Valley View University Fees Schedule 1.pdf', 'PDF', 1),
('fees_structure', 'tuition_fees', 'Summer Session 2025', NULL, 'Detailed fee structure for the 2025 summer session, including per-credit rates and specialized program costs.', 'wb_sunny', 'yellow-500', NULL, 'uploads/Fees Structure/Summer Fees Schedule 2025.pdf', 'PDF', 2),

('fees_structure', 'payment_methods', 'Bank Deposit', NULL, 'Payments can be made at any branch of our partner banks. Please use your student ID as the reference.', 'account_balance', 'blue-600', NULL, NULL, NULL, 1),
('fees_structure', 'payment_methods', 'Online Portal', NULL, 'Securely pay your fees online using your credit or debit card through the student portal.', 'credit_card', 'blue-600', NULL, NULL, NULL, 2),
('fees_structure', 'payment_methods', 'Mobile Money', NULL, 'Conveniently pay via MTN Mobile Money or other supported mobile wallets.', 'smartphone', 'blue-600', NULL, NULL, NULL, 3),

('fees_structure', 'financial_policies', 'Payment Deadlines', NULL, 'Fees must be paid in full or according to an approved payment plan before the registration deadline each semester.', 'event_busy', 'blue-900', NULL, NULL, NULL, 1),
('fees_structure', 'financial_policies', 'Refund Policy', NULL, 'Refunds for withdrawals are processed based on the university''s official refund schedule found in the student handbook.', 'assignment_return', 'blue-900', NULL, NULL, NULL, 2),

('fees_structure', 'billing_inquiries', 'Call Finance', '+233 307011832', NULL, 'call', 'blue-600', NULL, NULL, NULL, 1),
('fees_structure', 'billing_inquiries', 'Email Billing', 'finance@vvu.edu.gh', NULL, 'mail', 'blue-600', NULL, NULL, NULL, 2),
('fees_structure', 'billing_inquiries', 'Visit Us', 'Finance Office, Main Campus', NULL, 'location_on', 'blue-600', NULL, NULL, NULL, 3),

-- Why Choose VVU
('why_choose_vvu', 'mission', 'Chartered Status', NULL, 'The First Chartered Private University in Ghana.', 'verified', 'yellow-400', NULL, NULL, NULL, 1),
('why_choose_vvu', 'mission', 'High Demand', NULL, 'Top-tier demand for our CS, IT, and Nursing graduates.', 'trending_up', 'blue-600', NULL, NULL, NULL, 2),

('why_choose_vvu', 'achievements', 'Global Quality Award', NULL, 'Awarded Gold for Leadership and Commitment to Quality by BID, Geneva (2013).', 'military_tech', 'blue-600', NULL, NULL, NULL, 1),
('why_choose_vvu', 'achievements', 'Cyber Security Pioneer', NULL, 'First University in Ghana to train the Ghana Police in Computer Security and Cyber Forensics.', 'shield', 'yellow-500', NULL, NULL, NULL, 2),
('why_choose_vvu', 'achievements', 'Tech Innovation', NULL, 'First to develop and use its own School and Hospital Management Systems.', 'terminal', 'purple-600', NULL, NULL, NULL, 3),
('why_choose_vvu', 'achievements', 'Global Competitions', NULL, 'Winners of Zain Africa Challenge and Microsoft Imagine Cup representatives.', 'rocket_launch', 'green-600', NULL, NULL, NULL, 4),
('why_choose_vvu', 'achievements', 'Research Excellence', NULL, 'The Private University with the highest number of academic publications in Ghana.', 'menu_book', 'red-600', NULL, NULL, NULL, 5),
('why_choose_vvu', 'achievements', 'Entrepreneurship', NULL, 'ENACTUS team won the 2013 Global Management Challenge Finals.', 'lightbulb', 'orange-600', NULL, NULL, NULL, 6),

('why_choose_vvu', 'eco_friendly', 'Solar Power', NULL, 'Supplies excess solar power to the national grid (ECG)', 'solar_power', 'green-500', NULL, NULL, NULL, 1),
('why_choose_vvu', 'eco_friendly', 'Bio Gas', NULL, 'Uses Bio Gas to supplement fuel consumption', 'propane_tank', 'green-500', NULL, NULL, NULL, 2),
('why_choose_vvu', 'eco_friendly', 'Water Plant', NULL, 'First private university with a Sachet Water Plant', 'water_drop', 'green-500', NULL, NULL, NULL, 3),

('why_choose_vvu', 'facilities', 'Accredited Hospital', NULL, 'First private university with an NHIS accredited Hospital.', 'local_hospital', 'blue-600', NULL, NULL, NULL, 1),
('why_choose_vvu', 'facilities', 'Radio Station', NULL, 'The first private university with its own Radio station.', 'radio', 'yellow-500', NULL, NULL, NULL, 2),
('why_choose_vvu', 'facilities', 'Healthy Living', NULL, 'Unique Wheat-Soy Bakery promoting healthy lifestyles.', 'bakery_dining', 'purple-600', NULL, NULL, NULL, 3),
('why_choose_vvu', 'facilities', 'Smart Campus', NULL, 'Advanced Biometric Authentication and Management Systems.', 'fingerprint', 'green-600', NULL, NULL, NULL, 4),

('why_choose_vvu', 'student_life', 'Student Clubs', NULL, 'Over 50 Student Clubs & Organizations', 'groups', 'blue-600', NULL, NULL, NULL, 1),
('why_choose_vvu', 'student_life', 'Sports Facilities', NULL, 'State-of-the-art Sports Facilities', 'sports_soccer', 'yellow-500', NULL, NULL, NULL, 2),
('why_choose_vvu', 'student_life', 'Spiritual Growth', NULL, 'Spiritual Growth & Mentorship', 'church', 'purple-600', NULL, NULL, NULL, 3),

-- Download Forms
('download_forms', 'undergrad', 'Undergraduate Admission Form 2019', NULL, 'Official application form for all undergraduate programs.', 'description', 'blue-500', NULL, 'undergraduate-admission-form-2019.pdf', 'PDF', 1),
('download_forms', 'undergrad', 'General Admission Requirements', NULL, 'Comprehensive guide on entry requirements.', 'description', 'blue-500', NULL, 'admission-requirements-general.pdf', 'PDF', 2),
('download_forms', 'undergrad', 'University Access Program for SHS', NULL, 'Application form for the SHS access program.', 'description', 'blue-500', NULL, 'university-access-program-for-SHS.pdf', 'PDF', 3),
('download_forms', 'undergrad', 'Kumasi Campus Admissions Flyer', NULL, 'Information about Kumasi campus admissions.', 'description', 'blue-500', NULL, 'Kumasi-Campus-Opens-Admissions.pdf', 'PDF', 4),

('download_forms', 'postgrad', 'Postgraduate Admission Form', NULL, 'Application form for Masters and PhD programs.', 'description', 'purple-500', NULL, 'post-graduate-form.pdf', 'PDF', 1),
('download_forms', 'postgrad', 'Postgraduate Appendix A', NULL, 'Supplementary document for applications.', 'description', 'purple-500', NULL, 'post-graduate-appendix-a.pdf', 'PDF', 2),
('download_forms', 'postgrad', 'PhD Admission Forms', NULL, 'Specific forms for Doctorate applications.', 'description', 'purple-500', NULL, 'PhD-Admission-Forms.pdf', 'PDF', 3),

('download_forms', 'nursing_special', 'Nursing & Midwifery Advert', NULL, 'Information on Nursing and Midwifery admissions.', 'description', 'green-500', NULL, 'Advert-Nursing-Midwifery-February-2019.pdf', 'PDF', 1),
('download_forms', 'nursing_special', 'Nursing Access Application Form', NULL, 'Updated form for the Nursing Access program.', 'description', 'green-500', NULL, 'nursing-access-application-form-updated.pdf', 'PDF', 2),
('download_forms', 'nursing_special', 'Mature Admissions Form', NULL, 'Application form for mature students.', 'description', 'green-500', NULL, 'mature-admissions-form.pdf', 'PDF', 3),
('download_forms', 'nursing_special', 'Mature Pass List (Dec 2017)', NULL, 'Historical pass list for mature entrance exams.', 'description', 'green-500', NULL, 'December-2017- mature-pass list.pdf', 'PDF', 4),

('download_forms', 'international', 'Formulaire (Bénin)', '🇧🇯 Bénin', NULL, NULL, 'yellow-500', NULL, 'Formulaire dadmission au Bnin.pdf', 'PDF', 1),
('download_forms', 'international', 'Formulaire (DR Congo)', '🇨🇩 RD Congo', NULL, NULL, 'yellow-500', NULL, 'Formulaire dadmission au DR Congo.pdf', 'PDF', 2),
('download_forms', 'international', 'Formulaire (Gabon)', '🇬🇦 Gabon', NULL, NULL, 'yellow-500', NULL, 'Formulaire dadmission au Gabon.pdf', 'PDF', 3),
('download_forms', 'international', 'Formulaire (Togo)', '🇹🇬 Togo', NULL, NULL, 'yellow-500', NULL, 'Formulaire dadmission au Togo.pdf', 'PDF', 4),

('download_forms', 'others', 'Executive Sports Course Form', NULL, 'Application for executive sports courses.', 'sports_soccer', 'gray-700', NULL, '160-Application  form.pdf', 'PDF', 1),
('download_forms', 'others', 'Employment Application Form', NULL, 'Official form for job applications at VVU.', 'work', 'gray-700', NULL, 'employment-forma (1).pdf', 'PDF', 2),
('download_forms', 'others', 'Short Courses Application Form', NULL, 'Form for short-term professional courses.', 'quick_reference', 'gray-700', NULL, 'Short-Courses-Form.pdf', 'PDF', 3),
('download_forms', 'others', 'VVU IRB Application Form', NULL, 'Institutional Review Board application.', 'science', 'gray-700', NULL, 'VVU-IRB-Application-Form.docx', 'DOCX', 4),

-- Mature Entrance
('mature_entrance', 'why_choose', 'Accredited Programs', NULL, 'All programs are fully accredited by the National Accreditation Board.', 'check', 'blue-600', NULL, NULL, NULL, 1),
('mature_entrance', 'why_choose', 'Flexible Class Options', NULL, 'Choose from 6-week intensive or 10-Sunday preparation sessions.', 'schedule', 'green-600', NULL, NULL, NULL, 2),
('mature_entrance', 'why_choose', 'Tutorial Support', NULL, 'Comprehensive tutorials and guidance before examinations.', 'support_agent', 'yellow-500', NULL, NULL, NULL, 3),
('mature_entrance', 'why_choose', 'Quality Education', NULL, 'Ghana''s first chartered private university with 45+ years of excellence.', 'workspace_premium', 'purple-600', NULL, NULL, NULL, 4),

('mature_entrance', 'programs', 'BEd Mathematics', 'Education', 'Bachelor of Education in Mathematics for aspiring math educators.', 'school', 'indigo-600', NULL, NULL, NULL, 1),
('mature_entrance', 'programs', 'BEd English Language', 'Education', 'Train to become a professional English language teacher.', 'menu_book', 'indigo-600', NULL, NULL, NULL, 2),
('mature_entrance', 'programs', 'BEd Social Studies', 'Education', 'Comprehensive education degree in social sciences.', 'groups', 'indigo-600', NULL, NULL, NULL, 3),
('mature_entrance', 'programs', 'BEd Religious Studies', 'Education', 'Specialize in teaching religious education in schools.', 'church', 'indigo-600', NULL, NULL, NULL, 4),
('mature_entrance', 'programs', 'BBA Marketing', 'Business', 'Master marketing strategies and brand management.', 'trending_up', 'blue-600', NULL, NULL, NULL, 5),
('mature_entrance', 'programs', 'BBA Management', 'Business', 'Develop leadership and organizational management skills.', 'business_center', 'blue-600', NULL, NULL, NULL, 6),
('mature_entrance', 'programs', 'BBA Human Resource', 'Business', 'Specialize in human resource management and development.', 'group', 'blue-600', NULL, NULL, NULL, 7),
('mature_entrance', 'programs', 'BBA Accounting', 'Business', 'Master financial accounting and auditing practices.', 'calculate', 'blue-600', NULL, NULL, NULL, 8),
('mature_entrance', 'programs', 'BBA Banking & Finance', 'Business', 'Prepare for a career in banking and financial services.', 'account_balance', 'blue-600', NULL, NULL, NULL, 9),
('mature_entrance', 'programs', 'BSc Information Technology', 'Technology', 'Learn modern IT skills and software development.', 'computer', 'green-600', NULL, NULL, NULL, 10),
('mature_entrance', 'programs', 'BSc Mathematics with Economics', 'Science', 'Combine mathematical analysis with economic theory.', 'functions', 'green-600', NULL, NULL, NULL, 11),
('mature_entrance', 'programs', 'BSc Mathematics with Statistics', 'Science', 'Master statistical analysis and mathematical modeling.', 'bar_chart', 'green-600', NULL, NULL, NULL, 12),
('mature_entrance', 'programs', 'BA Theological Studies', 'Theology', 'Deep study of theology and religious ministry.', 'auto_stories', 'purple-600', NULL, NULL, NULL, 13),
('mature_entrance', 'programs', 'BSc Development Studies', 'Development', 'Study community development and social change.', 'public', 'orange-600', NULL, NULL, NULL, 14),
('mature_entrance', 'programs', 'Dip. Biomedical Equipment Tech', 'Healthcare', 'Specialize in medical equipment maintenance and repair.', 'biotech', 'red-600', NULL, NULL, NULL, 15),

('mature_entrance', 'how_to_apply', 'Download Form', NULL, 'Get the application form from our website or any VVU campus.', '1', 'blue-600', NULL, NULL, NULL, 1),
('mature_entrance', 'how_to_apply', 'Complete Form', NULL, 'Fill in all required information accurately and completely.', '2', 'green-600', NULL, NULL, NULL, 2),
('mature_entrance', 'how_to_apply', 'Attend Classes', NULL, 'Choose your preferred session and attend preparation classes.', '3', 'yellow-500', NULL, NULL, NULL, 3),
('mature_entrance', 'how_to_apply', 'Take Exam', NULL, 'Pass the examination and receive your admission letter.', '4', 'purple-600', NULL, NULL, NULL, 4),

('mature_entrance', 'contact', 'Phone', '0307010268', NULL, 'call', 'blue-600', NULL, NULL, NULL, 1),
('mature_entrance', 'contact', 'Mobile', '0242385710', NULL, 'phone_android', 'green-600', NULL, NULL, NULL, 2),
('mature_entrance', 'contact', 'Mobile', '0204005704', NULL, 'phone_android', 'yellow-500', NULL, NULL, NULL, 3),
('mature_entrance', 'contact', 'Mobile', '0244565524', NULL, 'phone_android', 'purple-600', NULL, NULL, NULL, 4),

-- Music Programs
('music_programs', 'benefits', 'Teaching Skills', NULL, 'Acquire the requisite skills and competencies for teaching music, dance, and drama at pre-tertiary levels.', 'school', 'purple-600', NULL, NULL, NULL, 1),
('music_programs', 'benefits', 'Cultural Promotion', NULL, 'Promote cultural and music activities in schools and communities, preserving our rich heritage.', 'diversity_3', 'yellow-500', NULL, NULL, NULL, 2),
('music_programs', 'benefits', 'Indigenous Music', NULL, 'Develop and maintain interest in indigenous music in yourself and inspire others to appreciate local culture.', 'music_note', 'green-600', NULL, NULL, NULL, 3),
('music_programs', 'benefits', 'Leadership Roles', NULL, 'Take up leadership roles in organizations that deal with music, arts, and cultural development.', 'groups', 'blue-600', NULL, NULL, NULL, 4),
('music_programs', 'benefits', 'Diverse Careers', NULL, 'Pursue various careers in the music profession outside the classroom - from production to performance.', 'work', 'red-600', NULL, NULL, NULL, 5),
('music_programs', 'benefits', 'Accredited Degree', NULL, 'Graduate with an accredited degree recognized nationally and internationally for quality education.', 'workspace_premium', 'indigo-600', NULL, NULL, NULL, 6),

('music_programs', 'programs', '4-Year B.Ed Music Education', 'Bachelor of Education', 'Candidates must have passed in three core and three elective subjects. A pass in Music will be an advantage.', 'school', 'yellow-400', NULL, NULL, NULL, 1),
('music_programs', 'programs', '2-Year Diploma in Music', 'Diploma Certificate', 'Candidates must have passed in three core and two elective subjects. A pass in Music will be an advantage.', 'verified', 'green-500', NULL, NULL, NULL, 2),

('music_programs', 'careers', 'Music Teacher', NULL, 'Teach at primary, secondary, or tertiary institutions', 'school', 'purple-600', NULL, NULL, NULL, 1),
('music_programs', 'careers', 'Music Director', NULL, 'Lead choirs, bands, and musical ensembles', 'mic', 'yellow-500', NULL, NULL, NULL, 2),
('music_programs', 'careers', 'Music Producer', NULL, 'Create and produce music for various media', 'album', 'green-600', NULL, NULL, NULL, 3),
('music_programs', 'careers', 'Cultural Officer', NULL, 'Promote arts and culture in organizations', 'theater_comedy', 'blue-600', NULL, NULL, NULL, 4),
('music_programs', 'careers', 'Radio/TV Presenter', NULL, 'Host music and entertainment shows', 'radio', 'red-600', NULL, NULL, NULL, 5),
('music_programs', 'careers', 'Event Manager', NULL, 'Organize concerts and cultural events', 'event', 'indigo-600', NULL, NULL, NULL, 6),
('music_programs', 'careers', 'Music Composer', NULL, 'Write original music for various purposes', 'edit_note', 'pink-600', NULL, NULL, NULL, 7),
('music_programs', 'careers', 'Music Therapist', NULL, 'Use music for healing and therapy', 'psychology', 'orange-600', NULL, NULL, NULL, 8),

('music_programs', 'why_vvu', 'First Chartered Private University', NULL, 'The first chartered private university in Ghana with over 45 years of academic excellence.', 'verified', 'purple-600', NULL, NULL, NULL, 1),
('music_programs', 'why_vvu', 'Award-Winning Institution', NULL, 'Best Private University 2013 and recipient of the Century International Quality ERA Award.', 'emoji_events', 'yellow-500', NULL, NULL, NULL, 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP, `item_title` = VALUES(`item_title`), `item_subtitle` = VALUES(`item_subtitle`), `item_description` = VALUES(`item_description`), `item_icon` = VALUES(`item_icon`), `item_color` = VALUES(`item_color`), `item_image` = VALUES(`item_image`), `item_link` = VALUES(`item_link`), `item_stat_value` = VALUES(`item_stat_value`);
