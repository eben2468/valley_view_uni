-- Add profile fields to admins table if they don't exist
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE ADD COLUMN before version 8.0.13
-- Use these statements one at a time if you get errors

ALTER TABLE `admins` 
ADD COLUMN `name` VARCHAR(255) NULL AFTER `id`,
ADD COLUMN `phone` VARCHAR(20) NULL,
ADD COLUMN `bio` TEXT NULL,
ADD COLUMN `last_login` TIMESTAMP NULL,
ADD COLUMN `profile_picture` VARCHAR(255) NULL;

-- If columns already exist, run these individually to add only missing ones:
-- ALTER TABLE `admins` ADD COLUMN `name` VARCHAR(255) NULL AFTER `id`;
-- ALTER TABLE `admins` ADD COLUMN `phone` VARCHAR(20) NULL;
-- ALTER TABLE `admins` ADD COLUMN `bio` TEXT NULL;
-- ALTER TABLE `admins` ADD COLUMN `last_login` TIMESTAMP NULL;
-- ALTER TABLE `admins` ADD COLUMN `profile_picture` VARCHAR(255) NULL;
