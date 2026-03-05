# Complete Live Server Deployment Guide

## 📦 Files to Upload (7 Total)

### PHP Files (5) - Upload via FTP:
1. `Modules/Essentials/Entities/PolicyTemplates.php`
2. `Modules/Essentials/Resources/views/policy/index.blade.php`
3. `Modules/Essentials/Http/Controllers/EssentialsPolicyController.php`
4. `Modules/Essentials/Entities/EssentialsPolicy.php`
5. `Modules/Essentials/Resources/lang/en/lang.php`

### Update Scripts (2) - Upload to ROOT:
6. `update_policy_content.php`
7. `update_leave_policy_content.php`

---

## 🗄️ Database Queries (Copy & Paste)

### Query 1: Backup (Run First!)
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

### Query 4: Final Check
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

---

## 🚀 Deployment Steps (10 Minutes)

### Step 1: Backup (1 min)
```bash
# Database backup
mysqldump -u user -p database > backup_$(date +%Y%m%d).sql

# Files backup
tar -czf backup_files_$(date +%Y%m%d).tar.gz Modules/Essentials/
```

### Step 2: Run Database Queries (2 min)
1. Open phpMyAdmin
2. Select your database
3. Go to SQL tab
4. Run Query 1 (Backup)
5. Run Query 2 (Delete HR)
6. Run Query 3 (Verify)
7. Run Query 4 (Final Check)

### Step 3: Upload Files (3 min)
- Upload 5 PHP files via FTP (maintain folder structure)
- Upload 2 update scripts to root directory

### Step 4: Run Update Scripts (2 min)
```bash
ssh user@server.com
cd /path/to/project
php update_policy_content.php
php update_leave_policy_content.php
```

### Step 5: Clear Cache (1 min)
```bash
php artisan optimize:clear
```

### Step 6: Test (1 min)
- Clear browser cache (Ctrl + Shift + Delete)
- Hard refresh (Ctrl + F5)
- Verify changes

---

## ✅ Verification Checklist

### Database:
- [ ] Backup table created
- [ ] HR policies deleted
- [ ] 4 policy types remain
- [ ] Company policies: 14 records
- [ ] Leave policies: 2 records

### Files:
- [ ] All 5 PHP files uploaded
- [ ] Both update scripts uploaded
- [ ] File permissions correct (644)

### Scripts:
- [ ] update_policy_content.php executed
- [ ] update_leave_policy_content.php executed
- [ ] No errors in output

### Cache:
- [ ] Laravel cache cleared
- [ ] Browser cache cleared
- [ ] Hard refresh done

### Testing:
- [ ] HR Policy NOT in dropdown
- [ ] Company Policy PDF shows new content
- [ ] Leave Policy PDF shows new content
- [ ] No console errors
- [ ] No 500 errors

---

## 📊 Expected Results

### Dropdown:
```
✓ Company Policy
✓ Leave Policy
✓ POSH Policy
✓ NDA Policy
✗ HR Policy (REMOVED)
```

### Database:
```
company_policy | 14 records
leave_policy   | 2 records
posh_policy    | X records
nda_policy     | X records
hr_policy      | 0 records
```

### PDFs:
```
Company Policy PDF:
- New HR Policy Manual content
- Multiple sections
- Professional formatting

Leave Policy PDF:
- New leave structure
- Festival holidays table
- Political campaign clause
```

---

## 🆘 Troubleshooting

### Issue: Old content in PDF
**Solution:**
```bash
php artisan optimize:clear
# Clear browser cache
# Hard refresh (Ctrl+F5)
```

### Issue: HR Policy still visible
**Solution:**
- Verify index.blade.php uploaded
- Check file permissions
- Clear view cache: `php artisan view:clear`

### Issue: Database query error
**Solution:**
- Check table name spelling
- Verify database selected
- Check user permissions

### Issue: 500 Error
**Solution:**
- Check Laravel logs: `storage/logs/laravel.log`
- Check file permissions (755 for folders, 644 for files)
- Verify all files uploaded completely

---

## 🔄 Rollback Plan

If something goes wrong:

```bash
# Restore database
mysql -u user -p database < backup_YYYYMMDD.sql

# Restore files
tar -xzf backup_files_YYYYMMDD.tar.gz

# Clear cache
php artisan optimize:clear
```

Or via SQL:
```sql
TRUNCATE TABLE essentials_policies;
INSERT INTO essentials_policies 
SELECT * FROM essentials_policies_backup;
```

---

## 📋 File Locations

### Upload via FTP:
```
Modules/
└── Essentials/
    ├── Entities/
    │   ├── PolicyTemplates.php ← UPLOAD
    │   └── EssentialsPolicy.php ← UPLOAD
    ├── Http/
    │   └── Controllers/
    │       └── EssentialsPolicyController.php ← UPLOAD
    └── Resources/
        ├── views/
        │   └── policy/
        │       └── index.blade.php ← UPLOAD
        └── lang/
            └── en/
                └── lang.php ← UPLOAD
```

### Upload to ROOT:
```
/
├── update_policy_content.php ← UPLOAD & RUN
└── update_leave_policy_content.php ← UPLOAD & RUN
```

---

## 🎯 Quick Reference

### SSH Commands:
```bash
# Connect
ssh user@server.com

# Navigate
cd /path/to/project

# Run updates
php update_policy_content.php
php update_leave_policy_content.php

# Clear cache
php artisan optimize:clear

# Check logs
tail -f storage/logs/laravel.log
```

### Database Queries:
```sql
-- Backup
CREATE TABLE essentials_policies_backup AS SELECT * FROM essentials_policies;

-- Delete HR
DELETE FROM essentials_policies WHERE policy_type = 'hr_policy';

-- Verify
SELECT policy_type, COUNT(*) FROM essentials_policies GROUP BY policy_type;
```

---

## ⏱️ Timeline

| Step | Time | Action |
|------|------|--------|
| 1 | 1 min | Backup |
| 2 | 2 min | Database queries |
| 3 | 3 min | Upload files |
| 4 | 2 min | Run scripts |
| 5 | 1 min | Clear cache |
| 6 | 1 min | Test |
| **Total** | **10 min** | **Complete** |

---

## ✨ Success Indicators

✅ HR Policy removed from dropdown
✅ Company Policy PDF = New HR Manual
✅ Leave Policy PDF = New leave structure
✅ No errors in logs
✅ All users can download PDFs
✅ Signatures preserved
✅ No 500 errors

---

## 📞 Support

If you need help:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check PHP error logs
3. Verify file permissions
4. Ensure database connection
5. Clear all caches

---

**Deployment Status:** Ready ✅
**Risk Level:** Low (with backup)
**Estimated Time:** 10 minutes
**Difficulty:** Easy

Good luck! 🚀
