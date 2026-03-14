-- =============================================
-- Ventures & Services Pages Migration
-- Manages: Bakery Factory, Water Factory, Grocery, Post Office, VVU Radio
-- =============================================

-- Main content table for ventures pages
CREATE TABLE IF NOT EXISTS `ventures_pages_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  
  -- Hero Section
  `hero_badge` varchar(100) DEFAULT 'VVU Ventures',
  `hero_title` varchar(255) NOT NULL,
  `hero_subtitle` text,
  `hero_description` text,
  `hero_image` text,
  
  -- About Section
  `about_heading` varchar(255) DEFAULT '',
  `about_text` text,
  `about_image` text,
  
  -- Ad/Banner Image
  `banner_image` text,
  
  -- CTA Section
  `cta_heading` varchar(255) DEFAULT '',
  `cta_subtitle` text,
  `cta_text` text,
  `cta_button_text` varchar(100) DEFAULT '',
  `cta_button_link` varchar(255) DEFAULT '',
  
  -- Contact
  `contact_phone` varchar(100) DEFAULT '',
  `contact_email` varchar(255) DEFAULT '',
  `contact_location` text,
  `contact_address` text,
  `contact_whatsapp` varchar(100) DEFAULT '',
  `contact_hours` text,
  
  -- Extra Fields
  `extra_field_1` text,
  `extra_field_2` text,
  `extra_field_3` text,
  
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sections within venture pages
CREATE TABLE IF NOT EXISTS `ventures_pages_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `section_title` varchar(255) NOT NULL,
  `section_subtitle` text,
  `section_description` text,
  `section_image` text,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_page_section` (`page_key`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Items within sections
CREATE TABLE IF NOT EXISTS `ventures_pages_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `item_title` varchar(255) NOT NULL,
  `item_subtitle` varchar(255) DEFAULT '',
  `item_description` text,
  `item_icon` varchar(100) DEFAULT '',
  `item_color` varchar(100) DEFAULT 'blue-600',
  `item_image` text,
  `item_link` varchar(255) DEFAULT '',
  `item_stat_value` varchar(100) DEFAULT '',
  `item_stat_label` varchar(100) DEFAULT '',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_page_section_item` (`page_key`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Stats/counters
CREATE TABLE IF NOT EXISTS `ventures_pages_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL,
  `stat_value` varchar(100) NOT NULL,
  `stat_label` varchar(100) NOT NULL,
  `stat_icon` varchar(100) DEFAULT '',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_page_stat` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BAKERY FACTORY DATA
-- =============================================
INSERT INTO `ventures_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `about_heading`, `about_text`, `about_image`, `cta_heading`, `cta_subtitle`, `contact_phone`, `contact_whatsapp`, `contact_location`, `contact_address`) VALUES
('bakery_factory', 'Bakery Factory - Valley View University', 'VVU Ventures', 'Bakery Factory', 'Freshly Baked, Naturally Healthy', '"Experience the taste of tradition and quality, crafted with passion and wholesome ingredients like honey for a healthier lifestyle."', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDFvEMRHq3gSd8o4eRxjctjrudbeCRUwckrJsi1uLIi_lvOSDYDHPzxCQsPNrjsJrriqbwiNKE_3vmZlmyOM8YjTiHAJ5DfLJ_Eo6iWniUzN74Tc-GxzOCkSNJx9P3moJeUDNbmgAu26S7LUxtQWkXV5w9Vn7IGoSQYAExqD6EbIyyFJseQXaQ8WaVTNxutg9EzyW_7zu_0DiYTWAnfU94X4HwG6tZlCXWObOoRASbv1k-NzD4TexgjSXo3FkyepJSw0kAbtizrBsGF', 'Quality You Can Trust', 'Our commitment to health and quality is reflected in every loaf we bake. We prioritize natural ingredients and traditional methods.', 'uploads/Bakery-Images/bakery-logo.jpg', 'Serving the Community', 'Our reach extends beyond the campus. With a growing fleet of delivery vans and a new depot in Ashaiman, we bring the taste of VVU to local markets and neighborhoods.', '+233 307011832', '+233 24 619 0061', 'Mile 19 Off the Adenta-Dodowa Road', 'P.O. Box AF 595 Adentan');

-- Bakery Sections
INSERT INTO `ventures_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `section_description`, `display_order`) VALUES
('bakery_factory', 'specialty_breads', 'Our Specialty Breads', 'Discover our wide range of freshly baked, artisanal breads made with the finest ingredients.', '', 1),
('bakery_factory', 'quality_features', 'Quality & Operations', '', '', 2),
('bakery_factory', 'gallery', 'Visual Showcase', 'A glimpse into our delicious creations and the passion that goes into every bake.', '', 3),
('bakery_factory', 'testimonials', 'What Our Customers Say', '', '', 4);

-- Bakery Products
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('bakery_factory', 'specialty_breads', 'Soya Bread', 'A protein-rich, nutritious choice perfect for a healthy breakfast or snack.', 'bakery_dining', 'amber-600', 1),
('bakery_factory', 'specialty_breads', 'Wheat Bread', 'High in fiber and full of natural goodness for sustained energy throughout the day.', 'grain', 'amber-700', 2),
('bakery_factory', 'specialty_breads', 'Coconut Bread', 'Infused with the tropical flavor of fresh coconut for a unique and delicious treat.', 'nutrition', 'amber-500', 3),
('bakery_factory', 'specialty_breads', 'Banana Bread', 'Moist, sweet, and packed with the natural goodness of ripe bananas.', 'breakfast_dining', 'yellow-600', 4),
('bakery_factory', 'specialty_breads', 'Corn Bread', 'Made with wheat flour, margarine, and yellow corn flour for a rich, golden taste.', 'grain', 'yellow-500', 5),
('bakery_factory', 'specialty_breads', 'Butter Bread', 'Rich, soft, and buttery - a classic favorite for any time of day.', 'restaurant', 'amber-400', 6),
('bakery_factory', 'specialty_breads', 'Potato Bread', 'A unique blend of wheat flour and potato puree for an extra moist and soft texture.', 'bakery_dining', 'orange-500', 7),
('bakery_factory', 'specialty_breads', 'Cassava Bread', 'Wholesome and nutritious, incorporating local cassava for a distinct, hearty flavor.', 'eco', 'emerald-500', 8);

-- Bakery Quality Features
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('bakery_factory', 'quality_features', 'Naturally Healthy', 'We use pure honey instead of high amounts of sugar in our recipes.', 'health_and_safety', 'amber-600', 1),
('bakery_factory', 'quality_features', '24/7 Operations', 'Operating around the clock with two shifts to ensure freshness.', 'schedule', 'amber-600', 2),
('bakery_factory', 'quality_features', 'High Production', 'Producing approximately 6,000 loaves weekly to meet high demand.', 'trending_up', 'amber-600', 3),
('bakery_factory', 'quality_features', 'Student Learning', 'A hands-on environment for our hospitality and culinary students.', 'school', 'amber-600', 4);

-- Bakery Gallery
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_image`, `display_order`) VALUES
('bakery_factory', 'gallery', 'Artisanal Breads', 'Naturally leavened perfection.', 'uploads/Bakery-Images/bread.jpg', 1),
('bakery_factory', 'gallery', 'Specialty Creations', 'Crafted with artistic passion.', 'uploads/Bakery-Images/bread-roses.jpg', 2),
('bakery_factory', 'gallery', 'Flaky Pastries', 'Buttery, golden, and delicious.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAvWsMkyswSwRjr0qj0-Xvf3L6_CY_0nnMFBvkgxG7tLk8vj4slF_AxoiLW-V1Jih-jwgczPwN5ko85lJDQB3nku8O7J0YV44aSOGXCCEcbVRXNZ8UO_4r0ZcimlZ8jf-JhST_O-cRYEsDCBdf2JkDRHxdsWOogNQECxsUdlMptzpPWfuK4jw_5g7mXrEujkiq5IADWhbkcE3Nk_nHPxOgxqp_Rm3aeIKfu5DtXcEQvYfq50WwLuMSJtkTTq7ntYE_bKX6Mg5J7Na7Q', 3),
('bakery_factory', 'gallery', 'Wholesome Snacks', 'Healthy treats for any time.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAtIdzg6z9y-x6O05_W-E0MPhjim-i_8D2EApXsztUAc7_1L39jHpM6dYB0Z_7cJkB1NGSiDENL2YxFnn2opTSGkDBq6b5pwgOZ62k_539ekK3_7CV0wBUzUjNpSCw1qloFQwbrzaHf-pSeTjEKlPWR9Fk_u4Fb2tjsj25lYRFe6Cas9ZstnWU7ZNUMC-FepSXCXhl76_0UD2mmTpDth6wTFrNeLfjis7sv2WxD9Hizm5HZOB_KCgJXDxg4_zc-Gr6w3MjwEDPA7bYO', 4);

