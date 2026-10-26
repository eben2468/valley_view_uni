-- =====================================================================
--  VVU Scholar — verified citations and complete publication lists
--
--  Each paper keeps the citation count from every source it was checked
--  against, and "citations" is the highest of them:
--      cites_openalex  (OpenAlex cited_by_count)
--      cites_crossref  (Crossref is-referenced-by-count)
--      cites_s2        (Semantic Scholar citationCount)
--  plus Semantic Scholar's "influential citations" and the citations
--  received in the last five years (OpenAlex counts_by_year), which feed
--  the 5-year citation and h-index figures on researcher profiles.
--
--  vvu_output = 0 marks a researcher's work published elsewhere (before
--  joining VVU, say). It shows on their profile but is not counted in the
--  University's own totals.
--
--  PORTABLE (MySQL 5.7 / 8.x, MariaDB), no DELIMITER, safe to re-run.
--  Run AFTER sql/research_quality_migration.sql.
-- =====================================================================

SET NAMES utf8mb4;

SET @t := 'research_publications', @c := 'cites_openalex', @d := 'int(11) DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'cites_crossref', @d := 'int(11) DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'cites_s2', @d := 'int(11) DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'influential_citations', @d := 'int(11) DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'citations_5y', @d := 'int(11) DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'citations_synced_at', @d := 'datetime DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_publications', @c := 'vvu_output', @d := 'tinyint(1) NOT NULL DEFAULT 1 COMMENT ''0 = a researcher''''s work published elsewhere''';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_scholars', @c := 'influential_citations', @d := 'int(11) NOT NULL DEFAULT 0';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @t := 'research_scholars', @c := 'citations_per_year', @d := 'text DEFAULT NULL COMMENT ''JSON {year: citations}''';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Citations received per year, per paper (OpenAlex counts_by_year), for the
-- "citations by year" chart and the 5-year figures.
CREATE TABLE IF NOT EXISTS `research_citations_by_year` (
  `publication_id` int(11) NOT NULL,
  `year`           smallint(6) NOT NULL,
  `citations`      int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`publication_id`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Citations typed in from Google Scholar (by the researcher or the Research
-- Office) for papers no open index covers; "citations" takes the highest.
SET @t := 'research_publications', @c := 'cites_manual', @d := 'int(11) DEFAULT NULL';
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @t AND COLUMN_NAME = @c) = 0,
  CONCAT('ALTER TABLE `', @t, '` ADD COLUMN `', @c, '` ', @d), 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
