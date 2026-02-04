-- SQL script to add start_date, end_date, and user_id columns to project_tasks table
-- Run this if migration fails
-- Note: Using int(10) unsigned to match users table id column

ALTER TABLE `project_tasks` 
ADD COLUMN `start_date` date DEFAULT NULL AFTER `remark`,
ADD COLUMN `end_date` date DEFAULT NULL AFTER `start_date`,
ADD COLUMN `user_id` int(10) unsigned DEFAULT NULL AFTER `end_date`,
ADD KEY `project_tasks_user_id_foreign` (`user_id`),
ADD CONSTRAINT `project_tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

