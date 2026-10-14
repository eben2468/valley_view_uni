-- =====================================================================
--  VVU Scholar — researcher accounts ("Claim Your Profile")
--
--  Lets VVU researchers sign up with their @vvu.edu.gh email, claim their
--  profile in the research portal (or ask for a new one), and — once the
--  Research Office approves the claim — manage their own profile and
--  publications at research/account.php.
--
--    research_accounts       one login per researcher
--    research_claims         "this profile is mine" requests to review
--    research_activity       what researchers changed, for the Office
--    research_publications.added_by_account   who added a paper by hand
--
--  Also points the "Claim Your Profile" button at research/claim.php.
--
--  PORTABLE (MySQL 5.7 / 8.x and MariaDB), no DELIMITER, safe to re-run.
--  Run AFTER the research portal schema (already on the live site).
-- =====================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `research_accounts` (
  `id`               int(11) NOT NULL AUTO_INCREMENT,
  `email`            varchar(190) NOT NULL,
  `full_name`        varchar(200) NOT NULL,
  `password_hash`    varchar(255) NOT NULL,
  `scholar_id`       int(11) DEFAULT NULL COMMENT 'Set when the claim is approved',
  `status`           enum('pending','active','rejected','suspended') NOT NULL DEFAULT 'pending',
  `reset_token_hash` char(64) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `failed_logins`    int(11) NOT NULL DEFAULT 0,
  `locked_until`     datetime DEFAULT NULL,
  `last_login_at`    datetime DEFAULT NULL,
  `created_at`       timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`       timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_research_account_email` (`email`),
  KEY `idx_research_account_scholar` (`scholar_id`),
  KEY `idx_research_account_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `research_claims` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `account_id`  int(11) NOT NULL,
  `scholar_id`  int(11) DEFAULT NULL COMMENT 'NULL = "I am not listed, create my profile"',
  `position`    varchar(200) DEFAULT NULL,
  `unit_id`     int(11) DEFAULT NULL,
  `orcid`       varchar(40) DEFAULT NULL,
  `message`     text DEFAULT NULL,
  `status`      enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note`  varchar(500) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at`  timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_research_claim_status` (`status`),
  KEY `idx_research_claim_account` (`account_id`),
  KEY `idx_research_claim_scholar` (`scholar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `research_activity` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `scholar_id` int(11) DEFAULT NULL,
  `action`     varchar(60) NOT NULL,
  `detail`     varchar(600) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_research_activity_scholar` (`scholar_id`),
  KEY `idx_research_activity_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- research_publications.added_by_account — lets a researcher edit the
-- papers they typed in themselves (imported ones stay as imported).
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME   = 'research_publications'
                   AND COLUMN_NAME  = 'added_by_account');
SET @sql := IF(@exists = 0,
  'ALTER TABLE `research_publications` ADD COLUMN `added_by_account` int(11) DEFAULT NULL AFTER `external_id`',
  'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Settings (only added when missing, so admin edits survive a re-run).
INSERT INTO `research_settings` (`setting_key`, `setting_value`)
SELECT 'claim_email_domains', 'vvu.edu.gh' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `research_settings` WHERE `setting_key` = 'claim_email_domains');

INSERT INTO `research_settings` (`setting_key`, `setting_value`)
SELECT 'claim_notify_email', '' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `research_settings` WHERE `setting_key` = 'claim_notify_email');

-- The CTA button: from the contact page to the new claim page.
UPDATE `research_settings` SET `setting_value` = 'research/claim.php'
 WHERE `setting_key` = 'cta_button_link'
   AND (`setting_value` IS NULL OR `setting_value` IN ('', '#', 'contact_us.php', 'contact_us'));
