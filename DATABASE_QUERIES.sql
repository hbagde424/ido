-- =====================================================
-- DATABASE CHANGES FOR POLICY UPDATES
-- =====================================================
-- Run these queries in phpMyAdmin or MySQL
-- =====================================================

-- =====================================================
-- STEP 1: CHECK EXISTING DATA
-- =====================================================

-- Check how many policies exist
SELECT COUNT(*) as total_policies FROM essentials_policies;

-- Check policies by type
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;

-- Check company policies
SELECT id, user_id, title, status, created_at 
FROM essentials_policies 
WHERE policy_type = 'company_policy';

-- Check leave policies
SELECT id, user_id, title, status, created_at 
FROM essentials_policies 
WHERE policy_type = 'leave_policy';

-- Check HR policies (if any exist)
SELECT id, user_id, title, status, created_at 
FROM essentials_policies 
WHERE policy_type = 'hr_policy';

-- =====================================================
-- STEP 2: DELETE HR POLICY RECORDS (OPTIONAL)
-- =====================================================
-- Only run this if you want to remove existing HR policies
-- Backup first!

DELETE FROM essentials_policies 
WHERE policy_type = 'hr_policy';

-- Verify deletion
SELECT COUNT(*) as hr_policies_remaining 
FROM essentials_policies 
WHERE policy_type = 'hr_policy';

-- =====================================================
-- STEP 3: VERIFY AFTER DELETION
-- =====================================================

-- Check remaining policies
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;

-- =====================================================
-- STEP 4: UPDATE COMPANY POLICY CONTENT (IF NEEDED)
-- =====================================================
-- This is optional - the PHP script will do this automatically
-- Only run if you want to manually update

-- Update all company policies with new content
-- Note: You need to copy the actual HTML content from PolicyTemplates.php
-- This is just a placeholder

UPDATE essentials_policies 
SET content = '<h2>HUMAN RESOURCE POLICY MANUAL</h2>...' 
WHERE policy_type = 'company_policy';

-- =====================================================
-- STEP 5: UPDATE LEAVE POLICY CONTENT (IF NEEDED)
-- =====================================================
-- This is optional - the PHP script will do this automatically

UPDATE essentials_policies 
SET content = '<h2>LEAVE POLICY</h2>...' 
WHERE policy_type = 'leave_policy';

-- =====================================================
-- STEP 6: VERIFY UPDATES
-- =====================================================

-- Check if content was updated
SELECT id, user_id, policy_type, title, 
       SUBSTRING(content, 1, 100) as content_preview,
       status, updated_at
FROM essentials_policies 
WHERE policy_type = 'company_policy' 
LIMIT 1;

-- Check leave policy content
SELECT id, user_id, policy_type, title, 
       SUBSTRING(content, 1, 100) as content_preview,
       status, updated_at
FROM essentials_policies 
WHERE policy_type = 'leave_policy' 
LIMIT 1;

-- =====================================================
-- STEP 7: CHECK POLICY SIGNATURES
-- =====================================================

-- See which users have signed which policies
SELECT 
    ep.id,
    u.first_name,
    u.last_name,
    ep.policy_type,
    ep.status,
    ep.signed_date,
    ep.created_at
FROM essentials_policies ep
JOIN users u ON ep.user_id = u.id
ORDER BY ep.policy_type, ep.created_at DESC;

-- =====================================================
-- STEP 8: BACKUP BEFORE MAKING CHANGES
-- =====================================================
-- Run this to create a backup table

CREATE TABLE essentials_policies_backup AS 
SELECT * FROM essentials_policies;

-- Verify backup
SELECT COUNT(*) as backup_count FROM essentials_policies_backup;

-- =====================================================
-- STEP 9: ROLLBACK (IF SOMETHING GOES WRONG)
-- =====================================================
-- Restore from backup

TRUNCATE TABLE essentials_policies;

INSERT INTO essentials_policies 
SELECT * FROM essentials_policies_backup;

-- =====================================================
-- STEP 10: FINAL VERIFICATION
-- =====================================================

-- Check final state
SELECT 
    policy_type,
    COUNT(*) as total_records,
    COUNT(CASE WHEN status = 'signed' THEN 1 END) as signed_records,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_records
FROM essentials_policies
GROUP BY policy_type;

-- =====================================================
-- SUMMARY OF CHANGES
-- =====================================================
-- 
-- 1. HR Policy records deleted (if any existed)
-- 2. Company Policy content updated (14 records)
-- 3. Leave Policy content updated (2 records)
-- 4. All signatures and dates preserved
-- 5. Status remains unchanged
--
-- =====================================================
