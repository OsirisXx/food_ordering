-- Check what tables exist in the food database
-- Make sure you're connected to the 'food' database first!

-- Simple way to see all tables
SHOW TABLES;

-- Check if specific tables exist
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'food' 
    AND TABLE_NAME IN ('categories', 'items', 'users', 'orders');
