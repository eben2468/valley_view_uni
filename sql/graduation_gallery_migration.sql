-- =====================================================================
--  Gallery CMS — Graduation 2026 gallery  (gallery_key = 'graduation')
--
--  1. gallery_albums.external_url — a link to the full album (Google
--     Photos, Drive, Flickr…). The site shows a few compressed photos per
--     album and a "View all photos" button that opens this link.
--  2. Page copy + stat counters for graduation_gallery.php.
--  3. "Graduation 2026" link in every menu section titled "Gallery".
--
--  Run AFTER sql/gallery_page_schema.sql and sql/gallery_multi_migration.sql
--  (both are already on any server that shows the SRC Gallery).
--
--  PORTABLE (MySQL 5.7 / 8.x and MariaDB), no DELIMITER, and safe to
--  re-run: every step checks whether it has already been applied.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. gallery_albums.external_url
-- ---------------------------------------------------------------------
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME   = 'gallery_albums'
                   AND COLUMN_NAME  = 'external_url');
SET @sql := IF(@exists = 0,
  'ALTER TABLE `gallery_albums` ADD COLUMN `external_url` varchar(1000) DEFAULT NULL AFTER `cover_image`',
  'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- 2. Page copy for the Graduation 2026 gallery (only if missing, so
--    edits made in the admin are never overwritten by a re-run).
-- ---------------------------------------------------------------------
INSERT INTO `gallery_page_content`
  (`page_key`, `intro_heading`, `intro_text`,
   `albums_heading`, `albums_subheading`, `all_filter_label`,
   `search_placeholder`, `empty_state_text`,
   `stats_heading`, `stats_text`,
   `cta_heading`, `cta_text`,
   `cta_1_text`, `cta_1_link`, `cta_1_icon`,
   `cta_2_text`, `cta_2_link`, `cta_2_icon`,
   `detail_back_label`, `detail_photos_label`, `status`)
SELECT 'graduation',
  'Celebrating the Class of 2026',
  'Relive the 2026 graduation ceremonies of Valley View University. Each album opens with a selection of highlights, and the full collection of photos is one click away.',
  'Graduation Albums',
  'Open an album to view its highlights, or use “View all photos” to see and download every picture.',
  'All Albums',
  'Search graduation albums…',
  'No albums match your search yet.',
  'Graduation 2026',
  '',
  'Congratulations, Class of 2026',
  'Your hard work, faith and perseverance have brought you here. We are proud of every graduate and excited for the future you will build.',
  'Main Gallery', 'gallery.php', 'photo_library',
  'Apply to VVU', 'apply.php', 'school',
  'Back to all albums', 'photos in this album', 'active'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `gallery_page_content` WHERE `page_key` = 'graduation');

-- ---------------------------------------------------------------------
-- 3. Stat counters (counted live from the albums/photos you publish).
-- ---------------------------------------------------------------------
INSERT INTO `gallery_stats` (`gallery_key`, `icon`, `value_text`, `label`, `auto_source`, `display_order`, `status`)
SELECT 'graduation', 'collections', '0', 'Albums', 'albums', 1, 'active' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `gallery_stats` WHERE `gallery_key` = 'graduation' AND `auto_source` = 'albums');

INSERT INTO `gallery_stats` (`gallery_key`, `icon`, `value_text`, `label`, `auto_source`, `display_order`, `status`)
SELECT 'graduation', 'photo_library', '0', 'Highlight Photos', 'photos', 2, 'active' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `gallery_stats` WHERE `gallery_key` = 'graduation' AND `auto_source` = 'photos');

INSERT INTO `gallery_stats` (`gallery_key`, `icon`, `value_text`, `label`, `auto_source`, `display_order`, `status`)
SELECT 'graduation', 'school', '2026', 'Graduating Class', 'none', 3, 'active' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `gallery_stats` WHERE `gallery_key` = 'graduation' AND `label` = 'Graduating Class');

-- ---------------------------------------------------------------------
-- 4. Menu: add "Graduation 2026" under every "Gallery" menu section,
--    placed after the links already there. Skipped where it exists.
-- ---------------------------------------------------------------------
INSERT INTO `navigation_links` (`section_id`, `title`, `url`, `target`, `sort_order`, `is_active`)
SELECT s.`id`, 'Graduation 2026', 'graduation_gallery.php', '_self',
       COALESCE((SELECT MAX(l2.`sort_order`) FROM `navigation_links` l2 WHERE l2.`section_id` = s.`id`), 0) + 1,
       1
  FROM `navigation_sections` s
 WHERE s.`section_title` = 'Gallery'
   AND NOT EXISTS (SELECT 1 FROM `navigation_links` l
                    WHERE l.`section_id` = s.`id` AND l.`url` = 'graduation_gallery.php');
