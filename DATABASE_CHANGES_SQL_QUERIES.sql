-- ============================================
-- Policies Management Feature - Database Changes
-- Date: 4 Feb - 10 Feb 2026
-- ============================================

-- ============================================
-- 1. CREATE NEW TABLE: essentials_policies
-- ============================================
CREATE TABLE IF NOT EXISTS `essentials_policies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `policy_type` enum('company_policy','hr_policy','leave_policy','posh_policy','nda_policy') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext,
  `signature_photo` varchar(255),
  `signed_date` date,
  `status` enum('pending','signed','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_id` (`business_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. ADD COLUMN TO EXISTING TABLE: users
-- ============================================
-- Add signature_photo column to users table (if not exists)
ALTER TABLE `users` ADD COLUMN `signature_photo` varchar(255) NULL AFTER `profile_photo`;

-- ============================================
-- 3. CREATE INDEXES FOR BETTER PERFORMANCE
-- ============================================
-- Index on business_id for faster queries
ALTER TABLE `essentials_policies` ADD INDEX `idx_business_id` (`business_id`);

-- Index on user_id for faster queries
ALTER TABLE `essentials_policies` ADD INDEX `idx_user_id` (`user_id`);

-- Index on status for filtering
ALTER TABLE `essentials_policies` ADD INDEX `idx_status` (`status`);

-- Index on policy_type for filtering
ALTER TABLE `essentials_policies` ADD INDEX `idx_policy_type` (`policy_type`);

-- ============================================
-- 4. VERIFY TABLE STRUCTURE
-- ============================================
-- Check essentials_policies table structure
-- DESCRIBE essentials_policies;

-- Check users table for signature_photo column
-- DESCRIBE users;

-- ============================================
-- NOTES:
-- ============================================
-- 1. essentials_policies table stores all policy records
-- 2. Each policy is linked to a business_id and user_id
-- 3. Policy types: company_policy, hr_policy, leave_policy, posh_policy, nda_policy
-- 4. Status can be: pending, signed, rejected
-- 5. signature_photo stores the path to uploaded signature image
-- 6. signed_date records when the policy was signed
-- 7. rejection_reason stores reason if policy is rejected
-- 8. Users table now has signature_photo column for storing user signatures
