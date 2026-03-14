-- Digital Books & Online Resources Page Content Migration
-- This creates the page entry and all content sections in the database

-- Create the page entry
INSERT INTO administration_pages (page_slug, page_name, is_active) 
VALUES ('digital_books', 'Digital Books & Online Resources', 1);

SET @page_id = LAST_INSERT_ID();

-- ============ HERO SECTION ============
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active)
VALUES (@page_id, 'hero', 'hero', 1, 1);
SET @hero_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@hero_id, 'title', 'Digital Books & Online Resources', 'text'),
(@hero_id, 'subtitle', 'Access thousands of e-books, journals, and digital resources from anywhere, anytime.', 'text'),
(@hero_id, 'bg_image', 'uploads/Library/library-banner.jpg', 'image');

-- ============ QR CODE SECTION ============
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active)
VALUES (@page_id, 'content', 'qr_ebooks', 2, 1);
SET @qr_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@qr_id, 'section_title', 'VVU Library E-Books', 'text'),
(@qr_id, 'section_description', 'Scan the QR code with your mobile device to instantly access our complete e-books collection on Google Drive. You can also click the button below to access directly from your browser.', 'textarea'),
(@qr_id, 'qr_image', 'https://vvu.edu.gh/images/library/qr-code.png', 'image'),
(@qr_id, 'ebooks_url', 'https://drive.google.com/drive/folders/1S5RF_uoxaLGM_F99uQBWloysm8Dom7G8', 'text'),
(@qr_id, 'button_text', 'Access E-Books Collection', 'text');

-- ============ ONLINE RESOURCES SECTION ============
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active)
VALUES (@page_id, 'content', 'online_resources', 3, 1);
SET @online_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@online_id, 'resource_1_title', 'Online Resources', 'text'),
(@online_id, 'resource_1_description', 'Browse through our diverse array of online libraries and acquire the knowledge you seek. Access academic databases, research journals, and reference materials.', 'textarea'),
(@online_id, 'resource_1_url', 'https://vvu.edu.gh/index.php/component/content/article?id=167&Itemid=437', 'text'),
(@online_id, 'resource_1_icon', 'language', 'text'),
(@online_id, 'resource_1_button_text', 'Explore Resources', 'text'),
(@online_id, 'resource_2_title', 'PDF Books', 'text'),
(@online_id, 'resource_2_description', 'Check out our archives of PDF books on various topics that may interest you and support your journey to academic excellence and greatness.', 'textarea'),
(@online_id, 'resource_2_url', 'https://vvu.edu.gh/index.php/component/sppagebuilder/?view=page&id=377', 'text'),
(@online_id, 'resource_2_icon', 'picture_as_pdf', 'text'),
(@online_id, 'resource_2_button_text', 'Browse PDF Books', 'text');

-- ============ PARTNER LIBRARIES SECTION ============
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active)
VALUES (@page_id, 'content', 'partner_libraries', 4, 1);
SET @partner_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@partner_id, 'section_title', 'Partner Libraries', 'text'),
(@partner_id, 'section_description', 'Access resources from our partner institutions and expand your research horizons.', 'textarea'),
(@partner_id, 'partner_1_title', 'Babcock University Library', 'text'),
(@partner_id, 'partner_1_description', 'Access the digital resources of Babcock University Library, a leading Seventh-day Adventist institution in Nigeria with a rich academic library collection.', 'textarea'),
(@partner_id, 'partner_1_url', 'https://www.library.babcock.edu.ng', 'text'),
(@partner_id, 'partner_1_icon', 'school', 'text'),
(@partner_id, 'partner_2_title', 'Leslie Hardinge Library', 'text'),
(@partner_id, 'partner_2_description', 'Explore the Leslie Hardinge Library collection, a valuable partner resource offering theological and academic materials for research and study.', 'textarea'),
(@partner_id, 'partner_2_url', 'https://vvu.edu.gh/index.php/component/sppagebuilder/?view=page&id=76', 'text'),
(@partner_id, 'partner_2_icon', 'local_library', 'text');

-- ============ QUICK LINKS SECTION ============
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active)
VALUES (@page_id, 'content', 'quick_links', 5, 1);
SET @links_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@links_id, 'link_1_title', 'Library Catalog (OPAC)', 'text'),
(@links_id, 'link_1_url', '#', 'text'),
(@links_id, 'link_1_icon', 'manage_search', 'text'),
(@links_id, 'link_1_description', 'Search our online catalog for books, journals, and media', 'text'),
(@links_id, 'link_2_title', 'E-Learning Platform', 'text'),
(@links_id, 'link_2_url', 'https://learning.vvu.edu.gh', 'text'),
(@links_id, 'link_2_icon', 'cast_for_education', 'text'),
(@links_id, 'link_2_description', 'Access course materials and online learning resources', 'text'),
(@links_id, 'link_3_title', 'Research Journals', 'text'),
(@links_id, 'link_3_url', 'https://journal.vvu.edu.gh', 'text'),
(@links_id, 'link_3_icon', 'science', 'text'),
(@links_id, 'link_3_description', 'Browse The Integrator and other VVU research publications', 'text'),
(@links_id, 'link_4_title', 'Library Resources', 'text'),
(@links_id, 'link_4_url', 'library_resources.php', 'text'),
(@links_id, 'link_4_icon', 'menu_book', 'text'),
(@links_id, 'link_4_description', 'Back to the main Library Resources page', 'text');
