-- Ensure categories table exists with expected columns
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
  -- Note: Removed unique constraint on categories.name to allow multiple categories with same name
  -- (e.g., "Chicken - Main", "Chicken - Appetizer", etc.)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure items table has expected columns used by the app
-- Add missing columns if they do not exist
SET @db := DATABASE();

-- description
SELECT COUNT(*) INTO @exists_desc
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA=@db AND TABLE_NAME='items' AND COLUMN_NAME='description';
SET @sql := IF(@exists_desc=0,
  'ALTER TABLE `items` ADD COLUMN `description` TEXT NULL AFTER `name`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- status
SELECT COUNT(*) INTO @exists_status
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA=@db AND TABLE_NAME='items' AND COLUMN_NAME='status';
SET @sql := IF(@exists_status=0,
  'ALTER TABLE `items` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT ''Available'' AFTER `price`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- category_id
SELECT COUNT(*) INTO @exists_cat
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA=@db AND TABLE_NAME='items' AND COLUMN_NAME='category_id';
SET @sql := IF(@exists_cat=0,
  'ALTER TABLE `items` ADD COLUMN `category_id` INT(11) NULL AFTER `status`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- deleted
SELECT COUNT(*) INTO @exists_deleted
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA=@db AND TABLE_NAME='items' AND COLUMN_NAME='deleted';
SET @sql := IF(@exists_deleted=0,
  'ALTER TABLE `items` ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `category_id`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- image
SELECT COUNT(*) INTO @exists_image
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA=@db AND TABLE_NAME='items' AND COLUMN_NAME='image';
SET @sql := IF(@exists_image=0,
  'ALTER TABLE `items` ADD COLUMN `image` LONGBLOB NULL AFTER `deleted`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add indexes/constraints expected by code
-- Note: Removed unique constraint on items.name to allow multiple items with same name
-- (e.g., "Chicken Breast", "Chicken Wings", "Chicken Curry", etc.)

-- Category relationship (optional; keep NULL to avoid breaking existing data)
-- Uncomment if you want to enforce referential integrity and you have valid category IDs
-- ALTER TABLE `items`
--   ADD CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Note: Run this script against the `food` database.


