-- Remove unique constraints on items.name to allow duplicate item names
-- Copy and paste each command below into phpMyAdmin SQL tab

-- Step 1: Remove the unique constraint named 'name'
ALTER TABLE `items` DROP INDEX `name`;

-- Step 2: Remove the unique constraint named 'uniq_items_name'
ALTER TABLE `items` DROP INDEX `uniq_items_name`;

-- Step 3: Verify the constraints are removed (optional)
SHOW INDEX FROM items WHERE Column_name = 'name';
