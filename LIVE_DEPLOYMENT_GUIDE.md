# Live Server Deployment Guide - Policy Changes

## Important Files to Upload/Change on Live Server

### 1. Core Policy Files (MUST UPLOAD)

#### A. Policy Templates (Main Content)
```
Modules/Essentials/Entities/PolicyTemplates.php
```
**Changes:**
- Company Policy content updated (comprehensive HR manual)
- Leave Policy content updated (new leave structure)
- HR Policy template removed (hrPolicyTemplate function deleted)

---

#### B. Policy View File
```
Modules/Essentials/Resources/views/policy/index.blade.php
```
**Changes:**
- HR Policy option removed from dropdown
- HR Policy tab removed from tabs section
- JavaScript arrays updated (removed hr_policy)

---

#### C. Policy Controller
```
Modules/Essentials/Http/Controllers/EssentialsPolicyController.php
```
**Changes:**
- All $policy_types arrays updated (hr_policy removed)

---

#### D. Policy Model
```
Modules/Essentials/Entities/EssentialsPolicy.php
```
**Changes:**
- $policy_types array updated (hr_policy removed)

---

#### E. Language File
```
Modules/Essentials/Resources/lang/en/lang.php
```
**Changes:**
- 'hr_policy' translation removed

---

### 2. Database Changes (MUST RUN)

#### A. Update Existing Policy Content
Upload these PHP scripts to live server root and run:

```bash
# Update Company Policy content in database
php update_policy_content.php

# Update Leave Policy content in database
php update_leave_policy_content.php
```

**Files to upload:**
```
update_policy_content.php
update_leave_policy_content.php
```

---

#### B. Remove HR Policy Records (Optional)
If you want to remove existing HR policy records:

```sql
-- Run this in phpMyAdmin or MySQL
DELETE FROM essentials_policies WHERE policy_type = 'hr_policy';
```

**SQL File:**
```
remove_hr_policy.sql
```

---

### 3. Cache Clear Commands (MUST RUN)

After uploading files, run these commands on live server:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

---

## Step-by-Step Deployment Process

### Step 1: Backup
```bash
# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Backup files
tar -czf backup_files_$(date +%Y%m%d).tar.gz Modules/Essentials/
```

---

### Step 2: Upload Files via FTP/SFTP

Upload these files to live server:

1. **Modules/Essentials/Entities/PolicyTemplates.php**
2. **Modules/Essentials/Resources/views/policy/index.blade.php**
3. **Modules/Essentials/Http/Controllers/EssentialsPolicyController.php**
4. **Modules/Essentials/Entities/EssentialsPolicy.php**
5. **Modules/Essentials/Resources/lang/en/lang.php**
6. **update_policy_content.php** (to root directory)
7. **update_leave_policy_content.php** (to root directory)

---

### Step 3: Run Database Update Scripts

SSH into live server and run:

```bash
cd /path/to/your/project

# Update Company Policy content
php update_policy_content.php

# Update Leave Policy content
php update_leave_policy_content.php
```

**Expected Output:**
```
✓ Successfully updated 14 company policy records with new content!
✓ Successfully updated 2 leave policy records with new content!
```

---

### Step 4: Clear All Caches

```bash
php artisan optimize:clear
```

---

### Step 5: Verify Changes

1. Open policy page in browser
2. Clear browser cache (Ctrl + Shift + Delete)
3. Hard refresh (Ctrl + F5)
4. Select user and download PDF
5. Verify new content appears

---

## Quick Checklist

- [ ] Backup database and files
- [ ] Upload PolicyTemplates.php
- [ ] Upload index.blade.php
- [ ] Upload EssentialsPolicyController.php
- [ ] Upload EssentialsPolicy.php
- [ ] Upload lang/en/lang.php
- [ ] Upload update scripts (update_policy_content.php, update_leave_policy_content.php)
- [ ] Run update_policy_content.php
- [ ] Run update_leave_policy_content.php
- [ ] Run artisan optimize:clear
- [ ] Test on live site
- [ ] Clear browser cache and verify

---

## Rollback Plan (If Something Goes Wrong)

```bash
# Restore database
mysql -u username -p database_name < backup_YYYYMMDD.sql

# Restore files
tar -xzf backup_files_YYYYMMDD.tar.gz

# Clear cache
php artisan optimize:clear
```

---

## Important Notes

1. **Don't skip database update scripts** - Without running these, PDFs will show old content
2. **Clear all caches** - Both server-side and browser cache
3. **Test thoroughly** - Download PDFs for different users and policies
4. **Keep backups** - Always maintain backups before deployment

---

## Files Summary

### Must Upload (5 files):
1. Modules/Essentials/Entities/PolicyTemplates.php
2. Modules/Essentials/Resources/views/policy/index.blade.php
3. Modules/Essentials/Http/Controllers/EssentialsPolicyController.php
4. Modules/Essentials/Entities/EssentialsPolicy.php
5. Modules/Essentials/Resources/lang/en/lang.php

### Must Run (2 scripts):
1. update_policy_content.php
2. update_leave_policy_content.php

### Must Execute (1 command):
1. php artisan optimize:clear

---

## Support

If you face any issues:
1. Check Laravel logs: storage/logs/laravel.log
2. Check PHP error logs
3. Verify file permissions (755 for directories, 644 for files)
4. Ensure database connection is working
