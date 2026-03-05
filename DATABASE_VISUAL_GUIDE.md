# Database Changes - Visual Guide

## 📊 Current Database State

### Before Changes:
```
essentials_policies table:
┌────┬─────────────┬──────────────────┬────────┐
│ id │ policy_type │ title            │ status │
├────┼─────────────┼──────────────────┼────────┤
│ 1  │ company     │ Company Policy   │ signed │
│ 2  │ company     │ Company Policy   │ signed │
│ 3  │ hr_policy   │ HR Policy        │ signed │ ← DELETE
│ 4  │ hr_policy   │ HR Policy        │ signed │ ← DELETE
│ 5  │ leave       │ Leave Policy     │ signed │
│ 6  │ leave       │ Leave Policy     │ signed │
│ 7  │ posh        │ POSH Policy      │ signed │
│ 8  │ nda         │ NDA Policy       │ signed │
└────┴─────────────┴──────────────────┴────────┘

Total: 8 records
- company_policy: 2
- hr_policy: 2 ← TO BE DELETED
- leave_policy: 2
- posh_policy: 1
- nda_policy: 1
```

---

## 🔄 Changes to Make

### Change 1: Delete HR Policy Records
```
BEFORE:
┌─────────────┬───────┐
│ policy_type │ count │
├─────────────┼───────┤
│ company     │  14   │
│ hr_policy   │   X   │ ← DELETE ALL
│ leave       │   2   │
│ nda         │   X   │
│ posh        │   X   │
└─────────────┴───────┘

AFTER:
┌─────────────┬───────┐
│ policy_type │ count │
├─────────────┼───────┤
│ company     │  14   │
│ leave       │   2   │
│ nda         │   X   │
│ posh        │   X   │
└─────────────┴───────┘
```

### Change 2: Update Content
```
BEFORE:
┌─────────────┬──────────────────────────────┐
│ policy_type │ content                      │
├─────────────┼──────────────────────────────┤
│ company     │ Old simple policy content... │ ← UPDATE
│ leave       │ Old leave policy content...  │ ← UPDATE
└─────────────┴──────────────────────────────┘

AFTER:
┌─────────────┬──────────────────────────────────────────┐
│ policy_type │ content                                  │
├─────────────┼──────────────────────────────────────────┤
│ company     │ New comprehensive HR Policy Manual...    │ ← UPDATED
│ leave       │ New detailed Leave Policy with tables... │ ← UPDATED
└─────────────┴──────────────────────────────────────────┘
```

---

## 🎯 Step-by-Step Process

### Step 1: Check Status
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

```
Output:
┌─────────────┬───────┐
│ policy_type │ count │
├─────────────┼───────┤
│ company     │  14   │
│ hr_policy   │   2   │ ← Found
│ leave       │   2   │
│ nda         │   1   │
│ posh        │   1   │
└─────────────┴───────┘
```

### Step 2: Delete HR Policy
```sql
DELETE FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

```
Result: 2 rows deleted
```

### Step 3: Verify Deletion
```sql
SELECT COUNT(*) as hr_policy_count
FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

```
Output:
┌──────────────────┐
│ hr_policy_count  │
├──────────────────┤
│ 0                │ ← Success!
└──────────────────┘
```

### Step 4: Update Content
```bash
php update_policy_content.php
php update_leave_policy_content.php
```

```
Output:
✓ Successfully updated 14 company policy records
✓ Successfully updated 2 leave policy records
```

### Step 5: Verify Updates
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

```
Output:
┌─────────────┬───────┐
│ policy_type │ count │
├─────────────┼───────┤
│ company     │  14   │ ← Updated
│ leave       │   2   │ ← Updated
│ nda         │   1   │
│ posh        │   1   │
└─────────────┴───────┘

Total: 18 records (was 20 before)
```

---

## 📈 Data Flow Diagram