-- Bakery Testimonial
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_description`, `item_stat_value`, `display_order`) VALUES
('bakery_factory', 'testimonials', 'Kwasi Okyere-Darko', 'Satisfied Customer', '"I strongly recommend the products of the Valley View University Bakery. They are rich and nutritious and, in fact, second to none..."', 'KO', 1);

-- Bakery Stats
INSERT INTO `ventures_pages_stats` (`page_key`, `stat_value`, `stat_label`, `display_order`) VALUES
('bakery_factory', '6K+', 'Loaves Weekly', 1),
('bakery_factory', '24/7', 'Operations', 2);

-- Bakery Distribution Images
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_image`, `display_order`) VALUES
('bakery_factory', 'distribution', 'Delivery Van', 'uploads/Bakery-Images/car.jpg', 1),
('bakery_factory', 'distribution', 'Delivery Fleet', 'uploads/Bakery-Images/car2.jpg', 2);

-- =============================================
-- WATER FACTORY DATA
-- =============================================
INSERT INTO `ventures_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `about_heading`, `about_text`, `about_image`, `banner_image`, `cta_heading`, `cta_subtitle`, `cta_text`, `contact_phone`, `contact_location`, `contact_address`) VALUES
('water_factory', 'Water Factory - Valley View University', 'VVU Ventures', 'Water Factory', 'Pure, Safe & Refreshing', '"Experience the purity of nature, bottled with care. Sourced and produced with the highest standards, right here at Valley View University."', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAXCKbNczcm4HaqAIa9kvYGLFZ-9n1fhskepGNxgC6vzqo-3HIDZuo8MrZLOL3FcREEpUA_RjP7A71CSoA-sHbq69DypseQqUlFwpL1RDLPmwx9sxTw0Dcer3b_PdfJwQ6p_gC9IB6QosIOnN1OAu03kGUHNY8K_q9msfPbYVWtFAE2gR6hJ2pvkvOjSTcBCelbhpJU5SuObA3H-JuQCNTvr9TeTOoc0hIiU28gdVCJ70TmrJCAtnzb-mJldTnwRXmesRwXkZdbJxBf', 'About Our Water Factory', 'Valley View University Water Factory is a university-led venture dedicated to providing our community with pure, safe, and refreshing drinking water. We combine modern technology with a commitment to sustainability and student empowerment, ensuring every drop meets the highest quality standards.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCNVcThmbACjczdDRt4TfSWR1JqS1mFmf2mHRjP8Z_r2aJ54-FqG8AvPlfo4wxUZHJrtKlFZXwtf6UOGJF4mr1MxINz6AeQ1w6lWDoaTqsJSlJ6feoufP_xfUXMw316Kcf4mhDEupaqHT02p0QGVXp0ZUyTnl1WaYthOVF9MOYgC9yJsfEqj4s3mqfpaSQUaf-KdmM6aphq5zFtRGFYg0GPm3fQEmjdyPyIJZtH_XpQZhM13dJNZXPmsomE34jy87D4drjSnX2FlSKh', 'images/Home_files/water-factory-ad-v5.jpg', 'Become a Distributor', 'Partner With Excellence', 'Join us in our mission to provide pure, refreshing water to communities everywhere. Partner with a brand built on trust and quality.', '+233 307 011 832', 'Mile 19 Off the Adenta-Dodowa Road, Valley View University, Oyibi', 'P.O. Box AF 595, Adentan, Ghana');

-- Water Factory Sections
INSERT INTO `ventures_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('water_factory', 'products', 'Our Premium Sachet Water', 'Pure, refreshing sachet water produced with the highest quality standards for your everyday hydration needs.', 1),
('water_factory', 'process', 'Our Purification Process', 'From natural source to sealed sachet, every step is meticulously managed for unparalleled purity.', 2),
('water_factory', 'quality', 'Commitment to Quality', 'We adhere to the strictest national and international standards to guarantee the safety and quality of every drop.', 3),
('water_factory', 'why_choose', 'Why Choose VVU Water?', 'Discover what sets our sachet water apart from the rest.', 4);

