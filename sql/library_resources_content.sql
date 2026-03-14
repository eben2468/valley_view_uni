-- =====================================================
-- Library Resources Page - Additional Content Migration
-- Run this AFTER the base administration tables exist
-- Page ID: 53 (library_resources)
-- =====================================================

-- Update the director/welcome section message to full content
UPDATE administration_content_fields 
SET field_value = 'Dear Faculty, Staff, Students, and Researchers,

On behalf of the library team, it is my pleasure to extend a warm welcome to you all. As the University Librarian, I am excited to invite you to explore the diverse resources and services available at our library.

Our collection boasts a diverse array of materials, curated to support your academic and research endeavors. Whether you''re delving into theology, computing, health sciences, education, or any other field, our shelves are stocked with resources to fuel your intellectual curiosity.

In addition to our physical collection, we offer access to an extensive range of digital resources through our online databases (https://vvu.edu.gh/index.php/component/sppagebuilder/?view=page&id=371).

These databases, sourced through CARLIGH (Consortium of Academic and Research Libraries in Ghana), sister universities, and the generous contributions of the General Conference of Seventh-Day Adventists, offer a wealth of scholarly articles, journals, and publications to you. I encourage you to take advantage of these resources to enrich your studies and broaden your understanding.

Our dedicated team of trained librarians is here to assist you on your academic journey. Whether you need help locating a specific resource, navigating our databases, or conducting research, we are committed to providing the support and guidance you need.

I invite you to make the library your academic sanctuary – a place where ideas flourish, knowledge is shared, and connections are made. Whether you''re seeking a quiet space for study, engaging in collaborative research, or simply browsing our shelves, know that you are always welcome here.

Thank you for choosing to be a part of our vibrant academic community. Together, let us embark on a journey of discovery, learning, collaboration and growth.

Warm regards and blessings,'
WHERE content_id = (SELECT id FROM administration_content WHERE page_id = 53 AND section_key = 'director')
AND field_key = 'message';

-- =====================================================
-- New Section: Digital Resources Banner (order=4)
-- =====================================================
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active) 
VALUES (53, 'banner', 'digital_resources', 4, 1);

SET @digital_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@digital_id, 'banner_title', 'Discover new knowledge!', 'text'),
(@digital_id, 'banner_subtitle', 'Click the button below to unlock our vast collection of digital books and online resources.', 'textarea'),
(@digital_id, 'banner_button_text', 'Check Out Digital Books and Online Resources', 'text'),
(@digital_id, 'banner_button_url', 'https://vvu.edu.gh/index.php/component/sppagebuilder/?view=page&id=371', 'text'),
(@digital_id, 'banner_image', 'uploads/Library/library-banner.jpg', 'image');

-- =====================================================
-- New Section: About VVU Libraries (order=5)
-- =====================================================
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active) 
VALUES (53, 'content', 'about_libraries', 5, 1);

SET @about_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@about_id, 'about_title', 'About The VVU Libraries', 'text'),
(@about_id, 'about_intro', 'The Oyibi campus library is named after Valley View University''s first President, Pastor Dr. Walton Whaley. Walton Whaley Library is the main library of our university. It currently has a seating capacity of 112 and sports about 15,000 volumes. Apart from that it provides journal subscriptions across the disciplines. Other sections of the Library include the Newspaper Section, a Reference Section, a Periodical Section, an Information Desk, a Photocopy Section, and Ellen G. White Sections. Each of the three branch libraries provides approximately 5000+ books.', 'textarea'),
(@about_id, 'about_image_1', 'uploads/Library/img_1748-custom.jpg', 'image'),
(@about_id, 'about_image_2', 'uploads/Library/library-banner.jpg', 'image');

-- =====================================================
-- New Section: Branch Libraries (order=6)
-- =====================================================
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active) 
VALUES (53, 'content', 'branch_libraries', 6, 1);

SET @branch_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@branch_id, 'branch_title', 'Branch Libraries', 'text'),
(@branch_id, 'branch_intro', 'It has branch libraries which include the following:', 'text'),
(@branch_id, 'branch_1_name', 'Harold Lee Library', 'text'),
(@branch_id, 'branch_1_desc', 'This library is located on the second floor of the Columbia Block and is stocked primarily with books, audio-visuals, journals, etc. The library materials deal with Nursing and Bio-medical technology.', 'textarea'),
(@branch_id, 'branch_2_name', 'Kumasi City Campus Library', 'text'),
(@branch_id, 'branch_2_desc', 'It is located at Mancell in Kumasi (GPS Address). It caters to the information needs of the Valley View University academic community in Kumasi and its environs. This library is currently stocked with general business, religion, and computer books.', 'textarea'),
(@branch_id, 'branch_3_name', 'Techiman Campus Library', 'text'),
(@branch_id, 'branch_3_desc', 'This library is located on the Techiman Extension Campus of Valley View University (GPS Address) and caters to agribusiness, education, business, religion, theology, and computer programs.', 'textarea');

-- =====================================================
-- New Section: Library Plans & Development (order=7)
-- =====================================================
INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active) 
VALUES (53, 'content', 'library_plans', 7, 1);

SET @plans_id = LAST_INSERT_ID();

INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES
(@plans_id, 'plans_title', 'Library Development & Plans', 'text'),
(@plans_id, 'new_building_plans', 'Plans are underway to build a new, functional Library building for the University. The functional specifications for the new Library are available in the office of the University Librarian. It was prepared in March 2004 by Prof. Keith Clouten, the WWL consultant in consultation with the Library Staff and the University Administration.', 'textarea'),
(@plans_id, 'automation_intro', 'Automation/Digitization is the goal of every library today and Walton Whaley Library has captured that goal. As such, major features proposed in the new building that may have an impact on the future development and upgrade of the technical infrastructure of the automated library information system are that:', 'textarea'),
(@plans_id, 'automation_point_1', 'The migration of the automated system from the present library building to the new building will not cause any problems.', 'textarea'),
(@plans_id, 'automation_point_2', 'It will not also require any major software or hardware upgrades or replacements.', 'textarea'),
(@plans_id, 'automation_point_3', 'The system to be installed should be flexible enough to accommodate the projected increase in the number of students and the Library''s collection.', 'textarea'),
(@plans_id, 'library_operations', 'Library operations in WWL are like those found in typical University Libraries, except that, they are conducted on a smaller scale because of our numbers compared to public universities. A system that integrates acquisition, cataloging, circulation, an online catalog (OPAC), and produces statistical reports is no longer a luxury for academic libraries. Walton Whaley Library desires this progress of time to get the right information, into the hands of the right people, at the right time.', 'textarea'),
(@plans_id, 'technology_plan', 'Therefore, it is hoped that soon enough the technology plan will be completed and Walton Whaley Library will arrive at its preferred future climate – an Automated Library Information System (ALIS).', 'textarea'),
(@plans_id, 'funding_plans', 'Plans are afoot to find funding and put up a modern library building.', 'textarea'),
(@plans_id, 'closing_statement', 'TO GOD IS THE GLORY, GREAT THINGS HE HAS DONE!!!', 'text');
