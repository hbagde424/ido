-- SQL script to add last_viewed_at column to project_checklists table
-- Run this if migration fails

ALTER TABLE `project_checklists` 
ADD COLUMN `last_viewed_at` timestamp NULL DEFAULT NULL AFTER `project_lead_id`;