-- Water Products
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_stat_value`, `display_order`) VALUES
('water_factory', 'products', 'Premium Sachet', 'Our flagship product - pure, refreshing water in convenient sachet packaging for everyday use.', 'water_drop', 'cyan-500', 'Best Seller', 1),
('water_factory', 'products', 'Bulk Orders', 'Perfect for events, offices, and large gatherings. Special pricing available for bulk purchases.', 'inventory_2', 'blue-600', 'Events & Offices', 2),
('water_factory', 'products', 'Distribution Packs', 'Wholesale packages for retailers and distributors. Join our growing network of partners.', 'local_shipping', 'teal-500', 'Wholesale', 3);

-- Water Process Steps
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_stat_value`, `display_order`) VALUES
('water_factory', 'process', 'Natural Sourcing', 'Water sourced from protected, natural underground aquifers.', 'water', 'cyan-600', '01', 1),
('water_factory', 'process', 'Advanced Filtration', 'Multi-stage micro-filtration and reverse osmosis remove impurities.', 'filter_alt', 'cyan-600', '02', 2),
('water_factory', 'process', 'UV Sterilization', 'UV light chambers eliminate harmful microorganisms.', 'science', 'cyan-600', '03', 3),
('water_factory', 'process', 'Automated Packaging', 'State-of-the-art hygienic packaging and sealing process.', 'precision_manufacturing', 'cyan-600', '04', 4);

