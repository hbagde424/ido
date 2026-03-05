-- SQL Query to update existing Company Policy records with new content
-- This will update the content field for all company_policy records

-- Note: Run this only if you want to update already signed/existing policies
-- New policies will automatically use the updated template

UPDATE essentials_policies 
SET content = (
    SELECT content FROM (
        SELECT 'Updated content from PolicyTemplates.php' as content
    ) as temp
)
WHERE policy_type = 'company_policy';

-- If you want to completely refresh the content, you can delete old records
-- and let users sign the new policy again:
-- DELETE FROM essentials_policies WHERE policy_type = 'company_policy';
