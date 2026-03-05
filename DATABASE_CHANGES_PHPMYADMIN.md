# Database Changes - phpMyAdmin Guide

## 🎯 How to Run SQL Queries in phpMyAdmin

### Step 1: Open phpMyAdmin
```
Go to: http://your-server.com/phpmyadmin
Login with your credentials
```

---

## 📋 Queries to Run (In Order)

### QUERY 1: BACKUP (Run First!)
```sql
CREATE TABLE essentials_policies_backup AS 
SELECT * FROM essentials_policies;
```

**Steps:**
1. Click on "SQL" tab
2. Paste the query above
3. Click "Go" button
4. You should see: "Query executed successfully"

---

### QUERY 2: CHECK EXISTING DATA
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

**Steps:**
1. Click on "SQL" tab
2. Paste the query
3. Click "Go"
4. You will see how many policies of each type exist

**Expected Output:**
```
company_policy | 14
leave_policy   | 2
posh_policy    | X
nda_policy     | X
hr_policy      | X (if any)
```

---

### QUERY 3: DELETE HR POLICY RECORDS
```sql
DELETE FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

**Steps:**
1. Click on "SQL" tab
2. Paste the query
3. Click "Go"
4. You should see: "X rows affected"

---

### QUERY 4: VERIFY HR POLICY DELETED
```sql
SELECT COUNT(*) as hr_policies_remaining 
FROM essentials_policies 
WHERE policy_type = 'hr_policy';
```

**Steps:**
1. Click on "SQL" tab
2. Paste the query
3. Click "Go"
4. Result should be: 0

---

### QUERY 5: FINAL CHECK
```sql
SELECT policy_type, COUNT(*) as count 
FROM essentials_policies 
GROUP BY policy_type;
```

**Steps:**
1. Click on "SQL" tab
2. Paste the query
3. Click "Go"
4. Verify HR Policy is gone

**Expected Output:**
```
company_policy | 14
leave_policy   | 2
posh_policy    | X
nda_policy     | X
```

---

## 🔄 If Something Goes Wrong - ROLLBACK

```sql
TRUNCATE TABLE essentials_policies;

INSERT INTO essentials_policies 
SELECT * FROM essentials_policies_backup;
```

**Steps:**
1. Click on "SQL" tab
2. Paste both queries
3. Click "Go"
4. Data will be restored

---

## ✅ After Database Changes

Once database queries are done:

1. Upload the 5 PHP files via FTP
2. Run update scripts via SSH:
   ```bash
   php update_policy_content.php
   php update_leave_policy_content.php
   ```
3. Clear cache:
   ```bash
   php artisan optimize:clear
   ```
4. Test in browser

---

## 📊 Database Table Structure

### essentials_policies table columns:
```
id                  - Primary key
business_id         - Company ID
user_id             - Employee ID
policy_type         - Type: company_policy, leave_policy, posh_policy, nda_policy
title               - Policy title
content             - Policy HTML content
signature_photo     - Signature image filename
signed_date         - Date when signed
status              - pending, signed, rejected
rejection_reason    - Reason if rejected
created_at          - Created timestamp
updated_at          - Updated timestamp
```

---

## 🎯 What Each Query Does

| Query | Purpose | Impact |
|-------|---------|--------|
| CREATE TABLE backup | Backup data | Safe - no changes |
| SELECT COUNT | Check data | Safe - read only |
| DELETE hr_policy | Remove HR policies | Removes HR policy records |
| SELECT COUNT verify | Verify deletion | Safe - read only |
| SELECT final check | Final verification | Safe - read only |

---

## ⚠️ Important Notes

1. **Always backup first** - Run Query 1 before anything else
2. **Run queries in order** - Don't skip steps
3. **Verify each step** - Check output after each query
4. **Keep backup table** - Don't delete essentials_policies_backup
5. **Test thoroughly** - After all changes, test in browser

---

## 🆘 Troubleshooting

### Error: "Table already exists"
- The backup table already exists
- Use a different name or delete old backup first

### Error: "Access denied"
- Check your phpMyAdmin user permissions
- Contact your hosting provider

### Error: "Unknown column"
- Check table name spelling
- Verify you're in correct database

---

## 📞 Quick Reference

### To see all policies:
```sql
SELECT * FROM essentials_policies;
```

### To see company policies only:
```sql
SELECT * FROM essentials_policies WHERE policy_type = 'company_policy';
```

### To see who signed what:
```sql
SELECT u.first_name, u.last_name, ep.policy_type, ep.status, ep.signed_date
FROM essentials_policies ep
JOIN users u ON ep.user_id = u.id
ORDER BY ep.policy_type;
```

### To count by status:
```sql
SELECT policy_type, status, COUNT(*) 
FROM essentials_policies 
GROUP BY policy_type, status;
```

---

## ✅ Success Checklist

- [ ] Backup created
- [ ] HR policies deleted
- [ ] Remaining policies verified (4 types only)
- [ ] No errors in queries
- [ ] Backup table still exists
- [ ] Ready for PHP file upload

---

**Next Step:** Upload the 5 PHP files and run update scripts