```
┌──────────────────────────────────────────────────┐
│         essentials_policies Table                │
│                                                  │
│  ┌────────────────────────────────────────────┐ │
│  │ id | policy_type | content | status       │ │
│  ├────────────────────────────────────────────┤ │
│  │ 1  │ company     │ OLD     │ signed       │ │
│  │ 2  │ company     │ OLD     │ signed       │ │
│  │ 3  │ hr_policy   │ OLD     │ signed       │ ← DELETE
│  │ 4  │ hr_policy   │ OLD     │ signed       │ ← DELETE
│  │ 5  │ leave       │ OLD     │ signed       │
│  │ 6  │ leave       │ OLD     │ signed       │
│  │ 7  │ posh        │ OLD     │ signed       │
│  │ 8  │ nda         │ OLD     │ signed       │
│  └────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────┘
                    │
                    │ DELETE hr_policy
                    ▼
┌──────────────────────────────────────────────────┐
│         essentials_policies Table                │
│                                                  │
│  ┌────────────────────────────────────────────┐ │
│  │ id | policy_type | content | status       │ │
│  ├────────────────────────────────────────────┤ │
│  │ 1  │ company     │ OLD     │ signed       │
│  │ 2  │ company     │ OLD     │ signed       │
│  │ 5  │ leave       │ OLD     │ signed       │
│  │ 6  │ leave       │ OLD     │ signed       │
│  │ 7  │ posh        │ OLD     │ signed       │
│  │ 8  │ nda         │ OLD     │ signed       │
│  └────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────┘
                    │
                    │ UPDATE content
                    ▼
┌──────────────────────────────────────────────────┐
│         essentials_policies Table                │
│                                                  │
│  ┌────────────────────────────────────────────┐ │
│  │ id | policy_type | content | status       │ │
│  ├────────────────────────────────────────────┤ │
│  │ 1  │ company     │ NEW ✓   │ signed       │
│  │ 2  │ company     │ NEW ✓   │ signed       │
│  │ 5  │ leave       │ NEW ✓   │ signed       │
│  │ 6  │ leave       │ NEW ✓   │ signed       │
│  │ 7  │ posh        │ OLD     │ signed       │
│  │ 8  │ nda         │ OLD     │ signed       │
│  └────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────┘
                    │
                    ▼
            ✅ COMPLETE
```

---

## 🔍 Query Execution Order

```
1️⃣ BACKUP
   └─ mysqldump -u user -p db > backup.sql

2️⃣ CHECK
   └─ SELECT policy_type, COUNT(*) FROM essentials_policies GROUP BY policy_type;

3️⃣ DELETE
   └─ DELETE FROM essentials_policies WHERE policy_type = 'hr_policy';

4️⃣ VERIFY DELETE
   └─ SELECT COUNT(*) FROM essentials_policies WHERE policy_type = 'hr_policy';

5️⃣ UPDATE CONTENT
   ├─ php update_policy_content.php
   └─ php update_leave_policy_content.php

6️⃣ VERIFY UPDATE
   └─ SELECT DISTINCT policy_type FROM essentials_policies;

7️⃣ FINAL CHECK
   └─ SELECT policy_type, COUNT(*) FROM essentials_policies GROUP BY policy_type;
```

---

## 📊 Expected Results

### Before:
```
Total Records: 20
- company_policy: 14
- hr_policy: 2
- leave_policy: 2
- nda_policy: 1
- posh_policy: 1
```

### After:
```
Total Records: 18
- company_policy: 14 (content updated)
- leave_policy: 2 (content updated)
- nda_policy: 1
- posh_policy: 1
```

---

## ✅ Success Indicators

```
✓ HR Policy count = 0
✓ Company Policy count = 14
✓ Leave Policy count = 2
✓ Total records = 18
✓ No errors in logs
✓ PDFs show new content
```

---

## 🆘 Rollback

```
If something goes wrong:

1. Restore database:
   mysql -u user -p db < backup.sql

2. Verify:
   SELECT policy_type, COUNT(*) FROM essentials_policies GROUP BY policy_type;

3. Check logs:
   tail -f storage/logs/laravel.log
```

---

## 📝 Summary

| Action | Before | After | Status |
|--------|--------|-------|--------|
| HR Policy Records | 2 | 0 | ✅ Deleted |
| Company Policy Content | Old | New | ✅ Updated |
| Leave Policy Content | Old | New | ✅ Updated |
| Total Records | 20 | 18 | ✅ Correct |
| Policy Types | 5 | 4 | ✅ Correct |

---

**Database changes are simple and safe!** 🎉
