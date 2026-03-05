# Database Changes Summary

## 🎯 What Needs to Change in Database?

### Option 1: Automatic (Recommended)
Use PHP scripts to update content automatically:
```bash
php update_policy_content.php
php update_leave_policy_content.php
```

### Option 2: Manual SQL Queries
Run SQL queries in phpMyAdmin

---

## 📋 SQL Queries (Copy & Paste Ready)

### Query 1: Backup (MUST RUN FIRST)
```sql
CREATE TABLE essentials_policies_backup AS 
SELECT * FROM essentials_policies;
```

### Query 2: Delete HR Policy Records
```sql
DELETE FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

### Query 3: Verify Deletion
```sql
SELECT COUNT(*) as hr_policies_remaining 
FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

### Query 4: Check Final State
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

---

## 🔄 What Gets Updated?

### essentials_policies Table

| Column | Change | Details |
|--------|--------|---------|
| policy_type | Filtered | HR policies removed |
| content | Updated | New Company & Leave Policy content |
| status | Unchanged | Signatures preserved |
| signed_date | Unchanged | Dates preserved |
| user_id | Unchanged | User links preserved |

---

## 📊 Expected Results

### Before Changes:
```
company_policy | 14 records
leave_policy   | 2 records
posh_policy    | X records
nda_policy     | X records
hr_policy      | X records ← WILL BE DELETED
```

### After Changes:
```
company_policy | 14 records (content updated)
leave_policy   | 2 records (content updated)
posh_policy    | X records (unchanged)
nda_policy     | X records (unchanged)
hr_policy      | 0 records ← DELETED
```

---

## ⚡ Quick Steps

### Step 1: Backup
```sql
CREATE TABLE essentials_policies_backup AS 
SELECT * FROM essentials_policies;
```

### Step 2: Delete HR Policies
```sql
DELETE FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

### Step 3: Verify
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

### Step 4: Update Content (via PHP)
```bash
php update_policy_content.php
php update_leave_policy_content.php
```

---

## 🛡️ Rollback (If Needed)

```sql
TRUNCATE TABLE essentials_policies;

INSERT INTO essentials_policies 
SELECT * FROM essentials_policies_backup;
```

---

## ✅ Verification Queries

### Check all policies:
```sql
SELECT * FROM essentials_policies;
```

### Check by type:
```sql
SELECT policy_type, COUNT(*) FROM essentials_policies GROUP BY policy_type;
```

### Check signatures:
```sql
SELECT u.first_name, ep.policy_type, ep.status, ep.signed_date
FROM essentials_policies ep
JOIN users u ON ep.user_id = u.id;
```

---

## 📝 Notes

1. **No user data is deleted** - Only HR policy records
2. **Signatures are preserved** - All signed dates remain
3. **Content is updated** - New policy content added
4. **Backup is created** - Safe rollback available
5. **Status unchanged** - Signed/pending status preserved

---

## 🎯 Final Checklist

- [ ] Backup created
- [ ] HR policies deleted
- [ ] Verification query shows 4 policy types
- [ ] PHP update scripts ready
- [ ] Files uploaded
- [ ] Cache cleared
- [ ] Browser cache cleared
- [ ] PDFs tested

---

**Total Database Changes:** 3 queries + 2 PHP scripts
**Time Required:** 5 minutes
**Risk Level:** Low (with backup)