-- Water Quality Features
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('water_factory', 'quality', 'FDA Approved', 'Fully certified by the Food and Drugs Authority, meeting all regulatory requirements.', 'verified', 'cyan-600', 1),
('water_factory', 'quality', 'Regular Testing', 'Our in-house lab conducts hourly quality checks on every production batch.', 'labs', 'cyan-600', 2),
('water_factory', 'quality', 'Eco-Friendly', 'Recyclable materials and commitment to reducing environmental footprint.', 'eco', 'cyan-600', 3),
('water_factory', 'quality', 'Premium Quality', 'Essential minerals retained for optimal taste and health benefits.', 'workspace_premium', 'cyan-600', 4);

-- Water Why Choose Features
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('water_factory', 'why_choose', '100% Pure & Safe', 'Rigorous purification ensures every drop is safe for your family.', 'health_and_safety', 'cyan-600', 1),
('water_factory', 'why_choose', 'Student Empowerment', 'Provides hands-on learning and work-study opportunities for students.', 'school', 'cyan-600', 2),
('water_factory', 'why_choose', 'Wide Distribution', 'Available on campus and in surrounding communities for easy access.', 'local_shipping', 'cyan-600', 3),
('water_factory', 'why_choose', 'Affordable Pricing', 'Premium quality water at prices that won''t break the bank.', 'payments', 'cyan-600', 4),
('water_factory', 'why_choose', 'Community Focused', 'Dedicated to serving the university and local community with excellence.', 'support_agent', 'cyan-600', 5),
('water_factory', 'why_choose', 'Consistent Supply', 'Reliable production ensures you always have access to pure water.', 'schedule', 'cyan-600', 6);

