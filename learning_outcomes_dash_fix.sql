-- ===========================================================================
-- Learning Outcomes: restore em dashes lost in an earlier import
--
-- Four descriptions on learning_outcomes.php show "???" where an em dash
-- (—) belongs, e.g. "learning???a passion" -> "learning—a passion".
-- Safe to import more than once: rows without "???" are not touched.
-- ===========================================================================

SET NAMES utf8mb4;

UPDATE `academic_pages_items`
   SET `item_description` = REPLACE(`item_description`, '???', '—')
 WHERE `page_key` = 'learning_outcomes'
   AND `item_description` LIKE '%???%';
