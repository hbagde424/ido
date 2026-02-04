-- SQL script to add start_date, end_date, and project_lead_id columns to project_checklists table
-- Run this if migration fails
-- Note: Using int(10) unsigned to match users table id column

ALTER TABLE `project_checklists` 
ADD COLUMN `start_date` date DEFAULT NULL AFTER `project_name`,
ADD COLUMN `end_date` date DEFAULT NULL AFTER `start_date`,
ADD COLUMN `project_lead_id` int(10) unsigned DEFAULT NULL AFTER `end_date`,
ADD KEY `project_checklists_project_lead_id_foreign` (`project_lead_id`),
ADD CONSTRAINT `project_checklists_project_lead_id_foreign` FOREIGN KEY (`project_lead_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

