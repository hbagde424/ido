-- SQL Queries to manually add user document support to media table
-- Run these queries in your database

-- 1. Check if model_media_type column exists, if not add it
-- For MySQL/MariaDB:
ALTER TABLE `media` 
ADD COLUMN IF NOT EXISTS `model_media_type` VARCHAR(255) NULL AFTER `model_type`;

-- If the above doesn't work (older MySQL versions), use this instead:
-- First check if column exists:
-- SELECT COUNT(*) FROM information_schema.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'media' 
-- AND COLUMN_NAME = 'model_media_type';

-- If count is 0, then run:
ALTER TABLE `media` 
ADD COLUMN `model_media_type` VARCHAR(255) NULL AFTER `model_type`;

-- 2. Add index for better query performance
CREATE INDEX IF NOT EXISTS `media_model_media_type_index` ON `media` (`model_media_type`);

-- If the above doesn't work, use this:
-- First check if index exists:
-- SELECT COUNT(*) FROM information_schema.STATISTICS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'media' 
-- AND INDEX_NAME = 'media_model_media_type_index';

-- If count is 0, then run:
CREATE INDEX `media_model_media_type_index` ON `media` (`model_media_type`);

-- 3. Verify the column was added (run this to check)
-- DESCRIBE `media`;
-- OR
-- SHOW COLUMNS FROM `media` LIKE 'model_media_type';

-- 4. Verify the index was created (run this to check)
-- SHOW INDEXES FROM `media` WHERE Key_name = 'media_model_media_type_index';

