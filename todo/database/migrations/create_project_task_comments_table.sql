-- SQL script to create project_task_comments table
-- Run this if migration fails
-- Note: Using int(10) unsigned to match project_tasks and users table id columns

CREATE TABLE IF NOT EXISTS `project_task_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_task_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_task_comments_project_task_id_foreign` (`project_task_id`),
  KEY `project_task_comments_user_id_foreign` (`user_id`),
  CONSTRAINT `project_task_comments_project_task_id_foreign` FOREIGN KEY (`project_task_id`) REFERENCES `project_tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_task_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

