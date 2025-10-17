-- Modern MySQL syntax to fix categories unique constraints
-- Run this entire block at once in phpMyAdmin

-- Step 1: Find the actual foreign key constraint name
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'items' 
    AND COLUMN_NAME = 'category_id'
    AND REFERENCED_TABLE_NAME = 'categories';

-- Step 2: Find the unique constraint on categories.name
SELECT 
    INDEX_NAME,
    NON_UNIQUE,
    COLUMN_NAME
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'categories' 
    AND COLUMN_NAME = 'name'
    AND NON_UNIQUE = 0;

-- Step 3: Remove unique constraint from categories (this is what's causing the error)
-- We'll remove ALL unique constraints on the name column
SET @sql = (
    SELECT CONCAT('ALTER TABLE `categories` DROP INDEX `', INDEX_NAME, '`')
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'categories' 
        AND COLUMN_NAME = 'name'
        AND NON_UNIQUE = 0
    LIMIT 1
);

-- Execute the dynamic SQL if we found a unique constraint
SET @sql_exists = @sql IS NOT NULL;
IF @sql_exists THEN
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END IF;

-- Step 4: Verify the unique constraint is removed
SHOW INDEX FROM categories WHERE Column_name = 'name';
