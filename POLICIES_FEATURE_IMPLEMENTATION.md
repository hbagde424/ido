# Policies Management Feature - Implementation Guide

## Overview
Naya **Policies Management** feature HRM module me add kiya gaya hai. Yeh feature 5 types ke policies manage karta hai with user signature support aur PDF download functionality.

## Features Implemented

### 1. **5 Policy Types**
- Company Policy
- HR Policy
- Leave Policy
- POSH Policy
- NDA Policy

### 2. **Filters**
- **User Filter**: Specific user ke policies dekh sakte ho
- **Policy Type Filter**: Specific policy type ke policies dekh sakte ho

### 3. **User Signature Support**
- User create/edit karte time signature photo upload kar sakte hain
- Signature photo policy PDF me display hota hai

### 4. **PDF Download**
- Har policy ko PDF format me download kar sakte ho
- PDF me employee details, policy content, aur signature included hota hai

### 5. **Policy Status**
- **Pending**: Policy abhi sign nahi hua
- **Signed**: Policy signed ho gaya
- **Rejected**: Policy reject ho gaya (rejection reason ke saath)

## Database Structure

### Table: `essentials_policies`
```sql
- id (Primary Key)
- business_id (Foreign Key)
- user_id (Foreign Key)
- policy_type (enum: company_policy, hr_policy, leave_policy, posh_policy, nda_policy)
- title (string)
- content (longText)
- signature_photo (string - filename)
- signed_date (date)
- status (enum: pending, signed, rejected)
- rejection_reason (text)
- created_at, updated_at (timestamps)
```

## Files Created

### 1. **Migration**
- `Modules/Essentials/Database/Migrations/2025_02_04_create_essentials_policies_table.php`

### 2. **Model**
- `Modules/Essentials/Entities/EssentialsPolicy.php`

### 3. **Controller**
- `Modules/Essentials/Http/Controllers/EssentialsPolicyController.php`

### 4. **Views**
- `Modules/Essentials/Resources/views/policy/index.blade.php` - List view with filters
- `Modules/Essentials/Resources/views/policy/create.blade.php` - Create policy form
- `Modules/Essentials/Resources/views/policy/edit.blade.php` - Edit policy form
- `Modules/Essentials/Resources/views/policy/show.blade.php` - View policy modal
- `Modules/Essentials/Resources/views/policy/pdf.blade.php` - PDF template

### 5. **Routes**
- Added in `Modules/Essentials/Routes/web.php`
- Routes: `/hrm/policy` (CRUD operations)
- Special route: `/hrm/policy/{id}/download-pdf`

### 6. **Navigation**
- Updated `Modules/Essentials/Resources/views/layouts/nav_hrm.blade.php`
- "Policies" tab added to HRM navigation

### 7. **Permissions**
- Added `essentials.crud_policy` permission in `Modules/Essentials/Http/Controllers/DataController.php`

### 8. **Language Files**
- Updated `Modules/Essentials/Resources/lang/en/lang.php` with policy translations

### 9. **Directory**
- Created `public/uploads/policy_signatures/` for storing signature photos

## How to Use

### 1. **Access Policies**
- HRM Dashboard > Policies tab

### 2. **Add New Policy**
- Click "Add New Policy" button
- Fill in:
  - User (select employee)
  - Policy Type (select from 5 types)
  - Title
  - Content (rich text editor)
  - User Signature Photo (upload image)
  - Status (Pending/Signed/Rejected)
- Click "Save Policy"

### 3. **Edit Policy**
- Click "Edit" button on policy row
- Modify details
- Upload new signature if needed
- Click "Update Policy"

### 4. **View Policy**
- Click "View" button to see policy details in modal
- Shows all information including signature photo

### 5. **Download PDF**
- Click "PDF" button to download policy as PDF
- PDF includes:
  - Employee name and ID
  - Policy type and status
  - Full policy content
  - Employee signature
  - Company representative signature space
  - Document date

### 6. **Filter Policies**
- Use "Filter by User" dropdown to see specific user's policies
- Use "Filter by Policy Type" dropdown to see specific policy type
- Combine both filters for precise results

### 7. **Delete Policy**
- Click "Delete" button
- Confirm deletion
- Signature photo automatically deleted from server

## Permissions

### Required Permission
- `essentials.crud_policy` - Manage Policies

### Who Can Access
- Superadmin (by default)
- Users with `essentials.crud_policy` permission
- Users with Essentials module subscription

## Technical Details

### Controller Methods
- `index()` - List all policies with AJAX datatable
- `create()` - Show create form
- `store()` - Save new policy
- `show()` - Display policy details
- `edit()` - Show edit form
- `update()` - Update policy
- `destroy()` - Delete policy
- `downloadPdf()` - Generate and download PDF

### File Upload
- Signature photos stored in: `public/uploads/policy_signatures/`
- Filename format: `{timestamp}_{original_filename}`
- Old signature automatically deleted when updating

### PDF Generation
- Uses Laravel PDF library (barryvdh/laravel-dompdf)
- Professional layout with signature section
- Includes company representative signature space

## Migration Steps

1. Run migration:
   ```bash
   php artisan migrate
   ```

2. Grant permission to users:
   - Go to User Management > Roles
   - Select role
   - Check "Manage Policies" permission
   - Save

3. Access Policies:
   - Go to HRM > Policies
   - Start managing policies

## Notes

- Signature photos are required for "Signed" status policies
- Rejection reason is only shown when status is "Rejected"
- All policies are business-specific (multi-tenant support)
- Datatable supports sorting and searching
- Responsive design for mobile devices

## Future Enhancements

- Email notifications when policy status changes
- Bulk policy assignment to multiple users
- Policy version history
- Policy acknowledgment tracking
- Automated policy renewal reminders
- Policy templates

## Support

For issues or questions, check:
- Controller: `Modules/Essentials/Http/Controllers/EssentialsPolicyController.php`
- Model: `Modules/Essentials/Entities/EssentialsPolicy.php`
- Views: `Modules/Essentials/Resources/views/policy/`
