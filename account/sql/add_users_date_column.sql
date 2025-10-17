-- Migration script to add date column to users table
-- Run this script if you have an existing installation without the date column

-- Add date column to users table
ALTER TABLE `users` ADD COLUMN `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Update existing records to have a default date (optional - you can customize this)
-- UPDATE `users` SET `date` = NOW() WHERE `date` IS NULL;
