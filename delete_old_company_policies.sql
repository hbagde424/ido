-- Delete all existing company_policy records so users can sign the new updated policy
-- This will force users to re-sign with the new content

-- IMPORTANT: Backup your data before running this!

-- Step 1: Check how many records will be deleted
SELECT COUNT(*) as total_records, 
       COUNT(CASE WHEN status = 'signed' THEN 1 END) as signed_records
FROM essentials_policies 
WHERE policy_type = 'company_policy';

-- Step 2: See which users have signed
SELECT u.id, u.first_name, u.last_name, ep.signed_date, ep.status
FROM essentials_policies ep
JOIN users u ON ep.user_id = u.id
WHERE ep.policy_type = 'company_policy';

-- Step 3: Delete all company_policy records (uncomment to execute)
DELETE FROM essentials_policies WHERE policy_type = 'company_policy';

-- After running this, users will need to sign the policy again with the new content