-- Water Stats
INSERT INTO `ventures_pages_stats` (`page_key`, `stat_value`, `stat_label`, `display_order`) VALUES
('water_factory', 'FDA', 'Certified', 1),
('water_factory', '100%', 'Pure Water', 2),
('water_factory', '24/7', 'Production', 3);

-- =============================================
-- GROCERY DATA
-- =============================================
INSERT INTO `ventures_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `about_heading`, `about_text`, `about_image`, `cta_heading`, `cta_subtitle`, `cta_text`, `contact_phone`, `contact_location`, `contact_hours`) VALUES
('grocery', 'VVU Grocery - Valley View University', 'VVU Ventures', 'VVU Grocery', 'Fresh, Local & Affordable', '"Your one-stop shop for quality groceries, fresh produce, and everyday essentials right here on campus."', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA4_CBSSL5nHq2EzPsCCCygkYtflj5uczsGsgys1TK3LjWONorSHLxQoKVgAsBSEOoBROdHvZqi7pAGGnE85MsrBbHnUZgGJhR9Sm6gW3Bx_0qKz4OEz1RpohP8qCl2-Yaoe8moV9Bzx4CKwRfzQKI2YuklVK60VRuaRLzHAMK1QVXYlNskhEIluTrrD43adXvA99CwjT0nkNmU2uyJMkgeINdcWkJK4G0qc-Qds58hSk1JPC4q74I7Id-PNUm58RMcG74LmAg4hdjE', 'About VVU Grocery', 'The VVU Grocery is more than just a store - it''s a commitment to our community. We provide accessible, healthy, and affordable food options right here on campus. By partnering with local farmers and suppliers, we ensure the freshest quality while supporting our regional economy.', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAP8IpfRZwT_gQOeutqxLhTTIakrWterHBROopbMKiI2eEcsfcgHzdM3jRdk3KzMbja-IgYBKr9OZMIilH8VThjqIfRErY9PFvBg5FEwNoXNd1UCYct7YKwypuzCTSsM06Kz7hvpauhhh06gzDd6B39rugdFB2gfQDa9HYqdPOFfDpzj7wuTwOfo1uVIq_jbnU66qbzJjS3JsLxX6UVgfyfH3BwZw0t9IoaeRFMPD19nc5fYRw5Bl__q9pWxd5L99w64EvQujwjiL-J', 'Visit Us Today', 'Fresh Deals Await!', 'Stop by the VVU Grocery for fresh products, great prices, and friendly service. Your campus convenience store!', '+233 307 011 832', 'Valley View University Campus, Oyibi', 'Monday - Friday: 9:00 AM - 5:00 PM, Closed on Weekends');

-- Grocery Sections
INSERT INTO `ventures_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('grocery', 'why_shop', 'Why Shop With Us?', 'Experience the VVU Grocery difference - quality, convenience, and community.', 1),
('grocery', 'categories', 'Explore Our Aisles', 'From fresh produce to pantry essentials, we have everything you need.', 2),
('grocery', 'additional_categories', 'More Categories', '', 3),
('grocery', 'mission', 'Our Mission', 'The VVU Grocery is dedicated to providing accessible, healthy, and affordable food options right here on campus. We believe that good nutrition is fundamental to academic success.', 4);

-- Grocery Why Shop items
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `item_stat_value`, `display_order`) VALUES
('grocery', 'why_shop', 'Locally Sourced', 'Fresh products from local farms and trusted partners, supporting our regional economy.', 'local_florist', 'emerald-500', 'Farm Fresh', 1),
('grocery', 'why_shop', 'Affordable Prices', 'Quality products at competitive prices. We believe everyone deserves access to good food.', 'savings', 'yellow-500', 'Best Value', 2),
('grocery', 'why_shop', 'Community Focused', 'A student-run venture that invests back into our campus and provides valuable work experience.', 'diversity_3', 'teal-500', 'Work-Study', 3);

