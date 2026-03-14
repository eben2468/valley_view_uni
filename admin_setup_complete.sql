-- Complete Admin Table Setup
-- Run this in phpMyAdmin to add all necessary columns to admin_users table

-- First, let's see your current structure
-- SHOW COLUMNS FROM admin_users;

-- Add missing columns one by one (ignore errors if column already exists)

-- Add email column (if not exists)
ALTER TABLE `admin_users` ADD COLUMN `email` VARCHAR(255) NULL;

-- Add phone column  
ALTER TABLE `admin_users` ADD COLUMN `phone` VARCHAR(20) NULL;

-- Add bio column
ALTER TABLE `admin_users` ADD COLUMN `bio` TEXT NULL;

-- Add last_login column
ALTER TABLE `admin_users` ADD COLUMN `last_login` TIMESTAMP NULL;

-- Add profile_picture column
ALTER TABLE `admin_users` ADD COLUMN `profile_picture` VARCHAR(255) NULL;

-- Add created_at column (if not exists)
ALTER TABLE `admin_users` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- If you get "Duplicate column name" errors, that's OK - it means the column already exists
-- Just continue with the next statement
