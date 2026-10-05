-- ---------------------------------------------------------------------------
-- Valley View University — Discover More card descriptions
--
-- Adds an editable short description to each homepage "Discover More" card
-- (shown in small grey text under the card title) and fills the existing
-- cards with the lines the homepage currently shows.
--
-- How to run: in phpMyAdmin, select the site's database, open the Import
-- (or SQL) tab and run this file. It is safe to run more than once:
--   * the column is only added if it does not exist yet
--   * descriptions are only filled where a card has none, so text already
--     edited in the admin panel is never overwritten
-- Works on MySQL 5.7+/8.x and MariaDB 10.x.
-- ---------------------------------------------------------------------------

SET @vvu_has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'homepage_discover_cards'
      AND COLUMN_NAME = 'description'
);

SET @vvu_sql := IF(
    @vvu_has_col = 0,
    'ALTER TABLE `homepage_discover_cards` ADD COLUMN `description` VARCHAR(255) NULL DEFAULT NULL AFTER `title`',
    'SELECT ''description column already exists'' AS info'
);

PREPARE vvu_stmt FROM @vvu_sql;
EXECUTE vvu_stmt;
DEALLOCATE PREPARE vvu_stmt;

-- Fill existing cards (matched by keywords in the title, first match wins),
-- only where no description has been set yet.
UPDATE `homepage_discover_cards`
SET `description` = CASE
        WHEN LOWER(`title`) LIKE '%admission%' THEN 'Entry requirements and how to apply'
        WHEN LOWER(`title`) LIKE '%academic%'  THEN 'Schools, faculties and programmes'
        WHEN LOWER(`title`) LIKE '%student%'   THEN 'Clubs, halls and campus community'
        WHEN LOWER(`title`) LIKE '%research%'  THEN 'Projects, publications and units'
        WHEN LOWER(`title`) LIKE '%faculty%'   THEN 'Find lecturers and staff contacts'
        WHEN LOWER(`title`) LIKE '%library%'   THEN 'Books, journals and e-resources'
        WHEN LOWER(`title`) LIKE '%campus%'    THEN 'Our grounds, halls and facilities'
        WHEN LOWER(`title`) LIKE '%facilit%'   THEN 'Our grounds, halls and facilities'
        WHEN LOWER(`title`) LIKE '%event%'     THEN 'What''s happening around VVU'
        WHEN LOWER(`title`) LIKE '%news%'      THEN 'What''s happening around VVU'
        WHEN LOWER(`title`) LIKE '%graduate%'  THEN 'Postgraduate study and research degrees'
        WHEN LOWER(`title`) LIKE '%history%'   THEN 'Our story, heritage and milestones'
        WHEN LOWER(`title`) LIKE '%sport%'     THEN 'Teams, fitness and recreation'
        WHEN LOWER(`title`) LIKE '%alumni%'    THEN 'Stay connected with VVU'
        WHEN LOWER(`title`) LIKE '%contact%'   THEN 'Get in touch with the university'
        ELSE `description`
    END
WHERE `description` IS NULL OR `description` = '';
