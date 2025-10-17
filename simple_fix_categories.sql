-- Simple fix for categories unique constraint - works with any MySQL version
-- Copy and paste this into phpMyAdmin

-- First, let's see what constraints exist
SHOW INDEX FROM categories WHERE Column_name = 'name';

-- If you see a unique constraint (Non_unique = 0), note the Key_name
-- Then run one of these commands based on what you see:

-- Option 1: If the constraint is named 'name'
-- ALTER TABLE `categories` DROP INDEX `name`;

-- Option 2: If the constraint is named 'uniq_categories_name'  
-- ALTER TABLE `categories` DROP INDEX `uniq_categories_name`;

-- Option 3: If it has a different name, replace 'INDEX_NAME' with the actual name
-- ALTER TABLE `categories` DROP INDEX `INDEX_NAME`;

-- After running the DROP INDEX command, verify it worked:
-- SHOW INDEX FROM categories WHERE Column_name = 'name';
