-- =====================================================================
--  VVU Scholar — data quality, impact metrics and automatic updates
--
--  1. research_scholars.merged_into — one person split into several
--     profiles is merged; the old profiles point at the kept one, so
--     imports map onto it and old links redirect.
--  2. research_author_exclusions — "this person is not an author of this
--     paper" (non-VVU affiliation, or a researcher's "Not mine"), so a
--     re-import never links them again.
--     research_scholar_not_same — profiles an editor said must NOT merge.
--  3. Impact metrics: reads, recommendations, ratings & reviews (from
--     visitors of the portal) plus readers, Altmetric attention, FWCI and
--     citation percentile (from Altmetric / OpenAlex).
--  4. research_sync_log + settings for the automatic update.
--
--  After importing, open Admin → Research Portal → Data Quality and press
--  "Run clean-up" once.
--
--  PORTABLE (MySQL 5.7 / 8.x, MariaDB), no DELIMITER, safe to re-run.
-- =====================================================================

SET NAMES utf8mb4;

-- Helper pattern: add a column only when it is missing.
-- ---------------------------------------------------------------------
SET @t := 'research_scholars', @c := 'merged_into',
    @d := 'int(11) DEFAULT NULL AFTER `is_active`';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_scholars', @c := 'views', @d := 'int(11) NOT NULL DEFAULT 0';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_scholars', @c := 'works_synced_at', @d := 'datetime DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'views', @d := 'int(11) NOT NULL DEFAULT 0';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'recommendations', @d := 'int(11) NOT NULL DEFAULT 0';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'rating_sum', @d := 'int(11) NOT NULL DEFAULT 0';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'rating_count', @d := 'int(11) NOT NULL DEFAULT 0';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'readers', @d := 'int(11) DEFAULT NULL COMMENT ''Mendeley readers (via Altmetric)''';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'altmetric_score', @d := 'decimal(10,2) DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'fwci', @d := 'decimal(8,2) DEFAULT NULL COMMENT ''Field-weighted citation impact (OpenAlex)''';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'citation_percentile', @d := 'decimal(5,2) DEFAULT NULL COMMENT ''0-100, vs. same field and year (OpenAlex)''';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'metrics_synced_at', @d := 'datetime DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- DOI look-ups are now frequent (duplicate checks on every import).
SET @sql := IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'research_publications' AND INDEX_NAME = 'idx_research_pub_doi') = 0,
  'ALTER TABLE `research_publications` ADD KEY `idx_research_pub_doi` (`doi`)', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `research_author_exclusions` (
  `publication_id` int(11) NOT NULL,
  `scholar_id`     int(11) NOT NULL,
  `reason`         varchar(255) DEFAULT NULL,
  `created_at`     timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`publication_id`, `scholar_id`),
  KEY `idx_rae_scholar` (`scholar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `research_scholar_not_same` (
  `scholar_a`  int(11) NOT NULL,
  `scholar_b`  int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`scholar_a`, `scholar_b`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Every merge, with what moved, so the Research Office can undo it.
CREATE TABLE IF NOT EXISTS `research_scholar_merges` (
  `id`              int(11) NOT NULL AUTO_INCREMENT,
  `keep_id`         int(11) NOT NULL,
  `merged_id`       int(11) NOT NULL,
  `moved_links`     text DEFAULT NULL COMMENT 'publication ids whose authorship moved',
  `moved_leads`     text DEFAULT NULL COMMENT 'publication ids whose lead author moved',
  `merged_by`       varchar(20) NOT NULL DEFAULT 'auto',
  `undone_at`       datetime DEFAULT NULL,
  `created_at`      timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_merge_keep` (`keep_id`),
  KEY `idx_merge_merged` (`merged_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- One row per visitor, publication, kind and day — so a refresh is not a
-- second read, and a visitor can recommend a paper only once.
CREATE TABLE IF NOT EXISTS `research_publication_hits` (
  `publication_id` int(11) NOT NULL,
  `visitor`        char(40) NOT NULL,
  `kind`           enum('view','recommend','scholar_view') NOT NULL,
  `day`            date NOT NULL,
  PRIMARY KEY (`publication_id`, `visitor`, `kind`, `day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `research_publication_reviews` (
  `id`             int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL,
  `reviewer_name`  varchar(120) NOT NULL,
  `reviewer_role`  varchar(120) DEFAULT NULL,
  `rating`         tinyint(4) NOT NULL,
  `comment`        text DEFAULT NULL,
  `status`         enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `visitor`        char(40) NOT NULL,
  `created_at`     timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_review_visitor` (`publication_id`, `visitor`),
  KEY `idx_review_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `research_sync_log` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `trigger_by`  varchar(20) NOT NULL DEFAULT 'auto',
  `started_at`  datetime NOT NULL,
  `finished_at` datetime DEFAULT NULL,
  `ok`          tinyint(1) NOT NULL DEFAULT 0,
  `summary`     text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sync_started` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Settings (added only when missing)
INSERT INTO `research_settings` (`setting_key`, `setting_value`)
SELECT 'auto_sync_enabled', '1' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `research_settings` WHERE `setting_key` = 'auto_sync_enabled');

INSERT INTO `research_settings` (`setting_key`, `setting_value`)
SELECT 'auto_sync_hours', '24' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `research_settings` WHERE `setting_key` = 'auto_sync_hours');

INSERT INTO `research_settings` (`setting_key`, `setting_value`)
SELECT 'auto_sync_key', SHA2(CONCAT(RAND(), NOW(), UUID()), 256) FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `research_settings` WHERE `setting_key` = 'auto_sync_key');

INSERT INTO `research_settings` (`setting_key`, `setting_value`)
SELECT 'reviews_require_approval', '1' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `research_settings` WHERE `setting_key` = 'reviews_require_approval');

INSERT INTO `research_settings` (`setting_key`, `setting_value`)
SELECT 'auto_sync_orcid_scope', 'claimed' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `research_settings` WHERE `setting_key` = 'auto_sync_orcid_scope');
