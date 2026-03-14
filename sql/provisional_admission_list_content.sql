-- Add content for Provisional Admissions List page
INSERT INTO `academic_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `cta_title`, `cta_subtitle`, `cta_button_text`, `cta_button_link`) VALUES
('provisional_admission_list', 'Provisional Admission List', 'Admissions 2024/2025', 'Provisional', 'Admission List', 'Check your admission status and view the official lists of provisionally admitted students for the current academic session.', 'vvu_admissions_hero_1766876689316.png', 'Not on the list yet?', 'Don\'t worry, more lists are being released soon. Stay tuned or contact our admissions office for more information.', 'Contact Admissions', 'contact_us.php')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Add sections for Provisional Admissions List page
INSERT INTO `academic_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('provisional_admission_list', 'official_lists', 'Official Admission Lists', 'Click on any document below to view or download the provisionally admitted students list.', 1)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Add sample items (PDFs) for Provisional Admissions List page
INSERT INTO `academic_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_link`, `item_stat_value`, `display_order`) VALUES
('provisional_admission_list', 'official_lists', 'Undergraduate Batch 1', 'List of provisionally admitted students for undergraduate programs - Batch 1.', 'description', 'uploads/admission_lists/undergrad_batch_1.pdf', 'PDF', 1),
('provisional_admission_list', 'official_lists', 'Postgraduate Batch 1', 'List of provisionally admitted students for postgraduate programs - Batch 1.', 'description', 'uploads/admission_lists/postgrad_batch_1.pdf', 'PDF', 2)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;
