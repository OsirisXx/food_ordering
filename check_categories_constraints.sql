-- Check what indexes and constraints exist on the categories table
-- This will help us identify the exact constraint causing the problem

SELECT '=== CURRENT INDEXES ON categories.name ===' as info;

SELECT 
    INDEX_NAME,
    INDEX_TYPE,
    NON_UNIQUE,
    COLUMN_NAME
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'categories' 
    AND COLUMN_NAME = 'name'
ORDER BY INDEX_NAME;

SELECT '=== CONSTRAINTS ON categories.name ===' as info;

-- Check for constraints (corrected column names)
SELECT 
    CONSTRAINT_NAME,
    CONSTRAINT_SCHEMA,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'categories' 
    AND COLUMN_NAME = 'name';

SELECT '=== TABLE STRUCTURE ===' as info;

-- Show the current table structure
DESCRIBE categories;

SELECT '=== SHOW INDEX COMMAND ===' as info;

-- Alternative way to see indexes
SHOW INDEX FROM categories WHERE Column_name = 'name';
