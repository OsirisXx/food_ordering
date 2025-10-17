-- Remove unique constraint from existing categories table
-- Since the table exists with foreign key constraints, we'll modify it instead of dropping

-- Step 1: Remove the foreign key constraint first
ALTER TABLE `items` DROP FOREIGN KEY `items_category_fk`;

-- Step 2: Remove any unique constraints on categories.name
-- First check what indexes exist, then remove them one by one
-- Try common constraint names (run these one at a time if you get errors)

-- Remove constraint named 'name' (if it exists)
ALTER TABLE `categories` DROP INDEX `name`;

-- Remove constraint named 'uniq_categories_name' (if it exists)  
ALTER TABLE `categories` DROP INDEX `uniq_categories_name`;

-- Step 3: Re-add the foreign key constraint (without unique constraint)
ALTER TABLE `items` 
ADD CONSTRAINT `items_category_fk` 
FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Step 4: Verify the changes
SELECT '=== CATEGORIES TABLE STRUCTURE ===' as info;
DESCRIBE categories;

SELECT '=== CATEGORIES INDEXES ===' as info;
SHOW INDEX FROM categories WHERE Column_name = 'name';

SELECT '=== CATEGORIES CONTENTS ===' as info;
SELECT * FROM categories;
