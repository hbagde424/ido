# Database Changes - Step by Step Guide

## 🎯 What Needs to Change in Database?

### Option 1: Automatic (Recommended)
Use PHP scripts to update content automatically:
```bash
php update_policy_content.php
php update_leave_policy_content.php
```

### Option 2: Manual (Using SQL)
Run SQL queries in phpMyAdmin

---

## 📊 Database Structure

### Table: `essentials_policies`

```
Columns:
- id (Primary Key)
- business_id
- user_id
- policy_type (ENUM: 'company_policy', 'hr_policy', 'leave_policy', 'posh_policy', 'nda_policy')
- title
- content (LONGTEXT)
- signature_photo
- signed_date
- status (ENUM: 'pending', 'signed', 'rejected')
- rejection_reason
- created_at
- updated_at
```

---

## 🔍 Step 1: Check Current Status

### Query 1: See all policies
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

**Expected Output:**
```
company_policy | 14
hr_policy      | X (any number)
leave_policy   | 2
nda_policy     | X
posh_policy    | X
```

### Query 2: Check HR Policy records
```sql
SELECT COUNT(*) as hr_policy_count
FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

---

## 🗑️ Step 2: Delete HR Policy Records (Optional)

### Query: Delete all HR Policy records
```sql
DELETE FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

### Verify deletion:
```sql
SELECT COUNT(*) as hr_policy_count
FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

**Expected Result:** 0

---

## 📝 Step 3: Update Policy Content

### Option A: Using PHP Scripts (Automatic)

```bash
# Update Company Policy content
php update_policy_content.php

# Update Leave Policy content
php update_leave_policy_content.php
```

**What it does:**
- Fetches new content from PolicyTemplates.php
- Updates all company_policy records
- Updates all leave_policy records

### Option B: Manual SQL Update

```sql
-- This is NOT recommended as content is very long
-- Use PHP scripts instead
-- But if needed, you can update like this:

UPDATE essentials_policies 
SET content = 'NEW_CONTENT_HERE'
WHERE policy_type = 'company_policy';

UPDATE essentials_policies 
SET content = 'NEW_CONTENT_HERE'
WHERE policy_type = 'leave_policy';
```

---

## ✅ Step 4: Verify Changes

### Query 1: Check Company Policy records
```sql
SELECT 
    id,
    user_id,
    policy_type,
    title,
    LENGTH(content) as content_length,
    status,
    signed_date
FROM essentials_policies 
WHERE policy_type = 'company_policy'
ORDER BY created_at DESC;
```

**Expected:**
- 14 records (or your number)
- content_length should be large (new content is longer)

### Query 2: Check Leave Policy records
```sql
SELECT 
    id,
    user_id,
    policy_type,
    title,
    LENGTH(content) as content_length,
    status,
    signed_date
FROM essentials_policies 
WHERE policy_type = 'leave_policy'
ORDER BY created_at DESC;
```

### Query 3: Verify HR Policy is gone
```sql
SELECT COUNT(*) as hr_policy_count
FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

**Expected Result:** 0

### Query 4: Check all policy types
```sql
SELECT DISTINCT policy_type 
FROM essentials_policies 
ORDER BY policy_type;
```

**Expected Result:**
```
company_policy
leave_policy
nda_policy
posh_policy
```

---

## 📊 Step 5: View Detailed Information

### Query: See who signed which policy
```sql
SELECT 
    u.id,
    u.first_name,
    u.last_name,
    ep.policy_type,
    ep.status,
    ep.signed_date,
    ep.created_at
FROM essentials_policies ep
JOIN users u ON ep.user_id = u.id
ORDER BY ep.policy_type, ep.signed_date DESC;
```

### Query: See policy statistics
```sql
SELECT 
    policy_type,
    status,
    COUNT(*) as count
FROM essentials_policies
GROUP BY policy_type, status
ORDER BY policy_type, status;
```

---

## 🔄 Complete Workflow

```
┌─────────────────────────────────────────┐
│ 1. Check Current Status                 │
│    SELECT policy_type, COUNT(*)         │
│    FROM essentials_policies             │
│    GROUP BY policy_type;                │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│ 2. Delete HR Policy Records (Optional)  │
│    DELETE FROM essentials_policies      │
│    WHERE policy_type = 'hr_policy';     │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│ 3. Update Policy Content                │
│    Option A: Run PHP scripts            │
│    Option B: Run SQL UPDATE             │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│ 4. Verify Changes                       │
│    SELECT * FROM essentials_policies    │
│    WHERE policy_type = 'company_policy' │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│ ✅ Database Changes Complete            │
└─────────────────────────────────────────┘
```

---

## 🚨 Important Notes

1. **Don't manually update content via SQL** - Use PHP scripts instead
2. **HR Policy deletion is optional** - You can keep records if needed
3. **Always backup before making changes** - Use phpMyAdmin export
4. **Verify after each step** - Run verification queries
5. **Content length will increase** - New policies are longer

---

## 📋 Quick Checklist

- [ ] Run: `SELECT policy_type, COUNT(*) FROM essentials_policies GROUP BY policy_type;`
- [ ] Run: `DELETE FROM essentials_policies WHERE policy_type = 'hr_policy';`
- [ ] Run: `php update_policy_content.php`
- [ ] Run: `php update_leave_policy_content.php`
- [ ] Run: `SELECT DISTINCT policy_type FROM essentials_policies;`
- [ ] Verify HR Policy count is 0
- [ ] Verify Company Policy records exist
- [ ] Verify Leave Policy records exist

---

## 🆘 Troubleshooting

### Problem: Can't delete HR Policy
**Solution:** Check if there are foreign key constraints
```sql
SELECT CONSTRAINT_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'essentials_policies';
```

### Problem: Content not updating
**Solution:** Run PHP scripts instead of SQL
```bash
php update_policy_content.php
php update_leave_policy_content.php
```

### Problem: Need to rollback
**Solution:** Restore from backup
```bash
mysql -u user -p database < backup.sql
```

---

## 📞 Summary

**Database Changes Required:**
1. ✅ Delete HR Policy records (optional)
2. ✅ Update Company Policy content (via PHP script)
3. ✅ Update Leave Policy content (via PHP script)

**No schema changes needed** - Only data updates!
