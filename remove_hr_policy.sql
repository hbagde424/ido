-- SQL Query to remove HR Policy records from database
-- Run this query to clean up existing HR policy data

-- Delete all HR policy records
DELETE FROM essentials_policies WHERE policy_type = 'hr_policy';

-- Optional: If you want to modify the enum column to remove hr_policy option
-- Note: This requires recreating the column in MySQL
-- ALTER TABLE essentials_policies MODIFY COLUMN policy_type ENUM('company_policy', 'leave_policy', 'posh_policy', 'nda_policy');
