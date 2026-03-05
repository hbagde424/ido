# Live Server Deployment - Step by Step

## 🎯 Main Goal
Update Company Policy and Leave Policy content, and remove HR Policy from the system.

---

## 📦 Phase 1: File Upload (via FTP/FileZilla)

### Upload these 5 PHP files:

```
1️⃣ Modules/Essentials/Entities/PolicyTemplates.php
   └─ Contains: New Company & Leave Policy content

2️⃣ Modules/Essentials/Resources/views/policy/index.blade.php
   └─ Contains: Updated UI (HR Policy removed)

3️⃣ Modules/Essentials/Http/Controllers/EssentialsPolicyController.php
   └─ Contains: Updated controller logic

4️⃣ Modules/Essentials/Entities/EssentialsPolicy.php
   └─ Contains: Updated model

5️⃣ Modules/Essentials/Resources/lang/en/lang.php
   └─ Contains: Updated translations
```

### Upload these 2 scripts to ROOT directory:

```
6️⃣ update_policy_content.php
   └─ Purpose: Update Company Policy in database

7️⃣ update_leave_policy_content.php
   └─ Purpose: Update Leave Policy in database
```

---

## 🗄️ Phase 2: Database Update (via SSH)

### Connect to server via SSH:
```bash
ssh username@your-server.com
cd /path/to/your/laravel/project
```

### Run update scripts:
```bash
# Update Company Policy content (14 records)
php update_policy_content.php

# Update Leave Policy content (2 records)
php update_leave_policy_content.php
```

### Expected Output:
```
✓ Successfully updated 14 company policy records with new content!
✓ Successfully updated 2 leave policy records with new content!
```

---

## 🧹 Phase 3: Clear Cache (via SSH)

```bash
php artisan optimize:clear
```

### This clears:
- ✅ Events cache
- ✅ Views cache
- ✅ Config cache
- ✅ Route cache
- ✅ Application cache

---

## 🧪 Phase 4: Testing

### Browser Testing:
1. Open browser
2. Press `Ctrl + Shift + Delete` (Clear cache)
3. Go to policy page
4. Press `Ctrl + F5` (Hard refresh)

### Verify Changes:
- ✅ HR Policy NOT visible in dropdown
- ✅ Only 4 policies visible: Company, Leave, POSH, NDA
- ✅ Download Company Policy PDF → New content
- ✅ Download Leave Policy PDF → New content

---

## 🔄 Complete Flow Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    START DEPLOYMENT                      │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│  STEP 1: Backup (Database + Files)                      │
│  ├─ mysqldump database                                  │
│  └─ tar backup files                                    │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│  STEP 2: Upload 5 PHP Files via FTP                     │
│  ├─ PolicyTemplates.php                                 │
│  ├─ index.blade.php                                     │
│  ├─ EssentialsPolicyController.php                      │
│  ├─ EssentialsPolicy.php                                │
│  └─ lang.php                                            │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│  STEP 3: Upload 2 Update Scripts to ROOT                │
│  ├─ update_policy_content.php                           │
│  └─ update_leave_policy_content.php                     │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│  STEP 4: SSH into Server                                │
│  └─ ssh username@server.com                             │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│  STEP 5: Run Database Update Scripts                    │
│  ├─ php update_policy_content.php                       │
│  └─ php update_leave_policy_content.php                 │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│  STEP 6: Clear All Caches                               │
│  └─ php artisan optimize:clear                          │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│  STEP 7: Test in Browser                                │
│  ├─ Clear browser cache                                 │
│  ├─ Hard refresh (Ctrl+F5)                              │
│  ├─ Check dropdown (no HR Policy)                       │
│  └─ Download PDFs (verify new content)                  │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│              ✅ DEPLOYMENT COMPLETE                      │
└─────────────────────────────────────────────────────────┘
```

---

## ⚠️ Common Issues & Solutions

### Issue 1: Old content still showing in PDF
**Solution:** 
- Run update scripts again
- Clear Laravel cache
- Clear browser cache

### Issue 2: HR Policy still visible
**Solution:**
- Verify index.blade.php uploaded correctly
- Clear view cache: `php artisan view:clear`

### Issue 3: 500 Error after upload
**Solution:**
- Check file permissions (755 for folders, 644 for files)
- Check Laravel logs: `storage/logs/laravel.log`
- Verify all files uploaded completely

---

## 📞 Quick Reference

### File Locations:
```
Modules/
└── Essentials/
    ├── Entities/
    │   ├── PolicyTemplates.php ← UPDATE
    │   └── EssentialsPolicy.php ← UPDATE
    ├── Http/
    │   └── Controllers/
    │       └── EssentialsPolicyController.php ← UPDATE
    └── Resources/
        ├── views/
        │   └── policy/
        │       └── index.blade.php ← UPDATE
        └── lang/
            └── en/
                └── lang.php ← UPDATE
```

### Root Directory Scripts:
```
/
├── update_policy_content.php ← UPLOAD & RUN
└── update_leave_policy_content.php ← UPLOAD & RUN
```

---

## ✅ Final Checklist

Before going live:
- [ ] Backup completed
- [ ] All 5 PHP files uploaded
- [ ] Both update scripts uploaded to root
- [ ] update_policy_content.php executed
- [ ] update_leave_policy_content.php executed
- [ ] Cache cleared
- [ ] Browser cache cleared
- [ ] PDFs tested and verified
- [ ] HR Policy not visible
- [ ] New content visible in PDFs

---

## 🎉 Success Criteria

Your deployment is successful when:
1. ✅ HR Policy is NOT visible in dropdown
2. ✅ Company Policy PDF shows new HR Manual content
3. ✅ Leave Policy PDF shows new leave structure
4. ✅ No errors in Laravel logs
5. ✅ All users can download updated PDFs