-- Grocery Categories
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_subtitle`, `item_icon`, `item_color`, `item_image`, `display_order`) VALUES
('grocery', 'categories', 'Fresh Produce', 'Fruits & Vegetables', 'nutrition', 'emerald-500', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCX7EhjQ2TI8wvhBihRXcvbAuI1W5AxseOx-zdNdgwWer6YJeM22fJMCdCF31FqmHJYbIoR346OI-psEisLhYNg6M4Jok6LlX_ZB0FSxTpfj6xMOJ0ExoQIdd4-_NwWA7vFmM7v55wTJL8PhzZOEvduI1S1bFFDEN_2opfnq0QfMbxtxj-7iQyshpIu8hU7hl7Jv39Cr1GssSwzRYhi-CIxsH3bUs_JogNNkLOhdO423fF3up9bzIfxnrLDZ8Bugc6PbsGcFSBBWF8s', 1),
('grocery', 'categories', 'Bakery', 'Fresh Breads & Pastries', 'bakery_dining', 'yellow-500', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCom-jjOoJKc1VnEWFmd2FNNg2dgz0-5UCaG2bthnA8UWQ_9xRxY0kRTGqhBOuzSD4__1DaNaYMDxOjFKu3OHRInuSHb6zBOM8M6zNXG0z6UI1T1zPOzkeVm5Ghwrxl7O1rSeWPMzD-aHuem1Wjrwqv6goalgkH9NA1MZ0DmZVO7194cSksUQgsrdE2EroXF1Rr-IY-Oiz7vXryfLON0iBLjLLCH_lEISdQK9cdRhSOM6rAXgUasJW2reavZ-2y5XzKc-NUz7mwtXqa', 2),
('grocery', 'categories', 'Dairy & Eggs', 'Milk, Cheese & More', 'egg', 'blue-500', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBrPboIHC8V_tF5yY0yIl7WUKKiQH1Yhr2tkTcJy7l5ODA4PHd33tIHsPIejbIwYPmAX6zdEjsRlvci3FfkqKwUenQYGg3xUvz6XJuMo2vVFlEWVh_cPQUFdSI8cvjiD6JA1sCyys31zUm7myYa4OwJCOGzQx1lB7Pg_DkHY_a_0YSKlkhIWcDeiMqbZ2xf-O_1NgcYAYakUWvmvEs0KyK1x1X4wc-qdQl89ISYalPx4gN8Wq7rkhubbC7X5oIyyGh6C8oixzca5B8G', 3),
('grocery', 'categories', 'Pantry', 'Essential Staples', 'kitchen', 'orange-500', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAAQ6g71PhKLKKYEqnALMHGbcZ5ftVN-wUNPKIpFSjgeszcyUap0iCHb7k-03tixjAZW5iZQ6N2A3ZAuyvpihY_ClvkjVDN9bNqCf_bZhpm2ZolyOnC2tMZHadjzjHS8h75Q13EfS-4QOhLHFSolW7WX1SPQ5Xzftwrt6gYCR7yjTn3LIZBdM__BHnosn0E9mWJAEuGtR9pAcPaxwz-qmWmp2y9iIHRQ_eHl0381ahNcitF2eWY1e2TIDE6oN14qNq8s7eGAyw6kdBD', 4);

-- Grocery Additional Categories
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('grocery', 'additional_categories', 'Beverages', 'Juices, soft drinks, water, and refreshments for every occasion.', 'local_drink', 'emerald-500', 1),
('grocery', 'additional_categories', 'Snacks & Treats', 'Chips, cookies, candies, and delicious treats for your cravings.', 'icecream', 'emerald-500', 2),
('grocery', 'additional_categories', 'Household Items', 'Cleaning supplies, toiletries, and essential household products.', 'cleaning_services', 'emerald-500', 3);

-- Grocery Mission Features
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('grocery', 'mission', 'Quality Assured', 'All products meet strict quality and freshness standards.', 'verified', 'emerald-500', 1),
('grocery', 'mission', 'Giving Back', 'Proceeds support university programs and student activities.', 'volunteer_activism', 'emerald-500', 2);

-- Grocery Stats
INSERT INTO `ventures_pages_stats` (`page_key`, `stat_value`, `stat_label`, `display_order`) VALUES
('grocery', 'Fresh', 'Daily Produce', 1),
('grocery', 'Local', 'Farm Partners', 2),
('grocery', '5 Days', 'Mon - Fri', 3);

-- =============================================
-- POST OFFICE DATA
-- =============================================
INSERT INTO `ventures_pages_content` (`page_key`, `page_title`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `hero_image`, `about_heading`, `about_text`, `cta_heading`, `cta_text`, `contact_phone`, `contact_email`, `contact_location`, `contact_hours`, `extra_field_1`) VALUES
('post_office', 'University Post Office - Valley View University', 'Campus Essential Services', 'University Post Office', 'Global Connections, Campus Convenience — Your link to the world of communication and finance.', '', 'https://images.unsplash.com/photo-1566847438217-76e82d383f84?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80', 'Post Office & Financial Hub', 'The Valley View University Post Office is a full-service postal agency operated in partnership with Ghana Post. We provide the campus community with reliable domestic and international mail services, secure money transfers, and professional courier solutions.', 'Need Assistance?', 'Visit our branch located on the main campus for all your postage and financial transaction needs.', '+233 307 011 832', 'postoffice@vvu.edu.gh', 'Valley View University Main Campus, Near the Student Center Building.', 'Monday - Thursday: 8:00 AM - 5:00 PM, Friday: 8:00 AM - 1:00 PM, Saturday & Sunday: Closed', 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80');

-- Post Office Sections
INSERT INTO `ventures_pages_sections` (`page_key`, `section_key`, `section_title`, `section_subtitle`, `display_order`) VALUES
('post_office', 'services', 'Our Solutions', 'Explore the range of postal and financial services designed to keep you connected.', 1),
('post_office', 'financial', 'Global Money Transfers', 'Safe, fast, and secure financial transactions right on campus.', 2);

-- Post Office Services
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_icon`, `item_color`, `display_order`) VALUES
('post_office', 'services', 'Domestic & International Mail', 'Reliable delivery of letters and documents across Ghana and to over 200 countries worldwide.', 'mail', 'blue', 1),
('post_office', 'services', 'EMS Ghana (Priority Mail)', 'The fastest courier service for your urgent documents and parcels with end-to-end tracking.', 'electric_bolt', 'yellow', 2),
('post_office', 'services', 'PO Box Rentals', 'Secure and private mail boxes available for students and staff (e.g., P.O. Box 30-3350) to receive personal mail safely.', 'inbox', 'purple', 3),
('post_office', 'services', 'Parcel & Logistics', 'Efficient handling and delivery of larger packages (SpeedLink) and bulk mail solutions.', 'package_2', 'orange', 4);

-- Post Office Financial Services
INSERT INTO `ventures_pages_items` (`page_key`, `section_key`, `item_title`, `item_description`, `item_color`, `item_stat_value`, `item_stat_label`, `display_order`) VALUES
('post_office', 'financial', 'Western Union', 'Receive or send money globally with one of the most trusted names in financial services.', 'bg-[#FFCC00]', 'text-[#000000]', 'Western Union', 1),
('post_office', 'financial', 'MoneyGram', 'Fast, reliable international money transfers with thousands of locations worldwide.', 'bg-[#E11B22]', 'text-white', 'MoneyGram', 2),
('post_office', 'financial', 'Cash Post', 'Ghana Post''s own reliable domestic money transfer service for secure local transactions.', 'bg-blue-600', 'text-white', 'Cash Post', 3);

-- Post Office Stats
INSERT INTO `ventures_pages_stats` (`page_key`, `stat_value`, `stat_label`, `display_order`) VALUES
('post_office', '3000+', 'Mail Boxes', 1),
('post_office', '24/7', 'Security', 2);

-- =============================================
-- VVU RADIO DATA (if not already using radio_content table)
-- We keep the existing radio tables as-is
-- =============================================
-- Radio is already database-driven via radio_content, radio_programs, radio_features tables
