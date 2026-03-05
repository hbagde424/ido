-- =====================================================
-- SIMPLE DATABASE CHANGES - COPY & PASTE READY
-- =====================================================
-- Run these queries in phpMyAdmin or MySQL
-- =====================================================

-- =====================================================
-- QUERY 1: BACKUP (RUN FIRST!)
-- =====================================================
-- Create backup table before making any changes

CREATE TABLE essentials_policies_backup_$(date) AS 
SELECT * FROM essentials_policies;

-- Verify backup created
SELECT COUNT(*) FROM essentials_policies_backup_$(date);

-- =====================================================
-- QUERY 2: DELETE HR POLICY RECORDS
-- =====================================================
-- Remove all HR policy records from database

DELETE FROM essentials_policies 
WHERE policy_type = 'hr_policy';

-- Verify deletion
SELECT COUNT(*) as hr_policies_remaining 
FROM essentials_policies 
WHERE policy_type = 'hr_policy';

-- =====================================================
-- QUERY 3: CHECK REMAINING POLICIES
-- =====================================================
-- Verify only 4 policy types remain

SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;

-- Expected output:
-- company_policy | 14
-- leave_policy   | 2
-- posh_policy    | X
-- nda_policy     | X

-- =====================================================
-- QUERY 4: VERIFY COMPANY POLICIES
-- =====================================================
-- Check company policy records

SELECT id, user_id, title, status, signed_date 
FROM essentials_policies 
WHERE policy_type = 'company_policy'
ORDER BY created_at DESC;

-- =====================================================
-- QUERY 5: VERIFY LEAVE POLICIES
-- =====================================================
-- Check leave policy records

SELECT id, user_id, title, status, signed_date 
FROM essentials_policies 
WHERE policy_type = 'leave_policy'
ORDER BY created_at DESC;

-- =====================================================
-- QUERY 6: FINAL CHECK
-- =====================================================
-- Summary of all policies

SELECT 
    policy_type,
    COUNT(*) as total,
    COUNT(CASE WHEN status = 'signed' THEN 1 END) as signed,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending
FROM essentials_policies
GROUP BY policy_type;

-- =====================================================
-- IF SOMETHING GOES WRONG - ROLLBACK
-- =====================================================
-- Restore from backup

TRUNCATE TABLE essentials_policies;

INSERT INTO essentials_policies 
SELECT * FROM essentials_policies_backup_$(date);

-- =====================================================
-- DONE!
-- =====================================================
-- Now run the PHP update scripts:
-- php update_policy_content.php
-- php update_leave_policy_content.php
-- =====================================================
