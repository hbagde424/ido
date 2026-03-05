# Company Policy Content Update Instructions

## Problem
PDF में पुराना content दिख रहा है क्योंकि database में पुराना content stored है।

## Solution Options

### Option 1: Database में Content Update करें (Recommended)

Run these commands in terminal:

```bash
php artisan tinker
```

Then paste this code:

```php
$newContent = \Modules\Essentials\Entities\PolicyTemplates::getTemplate('company_policy');
$updated = \Modules\Essentials\Entities\EssentialsPolicy::where('policy_type', 'company_policy')->update(['content' => $newContent]);
echo "Updated {$updated} records\n";
exit;
```

### Option 2: Old Policies Delete करें (Users को फिर से sign करना होगा)

```bash
php artisan tinker
```

Then:

```php
$deleted = \Modules\Essentials\Entities\EssentialsPolicy::where('policy_type', 'company_policy')->delete();
echo "Deleted {$deleted} records. Users need to sign again.\n";
exit;
```

### Option 3: Direct SQL Query (phpMyAdmin या MySQL में)

```sql
-- Check existing records
SELECT id, user_id, title, status FROM essentials_policies WHERE policy_type = 'company_policy';

-- Delete old records (users will re-sign)
DELETE FROM essentials_policies WHERE policy_type = 'company_policy';
```

## After Update

1. Clear browser cache (Ctrl + Shift + Delete)
2. Reload the policy page (Ctrl + F5)
3. Select user and download PDF again
4. New content will appear

## Note
- Option 1: Updates content but keeps signatures (users won't know content changed)
- Option 2: Deletes records, users must re-sign (better for compliance)
- Option 3: Same as Option 2 but via SQL

Choose Option 2 if you want users to acknowledge the new policy by signing again.
