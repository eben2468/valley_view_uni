-- Sample data for Strategic Plan, Policies, History, and Accreditation & Charter pages
-- Run this after running strategy_history_schema.sql

-- ============================================
-- STRATEGIC PLAN PAGE DATA
-- ============================================

INSERT INTO strategic_plan_hero (page_subtitle, hero_title_1, hero_title_2, hero_description, hero_image_url, download_button_text, download_pdf_url) VALUES
('Vision 2026 & Beyond', 'Strategic Plan', 'Shaping Our Future', 'Our roadmap for innovation, excellence, and community impact. Discover how Valley View University is redefining higher education for the next generation.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAUvUfhIZVhbLlcWbvh2K0tcDjyOiDtIu3B2vfPeBtSHv8AacdtYQQdtHrHVAopeOlhPyc4b9yXYTLBQE4vfvaalzQF-NCOHT6bwryFrbQZiyGoFMKQnkurMJ2l-1d7UKMk4e9u6woXkdkq4SkyrSC7tgZrdEFcXbaizT5320z06QTYFgdXStkPEpReAmCxeZXC95kcAxnPqnmj-3VMQC38wSxZto4dDPaG4aVczHcIp3oDGQCF2SfW1_Fj8blUd12xZgSoD_d1W6TE', 'Download Vision 2025 (PDF)', 'uploads/VISION 2025.pdf');

