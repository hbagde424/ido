-- This SQL will update all existing company_policy records with the new content from PolicyTemplates

-- First, let's see what policies exist
SELECT id, user_id, policy_type, title, status, signed_date, created_at 
FROM essentials_policies 
WHERE policy_type = 'company_policy';

-- To update the content for all existing company_policy records:
-- Note: You need to replace 'NEW_CONTENT_HERE' with actual content from PolicyTemplates.php

-- Option 1: Delete existing company policies so users can sign the new version
-- DELETE FROM essentials_policies WHERE policy_type = 'company_policy';

-- Option 2: Update the content field (but this won't trigger re-signing)
-- You'll need to manually copy the content from PolicyTemplates.php and paste it here
