# Live Server Deployment - Quick Summary

## 🎯 What Changed?

### 1. HR Policy Removed
- HR Policy option हटाया गया dropdown से
- HR Policy tab हटाया गया
- Database से hr_policy type remove किया

### 2. Company Policy Updated
- पुराना simple policy हटाया
- नया comprehensive HR Policy Manual added
- सभी sections: Recruitment, Training, Probation, Working Hours, Attendance, Leave, WFH, Performance, POSH, etc.

### 3. Leave Policy Updated
- नया detailed leave structure
- Festival holidays table
- Political campaign clause
- Extended weekend guidelines

---

## 📦 Files to Upload (Total: 7 files)

### Main Application Files (5):
```
1. Modules/Essentials/Entities/PolicyTemplates.php
2. Modules/Essentials/Entities/EssentialsPolicy.php
3. Modules/Essentials/Http/Controllers/EssentialsPolicyController.php
4. Modules/Essentials/Resources/views/policy/index.blade.php
5. Modules/Essentials/Resources/lang/en/lang.php
```

### Update Scripts (2):
```
6. update_policy_content.php (root folder)
7. update_leave_policy_content.php (root folder)
```

---

## 🚀 Deployment Steps (5 Minutes)

### Step 1: Backup (1 min)
```bash
# Database backup
mysqldump -u user -p database > backup.sql

# Files backup
cp -r Modules/Essentials Modules/Essentials_backup
```

### Step 2: Upload Files (2 min)
- Upload all 7 files via FTP/FileZilla
- Maintain folder structure

### Step 3: Run Scripts (1 min)
```bash
ssh user@server
cd /path/to/project
php update_policy_content.php
php update_leave_policy_content.php
```

### Step 4: Clear Cache (30 sec)
```bash
php artisan optimize:clear
```

### Step 5: Test (30 sec)
- Clear browser cache
- Refresh page (Ctrl + F5)
- Download PDFs to verify

---

## ✅ Verification Checklist

- [ ] HR Policy removed from dropdown
- [ ] Only 4 policies visible (Company, Leave, POSH, NDA)
- [ ] Company Policy PDF shows new content
- [ ] Leave Policy PDF shows new content
- [ ] No errors in browser console
- [ ] No errors in Laravel logs

---

## 🔧 Commands Reference

### Upload via FTP:
```
Use FileZilla or any FTP client
Server: your-server.com
Port: 21 (or 22 for SFTP)
```

### Run via SSH:
```bash
# Connect
ssh username@your-server.com

# Navigate
cd /var/www/html/your-project

# Run updates
php update_policy_content.php
php update_leave_policy_content.php

# Clear cache
php artisan optimize:clear

# Check logs
tail -f storage/logs/laravel.log
```

---

## 📊 Expected Output

### update_policy_content.php:
```
Fetching new content from PolicyTemplates...
Updating company policy records...
✓ Successfully updated 14 company policy records with new content!
```

### update_leave_policy_content.php:
```
Fetching new Leave Policy content from PolicyTemplates...
Updating leave policy records...
✓ Successfully updated X leave policy records with new content!
```

---

## ⚠️ Troubleshooting

### Problem: Old content still showing in PDF
**Solution:**
```bash
php artisan optimize:clear
# Then clear browser cache (Ctrl + Shift + Delete)
```

### Problem: HR Policy still visible
**Solution:**
- Verify index.blade.php was uploaded correctly
- Check file permissions (644)
- Clear cache again

### Problem: Update script errors
**Solution:**
- Check database connection
- Verify file paths
- Check Laravel logs: `storage/logs/laravel.log`

---

## 🔄 Rollback (If Needed)

```bash
# Restore database
mysql -u user -p database < backup.sql

# Restore files
rm -rf Modules/Essentials
mv Modules/Essentials_backup Modules/Essentials

# Clear cache
php artisan optimize:clear
```

---

## 📞 Support

If you face issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check PHP error logs
3. Verify all files uploaded correctly
4. Ensure scripts ran successfully
5. Clear all caches (server + browser)

---

## 🎉 Success Indicators

✓ Dropdown shows only 4 policies
✓ Company Policy PDF = New HR Manual (multiple pages)
✓ Leave Policy PDF = New leave structure with tables
✓ No console errors
✓ No 404 or 500 errors
✓ All users can download PDFs

---

**Deployment Time:** 5-10 minutes
**Difficulty:** Easy
**Risk Level:** Low (with backup)
**Tested:** ✓ Yes

---

Good luck with deployment! 🚀