INSERT INTO strategic_plan_president_message (section_title, president_image_url, message_quote, message_author) VALUES
('A Message From The Vice Chancellor', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAkKeRPDBxhuQm5UZNxRE2coeRYfht7h-6lKFypxlTlppHFWH6bTPw10XqoWhMO1H0cSnqmIZLsldIPfTbRv4T-HnwbCcJyukLrLVj0EWB_HbBZRbpR3PsNISUgSd4IpBDzKX4t65jeueWusxVNs6HQ32WNZi5sOEhmJ8hdsNNbcFgkhoz-k3_kuJHRbGuXxoaLdr2cruXuayz1-FI6UL3NlcwPHqFc-YD6afOPonMFUabtgoQQNnJUaleJxPXbXqJFCFF-3WVbqHuM', 'Together, we embark on a journey to redefine higher education. This plan is our commitment to fostering an environment where every student can thrive, our research can break new ground, and our community can flourish.', '— Join us as we build the future of Valley View.');

INSERT INTO strategic_plan_pillars (icon, title, description, feature_1, feature_2, border_color, display_order) VALUES
('school', 'Academic Excellence', 'Pioneering innovative curricula and research to create the leaders of tomorrow. We are committed to world-class standards.', 'Innovative Research', 'Global Accreditation', 'blue-600', 1),
('sentiment_satisfied', 'Student Success', 'Fostering a supportive and inclusive environment for holistic student development, ensuring every student reaches their potential.', 'Holistic Mentorship', 'Career Readiness', 'yellow-500', 2),
('public', 'Community Impact', 'Engaging with local and global partners to address societal challenges and promote sustainable development.', 'Sustainable Outreach', 'Global Partnerships', 'green-600', 3);

INSERT INTO strategic_plan_timeline (phase_number, phase_badge, phase_title, phase_description, border_color, dot_color, display_order) VALUES
(1, 'Phase 1', 'Foundation & Growth', 'Launch new interdisciplinary programs and expand our digital infrastructure to support global learning.', 'blue-600', 'blue-600', 1),
(2, 'Phase 2', 'Innovation & Expansion', 'Establish global research partnerships and construct the new STEM complex for cutting-edge discovery.', 'yellow-500', 'yellow-500', 2),
(3, 'Phase 3', 'Leadership & Legacy', 'Achieve carbon neutrality and celebrate the culmination of our Vision 2025 goals as a leader in education.', 'green-600', 'green-600', 3);

INSERT INTO strategic_plan_stats (stat_value, stat_label, display_order) VALUES
('65%', 'Research Funding', 1),
('85%', 'Graduation Rate', 2),
('90%', 'Community Impact', 3);

INSERT INTO strategic_plan_cta (cta_title_1, cta_title_2, cta_description, button_1_text, button_1_url, button_2_text, button_2_url) VALUES
('Join Us on Our Journey,', 'Build the Future', 'Be part of a university that values the future as much as education. Discover how you can contribute to our strategic mission.', 'Download Full Plan (PDF)', 'uploads/VISION 2025.pdf', 'Contact Us', 'contact_us.php');

-- ============================================
-- POLICIES PAGE DATA
-- ============================================

INSERT INTO policies_hero (page_subtitle, hero_title, hero_subtitle, hero_description, hero_image_url) VALUES
('Governance & Standards', 'University', 'Policies', 'A comprehensive guide to the principles, regulations, and procedures that govern Valley View University. We ensure transparency and fairness in all our operations.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh');

INSERT INTO policies_categories (icon, title, description, border_color, display_order) VALUES
('gavel', 'Governance', 'Foundational documents that define the legal and operational structure of the university.', 'blue-600', 1),
('menu_book', 'Academic', 'Guidelines for academic standards, student conduct, and university life.', 'yellow-500', 2),
('badge', 'Staff', 'Resources and contracts for faculty and staff members of the university.', 'green-600', 3);

INSERT INTO policies_documents (category_id, document_title, document_url, icon_color, display_order) VALUES
(1, 'University Statutes', 'uploads/Statutes.pdf', 'blue-600', 1),
(1, 'VVU Bylaws', 'uploads/VVU Bylaws.pdf', 'blue-600', 2),
(2, 'Academic Bulletin', 'uploads/VVU-Academic-Bulletin-June-2020.pdf', 'yellow-600', 1),
(2, 'Student Handbook', 'uploads/VVU-STUDENT-HANDBOOK.pdf', 'yellow-600', 2),
(3, 'Employee Handbook', 'uploads/Employee Handbook.pdf', 'green-600', 1),
(3, 'Retirement Contract', 'uploads/Retirement Contract.pdf', 'green-600', 2);

INSERT INTO policies_quick_links (icon, title, description, link_text, link_url, icon_bg_color, display_order) VALUES
('description', 'Archives', 'Access historical policy documents and previous versions of university statutes.', 'View Archives', '#!', 'blue-600', 1),
('help', 'FAQs', 'Common questions regarding university regulations and policy implementation.', 'Read FAQs', 'faqs_about_vvu.php', 'yellow-500', 2),
('contact_support', 'Support', 'Need clarification on a policy? Contact the Registrar\'s office for assistance.', 'Contact Us', 'contact_us.php', 'green-600', 3);

INSERT INTO policies_cta (cta_title_1, cta_title_2, cta_description, button_1_text, button_1_url, button_2_text, button_2_url) VALUES
('Committed to', 'Integrity & Transparency', 'Our policies are designed to protect and empower every member of the Valley View University family.', 'Our Mission', 'mission_and_vision.php', 'Our Values', 'core_values.php');

-- ============================================
-- HISTORY PAGE DATA
-- ============================================

INSERT INTO history_hero (page_subtitle, hero_title, hero_subtitle, hero_description, hero_image_url) VALUES
('Our Legacy', 'The Journey', 'Of Excellence', 'From our humble beginnings in 1979 to becoming Ghana\'s first chartered private university, our history is a testament to faith, vision, and academic brilliance.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh');

INSERT INTO history_overview (section_title, paragraph_1, paragraph_2, founded_year, chartered_year, overview_image_url) VALUES
('A Visionary Beginning', 'Valley View University was established in 1979 by the West African Union Mission of Seventh-day Adventists. What started as a focused mission to provide quality Christian education has grown into a beacon of higher learning in West Africa.', 'In 1997, the institution was absorbed into the Adventist University system operated by the West Central African Division of Seventh-day Adventists, headquartered in Abidjan, Cote d\'Ivoire, further strengthening its global academic ties.', '1979', '2006', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh');

INSERT INTO history_milestones (year, milestone_title, milestone_description, border_color, dot_color, display_order) VALUES
('1979', 'The Foundation', 'Established by the West African Union Mission of Seventh-day Adventists to provide holistic Christian education.', 'blue-600', 'blue-600', 1),
('1983', 'Global Standards', 'The Adventist Accrediting Association (AAA) began its regular evaluation and review of the institution\'s accreditation status.', 'yellow-500', 'yellow-500', 2),
('1997', 'Expansion & Affiliation', 'Affiliated with Griggs University (USA) and granted accreditation by the National Accreditation Board, Ghana to award own degrees.', 'blue-600', 'blue-600', 3),
('2006', 'Presidential Charter', 'Granted a Presidential Charter by the Government of Ghana, becoming the first chartered private university in the country.', 'yellow-500', 'yellow-500', 4);

INSERT INTO history_community (section_title, section_description, feature_1_title, feature_1_label, feature_2_title, feature_2_label, feature_3_title, feature_3_label) VALUES
('A Global Community', 'Today, Valley View University serves undergraduate and graduate students from all over the world. We admit qualified students regardless of their religious background, provided they accept the Christian principles and lifestyle that form the basis of our operations.', 'Global', 'Reach', 'Inclusive', 'Community', 'Chartered', 'Excellence');

INSERT INTO history_cta (cta_title_1, cta_title_2, cta_description, button_1_text, button_1_url, button_2_text, button_2_url) VALUES
('Be Part of Our', 'Future History', 'Join a legacy of excellence and innovation. Your journey at Valley View University starts here.', 'Apply Now', 'admissions.php', 'Contact Us', 'contact_us.php');

-- ============================================
-- ACCREDITATION & CHARTER PAGE DATA
-- ============================================

INSERT INTO accreditation_hero (page_subtitle, hero_title, hero_subtitle, hero_description, hero_image_url) VALUES
('Quality Assurance', 'Accreditation', '& University Charter', 'Valley View University is fully accredited and committed to the highest standards of academic excellence, recognized by national and international governing bodies.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBT_9onDZsW2FiO7PENWLZ2-zS-pH_w_0fx3u39rY8cLStB2LjjTqB_NPnq0lt2LmdWHLAzaopeU6I9zjaUkGISXnPVoe1MkE_vBUUM8fr-BTT82YhFdDVvGv_gnYuMw_90H1Bwgk-XZwEVJuSa1lsZ1KcgaBA0zyrOQ79syt1j9--cEd2d8A70P0b85kpPxbccquV8y__dCuLp29-lsMWdKu4P4i2zCriI0j3fszUQio1xwXzRactEz8y9Wswe6Lxfec9HTLdXILKs');

INSERT INTO accreditation_cards (icon, title, description, border_color, display_order) VALUES
('verified', 'AAA Accreditation', 'Accredited by the <strong class="text-blue-600">Adventist Accrediting Association</strong> and International Board of Education (IBE). VVU is part of a global network of 109 Adventist colleges and universities.', 'blue-600', 1),
('gavel', 'National Board', 'Fully accredited by the <strong class="text-yellow-600">National Accreditation Board (NAB)</strong>, Ghana, for all academic programs, ensuring national recognition and quality standards.', 'yellow-500', 2),
('medical_services', 'NMC Ghana', 'Accredited by the <strong class="text-blue-600">Nurses and Midwifery Council, Ghana (NMC)</strong> for our healthcare programs, maintaining the highest professional standards in nursing education.', 'blue-600', 3);

INSERT INTO accreditation_charter (badge_text, section_title, paragraph_1, paragraph_2, quote, charter_year, achievement_text, achievement_location) VALUES
('A Historic Milestone', 'The Presidential Charter', 'In January 2006, Valley View University was granted a Presidential Charter by His Excellency, Mr. J. A. Kufuor, President of the Republic of Ghana.', 'This historic achievement made VVU the <strong class="text-blue-600">first Chartered Private University in Ghana</strong>, granting us the rights and privileges to operate as an autonomous degree-granting institution.', 'Chartered status is granted after careful scrutiny of an institution\'s statutes, examination procedures, and quality assurance standards.', '2006', 'First Chartered Private University', 'Ghana • 2006');

INSERT INTO accreditation_memberships (organization_name, organization_description, membership_type, location, display_order) VALUES
('Association of African Universities (AAU)', 'Promoting excellence in higher education across the continent.', 'membership', NULL, 1),
('Association of Commonwealth Universities (ACU)', 'Connecting universities across the Commonwealth.', 'membership', NULL, 2),
('Ghana Association of Private Tertiary Institutions (GAPTI)', 'The umbrella body for accredited tertiary institutions in Ghana.', 'membership', NULL, 3),
('Andrews University', NULL, 'linkage', 'USA', 1),
('State University of New York', NULL, 'linkage', 'Geneseo, USA', 2),
('Adventist University of Africa', NULL, 'linkage', 'Kenya', 3),
('Otterbein University', NULL, 'linkage', 'USA', 4);

INSERT INTO accreditation_cta (cta_title_1, cta_title_2, cta_description, button_1_text, button_1_url, button_2_text, button_2_url) VALUES
('Committed to', 'Academic Excellence', 'Our accreditation ensures that your degree is recognized and valued globally.', 'Explore Programs', 'academics.php', 'Contact Us', 'contact_us.php');
