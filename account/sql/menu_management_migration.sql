-- Menu Management Database Migration
-- Adds required fields for menu management functionality

-- Add new columns to items table
ALTER TABLE `items` 
ADD COLUMN `description` TEXT NULL AFTER `name`,
ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'Available' AFTER `price`,
ADD COLUMN `category_id` INT(11) NULL AFTER `status`;

-- Create categories table
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert some default categories
INSERT INTO `categories` (`name`) VALUES 
('Appetizers'),
('Main Course'),
('Desserts'),
('Beverages'),
('Salads');

-- Add foreign key constraint for category_id
ALTER TABLE `items` 
ADD CONSTRAINT `items_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

-- Update existing items to have a default category (Main Course)
UPDATE `items` SET `category_id` = 2 WHERE `category_id` IS NULL;
