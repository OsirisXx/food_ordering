-- Remove unique constraints on categories.name to allow duplicate category names
-- Copy and paste each command below into phpMyAdmin SQL tab

-- Step 1: Remove the unique constraint named 'name' on categories table
-- (This is the most likely constraint name based on the migration file)
ALTER TABLE `categories` DROP INDEX `name`;

-- Step 2: Verify the constraints are removed (optional)
SHOW INDEX FROM categories WHERE Column_name = 'name';
